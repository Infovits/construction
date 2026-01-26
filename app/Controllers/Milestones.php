<?php

namespace App\Controllers;

use App\Models\MilestoneModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\UserModel;

class Milestones extends BaseController
{
    protected $milestoneModel;
    protected $projectModel;
    protected $taskModel;
    protected $userModel;

    public function __construct()
    {
        helper('project');
        $this->milestoneModel = new MilestoneModel();
        $this->projectModel = new ProjectModel();
        $this->taskModel = new TaskModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $status = $this->request->getGet('status');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name, projects.project_code')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }
        if ($status) {
            $builder->where('tasks.status', $status);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();
        
        // Calculate milestone statistics
        $stats = [
            'total' => count($milestones),
            'completed' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'overdue' => 0
        ];
        
        foreach ($milestones as $milestone) {
            if ($milestone['status'] === 'completed') {
                $stats['completed']++;
            } elseif ($milestone['status'] === 'in_progress') {
                $stats['in_progress']++;
            } else {
                $stats['pending']++;
            }
            
            // Check if milestone is overdue
            if ($milestone['planned_end_date'] && strtotime($milestone['planned_end_date']) < time() && $milestone['status'] !== 'completed') {
                $stats['overdue']++;
            }
        }

        $data = [
            'title' => 'Milestones',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'overdue_milestones' => $this->milestoneModel->getOverdueMilestones(),
            'filters' => [
                'project_id' => $projectId,
                'status' => $status
            ],
            'stats' => $stats
        ];

        return view('milestones/index', $data);
    }

    public function create()
    {
        $projectId = $this->request->getGet('project_id');
        
        // Get all active users for the assignment dropdown
        $users = $this->userModel->where('status', 'active')
                               ->where('company_id', session('company_id'))
                               ->findAll();

        $data = [
            'title' => 'Create New Milestone',
            'projects' => $this->projectModel->getActiveProjects(),
            'selected_project' => $projectId,
            'users' => $users
        ];

        return view('milestones/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'project_id' => 'required|numeric',
            'title' => 'required|min_length[3]|max_length[255]',
            'planned_end_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'status' => 'permit_empty|in_list[pending,in_progress,completed,cancelled]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'task_type' => 'task',
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status') ?: 'pending',
            'planned_start_date' => $this->request->getPost('start_date') ?: null,
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'actual_cost' => $this->request->getPost('actual_cost') ?: 0,
            'is_milestone' => 1,
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'progress_percentage' => $this->request->getPost('progress_percentage') ?: 0,
            'company_id' => session('company_id')
        ];

        log_message('info', 'Attempting to create milestone with data: ' . json_encode($data));

        $milestoneId = $this->milestoneModel->createMilestone($data);

        if ($milestoneId) {
            log_message('info', 'Milestone created successfully with ID: ' . $milestoneId);
            return redirect()->to('/admin/milestones')->with('success', 'Milestone created successfully');
        }

        log_message('error', 'Failed to create milestone. Data: ' . json_encode($data));
        return redirect()->back()->withInput()->with('error', 'Failed to create milestone');
    }

    public function show($id)
    {
        $milestone = $this->milestoneModel->select('tasks.*, projects.name as project_name, projects.project_code')
                                         ->join('projects', 'tasks.project_id = projects.id')
                                         ->where('tasks.id', $id)
                                         ->where('tasks.is_milestone', 1)
                                         ->first();

        if (!$milestone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }

        // Get project details
        $project = $this->projectModel->find($milestone['project_id']);

        // Get assigned user details
        $assigned_user = null;
        if ($milestone['assigned_to']) {
            $assigned_user = $this->userModel->find($milestone['assigned_to']);
        }

        // Get related tasks (tasks that should be completed by this milestone)
        $relatedTasks = $this->taskModel->select('tasks.*, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
                                       ->join('users u', 'tasks.assigned_to = u.id', 'left')
                                       ->where('tasks.project_id', $milestone['project_id'])
                                       ->where('tasks.planned_end_date <=', $milestone['planned_end_date'])
                                       ->where('tasks.id !=', $milestone['id']) // Exclude the milestone itself
                                       ->findAll();

        $data = [
            'title' => $milestone['title'],
            'milestone' => $milestone,
            'project' => $project,
            'assigned_user' => $assigned_user,
            'related_tasks' => $relatedTasks,
            'completion_stats' => $this->calculateMilestoneCompletion($milestone, $relatedTasks)
        ];

        return view('milestones/view', $data);
    }

    public function edit($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }
        
        // Get all active users for the assignment dropdown
        $users = $this->userModel->where('status', 'active')
                               ->where('company_id', session('company_id'))
                               ->findAll();

        $data = [
            'title' => 'Edit Milestone',
            'milestone' => $milestone,
            'projects' => $this->projectModel->getActiveProjects(),
            'users' => $users
        ];

        return view('milestones/edit', $data);
    }

    public function update($id)
    {
        log_message('info', 'Milestone update called for ID: ' . $id);

        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || !$milestone['is_milestone']) {
            log_message('error', 'Milestone not found or not a milestone: ' . $id);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'planned_end_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'status' => 'required|in_list[not_started,in_progress,completed,cancelled,on_hold]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            log_message('error', 'Validation failed: ' . json_encode($validation->getErrors()));
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'planned_start_date' => $this->request->getPost('planned_start_date'),
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'actual_cost' => $this->request->getPost('actual_cost') ?: 0,
            'progress_percentage' => $this->request->getPost('progress_percentage') ?: 0
        ];

        // Auto-set actual_end_date if status is completed
        if ($data['status'] === 'completed' && empty($data['actual_end_date'])) {
            $data['actual_end_date'] = date('Y-m-d');
            $data['progress_percentage'] = 100;
        }

        log_message('info', 'Attempting to update milestone with data: ' . json_encode($data));

        if ($this->milestoneModel->updateMilestone($id, $data)) {
            log_message('info', 'Milestone updated successfully');
            // Update project progress
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            return redirect()->to('/admin/milestones/' . $id)->with('success', 'Milestone updated successfully');
        }

        log_message('error', 'Failed to update milestone in database');
        return redirect()->back()->withInput()->with('error', 'Failed to update milestone');
    }

