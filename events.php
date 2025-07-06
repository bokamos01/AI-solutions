<?php
/**
 * Events Page
 * Form for registering for events and displaying upcoming events
 */

// Start the session
session_start();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Get all upcoming events
$upcomingEvents = $da->GetUpcomingEvents();

// Create a lookup array for fast event title retrieval by ID
$eventTitles = [];
foreach ($upcomingEvents as $event) {
    $eventTitles[$event['EVENTID']] = $event['EVENTTITLE'];
}

// Handle form data and errors
$errors = $_SESSION['event_errors'] ?? [];
$formData = $_SESSION['event_form_data'] ?? [];

// Clear session variables after retrieving them
unset($_SESSION['event_errors']);
unset($_SESSION['event_form_data']);

// Find event title if form data contains event ID
$selectedEventId = $formData['event'] ?? '';
$selectedEventTitle = $eventTitles[$selectedEventId] ?? '';

// Set display state for registration form
$showRegistrationForm = !empty($errors);

// Set page title
$pageTitle = 'Events | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">Upcoming Events</h1>
    
    <p class="text-gray-600 text-center max-w-3xl mx-auto mb-10">
        Join us at our upcoming events to learn more about the latest AI innovations and how they can transform your business. 
        Attend live demonstrations, network with industry experts, and discover new opportunities for growth.
    </p>
    
    <?php if (empty($upcomingEvents)): ?>
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <p class="text-gray-600">
                There are no upcoming events scheduled at this time. Please check back later or 
                <a href="demonstration.php" class="text-primary hover:underline">schedule a personalized demo</a> instead.
            </p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="md:flex">
                        <div class="bg-primary text-white p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-center text-center md:text-left md:w-48 shrink-0">
                            <div>
                                <div class="text-xl uppercase font-medium"><?php echo date('M', strtotime($event['EVENTDATE'])); ?></div>
                                <div class="text-3xl font-bold"><?php echo date('d', strtotime($event['EVENTDATE'])); ?></div>
                            </div>
                        </div>
                        <div class="p-6 flex-1">
                            <h2 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($event['EVENTTITLE']); ?></h2>
                            <div class="flex flex-wrap gap-y-2 gap-x-6 text-gray-600 text-sm mb-4">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?php echo date('g:i A', strtotime($event['EVENTTIME'])); ?>
                                </div>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <?php echo htmlspecialchars($event['VANUE']); ?>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($event['EVENTDESCRIPTION']); ?></p>
                            <button onclick="registerForEvent('<?php echo $event['EVENTID']; ?>', '<?php echo htmlspecialchars(addslashes($event['EVENTTITLE'])); ?>')" 
                                class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-300">
                                Register Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div id="registrationFormContainer" class="<?php echo $showRegistrationForm ? '' : 'hidden'; ?> mt-12 bg-gray-50 rounded-lg p-6 md:p-8 shadow-md">
        <?php if (!empty($selectedEventTitle)): ?>
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Register for <?php echo htmlspecialchars($selectedEventTitle); ?></h2>
        <?php else: ?>
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Event Registration</h2>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li><strong>⚠️</strong> <?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
        <form id="eventRegistrationForm" action="eventRequest.php" method="POST" class="space-y-4">
            <input type="hidden" id="event" name="event" value="<?php echo htmlspecialchars($selectedEventId); ?>">
            <input type="hidden" id="event_title" name="event_title" value="<?php echo htmlspecialchars($selectedEventTitle); ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" 
                        value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required>
                </div>
                
                <div>
                    <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="lastname" name="lastname" 
                        value="<?php echo htmlspecialchars($formData['lastname'] ?? ''); ?>" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required>
                </div>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" 
                    value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                    required>
            </div>
            
            <div>
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
            
            <div>
                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                    Company Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="company" name="company" 
                    value="<?php echo htmlspecialchars($formData['company'] ?? ''); ?>" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                    required>
            </div>
            
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                    Country <span class="text-red-500">*</span>
                </label>
                <select id="country" name="country" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                    required>
                    <?php echo $da->PopulateCountryDropdown($formData['country'] ?? null); ?>
                </select>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4">
                <button type="button" id="cancelRegistration" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded transition duration-300">
                    Cancel
                </button>
                <button type="submit" 
                    class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-300">
                    Register Now
                </button>
            </div>
            
            <div class="text-sm text-gray-500 mt-4 text-center">
                <p>By registering, you agree to our privacy policy and terms of service.</p>
            </div>
        </form>
    </div>
</div>

<script>
function registerForEvent(eventId, eventTitle) {
    document.getElementById('event').value = eventId;
    document.getElementById('event_title').value = eventTitle;
    
    // Show the registration form
    document.getElementById('registrationFormContainer').classList.remove('hidden');
    
    // Update the form title
    const formTitle = document.querySelector('#registrationFormContainer h2');
    formTitle.textContent = 'Register for ' + eventTitle;
    
    // Scroll to the form
    document.getElementById('registrationFormContainer').scrollIntoView({
        behavior: 'smooth'
    });
}

document.getElementById('cancelRegistration').addEventListener('click', function() {
    document.getElementById('registrationFormContainer').classList.add('hidden');
    document.getElementById('eventRegistrationForm').reset();
});
</script>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
