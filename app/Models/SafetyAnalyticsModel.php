<?php

namespace App\Models;

use CodeIgniter\Model;

class SafetyAnalyticsModel extends Model
{
    protected $table = 'safety_analytics';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'project_id', 'analytics_date',
        'total_incidents', 'critical_incidents', 'high_incidents',
        'medium_incidents', 'low_incidents', 'total_injured_people',
        'total_near_misses', 'safety_audits_conducted',
        'audit_compliance_percentage', 'average_resolution_days',
        'incidents_this_month', 'incidents_previous_month',
        'trend_direction', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|integer',
        'analytics_date' => 'required|valid_date',
    ];

    public function getAnalyticsForDate($companyId, $date)
    {
        return $this->where('company_id', $companyId)
                    ->where('analytics_date', $date)
                    ->first();
    }

    public function getAnalyticsForMonth($companyId, $month = null, $year = null)
    {
        if (!$month) {
            $month = date('m');
            $year = date('Y');
        }

        return $this->where('company_id', $companyId)
                    ->where('MONTH(analytics_date)', $month)
                    ->where('YEAR(analytics_date)', $year)
                    ->orderBy('analytics_date', 'DESC')
                    ->findAll();
    }

    public function getAnalyticsForProject($projectId, $companyId, $days = 90)
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->where('analytics_date >=', $startDate)
                    ->orderBy('analytics_date', 'ASC')
                    ->findAll();
    }

    public function getTrendData($companyId, $days = 180)
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        return $this->where('company_id', $companyId)
                    ->where('analytics_date >=', $startDate)
                    ->orderBy('analytics_date', 'ASC')
                    ->findAll();
    }

    public function createAnalytics($companyId, $date, $data)
    {
        $analyticsData = array_merge([
            'company_id' => $companyId,
            'analytics_date' => $date
        ], $data);

        return $this->insert($analyticsData);
    }

    public function updateAnalyticsForDate($companyId, $date, $data)
    {
        return $this->where('company_id', $companyId)
                    ->where('analytics_date', $date)
                    ->set($data)
                    ->update();
    }

    public function calculateTrend($companyId, $days = 30)
    {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $data = $this->where('company_id', $companyId)
                     ->where('analytics_date >=', $startDate)
                     ->where('analytics_date <=', $endDate)
                     ->orderBy('analytics_date', 'ASC')
                     ->findAll();

        if (count($data) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($data, 0, (int)floor(count($data) / 2));
        $secondHalf = array_slice($data, (int)floor(count($data) / 2));

        $avgFirstHalf = array_sum(array_column($firstHalf, 'total_incidents')) / count($firstHalf);
        $avgSecondHalf = array_sum(array_column($secondHalf, 'total_incidents')) / count($secondHalf);

        if ($avgSecondHalf < $avgFirstHalf) {
            return 'improving';
        } elseif ($avgSecondHalf > $avgFirstHalf) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
}
