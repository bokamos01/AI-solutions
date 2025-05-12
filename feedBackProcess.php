<?php
/**
 * Feedback Processing Script
 * Handles submission of feedback form
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
    $firstName = trim($_POST["firstName"] ?? '');
    $lastName = trim($_POST["lastName"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $phone = trim($_POST["phone"] ?? '');
    $feedbackType = trim($_POST["feedbackType"] ?? '');
    $feedback = trim($_POST["feedback"] ?? '');
    $rating = intval($_POST["rating"] ?? 0);
    
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
    
    if (empty($feedbackType)) {
        $errors[] = "Feedback type is required";
    }
    
    if (empty($feedback)) {
        $errors[] = "Feedback content is required";
    }
    
    // If there are validation errors, redirect back to the form
    if (!empty($errors)) {
        $_SESSION['feedback_errors'] = $errors;
        $_SESSION['feedback_form_data'] = $_POST; // Save form data for repopulating the form
        header("Location: feedback.php");
        exit;
    }
    
    try {
        // Submit the feedback
        $result = $da->SubmitFeedback($firstName, $lastName, $email, $phone, $feedback, $feedbackType, $rating);
        
        if ($result > 0) {
            // Success
            $_SESSION['feedback_success'] = true;
            
            // Redirect to success page
            header("Location: feedbackSuccess.php");
            exit;
        } else {
            // Database operation failed
            $_SESSION['feedback_errors'] = ["Failed to submit your feedback. Please try again."];
            $_SESSION['feedback_form_data'] = $_POST; // Save form data for repopulating the form
            header("Location: feedback.php");
            exit;
        }
    } catch (Exception $ex) {
        // Handle database exceptions
        $_SESSION['feedback_errors'] = ["An error occurred: " . $ex->getMessage()];
        $_SESSION['feedback_form_data'] = $_POST; // Save form data for repopulating the form
        header("Location: feedback.php");
        exit;
    }
} else {
    // If the form wasn't submitted via POST, redirect to the form
    header("Location: feedback.php");
    exit;
}
?>
