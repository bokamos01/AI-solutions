<?php
/**
 * Manage Demonstrations Page
 * Allows admin users to view, assign, and manage demonstration requests
 */

// Start the session
session_start();

// Include security check script
require_once 'securityCheck.php';

// Verify that the user is an admin or demonstrator
verifyStaffAccess();

// Include the data access layer
require_once 'DataAccess.php';

// Create instance of DataAccess class
$da = new DataAccess();

// Check user role
$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
$isDemonstrator = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2;
$staffId = $_SESSION['staff_id'] ?? 0;

// Get all demonstrations
$demos = [];

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assign demonstrator (admin only)
    if (isset($_POST['assign_demo']) && $isAdmin) {
        $demoId = intval($_POST['demo_id'] ?? 0);
        $staffId = intval($_POST['staff_id'] ?? 0);
        
        $errors = [];
        
        if ($demoId <= 0) {
            $errors[] = "Invalid demonstration ID";
        }
        
        if ($staffId <= 0) {
            $errors[] = "Please select a demonstrator";
        }
        
        if (empty($errors)) {
            try {
                $result = $da->AssignDemonstration($demoId, $staffId);
                if ($result > 0) {
                    $_SESSION['demo_message'] = "Demonstration successfully assigned.";
                } else {
                    $_SESSION['demo_error'] = "Failed to assign demonstration.";
                }
            } catch (Exception $ex) {
                $_SESSION['demo_error'] = "Error: " . $ex->getMessage();
            }
        } else {
            $_SESSION['demo_error'] = implode("<br>", $errors);
        }
    }
    
    // Mark as complete (both admin and demonstrator)
    elseif (isset($_POST['complete_demo'])) {
        $demoId = intval($_POST['demo_id'] ?? 0);
        
        // For demonstrators, verify they are assigned to this demo
        if ($isDemonstrator) {
            $checkAssigned = $da->GetData(
                "SELECT COUNT(*) as count FROM tbl_demonstration WHERE DEMONSTRATIONID = ? AND STAFFID = ? AND DEMOSTATE = '1'",
                [$demoId, $staffId]
            );
            
            if ($checkAssigned[0]['count'] == 0) {
                $_SESSION['demo_error'] = "You are not authorized to complete this demonstration.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        }
        
        if ($demoId > 0) {
            try {
                $result = $da->CompleteDemonstration($demoId);
                if ($result > 0) {
                    $_SESSION['demo_message'] = "Demonstration marked as completed.";
                    // Add redirect to force list refresh
                    header("Location: " . $_SERVER['PHP_SELF'] . "?action=listall");
                    exit;
                } else {
                    $_SESSION['demo_error'] = "Failed to update demonstration status.";
                }
            } catch (Exception $ex) {
                $_SESSION['demo_error'] = "Error: " . $ex->getMessage();
            }
        } else {
            $_SESSION['demo_error'] = "Invalid demonstration ID";
        }
    }
    
    // Search for demos
    elseif (isset($_POST['btnsearch']) && !empty($_POST['search_term'])) {
        $searchTerm = trim($_POST['search_term']);
        $searchParams = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
        
        if ($isDemonstrator) {
            // Demonstrators only search within their assigned demonstrations
            $demos = $da->GetData(
                "SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.EMAILADDRESS, 
                       a.PHONENUMBER, a.COMPANYNAME, a.INTERESTDESCRIPTION,
                       b.COUNTRY, a.DEMOSTATE,
                       CASE a.DEMOSTATE 
                         WHEN '0' THEN 'Pending' 
                         WHEN '1' THEN 'Assigned' 
                         WHEN '2' THEN 'Completed' 
                       END AS STATUS,
                       a.STAFFID, CONCAT(c.FIRSTNAME, ' ', c.SURNAME) AS ASSIGNED_TO
                FROM tbl_demonstration a
                JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                LEFT JOIN tbl_staff c ON a.STAFFID = c.STAFFID
                WHERE (a.FIRSTNAME LIKE ? OR a.LASTNAME LIKE ? OR a.EMAILADDRESS LIKE ? OR a.COMPANYNAME LIKE ?)
                AND a.STAFFID = ?
                ORDER BY a.DEMONSTRATIONID DESC",
                array_merge($searchParams, [$staffId])
            );
        } else {
            // Admins search all demonstrations
            $demos = $da->GetData(
                "SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.EMAILADDRESS, 
                       a.PHONENUMBER, a.COMPANYNAME, a.INTERESTDESCRIPTION,
                       b.COUNTRY, a.DEMOSTATE,
                       CASE a.DEMOSTATE 
                         WHEN '0' THEN 'Pending' 
                         WHEN '1' THEN 'Assigned' 
                         WHEN '2' THEN 'Completed' 
                       END AS STATUS,
                       a.STAFFID, CONCAT(c.FIRSTNAME, ' ', c.SURNAME) AS ASSIGNED_TO
                FROM tbl_demonstration a
                JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                LEFT JOIN tbl_staff c ON a.STAFFID = c.STAFFID
                WHERE a.FIRSTNAME LIKE ? OR a.LASTNAME LIKE ? OR a.EMAILADDRESS LIKE ? OR a.COMPANYNAME LIKE ?
                ORDER BY a.DEMONSTRATIONID DESC",
                $searchParams
            );
        }
    }
    // List all demos
    elseif (isset($_POST['btnlistall'])) {
        if ($isDemonstrator) {
            // Get only demonstrations assigned to this demonstrator
            $demos = $da->GetData(
                "SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.EMAILADDRESS, 
                       a.PHONENUMBER, a.COMPANYNAME, a.INTERESTDESCRIPTION,
                       b.COUNTRY, a.DEMOSTATE,
                       CASE a.DEMOSTATE 
                         WHEN '0' THEN 'Pending' 
                         WHEN '1' THEN 'Assigned' 
                         WHEN '2' THEN 'Completed' 
                       END AS STATUS,
                       a.STAFFID, CONCAT(c.FIRSTNAME, ' ', c.SURNAME) AS ASSIGNED_TO
                FROM tbl_demonstration a
                JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                LEFT JOIN tbl_staff c ON a.STAFFID = c.STAFFID
                WHERE a.STAFFID = ?
                ORDER BY a.DEMONSTRATIONID DESC",
                [$staffId]
            );
        } else {
            // Admins see all demonstrations
            $demos = $da->GetAllDemonstrations();
        }
    }
} else {
    // Check for action parameter in URL (for redirects after completing a demo)
    $action = $_GET['action'] ?? '';
    
    if ($action == 'listall') {
        // Same logic as btnlistall - force list all demos
        if ($isDemonstrator) {
            $demos = $da->GetData(
                "SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.EMAILADDRESS, 
                       a.PHONENUMBER, a.COMPANYNAME, a.INTERESTDESCRIPTION,
                       b.COUNTRY, a.DEMOSTATE,
                       CASE a.DEMOSTATE 
                         WHEN '0' THEN 'Pending' 
                         WHEN '1' THEN 'Assigned' 
                         WHEN '2' THEN 'Completed' 
                       END AS STATUS,
                       a.STAFFID, CONCAT(c.FIRSTNAME, ' ', c.SURNAME) AS ASSIGNED_TO
                FROM tbl_demonstration a
                JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                LEFT JOIN tbl_staff c ON a.STAFFID = c.STAFFID
                WHERE a.STAFFID = ?
                ORDER BY a.DEMONSTRATIONID DESC",
                [$staffId]
            );
        } else {
            $demos = $da->GetAllDemonstrations();
        }
    } else {
        // Default: list demos on initial page load based on role
        if ($isDemonstrator) {
            // Get only demonstrations assigned to this demonstrator
            $demos = $da->GetData(
                "SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.EMAILADDRESS, 
                       a.PHONENUMBER, a.COMPANYNAME, a.INTERESTDESCRIPTION,
                       b.COUNTRY, a.DEMOSTATE,
                       CASE a.DEMOSTATE 
                         WHEN '0' THEN 'Pending' 
                         WHEN '1' THEN 'Assigned' 
                         WHEN '2' THEN 'Completed' 
                       END AS STATUS,
                       a.STAFFID, CONCAT(c.FIRSTNAME, ' ', c.SURNAME) AS ASSIGNED_TO
                FROM tbl_demonstration a
                JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                LEFT JOIN tbl_staff c ON a.STAFFID = c.STAFFID
                WHERE a.STAFFID = ?
                ORDER BY a.DEMONSTRATIONID DESC",
                [$staffId]
            );
        } else {
            // Admins see all demonstrations
            $demos = $da->GetAllDemonstrations();
        }
    }
}

// Get all demonstrators for dropdown (admin only)
$demonstrators = $isAdmin ? $da->GetDemonstrators() : [];

// Set page title
$pageTitle = 'Manage Demonstrations | AI Solutions';

// Start output buffering to capture content
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Demonstrations</h1>
    
    <?php if ($isDemonstrator): ?>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-sm text-blue-700">
                You are viewing demonstrations assigned to you.
            </p>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['demo_message'])): ?>
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
                            echo $_SESSION['demo_message'];
                            unset($_SESSION['demo_message']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['demo_error'])): ?>
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
                            echo $_SESSION['demo_error'];
                            unset($_SESSION['demo_error']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <fieldset class="border border-gray-200 rounded-md p-4">
            <legend class="text-lg font-medium text-gray-700 px-2">Search for demonstrations</legend>
            <form method="POST">
                <div class="space-y-4">
                    <input type="text" name="search_term" id="search_term" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                           placeholder="Search by name, email, or company">
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
    
    <!-- Demonstrations List -->
    <?php if (empty($demos)): ?>
        <div class="bg-yellow-50 text-yellow-700 p-4 rounded-md mb-6">
            No demonstration requests found.
            <?php if ($isDemonstrator): ?>
                <p class="mt-2 text-sm">You currently don't have any demonstrations assigned to you.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Company</th>
                            <th class="px-4 py-3 text-left">Contact</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Assigned To</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demos as $demo): ?>
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3"><?php echo $demo['DEMONSTRATIONID']; ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($demo['FIRSTNAME'] . ' ' . $demo['LASTNAME']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($demo['COMPANYNAME']); ?></td>
                                <td class="px-4 py-3">
                                    <div class="text-sm"><?php echo htmlspecialchars($demo['EMAILADDRESS']); ?></div>
                                    <?php if (!empty($demo['PHONENUMBER'])): ?>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($demo['PHONENUMBER']); ?></div>
                                    <?php endif; ?>
                                </td>
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
                                    <?php echo htmlspecialchars($demo['ASSIGNED_TO'] ?? 'Not Assigned'); ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($demo['STATUS'] === 'Pending' && $isAdmin): ?>
                                        <form method="POST">
                                            <input type="hidden" name="demo_id" value="<?php echo $demo['DEMONSTRATIONID']; ?>">
                                            <div class="flex items-center gap-2">
                                                <select name="staff_id" class="text-xs border border-gray-300 rounded py-1 px-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                                    <option value="">Select Demonstrator</option>
                                                    <?php foreach ($demonstrators as $demonstrator): ?>
                                                        <option value="<?php echo $demonstrator['STAFFID']; ?>">
                                                            <?php echo htmlspecialchars($demonstrator['FULLNAME']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" name="assign_demo" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300">
                                                    Assign
                                                </button>
                                            </div>
                                        </form>
                                    <?php elseif ($demo['STATUS'] === 'Assigned'): ?>
                                        <!-- For demonstrators, only show if assigned to them -->
                                        <?php if ($isAdmin || ($isDemonstrator && $demo['STAFFID'] == $staffId)): ?>
                                            <form method="POST" id="complete-demo-<?php echo $demo['DEMONSTRATIONID']; ?>">
                                                <input type="hidden" name="demo_id" value="<?php echo $demo['DEMONSTRATIONID']; ?>">
                                                <button type="submit" name="complete_demo" class="bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300">
                                                    Mark as Complete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-500 text-xs">No actions available</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-xs">No actions available</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Get the content from the buffer
$content = ob_get_clean();

// Include the master template
include 'master.php';
?>
