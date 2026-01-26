<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\ClientModel;
use App\Models\ProjectCategoryModel;
use App\Models\UserModel;
use App\Models\TaskModel;
use App\Models\ProjectTeamMemberModel;
use App\Models\MilestoneModel;

class Projects extends BaseController
{
    protected $projectModel;
    protected $clientModel;
    protected $categoryModel;
    protected $userModel;
    protected $taskModel;
    protected $teamModel;
    protected $milestoneModel;

    public function __construct()
    {
        helper('project');
        $this->projectModel = new ProjectModel();
        $this->clientModel = new ClientModel();
        $this->categoryModel = new ProjectCategoryModel();
        $this->userModel = new UserModel();
        $this->taskModel = new TaskModel();
        $this->teamModel = new ProjectTeamMemberModel();
        $this->milestoneModel = new MilestoneModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Projects',
            'pageTitle' => 'Projects Management',
            'projects' => $this->projectModel->getProjectsWithDetails(),
            'stats' => $this->projectModel->getProjectStats(),
            'overdue_projects' => $this->projectModel->getOverdueProjects()
        ];

        return view('projects/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create New Project',
            'clients' => $this->clientModel->where('company_id', session('company_id'))->findAll(),
            'categories' => $this->categoryModel->findAll(),
            'companies' => model('CompanyModel')->findAll(),
            'users' => $this->userModel->where('company_id', session('company_id'))->findAll()
        ];

        return view('projects/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'project_code' => 'required|max_length[50]',
            'company_id' => 'required|numeric',
            'project_type' => 'required|in_list[residential,commercial,industrial,infrastructure,renovation]',
            'estimated_budget' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = $this->request->getPost();
        $data['created_by'] = session('user_id');
        $data['company_id'] = session('company_id');
        $data['currency'] = $data['currency'] ?? 'MWK'; // Set default currency to MWK

        try {
            if ($this->projectModel->save($data)) {
                return redirect()->to(base_url('admin/projects'))->with('success', 'Project created successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating project: ' . $e->getMessage());
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create project');
    }

    public function show($id)
    {
        $project = $this->projectModel->getProjectWithTeam($id);
        
        if (!$project) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project not found');
        }

        $data = [
            'title' => $project['name'],
            'project' => $project,
            'tasks' => $this->taskModel->getTasksByProject($id),
            'milestones' => $this->milestoneModel->getProjectMilestones($id),
            'timeline' => $this->projectModel->getProjectTimeline($id),
            'budget_tracking' => $this->projectModel->getBudgetTracking($id),
            'overdue_tasks' => $this->taskModel->getOverdueTasks(null, $id),
            'recent_activities' => $this->getRecentActivities($id)
        ];

        return view('projects/view', $data);
    }

    public function view($id)
    {
        // Alias for show method to match route expectations
        return $this->show($id);
    }

    public function edit($id)
    {
        $project = $this->projectModel->find($id);
        if (!$project) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project not found');
        }

        $data = [
            'title' => 'Edit Project',
            'project' => $project,
            'clients' => $this->clientModel->where('company_id', session('company_id'))->findAll(),
            'categories' => $this->categoryModel->findAll(),
            'companies' => model('CompanyModel')->findAll(),
            'users' => $this->userModel->where('company_id', session('company_id'))->findAll()
        ];

        return view('projects/create', $data);
    }

    public function update($id)
    {
        $project = $this->projectModel->find($id);
        if (!$project) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project not found');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'project_code' => 'required|max_length[50]',
            'company_id' => 'required|numeric',
            'project_type' => 'required|in_list[residential,commercial,industrial,infrastructure,renovation]',
            'estimated_budget' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = $this->request->getPost();

        try {
            if ($this->projectModel->update($id, $data)) {
                return redirect()->to(base_url('admin/projects/view/' . $id))->with('success', 'Project updated successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating project: ' . $e->getMessage());
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update project');
    }

    public function delete($id)
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Project not found']);
        }

        // Soft delete by archiving
        if ($this->projectModel->update($id, ['is_archived' => true])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Project archived successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to archive project']);
    }

    public function team($id)
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project not found');
        }

        $data = [
            'title' => 'Project Team Management',
            'project' => $project,
            'team_members' => $this->teamModel->getProjectTeamMembers($id),
            'available_users' => $this->teamModel->getAvailableUsers($id, session('company_id'))
        ];

