<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'registration_number', 'tax_number', 'email', 'phone',
        'address', 'city', 'state', 'country', 'postal_code', 'website',
        'logo_url', 'industry_type', 'status', 'subscription_plan',
        'subscription_expires_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'email' => 'permit_empty|valid_email',
        'status' => 'in_list[active,inactive,suspended]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Company name is required',
            'min_length' => 'Company name must be at least 3 characters long'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get company information for display in reports and documents
     * 
     * @return array Company information
     */
    public function getCompanyInfo()
    {
        // Get the first active company (assuming single company setup)
        $company = $this->where('status', 'active')->first();
        
        if ($company) {
            return [
                'name' => $company['name'],
                'address' => $company['address'] . ', ' . $company['city'] . ', ' . $company['country'],
                'logo' => $company['logo_url'] ?? null,
                'email' => $company['email'],
                'phone' => $company['phone'],
                'registration_number' => $company['registration_number'],
                'tax_number' => $company['tax_number']
            ];
        }
        
        // Return default values if no company found
        return [
            'name' => 'Construction Management System',
            'address' => 'Default Address',
            'logo' => null,
            'email' => null,
            'phone' => null,
            'registration_number' => null,
            'tax_number' => null
        ];
    }
}
