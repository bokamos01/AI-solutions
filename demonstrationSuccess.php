<?php
/**
 * Demonstration Success Page
 * Displayed after a successful demonstration request submission
 */

// Start the session
session_start();

// Check if user arrived here through the proper channel
if (!isset($_SESSION['demo_success']) || $_SESSION['demo_success'] !== true) {
    // Redirect to the demonstration request form
    header("Location: demonstration.php");
    exit;
}

// Clear the success flag
unset($_SESSION['demo_success']);

// Set page title
$pageTitle = 'Demonstration Request Submitted | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Thank You!</h1>
        <h2 class="text-xl font-semibold text-primary mb-6">Your demonstration request has been submitted successfully.</h2>
        
        <div class="text-gray-600 mb-8 space-y-4">
            <p>Our team will review your request and contact you shortly to schedule your personalized demonstration.</p>
            <p>We have sent a confirmation email to the address you provided with more details.</p>
            <p>If you have any questions in the meantime, please don't hesitate to contact us.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php" class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-md transition duration-300">
                Return to Home
            </a>
            <a href="events.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-md transition duration-300">
                View Upcoming Events
            </a>
            <?php if(isset($_SESSION['staff_id'])): ?>
            <a href="manageDemos.php" class="bg-secondary hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-md transition duration-300">
                Manage Demonstrations
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
