<?php
/**
 * initReports.php - Initialize reports in the database
 * This script should be run once to populate the reports table
 */

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

try {
    // Initialize reports
    $result = $da->InitializeReports();
    
    echo "Reports initialized successfully!";
} catch (Exception $ex) {
    echo "Error initializing reports: " . $ex->getMessage();
}
