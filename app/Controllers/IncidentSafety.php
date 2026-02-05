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
        $filterType = $this->request->getGet('incident_type_id');
        $filterSeverity = $this->request->getGet('severity_id');
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
        $users = $this->userModel->where('company_id', $companyId)->where('status', 'active')->findAll();

        $data = [
            'incident' => $incident,
            'photos' => $photos,
            'actionSteps' => $actionSteps,
            'type' => $type,
            'severity' => $severity,
            'users' => $users,
            'title' => 'Incident Details - ' . $incident['incident_code']
        ];

        return view('incidentsafety/incidents/view', $data);
    }

    public function updateIncidentStatus($incidentId)
    {
        if ($this->request->getMethod() !== 'post') {
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

    public function servePhoto($photoId)
    {
        $companyId = session('company_id');
        
        $photo = $this->photoModel->find($photoId);
        if (!$photo) {
            throw new PageNotFoundException('Photo not found');
        }

        // Verify the photo belongs to an incident in the user's company
        $incident = $this->incidentModel->find($photo['incident_id']);
        if (!$incident || $incident['company_id'] != $companyId) {
            throw new PageNotFoundException('Unauthorized');
        }

        $filePath = ROOTPATH . $photo['photo_path'];
        
        if (!file_exists($filePath)) {
            throw new PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null);
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

        $audits = $this->auditModel->select('safety_audits.*, projects.name as project_name, 
                                            CONCAT(auditor.first_name, " ", auditor.last_name) as auditor_name')
                                   ->join('projects', 'projects.id = safety_audits.project_id', 'left')
                                   ->join('users as auditor', 'auditor.id = safety_audits.auditor_id', 'left')
                                   ->where('safety_audits.company_id', $companyId);

        if ($projectId) {
            $audits = $audits->where('safety_audits.project_id', $projectId);
        }

        if ($auditType) {
            $audits = $audits->where('safety_audits.audit_type', $auditType);
        }

        if ($status) {
            $audits = $audits->where('safety_audits.status', $status);
        }

        $audits = $audits->orderBy('safety_audits.audit_date', 'DESC')->paginate(25);

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
        $users = $this->userModel->where('company_id', $companyId)->where('status', 'active')->findAll();

        $data = [
            'projects' => $projects,
            'users' => $users,
            'title' => 'Create Safety Audit'
        ];

        return view('incidentsafety/audits/create', $data);
    }

    public function storeAudit()
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        $auditCode = $this->auditModel->generateAuditCode($companyId);

        // Handle document upload
        $documentPath = null;
        $file = $this->request->getFile('document_path');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/audits/' . $companyId . '/';
            
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            
            if ($file->move($uploadPath, $fileName)) {
                $documentPath = 'writable/uploads/audits/' . $companyId . '/' . $fileName;
            }
        }

        $auditData = [
            'company_id' => $companyId,
            'project_id' => $this->request->getPost('project_id'),
            'audit_code' => $auditCode,
            'audit_date' => $this->request->getPost('audit_date'),
            'audit_type' => $this->request->getPost('audit_type'),
            'auditor_id' => $this->request->getPost('auditor_id'),
            'audit_scope' => $this->request->getPost('audit_scope'),
            'total_observations' => $this->request->getPost('total_observations') ?: 0,
            'critical_findings' => $this->request->getPost('critical_findings') ?: 0,
            'major_findings' => $this->request->getPost('major_findings') ?: 0,
            'minor_findings' => $this->request->getPost('minor_findings') ?: 0,
            'conformance_percentage' => $this->request->getPost('conformance_percentage') ?: 0,
            'findings_summary' => $this->request->getPost('findings_summary'),
            'due_date_for_corrections' => $this->request->getPost('due_date_for_corrections'),
            'follow_up_date' => $this->request->getPost('follow_up_date'),
            'document_path' => $documentPath,
            'status' => 'draft'
        ];

        if ($this->auditModel->insert($auditData)) {
            $auditId = $this->auditModel->getInsertID();
            return redirect()->to('/incident-safety/audits/' . $auditId)
                           ->with('success', 'Safety audit created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create safety audit');
    }

    public function editAudit($auditId)
    {
        $companyId = session('company_id');
        
        $audit = $this->auditModel->where('id', $auditId)->where('company_id', $companyId)->first();
        if (!$audit) {
            throw new PageNotFoundException('Audit not found');
        }

        $projects = $this->projectModel->where('company_id', $companyId)->where('status', 'active')->findAll();
        $users = $this->userModel->where('company_id', $companyId)->where('status', 'active')->findAll();

        $data = [
            'title' => 'Edit Safety Audit',
            'audit' => $audit,
            'projects' => $projects,
            'users' => $users
        ];

        return view('incidentsafety/audits/edit', $data);
    }

    public function updateAudit($auditId)
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        $audit = $this->auditModel->where('id', $auditId)->where('company_id', $companyId)->first();
        if (!$audit) {
            throw new PageNotFoundException('Audit not found');
        }

        $auditData = [
            'project_id' => $this->request->getPost('project_id'),
            'audit_date' => $this->request->getPost('audit_date'),
            'audit_type' => $this->request->getPost('audit_type'),
            'auditor_id' => $this->request->getPost('auditor_id'),
            'conformance_percentage' => $this->request->getPost('conformance_percentage'),
            'non_conformities' => $this->request->getPost('non_conformities'),
            'findings_summary' => $this->request->getPost('findings_summary'),
            'due_date_for_corrections' => $this->request->getPost('due_date_for_corrections'),
            'follow_up_date' => $this->request->getPost('follow_up_date'),
            'status' => $this->request->getPost('status')
        ];

        // Handle document upload if new file provided
        $file = $this->request->getFile('document_path');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/audits/' . $companyId . '/';
            
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            if ($file->move($uploadPath, $fileName)) {
                // Delete old file if exists
                if (!empty($audit['document_path'])) {
                    $oldFile = ROOTPATH . $audit['document_path'];
                    if (file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $auditData['document_path'] = 'writable/uploads/audits/' . $companyId . '/' . $fileName;
            }
        }

        if ($this->auditModel->update($auditId, $auditData)) {
            return redirect()->to('/incident-safety/audits/' . $auditId)
                           ->with('success', 'Safety audit updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update safety audit');
    }

    public function deleteAudit($auditId)
    {
        $companyId = session('company_id');

        $audit = $this->auditModel->where('id', $auditId)->where('company_id', $companyId)->first();
        if (!$audit) {
            return $this->response->setJSON(['success' => false, 'message' => 'Audit not found']);
        }

        // Delete associated document if exists
        if (!empty($audit['document_path'])) {
            $filePath = ROOTPATH . $audit['document_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        if ($this->auditModel->delete($auditId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Audit deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete audit']);
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

    public function serveAuditDocument($auditId)
    {
        $companyId = session('company_id');
        
        $audit = $this->auditModel->where('id', $auditId)
                                  ->where('company_id', $companyId)
                                  ->first();
        
        if (!$audit || empty($audit['document_path'])) {
            throw new PageNotFoundException('Document not found');
        }

        $filePath = ROOTPATH . $audit['document_path'];
        
        if (!file_exists($filePath)) {
            throw new PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null);
    }

    // =============== SAFETY REPORTS ===============

    public function reports()
    {
        $companyId = session('company_id');
        $reportType = $this->request->getGet('type');
        $status = $this->request->getGet('status');

        $reports = $this->reportModel->select('safety_reports.*, projects.name as project_name, 
                                              CONCAT(generator.first_name, " ", generator.last_name) as generated_by_name')
                                     ->join('projects', 'projects.id = safety_reports.project_id', 'left')
                                     ->join('users as generator', 'generator.id = safety_reports.generated_by', 'left')
                                     ->where('safety_reports.company_id', $companyId);

        if ($reportType) {
            $reports = $reports->where('safety_reports.report_type', $reportType);
        }

        if ($status) {
            $reports = $reports->where('safety_reports.status', $status);
        }

        $reports = $reports->orderBy('safety_reports.report_period_end', 'DESC')->paginate(25);

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

        // Handle document upload
        $documentPath = null;
        $file = $this->request->getFile('report_file_path');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/reports/' . $companyId . '/';
            
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            
            if ($file->move($uploadPath, $fileName)) {
                $documentPath = 'writable/uploads/reports/' . $companyId . '/' . $fileName;
            }
        }

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
            'report_file_path' => $documentPath,
            'status' => 'draft'
        ];

        if ($this->reportModel->insert($reportData)) {
            $reportId = $this->reportModel->getInsertID();
            return redirect()->to('/incident-safety/reports/' . $reportId)
                           ->with('success', 'Safety report created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create safety report');
    }

    public function editReport($reportId)
    {
        $companyId = session('company_id');
        
        $report = $this->reportModel->where('id', $reportId)->where('company_id', $companyId)->first();
        if (!$report) {
            throw new PageNotFoundException('Report not found');
        }

        $projects = $this->projectModel->where('company_id', $companyId)->where('status', 'active')->findAll();

        $data = [
            'title' => 'Edit Safety Report',
            'report' => $report,
            'projects' => $projects
        ];

        return view('incidentsafety/reports/edit', $data);
    }

    public function updateReport($reportId)
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        $report = $this->reportModel->where('id', $reportId)->where('company_id', $companyId)->first();
        if (!$report) {
            throw new PageNotFoundException('Report not found');
        }

        $reportData = [
            'project_id' => $this->request->getPost('project_id'),
            'report_type' => $this->request->getPost('report_type'),
            'report_period_start' => $this->request->getPost('report_period_start'),
            'report_period_end' => $this->request->getPost('report_period_end'),
            'total_incidents' => $this->request->getPost('total_incidents') ?: 0,
            'total_near_misses' => $this->request->getPost('total_near_misses') ?: 0,
            'total_injured_workers' => $this->request->getPost('total_injured_workers') ?: 0,
            'key_highlights' => $this->request->getPost('key_highlights'),
            'challenges_identified' => $this->request->getPost('challenges_identified'),
            'recommendations' => $this->request->getPost('recommendations'),
            'status' => $this->request->getPost('status')
        ];

        // Handle document upload if new file provided
        $file = $this->request->getFile('report_file_path');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/reports/' . $companyId . '/';
            
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            if ($file->move($uploadPath, $fileName)) {
                // Delete old file if exists
                if (!empty($report['report_file_path'])) {
                    $oldFile = ROOTPATH . $report['report_file_path'];
                    if (file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $reportData['report_file_path'] = 'writable/uploads/reports/' . $companyId . '/' . $fileName;
            }
        }

        if ($this->reportModel->update($reportId, $reportData)) {
            return redirect()->to('/incident-safety/reports/' . $reportId)
                           ->with('success', 'Safety report updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update safety report');
    }

    public function deleteReport($reportId)
    {
        $companyId = session('company_id');

        $report = $this->reportModel->where('id', $reportId)->where('company_id', $companyId)->first();
        if (!$report) {
            return $this->response->setJSON(['success' => false, 'message' => 'Report not found']);
        }

        // Delete associated document if exists
        if (!empty($report['report_file_path'])) {
            $filePath = ROOTPATH . $report['report_file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        if ($this->reportModel->delete($reportId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Report deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete report']);
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

    public function serveReportDocument($reportId)
    {
        $companyId = session('company_id');
        
        $report = $this->reportModel->where('id', $reportId)
                                    ->where('company_id', $companyId)
                                    ->first();
        
        if (!$report || empty($report['report_file_path'])) {
            throw new PageNotFoundException('Document not found');
        }

        $filePath = ROOTPATH . $report['report_file_path'];
        
        if (!file_exists($filePath)) {
            throw new PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null);
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
