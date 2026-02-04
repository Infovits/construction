<?php

namespace App\Models;

use CodeIgniter\Model;

class FileTagModel extends Model
{
    protected $table = 'file_tags';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'file_id', 'tag_name', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'file_id' => 'required|integer',
        'tag_name' => 'required|string|max_length[100]',
    ];

    public function getTagsByFile($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->select('tag_name')
                    ->findAll();
    }

    public function getAllTags($projectId)
    {
        return $this->where('file_id in', 
                    "SELECT id FROM files WHERE project_id = {$projectId}")
                    ->select('distinct tag_name')
                    ->findAll();
    }

    public function addTags($fileId, $tags)
    {
        if (empty($tags)) {
            return true;
        }

        $tagArray = is_array($tags) ? $tags : array_map('trim', explode(',', $tags));
        $data = [];

        foreach ($tagArray as $tag) {
            if (!empty($tag)) {
                $data[] = [
                    'file_id' => $fileId,
                    'tag_name' => trim($tag),
                ];
            }
        }

        if (!empty($data)) {
            return $this->insertBatch($data);
        }

        return true;
    }

    public function removeTags($fileId)
    {
        return $this->delete(['file_id' => $fileId]);
    }

    public function updateTags($fileId, $tags)
    {
        $this->removeTags($fileId);
        return $this->addTags($fileId, $tags);
    }
}
