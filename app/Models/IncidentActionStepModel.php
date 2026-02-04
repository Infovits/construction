<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentActionStepModel extends Model
{
    protected $table = 'incident_action_steps';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'incident_id', 'action_number', 'action_description',
        'responsible_person_id', 'assigned_to', 'due_date',
        'completed_date', 'completion_status', 'completion_notes', 'is_critical'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'incident_id' => 'required|integer',
        'action_description' => 'required|string',
    ];

    public function getIncidentActions($incidentId)
    {
        return $this->where('incident_id', $incidentId)
                    ->orderBy('action_number', 'ASC')
                    ->findAll();
    }

    public function getPendingActions($incidentId)
    {
        return $this->where('incident_id', $incidentId)
                    ->whereIn('completion_status', ['pending', 'in_progress', 'overdue'])
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    public function getOverdueActions($incidentId)
    {
        $today = date('Y-m-d');
        
        return $this->where('incident_id', $incidentId)
                    ->whereIn('completion_status', ['pending', 'in_progress'])
                    ->where('due_date <', $today)
                    ->findAll();
    }

    public function markAsCompleted($actionId, $completionNotes = null)
    {
        return $this->update($actionId, [
            'completion_status' => 'completed',
            'completed_date' => date('Y-m-d'),
            'completion_notes' => $completionNotes
        ]);
    }

    public function getNextActionNumber($incidentId)
    {
        $latest = $this->where('incident_id', $incidentId)
                       ->orderBy('action_number', 'DESC')
                       ->first();
        
        return ($latest) ? $latest['action_number'] + 1 : 1;
    }

    public function getCriticalActions($incidentId)
    {
        return $this->where('incident_id', $incidentId)
                    ->where('is_critical', 1)
                    ->whereIn('completion_status', ['pending', 'in_progress'])
                    ->findAll();
    }
}
