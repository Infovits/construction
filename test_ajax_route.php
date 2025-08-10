<?php
// Test the AJAX route directly
$baseUrl = "http://localhost:8080";
$materialRequestId = 1; // We created this earlier

$url = "$baseUrl/admin/purchase-orders/material-request-items/$materialRequestId";

echo "Testing URL: $url\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest',
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($error) {
    echo "cURL Error: $error\n";
}

// Try to decode JSON
if ($response) {
    $data = json_decode($response, true);
    if ($data) {
        echo "\nParsed Response:\n";
        print_r($data);
    } else {
        echo "\nFailed to parse JSON response\n";
    }
}
?>