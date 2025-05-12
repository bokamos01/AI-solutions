<?php
/**
 * Login Page
 * Form for staff login
 */

// Start the session
session_start();

// Check if user is already logged in
if (isset($_SESSION['staff_id'])) {
    // Redirect to dashboard
    header("Location: dashboard.php");
    exit;
}

// Get any error messages
$loginError = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

// Set page title
$pageTitle = 'Staff Login | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-md w-full mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 pt-6">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Staff Login</h1>
        </div>
        
        <div class="px-6 pb-6">
            <?php if ($loginError): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <?php echo htmlspecialchars($loginError); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" action="loginProcess.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required>
                </div>
                
                <button type="submit" 
                    class="w-full bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-300">
                    Log In
                </button>
                
                <div class="mt-4 text-sm text-gray-600 text-center">
                    <p>Only authorized staff members can log in. If you're experiencing issues with your account, please contact the system administrator.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
