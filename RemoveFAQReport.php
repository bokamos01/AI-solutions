<?php
/**
 * removeFAQReport.php - Removes the FAQ Usage Metrics report from the database
 */

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

try {
    // Get a connection
    $conn = $da->GetConnection();
    
    // Delete the FAQ Usage Metrics report
    $sql = "DELETE FROM tbl_reports WHERE REPORTNAME = 'FAQ Usage Metrics'";
    $count = $conn->exec($sql);
    
    echo "Removed FAQ Usage Metrics report. Rows affected: $count";
} catch (Exception $ex) {
    echo "Error removing report: " . $ex->getMessage();
}
