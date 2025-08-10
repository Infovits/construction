<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\ProjectModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

class DebugController extends BaseController
{
    public function checkData()
    {
        echo "<h1>Debug: Checking Database Data</h1>";
        
        // Check Projects
        echo "<h2>Projects:</h2>";
        try {
            $projectModel = new ProjectModel();
            $projects = $projectModel->findAll();
            echo "<p>Found " . count($projects) . " projects:</p>";
            if (!empty($projects)) {
                echo "<ul>";
                foreach (array_slice($projects, 0, 5) as $project) {
                    echo "<li>ID: {$project['id']}, Name: " . esc($project['name']) . "</li>";
                }
                echo "</ul>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error loading projects: " . $e->getMessage() . "</p>";
        }
        
        // Check Departments
        echo "<h2>Departments:</h2>";
        try {
            $departmentModel = new DepartmentModel();
            $departments = $departmentModel->findAll();
            echo "<p>Found " . count($departments) . " departments:</p>";
            if (!empty($departments)) {
                echo "<ul>";
                foreach (array_slice($departments, 0, 5) as $department) {
                    echo "<li>ID: {$department['id']}, Name: " . esc($department['name']) . "</li>";
                }
                echo "</ul>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error loading departments: " . $e->getMessage() . "</p>";
        }
        
        // Check Materials
        echo "<h2>Materials:</h2>";
        try {
            $materialModel = new MaterialModel();
            $materials = $materialModel->findAll();
            echo "<p>Found " . count($materials) . " materials:</p>";
            if (!empty($materials)) {
                echo "<ul>";
                foreach (array_slice($materials, 0, 5) as $material) {
                    echo "<li>ID: {$material['id']}, Name: " . esc($material['name']) . ", Code: " . esc($material['item_code']) . "</li>";
                }
                echo "</ul>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error loading materials: " . $e->getMessage() . "</p>";
        }
        
        // Check Users
        echo "<h2>Users:</h2>";
        try {
            $userModel = new UserModel();
            $users = $userModel->findAll();
            echo "<p>Found " . count($users) . " users:</p>";
            if (!empty($users)) {
                echo "<ul>";
                foreach (array_slice($users, 0, 5) as $user) {
                    echo "<li>ID: {$user['id']}, Name: " . esc($user['first_name'] . ' ' . $user['last_name']) . "</li>";
                }
                echo "</ul>";
            }
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error loading users: " . $e->getMessage() . "</p>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . base_url('admin/material-requests/create') . "'>Go to Material Request Create Form</a></p>";
    }
}
