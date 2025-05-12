<?php
/**
 * Event Registration Success Page
 * Displayed after a successful event registration
 */

// Start the session
session_start();

// Check if user arrived here through the proper channel
if (!isset($_SESSION['event_reg_success']) || $_SESSION['event_reg_success'] !== true) {
    // Redirect to the events page
    header("Location: events.php");
    exit;
}

// Get registration details from session
$registrationId = $_SESSION['event_reg_id'] ?? 'N/A';
$eventTitle = $_SESSION['event_title'] ?? 'Unknown Event';
$eventDate = $_SESSION['event_date'] ?? 'TBD';
$eventTime = $_SESSION['event_time'] ?? 'TBD';
$eventVenue = $_SESSION['event_venue'] ?? 'TBD';

// Clear the session variables
unset($_SESSION['event_reg_success']);
unset($_SESSION['event_reg_id']);
unset($_SESSION['event_title']);
unset($_SESSION['event_date']);
unset($_SESSION['event_time']);
unset($_SESSION['event_venue']);

// Set page title
$pageTitle = 'Event Registration Confirmed | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Registration Confirmed!</h1>
        <h2 class="text-xl font-semibold text-primary mb-6">You're now registered for the event.</h2>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h3 class="text-xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($eventTitle); ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Registration ID</span>
                    <span class="font-medium"><?php echo htmlspecialchars($registrationId); ?></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Date</span>
                    <span class="font-medium"><?php echo htmlspecialchars($eventDate); ?></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Time</span>
                    <span class="font-medium"><?php echo htmlspecialchars($eventTime); ?></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Venue</span>
                    <span class="font-medium"><?php echo htmlspecialchars($eventVenue); ?></span>
                </div>
            </div>
        </div>
        
        <div class="text-gray-600 mb-8 space-y-4">
            <p>We've sent a confirmation email with these details to your registered email address.</p>
            <p><strong>Important:</strong> Please keep your Registration ID handy as you'll need it for check-in at the event.</p>
            <p>If you have any questions or need to modify your registration, please contact us at <a href="mailto:events@ai-solutions.com" class="text-primary hover:underline">events@ai-solutions.com</a>.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php" class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-md transition duration-300">
                Return to Home
            </a>
            <a href="events.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-md transition duration-300">
                View More Events
            </a>
            <?php if(isset($_SESSION['staff_id'])): ?>
            <a href="eventRegistrations.php" class="bg-secondary hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-md transition duration-300">
                Manage Registrations
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
