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
use App\Models\UserModel;
use App\Models\RoleModel;
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
    protected $userModel;
    protected $roleModel;

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
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
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
                                     ->where('is_archived', 0)
                                     ->where('is_latest_version', 1)
                                     ->orderBy('created_at', 'DESC')
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

    public function archived()
    {
        $companyId = session('company_id');

        $projects = $this->projectModel->where('company_id', $companyId)
                                       ->where('is_archived', 0)
                                       ->findAll();
        
        $archivedFiles = $this->fileModel->where('company_id', $companyId)
                                         ->where('is_archived', 1)
                                         ->orderBy('created_at', 'DESC')
                                         ->findAll();
        
        $categories = $this->categoryModel->getCategoriesByCompany($companyId);

        $data = [
            'projects' => $projects,
            'files' => $archivedFiles,
            'categories' => $categories,
            'title' => 'Archived Files'
        ];

        return view('filemanagement/archived', $data);
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
        if (!$projectId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Project is required',
                'csrfHash' => csrf_hash()
            ]);
        }

        $project = $this->projectModel->getProjectById($projectId, $companyId);
        if (!$project) {
            return $this->response->setJSON(['success' => false, 'message' => 'Project not found', 'csrfHash' => csrf_hash()])->setStatusCode(404);
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
        $errors = [];

        foreach ($files as $file) {
            // Check if file is valid and hasn't been moved
            if (!$file->isValid()) {
                $errorMsg = 'File validation failed: ' . $file->getErrorString();
                $errors[] = $errorMsg;
                log_message('error', 'Upload validation error: ' . $errorMsg);
                continue;
            }
            
            if ($file->hasMoved()) {
                $errors[] = 'File has already been moved';
                continue;
            }

            // Get file information BEFORE moving
            $originalName = $file->getClientName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            // Use original filename, make it unique if already exists
            $fileName = $originalName;
            $uploadPath = WRITEPATH . 'uploads/files/' . $companyId . '/' . $projectId . '/';
            
            // If file already exists, append timestamp to make it unique
            if (file_exists($uploadPath . $fileName)) {
                $pathInfo = pathinfo($fileName);
                $fileName = $pathInfo['filename'] . '_' . time() . '.' . $pathInfo['extension'];
            }
            
            log_message('debug', "Processing upload: {$originalName}, Size: {$fileSize}, MIME: {$mimeType}");

            // Create directory if not exists
            if (!is_dir($uploadPath)) {
                if (!@mkdir($uploadPath, 0777, true)) {
                    $errors[] = 'Failed to create upload directory: ' . $uploadPath;
                    continue;
                }
            }

            // Attempt to move the file
            try {
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
                    } else {
                        $errors[] = 'Failed to save file info for: ' . $originalName;
                    }
                } else {
                    $errors[] = 'Failed to move file: ' . $originalName . ' to ' . $uploadPath;
                }
            } catch (\Exception $e) {
                $errors[] = 'Exception moving file ' . $originalName . ': ' . $e->getMessage();
            }
        }

        if (count($uploadedFiles) === 0) {
            $errorMessage = 'No files were uploaded successfully.';
            if (!empty($errors)) {
                $errorMessage .= ' Details: ' . implode('; ', array_slice($errors, 0, 3));
            } else {
                $errorMessage .= ' Please check file permissions.';
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage,
                'csrfHash' => csrf_hash()
            ]);
        }

        $message = count($uploadedFiles) . ' file(s) uploaded successfully';
        if (!empty($errors)) {
            $message .= ' (' . count($errors) . ' file(s) failed)';
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
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
        $accessList = $this->accessModel->getUsersWithAccess($fileId);
        $users = $this->userModel->getUsersWithRoles($companyId);
        $roles = $this->roleModel->getActiveRoles($companyId);

        $canManage = $this->checkFileAccess($fileId, $userId, 'manage');
        $canEdit = $this->checkFileAccess($fileId, $userId, 'edit');
        $canDelete = $this->checkFileAccess($fileId, $userId, 'delete');

        $data = [
            'file' => $file,
            'versions' => $versions,
            'comments' => $comments,
            'tags' => $tags,
            'changeLogs' => $changeLogs,
            'accessList' => $accessList,
            'users' => $users,
            'roles' => $roles,
            'canManage' => $canManage,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
            'title' => 'View File - ' . $file['original_file_name']
        ];

        return view('filemanagement/view', $data);
    }

    public function download($fileId)
    {
        $companyId = session('company_id');
        $userId = session('user_id');
        $versionNumber = $this->request->getGet('version');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            throw new PageNotFoundException('File not found');
        }

        // Check access
        if (!$this->checkFileAccess($fileId, $userId, 'view')) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Access denied']);
        }

        $filePath = $file['file_path'];
        $downloadName = $file['original_file_name'];

        if ($versionNumber) {
            $version = $this->versionModel->getSpecificVersion($fileId, $versionNumber);
            if ($version) {
                $versionFullPath = $this->normalizePath(ROOTPATH . $version['file_path']);
                if (file_exists($versionFullPath)) {
                    $filePath = $version['file_path'];
                    $downloadName = 'v' . $versionNumber . '_' . $file['original_file_name'];
                }
            }
        }

        // Convert relative path to absolute path using ROOTPATH
        $fullPath = $this->normalizePath(ROOTPATH . ltrim($filePath, '/\\'));
        if (!file_exists($fullPath)) {
            log_message('error', 'Download file not found: ' . $fullPath . ' (stored path: ' . $filePath . ')');
            throw new PageNotFoundException('File not found on disk. The file may have been deleted or moved.');
        }

        return $this->response->download($fullPath, null, $downloadName);
    }

    public function preview($fileId)
    {
        $companyId = session('company_id');
        $userId = session('user_id');
        $versionNumber = $this->request->getGet('version');

        $file = $this->fileModel->getFileById($fileId, $companyId);
        if (!$file) {
            throw new PageNotFoundException('File not found');
        }

        if (!$this->checkFileAccess($fileId, $userId, 'view')) {
            throw new PageNotFoundException('Access denied');
        }

        $filePath = $file['file_path'];

        if ($versionNumber) {
            $version = $this->versionModel->getSpecificVersion($fileId, $versionNumber);
            if ($version) {
                $versionFullPath = $this->normalizePath(ROOTPATH . $version['file_path']);
                if (file_exists($versionFullPath)) {
                    $filePath = $version['file_path'];
                }
            }
        }

        // Convert relative path to absolute path using ROOTPATH
        $fullPath = $this->normalizePath(ROOTPATH . ltrim($filePath, '/\\'));
        if (!file_exists($fullPath)) {
            log_message('error', 'Preview file not found: ' . $fullPath . ' (stored path: ' . $filePath . ')');
            throw new PageNotFoundException('File not found on disk. The file may have been deleted or moved.');
        }

        return $this->response
            ->setHeader('Content-Type', $file['mime_type'])
            ->setHeader('Content-Disposition', 'inline; filename="' . $file['original_file_name'] . '"')
            ->setBody(file_get_contents($fullPath));
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

        // If already archived, permanently delete
        if ($file['is_archived'] == 1) {
            // Delete physical file
            $filePath = ROOTPATH . 'writable/uploads/files/' . $file['company_id'] . '/' . $file['project_id'] . '/' . $file['file_name'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            if ($this->fileModel->delete($fileId)) {
                $this->changeLogModel->logAction($fileId, 'deleted', $userId, 'File permanently deleted');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File permanently deleted',
                    'csrfHash' => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete file',
                'csrfHash' => csrf_hash()
            ]);
        }

        // Otherwise, archive it
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

    public function restore($fileId)
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

        // Restore the file (set is_archived to 0)
        if ($this->fileModel->update($fileId, ['is_archived' => 0])) {
            $this->changeLogModel->logAction($fileId, 'restored', $userId, 'File restored from archive');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'File restored successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to restore file',
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

        // Get file information BEFORE moving
        $originalName = $newFile->getClientName();
        $fileSize = $newFile->getSize();
        $mimeType = $newFile->getMimeType();
        
        // Use original filename, make it unique if already exists
        $fileName = $originalName;
        $uploadPath = WRITEPATH . 'uploads/files/' . $companyId . '/' . $file['project_id'] . '/';
        
        // If file already exists, append timestamp to make it unique
        if (file_exists($uploadPath . $fileName)) {
            $pathInfo = pathinfo($fileName);
            $fileName = $pathInfo['filename'] . '_' . time() . '.' . $pathInfo['extension'];
        }

        if ($newFile->move($uploadPath, $fileName)) {
            // Delete old physical file
            $oldFullPath = $this->normalizePath(ROOTPATH . ltrim($file['file_path'], '/\\'));
            if (file_exists($oldFullPath)) {
                @unlink($oldFullPath);
            }

            // Update file record
            $updateData = [
                'file_name' => $fileName,
                'original_file_name' => $originalName,
                'file_path' => 'writable/uploads/files/' . $companyId . '/' . $file['project_id'] . '/' . $fileName,
                'file_size' => $fileSize,
                'file_type' => $this->getFileExtension($originalName),
                'mime_type' => $mimeType,
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
                    'version' => $nextVersion,
                    'csrfHash' => csrf_hash()
                ]);
            }
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update file',
            'csrfHash' => csrf_hash()
        ]);
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
        $targetRoleId = $this->request->getPost('role_id');
        $accessType = $this->request->getPost('access_type');
        $expiresAt = $this->request->getPost('expires_at');

        if (empty($targetUserId) && empty($targetRoleId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Select a user or role',
                'csrfHash' => csrf_hash()
            ])->setStatusCode(400);
        }

        if (!empty($targetUserId) && !empty($targetRoleId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Select either user or role, not both',
                'csrfHash' => csrf_hash()
            ])->setStatusCode(400);
        }

        $accessData = [
            'file_id' => $fileId,
            'user_id' => $targetUserId ?: null,
            'role_id' => $targetRoleId ?: null,
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

    public function revokeAccess($accessId)
    {
        if ($this->request->getMethod() !== 'post') {
            throw new PageNotFoundException('Method not allowed');
        }

        $userId = session('user_id');
        $access = $this->accessModel->find($accessId);

        if (!$access) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access record not found'])->setStatusCode(404);
        }

        if (!$this->checkFileAccess($access['file_id'], $userId, 'manage')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        if ($this->accessModel->revokeAccess($accessId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Access revoked successfully',
                'csrfHash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to revoke access',
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

        // Check user access
        if ($this->accessModel->checkAccess($fileId, $userId, $accessType) !== null) {
            return true;
        }

        // Check role access
        $userRole = $this->userModel->getUserRole($userId);
        $roleId = $userRole['role_id'] ?? null;

        if ($roleId && $this->accessModel->checkAccessByRole($fileId, $roleId, $accessType) !== null) {
            return true;
        }

        return false;
    }

    protected function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Normalize file paths to work across Windows and Unix systems
     * Converts forward slashes to the appropriate directory separator
     */
    protected function normalizePath($path)
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
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
