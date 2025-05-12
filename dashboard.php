<?php

// Dashboard Page


// Start the session
session_start();

require_once 'securityCheck.php';

// Verify that the user is logged in
verifyLogin();

// Prevent demonstrators from accessing dashboard
if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
    header("Location: manageDemos.php");
    exit;
}

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Get counts for dashboard
$stats = [
    'pending_demos' => 0,
    'upcoming_events' => 0,
    'total_registrations' => 0,
    'recent_feedback' => 0
];

try {
    // Get count of pending demonstrations
    $pendingDemos = $da->GetData("SELECT COUNT(*) as COUNT FROM tbl_demonstration WHERE DEMOSTATE = '0'");
    $stats['pending_demos'] = $pendingDemos[0]['COUNT'];
    
    // Get count of upcoming events
    $upcomingEvents = $da->GetData("SELECT COUNT(*) as COUNT FROM tbl_events WHERE EVENTDATE >= CURDATE()");
    $stats['upcoming_events'] = $upcomingEvents[0]['COUNT'];
    
    // Get total event registrations
    $totalRegistrations = $da->GetData("SELECT COUNT(*) as COUNT FROM tbl_eventregistry");
    $stats['total_registrations'] = $totalRegistrations[0]['COUNT'];
    
    // Get recent feedback count (last 30 days)
    $recentFeedback = $da->GetData("SELECT COUNT(*) as COUNT FROM tbl_feedback WHERE FEEDBACKID > (SELECT MAX(FEEDBACKID) - 10 FROM tbl_feedback)");
    $stats['recent_feedback'] = $recentFeedback[0]['COUNT'];
    
    // Get recent demonstrations
    $recentDemos = $da->GetData("SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.COMPANYNAME, 
                                 CASE a.DEMOSTATE WHEN '0' THEN 'Pending' WHEN '1' THEN 'Assigned' WHEN '2' THEN 'Completed' END AS STATUS
                                 FROM tbl_demonstration a
                                 ORDER BY a.DEMONSTRATIONID DESC LIMIT 5");
    
    // Get upcoming events
    $upcomingEventsList = $da->GetUpcomingEvents();
    if (count($upcomingEventsList) > 2) {
        $upcomingEventsList = array_slice($upcomingEventsList, 0, 2);
    }
    
} catch (Exception $ex) {
    // Handle errors silently in this case
    $error = $ex->getMessage();
}

// Set page title
$pageTitle = 'Admin Dashboard | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
        <p class="text-lg text-gray-600">Welcome back, <?php echo htmlspecialchars($_SESSION['staff_name']); ?>!</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Demos Card -->
        <div class="bg-white rounded-lg shadow-md p-6 flex items-center relative">
            <div class="text-3xl text-red-500 mr-4">📋</div>
            <div>
                <div class="text-2xl font-bold text-gray-800"><?php echo $stats['pending_demos']; ?></div>
                <div class="text-sm text-gray-600">Pending Demos</div>
            </div>
            <a href="manageDemos.php" class="absolute right-4 bottom-4 text-sm text-primary hover:underline">View All</a>
        </div>
        
        <!-- Upcoming Events Card -->
        <div class="bg-white rounded-lg shadow-md p-6 flex items-center relative">
            <div class="text-3xl text-primary mr-4">🗓️</div>
            <div>
                <div class="text-2xl font-bold text-gray-800"><?php echo $stats['upcoming_events']; ?></div>
                <div class="text-sm text-gray-600">Upcoming Events</div>
            </div>
            <a href="manageEvents.php" class="absolute right-2 bottom-2 text-sm text-primary hover:underline">View All</a>
        </div>
        
        <!-- Registrations Card -->
        <div class="bg-white rounded-lg shadow-md p-6 flex items-center relative">
            <div class="text-3xl text-green-500 mr-4">👥</div>
            <div>
                <div class="text-2xl font-bold text-gray-800"><?php echo $stats['total_registrations']; ?></div>
                <div class="text-sm text-gray-600">Event Registrations</div>
            </div>
            <a href="reportViewer.php?reportId=3" class="absolute right-4 bottom-4 text-sm text-primary hover:underline">View Reg</a>
        </div>
        
        <!-- Feedback Card -->
        <div class="bg-white rounded-lg shadow-md p-6 flex items-center relative">
            <div class="text-3xl text-yellow-500 mr-4">💬</div>
            <div>
                <div class="text-2xl font-bold text-gray-800"><?php echo $stats['recent_feedback']; ?></div>
                <div class="text-sm text-gray-600">Recent Feedback</div>
            </div>
            <a href="manageFeedback.php" class="absolute right-4 bottom-4 text-sm text-primary hover:underline">View Details</a>
        </div>
    </div>
    
    <!-- Two Column Layout for Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Demonstration Requests -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Recent Demonstration Requests</h2>
            
            <?php if (empty($recentDemos)): ?>
                <p class="text-gray-600 italic">No recent demonstration requests.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Company</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDemos as $demo): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3"><?php echo $demo['DEMONSTRATIONID']; ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($demo['FIRSTNAME'] . ' ' . $demo['LASTNAME']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($demo['COMPANYNAME']); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if ($demo['STATUS'] === 'Pending'): ?>
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                                Pending
                                            </span>
                                        <?php elseif ($demo['STATUS'] === 'Assigned'): ?>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                Assigned
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                Completed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="manageDemos.php" class="text-primary hover:underline">Manage</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Upcoming Events -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Upcoming Events</h2>
            
            <?php if (empty($upcomingEventsList)): ?>
                <p class="text-gray-600 italic">No upcoming events.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($upcomingEventsList as $event): ?>
                        <div class="flex bg-gray-50 rounded-lg overflow-hidden">
                            <div class="flex flex-col justify-center items-center p-4 bg-primary text-white min-w-[80px]">
                                <span class="text-sm uppercase"><?php echo date('M', strtotime($event['EVENTDATE'])); ?></span>
                                <span class="text-2xl font-bold"><?php echo date('d', strtotime($event['EVENTDATE'])); ?></span>
                            </div>
                            <div class="p-4 flex-1">
                                <h3 class="font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($event['EVENTTITLE']); ?></h3>
                                <div class="mb-2 text-sm text-gray-600">
                                    <div class="flex items-center mb-1">
                                        <span class="mr-1">🕒</span> <?php echo date('g:i A', strtotime($event['EVENTTIME'])); ?>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="mr-1">📍</span> <?php echo htmlspecialchars($event['VANUE']); ?>
                                    </div>
                                </div>
                                <a href="manageEvents.php" class="text-primary hover:underline text-sm">Manage Event</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <a href="manageEvents.php?action=add" class="flex flex-col items-center p-4 bg-gray-100 rounded-lg hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl mb-2">➕</span>
                <span class="font-medium text-gray-800">Add New Event</span>
            </a>
            
            <a href="manageStaff.php?action=add" class="flex flex-col items-center p-4 bg-gray-100 rounded-lg hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl mb-2">👤</span>
                <span class="font-medium text-gray-800">Add Staff Member</span>
            </a>
            
            <a href="reports.php" class="flex flex-col items-center p-4 bg-gray-100 rounded-lg hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl mb-2">📊</span>
                <span class="font-medium text-gray-800">View Reports</span>
            </a>
            
            <a href="updateProfile.php" class="flex flex-col items-center p-4 bg-gray-100 rounded-lg hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl mb-2">⚙️</span>
                <span class="font-medium text-gray-800">Update Profile</span>
            </a>
        </div>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
