<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectTeamMemberModel extends Model
{
    protected $table = 'project_team_members';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'project_id', 'user_id', 'role', 'assigned_by', 'assigned_at', 'removed_at', 'notes'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'project_id' => 'required|numeric',
        'user_id' => 'required|numeric',
        'role' => 'required|max_length[100]'
    ];

    public function getTeamMembers($projectId)
    {
        return $this->select('project_team_members.*, 
                            users.first_name, users.last_name, users.email, users.phone')
            ->join('users', 'project_team_members.user_id = users.id')
            ->where('project_team_members.project_id', $projectId)
            ->where('project_team_members.removed_at IS NULL')
            ->orderBy('project_team_members.role')
            ->findAll();
    }

    public function addTeamMember($projectId, $userId, $role, $assignedBy = null, $notes = null)
    {
        // Check if user is already a team member
        $existing = $this->where('project_id', $projectId)
                        ->where('user_id', $userId)
                        ->where('removed_at IS NULL')
                        ->first();

        if ($existing) {
            return false; // User already a team member
        }

        $data = [
            'project_id' => $projectId,
            'user_id' => $userId,
            'role' => $role,
            'assigned_by' => $assignedBy ?: session('user_id'),
            'assigned_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ];

        return $this->insert($data);
    }

    public function removeTeamMember($projectId, $userId)
    {
        return $this->where('project_id', $projectId)
                   ->where('user_id', $userId)
                   ->set('removed_at', date('Y-m-d H:i:s'))
                   ->update();
    }

    public function updateTeamMemberRole($projectId, $userId, $role)
    {
        return $this->where('project_id', $projectId)
                   ->where('user_id', $userId)
                   ->where('removed_at IS NULL')
                   ->set('role', $role)
                   ->update();
    }

    public function getUserProjects($userId)
    {
        return $this->select('project_team_members.*, projects.name as project_name, projects.status as project_status')
            ->join('projects', 'project_team_members.project_id = projects.id')
            ->where('project_team_members.user_id', $userId)
            ->where('project_team_members.removed_at IS NULL')
            ->where('projects.is_archived', false)
            ->orderBy('projects.created_at', 'DESC')
            ->findAll();
    }

    public function getTeamMembersByRole($projectId, $role)
    {
        return $this->select('project_team_members.*, users.first_name, users.last_name, users.email')
            ->join('users', 'project_team_members.user_id = users.id')
            ->where('project_team_members.project_id', $projectId)
            ->where('project_team_members.role', $role)
            ->where('project_team_members.removed_at IS NULL')
            ->findAll();
    }

    public function getAvailableUsers($projectId, $companyId = null)
    {
        $companyId = $companyId ?: session('company_id');
        
        // Get users who are not already team members
        $existingMembers = $this->select('user_id')
                               ->where('project_id', $projectId)
                               ->where('removed_at IS NULL')
                               ->findColumn('user_id');

        $userModel = new UserModel();
        $builder = $userModel->select('users.id, users.first_name, users.last_name, users.email, users.phone')
                            ->where('users.company_id', $companyId)
                            ->where('users.status', 'active');

        if (!empty($existingMembers)) {
            $builder->whereNotIn('users.id', $existingMembers);
        }

        return $builder->orderBy('users.first_name')->findAll();
    }

    public function getTeamStats($projectId)
    {
        $totalMembers = $this->where('project_id', $projectId)
                            ->where('removed_at IS NULL')
                            ->countAllResults();

        $roleStats = $this->select('role, COUNT(*) as count')
                         ->where('project_id', $projectId)
                         ->where('removed_at IS NULL')
                         ->groupBy('role')
                         ->findAll();

        return [
            'total_members' => $totalMembers,
            'role_distribution' => $roleStats
        ];
    }

    public function getProjectTeamMembers($projectId)
    {
        return $this->select('project_team_members.*, 
                            users.first_name, users.last_name, users.email, users.phone')
            ->join('users', 'project_team_members.user_id = users.id')
            ->where('project_team_members.project_id', $projectId)
            ->where('project_team_members.removed_at IS NULL')
            ->orderBy('project_team_members.role')
            ->findAll();
    }
}
