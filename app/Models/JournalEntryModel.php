<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntryModel extends Model
{
    protected $table = 'journal_entries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'entry_number',
        'reference_type', 
        'reference_id',
        'entry_date',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'posted_at',
        'posted_by',
        'reversed_at',
        'reversed_by',
        'reversal_reason',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'required|integer',
        'entry_number' => 'required|max_length[50]',
        'entry_date' => 'required|valid_date',
        'description' => 'required|max_length[1000]',
        'total_debit' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'total_credit' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'status' => 'required|in_list[draft,posted,reversed]'
    ];

    protected $validationMessages = [
        'entry_number' => [
            'required' => 'Entry number is required',
            'max_length' => 'Entry number cannot exceed 50 characters'
        ],
        'entry_date' => [
            'required' => 'Entry date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'description' => [
            'required' => 'Description is required',
            'max_length' => 'Description cannot exceed 1000 characters'
        ],
        'total_debit' => [
            'decimal' => 'Total debit must be a valid decimal number',
            'greater_than_equal_to' => 'Total debit must be 0 or greater'
        ],
        'total_credit' => [
            'decimal' => 'Total credit must be a valid decimal number',
            'greater_than_equal_to' => 'Total credit must be 0 or greater'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['addCompanyId', 'generateEntryNumber'];

    /**
     * Add company_id if not provided
     */
    protected function addCompanyId(array $data)
    {
        if (!isset($data['data']['company_id'])) {
            $data['data']['company_id'] = session('company_id') ?? 1;
        }
        return $data;
    }

    /**
     * Generate entry number if not provided
     */
    protected function generateEntryNumber(array $data)
    {
        if (!isset($data['data']['entry_number'])) {
            $data['data']['entry_number'] = $this->getNextEntryNumber();
        }
        return $data;
    }

    /**
     * Get journal entries with details
     */
    public function getJournalEntriesWithDetails($filters = [])
    {
        // Check if table exists first
        if (!$this->db->tableExists($this->table)) {
            return [];
        }
        
        try {
            $builder = $this->db->table($this->table . ' je');
            
            $builder->select("
                je.*,
                COUNT(jel.id) as line_count
            ", false);
            
            $builder->join('journal_entry_lines jel', 'je.id = jel.journal_entry_id', 'left');
            $builder->where('je.company_id', session('company_id') ?? 1);
            
            // Apply filters
            if (!empty($filters['status'])) {
                $builder->where('je.status', $filters['status']);
            }
            
            if (!empty($filters['date_from'])) {
                $builder->where('je.entry_date >=', $filters['date_from']);
            }
            
            if (!empty($filters['date_to'])) {
                $builder->where('je.entry_date <=', $filters['date_to']);
            }
            
            if (!empty($filters['search'])) {
                $builder->groupStart()
                       ->like('je.entry_number', $filters['search'])
                       ->orLike('je.description', $filters['search'])
                       ->orLike('je.reference_type', $filters['search'])
                       ->groupEnd();
            }
            
            $builder->groupBy('je.id');
            $builder->orderBy('je.entry_date', 'DESC');
            $builder->orderBy('je.entry_number', 'DESC');
            
            return $builder->get()->getResultArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting journal entries: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get journal entry with lines
     */
    public function getJournalEntryWithLines($id)
    {
        // Get journal entry header
        $entry = $this->find($id);
        if (!$entry) {
            return null;
        }

        // Get journal entry lines
        $journalLineModel = new JournalEntryLineModel();
        $entry['lines'] = $journalLineModel->getJournalLinesWithAccounts($id);
        
        return $entry;
    }

    /**
     * Get next entry number
     */
    public function getNextEntryNumber($prefix = 'JE')
    {
        $companyId = session('company_id') ?? 1;
        $year = date('Y');
        
        $builder = $this->where('company_id', $companyId)
                       ->like('entry_number', $prefix . '-' . $year, 'after')
                       ->orderBy('entry_number', 'DESC')
                       ->limit(1);
        
        $lastEntry = $builder->first();
        
        if ($lastEntry) {
            // Extract number from format: JE-2025-001
            preg_match('/(\d+)$/', $lastEntry['entry_number'], $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Post journal entry
     */
    public function postJournalEntry($id, $userId = null)
    {
        $entry = $this->find($id);
        if (!$entry || $entry['status'] !== 'draft') {
            return false;
        }

        // Validate that debits equal credits
        if (!$this->validateJournalBalance($id)) {
            return false;
        }

        $data = [
            'status' => 'posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'posted_by' => $userId ?? session('user_id')
        ];

        return $this->update($id, $data);
    }

    /**
     * Reverse journal entry
     */
    public function reverseJournalEntry($id, $userId = null)
    {
        $entry = $this->find($id);
        if (!$entry || $entry['status'] !== 'posted') {
            return false;
        }

        // Create reversal entry
        $reversalData = [
            'company_id' => $entry['company_id'],
            'entry_number' => $this->getNextEntryNumber('REV'),
            'entry_date' => date('Y-m-d'),
            'reference_type' => 'REV-' . $entry['entry_number'],
            'description' => 'Reversal of ' . $entry['description'],
            'total_debit' => $entry['total_debit'],
            'total_credit' => $entry['total_credit'],
            'status' => 'posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'posted_by' => $userId ?? session('user_id'),
            'created_by' => $userId ?? session('user_id')
        ];

        $reversalId = $this->insert($reversalData, true);
        
        if ($reversalId) {
            // Create reversal lines (swap debits and credits)
            $journalLineModel = new JournalEntryLineModel();
            $originalLines = $journalLineModel->where('journal_entry_id', $id)->findAll();
            
            foreach ($originalLines as $line) {
                $reversalLineData = [
                    'journal_entry_id' => $reversalId,
                    'account_id' => $line['account_id'],
                    'description' => 'Reversal: ' . $line['description'],
                    'debit_amount' => $line['credit_amount'], // Swap
                    'credit_amount' => $line['debit_amount'], // Swap
                    'project_id' => $line['project_id'],
                    'cost_code_id' => $line['cost_code_id'],
                    'reference' => $line['reference'],
                    'line_number' => $line['line_number']
                ];
                
                $journalLineModel->insert($reversalLineData);
            }

            // Update original entry
            $this->update($id, [
                'status' => 'reversed',
                'reversed_at' => date('Y-m-d H:i:s'),
                'reversed_by' => $userId ?? session('user_id'),
                'reversal_reason' => 'Reversed by entry #' . $reversalId
            ]);

            return $reversalId;
        }

        return false;
    }

    /**
     * Validate journal entry balance (debits = credits)
     */
    public function validateJournalBalance($id)
    {
        $journalLineModel = new JournalEntryLineModel();
        
        $totals = $journalLineModel->select('
                SUM(debit_amount) as total_debit, 
                SUM(credit_amount) as total_credit
            ')
            ->where('journal_entry_id', $id)
            ->first();
        
        $totalDebit = floatval($totals['total_debit'] ?? 0);
        $totalCredit = floatval($totals['total_credit'] ?? 0);
        
        return abs($totalDebit - $totalCredit) < 0.01; // Allow for small rounding differences
    }

    /**
     * Get journal entry statistics
     */
    public function getJournalStats()
    {
        // Check if table exists first
        if (!$this->db->tableExists($this->table)) {
            return [];
        }
        
        $builder = $this->db->table($this->table);
        
        $builder->select("
            status,
            COUNT(*) as entry_count,
            SUM(total_debit) as total_debit,
            SUM(total_credit) as total_credit,
            AVG(total_debit) as avg_debit,
            AVG(total_credit) as avg_credit
        ", false);
        
        $builder->where('company_id', session('company_id') ?? 1);
        $builder->groupBy('status');
        
        return $builder->get()->getResultArray();
    }
}