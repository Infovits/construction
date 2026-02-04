<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\FileCategoryModel;
use App\Models\FileVersionModel;
use App\Models\FileAccessControlModel;
use App\Models\FileTagModel;
use App\Models\FileCommentModel;
use App\Models\FileChangeLogModel;
use App\Models\ProjectModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class FileManagement extends BaseController
{
    protected $fileModel;
    protected $categoryModel;
    protected $versionModel;
    protected $accessModel;
    protected $tagModel;
    protected $commentModel;
    protected $changeLogModel;
    protected $projectModel;

    public function __construct()
    {
        $this->fileModel = new FileModel();
        $this->categoryModel = new FileCategoryModel();
        $this->versionModel = new FileVersionModel();
        $this->accessModel = new FileAccessControlModel();
        $this->tagModel = new FileTagModel();
        $this->commentModel = new FileCommentModel();
        $this->changeLogModel = new FileChangeLogModel();
        $this->projectModel = new ProjectModel();
    }

    public function index()
    {
        $companyId = session('company_id');
        $projectId = $this->request->getGet('project_id');

        // If no project selected, show all files and project selector
        if (!$projectId) {
            $projects = $this->projectModel->where('company_id', $companyId)
                                           ->where('is_archived', 0)
                                           ->findAll();
            $files = $this->fileModel->where('company_id', $companyId)
                                     ->orderBy('created_at', 'DESC')
                                     ->limit(50)
                                     ->findAll();
            $categories = $this->categoryModel->getCategoriesByCompany($companyId);
            $expiringFiles = $this->fileModel->getExpiringFiles($companyId, 30);

            $data = [
                'projects' => $projects,
                'project' => null,
                'files' => $files,
                'categories' => $categories,
                'expiringFiles' => $expiringFiles,
                'title' => 'File Management - All Projects'
            ];

            return view('filemanagement/index', $data);
        }

        $project = $this->projectModel->getProjectById($projectId, $companyId);
        if (!$project) {
            throw new PageNotFoundException('Project not found');
        }

        $projects = $this->projectModel->where('company_id', $companyId)
                                       ->where('is_archived', 0)
                                       ->findAll();
        $files = $this->fileModel->getFilesByProject($projectId, $companyId);
        $categories = $this->categoryModel->getCategoriesByCompany($companyId);
        $expiringFiles = $this->fileModel->getExpiringFiles($companyId, 30);

        $data = [
            'projects' => $projects,
            'project' => $project,
            'files' => $files,
            'categories' => $categories,
            'expiringFiles' => $expiringFiles,
            'title' => 'File Management - ' . $project['name']
        ];

        return view('filemanagement/index', $data);
    }

    public function upload()
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $projectId = $this->request->getPost('project_id');
        $categoryId = $this->request->getPost('category_id');
        $description = $this->request->getPost('description');
        $tags = $this->request->getPost('tags');
        $documentDate = $this->request->getPost('document_date');
        $expiresAt = $this->request->getPost('expires_at');

        // Verify project exists
        $project = $this->projectModel->getProjectById($projectId, $companyId);
        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Project not found'])->setStatusCode(404);
        }

        // Handle file upload
        $files = $this->request->getFileMultiple('files');
        
        if (empty($files)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No files selected',
                'csrfHash' => csrf_hash()
            ]);
        }

        $uploadedFiles = [];

        foreach ($files as $file) {
            if (!$file->isValid() || $file->hasMoved()) {
                continue;
            }

            // Get file information BEFORE moving
            $originalName = $file->getClientName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/files/' . $companyId . '/' . $projectId . '/';

            // Create directory if not exists
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            if ($file->move($uploadPath, $fileName)) {
                $fileData = [
                    'company_id' => $companyId,
                    'project_id' => $projectId,
                    'category_id' => $categoryId ?: null,
                    'file_name' => $fileName,
                    'original_file_name' => $originalName,
                    'file_path' => 'writable/uploads/files/' . $companyId . '/' . $projectId . '/' . $fileName,
                    'file_type' => $this->getFileExtension($originalName),
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'description' => $description,
                    'uploaded_by' => session('user_id'),
                    'document_date' => $documentDate ?: null,
                    'expires_at' => $expiresAt ?: null,
                    'storage_location' => 'local',
                    'version_number' => 1,
                    'is_latest_version' => 1,
                ];

                if ($this->fileModel->insert($fileData)) {
                    $fileId = $this->fileModel->getInsertID();
                    
                    // Add tags
                    if ($tags) {
                        $this->tagModel->addTags($fileId, $tags);
                    }

                    // Log the upload action
                    $this->changeLogModel->logAction($fileId, 'uploaded', session('user_id'), 
                        'File uploaded: ' . $originalName);

                    $uploadedFiles[] = [
                        'id' => $fileId,
                        'name' => $originalName,
                        'size' => $fileSize
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'success' => count($uploadedFiles) > 0,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles,
            'csrfHash' => csrf_hash()
        ]);
    }

    public function view($fileId)
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            throw new PageNotFoundException('File not found');
        }

        // Check access
        if (!$this->checkFileAccess($fileId, $userId, 'view')) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Access denied']);
        }

        $versions = $this->versionModel->getVersionsByFile($fileId);
        $comments = $this->commentModel->getFileComments($fileId);
        $tags = $this->tagModel->getTagsByFile($fileId);
        $changeLogs = $this->changeLogModel->getFileChangeLogs($fileId);

        $data = [
            'file' => $file,
            'versions' => $versions,
            'comments' => $comments,
            'tags' => $tags,
            'changeLogs' => $changeLogs,
            'title' => 'View File - ' . $file['original_file_name']
        ];

        return view('filemanagement/view', $data);
    }

    public function download($fileId)
    {
        $companyId = session('company_id');
        $userId = session('user_id');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            throw new PageNotFoundException('File not found');
        }

        // Check access
        if (!$this->checkFileAccess($fileId, $userId, 'view')) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Access denied']);
        }

        if (!file_exists($file['file_path'])) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'File not found on disk']);
        }

        return $this->response->download($file['file_path'], $file['original_file_name']);
    }

    public function delete($fileId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $userId = session('user_id');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found'])->setStatusCode(404);
        }

        // Check access
        if (!$this->checkFileAccess($fileId, $userId, 'delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        if ($this->fileModel->update($fileId, ['is_archived' => 1])) {
            $this->changeLogModel->logAction($fileId, 'deleted', $userId, 'File archived');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'File archived successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to archive file',
            'csrfHash' => csrf_hash()
        ]);
    }

    public function updateVersion($fileId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $userId = session('user_id');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found'])->setStatusCode(404);
        }

        // Check access
        if (!$this->checkFileAccess($fileId, $userId, 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $newFile = $this->request->getFile('file');
        if (!$newFile->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid file',
                'csrfHash' => csrf_hash()
            ]);
        }

        // Get next version number
        $nextVersion = $this->versionModel->getNextVersionNumber($fileId);

        // Save the old file to versions
        $oldFileData = [
            'file_id' => $fileId,
            'version_number' => $file['version_number'],
            'file_path' => $file['file_path'],
            'file_size' => $file['file_size'],
            'uploaded_by' => $file['uploaded_by'],
            'change_description' => $this->request->getPost('change_description')
        ];

        $this->versionModel->insert($oldFileData);

        // Save new file
        $fileName = $newFile->getRandomName();
        $uploadPath = dirname($file['file_path']) . '/';

        if ($newFile->move($uploadPath, $fileName)) {
            // Delete old file
            if (file_exists($file['file_path'])) {
                @unlink($file['file_path']);
            }

            // Update file record
            $updateData = [
                'file_path' => $uploadPath . $fileName,
                'file_size' => $newFile->getSize(),
                'version_number' => $nextVersion,
                'is_latest_version' => 1
            ];

            if ($this->fileModel->update($fileId, $updateData)) {
                $this->changeLogModel->logAction($fileId, 'updated', $userId, 
                    'File updated to version ' . $nextVersion, 
                    ['version' => $file['version_number']], 
                    ['version' => $nextVersion]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File updated successfully',
                    'version' => $newVersion,
                    'csrfHash' => csrf_hash()
                ]);
            }
        }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update file',
                'csrfHash' => csrf_hash()
            ]);
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update file']);
    }

    public function comment($fileId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $userId = session('user_id');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found'])->setStatusCode(404);
        }

        // Check access
        if (!$this->checkFileAccess($fileId, $userId, 'view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $commentText = $this->request->getPost('comment_text');
        if (empty($commentText)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comment cannot be empty']);
        }

        $commentData = [
            'file_id' => $fileId,
            'user_id' => $userId,
            'comment_text' => $commentText
        ];

        if ($this->commentModel->insert($commentData)) {
            $this->changeLogModel->logAction($fileId, 'commented', $userId, 'Comment added to file');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Comment added successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add comment',
            'csrfHash' => csrf_hash()
        ]);
    }

    public function search()
    {
        $companyId = session('company_id');
        $projectId = $this->request->getGet('project_id');
        $searchTerm = $this->request->getGet('q');

        if (!$projectId || !$searchTerm) {
            return $this->response->setJSON(['results' => []]);
        }

        $results = $this->fileModel->searchFiles($companyId, $projectId, $searchTerm);

        return $this->response->setJSON(['results' => $results]);
    }

    public function byCategory($categoryId)
    {
        $companyId = session('company_id');
        $projectId = $this->request->getGet('project_id');

        if (!$projectId) {
            return $this->response->setJSON(['error' => 'Project ID required'])->setStatusCode(400);
        }

        $files = $this->fileModel->getFilesByCategory($categoryId, $projectId, $companyId);
        $category = $this->categoryModel->find($categoryId);

        $data = [
            'files' => $files,
            'category' => $category,
            'title' => 'Files in Category - ' . ($category['name'] ?? 'Unknown')
        ];

        return view('filemanagement/by_category', $data);
    }

    public function grantAccess($fileId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $userId = session('user_id');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found'])->setStatusCode(404);
        }

        // Check if user is file owner or has manage permission
        if (!$this->checkFileAccess($fileId, $userId, 'manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $targetUserId = $this->request->getPost('user_id');
        $accessType = $this->request->getPost('access_type');
        $expiresAt = $this->request->getPost('expires_at');

        $accessData = [
            'file_id' => $fileId,
            'user_id' => $targetUserId,
            'access_type' => $accessType,
            'granted_by' => $userId,
            'expires_at' => $expiresAt ?: null
        ];

        if ($this->accessModel->insert($accessData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Access granted successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to grant access',
            'csrfHash' => csrf_hash()
        ]);
    }

    protected function checkFileAccess($fileId, $userId, $accessType)
    {
        // File owner has full access
        $file = $this->fileModel->find($fileId);
        if ($file && $file['uploaded_by'] == $userId) {
            return true;
        }

        // Check access control
        return $this->accessModel->checkAccess($fileId, $userId, $accessType) !== null;
    }

    protected function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    // =============== CATEGORY MANAGEMENT ===============

    public function categories()
    {
        $companyId = session('company_id');
        $categories = $this->categoryModel->getAllCategories($companyId);

        $data = [
            'categories' => $categories,
            'title' => 'File Categories'
        ];

        return view('filemanagement/categories', $data);
    }

    public function storeCategory()
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');

        $categoryData = [
            'company_id' => $companyId,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'color_code' => $this->request->getPost('color_code') ?: '#6366f1',
            'is_active' => 1
        ];

        if ($this->categoryModel->insert($categoryData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category created successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create category',
            'errors' => $this->categoryModel->errors(),
            'csrfHash' => csrf_hash()
        ]);
    }

    public function updateCategory($categoryId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $category = $this->categoryModel->where('id', $categoryId)
                                        ->where('company_id', $companyId)
                                        ->first();

        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category not found',
                'csrfHash' => csrf_hash()
            ])->setStatusCode(404);
        }

        $updateData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'color_code' => $this->request->getPost('color_code'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->categoryModel->update($categoryId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category updated successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update category',
            'errors' => $this->categoryModel->errors(),
            'csrfHash' => csrf_hash()
        ]);
    }

    public function deleteCategory($categoryId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $companyId = session('company_id');
        $category = $this->categoryModel->where('id', $categoryId)
                                        ->where('company_id', $companyId)
                                        ->first();

        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category not found',
                'csrfHash' => csrf_hash()
            ])->setStatusCode(404);
        }

        if ($this->categoryModel->delete($categoryId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category deleted successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete category',
            'csrfHash' => csrf_hash()
        ]);
    }
}
