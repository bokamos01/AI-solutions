<?php
/**
 * Event Registration Processing Script
 * Handles submission of event registration form
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
    $eventId = intval($_POST["event"] ?? 0);
    $eventTitle = trim($_POST["event_title"] ?? ''); // Get event title from form
    
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
    
    if ($eventId <= 0) {
        $errors[] = "Event selection is required";
    }
    
    // If there are validation errors, redirect back to the form
    if (!empty($errors)) {
        $_SESSION['event_errors'] = $errors;
        $_SESSION['event_form_data'] = $_POST; // Save form data for repopulating the form
        header("Location: events.php");
        exit;
    }
    
    try {
        // Check if this email already registered for this event
        $sql = "SELECT COUNT(*) AS COUNT FROM tbl_eventregistry WHERE EMAILADDRESS = ? AND EVENTID = ?";
        $result = $da->GetData($sql, [$email, $eventId]);
        
        if ($result[0]['COUNT'] > 0) {
            // Email already registered for this event
            $_SESSION['event_errors'] = ["This email address is already registered for this event. Please use a different email or contact us to retrieve your registration details."];
            $_SESSION['event_form_data'] = $_POST; // Save form data for repopulating the form
            header("Location: events.php");
            exit;
        }
        
        // Register for the event
        $result = $da->RegisterForEvent($firstName, $lastName, $email, $phone, $company, $eventId, $country);
        
        if ($result > 0) {
            // Success
            // Get the registration ID (last inserted ID)
            $sql = "SELECT MAX(REGISTRATIONID) AS REGID FROM tbl_eventregistry WHERE EMAILADDRESS = ? AND EVENTID = ?";
            $registrationResult = $da->GetData($sql, [$email, $eventId]);
            $registrationId = $registrationResult[0]['REGID'];
            
            // Get event details
            $sql = "SELECT EVENTTITTLE, EVENTDATE, EVENTTIME, VANUE FROM tbl_events WHERE EVENTID = ?";
            $eventResult = $da->GetData($sql, [$eventId]);
            
            // If we have event details from the database, use them
            if (!empty($eventResult)) {
                $eventTitle = $eventResult[0]['EVENTTITTLE'];
                $eventDate = $da->FormatDate($eventResult[0]['EVENTDATE']);
                $eventTime = $da->FormatTime($eventResult[0]['EVENTTIME']);
                $eventVenue = $eventResult[0]['VANUE'];
            } else {
                // Fallback to using the title from the form, and defaults for other values
                $eventDate = "TBD";
                $eventTime = "TBD";
                $eventVenue = "TBD";
            }
            
            // Store registration details in session for display on success page
            $_SESSION['event_reg_success'] = true;
            $_SESSION['event_reg_id'] = $registrationId;
            $_SESSION['event_title'] = $eventTitle;
            $_SESSION['event_date'] = $eventDate;
            $_SESSION['event_time'] = $eventTime;
            $_SESSION['event_venue'] = $eventVenue;
            
            // In a real implementation, you would send a confirmation email here
            
            // Redirect to success page
            header("Location: eventsSuccess.php");
            exit;
        } else {
            // Database operation failed
            $_SESSION['event_errors'] = ["Failed to register for the event. Please try again."];
            $_SESSION['event_form_data'] = $_POST; // Save form data for repopulating the form
            header("Location: events.php");
            exit;
        }
    } catch (Exception $ex) {
        // Handle database exceptions
        $_SESSION['event_errors'] = ["An error occurred: " . $ex->getMessage()];
        $_SESSION['event_form_data'] = $_POST; // Save form data for repopulating the form
        header("Location: events.php");
        exit;
    }
} else {
    // If the form wasn't submitted via POST, redirect to the form
    header("Location: events.php");
    exit;
}
?>

