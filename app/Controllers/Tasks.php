<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\ProjectModel;
use App\Models\UserModel;

class Tasks extends BaseController
{
    protected $taskModel;
    protected $taskAttachmentModel;
    protected $userModel;
    protected $taskActivityLogModel;
    protected $projectModel;

    public function __construct()
    {
        helper('project');
        $this->taskModel = new TaskModel();
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
        $this->taskActivityLogModel = new \App\Models\TaskActivityLogModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $projectId = $this->request->getGet('project');
        $assignedTo = $this->request->getGet('assigned_to');

        $builder = $this->taskModel->select('tasks.*, projects.name as project_name, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
                                  ->join('projects', 'tasks.project_id = projects.id')
                                  ->join('users u', 'tasks.assigned_to = u.id', 'left')
                                  ->where('projects.company_id', session('company_id'));

        if ($status) {
            $builder->where('tasks.status', $status);
        }
        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }
        if ($assignedTo) {
            $builder->where('tasks.assigned_to', $assignedTo);
        }

        $tasks = $builder->orderBy('tasks.planned_end_date', 'ASC')->paginate(20);

        $data = [
            'title' => 'Tasks',
            'tasks' => $tasks,
            'pager' => $this->taskModel->pager,
            'projects' => $this->projectModel->getActiveProjects(),
            'users' => $this->userModel->getActiveEmployees(),
            'filters' => [
                'status' => $status,
                'project' => $projectId,
                'assigned_to' => $assignedTo
            ]
        ];

        return view('tasks/index', $data);
    }

    public function create()
    {
        $projectId = $this->request->getGet('project_id');
        
        $data = [
            'title' => 'Create New Task',
            'projects' => $this->projectModel->getActiveProjects(),
            'users' => $this->userModel->getActiveEmployees(),
            'selected_project' => $projectId,
            'task_code' => $projectId ? $this->taskModel->generateTaskCode($projectId) : null
        ];

        return view('tasks/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'project_id' => 'required|numeric',
            'title' => 'required|min_length[3]|max_length[255]',
            'task_type' => 'required|in_list[task,subtask]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'planned_start_date' => 'permit_empty|valid_date',
            'planned_end_date' => 'permit_empty|valid_date',
            'estimated_hours' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'estimated_cost' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'parent_task_id' => $this->request->getPost('parent_task_id') ?: null,
            'task_code' => $this->request->getPost('task_code') ?: $this->taskModel->generateTaskCode($this->request->getPost('project_id')),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'task_type' => $this->request->getPost('task_type'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'not_started',
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'assigned_by' => session('user_id'),
            'planned_start_date' => $this->request->getPost('planned_start_date'),
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'estimated_hours' => $this->request->getPost('estimated_hours') ?: 0,
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'depends_on' => $this->request->getPost('depends_on') ? implode(',', $this->request->getPost('depends_on')) : null,
            'is_critical_path' => $this->request->getPost('is_critical_path') ? true : false,
            'requires_approval' => $this->request->getPost('requires_approval') ? true : false,
            'is_billable' => $this->request->getPost('is_billable') ? true : false,
            'created_by' => session('user_id')
        ];

        $taskId = $this->taskModel->insert($data);

        if ($taskId) {
            // Log task creation
            $this->taskActivityLogModel->logTaskCreated($taskId, session('user_id'));
            
            // Send email notification to assigned user
            if ($data['assigned_to']) {
                $this->sendTaskAssignmentNotification($taskId);
            }

            return redirect()->to('/admin/tasks')->with('success', 'Task created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create task');
    }

    public function show($id)
    {
        $task = $this->taskModel->select('tasks.*, projects.name as project_name, projects.project_code,
                                        CONCAT(assigned.first_name, " ", assigned.last_name) as assigned_name,
                                        CONCAT(creator.first_name, " ", creator.last_name) as created_by_name')
                                ->join('projects', 'tasks.project_id = projects.id')
                                ->join('users assigned', 'tasks.assigned_to = assigned.id', 'left')
                                ->join('users creator', 'tasks.created_by = creator.id', 'left')
                                ->find($id);

        if (!$task) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Task not found');
        }

        $data = [
            'title' => $task['title'],
            'task' => $task,
            'comments' => $this->taskModel->getTaskComments($id),
            'attachments' => $this->taskModel->getTaskAttachments($id),
            'subtasks' => $this->taskModel->where('parent_task_id', $id)->findAll(),
            'dependencies' => $this->getTaskDependencies($task),
            'activity_log' => $this->getTaskActivityLog($id)
        ];

        return view('tasks/view', $data);
    }

    public function edit($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Task not found');
        }

        $data = [
            'title' => 'Edit Task',
            'task' => $task,
            'projects' => $this->projectModel->where('company_id', session('company_id'))->findAll(),
            'users' => $this->userModel->where('company_id', session('company_id'))->findAll(),
            'task_attachments' => $this->taskModel->getTaskAttachments($id)
        ];

        return view('tasks/edit', $data);
    }

    public function view($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Task not found');
        }

        $project = $this->projectModel->find($task['project_id']);
        $assignedUser = $task['assigned_to'] ? $this->userModel->find($task['assigned_to']) : null;

        $data = [
            'title' => 'Task Details',
            'task' => $task,
            'project' => $project,
            'assigned_user' => $assignedUser,
            'comments' => $this->taskModel->getTaskComments($id),
            'attachments' => $this->taskModel->getTaskAttachments($id),
            'dependencies' => $this->taskModel->getTaskDependencies($id)
        ];

        return view('tasks/view', $data);
    }

