<?php

namespace App\Controllers;

use App\Models\IncidentModel;
use App\Models\IncidentTypeModel;
use App\Models\IncidentSeverityModel;
use App\Models\IncidentPhotoModel;
use App\Models\IncidentActionStepModel;
use App\Models\SafetyAuditModel;
use App\Models\SafetyAnalyticsModel;
use App\Models\SafetyReportModel;
use App\Models\ProjectModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class IncidentSafety extends BaseController
{
    protected $incidentModel;
    protected $typeModel;
    protected $severityModel;
    protected $photoModel;
    protected $actionStepModel;
    protected $auditModel;
    protected $analyticsModel;
    protected $reportModel;
    protected $projectModel;
    protected $userModel;

    public function __construct()
    {
        $this->incidentModel = new IncidentModel();
        $this->typeModel = new IncidentTypeModel();
        $this->severityModel = new IncidentSeverityModel();
        $this->photoModel = new IncidentPhotoModel();
        $this->actionStepModel = new IncidentActionStepModel();
        $this->auditModel = new SafetyAuditModel();
        $this->analyticsModel = new SafetyAnalyticsModel();
        $this->reportModel = new SafetyReportModel();
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
    }

    // =============== INCIDENTS ===============

    public function incidents()
    {
        $companyId = session('company_id');
        $projectId = $this->request->getGet('project_id');
        $filterType = $this->request->getGet('type');
        $filterSeverity = $this->request->getGet('severity');
        $filterStatus = $this->request->getGet('status');

        $incidents = $this->incidentModel->where('company_id', $companyId);

        if ($projectId) {
            $incidents = $incidents->where('project_id', $projectId);
        }

        if ($filterType) {
            $incidents = $incidents->where('incident_type_id', $filterType);
        }

        if ($filterSeverity) {
            $incidents = $incidents->where('severity_id', $filterSeverity);
        }

        if ($filterStatus) {
            $incidents = $incidents->where('status', $filterStatus);
        }

        $incidents = $incidents->orderBy('incident_date', 'DESC')->paginate(25);

        $projects = $this->projectModel->where('company_id', $companyId)->findAll();
        $types = $this->typeModel->getActiveTypes($companyId);
        $severities = $this->severityModel->getActiveSeverities($companyId);

        $data = [
            'incidents' => $incidents,
            'projects' => $projects,
            'types' => $types,
            'severities' => $severities,
            'pager' => $this->incidentModel->pager,
            'title' => 'Incident Reports'
        ];

        return view('incidentsafety/incidents/list', $data);
    }

    public function createIncident()
    {
        $companyId = session('company_id');

        if ($this->request->getMethod() === 'post') {
            return $this->storeIncident();
        }

        $projects = $this->projectModel->where('company_id', $companyId)->findAll();
        $types = $this->typeModel->getActiveTypes($companyId);
        $severities = $this->severityModel->getActiveSeverities($companyId);

        $data = [
            'projects' => $projects,
            'types' => $types,
            'severities' => $severities,
            'title' => 'Report Incident'
        ];

        return view('incidentsafety/incidents/create', $data);
    }

    public function storeIncident()
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        $incidentCode = $this->incidentModel->generateIncidentCode($companyId);

        $incidentData = [
            'company_id' => $companyId,
            'project_id' => $this->request->getPost('project_id'),
            'incident_code' => $incidentCode,
            'incident_type_id' => $this->request->getPost('incident_type_id'),
            'severity_id' => $this->request->getPost('severity_id'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'incident_date' => $this->request->getPost('incident_date') . ' ' . 
                              ($this->request->getPost('incident_time') ?: '00:00:00'),
            'reported_by' => $userId,
            'location' => $this->request->getPost('location'),
            'affected_people_count' => $this->request->getPost('affected_people_count') ?: 0,
            'affected_people_names' => $this->request->getPost('affected_people_names'),
            'witness_count' => $this->request->getPost('witness_count') ?: 0,
            'witness_names' => $this->request->getPost('witness_names'),
            'injuries_sustained' => $this->request->getPost('injuries_sustained'),
            'property_damage_description' => $this->request->getPost('property_damage_description'),
            'immediate_actions_taken' => $this->request->getPost('immediate_actions_taken'),
            'status' => 'reported'
        ];

        if ($this->incidentModel->insert($incidentData)) {
            $incidentId = $this->incidentModel->getInsertID();

            // Handle photo uploads
            $this->uploadIncidentPhotos($incidentId, $companyId);

            return redirect()->to('/incident-safety/incidents/' . $incidentId)
                           ->with('success', 'Incident reported successfully');
        }

        return redirect()->back()->with('error', 'Failed to report incident');
    }

    public function viewIncident($incidentId)
    {
        $companyId = session('company_id');

        $incident = $this->incidentModel->getIncidentById($incidentId, $companyId);
        if (!$incident) {
            throw new PageNotFoundException('Incident not found');
        }

        $photos = $this->photoModel->getIncidentPhotos($incidentId);
        $actionSteps = $this->actionStepModel->getIncidentActions($incidentId);
        $type = $this->typeModel->find($incident['incident_type_id']);
        $severity = $this->severityModel->find($incident['severity_id']);

        $data = [
            'incident' => $incident,
            'photos' => $photos,
            'actionSteps' => $actionSteps,
            'type' => $type,
            'severity' => $severity,
            'title' => 'Incident Details - ' . $incident['incident_code']
        ];

        return view('incidentsafety/incidents/view', $data);
    }

    public function updateIncidentStatus($incidentId)
    {
        if (!$this->request->isPost()) {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $userId = session('user_id');

        $incident = $this->incidentModel->getIncidentById($incidentId, $companyId);
        if (!$incident) {
            return $this->response->setJSON(['success' => false, 'message' => 'Incident not found'])->setStatusCode(404);
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'resolved') {
            $updateData['investigation_findings'] = $notes;
            $updateData['investigation_completed_date'] = date('Y-m-d');
            $updateData['investigation_completed_by'] = $userId;
        }

        if ($this->incidentModel->update($incidentId, $updateData)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function uploadIncidentPhotos($incidentId, $companyId)
    {
        $userId = session('user_id');
        
        // Check if files were uploaded
        $files = $this->request->getFileMultiple('photos');
        
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            // Check if file is valid
            if (!$file->isValid() || $file->hasMoved()) {
                continue;
            }

            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/incidents/' . $companyId . '/' . $incidentId . '/';

            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            if ($file->move($uploadPath, $fileName)) {
                $photoData = [
                    'incident_id' => $incidentId,
                    'photo_path' => 'writable/uploads/incidents/' . $companyId . '/' . $incidentId . '/' . $fileName,
                    'original_file_name' => $file->getClientName(),
                    'photo_type' => $this->request->getPost('photo_type') ?? 'evidence',
                    'description' => $this->request->getPost('photo_description'),
                    'uploaded_by' => $userId
                ];

                $this->photoModel->insert($photoData);
            }
        }
    }

    // =============== ACTION STEPS ===============

    public function addActionStep($incidentId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');

        $incident = $this->incidentModel->getIncidentById($incidentId, $companyId);
        if (!$incident) {
            return $this->response->setJSON(['success' => false, 'message' => 'Incident not found'])->setStatusCode(404);
        }

        $actionNumber = $this->actionStepModel->getNextActionNumber($incidentId);

        $actionData = [
            'incident_id' => $incidentId,
            'action_number' => $actionNumber,
            'action_description' => $this->request->getPost('action_description'),
            'assigned_to' => $this->request->getPost('assigned_to'),
            'due_date' => $this->request->getPost('due_date'),
            'is_critical' => $this->request->getPost('is_critical') ? 1 : 0
        ];

        if ($this->actionStepModel->insert($actionData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Action step added successfully'
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to add action step']);
    }

    public function completeActionStep($actionStepId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $completionNotes = $this->request->getPost('completion_notes');

        if ($this->actionStepModel->markAsCompleted($actionStepId, $completionNotes)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Action step marked as completed']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update action step']);
    }

    // =============== SAFETY AUDITS ===============

    public function audits()
    {
        $companyId = session('company_id');
        $projectId = $this->request->getGet('project_id');
        $auditType = $this->request->getGet('type');
        $status = $this->request->getGet('status');

        $audits = $this->auditModel->where('company_id', $companyId);

        if ($projectId) {
            $audits = $audits->where('project_id', $projectId);
        }

        if ($auditType) {
            $audits = $audits->where('audit_type', $auditType);
        }

        if ($status) {
            $audits = $audits->where('status', $status);
        }

        $audits = $audits->orderBy('audit_date', 'DESC')->paginate(25);

        $projects = $this->projectModel->where('company_id', $companyId)->findAll();

        $data = [
            'audits' => $audits,
            'projects' => $projects,
            'pager' => $this->auditModel->pager,
            'title' => 'Safety Audits'
        ];

        return view('incidentsafety/audits/list', $data);
    }

    public function createAudit()
    {
        $companyId = session('company_id');

        if ($this->request->getMethod() === 'post') {
            return $this->storeAudit();
        }

        $projects = $this->projectModel->where('company_id', $companyId)->findAll();

        $data = [
            'projects' => $projects,
            'title' => 'Create Safety Audit'
        ];

        return view('incidentsafety/audits/create', $data);
    }

    public function storeAudit()
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        $auditCode = $this->auditModel->generateAuditCode($companyId);

        $auditData = [
            'company_id' => $companyId,
            'project_id' => $this->request->getPost('project_id'),
            'audit_code' => $auditCode,
            'audit_date' => $this->request->getPost('audit_date'),
            'audit_type' => $this->request->getPost('audit_type'),
            'auditor_id' => $userId,
            'audit_scope' => $this->request->getPost('audit_scope'),
            'total_observations' => $this->request->getPost('total_observations') ?: 0,
            'critical_findings' => $this->request->getPost('critical_findings') ?: 0,
            'major_findings' => $this->request->getPost('major_findings') ?: 0,
            'minor_findings' => $this->request->getPost('minor_findings') ?: 0,
            'conformance_percentage' => $this->request->getPost('conformance_percentage') ?: 0,
            'status' => 'draft'
        ];

        if ($this->auditModel->insert($auditData)) {
            $auditId = $this->auditModel->getInsertID();
            return redirect()->to('/incident-safety/audits/' . $auditId)
                           ->with('success', 'Safety audit created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create safety audit');
    }

    public function viewAudit($auditId)
    {
        $companyId = session('company_id');

        $audit = $this->auditModel->getAuditById($auditId, $companyId);
        if (!$audit) {
            throw new PageNotFoundException('Audit not found');
        }

        $data = [
            'audit' => $audit,
            'title' => 'Safety Audit - ' . $audit['audit_code']
        ];

        return view('incidentsafety/audits/view', $data);
    }

    // =============== SAFETY REPORTS ===============

    public function reports()
    {
        $companyId = session('company_id');
        $reportType = $this->request->getGet('type');
        $status = $this->request->getGet('status');

        $reports = $this->reportModel->where('company_id', $companyId);

        if ($reportType) {
            $reports = $reports->where('report_type', $reportType);
        }

        if ($status) {
            $reports = $reports->where('status', $status);
        }

        $reports = $reports->orderBy('report_period_end', 'DESC')->paginate(25);

        $data = [
            'reports' => $reports,
            'pager' => $this->reportModel->pager,
            'title' => 'Safety Reports'
        ];

        return view('incidentsafety/reports/list', $data);
    }

    public function createReport()
    {
        $companyId = session('company_id');

        if ($this->request->getMethod() === 'post') {
            return $this->storeReport();
        }

        $projects = $this->projectModel->where('company_id', $companyId)->findAll();

        $data = [
            'projects' => $projects,
            'title' => 'Create Safety Report'
        ];

        return view('incidentsafety/reports/create', $data);
    }

    public function storeReport()
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        $reportCode = $this->reportModel->generateReportCode($companyId);

        $reportData = [
            'company_id' => $companyId,
            'project_id' => $this->request->getPost('project_id'),
            'report_code' => $reportCode,
            'report_type' => $this->request->getPost('report_type'),
            'report_period_start' => $this->request->getPost('report_period_start'),
            'report_period_end' => $this->request->getPost('report_period_end'),
            'generated_by' => $userId,
            'total_incidents_reported' => $this->request->getPost('total_incidents_reported') ?: 0,
            'total_near_misses' => $this->request->getPost('total_near_misses') ?: 0,
            'total_injured_workers' => $this->request->getPost('total_injured_workers') ?: 0,
            'key_highlights' => $this->request->getPost('key_highlights'),
            'challenges_identified' => $this->request->getPost('challenges_identified'),
            'recommendations' => $this->request->getPost('recommendations'),
            'status' => 'draft'
        ];

        if ($this->reportModel->insert($reportData)) {
            $reportId = $this->reportModel->getInsertID();
            return redirect()->to('/incident-safety/reports/' . $reportId)
                           ->with('success', 'Safety report created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create safety report');
    }

    public function viewReport($reportId)
    {
        $companyId = session('company_id');

        $report = $this->reportModel->getReportById($reportId, $companyId);
        if (!$report) {
            throw new PageNotFoundException('Report not found');
        }

        $data = [
            'report' => $report,
            'title' => 'Safety Report - ' . $report['report_code']
        ];

        return view('incidentsafety/reports/view', $data);
    }

    // =============== ANALYTICS ===============

    public function analytics()
    {
        $companyId = session('company_id');
        $projectId = $this->request->getGet('project_id');

        // Get all projects for filter dropdown
        $projectModel = model('ProjectModel');
        $projects = $projectModel->where('company_id', $companyId)
                                  ->where('is_archived', 0)
                                  ->findAll();

        // Calculate current metrics
        $incidentsQuery = $this->incidentModel->where('company_id', $companyId);
        if ($projectId) {
            $incidentsQuery = $incidentsQuery->where('project_id', $projectId);
        }
        $totalIncidents = $incidentsQuery->countAllResults();

        $criticalIncidents = $this->incidentModel->where('company_id', $companyId)
                                                   ->where('severity_id', 4);
        if ($projectId) {
            $criticalIncidents = $criticalIncidents->where('project_id', $projectId);
        }
        $criticalIncidents = $criticalIncidents->countAllResults();

        $openIncidents = count($this->incidentModel->getOpenIncidents($companyId, $projectId));

        // Get incidents for this month and previous month
        $thisMonthStart = date('Y-m-01');
        $thisMonthIncidents = $this->incidentModel->where('company_id', $companyId)
                                                    ->where('incident_date >=', $thisMonthStart);
        if ($projectId) {
            $thisMonthIncidents = $thisMonthIncidents->where('project_id', $projectId);
        }
        $thisMonthIncidents = $thisMonthIncidents->countAllResults();

        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));
        $lastMonthIncidents = $this->incidentModel->where('company_id', $companyId)
                                                    ->where('incident_date >=', $lastMonthStart)
                                                    ->where('incident_date <=', $lastMonthEnd);
        if ($projectId) {
            $lastMonthIncidents = $lastMonthIncidents->where('project_id', $projectId);
        }
        $lastMonthIncidents = $lastMonthIncidents->countAllResults();

        // Calculate trend direction
        if ($thisMonthIncidents < $lastMonthIncidents) {
            $trendDirection = 'improving';
        } elseif ($thisMonthIncidents > $lastMonthIncidents) {
            $trendDirection = 'declining';
        } else {
            $trendDirection = 'stable';
        }

        // Get safety audits
        $auditsQuery = $this->auditModel->where('company_id', $companyId);
        if ($projectId) {
            $auditsQuery = $auditsQuery->where('project_id', $projectId);
        }
        $safetyAudits = $auditsQuery->countAllResults();
        
        $auditCompliance = $auditsQuery->selectSum('conformance_percentage')
                                       ->get()
                                       ->getRowArray();
        $auditCompliancePercent = $safetyAudits > 0 ? round(($auditCompliance['conformance_percentage'] ?? 0) / $safetyAudits) : 0;

        // Get injured people
        $injuredQuery = $this->incidentModel->selectSum('affected_people_count')
                                             ->where('company_id', $companyId);
        if ($projectId) {
            $injuredQuery = $injuredQuery->where('project_id', $projectId);
        }
        $injuredPeople = $injuredQuery->get()->getRowArray();

        // Calculate average resolution days (using investigation_completed_date - incident_date)
        $resolutionsQuery = $this->incidentModel->select('DATEDIFF(investigation_completed_date, incident_date) as resolution_days')
                                                ->where('company_id', $companyId)
                                                ->where('investigation_completed_date IS NOT NULL', null, false);
        if ($projectId) {
            $resolutionsQuery = $resolutionsQuery->where('project_id', $projectId);
        }
        $resolutions = $resolutionsQuery->get()->getResultArray();
        
        $avgResolutionDays = 0;
        if (!empty($resolutions)) {
            $totalDays = 0;
            foreach ($resolutions as $resolution) {
                $totalDays += (int)($resolution['resolution_days'] ?? 0);
            }
            $avgResolutionDays = round($totalDays / count($resolutions));
        }

        // Get incidents by severity for chart
        $severityQuery = $this->incidentModel->select('severity_id, COUNT(*) as count')
                                             ->where('company_id', $companyId);
        if ($projectId) {
            $severityQuery = $severityQuery->where('project_id', $projectId);
        }
        $severityData = $severityQuery->groupBy('severity_id')->get()->getResultArray();

        // Map severity levels (1=Minor, 2=Moderate, 3=Serious, 4=Critical)
        $highIncidents = 0;
        $mediumIncidents = 0;
        $lowIncidents = 0;
        
        foreach ($severityData as $item) {
            if ($item['severity_id'] == 4) {
                // Already counted as critical_incidents
            } elseif ($item['severity_id'] == 3) {
                $highIncidents = $item['count'];
            } elseif ($item['severity_id'] == 2) {
                $mediumIncidents = $item['count'];
            } elseif ($item['severity_id'] == 1) {
                $lowIncidents = $item['count'];
            }
        }

        // Build analytics array
        $analytics = [
            'total_incidents' => $totalIncidents,
            'critical_incidents' => $criticalIncidents,
            'high_incidents' => $highIncidents,
            'medium_incidents' => $mediumIncidents,
            'low_incidents' => $lowIncidents,
            'open_incidents' => $openIncidents,
            'incidents_this_month' => $thisMonthIncidents,
            'incidents_previous_month' => $lastMonthIncidents,
            'trend_direction' => $trendDirection,
            'safety_audits_conducted' => $safetyAudits,
            'audit_compliance_percentage' => $auditCompliancePercent,
            'total_injured_people' => $injuredPeople['affected_people_count'] ?? 0,
            'average_resolution_days' => $avgResolutionDays,
        ];

        $data = [
            'analytics' => $analytics,
            'projects' => $projects,
            'title' => 'Safety Analytics'
        ];

        return view('incidentsafety/analytics', $data);
    }

    public function dashboard()
    {
        $companyId = session('company_id');

        $recentIncidents = $this->incidentModel->getRecentIncidents($companyId, 5);
        $openIncidents = $this->incidentModel->getOpenIncidents($companyId);
        $criticalIncidents = $this->incidentModel->getCriticalIncidents($companyId, 30);
        $recentAudits = $this->auditModel->getRecentAudits($companyId, 5);

        $data = [
            'recentIncidents' => $recentIncidents,
            'openIncidents' => $openIncidents,
            'criticalIncidents' => $criticalIncidents,
            'recentAudits' => $recentAudits,
            'title' => 'Incident & Safety Dashboard'
        ];

        return view('incidentsafety/dashboard', $data);
    }
}
