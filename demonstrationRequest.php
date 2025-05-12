<?php
/**
 * Demonstration Request Processing Script
 * Handles submission of demonstration request form
 */

// Start the session
session_start();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = trim($_POST["name"] ?? '');
    $lastName = trim($_POST["lastname"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $phone = trim($_POST["phone"] ?? '');
    $company = trim($_POST["company"] ?? '');
    $country = intval($_POST["country"] ?? 0);
    $interests = trim($_POST["interests"] ?? '');
    $additional = trim($_POST["additional"] ?? '');
    
    // Combine interests and additional information for the interest description
    $interestDescription = "Interest: $interests\n\nAdditional Information: $additional";
    
    // Validate required fields
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($company)) {
        $errors[] = "Company name is required";
    }
    
    if ($country <= 0) {
        $errors[] = "Country selection is required";
    }
    
    if (empty($interests)) {
        $errors[] = "Interest selection is required";
    }
    
    // If there are validation errors, redirect back to the form
    if (!empty($errors)) {
        $_SESSION['demo_errors'] = $errors;
        $_SESSION['demo_form_data'] = $_POST; // Save form data for repopulating the form
        header("Location: demonstration.php");
        exit;
    }
    
    try {
        // Request the demonstration
        $result = $da->RequestDemonstration($firstName, $lastName, $email, $phone, $company, $interestDescription, $country);
        
        if ($result > 0) {
            // Success
            $_SESSION['demo_success'] = true;
            
            // In a real implementation, you would send a confirmation email here
            
            // Redirect to success page
            header("Location: demonstrationSuccess.php");
            exit;
        } else {
            // Database operation failed
            $_SESSION['demo_errors'] = ["Failed to submit your demonstration request. Please try again."];
            $_SESSION['demo_form_data'] = $_POST; // Save form data for repopulating the form
            header("Location: demonstration.php");
            exit;
        }
    } catch (Exception $ex) {
        // Handle database exceptions
        $_SESSION['demo_errors'] = ["An error occurred: " . $ex->getMessage()];
        $_SESSION['demo_form_data'] = $_POST; // Save form data for repopulating the form
        header("Location: demonstration.php");
        exit;
    }
} else {
    // If the form wasn't submitted via POST, redirect to the form
    header("Location: demonstration.php");
    exit;
}
?>