    public function complete($id)
    {
        if ($this->milestoneModel->completeMilestone($id)) {
            $milestone = $this->milestoneModel->find($id);
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            return $this->response->setJSON(['success' => true, 'message' => 'Milestone marked as completed']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to complete milestone']);
    }

    public function updateProgress($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || !$milestone['is_milestone']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Milestone not found']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'progress_percentage' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'notes' => 'permit_empty|max_length[1000]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())]);
        }

        $data = [
            'progress_percentage' => $this->request->getPost('progress_percentage'),
        ];

        // Auto-set status based on progress
        $progress = (int) $data['progress_percentage'];
        if ($progress == 100) {
            $data['status'] = 'completed';
            $data['actual_end_date'] = date('Y-m-d');
        } elseif ($progress > 0) {
            $data['status'] = 'in_progress';
            if (empty($milestone['actual_start_date'])) {
                $data['actual_start_date'] = date('Y-m-d');
            }
        }

        if ($this->milestoneModel->updateMilestone($id, $data)) {
            // Update project progress
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            // Log the progress update if notes were provided
            $notes = $this->request->getPost('notes');
            if (!empty($notes)) {
                // You could add activity logging here if needed
                log_message('info', 'Milestone progress updated: ' . $id . ' - ' . $progress . '% - ' . $notes);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Progress updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update progress']);
    }

    public function delete($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || !$milestone['is_milestone']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Milestone not found']);
        }

        if ($this->milestoneModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Milestone deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete milestone']);
    }

    public function upcoming()
    {
        $days = $this->request->getGet('days') ?: 30;
        $projectId = $this->request->getGet('project_id');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1)
                                       ->where('tasks.planned_end_date <=', date('Y-m-d', strtotime('+' . $days . ' days')))
                                       ->where('tasks.planned_end_date >=', date('Y-m-d'))
                                       ->whereNotIn('tasks.status', ['completed', 'cancelled']);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();

        $data = [
            'title' => 'Upcoming Milestones',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'days' => $days,
            'project_id' => $projectId
        ];

        return view('milestones/upcoming', $data);
    }

    public function calendar()
    {
        $projectId = $this->request->getGet('project_id');

        $data = [
            'title' => 'Milestone Calendar',
            'projects' => $this->projectModel->getActiveProjects(),
            'project_id' => $projectId
        ];

        return view('milestones/calendar', $data);
    }

    public function report()
    {
        $projectId = $this->request->getGet('project_id');
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?: date('Y-m-t');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1)
                                       ->where('tasks.planned_end_date >=', $dateFrom)
                                       ->where('tasks.planned_end_date <=', $dateTo);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();

        // Calculate statistics
        $stats = [
            'total' => count($milestones),
            'completed' => count(array_filter($milestones, function($m) { return $m['status'] === 'completed'; })),
            'overdue' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] < date('Y-m-d') && $m['status'] !== 'completed'; 
            })),
            'upcoming' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] >= date('Y-m-d') && $m['status'] !== 'completed'; 
            }))
        ];

        $data = [
            'title' => 'Milestone Report',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'stats' => $stats,
            'filters' => [
                'project_id' => $projectId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ];

        return view('milestones/report', $data);
    }

    private function calculateMilestoneCompletion($milestone, $relatedTasks)
    {
        $totalTasks = count($relatedTasks);
        $completedTasks = count(array_filter($relatedTasks, function($task) {
            return $task['status'] === 'completed';
        }));

        $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => round($completionRate, 2),
            'is_ready_for_completion' => $completedTasks === $totalTasks
        ];
    }

    private function prepareCalendarEvents($milestones)
    {
        $events = [];
        
        foreach ($milestones as $milestone) {
            $events[] = [
                'id' => $milestone['id'],
                'title' => $milestone['title'],
                'start' => $milestone['planned_end_date'],
                'end' => $milestone['planned_end_date'],
                'className' => $this->getMilestoneStatusClass($milestone['status']),
                'url' => '/milestones/' . $milestone['id'],
                'rendering' => 'background'
            ];
        }
        
        return $events;
    }

    public function getProjectMilestones($projectId)
    {
        // Validate project access
        $project = $this->projectModel->where('id', $projectId)
                                     ->where('company_id', session('company_id'))
                                     ->first();
        
        if (!$project) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Project not found or access denied',
                'milestones' => []
            ]);
        }

        // Get milestones for the project
        $milestones = $this->milestoneModel->select('id, title, planned_end_date, status')
                                          ->where('project_id', $projectId)
                                          ->where('is_milestone', 1)
                                          ->orderBy('planned_end_date', 'ASC')
                                          ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'milestones' => $milestones
        ]);
    }

    private function getMilestoneStatusClass($status)
    {
        $classes = [
            'not_started' => 'fc-event-info',
            'in_progress' => 'fc-event-warning',
            'completed' => 'fc-event-success',
            'cancelled' => 'fc-event-danger',
            'on_hold' => 'fc-event-secondary'
        ];

        return $classes[$status] ?? 'fc-event-default';
    }

    /**
     * API endpoint for calendar events
     */
    public function apiCalendarEvents()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');

        $milestones = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                          ->join('projects', 'tasks.project_id = projects.id')
                                          ->where('projects.company_id', session('company_id'))
                                          ->where('tasks.is_milestone', 1)
                                          ->where('tasks.planned_end_date IS NOT NULL');

        if ($start && $end) {
            $milestones->groupStart()
                      ->where('tasks.planned_end_date >=', $start)
                      ->where('tasks.planned_end_date <=', $end)
                      ->groupEnd();
        }

        $milestones = $milestones->findAll();

        return $this->response->setJSON(['milestones' => $milestones]);
    }
}
