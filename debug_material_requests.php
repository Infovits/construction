<?php
// Simple debug script to test material requests
echo "<h1>Debug Material Requests</h1>";

// Test if we can access the database
try {
    $db = \Config\Database::connect();
    echo "<p>✅ Database connection successful</p>";
    
    // Check if material_requests table exists
    if ($db->tableExists('material_requests')) {
        echo "<p>✅ material_requests table exists</p>";
    } else {
        echo "<p>❌ material_requests table does not exist</p>";
    }
    
    // Check if projects table exists
    if ($db->tableExists('projects')) {
        echo "<p>✅ projects table exists</p>";
    } else {
        echo "<p>❌ projects table does not exist</p>";
    }
    
    // Check if users table exists
    if ($db->tableExists('users')) {
        echo "<p>✅ users table exists</p>";
    } else {
        echo "<p>❌ users table does not exist</p>";
    }
    
    // Check if departments table exists
    if ($db->tableExists('departments')) {
        echo "<p>✅ departments table exists</p>";
    } else {
        echo "<p>❌ departments table does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<p><a href='" . base_url('admin/material-requests') . "'>Try Material Requests Page</a></p>";
?>
