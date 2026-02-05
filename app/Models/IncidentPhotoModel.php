<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentPhotoModel extends Model
{
    protected $table = 'incident_photos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'incident_id', 'photo_path', 'original_file_name',
        'photo_type', 'description', 'uploaded_by', 'is_deleted'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'uploaded_at';
    protected $updatedField = '';

    protected $validationRules = [
        'incident_id' => 'required|integer',
        'photo_path' => 'required|string',
        'original_file_name' => 'required|string',
        'uploaded_by' => 'required|integer',
    ];

    public function getIncidentPhotos($incidentId)
    {
        return $this->where('incident_id', $incidentId)
                    ->where('is_deleted', 0)
                    ->orderBy('uploaded_at', 'DESC')
                    ->findAll();
    }

    public function getPhotosByType($incidentId, $photoType)
    {
        return $this->where('incident_id', $incidentId)
                    ->where('photo_type', $photoType)
                    ->where('is_deleted', 0)
                    ->findAll();
    }

    public function deletePhoto($photoId)
    {
        return $this->update($photoId, ['is_deleted' => 1]);
    }

    public function getPhotoCount($incidentId)
    {
        return $this->where('incident_id', $incidentId)
                    ->where('is_deleted', 0)
                    ->countAllResults();
    }
}
