<?php
/**
 * Manage Events Page
 * Allows admin users to create, edit, and delete events
 */

// Start the session
session_start();

// Include security check script
require_once 'securityCheck.php';

// Verify that the user is an admin
verifyAdmin();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Get the next event ID
function getNextEventId($da)
{
    // Get all existing event IDs
    $sql = "SELECT EVENTID FROM tbl_events ORDER BY CAST(EVENTID AS UNSIGNED)";
    $result = $da->GetData($sql);
    
    // Start with ID 1
    $next_id = 1;
    
    // Find the first gap in the sequence
    if (count($result) > 0) {
        $existing_ids = array_map('intval', array_column($result, 'EVENTID'));
        
        // Loop through possible IDs until we find a gap
        while (in_array($next_id, $existing_ids)) {
            $next_id++;
        }
    }
    
    return $next_id;
}

// Default: don't load any events unless requested
$events = [];

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new event
    if (isset($_POST['add_event'])) {
        $title = trim($_POST['event_title'] ?? '');
        $description = trim($_POST['event_description'] ?? '');
        $venue = trim($_POST['event_venue'] ?? '');
        $date = trim($_POST['event_date'] ?? '');
        $time = trim($_POST['event_time'] ?? '');
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = "Event title is required";
        }
        
        if (empty($description)) {
            $errors[] = "Event description is required";
        }
        
        if (empty($venue)) {
            $errors[] = "Event venue is required";
        }
        
        if (empty($date)) {
            $errors[] = "Event date is required";
        }
        
        if (empty($time)) {
            $errors[] = "Event time is required";
        }
        
        if (empty($errors)) {
            try {
                // Get the next available event ID
                $eventId = getNextEventId($da);
                
                // Use the updated AddEvent method from DataAccess
                $result = $da->AddEvent($eventId, $title, $description, $venue, $date, $time);
                
                if ($result > 0) {
                    $_SESSION['event_message'] = "Event successfully added.";
                } else {
                    $_SESSION['event_error'] = "Failed to add event.";
                }
            } catch (Exception $ex) {
                $_SESSION['event_error'] = "Error: " . $ex->getMessage();
            }
        } else {
            $_SESSION['event_error'] = implode("<br>", $errors);
        }
    }
    
    // Edit event
    elseif (isset($_POST['edit_event'])) {
        $eventId = intval($_POST['event_id'] ?? 0);
        $title = trim($_POST['event_title'] ?? '');
        $description = trim($_POST['event_description'] ?? '');
        $venue = trim($_POST['event_venue'] ?? '');
        $date = trim($_POST['event_date'] ?? '');
        $time = trim($_POST['event_time'] ?? '');
        
        $errors = [];
        
        if ($eventId <= 0) {
            $errors[] = "Invalid event ID";
        }
        
        if (empty($title)) {
            $errors[] = "Event title is required";
        }
        
        if (empty($description)) {
            $errors[] = "Event description is required";
        }
        
        if (empty($venue)) {
            $errors[] = "Event venue is required";
        }
        
        if (empty($date)) {
            $errors[] = "Event date is required";
        }
        
        if (empty($time)) {
            $errors[] = "Event time is required";
        }
        
        if (empty($errors)) {
            try {
                $result = $da->UpdateEvent($eventId, $title, $description, $venue, $date, $time);
                if ($result > 0) {
                    $_SESSION['event_message'] = "Event successfully updated.";
                } else {
                    $_SESSION['event_error'] = "Failed to update event. No changes were made or event not found.";
                }
            } catch (Exception $ex) {
                $_SESSION['event_error'] = "Error: " . $ex->getMessage();
            }
        } else {
            $_SESSION['event_error'] = implode("<br>", $errors);
        }
    }
    
    // Delete event
    elseif (isset($_POST['delete_event'])) {
        $eventId = intval($_POST['event_id'] ?? 0);
        
        if ($eventId > 0) {
            try {
                // First delete any registrations for this event
                $da->ExecuteCommand("DELETE FROM tbl_eventregistry WHERE EVENTID = ?", [$eventId]);
                
                // Then delete the event itself
                $result = $da->DeleteEvent($eventId);
                if ($result > 0) {
                    $_SESSION['event_message'] = "Event successfully deleted.";
                } else {
                    $_SESSION['event_error'] = "Failed to delete event. Event not found.";
                }
            } catch (Exception $ex) {
                $_SESSION['event_error'] = "Error: " . $ex->getMessage();
            }
        } else {
            $_SESSION['event_error'] = "Invalid event ID";
        }
    }
    
    // Search for events
    elseif (isset($_POST['btnsearch']) && !empty($_POST['search_term'])) {
        $searchTerm = trim($_POST['search_term']);
        $events = $da->GetData(
            "SELECT * FROM tbl_events WHERE EVENTTITTLE LIKE ? OR VANUE LIKE ? OR EVENTDESCRIPTION LIKE ?",
            ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]
        );
    }
    // List all events
    elseif (isset($_POST['btnlistall'])) {
        $events = $da->GetAllEvents();
    }
} else {
    // Default: don't load any events on initial page load
    $events = [];
}