        return view('projects/team', $data);
    }

    public function addTeamMember($projectId)
    {
        $project = $this->projectModel->find($projectId);
        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Project not found']);
        }

        $rules = [
            'user_id' => 'required|numeric',
            'role' => 'required|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = [
            'project_id' => $projectId,
            'user_id' => $this->request->getPost('user_id'),
            'role' => $this->request->getPost('role') === 'Other' 
                ? $this->request->getPost('custom_role') 
                : $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'joined_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->teamModel->save($data)) {
                return redirect()->back()->with('success', 'Team member added successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding team member: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Failed to add team member');
    }

    public function updateTeamMember($projectId, $memberId)
    {
        $member = $this->teamModel->find($memberId);
        if (!$member || $member['project_id'] != $projectId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Team member not found']);
        }

        $data = [
            'role' => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        try {
            if ($this->teamModel->update($memberId, $data)) {
                return redirect()->back()->with('success', 'Team member updated successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating team member: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Failed to update team member');
    }

    public function toggleTeamMemberStatus($projectId, $memberId)
    {
        $member = $this->teamModel->find($memberId);
        if (!$member || $member['project_id'] != $projectId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Team member not found']);
        }

        $isActive = $this->request->getPost('is_active');
        
        try {
            if ($this->teamModel->update($memberId, ['is_active' => $isActive])) {
                return $this->response->setJSON(['success' => true]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function removeTeamMember($projectId, $memberId)
    {
        $member = $this->teamModel->find($memberId);
        if (!$member || $member['project_id'] != $projectId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Team member not found']);
        }

        try {
            if ($this->teamModel->delete($memberId)) {
                return $this->response->setJSON(['success' => true]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove team member']);
    }

    public function gantt($id)
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            return redirect()->to('/admin/projects')->with('error', 'Project not found');
        }
        
        // For debugging
        log_message('info', 'Gantt chart requested for project ID: ' . $id);
        
        // Get tasks with all needed details - excluding milestones
        $tasks = $this->taskModel->where('project_id', $id)
            ->where('task_type !=', 'milestone')  // Using != to exclude milestones
            ->findAll();
        
        log_message('info', 'Found ' . count($tasks) . ' tasks for gantt chart');
            
        // Add is_critical_path flag if missing and assigned_name
        foreach ($tasks as &$task) {
            if (!isset($task['is_critical_path'])) {
                $task['is_critical_path'] = false;
            }
            
            // Get assigned user name if there's an assigned_to value
            if (!empty($task['assigned_to'])) {
                $assignedUser = $this->userModel->find($task['assigned_to']);
                if ($assignedUser) {
                    // Combine first and last name for display
                    $task['assigned_name'] = $assignedUser['first_name'] . ' ' . $assignedUser['last_name'];
                } else {
                    $task['assigned_name'] = '';
                }
            } else {
                $task['assigned_name'] = '';
            }
        }
        
        // Get milestones specifically - first try with MilestoneModel
        $milestones = $this->milestoneModel->where('project_id', $id)->findAll();
        
        // Fallback: If no milestones found or model doesn't separate them, check tasks table
        if (empty($milestones)) {
            log_message('info', 'No milestones found in milestone model, checking task model');
            $milestones = $this->taskModel->where('project_id', $id)
                ->where('task_type', 'milestone')
                ->findAll();
        }
        
        log_message('info', 'Found ' . count($milestones) . ' milestones for gantt chart');
        
        // Ensure all tasks and milestones have valid dates
        foreach ($tasks as $key => $task) {
            // Skip tasks with missing dates
            if (empty($task['planned_start_date']) || empty($task['planned_end_date'])) {
                unset($tasks[$key]);
                continue;
            }
            
            // Ensure valid date format
            $tasks[$key]['planned_start_date'] = date('Y-m-d', strtotime($task['planned_start_date']));
            $tasks[$key]['planned_end_date'] = date('Y-m-d', strtotime($task['planned_end_date']));
            
            // Set progress to 0 if not defined
            if (!isset($task['progress_percentage'])) {
                $tasks[$key]['progress_percentage'] = 0;
            }
        }
        
        foreach ($milestones as $key => $milestone) {
            // Skip milestones with missing dates
            if (empty($milestone['planned_end_date'])) {
                unset($milestones[$key]);
                continue;
            }
            
            // Ensure valid date format
            $milestones[$key]['planned_end_date'] = date('Y-m-d', strtotime($milestone['planned_end_date']));
            
            // Set progress to 0 if not defined
            if (!isset($milestone['progress_percentage'])) {
                $milestones[$key]['progress_percentage'] = 0;
            }
        }
        
        // If we don't have any tasks or milestones, create some dummy data for testing
        if (empty($tasks) && empty($milestones)) {
            // Add dummy tasks for testing
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+2 weeks'));
            
            // Dummy task 1
            $tasks[] = [
                'id' => 999,
                'title' => 'Sample Task 1',
                'planned_start_date' => $startDate,
                'planned_end_date' => date('Y-m-d', strtotime('+5 days')),
                'progress_percentage' => 50,
                'is_critical_path' => true,
                'assigned_name' => 'Test User',
                'status' => 'in_progress',
                'priority' => 'high'
            ];
            
            // Dummy task 2
            $tasks[] = [
                'id' => 1000,
                'title' => 'Sample Task 2',
                'planned_start_date' => date('Y-m-d', strtotime('+3 days')),
                'planned_end_date' => date('Y-m-d', strtotime('+10 days')),
                'progress_percentage' => 20,
                'is_critical_path' => false,
                'assigned_name' => 'Another User',
                'status' => 'not_started',
                'priority' => 'medium'
            ];
            
            // Dummy milestone
            $milestones[] = [
                'id' => 500,
                'title' => 'Project Completion',
                'planned_end_date' => date('Y-m-d', strtotime('+2 weeks')),
                'progress_percentage' => 0,
                'status' => 'not_started',
                'priority' => 'high'
            ];
        }
        
        // Prepare the tasks for the Gantt chart
        $tasks = array_values($tasks); // Reindex array after potential removals
        $milestones = array_values($milestones); // Reindex array after potential removals
        
        $data = [
            'title' => 'Gantt Chart - ' . $project['name'],
            'project' => $project,
            'tasks' => $tasks,
            'milestones' => $milestones
        ];
        
        return view('projects/gantt', $data);
    }

    public function exportGanttPdf($id)
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            return redirect()->to('/admin/projects')->with('error', 'Project not found');
        }
        
        // Get tasks and milestones for the PDF
        $tasks = $this->taskModel->where('project_id', $id)
            ->where('task_type !=', 'milestone')
            ->findAll();
            
        $milestones = $this->milestoneModel->where('project_id', $id)->findAll();
        
        if (empty($milestones)) {
            $milestones = $this->taskModel->where('project_id', $id)
                ->where('task_type', 'milestone')
                ->findAll();
        }
        
        // Debug: Log what we found
        log_message('info', 'PDF Export - Tasks found: ' . count($tasks));
        log_message('info', 'PDF Export - Milestones found: ' . count($milestones));
        
        // If still no tasks/milestones, try to get them with valid dates
        if (empty($tasks) && empty($milestones)) {
            log_message('info', 'PDF Export - No tasks/milestones found, trying alternative query');
            
            // Try getting all tasks for the project regardless of type
            $allTasks = $this->taskModel->where('project_id', $id)->findAll();
            log_message('info', 'PDF Export - All tasks found: ' . count($allTasks));
            
            foreach ($allTasks as $task) {
                if (!empty($task['planned_start_date']) && !empty($task['planned_end_date'])) {
                    if ($task['task_type'] === 'milestone') {
                        $milestones[] = $task;
                    } else {
                        $tasks[] = $task;
                    }
                }
            }
        }
        
        // Prepare data for PDF
        foreach ($tasks as &$task) {
            if (!empty($task['assigned_to'])) {
                $assignedUser = $this->userModel->find($task['assigned_to']);
                if ($assignedUser) {
                    $task['assigned_name'] = $assignedUser['first_name'] . ' ' . $assignedUser['last_name'];
                } else {
                    $task['assigned_name'] = '';
                }
            } else {
                $task['assigned_name'] = '';
            }
        }
        
        // Ensure valid dates
        foreach ($tasks as $key => $task) {
            if (empty($task['planned_start_date']) || empty($task['planned_end_date'])) {
                unset($tasks[$key]);
                continue;
            }
            $tasks[$key]['planned_start_date'] = date('Y-m-d', strtotime($task['planned_start_date']));
            $tasks[$key]['planned_end_date'] = date('Y-m-d', strtotime($task['planned_end_date']));
            if (!isset($task['progress_percentage'])) {
                $tasks[$key]['progress_percentage'] = 0;
            }
        }
        
        foreach ($milestones as $key => $milestone) {
            if (empty($milestone['planned_end_date'])) {
                unset($milestones[$key]);
                continue;
            }
            $milestones[$key]['planned_end_date'] = date('Y-m-d', strtotime($milestone['planned_end_date']));
            if (!isset($milestone['progress_percentage'])) {
                $milestones[$key]['progress_percentage'] = 0;
            }
        }
        
        $tasks = array_values($tasks);
        $milestones = array_values($milestones);
        
        $data = [
            'project' => $project,
            'tasks' => $tasks,
            'milestones' => $milestones,
            'export_date' => date('Y-m-d H:i:s')
        ];
        
        // Load the PDF view
        $html = view('projects/gantt_pdf', $data);
        
        // Create PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF
        $filename = 'Gantt_Chart_' . $project['project_code'] . '_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    public function dashboard($id)
    {
        $project = $this->projectModel->getProjectWithTeam($id);
        
        if (!$project) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project not found');
        }

        $data = [
            'title' => 'Dashboard - ' . $project['name'],
            'project' => $project,
            'budget_tracking' => $this->projectModel->getBudgetTracking($id),
            'task_summary' => $this->taskModel->getTaskSummaryByProject($id),
            'milestone_progress' => $this->milestoneModel->getMilestoneProgress($id),
            'overdue_tasks' => $this->taskModel->getOverdueTasks(null, $id),
            'upcoming_milestones' => $this->milestoneModel->getUpcomingMilestones($id, 14),
            'recent_activities' => $this->getRecentActivities($id),
            'team_stats' => $this->teamModel->getTeamStats($id)
        ];

        return view('projects/dashboard', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $projects = $this->projectModel->searchProjects($keyword);

        return $this->response->setJSON($projects);
    }

    public function clone($id)
    {
        $originalProject = $this->projectModel->find($id);
        
        if (!$originalProject) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project not found');
        }

        // Prepare data for cloning
        unset($originalProject['id']);
        $originalProject['name'] = $originalProject['name'] . ' (Copy)';
        $originalProject['project_code'] = $this->projectModel->generateProjectCode();
        $originalProject['status'] = 'planning';
        $originalProject['progress_percentage'] = 0;
        $originalProject['actual_cost'] = 0;
        $originalProject['actual_end_date'] = null;
        $originalProject['created_by'] = session('user_id');

        $newProjectId = $this->projectModel->insert($originalProject);

        if ($newProjectId) {
            // Clone tasks and milestones
            $this->cloneProjectTasks($id, $newProjectId);
            
            return redirect()->to('/projects/' . $newProjectId)->with('success', 'Project cloned successfully');
        }

        return redirect()->back()->with('error', 'Failed to clone project');
    }

    private function cloneProjectTasks($originalProjectId, $newProjectId)
    {
        $tasks = $this->taskModel->where('project_id', $originalProjectId)->findAll();
        
        foreach ($tasks as $task) {
            unset($task['id']);
            $task['project_id'] = $newProjectId;
            $task['status'] = 'not_started';
            $task['progress_percentage'] = 0;
            $task['actual_start_date'] = null;
            $task['actual_end_date'] = null;
            $task['actual_hours'] = 0;
            $task['actual_cost'] = 0;
            $task['created_by'] = session('user_id');
            
            // Generate new task code
            $task['task_code'] = $this->taskModel->generateTaskCode($newProjectId);
            
            $this->taskModel->insert($task);
        }
    }

    private function prepareGanttData($tasks, $milestones)
    {
        $ganttData = [];
        
        // Add tasks to Gantt data
        foreach ($tasks as $task) {
            $ganttData[] = [
                'id' => 'task_' . $task['id'],
                'text' => $task['title'],
                'start_date' => $task['planned_start_date'],
                'end_date' => $task['planned_end_date'],
                'progress' => $task['progress_percentage'] / 100,
                'type' => 'task',
                'parent' => $task['parent_task_id'] ? 'task_' . $task['parent_task_id'] : null
            ];
        }
        
        // Add milestones to Gantt data
        foreach ($milestones as $milestone) {
            $ganttData[] = [
                'id' => 'milestone_' . $milestone['id'],
                'text' => $milestone['title'],
                'start_date' => $milestone['planned_end_date'],
                'end_date' => $milestone['planned_end_date'],
                'type' => 'milestone'
            ];
        }
        
        return $ganttData;
    }

    private function getRecentActivities($projectId, $limit = 10)
    {
        // This would typically query an activity log table
        // For now, we'll return a simple structure
        return [
            [
                'activity' => 'Project created',
                'user' => 'System Administrator',
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'project'
            ],
            [
                'activity' => 'Project updated',
                'user' => 'Project Manager',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'type' => 'project'
            ],
            [
                'activity' => 'New task added',
                'user' => 'Team Lead',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'type' => 'task'
            ],
            [
                'activity' => 'Milestone completed',
                'user' => 'Site Supervisor',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'type' => 'milestone'
            ],
            [
                'activity' => 'Team member added',
                'user' => 'Project Manager',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'type' => 'team'
            ]
        ];
    }
}