    public function update($id)
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Task not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'task_type' => 'required|in_list[task,subtask]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'status' => 'required|in_list[pending,in_progress,review,completed,cancelled,on_hold]',
            'planned_start_date' => 'permit_empty|valid_date',
            'planned_end_date' => 'permit_empty|valid_date',
            'estimated_hours' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'actual_hours' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'estimated_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'actual_cost' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'progress_percentage' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $originalAssignedTo = $task['assigned_to'];
        $newAssignedTo = $this->request->getPost('assigned_to');

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'task_type' => $this->request->getPost('task_type'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'assigned_to' => $newAssignedTo ?: null,
            'planned_start_date' => $this->request->getPost('planned_start_date'),
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'actual_start_date' => $this->request->getPost('actual_start_date'),
            'actual_end_date' => $this->request->getPost('actual_end_date'),
            'estimated_hours' => $this->request->getPost('estimated_hours') ?: 0,
            'actual_hours' => $this->request->getPost('actual_hours') ?: 0,
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'actual_cost' => $this->request->getPost('actual_cost') ?: 0,
            'progress_percentage' => $this->request->getPost('progress_percentage') ?: 0,
            'depends_on' => $this->request->getPost('depends_on') ? implode(',', $this->request->getPost('depends_on')) : null,
            'is_critical_path' => $this->request->getPost('is_critical_path') ? true : false,
            'requires_approval' => $this->request->getPost('requires_approval') ? true : false,
            'is_billable' => $this->request->getPost('is_billable') ? true : false
        ];

