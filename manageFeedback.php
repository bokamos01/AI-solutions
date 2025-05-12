<?php
/**
 * manageFeedback.php - Admin feedback management page
 * Displays all feedback submitted by users
 */

// Start the session
session_start();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Check if user is logged in and is an admin
if (!$da->isLoggedIn() || !$da->isAdmin()) {
    // Not logged in or not an admin, redirect to login
    header("Location: login.php");
    exit;
}

// Get all feedback entries
$feedbackList = $da->GetAllFeedback();

// Set page title
$pageTitle = 'Manage Feedback | AI Solutions';

// Start output buffering to capture content
ob_start();
?>
<div class="max-w-7xl mx-auto px-4 py-8">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Feedback Management</h1>
        <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            Return to Dashboard
        </a>
    </div>

    <?php if (empty($feedbackList)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
            <p>No feedback has been submitted yet.</p>
        </div>
    <?php else: ?>
        <!-- Responsive grid layout instead of table -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($feedbackList as $feedback): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-md transition-shadow"
                     onclick="showFeedbackDetails(
                         '<?php echo htmlspecialchars($feedback['FEEDBACKID']); ?>',
                         '<?php echo htmlspecialchars($feedback['FIRSTNAME'] . ' ' . $feedback['LASTNAME']); ?>',
                         '<?php echo htmlspecialchars($feedback['EMAILADDRESS']); ?>',
                         '<?php echo htmlspecialchars($feedback['PHONENUMBER']); ?>',
                         '<?php echo htmlspecialchars($feedback['FEEDBACKTYPE'] ?? 'General'); ?>',
                         '<?php echo htmlspecialchars($feedback['RATING'] ?? 0); ?>',
                         '<?php echo htmlspecialchars(str_replace("'", "\\'", $feedback['FEEDBACK'])); ?>'
                     )">
                    <div class="border-b px-4 py-3 bg-gray-50 flex justify-between items-center">
                        <div class="font-medium"><?php echo htmlspecialchars($feedback['FIRSTNAME'] . ' ' . $feedback['LASTNAME']); ?></div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            <?php echo htmlspecialchars($feedback['FEEDBACKTYPE'] ?? 'General'); ?>
                        </span>
                    </div>
                    <div class="px-4 py-3">
                        <div class="text-sm mb-2">
                            <span class="font-medium text-gray-500">Contact:</span> 
                            <?php echo htmlspecialchars($feedback['EMAILADDRESS']); ?>
                            <?php if (!empty($feedback['PHONENUMBER'])): ?>
                                <br><?php echo htmlspecialchars($feedback['PHONENUMBER']); ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($feedback['RATING']) && $feedback['RATING'] > 0): ?>
                            <div class="flex text-yellow-400 mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $feedback['RATING']): ?>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 24 24">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                        </svg>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-sm text-gray-900 border-t pt-2 mt-2">
                            <?php 
                            $feedbackText = htmlspecialchars($feedback['FEEDBACK']);
                            echo (strlen($feedbackText) > 150) ? substr($feedbackText, 0, 150) . '...' : $feedbackText;
                            ?>
                            <div class="text-indigo-600 hover:text-indigo-900 text-xs mt-2">Click to view full details</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal for viewing feedback details -->
    <div id="feedbackModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">
                                Feedback Details
                            </h3>
                            <div class="mt-2">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From:</label>
                                    <p id="modalName" class="text-sm text-gray-900"></p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact:</label>
                                    <p id="modalEmail" class="text-sm text-gray-900"></p>
                                    <p id="modalPhone" class="text-sm text-gray-900"></p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Type:</label>
                                    <p id="modalType" class="text-sm text-gray-900"></p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating:</label>
                                    <div id="modalRating" class="flex text-yellow-400"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feedback:</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                        <p id="modalFeedback" class="text-sm text-gray-900 whitespace-pre-wrap"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
