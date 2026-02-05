<?php

namespace App\Models;

use CodeIgniter\Model;

class FileVersionModel extends Model
{
    protected $table = 'file_versions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'file_id', 'version_number', 'file_path', 'file_size',
        'uploaded_by', 'change_description', 'change_log'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'file_id' => 'required|integer',
        'version_number' => 'required|integer',
        'file_path' => 'required|string',
        'uploaded_by' => 'required|integer',
    ];

    public function getVersionsByFile($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->orderBy('version_number', 'DESC')
                    ->findAll();
    }

    public function getSpecificVersion($fileId, $versionNumber)
    {
        return $this->where('file_id', $fileId)
                    ->where('version_number', $versionNumber)
                    ->first();
    }

    public function getLatestVersion($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->orderBy('version_number', 'DESC')
                    ->first();
    }

    public function getNextVersionNumber($fileId)
    {
        $latest = $this->getLatestVersion($fileId);
        return ($latest) ? $latest['version_number'] + 1 : 1;
    }
}
