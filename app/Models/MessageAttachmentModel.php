<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageAttachmentModel extends Model
{
    protected $table = 'message_attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['message_id', 'file_name', 'file_path', 'file_size', 'mime_type', 'created_at'];
    protected $useTimestamps = false;
}
