<?php
/**
 * Master template for AI Solutions website
 */

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in but firstname/surname aren't set
if (isset($_SESSION['staff_id']) && (!isset($_SESSION['firstname']) || !isset($_SESSION['surname']))) {
    require_once 'DataAccess.php';
    $db = new DataAccess();
    $sql = "SELECT FIRSTNAME, SURNAME FROM tbl_staff WHERE STAFFID = ?";
    $result = $db->GetData($sql, [$_SESSION['staff_id']]);
    
    if (count($result) > 0) {
        $_SESSION['firstname'] = $result[0]['FIRSTNAME'];
        $_SESSION['surname'] = $result[0]['SURNAME'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'AI Solutions'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind.cofig.js"></script>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <?php echo isset($additionalHead) ? $additionalHead : ''; ?>
</head>

<body class="flex flex-col min-h-screen font-sans text-gray-800 m-0">
    <!-- Navigation Bar -->
    <nav class="<?php echo isset($_SESSION['staff_id']) ? 'bg-gray-100' : 'bg-secondary text-white'; ?> sticky top-0 z-40 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo isset($_SESSION['staff_id']) ? ((isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) ? 'index.php' : 'dashboard.php') : 'index.php'; ?>" class="<?php echo isset($_SESSION['staff_id']) ? 'text-secondary' : 'text-white'; ?> text-xl font-bold">AI Solutions</a>
                </div>
                <div class="hidden md:flex md:items-center">
                    <?php if (!isset($_SESSION['staff_id'])): ?>
                        <a href="index.php" class="text-white hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Home</a>
                        <a href="events.php" class="text-white hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Events</a>
                        <a href="demonstration.php" class="text-white hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Request Demo</a>
                        <a href="feedback.php" class="text-white hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Feedback</a>
                        <a href="faq.php" class="text-white hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">FAQ</a>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['staff_id'])): ?>
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                        <a href="dashboard.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
			<a href="manageStaff.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Manage Staff</a>
                        <a href="manageEvents.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Manage Events</a>
			<a href="manageFAQ.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Manage FAQ</a>
			<?php endif; ?>

			<?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)): ?>
                        <a href="manageDemos.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Manage Demos</a>
                        <?php endif; ?>
 			
			<?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                        <a href="Reports.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Reports</a>
                        <?php endif; ?>
                       
                        <a href="updateProfile.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Profile</a>
                        <a href="logout.php" class="text-secondary hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Log Out</a>
                    <?php else: ?>
                        <a href="login.php" class="text-white hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">Log In</a>
                    <?php endif; ?>
                </div>
                <div class="flex items-center md:hidden">
                    <button id="navbar-toggle" type="button" class="<?php echo isset($_SESSION['staff_id']) ? 'text-secondary' : 'text-white'; ?> inline-flex items-center justify-center p-2 rounded-md hover:text-primary focus:outline-none">
                        <span class="sr-only">Open main menu</span>
                        ☰
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="navbar-menu" class="hidden md:hidden bg-<?php echo isset($_SESSION['staff_id']) ? 'gray-100' : 'secondary'; ?> px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <?php if (!isset($_SESSION['staff_id'])): ?>
                <a href="index.php" class="text-white hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="events.php" class="text-white hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Events</a>
                <a href="demonstration.php" class="text-white hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Request Demo</a>
                <a href="feedback.php" class="text-white hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Feedback</a>
                <a href="faq.php" class="text-white hover:text-primary block px-3 py-2 rounded-md text-base font-medium">FAQ</a>
            <?php endif; ?>
            
                            <?php if (isset($_SESSION['staff_id'])): ?>
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                <a href="manageStaff.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Manage Staff</a>
                <a href="manageEvents.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Manage Events</a>
                <a href="manageFAQ.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Manage FAQ</a>
                <a href="dashboard.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                <a href="reports.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Reports</a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)): ?>
                <a href="manageDemos.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Manage Demos</a>
                <?php endif; ?>
                
                <a href="updateProfile.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Profile</a>
                <a href="logout.php" class="text-secondary hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="text-white hover:text-primary block px-3 py-2 rounded-md text-base font-medium">Log In</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="flex-grow">
        <?php echo $content; ?>
    </div>

    <!-- Footer -->
    <footer class="<?php echo isset($_SESSION['staff_id']) ? 'bg-gray-100' : 'bg-secondary text-white'; ?> mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-primary text-lg font-medium mb-4">About Us</h4>
                    <p class="<?php echo isset($_SESSION['staff_id']) ? 'text-gray-600' : 'text-gray-300'; ?> text-sm">We provide innovative AI solutions for businesses of all sizes, specializing in virtual assistants and personalized AI implementations.</p>
                </div>
                <div>
                    <h4 class="text-primary text-lg font-medium mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <?php if (!isset($_SESSION['staff_id'])): ?>
                            <li><a href="index.php" class="text-gray-300 hover:text-primary text-sm transition-colors">Home</a></li>
                            <li><a href="demonstration.php" class="text-gray-300 hover:text-primary text-sm transition-colors">Schedule a Demo</a></li>
                            <li><a href="events.php" class="text-gray-300 hover:text-primary text-sm transition-colors">Events</a></li>
                            <li><a href="faq.php" class="text-gray-300 hover:text-primary text-sm transition-colors">FAQ</a></li>
                        <?php else: ?>
                            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                            <li><a href="dashboard.php" class="text-gray-600 hover:text-primary text-sm transition-colors">Dashboard</a></li>
                            <li><a href="reports.php" class="text-gray-600 hover:text-primary text-sm transition-colors">Reports</a></li>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)): ?>
                            <li><a href="manageDemos.php" class="text-gray-600 hover:text-primary text-sm transition-colors">Manage Demos</a></li>
                            <?php endif; ?>
                            
                            <li><a href="updateProfile.php" class="text-gray-600 hover:text-primary text-sm transition-colors">Profile</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-primary text-lg font-medium mb-4">Contact</h4>
                    <p class="<?php echo isset($_SESSION['staff_id']) ? 'text-gray-600' : 'text-gray-300'; ?> text-sm mb-2">Email: info@ai-solutions.com</p>
                    <p class="<?php echo isset($_SESSION['staff_id']) ? 'text-gray-600' : 'text-gray-300'; ?> text-sm">Phone: (267) 333-5555</p>
                </div>
            </div>
            <div class="border-t <?php echo isset($_SESSION['staff_id']) ? 'border-gray-200' : 'border-gray-700'; ?> mt-8 pt-8 text-center">
                <p class="<?php echo isset($_SESSION['staff_id']) ? 'text-gray-600' : 'text-gray-300'; ?> text-sm">&copy; <?php echo date('Y'); ?> AI Solutions. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../assets/js/script.js"></script>
    <?php echo isset($additionalScripts) ? $additionalScripts : ''; ?>
</body>
</html>
