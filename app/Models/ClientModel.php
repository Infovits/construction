<?php

// ============================================================================
// CLIENT MODEL
// ============================================================================

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'client_code', 'name', 'contact_person', 'email', 'phone',
        'mobile', 'address', 'city', 'state', 'country', 'postal_code',
        'tax_number', 'payment_terms', 'credit_limit', 'client_type', 'status', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|numeric',
        'name' => 'required|min_length[3]|max_length[255]',
        'email' => 'permit_empty|valid_email',
        'client_type' => 'in_list[individual,company,government]'
    ];

    public function getActiveClients($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function generateClientCode($prefix = 'CLT')
    {
        $year = date('Y');
        $lastClient = $this->where('company_id', session('company_id'))
            ->where('client_code LIKE', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastClient) {
            $lastNumber = (int) substr($lastClient['client_code'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
