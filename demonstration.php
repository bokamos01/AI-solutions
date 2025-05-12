<?php
/**
 * Demonstration Request Page
 * Form for scheduling a product demonstration
 */

// Start the session
session_start();

// Set the page title
$pageTitle = 'Schedule a Demo';

// Include the DataAccess class
require_once 'DataAccess.php';
$db = new DataAccess();

// Start capturing the content
ob_start();
?>

<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Schedule a Demo</h1>
    
    <div class="relative mb-8 rounded-lg overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/70 to-indigo-600/70 z-10"></div>
        <div class="bg-cover bg-center h-64" style="background-image: url('assets/img/eventPreview.jpg')"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center z-20 p-6 text-white">
            <h3 class="text-2xl font-semibold mb-2">Join Our Events</h3>
            <p class="text-lg mb-6 text-center">Stay updated with our latest AI innovations and industry events</p>
            <button class="bg-white hover:bg-gray-100 text-primary font-medium py-2 px-6 rounded-md transition duration-300">
                View Upcoming Events
            </button>
        </div>
    </div>
    
    <?php
    // Display error messages if any
    if (isset($_SESSION['demo_errors']) && !empty($_SESSION['demo_errors'])) {
        echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">';
        echo '<div class="text-red-700">';
        echo '<ul class="list-disc pl-5 space-y-1">';
        foreach ($_SESSION['demo_errors'] as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        
        // Clear the errors
        unset($_SESSION['demo_errors']);
    }
    
    // Get stored form data for repopulation
    $formData = $_SESSION['demo_form_data'] ?? [];
    unset($_SESSION['demo_form_data']);
    ?>
    
    <form id="demoForm" action="demonstrationRequest.php" method="post" class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div class="form-group">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="lastname" name="lastname" 
                       value="<?php echo htmlspecialchars($formData['lastname'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                       required>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address <span class="text-red-500">*</span>
            </label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                   required>
        </div>
        
        <div class="mt-4">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number
            </label>
            <input type="tel" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        
        <div class="mt-4">
            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                Company Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="company" name="company" 
                   value="<?php echo htmlspecialchars($formData['company'] ?? ''); ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                   required>
        </div>
        
        <div class="mt-4">
            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                Country <span class="text-red-500">*</span>
            </label>
            <select id="country" name="country" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                    required>
                <?php 
                $selectedCountry = $formData['country'] ?? '';
                echo $db->PopulateCountryDropdown($selectedCountry); 
                ?>
            </select>
        </div>
        
        <div class="mt-4">
            <label for="interests" class="block text-sm font-medium text-gray-700 mb-1">
                What are you interested in? <span class="text-red-500">*</span>
            </label>
            <select id="interests" name="interests" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                    required>
                <option value="" <?php echo empty($formData['interests']) ? 'selected' : ''; ?>>Select an option</option>
                <option value="virtual-assistant" <?php echo ($formData['interests'] ?? '') === 'virtual-assistant' ? 'selected' : ''; ?>>AI-Powered Virtual Assistant</option>
                <option value="custom-solution" <?php echo ($formData['interests'] ?? '') === 'custom-solution' ? 'selected' : ''; ?>>Personalized Solution Demo</option>
                <option value="both" <?php echo ($formData['interests'] ?? '') === 'both' ? 'selected' : ''; ?>>Both</option>
            </select>
        </div>
        
        <div class="mt-4">
            <label for="additional" class="block text-sm font-medium text-gray-700 mb-1">
                Additional Information
            </label>
            <textarea id="additional" name="additional" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary h-32 resize-y" 
                      placeholder="Tell us more about your specific needs or questions..."><?php echo htmlspecialchars($formData['additional'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" class="w-full mt-6 bg-primary hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-md transition duration-300">
            Schedule My Demo
        </button>
        
        <div class="mt-4 text-sm text-gray-500 text-center">
            <p>By submitting this form, you agree to our privacy policy and terms of service.</p>
        </div>
    </form>
</div>

<?php
// Get the captured content and store it in the $content variable
$content = ob_get_clean();

// Include the master template file
require_once 'master.php';
?>
