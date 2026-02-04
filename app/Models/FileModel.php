<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'project_id', 'category_id', 'file_name',
        'original_file_name', 'file_path', 'file_type', 'file_size',
        'mime_type', 'description', 'uploaded_by', 'version_number',
        'is_latest_version', 'is_archived', 'is_public', 'document_date',
        'expires_at', 'storage_location'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|integer',
        'file_name' => 'required|string',
        'original_file_name' => 'required|string',
        'file_path' => 'required|string',
        'file_type' => 'required|string',
        'uploaded_by' => 'required|integer',
    ];

    protected $validationMessages = [];

    public function getFilesByProject($projectId, $companyId, $limit = 25, $offset = 0)
    {
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->where('is_archived', 0)
                    ->where('is_latest_version', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    public function getFilesByCategory($categoryId, $projectId, $companyId)
    {
        return $this->where('category_id', $categoryId)
                    ->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->where('is_archived', 0)
                    ->where('is_latest_version', 1)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function searchFiles($companyId, $projectId, $searchTerm)
    {
        return $this->where('company_id', $companyId)
                    ->where('project_id', $projectId)
                    ->where('is_archived', 0)
                    ->where('is_latest_version', 1)
                    ->like('file_name', $searchTerm)
                    ->orLike('description', $searchTerm)
                    ->orLike('original_file_name', $searchTerm)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getFileVersions($fileId)
    {
        $fileVersionModel = new FileVersionModel();
        return $fileVersionModel->where('file_id', $fileId)
                                ->orderBy('version_number', 'DESC')
                                ->findAll();
    }

    public function getFileById($fileId, $companyId)
    {
        return $this->where('id', $fileId)
                    ->where('company_id', $companyId)
                    ->first();
    }

    public function getFileWithComments($fileId, $companyId)
    {
        $file = $this->getFileById($fileId, $companyId);
        if ($file) {
            $commentModel = new FileCommentModel();
            $file['comments'] = $commentModel->getFileComments($fileId);
        }
        return $file;
    }

    public function getRecentFiles($companyId, $limit = 10)
    {
        return $this->where('company_id', $companyId)
                    ->where('is_archived', 0)
                    ->where('is_latest_version', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getFilesByTag($tagName, $projectId, $companyId)
    {
        $tagModel = new FileTagModel();
        $fileIds = $tagModel->where('tag_name', $tagName)
                            ->select('file_id')
                            ->findAll();

        $ids = array_column($fileIds, 'file_id');
        if (empty($ids)) {
            return [];
        }

        return $this->whereIn('id', $ids)
                    ->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->where('is_archived', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getExpiringFiles($companyId, $daysUntilExpiry = 30)
    {
        $futureDate = date('Y-m-d', strtotime("+{$daysUntilExpiry} days"));
        return $this->where('company_id', $companyId)
                    ->where('expires_at <=', $futureDate)
                    ->where('expires_at !=', null)
                    ->where('is_archived', 0)
                    ->orderBy('expires_at', 'ASC')
                    ->findAll();
    }

    public function getArchivedFiles($projectId, $companyId)
    {
        return $this->where('project_id', $projectId)
                    ->where('company_id', $companyId)
                    ->where('is_archived', 1)
                    ->orderBy('updated_at', 'DESC')
                    ->findAll();
    }
}
