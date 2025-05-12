<?php
/**
 * Login Processing Script
 * Handles authentication of staff members
 */

// Start the session
session_start();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from the form
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email and password are required.";
        header("Location: login.php");
        exit;
    }
    
    try {
        // Authenticate the user
        $userDetails = $da->GetUserDetails($email, $password);
        
        if (count($userDetails) > 0) {
            // Authentication successful, set session variables
            $_SESSION['staff_id'] = $userDetails[0]['STAFFID'];
            $_SESSION['staff_name'] = $userDetails[0]['FIRSTNAME'] . ' ' . $userDetails[0]['SURNAME'];
            $_SESSION['staff_email'] = $userDetails[0]['EMAILADDRESS'];
            $_SESSION['role_id'] = $userDetails[0]['ROLEID'];
            $_SESSION['staff_role'] = $userDetails[0]['ROLE'];
            
            // Also set the firstname and surname variables for the welcome message
            $_SESSION['firstname'] = $userDetails[0]['FIRSTNAME'];
            $_SESSION['surname'] = $userDetails[0]['SURNAME'];
            
            // Redirect based on role
            if ($_SESSION['role_id'] == 2) { // Demonstrator
                header("Location: manageDemos.php");
            } else { // Admin or other roles
                header("Location: dashboard.php");
            }
            exit;
        } else {
            // Authentication failed
            $_SESSION['login_error'] = "Invalid email or password.";
            header("Location: login.php");
            exit;
        }
    } catch (Exception $ex) {
        // Handle database exceptions
        $_SESSION['login_error'] = "An error occurred during login: " . $ex->getMessage();
        header("Location: login.php");
        exit;
    }
} else {
    // If the form wasn't submitted via POST, redirect to the login form
    header("Location: login.php");
    exit;
}
?>
