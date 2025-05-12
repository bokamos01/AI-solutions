<?php
/**
 * FAQ Page
 * Displays frequently asked questions
 */

// Start the session
session_start();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Get all FAQs
$faqs = $da->GetAllFAQs();

// Set page title
$pageTitle = 'Frequently Asked Questions | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Frequently Asked Questions</h1>
    
    <div class="space-y-4 mb-12">
        <?php foreach ($faqs as $faq): ?>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button class="faq-question w-full flex justify-between items-center p-4 bg-white hover:bg-gray-50 transition-colors duration-200 focus:outline-none text-left" onclick="toggleAnswer(this)">
                    <span class="font-medium text-gray-800"><?php echo htmlspecialchars($faq['FAQUESTION']); ?></span>
                    <span class="toggle-icon text-primary font-bold text-xl transition-transform duration-200">+</span>
                </button>
                <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-4 bg-gray-50 text-gray-700">
                        <?php echo nl2br(htmlspecialchars($faq['FAQANSWER'])); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="bg-gray-100 rounded-lg p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Still have questions?</h2>
        <p class="text-gray-600 mb-6">Our team is here to help. Contact us or schedule a demo to learn more about our AI solutions.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="feedback.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-md transition duration-300">Contact Us</a>
            <a href="demonstration.php" class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-md transition duration-300">Schedule a Demo</a>
        </div>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
