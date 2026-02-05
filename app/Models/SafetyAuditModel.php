<?php

namespace App\Models;

use CodeIgniter\Model;

class SafetyAuditModel extends Model
{
    protected $table = 'safety_audits';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'project_id', 'incident_id', 'audit_code',
        'audit_date', 'audit_type', 'auditor_id', 'audit_scope',
        'findings_summary', 'total_observations', 'critical_findings',
        'major_findings', 'minor_findings', 'conformance_percentage',
        'status', 'document_path', 'due_date_for_corrections', 'follow_up_date'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|integer',
        'audit_date' => 'required|valid_date',
        'audit_type' => 'required|in_list[routine,incident_related,compliance,follow_up]',
        'auditor_id' => 'required|integer',
    ];

    public function getAuditsByProject($projectId, $companyId)
    {
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('audit_date', 'DESC')
                    ->findAll();
    }

    public function getAuditsByType($auditType, $projectId, $companyId)
    {
        return $this->where('audit_type', $auditType)
                    ->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('audit_date', 'DESC')
                    ->findAll();
    }

    public function getAuditsByStatus($status, $companyId)
    {
        return $this->where('status', $status)
                    ->where('company_id', $companyId)
                    ->orderBy('audit_date', 'DESC')
                    ->findAll();
    }

    public function getCompletedAudits($projectId, $companyId)
    {
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->where('status', 'completed')
                    ->orderBy('audit_date', 'DESC')
                    ->findAll();
    }

    public function getRecentAudits($companyId, $limit = 10)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('audit_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getAuditById($auditId, $companyId)
    {
        return $this->select('safety_audits.*, projects.name as project_name, 
                             CONCAT(auditor.first_name, " ", auditor.last_name) as auditor_name')
                    ->join('projects', 'projects.id = safety_audits.project_id', 'left')
                    ->join('users as auditor', 'auditor.id = safety_audits.auditor_id', 'left')
                    ->where('safety_audits.id', $auditId)
                    ->where('safety_audits.company_id', $companyId)
                    ->first();
    }

    public function generateAuditCode($companyId)
    {
        $year = date('Y');
        $month = date('m');
        $count = $this->where('company_id', $companyId)
                      ->where('YEAR(created_at)', $year)
                      ->where('MONTH(created_at)', $month)
                      ->countAllResults() + 1;

        return sprintf('AUDIT-%s%s-%05d', $year, $month, $count);
    }

    public function getAuditCountByStatus($companyId, $status)
    {
        return $this->where('company_id', $companyId)
                    ->where('status', $status)
                    ->countAllResults();
    }

    public function getHighConformanceRate($companyId, $threshold = 80)
    {
        return $this->where('company_id', $companyId)
                    ->where('conformance_percentage >=', $threshold)
                    ->countAllResults();
    }
}
