<?php
/**
 * Feedback Page
 * Form for submitting feedback
 */

// Start the session
session_start();

// Handle form data and errors
$errors = $_SESSION['feedback_errors'] ?? [];
$formData = $_SESSION['feedback_form_data'] ?? [];

// Clear session variables
unset($_SESSION['feedback_errors']);
unset($_SESSION['feedback_form_data']);

// Set page title
$pageTitle = 'Provide Feedback | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">Your Feedback Matters</h1>
    
    <p class="text-gray-600 text-center mb-8">
        We value your thoughts and suggestions. Please share your feedback with us to help improve our products and services.
    </p>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    
    <form id="feedbackForm" action="feedbackProcess.php" method="POST" class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="firstName" name="firstName" 
                       value="<?php echo htmlspecialchars($formData['firstName'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                       required>
            </div>
            
            <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="lastName" name="lastName" 
                       value="<?php echo htmlspecialchars($formData['lastName'] ?? ''); ?>" 
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
	    <input type="text" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                   pattern="[0-9]*" 
                   inputmode="numeric"
                   title="Please enter numbers only"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                   </div>
        
        <div class="mt-4">
            <label for="feedbackType" class="block text-sm font-medium text-gray-700 mb-1">
                Feedback Type <span class="text-red-500">*</span>
            </label>
            <select id="feedbackType" name="feedbackType" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                    required>
                <option value="">Select feedback type</option>
                <option value="general" <?php echo (isset($formData['feedbackType']) && $formData['feedbackType'] == 'general') ? 'selected' : ''; ?>>General Feedback</option>
                <option value="product" <?php echo (isset($formData['feedbackType']) && $formData['feedbackType'] == 'product') ? 'selected' : ''; ?>>Product Suggestion</option>
                <option value="support" <?php echo (isset($formData['feedbackType']) && $formData['feedbackType'] == 'support') ? 'selected' : ''; ?>>Support Experience</option>
                <option value="demo" <?php echo (isset($formData['feedbackType']) && $formData['feedbackType'] == 'demo') ? 'selected' : ''; ?>>Demo Experience</option>
                <option value="event" <?php echo (isset($formData['feedbackType']) && $formData['feedbackType'] == 'event') ? 'selected' : ''; ?>>Event Feedback</option>
                <option value="other" <?php echo (isset($formData['feedbackType']) && $formData['feedbackType'] == 'other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        
        <div class="mt-4">
            <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">
                Your Feedback <span class="text-red-500">*</span>
            </label>
            <textarea id="feedback" name="feedback" rows="6" required 
                      placeholder="Please share your thoughts, suggestions, or experiences..." 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($formData['feedback'] ?? ''); ?></textarea>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Rate Your Experience
            </label>
            <div class="flex items-center">
                <div class="flex flex-row-reverse">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <div class="flex flex-col items-center mx-1">
                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                               <?php echo (isset($formData['rating']) && $formData['rating'] == $i) ? 'checked' : ''; ?>
                               class="hidden peer">
                        <label for="star<?php echo $i; ?>" 
                               class="cursor-pointer text-2xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400">★</label>
                    </div>
                    <?php endfor; ?>
                </div>
                <span class="ml-4 text-sm text-gray-600">Select a rating (optional)</span>
            </div>
        </div>
        
        <button type="submit" class="w-full mt-6 bg-primary hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-md transition duration-300">
            Submit Feedback
        </button>
        
        <div class="mt-4 text-sm text-gray-500 text-center">
            <p>Your feedback will be reviewed by our team and may be used to improve our services.</p>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const feedbackForm = document.getElementById('feedbackForm');
    
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(event) {
            // Basic form validation
            const requiredFields = feedbackForm.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500', 'ring-2', 'ring-red-500');
                } else {
                    field.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    }
    
    // Handle star rating hover effect
    const starLabels = document.querySelectorAll('label[for^="star"]');
    
    starLabels.forEach(label => {
        label.addEventListener('mouseover', function() {
            const currentStar = this;
            const starId = parseInt(this.getAttribute('for').replace('star', ''));
            
            starLabels.forEach(star => {
                const thisStar = parseInt(star.getAttribute('for').replace('star', ''));
                if (thisStar <= starId) {
                    star.classList.add('text-yellow-400');
                    star.classList.remove('text-gray-300');
                } else {
                    star.classList.add('text-gray-300');
                    star.classList.remove('text-yellow-400');
                }
            });
        });
    });
    
    const ratingContainer = document.querySelector('.flex.flex-row-reverse');
    
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseout', function() {
            const checkedRating = document.querySelector('input[name="rating"]:checked');
            
            starLabels.forEach(star => {
                if (checkedRating) {
                    const starId = parseInt(star.getAttribute('for').replace('star', ''));
                    const ratingValue = parseInt(checkedRating.value);
                    
                    if (starId <= ratingValue) {
                        star.classList.add('text-yellow-400');
                        star.classList.remove('text-gray-300');
                    } else {
                        star.classList.add('text-gray-300');
                        star.classList.remove('text-yellow-400');
                    }
                } else {
                    star.classList.add('text-gray-300');
                    star.classList.remove('text-yellow-400');
                }
            });
        });
    }
});
</script>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
