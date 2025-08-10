<?php
// Test the AJAX route with the correct Material Request ID
$baseUrl = "http://localhost:8080";
$materialRequestId = 1; // The correct ID that exists

$url = "$baseUrl/admin/purchase-orders/material-request-items/$materialRequestId";

echo "Testing with CORRECT Material Request ID: $materialRequestId\n";
echo "URL: $url\n\n";

// Initialize cURL with session simulation
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt'); // Store cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt'); // Use cookies

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
}

if ($httpCode == 200) {
    echo "✅ Success! Response:\n";
    echo $response . "\n\n";
    
    // Try to decode JSON
    $data = json_decode($response, true);
    if ($data) {
        if (isset($data['success']) && $data['success']) {
            echo "✅ AJAX call successful!\n";
            echo "Material Request: {$data['materialRequest']['request_number']}\n";
            echo "Items found: " . count($data['items']) . "\n";
            
            if (!empty($data['items'])) {
                echo "\nItems details:\n";
                foreach ($data['items'] as $item) {
                    echo "- {$item['material_name']}: {$item['quantity_approved']} {$item['unit']} @ MWK {$item['estimated_unit_cost']}\n";
                }
            }
        } else {
            echo "❌ AJAX call failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Failed to parse JSON response\n";
    }
} else {
    echo "❌ HTTP Error $httpCode\n";
    echo "Response: " . substr($response, 0, 500) . "...\n";
}
?>