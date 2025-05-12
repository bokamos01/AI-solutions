<?php
/**
 * Reports.php - Admin reports page
 * Displays available reports for administrators
 */

// Start the session
session_start();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Check if user is logged in and is an admin
if (!$da->isLoggedIn() || !$da->isAdmin()) {
    // Not logged in or not an admin, redirect to login
    header("Location: login.php");
    exit;
}

// Set page title
$pageTitle = 'Reports | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reports</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        
        <div class="p-6">
            <?php
            // Get all reports from database
            $reports = $da->GetAllReports();
            
            if (empty($reports)): 
            ?>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                    <p>No reports are available. Please run the initialization script.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    // Define report icons based on category
                    $categoryIcons = [
                        'Staff' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
                        'Events' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
                        'Feedback' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>',
                        'Demonstrations' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>'
                    ];
                    
                    $defaultIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>';
                    
                    foreach ($reports as $report): 
                        $icon = $categoryIcons[$report['CATEGORY']] ?? $defaultIcon;
                    ?>
                        <a href="reportViewer.php?reportId=<?php echo $report['REPORTID']; ?>" 
                           class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <div class="flex items-start">
                                <div class="flex-shrink-0"><?php echo $icon; ?></div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($report['REPORTNAME']); ?></h3>
                                    <p class="text-gray-500 mt-1"><?php echo htmlspecialchars($report['CATEGORY']); ?></p>
                                    <?php if (!empty($report['PREVIEW'])): ?>
                                        <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($report['PREVIEW']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