        try {
            $result = $this->taskModel->update($id, $data);
            if ($result) {
                // Send notification if assignee changed
                if ($originalAssignedTo != $newAssignedTo && $newAssignedTo) {
                    $this->sendTaskAssignmentNotification($id);
                }

                // Update project progress
                $this->projectModel->updateProjectProgress($task['project_id']);

                return redirect()->to('/admin/tasks/' . $id)->with('success', 'Task updated successfully');
            } else {
                // Get the database error
                $dbError = $this->taskModel->db->error();
                $errorMessage = 'Failed to update task';
                
                if (!empty($dbError['message'])) {
                    $errorMessage = 'Database error: ' . $dbError['message'];
                }
                
                // Log the update failure with database error
                log_message('error', 'Task update failed for task ID: ' . $id . '. Data: ' . json_encode($data) . '. DB Error: ' . json_encode($dbError));
                return redirect()->back()->withInput()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the exception
            log_message('error', 'Exception during task update: ' . $e->getMessage() . ' for task ID: ' . $id);
            return redirect()->back()->withInput()->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return $this->response->setJSON(['success' => false, 'message' => 'Task not found']);
        }

        $status = $this->request->getPost('status');
        
        // Map frontend status names to backend status names
        $statusMapping = [
            'not_started' => 'pending',
            'in_progress' => 'in_progress',
            'review' => 'review',
            'completed' => 'completed',
            'on_hold' => 'on_hold',
            'cancelled' => 'cancelled'
        ];
        
        // Use mapped status or original if not in mapping
        $mappedStatus = $statusMapping[$status] ?? $status;
        $validStatuses = ['pending', 'in_progress', 'review', 'completed', 'on_hold', 'cancelled'];
        
        if (!in_array($mappedStatus, $validStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        $updateData = ['status' => $mappedStatus];
        
        // Auto-update progress based on status
        if ($mappedStatus === 'completed') {
            $updateData['progress_percentage'] = 100;
            $updateData['actual_end_date'] = date('Y-m-d H:i:s');
        } elseif ($mappedStatus === 'in_progress' && $task['progress_percentage'] == 0) {
            $updateData['progress_percentage'] = 25;
        } elseif ($mappedStatus === 'pending') {
            $updateData['progress_percentage'] = 0;
            $updateData['actual_end_date'] = null;
        }

        try {
            if ($this->taskModel->update($id, $updateData)) {
                return $this->response->setJSON(['success' => true]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function addComment($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $rules = [
            'comment' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $commentData = [
            'task_id' => $id,
            'user_id' => session('user_id'),
            'comment' => $this->request->getPost('comment'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->taskModel->addTaskComment($commentData)) {
                // Log comment addition
                $this->taskActivityLogModel->logCommentAdded($id, session('user_id'));
                return redirect()->back()->with('success', 'Comment added successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding comment: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Failed to add comment');
    }

    public function deleteComment($commentId)
    {
        try {
            if ($this->taskModel->deleteTaskComment($commentId, session('user_id'))) {
                return $this->response->setJSON(['success' => true]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete comment']);
    }

    public function uploadAttachment($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $file = $this->request->getFile('attachment');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please select a valid file');
        }

        // Validate file type and size
        $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        if (!in_array($file->getExtension(), $allowedTypes)) {
            return redirect()->back()->with('error', 'File type not allowed');
        }

        if ($file->getSize() > $maxSize) {
            return redirect()->back()->with('error', 'File size too large. Maximum 10MB allowed');
        }

        try {
            $uploadPath = WRITEPATH . 'uploads/tasks/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fileName = $file->getRandomName();
            if ($file->move($uploadPath, $fileName)) {
                $attachmentData = [
                    'task_id' => $id,
                    'file_name' => $file->getClientName(),
                    'file_path' => $uploadPath . $fileName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                    'uploaded_by' => session('user_id'),
                    'uploaded_at' => date('Y-m-d H:i:s')
                ];

                if ($this->taskModel->addTaskAttachment($attachmentData)) {
                    // Log attachment addition
                    $this->taskActivityLogModel->logAttachmentAdded($id, session('user_id'), $file->getClientName());
                    return redirect()->back()->with('success', 'File uploaded successfully');
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error uploading file: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Failed to upload file');
    }

    public function deleteAttachment($attachmentId)
    {
        try {
            if ($this->taskModel->deleteTaskAttachment($attachmentId, session('user_id'))) {
                return $this->response->setJSON(['success' => true]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete attachment']);
    }

    public function download($attachmentId)
    {
        $attachment = $this->taskModel->getTaskAttachment($attachmentId);
        if (!$attachment || !file_exists($attachment['file_path'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        return $this->response->download($attachment['file_path'], null)
                              ->setFileName($attachment['file_name']);
    }

    public function logTime($id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $rules = [
            'hours' => 'required|numeric|greater_than[0]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $timeLogData = [
            'task_id' => $id,
            'user_id' => session('user_id'),
            'hours' => $this->request->getPost('hours'),
            'description' => $this->request->getPost('description'),
            'logged_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->taskModel->addTimeLog($timeLogData)) {
                // Update actual hours in task
                $totalHours = $this->taskModel->getTotalLoggedHours($id);
                $this->taskModel->update($id, ['actual_hours' => $totalHours]);
                
                return redirect()->back()->with('success', 'Time logged successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error logging time: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Failed to log time');
    }

    public function getProjectTasks($projectId)
    {
        $tasks = $this->taskModel->where('project_id', $projectId)
                                ->select('id, title, status, progress_percentage')
                                ->findAll();

        return $this->response->setJSON(['tasks' => $tasks]);
    }

    private function getTaskDependencies($task)
    {
        if (empty($task['depends_on'])) {
            return [];
        }

        $dependencyIds = explode(',', $task['depends_on']);
        return $this->taskModel->whereIn('id', $dependencyIds)->findAll();
    }

    private function getTaskActivityLog($taskId)
    {
        return $this->taskActivityLogModel->getTaskActivityLog($taskId, 50);
    }

    private function prepareCalendarEvents($tasks)
    {
        $events = [];
        
        foreach ($tasks as $task) {
            $events[] = [
                'id' => $task['id'],
                'title' => $task['title'],
                'start' => $task['planned_start_date'],
                'end' => $task['planned_end_date'],
                'className' => $this->getTaskStatusClass($task['status']),
                'url' => '/tasks/' . $task['id']
            ];
        }
        
        return $events;
    }

    private function getTaskStatusClass($status)
    {
        $classes = [
            'not_started' => 'fc-event-info',
            'in_progress' => 'fc-event-warning',
            'review' => 'fc-event-primary',
            'completed' => 'fc-event-success',
            'cancelled' => 'fc-event-danger',
            'on_hold' => 'fc-event-secondary'
        ];

        return $classes[$status] ?? 'fc-event-default';
    }

    private function sendTaskAssignmentNotification($taskId)
    {
        // This would send an email notification
        // Implementation depends on your email service setup
        // For now, just log the action
        log_message('info', "Task assignment notification should be sent for task ID: {$taskId}");
    }

    /**
     * Generate task code for AJAX requests
     */
    public function generateCode()
    {
        $projectId = $this->request->getGet('project_id');
        
        if (!$projectId) {
            return $this->response->setStatusCode(400)->setBody('Project ID is required');
        }
        
        $taskCode = $this->taskModel->generateTaskCode($projectId);
        
        return $this->response->setContentType('text/plain')->setBody($taskCode);
    }

    /**
     * Get tasks by project for AJAX requests
     */
    public function myTasks()
    {
        $userId = session('user_id');

        $builder = $this->taskModel->select('tasks.*, projects.name as project_name, projects.project_code, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
                                  ->join('projects', 'tasks.project_id = projects.id')
                                  ->join('users u', 'tasks.assigned_to = u.id', 'left')
                                  ->where('tasks.assigned_to', $userId)
                                  ->where('projects.company_id', session('company_id'))
                                  ->where('tasks.status !=', 'cancelled');

        $tasks = $builder->orderBy('tasks.planned_end_date', 'ASC')->paginate(20);

        $data = [
            'title' => 'My Tasks',
            'tasks' => $tasks,
            'pager' => $this->taskModel->pager,
            'projects' => $this->projectModel->getActiveProjects(),
            'users' => $this->userModel->getActiveEmployees(),
            'filters' => [
                'status' => null,
                'project' => null,
                'assigned_to' => $userId
            ]
        ];

        return view('tasks/index', $data);
    }

    public function calendar()
    {
        $data = [
            'title' => 'Task Calendar',
            'tasks' => $this->taskModel->select('tasks.*, projects.name as project_name')
                                      ->join('projects', 'tasks.project_id = projects.id')
                                      ->where('projects.company_id', session('company_id'))
                                      ->where('tasks.status !=', 'cancelled')
                                      ->findAll()
        ];

        return view('tasks/calendar', $data);
    }

    /**
     * API endpoint for calendar events
     */
    public function apiCalendarEvents()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');

        $tasks = $this->taskModel->select('tasks.*, projects.name as project_name, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
                                ->join('projects', 'tasks.project_id = projects.id')
                                ->join('users u', 'tasks.assigned_to = u.id', 'left')
                                ->where('projects.company_id', session('company_id'))
                                ->where('tasks.status !=', 'cancelled');

        if ($start && $end) {
            $tasks->where('tasks.planned_end_date >=', $start)
                  ->where('tasks.planned_end_date <=', $end);
        }

        $tasks = $tasks->findAll();

        return $this->response->setJSON(['tasks' => $tasks]);
    }

    /**
     * API endpoint for task details
     */
    public function apiTaskDetails($id)
    {
        $task = $this->taskModel->select('tasks.*, projects.name as project_name, projects.project_code, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
                               ->join('projects', 'tasks.project_id = projects.id')
                               ->join('users u', 'tasks.assigned_to = u.id', 'left')
                               ->find($id);

        if (!$task) {
            return $this->response->setJSON(['error' => 'Task not found'], 404);
        }

        return $this->response->setJSON(['task' => $task]);
    }

    public function byProject($projectId)
    {
        if (!$projectId) {
            return $this->response->setJSON(['error' => 'Project ID is required'], 400);
        }

        $tasks = $this->taskModel->where('project_id', $projectId)
                                ->where('status !=', 'cancelled')
                                ->select('id, title, task_code, task_type')
                                ->findAll();

        return $this->response->setJSON($tasks);
    }

    /**
     * Delete a task
     */
    public function delete($id)
    {
        try {
            $task = $this->taskModel->find($id);
            
            if (!$task) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Task not found'], 404);
                }
                return redirect()->back()->with('error', 'Task not found');
            }

            // Check if user has permission to delete this task
            // You can add authorization logic here

            // Delete the task
            if ($this->taskModel->delete($id)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Task deleted successfully']);
                }
                return redirect()->to('/admin/tasks')->with('success', 'Task deleted successfully');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete task'], 500);
                }
                return redirect()->back()->with('error', 'Failed to delete task');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting task: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'An error occurred while deleting the task'], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while deleting the task');
        }
    }
}