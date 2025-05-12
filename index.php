<?php

// Start the session
session_start();

// Redirect logged-in users to dashboard
if (isset($_SESSION['staff_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Set page title
$pageTitle = 'AI Solutions | Advanced AI Services for Your Business';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="w-full md:w-1/2 space-y-6">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 leading-tight">
                Rapidly and proactively address employee experience issues with our AI-solutions
            </h1>
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <a href="events.php" class="bg-secondary hover:bg-gray-800 text-white font-medium py-3 px-6 rounded-md transition duration-300 text-center">Join event</a>
                <a href="demonstration.php" class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-md transition duration-300 text-center">Schedule demo</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 mt-8 md:mt-0">
            <img src="assets/img/ai-illustration.png" alt="AI Technology Illustration" class="w-full h-auto object-contain max-w-lg mx-auto">
        </div>
    </div>
<section class="mt-16 mb-12">
            <h3 class="<?php echo $largeText ? 'text-2xl md:text-3xl' : 'text-xl md:text-2xl'; ?> font-bold mb-6 <?php echo $highContrast ? 'text-yellow-400' : 'text-secondary'; ?>">
                How Our AI Solutions Work
            </h3>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 rounded-lg <?php echo $highContrast ? 'bg-gray-900' : 'bg-gray-50'; ?> shadow-sm">
                    <h4 class="<?php echo $largeText ? 'text-xl' : 'text-lg'; ?> font-semibold mb-3 <?php echo $highContrast ? 'text-yellow-300' : 'text-primary'; ?>">Data Collection</h4>
                    <p class="<?php echo $textSizeClass; ?>">Our platform collects and analyzes employee feedback and workplace metrics in real-time.</p>
                </div>
                <div class="p-6 rounded-lg <?php echo $highContrast ? 'bg-gray-900' : 'bg-gray-50'; ?> shadow-sm">
                    <h4 class="<?php echo $largeText ? 'text-xl' : 'text-lg'; ?> font-semibold mb-3 <?php echo $highContrast ? 'text-yellow-300' : 'text-primary'; ?>">AI Analysis</h4>
                    <p class="<?php echo $textSizeClass; ?>">Advanced algorithms identify patterns and potential issues before they impact productivity.</p>
                </div>
                <div class="p-6 rounded-lg <?php echo $highContrast ? 'bg-gray-900' : 'bg-gray-50'; ?> shadow-sm">
                    <h4 class="<?php echo $largeText ? 'text-xl' : 'text-lg'; ?> font-semibold mb-3 <?php echo $highContrast ? 'text-yellow-300' : 'text-primary'; ?>">Actionable Solutions</h4>
                    <p class="<?php echo $textSizeClass; ?>">Receive customized recommendations and strategies to improve employee experience.</p>
                </div>
            </div>
</section>
</div>
<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
