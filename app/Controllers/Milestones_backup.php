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

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'));

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
            'priority' => 'required|in_list[low,medium,high,critical]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'not_started',
            'planned_start_date' => $this->request->getPost('planned_start_date') ?: $this->request->getPost('planned_end_date'),
            'planned_end_date' => $this->request->getPost('planned_end_date')
        ];

        $milestoneId = $this->milestoneModel->createMilestone($data);

        if ($milestoneId) {
            return redirect()->to('/milestones')->with('success', 'Milestone created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create milestone');
    }

    public function show($id)
    {
        $milestone = $this->milestoneModel->select('tasks.*, projects.name as project_name, projects.project_code')
                                         ->join('projects', 'tasks.project_id = projects.id')
                                         ->find($id);

        if (!$milestone || $milestone['task_type'] !== 'milestone') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }

        // Get related tasks (tasks that should be completed by this milestone)
        $relatedTasks = $this->taskModel->where('project_id', $milestone['project_id'])
                                       ->where('planned_end_date <=', $milestone['planned_end_date'])
                                       ->where('task_type !=', 'milestone')
                                       ->findAll();

        $data = [
            'title' => $milestone['title'],
            'milestone' => $milestone,
            'related_tasks' => $relatedTasks,
            'completion_stats' => $this->calculateMilestoneCompletion($milestone, $relatedTasks)
        ];

        return view('milestones/show', $data);
    }

    public function edit($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || $milestone['task_type'] !== 'milestone') {
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
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || $milestone['task_type'] !== 'milestone') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'planned_end_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'status' => 'required|in_list[not_started,in_progress,completed,cancelled,on_hold]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'planned_start_date' => $this->request->getPost('planned_start_date'),
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'actual_start_date' => $this->request->getPost('actual_start_date'),
            'actual_end_date' => $this->request->getPost('actual_end_date'),
            'progress_percentage' => $this->request->getPost('progress_percentage') ?: 0
        ];

        // Auto-set actual_end_date if status is completed
        if ($data['status'] === 'completed' && empty($data['actual_end_date'])) {
            $data['actual_end_date'] = date('Y-m-d');
            $data['progress_percentage'] = 100;
        }

        if ($this->milestoneModel->updateMilestone($id, $data)) {
            // Update project progress
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            return redirect()->to('/milestones/' . $id)->with('success', 'Milestone updated successfully');
        }

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

    public function delete($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || $milestone['task_type'] !== 'milestone') {
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

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'));

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->findAll();

        $data = [
            'title' => 'Milestone Calendar',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'calendar_events' => $this->prepareCalendarEvents($milestones)
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
}
