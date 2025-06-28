<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Barcode Scanner - Materials<?= $this->endSection() ?>

<?= $this->section('head') ?>
<!-- Quagga JS for barcode scanning -->
<script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
<style>
    #barcode-scanner {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }
    
    #barcode-scanner video,
    #barcode-scanner canvas {
        width: 100%;
        height: auto;
        max-height: 400px;
    }
    
    #barcode-scanner canvas.drawingBuffer {
        position: absolute;
        top: 0;
        left: 0;
    }
    
    #barcode-scanner-loader {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: 10;
    }
    
    #material-details {
        display: none;
        margin-top: 2rem;
    }
    
    .scan-result-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    
    .scan-success {
        border: 4px solid #10B981;
    }
    
    .scan-error {
        border: 4px solid #EF4444;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Barcode Scanner</h1>
            <p class="text-gray-600">Scan material barcodes to quickly check stock levels and details</p>
        </div>
        <div>
            <a href="<?= base_url('admin/materials') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Materials
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Barcode Scanner Section -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Scan Barcode</h2>
                <p class="text-sm text-gray-600">Position the barcode within the viewfinder</p>
            </div>
            
            <div class="p-4">
                <div class="mb-4">
                    <div class="flex space-x-2">
                        <button id="start-scanner" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="scan-barcode" class="w-4 h-4 mr-2"></i>
                            Start Scanner
                        </button>
                        <button id="stop-scanner" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" disabled>
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                            Stop Scanner
                        </button>
                    </div>
                </div>
                
                <div id="barcode-scanner">
                    <div id="barcode-scanner-loader" style="display: none;">
                        <div class="text-center">
                            <svg class="animate-spin h-10 w-10 mb-4 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-lg font-semibold">Initializing camera...</p>
                            <p class="text-sm mt-2 text-gray-600">Please allow camera access if prompted</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex items-center">
                        <p class="text-sm text-gray-600 mr-4">No barcode scanner available?</p>
                        <div class="flex space-x-2 w-full">
                            <input type="text" id="manual-barcode" placeholder="Enter barcode manually" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 flex-grow">
                            <button id="submit-barcode" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Material Details Section -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Material Details</h2>
                <p class="text-sm text-gray-600">Information about the scanned item</p>
            </div>
            
            <div class="p-4">
                <div id="material-placeholder" class="text-center p-8">
                    <i data-lucide="package-search" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Scan a barcode or enter a code manually to view material details</p>
                </div>
                
                <div id="material-details" class="space-y-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="mr-4">
                            <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="package" class="w-8 h-8 text-indigo-600"></i>
                            </div>
                        </div>
                        <div>
                            <h3 id="material-name" class="text-lg font-semibold text-gray-900"></h3>
                            <div class="flex space-x-2 mt-1">
                                <span id="material-code" class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"></span>
                                <span id="material-category" class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border rounded-lg overflow-hidden">
                        <div class="border-b px-4 py-2 bg-gray-50">
                            <h4 class="font-medium text-gray-700">Stock Information</h4>
                        </div>
                        <div class="p-4 space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <span class="text-sm text-gray-500">Current Stock:</span>
                                    <p id="material-stock" class="font-semibold text-lg"></p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Unit:</span>
                                    <p id="material-unit" class="font-semibold"></p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Unit Cost:</span>
                                    <p id="material-cost" class="font-semibold"></p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Minimum Stock:</span>
                                    <p id="material-min-stock" class="font-semibold"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="stock-levels-section" class="border rounded-lg overflow-hidden">
                        <div class="border-b px-4 py-2 bg-gray-50">
                            <h4 class="font-medium text-gray-700">Warehouse Stock Levels</h4>
                        </div>
                        <div class="p-0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    </tr>
                                </thead>
                                <tbody id="stock-levels-body" class="bg-white divide-y divide-gray-200">
                                    <!-- Stock levels will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button id="record-movement-btn" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="move" class="w-4 h-4 mr-2"></i>
                            Record Stock Movement
                        </button>
                        <a id="movement-link" href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="history" class="w-4 h-4 mr-2"></i>
                            View Movement History
                        </a>
                        <a id="edit-link" href="#" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                            Edit Material
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Movement Modal -->
<div id="stock-movement-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 text-center">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="modal-overlay"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Record Stock Movement</h3>
                <button id="close-modal" class="text-gray-400 hover:text-gray-500">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-4">
                <div class="mb-4 flex items-center p-3 bg-indigo-50 rounded-lg">
                    <div class="mr-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="package" class="w-6 h-6 text-indigo-600"></i>
                        </div>
                    </div>
                    <div>
                        <h4 id="modal-material-name" class="text-md font-semibold text-gray-900"></h4>
                        <div class="flex space-x-2 mt-1">
                            <span id="modal-material-code" class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"></span>
                            <span id="modal-current-stock" class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800"></span>
                        </div>
                    </div>
                </div>
                
                <form id="stock-movement-form">
                    <input type="hidden" id="material-id" name="material_id">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="movement-type" class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
                            <select id="movement-type" name="movement_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="stock_in">Stock In</option>
                                <option value="stock_out">Stock Out</option>
                                <option value="transfer">Transfer</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="warehouse-id" class="block text-sm font-medium text-gray-700 mb-1">Warehouse</label>
                            <select id="warehouse-id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Warehouse</option>
                                <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?> (<?= $warehouse['code'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="destination-warehouse-container" class="hidden">
                            <label for="destination-warehouse-id" class="block text-sm font-medium text-gray-700 mb-1">Destination Warehouse</label>
                            <select id="destination-warehouse-id" name="destination_warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Destination Warehouse</option>
                                <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?> (<?= $warehouse['code'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" id="quantity" name="quantity" min="0.01" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>
                        
                        <div>
                            <label for="project-id" class="block text-sm font-medium text-gray-700 mb-1">Project (Optional)</label>
                            <select id="project-id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">No Project</option>
                                <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>"><?= $project['name'] ?> (#<?= $project['project_code'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-5 flex justify-end space-x-3">
                        <button type="button" id="cancel-movement" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" id="save-movement" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Movement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startScannerBtn = document.getElementById('start-scanner');
    const stopScannerBtn = document.getElementById('stop-scanner');
    const manualBarcodeInput = document.getElementById('manual-barcode');
    const submitBarcodeBtn = document.getElementById('submit-barcode');
    const scannerLoader = document.getElementById('barcode-scanner-loader');
    const materialDetails = document.getElementById('material-details');
    const materialPlaceholder = document.getElementById('material-placeholder');
    const recordMovementBtn = document.getElementById('record-movement-btn');
    const stockMovementModal = document.getElementById('stock-movement-modal');
    const modalOverlay = document.getElementById('modal-overlay');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelMovementBtn = document.getElementById('cancel-movement');
    const stockMovementForm = document.getElementById('stock-movement-form');
    const movementTypeSelect = document.getElementById('movement-type');
    const destinationWarehouseContainer = document.getElementById('destination-warehouse-container');
    
    let quaggaInitialized = false;
    let latestBarcode = null;
    let currentMaterial = null;
    
    // Initialize the scanner
    function initQuagga() {
        if (quaggaInitialized) {
            return;
        }
        
        scannerLoader.style.display = 'flex';
        
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#barcode-scanner'),
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                },
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader", "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader", "2of5_reader", "code_93_reader"]
            }
        }, function(err) {
            scannerLoader.style.display = 'none';
            
            if (err) {
                console.error('Failed to initialize Quagga:', err);
                alert('Failed to initialize the barcode scanner. Please make sure you have granted camera access and try again.');
                return;
            }
            
            console.log('Quagga initialized successfully');
            quaggaInitialized = true;
            Quagga.start();
            
            startScannerBtn.disabled = true;
            stopScannerBtn.disabled = false;
        });
        
        Quagga.onDetected(function(result) {
            const code = result.codeResult.code;
            
            if (code && code !== latestBarcode) {
                latestBarcode = code;
                manualBarcodeInput.value = code;
                
                // Play a beep sound on successful scan
                const beep = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU...');
                beep.volume = 0.2;
                beep.play();
                
                // Look up the material
                lookupMaterial(code);
            }
        });
    }
    
    // Stop the scanner
    function stopQuagga() {
        if (quaggaInitialized) {
            Quagga.stop();
            quaggaInitialized = false;
            
            startScannerBtn.disabled = false;
            stopScannerBtn.disabled = true;
        }
    }
    
    // Look up a material by barcode
    function lookupMaterial(barcode) {
        // Show loading spinner
        materialDetails.style.display = 'none';
        materialPlaceholder.innerHTML = `
            <div class="text-center">
                <svg class="animate-spin h-10 w-10 mb-4 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600">Looking up barcode ${barcode}...</p>
            </div>
        `;
        materialPlaceholder.style.display = 'block';
        
        fetch('<?= base_url('admin/materials/get-material-by-barcode') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({ barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                materialPlaceholder.innerHTML = `
                    <div class="text-center p-8">
                        <i data-lucide="alert-circle" class="w-16 h-16 mx-auto text-red-500 mb-4"></i>
                        <p class="text-red-500 font-medium">Material Not Found</p>
                        <p class="text-gray-500 mt-2">No material found with barcode "${barcode}"</p>
                    </div>
                `;
                materialDetails.style.display = 'none';
                lucide.createIcons();
                return;
            }
            
            // Material found, populate details
            const material = data.material;
            const stockLevels = data.stockLevels;
            
            // Store current material data for modal use
            currentMaterial = material;
            
            document.getElementById('material-name').textContent = material.name;
            document.getElementById('material-code').textContent = material.item_code;
            document.getElementById('material-category').textContent = material.category_id ? 'Category: ' + material.category_name : 'Uncategorized';
            document.getElementById('material-stock').textContent = material.current_stock;
            document.getElementById('material-unit').textContent = material.unit;
            document.getElementById('material-cost').textContent = '₱' + parseFloat(material.unit_cost).toFixed(2);
            document.getElementById('material-min-stock').textContent = material.minimum_stock;
            
            // Populate stock levels table
            const stockLevelsBody = document.getElementById('stock-levels-body');
            stockLevelsBody.innerHTML = '';
            
            if (stockLevels && stockLevels.length > 0) {
                stockLevels.forEach(stock => {
                    const row = document.createElement('tr');
                    
                    // Determine if stock level is low
                    const isLowStock = parseFloat(stock.current_quantity) <= parseFloat(stock.minimum_quantity);
                    const quantityClass = isLowStock ? 'text-red-600 font-medium' : 'text-gray-900';
                    
                    row.innerHTML = `
                        <td class="px-4 py-3 text-sm text-gray-900">${stock.warehouse_name} (${stock.warehouse_code})</td>
                        <td class="px-4 py-3 text-sm ${quantityClass}">${stock.current_quantity}${isLowStock ? ' <span class="text-xs text-red-500">(Low Stock)</span>' : ''}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">${stock.shelf_location || 'Not specified'}</td>
                    `;
                    
                    stockLevelsBody.appendChild(row);
                });
                
                document.getElementById('stock-levels-section').style.display = 'block';
            } else {
                document.getElementById('stock-levels-section').style.display = 'none';
            }
            
            // Update links
            document.getElementById('movement-link').href = '<?= base_url('admin/materials/stock-movement/') ?>' + material.id;
            document.getElementById('edit-link').href = '<?= base_url('admin/materials/edit/') ?>' + material.id;
            
            // Enable the record movement button
            recordMovementBtn.disabled = false;
            
            // Show material details and hide placeholder
            materialDetails.style.display = 'block';
            materialPlaceholder.style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching material:', error);
            materialPlaceholder.innerHTML = `
                <div class="text-center p-8">
                    <i data-lucide="alert-triangle" class="w-16 h-16 mx-auto text-amber-500 mb-4"></i>
                    <p class="text-amber-500 font-medium">Error</p>
                    <p class="text-gray-500 mt-2">Failed to lookup material information</p>
                </div>
            `;
            materialDetails.style.display = 'none';
            lucide.createIcons();
        });
    }
    
    // Event listeners
    startScannerBtn.addEventListener('click', initQuagga);
    stopScannerBtn.addEventListener('click', stopQuagga);
    
    submitBarcodeBtn.addEventListener('click', function() {
        const barcode = manualBarcodeInput.value.trim();
        if (barcode) {
            lookupMaterial(barcode);
        }
    });
    
    manualBarcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const barcode = this.value.trim();
            if (barcode) {
                lookupMaterial(barcode);
            }
        }
    });
    
    // Cleanup when leaving the page
    window.addEventListener('beforeunload', function() {
        stopQuagga();
    });
    
    // Handle movement type change to show/hide destination warehouse
    movementTypeSelect.addEventListener('change', function() {
        if (this.value === 'transfer') {
            destinationWarehouseContainer.classList.remove('hidden');
        } else {
            destinationWarehouseContainer.classList.add('hidden');
        }
    });
    
    // Record Movement Button Click
    recordMovementBtn.addEventListener('click', function() {
        if (!currentMaterial) return;
        
        // Populate the modal with material details
        document.getElementById('modal-material-name').textContent = currentMaterial.name;
        document.getElementById('modal-material-code').textContent = currentMaterial.item_code;
        document.getElementById('modal-current-stock').textContent = `Stock: ${currentMaterial.current_stock} ${currentMaterial.unit}`;
        document.getElementById('material-id').value = currentMaterial.id;
        
        // Reset form fields
        stockMovementForm.reset();
        document.getElementById('material-id').value = currentMaterial.id;
        
        // Show modal
        stockMovementModal.classList.remove('hidden');
    });
    
    // Close modal events
    [closeModalBtn, cancelMovementBtn, modalOverlay].forEach(element => {
        element.addEventListener('click', function() {
            stockMovementModal.classList.add('hidden');
        });
    });
    
    // Handle form submission
    stockMovementForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        const warehouseId = document.getElementById('warehouse-id').value;
        const quantity = document.getElementById('quantity').value;
        const movementType = document.getElementById('movement-type').value;
        
        if (!warehouseId || !quantity || parseFloat(quantity) <= 0) {
            alert('Please select a warehouse and enter a valid quantity');
            return;
        }
        
        if (movementType === 'transfer' && document.getElementById('destination-warehouse-id').value === warehouseId) {
            alert('Source and destination warehouses cannot be the same');
            return;
        }
        
        // Collect form data
        const formData = new FormData(stockMovementForm);
        
        // Show loading state
        const saveButton = document.getElementById('save-movement');
        saveButton.disabled = true;
        saveButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        
        // Submit the form via AJAX
        fetch('<?= base_url('admin/materials/record-stock-movement') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal
                stockMovementModal.classList.add('hidden');
                
                // Show success message
                alert('Stock movement recorded successfully');
                
                // Refresh material information
                if (currentMaterial && currentMaterial.item_code) {
                    lookupMaterial(currentMaterial.item_code);
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to record stock movement'));
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save Movement';
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
            alert('An error occurred while processing your request');
            saveButton.disabled = false;
            saveButton.innerHTML = 'Save Movement';
        });
    });
    
    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
