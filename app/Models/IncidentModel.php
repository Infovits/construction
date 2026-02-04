<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentModel extends Model
{
    protected $table = 'incidents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'project_id', 'incident_code', 'incident_type_id',
        'severity_id', 'title', 'description', 'incident_date', 'reported_by',
        'location', 'affected_people_count', 'affected_people_names',
        'witness_count', 'witness_names', 'injuries_sustained',
        'property_damage_description', 'immediate_actions_taken', 'status',
        'assigned_to', 'investigation_findings', 'investigation_completed_date',
        'investigation_completed_by', 'is_safety_audit_required', 'is_documented'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|integer',
        'incident_type_id' => 'required|integer',
        'severity_id' => 'required|integer',
        'title' => 'required|string|max_length[255]',
        'description' => 'required|string',
        'incident_date' => 'required|valid_date',
        'reported_by' => 'required|integer',
    ];

    public function getIncidentsByProject($projectId, $companyId, $limit = 25, $offset = 0)
    {
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('incident_date', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    public function getIncidentsByStatus($status, $projectId, $companyId)
    {
        return $this->where('status', $status)
                    ->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('incident_date', 'DESC')
                    ->findAll();
    }

    public function getIncidentsBySeverity($severityId, $projectId, $companyId)
    {
        return $this->where('severity_id', $severityId)
                    ->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('incident_date', 'DESC')
                    ->findAll();
    }

    public function getIncidentsByType($typeId, $projectId, $companyId)
    {
        return $this->where('incident_type_id', $typeId)
                    ->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('incident_date', 'DESC')
                    ->findAll();
    }

    public function getIncidentById($incidentId, $companyId)
    {
        return $this->select('incidents.*, projects.name as project_name, 
                             CONCAT(reporter.first_name, " ", reporter.last_name) as reported_by_name, 
                             CONCAT(assigned.first_name, " ", assigned.last_name) as assigned_to_name,
                             CONCAT(investigator.first_name, " ", investigator.last_name) as investigator_name')
                    ->join('projects', 'projects.id = incidents.project_id', 'left')
                    ->join('users as reporter', 'reporter.id = incidents.reported_by', 'left')
                    ->join('users as assigned', 'assigned.id = incidents.assigned_to', 'left')
                    ->join('users as investigator', 'investigator.id = incidents.investigation_completed_by', 'left')
                    ->where('incidents.id', $incidentId)
                    ->where('incidents.company_id', $companyId)
                    ->first();
    }

    public function searchIncidents($companyId, $searchTerm, $projectId = null)
    {
        $query = $this->where('company_id', $companyId);
        
        if ($projectId) {
            $query = $query->where('project_id', $projectId);
        }

        return $query->like('title', $searchTerm)
                    ->orLike('incident_code', $searchTerm)
                    ->orLike('description', $searchTerm)
                    ->orderBy('incident_date', 'DESC')
                    ->findAll();
    }

    public function getRecentIncidents($companyId, $limit = 10)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('incident_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getOpenIncidents($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->whereIn('status', ['reported', 'investigating', 'under_review', 'reopened'])
                    ->orderBy('incident_date', 'DESC')
                    ->findAll();
    }

    public function getCriticalIncidents($companyId, $timeframe = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$timeframe} days"));
        
        return $this->where('incidents.company_id', $companyId)
                ->join('incident_severity_levels', 'incidents.severity_id = incident_severity_levels.id')
                ->where('incident_severity_levels.numeric_level >=', 3)
                ->where('incidents.incident_date >=', $startDate)
                ->select('incidents.*')
                ->orderBy('incidents.incident_date', 'DESC')
                ->findAll();
    }

    public function generateIncidentCode($companyId)
    {
        $year = date('Y');
        $month = date('m');
        $count = $this->where('company_id', $companyId)
                      ->where('YEAR(created_at)', $year)
                      ->where('MONTH(created_at)', $month)
                      ->countAllResults() + 1;

        return sprintf('INC-%s%s-%05d', $year, $month, $count);
    }

    public function getIncidentCount($companyId)
    {
        return $this->where('company_id', $companyId)->countAllResults();
    }

    public function getIncidentCountByStatus($companyId, $status)
    {
        return $this->where('company_id', $companyId)
                    ->where('status', $status)
                    ->countAllResults();
    }
}