// Check if we need to edit a specific event
$eventDetails = null;
if (isset($_GET['id'])) {
    $eventId = $_GET['id'];
    $result = $da->GetData("SELECT * FROM tbl_events WHERE EVENTID = ?", [$eventId]);
    if (count($result) > 0) {
        $eventDetails = $result[0];
    }
}

// Set page title
$pageTitle = 'Manage Events | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Events</h1>
    
    <?php if (isset($_SESSION['event_message'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <?php 
                            echo $_SESSION['event_message'];
                            unset($_SESSION['event_message']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['event_error'])): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <?php 
                            echo $_SESSION['event_error'];
                            unset($_SESSION['event_error']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Side: Search and Event List -->
        <div class="lg:w-2/3">
            <!-- Search Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <fieldset class="border border-gray-200 rounded-md p-4">
                    <legend class="text-lg font-medium text-gray-700 px-2">Search for events</legend>
                    <form method="POST">
                        <div class="space-y-4">
                            <input type="text" name="search_term" id="search_term" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Search by title, venue or description">
                            <div class="flex justify-end space-x-3">
                                <button type="submit" name="btnsearch" 
                                        class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-md transition duration-300">
                                    Search
                                </button>
                                <button type="submit" name="btnlistall" 
                                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md transition duration-300">
                                    List All
                                </button>
                            </div>
                        </div>
                    </form>
                </fieldset>
            </div>

            <!-- Event List -->
            <?php
            try {
                // Only list events if search or list all was requested
                if (isset($_POST['btnlistall']) || isset($_POST['btnsearch'])) {
                    // Only display events if we have any to show
                    if (empty($events)) {
                        if (isset($_POST['btnsearch'])) {
                            echo "<div class='bg-red-50 text-red-700 p-4 rounded-md mb-6'>No events found matching your search.</div>";
                        } else {
                            echo "<div class='bg-red-50 text-red-700 p-4 rounded-md mb-6'>No events found.</div>";
                        }
                    } else {
                        // Check if we need to apply scrolling (if more than 5 events)
                        $scrollClass = (count($events) > 5) ? 'max-h-80 overflow-y-auto' : '';
                        ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                            <div class="hidden md:flex bg-gray-100 text-gray-700 font-medium sticky top-0 z-10">
                                <div class="w-16 px-4 py-3">ID</div>
                                <div class="flex-1 px-4 py-3">Title</div>
                                <div class="w-24 px-4 py-3">Date</div>
                                <div class="w-24 px-4 py-3">Time</div>
                                <div class="w-32 px-4 py-3">Venue</div>
                                <div class="w-44 px-4 py-3 text-right">Actions</div>
                            </div>
                            
                            <div class="<?php echo $scrollClass; ?>">
                                <?php foreach ($events as $event): ?>
                                <div class="border-t border-gray-200 flex flex-col md:flex-row hover:bg-gray-50">
                                    <div class="w-full md:w-16 px-4 py-3 font-medium text-gray-900 md:border-0 border-b border-gray-200 bg-gray-50 md:bg-transparent">
                                        <span class="md:hidden font-medium text-gray-500">ID: </span>
                                        <?php echo htmlspecialchars($event['EVENTID']); ?>
                                    </div>
                                    <div class="w-full md:flex-1 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Title: </span>
                                        <?php echo htmlspecialchars($event['EVENTTITLE']); ?>
                                    </div>
                                    <div class="w-full md:w-24 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Date: </span>
                                        <?php echo date('Y-m-d', strtotime($event['EVENTDATE'])); ?>
                                    </div>
                                    <div class="w-full md:w-24 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Time: </span>
                                        <?php echo date('H:i', strtotime($event['EVENTTIME'])); ?>
                                    </div>
                                    <div class="w-full md:w-32 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Venue: </span>
                                        <?php echo htmlspecialchars($event['VANUE']); ?>
                                    </div>
                                    <div class="w-full md:w-44 px-4 py-3 space-y-2 md:space-y-0 md:flex items-center justify-end gap-2">
                                        <button type="button" class="edit-btn w-full md:w-auto bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300" data-id="<?php echo htmlspecialchars($event['EVENTID']); ?>">Edit</button>
                                        
                                        <form method="POST" id="delete-event-<?php echo $event['EVENTID']; ?>" class="w-full md:w-auto">
                                            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['EVENTID']); ?>">
                                            <button type="button" onclick="confirmDeleteEvent('delete-event-<?php echo $event['EVENTID']; ?>')" class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300">Delete</button>
                                            <input type="hidden" name="delete_event" value="1">
                                        </form>
                                        
                                        <a href="eventRegistrations.php?id=<?php echo htmlspecialchars($event['EVENTID']); ?>" class="block w-full md:w-auto text-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300">Registrations</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // Display initial message when no search has been performed
                    echo "<div class='bg-gray-100 text-gray-700 p-6 rounded-md text-center mb-6'>
                            <p>Use the search box above to find events or click \"List All\" to view all events.</p>
                          </div>";
                }
            } catch (Exception $ex) {
                $msg = $ex->getMessage();
                echo "<div class='bg-red-50 text-red-700 p-4 rounded-md mb-6'>$msg</div>";
            }
            ?>
        </div>
        
        <!-- Right Side: Add/Edit Event Form -->
        <div class="lg:w-1/3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4" id="event-form-title">
                <?php echo isset($eventDetails) ? 'Edit Event' : 'Add New Event'; ?>
            </h2>
            <form id="eventForm" method="POST">
                <input type="hidden" id="event_id" name="event_id" value="<?php echo htmlspecialchars($eventDetails['EVENTID'] ?? ''); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="event_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Event Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="event_title" name="event_title" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo htmlspecialchars($eventDetails['EVENTTITTLE'] ?? ''); ?>">
                    </div>
                    
                    <div>
                        <label for="event_venue" class="block text-sm font-medium text-gray-700 mb-1">
                            Venue <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="event_venue" name="event_venue" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo htmlspecialchars($eventDetails['VANUE'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="event_date" name="event_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo isset($eventDetails) ? date('Y-m-d', strtotime($eventDetails['EVENTDATE'])) : ''; ?>">
                    </div>
                    
                    <div>
                        <label for="event_time" class="block text-sm font-medium text-gray-700 mb-1">
                            Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="event_time" name="event_time" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo isset($eventDetails) ? date('H:i', strtotime($eventDetails['EVENTTIME'])) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="event_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="event_description" name="event_description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary resize-y"><?php echo htmlspecialchars($eventDetails['EVENTDESCRIPTION'] ?? ''); ?></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="reset-form" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-300">
                        Reset
                    </button>
                    <?php if (isset($eventDetails)): ?>
                        <button type="submit" id="edit-event-btn" name="edit_event" 
                            class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                            Update Event
                        </button>
                    <?php else: ?>
                        <button type="submit" id="add-event-btn" name="add_event" 
                            class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                            Add Event
                        </button>
                    <?php endif; ?>
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
