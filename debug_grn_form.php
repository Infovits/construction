<?php
// Debug GRN form submission
if ($_POST) {
    echo "<h2>GRN Form Debug - POST Data Received:</h2>";
    echo "<pre>";
    echo "=== FORM DATA ===\n";
    print_r($_POST);
    
    echo "\n=== ITEMS ANALYSIS ===\n";
    if (isset($_POST['items']) && is_array($_POST['items'])) {
        echo "Items count: " . count($_POST['items']) . "\n";
        foreach ($_POST['items'] as $index => $item) {
            echo "\nItem $index:\n";
            foreach ($item as $key => $value) {
                echo "  $key: " . (is_null($value) ? 'NULL' : $value) . "\n";
            }
        }
    } else {
        echo "No items array found or items is not an array\n";
    }
    
    echo "\n=== VALIDATION CHECK ===\n";
    $errors = [];
    
    if (empty($_POST['purchase_order_id'])) {
        $errors[] = "Missing purchase_order_id";
    }
    
    if (empty($_POST['warehouse_id'])) {
        $errors[] = "Missing warehouse_id";
    }
    
    if (empty($_POST['delivery_date'])) {
        $errors[] = "Missing delivery_date";
    }
    
    if (empty($_POST['items']) || !is_array($_POST['items'])) {
        $errors[] = "Missing or invalid items array";
    } else {
        foreach ($_POST['items'] as $index => $item) {
            if (empty($item['purchase_order_item_id'])) {
                $errors[] = "Item $index: missing purchase_order_item_id";
            }
            if (empty($item['material_id'])) {
                $errors[] = "Item $index: missing material_id";
            }
            if (empty($item['quantity_delivered']) || $item['quantity_delivered'] <= 0) {
                $errors[] = "Item $index: invalid quantity_delivered (" . ($item['quantity_delivered'] ?? 'NULL') . ")";
            }
            if (!isset($item['unit_cost']) || $item['unit_cost'] < 0) {
                $errors[] = "Item $index: invalid unit_cost (" . ($item['unit_cost'] ?? 'NULL') . ")";
            }
        }
    }
    
    if (empty($errors)) {
        echo "✅ All basic validation passed\n";
    } else {
        echo "❌ Validation errors found:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }
    
    echo "</pre>";
    exit;
}
?>

<form method="POST" action="">
    <h2>GRN Debug Form</h2>
    <p>This form will show you exactly what data is being submitted</p>
    
    <label>Purchase Order ID: <input type="number" name="purchase_order_id" value="7"></label><br><br>
    <label>Warehouse ID: <input type="number" name="warehouse_id" value="1"></label><br><br>
    <label>Delivery Date: <input type="date" name="delivery_date" value="<?= date('Y-m-d') ?>"></label><br><br>
    
    <h3>Items</h3>
    <label>Item 1 - PO Item ID: <input type="number" name="items[0][purchase_order_item_id]" value="1"></label><br>
    <label>Item 1 - Material ID: <input type="number" name="items[0][material_id]" value="1"></label><br>
    <label>Item 1 - Quantity: <input type="number" name="items[0][quantity_delivered]" value="10" step="0.001"></label><br>
    <label>Item 1 - Unit Cost: <input type="number" name="items[0][unit_cost]" value="100" step="0.01"></label><br><br>
    
    <button type="submit">Debug Submit</button>
</form>