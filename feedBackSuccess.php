<?php
/**
 * Feedback Success Page
 * Displayed after successful feedback submission
 */

// Start the session
session_start();

// Check if user arrived here through the proper channel
if (!isset($_SESSION['feedback_success']) || $_SESSION['feedback_success'] !== true) {
    // Redirect to the feedback form
    header("Location: feedback.php");
    exit;
}

// Clear the success flag
unset($_SESSION['feedback_success']);

// Set page title
$pageTitle = 'Feedback Submitted | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Thank You!</h1>
        <h2 class="text-xl font-semibold text-primary mb-6">Your feedback has been submitted successfully.</h2>
        
        <div class="text-gray-600 mb-8 space-y-4">
            <p>Your feedback is invaluable in helping us improve our products and services.</p>
            <p>Our team reviews all feedback to identify areas where we can enhance your experience.</p>
            <p>If you've requested a response, a member of our team will be in touch with you shortly.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php" class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-md transition duration-300">
                Return to Home
            </a>
            <a href="demonstration.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-md transition duration-300">
                Schedule a Demo
            </a>
        </div>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
