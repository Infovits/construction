<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\MaterialCategoryModel;

class Suppliers extends BaseController
{
    protected $supplierModel;
    
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }
    
    public function index()
    {
        $companyId = session()->get('company_id');
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $type = $this->request->getGet('type');
        
        $filters = [
            'search' => $search,
            'status' => $status,
            'type' => $type
        ];
        
        $data = [
            'title' => 'Suppliers',
            'suppliers' => $this->supplierModel->getSuppliersList($companyId, $filters),
            'search' => $search,
            'status' => $status,
            'type' => $type
        ];
        
        return view('inventory/suppliers/index', $data);
    }
    
    public function new()
    {
        $companyId = session()->get('company_id');

        // Check if user is properly authenticated and has company_id
        if (!$companyId) {
            return redirect()->to('/auth/login')->with('error', 'Please log in to continue.');
        }

        $materialCategoryModel = new MaterialCategoryModel();

        $data = [
            'title' => 'Add New Supplier',
            'categories' => $materialCategoryModel->where('company_id', $companyId)->findAll()
        ];

        return view('inventory/suppliers/create', $data);
    }
    
    public function create()
    {
        helper(['form', 'url']);

        $companyId = session()->get('company_id');

        // Check if user is properly authenticated and has company_id
        if (!$companyId) {
            return redirect()->to('/auth/login')->with('error', 'Please log in to continue.');
        }
        
        // Auto-generate supplier code if not provided
        $supplierCode = $this->request->getVar('supplier_code');
        if (empty($supplierCode)) {
            $supplierCode = $this->generateSupplierCode($companyId);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'supplier_code' => 'required|is_unique[suppliers.supplier_code,company_id,'.$companyId.']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'company_id' => $companyId,
            'name' => $this->request->getVar('name'),
            'supplier_code' => $supplierCode,
            'contact_person' => $this->request->getVar('contact_person'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'mobile' => $this->request->getVar('mobile'),
            'address' => $this->request->getVar('address'),
            'city' => $this->request->getVar('city'),
            'state' => $this->request->getVar('state'),
            'country' => $this->request->getVar('country'),
            'tax_number' => $this->request->getVar('tax_number'),
            'payment_terms' => $this->request->getVar('payment_terms'),
            'credit_limit' => $this->request->getVar('credit_limit') ?? 0.00,
            'supplier_type' => $this->request->getVar('supplier_type') ?? 'mixed',
            'notes' => $this->request->getVar('notes'),
            'status' => $this->request->getVar('status') ?? 'active',
        ];
        
        if (!$this->supplierModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to add supplier.');
        }
        
        return redirect()->to('/admin/suppliers')->with('success', 'Supplier added successfully');
    }
    
    public function view($id)
    {
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        $data = [
            'title' => 'Supplier Details',
            'supplier' => $supplier,
            'deliveries' => $this->supplierModel->getSupplierDeliveries($id),
            'materials' => $this->supplierModel->getSupplierMaterials($id),
        ];
        
        return view('inventory/suppliers/view', $data);
    }
    
    public function edit($id)
    {
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        $data = [
            'title' => 'Edit Supplier',
            'supplier' => $supplier,
        ];
        
        return view('inventory/suppliers/edit', $data);
    }
    
    public function update($id)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'supplier_code' => 'required|is_unique[suppliers.supplier_code,id,'.$id.',company_id,'.$companyId.']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'name' => $this->request->getVar('name'),
            'supplier_code' => $this->request->getVar('supplier_code'),
            'contact_person' => $this->request->getVar('contact_person'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'mobile' => $this->request->getVar('mobile'),
            'address' => $this->request->getVar('address'),
            'city' => $this->request->getVar('city'),
            'state' => $this->request->getVar('state'),
            'country' => $this->request->getVar('country'),
            'tax_number' => $this->request->getVar('tax_number'),
            'payment_terms' => $this->request->getVar('payment_terms'),
            'credit_limit' => $this->request->getVar('credit_limit'),
            'supplier_type' => $this->request->getVar('supplier_type'),
            'notes' => $this->request->getVar('notes'),
            'status' => $this->request->getVar('status'),
        ];
        
        if (!$this->supplierModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update supplier.');
        }
        
        return redirect()->to('/admin/suppliers')->with('success', 'Supplier updated successfully');
    }
    
    public function delete($id)
    {
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        // Check if supplier has associated deliveries
        if ($this->supplierModel->hasDeliveries($id)) {
            return redirect()->to('/admin/suppliers')->with('error', 'Cannot delete supplier as it has delivery records');
        }
        
        if (!$this->supplierModel->delete($id)) {
            return redirect()->to('/admin/suppliers')->with('error', 'Failed to delete supplier');
        }
        
        return redirect()->to('/admin/suppliers')->with('success', 'Supplier deleted successfully');
    }
    
    public function rate($id)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        $rules = [
            'rating' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[5]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        
        $this->supplierModel->update($id, [
            'rating' => $this->request->getVar('rating')
        ]);
        
        return redirect()->to('/admin/suppliers/view/' . $id)->with('success', 'Supplier rating updated successfully');
    }
    
    /**
     * Get materials for a supplier in JSON format
     * 
     * @param int $supplierId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getMaterials($supplierId)
    {
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($supplierId);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return $this->response->setJSON(['error' => 'Supplier not found']);
        }
        
        $materials = $this->supplierModel->getSupplierMaterials($supplierId);
        return $this->response->setJSON($materials);
    }
    
    /**
     * Add material to supplier
     * 
     * @param int $supplierId
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function addMaterial($supplierId)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($supplierId);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        $rules = [
            'material_id' => 'required|numeric',
            'unit_price' => 'required|numeric|greater_than[0]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'supplier_id' => $supplierId,
            'material_id' => $this->request->getVar('material_id'),
            'unit_price' => $this->request->getVar('unit_price'),
            'min_order_qty' => $this->request->getVar('min_order_qty'),
            'lead_time' => $this->request->getVar('lead_time'),
            'notes' => $this->request->getVar('notes'),
        ];
        
        if ($this->supplierModel->addMaterialToSupplier($data)) {
            return redirect()->to('/admin/suppliers/view/' . $supplierId)->with('success', 'Material added to supplier successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add material to supplier');
        }
    }
    
    /**
     * Remove material from supplier
     * 
     * @param int $supplierId
     * @param int $materialId
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function removeMaterial($supplierId, $materialId)
    {
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($supplierId);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        if ($this->supplierModel->removeMaterialFromSupplier($supplierId, $materialId)) {
            return redirect()->to('/admin/suppliers/view/' . $supplierId)->with('success', 'Material removed from supplier successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to remove material from supplier');
        }
    }
    
    /**
     * Record a delivery from supplier
     * 
     * @param int $supplierId
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function recordDelivery($supplierId)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $supplier = $this->supplierModel->find($supplierId);
        
        if (!$supplier || $supplier['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Supplier not found');
        }
        
        $rules = [
            'material_id' => 'required|numeric',
            'warehouse_id' => 'required|numeric',
            'quantity' => 'required|numeric|greater_than[0]',
            'unit_price' => 'required|numeric|greater_than[0]',
            'delivery_date' => 'required|valid_date',
            'reference_number' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $userId = session()->get('user_id');
        $materialId = $this->request->getVar('material_id');
        $quantity = $this->request->getVar('quantity');
        $unitPrice = $this->request->getVar('unit_price');
        
        $data = [
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
            'material_id' => $materialId,
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'delivery_date' => $this->request->getVar('delivery_date'),
            'reference_number' => $this->request->getVar('reference_number'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $quantity * $unitPrice,
            'status' => $this->request->getVar('status'),
            'notes' => $this->request->getVar('notes'),
            'created_by' => $userId
        ];
        
        if ($this->supplierModel->recordDelivery($data)) {
            return redirect()->to('/admin/suppliers/view/' . $supplierId)->with('success', 'Delivery recorded successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to record delivery');
        }
    }
    
    /**
     * View delivery details
     * 
     * @param int $deliveryId
     * @return \CodeIgniter\HTTP\Response
     */
    public function delivery($deliveryId)
    {
        $companyId = session()->get('company_id');
        $delivery = $this->supplierModel->getDeliveryDetails($deliveryId);
        
        if (!$delivery || $delivery['company_id'] != $companyId) {
            return redirect()->to('/admin/suppliers')->with('error', 'Delivery not found');
        }
        
        $data = [
            'title' => 'Delivery Details',
            'delivery' => $delivery
        ];
        
        return view('inventory/suppliers/delivery', $data);
    }
    
    /**
     * Update delivery status
     * 
     * @param int $deliveryId
     * @param string $status
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function updateDeliveryStatus($deliveryId, $status)
    {
        $deliveryModel = model(DeliveryModel::class);
        
        $delivery = $deliveryModel->find($deliveryId);
        if (!$delivery || $delivery['company_id'] != session()->get('company_id')) {
            return redirect()->to('/admin/suppliers')->with('error', 'Delivery not found');
        }
        
        // Validate status
        $validStatuses = ['received', 'partial', 'pending', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }
        
        if ($deliveryModel->updateStatus($deliveryId, $status)) {
            return redirect()->to('/admin/suppliers/delivery/' . $deliveryId)->with('success', 'Delivery status updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update delivery status');
        }
    }

    /**
     * Generate a unique supplier code for the company
     *
     * @param int $companyId
     * @return string
     */
    private function generateSupplierCode($companyId)
    {
        // Get the count of existing suppliers for this company
        $count = $this->supplierModel->where('company_id', $companyId)->countAllResults();

        // Generate code in format SUP001, SUP002, etc.
        do {
            $count++;
            $code = 'SUP' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Check if this code already exists
            $existing = $this->supplierModel->where('company_id', $companyId)
                                          ->where('supplier_code', $code)
                                          ->first();
        } while ($existing);

        return $code;
    }
}
