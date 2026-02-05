<?php

namespace App\Models;

use CodeIgniter\Model;

class SafetyReportModel extends Model
{
    protected $table = 'safety_reports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'project_id', 'report_code', 'report_type',
        'report_period_start', 'report_period_end', 'generated_by',
        'total_incidents_reported', 'total_near_misses',
        'total_injured_workers', 'lost_time_incidents',
        'safety_audits_conducted', 'training_sessions_held',
        'key_highlights', 'challenges_identified', 'recommendations',
        'report_file_path', 'status', 'approved_by', 'approved_date',
        'distribution_list'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'report_date';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|integer',
        'report_type' => 'required|in_list[daily,weekly,monthly,quarterly,annual]',
        'generated_by' => 'required|integer',
    ];

    public function getReportsByProject($projectId, $companyId)
    {
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->orderBy('report_period_end', 'DESC')
                    ->findAll();
    }

    public function getReportsByType($reportType, $companyId)
    {
        return $this->where('report_type', $reportType)
                    ->where('company_id', $companyId)
                    ->orderBy('report_period_end', 'DESC')
                    ->findAll();
    }

    public function getReportsByStatus($status, $companyId)
    {
        return $this->where('status', $status)
                    ->where('company_id', $companyId)
                    ->orderBy('report_period_end', 'DESC')
                    ->findAll();
    }

    public function getApprovedReports($companyId)
    {
        return $this->where('status', 'approved')
                    ->where('company_id', $companyId)
                    ->orderBy('report_period_end', 'DESC')
                    ->findAll();
    }

    public function getDraftReports($companyId)
    {
        return $this->where('status', 'draft')
                    ->where('company_id', $companyId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getReportById($reportId, $companyId)
    {
        return $this->select('safety_reports.*, projects.name as project_name, 
                             CONCAT(generator.first_name, " ", generator.last_name) as generated_by_name,
                             CONCAT(approver.first_name, " ", approver.last_name) as approved_by_name')
                    ->join('projects', 'projects.id = safety_reports.project_id', 'left')
                    ->join('users as generator', 'generator.id = safety_reports.generated_by', 'left')
                    ->join('users as approver', 'approver.id = safety_reports.approved_by', 'left')
                    ->where('safety_reports.id', $reportId)
                    ->where('safety_reports.company_id', $companyId)
                    ->first();
    }

    public function approveReport($reportId, $approvedBy)
    {
        return $this->update($reportId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_date' => date('Y-m-d H:i:s')
        ]);
    }

    public function publishReport($reportId)
    {
        return $this->update($reportId, ['status' => 'published']);
    }

    public function generateReportCode($companyId)
    {
        $year = date('Y');
        $count = $this->where('company_id', $companyId)
                      ->where('YEAR(created_at)', $year)
                      ->countAllResults() + 1;

        return sprintf('SAFREP-%s-%05d', $year, $count);
    }

    public function getReportsByPeriod($startDate, $endDate, $companyId)
    {
        return $this->where('company_id', $companyId)
                    ->where('report_period_start >=', $startDate)
                    ->where('report_period_end <=', $endDate)
                    ->orderBy('report_period_end', 'DESC')
                    ->findAll();
    }
}
