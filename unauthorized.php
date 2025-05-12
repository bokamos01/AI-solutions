<?php
/**
 * Unauthorized Access Page
 * Displayed when a user tries to access a restricted page
 */

// Set page title
$pageTitle = 'Unauthorized Access | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="unauthorized-container">
    <div class="unauthorized-icon">
        <i class="icon-lock">🔒</i>
    </div>
    <h1>Unauthorized Access</h1>
    <p>You do not have permission to access this page.</p>
    <p>If you believe this is an error, please contact your administrator.</p>
    
    <div class="action-buttons">
        <a href="index.php" class="primary-button">Return to Home</a>
        <?php if (isset($_SESSION['staff_id'])): ?>
            <a href="dashboard.php" class="secondary-button">Go to Dashboard</a>
        <?php else: ?>
            <a href="login.php" class="secondary-button">Log In</a>
        <?php endif; ?>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
