<?php
/**
 * Security Check Script
 * Controls access to admin pages based on authentication and authorization
 */

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

/**
 * Function to verify if user is logged in
 * Redirects to login page if not logged in
 */
function verifyLogin() {
    if (!isset($_SESSION['staff_id'])) {
        // User is not logged in, redirect to login page
        header("Location: login.php");
        exit;
    }
}

/**
 * Function to verify if user has admin role
 * Redirects to unauthorized page if not an admin
 */
function verifyAdmin() {
    verifyLogin(); // First verify user is logged in
    
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
        // User is not an admin, redirect to unauthorized page
        header("Location: unauthorized.php");
        exit;
    }
}

/**
 * Function to verify if user is either admin or demonstrator
 * Redirects to unauthorized page if not an admin or demonstrator
 */
function verifyStaffAccess() {
    verifyLogin(); // First verify user is logged in
    
    if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
        // User is neither admin nor demonstrator, redirect to unauthorized page
        header("Location: unauthorized.php");
        exit;
    }
}

/**
 * Function to get the current user's role name
 * Returns 'Guest' if not logged in
 */
function getCurrentUserRole() {
    if (isset($_SESSION['staff_role'])) {
        return $_SESSION['staff_role'];
    }
    return 'Guest';
}

/**
 * Function to get the current user's name
 * Returns empty string if not logged in
 */
function getCurrentUserName() {
    if (isset($_SESSION['staff_name'])) {
        return $_SESSION['staff_name'];
    }
    return '';
}

/**
 * Function to get the current user's ID
 * Returns 0 if not logged in
 */
function getCurrentUserId() {
    if (isset($_SESSION['staff_id'])) {
        return $_SESSION['staff_id'];
    }
    return 0;
}

/**
 * Function to check if current page is an admin page
 * Used for active menu highlighting
 */
function isAdminPage() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    $adminPages = [
        'dashboard.php',
        'manageStaff.php',
        'manageEvents.php',
        'manageDemos.php',
        'reports.php',
        'updateProfile.php'
    ];
    
    return in_array($currentPage, $adminPages);
}
