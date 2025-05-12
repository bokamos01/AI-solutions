<?php
if (!isset($_SESSION)) {
    session_start();
}

// Include security check script
require_once 'securityCheck.php';

// Verify that the user is an admin
verifyAdmin();

// Set page-specific variables
$pageTitle = 'Manage Staff | AI Solutions';

// Include the data access layer
include_once './DataAccess.php';
$da = new DataAccess();

// Get all staff members
$staffMembers = $da->GetAllStaff();

// Get all roles (excluding customer role)
$roles = $da->GetDataSQL("SELECT * FROM tbl_roles WHERE ROLE != 'Customer' ORDER BY ROLE");

// Get all countries
$countries = $da->GetDataSQL("select * from tbl_countries");

// Get the next staff ID
function getNextStaffId($da)
{
    // Get all existing staff IDs
    $sql = "SELECT STAFFID FROM tbl_staff ORDER BY CAST(STAFFID AS UNSIGNED)";
    $result = $da->GetData($sql);
    
    // Start with ID 1
    $next_id = 1;
    
    // Find the first gap in the sequence
    if (count($result) > 0) {
        $existing_ids = array_map('intval', array_column($result, 'STAFFID'));
        
        // Loop through possible IDs until we find a gap
        while (in_array($next_id, $existing_ids)) {
            $next_id++;
        }
    }
    
    return $next_id;
}

// Initialize variables
$next_staff_id = getNextStaffId($da);
$staffDetails = null;

// Get staff details if ID is provided in URL
if (isset($_GET['id'])) {
    $staffId = $_GET['id'];
    
    // Prevent editing of staff with ID 1
    if ($staffId == 1) {
        $_SESSION['staff_error'] = "Editing user ID 1 is not allowed";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    $sql = "SELECT * FROM tbl_staff WHERE STAFFID = ?";
    $result = $da->GetData($sql, array($staffId));
    if (count($result) > 0) {
        $staffDetails = $result[0];
    }
}

// Handle form submissions
try {
    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['btnregister'])) {
            // Add new staff validation
            $firstname = trim($_POST["firstname"] ?? "");
            $surname = trim($_POST["surname"] ?? "");
            $gender = $_POST["gender"] ?? "";
            $dob = $_POST["dob"] ?? "";
            $email = trim($_POST["email"] ?? "");
            $password = trim($_POST["password"] ?? "");
            $confirm_password = trim($_POST["confirm_password"] ?? "");
            $roleid = intval($_POST["roleid"] ?? 0);
            $countryid = intval($_POST["countryid"] ?? 0);
            
            $errors = [];
            
            if (empty($firstname)) {
                $errors[] = "First name is required";
            }
            
            if (empty($surname)) {
                $errors[] = "Last name is required";
            }
            
            if (empty($gender)) {
                $errors[] = "Gender is required";
            }
            
            if (empty($dob)) {
                $errors[] = "Date of birth is required";
            } else {
                // Validate date of birth - prevent years 1900 or earlier
                $birthYear = date('Y', strtotime($dob));
                if ($birthYear <= 1900) {
                    $errors[] = "Birth year must be after 1900";
                }
                
                // Validate minimum age of 16
                $birthDate = new DateTime($dob);
                $today = new DateTime('today');
                $age = $birthDate->diff($today)->y;
                if ($age < 16) {
                    $errors[] = "Staff members must be at least 16 years old";
                }
            }
            
            if (empty($email)) {
                $errors[] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
            
            if (empty($password)) {
                $errors[] = "Password is required";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            
            if ($roleid <= 0) {
                $errors[] = "Role selection is required";
            }
            
            if ($countryid <= 0) {
                $errors[] = "Country selection is required";
            }
            
            if (empty($errors)) {
                // Get the next available staff ID
                $staffid = getNextStaffId($da);
                
                // Hash the password with SHA2 before storing
                $hashedPassword = hash('sha256', $password);
                
                // Use the AddStaffMember method from DataAccess with staffid
                $result = $da->AddStaffMember($staffid, $firstname, $surname, $gender, $dob, $email, $hashedPassword, $roleid, $countryid);
                
                if ($result > 0) {
                    $_SESSION['staff_message'] = "Staff registered successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['staff_error'] = "Staff registration failed!";
                }
            } else {
                $_SESSION['staff_error'] = implode("<br>", $errors);
            }
        } else if (isset($_POST['btnupdate'])) {
            // Update staff validation
            $staffid = $_POST["staffid"] ?? 0;
            
            // Prevent editing of staff with ID 1
            if ($staffid == 1) {
                $_SESSION['staff_error'] = "Editing user ID 1 is not allowed";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            
            $firstname = trim($_POST["firstname"] ?? "");
            $surname = trim($_POST["surname"] ?? "");
            $gender = $_POST["gender"] ?? "";
            $dob = $_POST["dob"] ?? "";
            $email = trim($_POST["email"] ?? "");
            $roleid = intval($_POST["roleid"] ?? 0);
            $countryid = intval($_POST["countryid"] ?? 0);
            $password = trim($_POST["password"] ?? "");
            $confirm_password = trim($_POST["confirm_password"] ?? "");
            
            $errors = [];
            
            if (empty($staffid) || $staffid <= 0) {
                $errors[] = "Invalid staff ID";
            }
            
            if (empty($firstname)) {
                $errors[] = "First name is required";
            }
            
            if (empty($surname)) {
                $errors[] = "Last name is required";
            }
            
            if (empty($gender)) {
                $errors[] = "Gender is required";
            }
            
            if (empty($dob)) {
                $errors[] = "Date of birth is required";
            } else {
                // Validate date of birth - prevent years 1900 or earlier
                $birthYear = date('Y', strtotime($dob));
                if ($birthYear <= 1900) {
                    $errors[] = "Birth year must be after 1900";
                }
                
                // Validate minimum age of 16
                $birthDate = new DateTime($dob);
                $today = new DateTime('today');
                $age = $birthDate->diff($today)->y;
                if ($age < 16) {
                    $errors[] = "Staff members must be at least 16 years old";
                }
            }
            
            if (empty($email)) {
                $errors[] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
            
            if ($roleid <= 0) {
                $errors[] = "Role selection is required";
            }
            
            if ($countryid <= 0) {
                $errors[] = "Country selection is required";
            }
            
            // Password validation if provided
            if (!empty($password) || !empty($confirm_password)) {
                if (empty($password)) {
                    $errors[] = "Password is required when resetting password";
                } elseif (strlen($password) < 6) {
                    $errors[] = "Password must be at least 6 characters long";
                }
                
                if ($password !== $confirm_password) {
                    $errors[] = "Passwords do not match";
                }
            }
            
            if (empty($errors)) {
                // Use the UpdateStaffMember method from DataAccess
                $result = $da->UpdateStaffMember($staffid, $firstname, $surname, $gender, $dob, $email, $roleid, $countryid);
                
                // If password is provided, hash and update the password
                if (!empty($password) && !empty($confirm_password)) {
                    $hashedPassword = hash('sha256', $password);
                    $passwordResult = $da->UpdateStaffPassword($staffid, $hashedPassword);
                    
                    if ($passwordResult > 0) {
                        $passwordMessage = "Password was reset successfully. ";
                    } else {
                        $passwordMessage = "Password reset failed. ";
                    }
                } else {
                    $passwordMessage = "";
                }
                
                if ($result > 0) {
                    $_SESSION['staff_message'] = $passwordMessage . "Staff updated successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['staff_error'] = $passwordMessage . "No changes were made or staff update failed!";
                }
            } else {
                $_SESSION['staff_error'] = implode("<br>", $errors);
            }
        } else if (isset($_POST['btnreset_password'])) {
            // Reset password validation
            $staffid = $_POST["staffid"] ?? 0;
            
            // Prevent password reset for staff with ID 1
            if ($staffid == 1) {
                $_SESSION['staff_error'] = "Resetting password for user ID 1 is not allowed";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            
            $password = trim($_POST["password"] ?? "");
            $confirm_password = trim($_POST["confirm_password"] ?? "");
            
            $errors = [];
            
            if (empty($staffid) || $staffid <= 0) {
                $errors[] = "Invalid staff ID";
            }
            
            if (empty($password)) {
                $errors[] = "Password is required";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            
            if (empty($errors)) {
                // Hash the password with SHA2 before storing
                $hashedPassword = hash('sha256', $password);
                
                // Use the UpdateStaffPassword method from DataAccess
                $result = $da->UpdateStaffPassword($staffid, $hashedPassword);
                
                if ($result > 0) {
                    $_SESSION['staff_message'] = "Password reset successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['staff_error'] = "Password reset failed!";
                }
            } else {
                $_SESSION['staff_error'] = implode("<br>", $errors);
            }
        } else if (isset($_POST['btndelete'])) {
            // Delete staff
            $staffid = $_POST["staffid"] ?? 0;
            
            // Prevent deletion of staff with ID 1
            if ($staffid == 1) {
                $_SESSION['staff_error'] = "Deleting user ID 1 is not allowed";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            
            if (!$staffid) {
                $_SESSION['staff_error'] = "Please select a staff member to delete!";
            } else {
                // Use the DeleteStaffMember method from DataAccess
                $result = $da->DeleteStaffMember($staffid);
                
                if ($result > 0) {
                    $_SESSION['staff_message'] = "Staff deleted successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['staff_error'] = "Staff deletion failed!";
                }
            }
        }
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    $_SESSION['staff_error'] = $msg;
}

// Start output buffering to capture content for master template
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Staff</h1>
    
    <?php if (isset($_SESSION['staff_message'])): ?>
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
                            echo $_SESSION['staff_message'];
                            unset($_SESSION['staff_message']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['staff_error'])): ?>
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
                            echo $_SESSION['staff_error'];
                            unset($_SESSION['staff_error']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Side: Search and Staff List -->
        <div class="lg:w-2/3">
            <!-- Search Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <fieldset class="border border-gray-200 rounded-md p-4">
                    <legend class="text-lg font-medium text-gray-700 px-2">Search for staff members</legend>
                    <form method="POST">
                        <div class="space-y-4">
                            <input type="text" name="search_term" id="search_term" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Search by ID, name or email">
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

            <!-- Staff List -->
            <?php
            try {
                // Only list staff if search or list all was requested
                if (isset($_POST['btnlistall']) || isset($_POST['btnsearch'])) {
                    // Default: List all staff members
                    $sql = "SELECT s.STAFFID, s.FIRSTNAME, s.SURNAME, s.EMAILADDRESS, s.ROLEID, r.ROLE as ROLENAME 
                            FROM tbl_staff s 
                            JOIN tbl_roles r ON s.ROLEID = r.ROLEID";
                    $params = array();

                    // Handle search
                    if (isset($_POST['btnsearch']) && !empty($_POST['search_term'])) {
                        $search_term = trim($_POST['search_term']);

                        $sql = "SELECT s.STAFFID, s.FIRSTNAME, s.SURNAME, s.EMAILADDRESS, s.ROLEID, r.ROLE as ROLENAME 
                                FROM tbl_staff s 
                                JOIN tbl_roles r ON s.ROLEID = r.ROLEID 
                                WHERE s.STAFFID = ? OR s.FIRSTNAME LIKE ? OR s.SURNAME LIKE ? OR s.EMAILADDRESS LIKE ?";

                        $params = array(
                            $search_term,
                            "%$search_term%",
                            "%$search_term%",
                            "%$search_term%"
                        );
                    }

                    $staff = $da->GetData($sql, $params);
                    
                    // If search returns a single result, pre-fill the form
                    if (isset($_POST['btnsearch']) && count($staff) == 1 && !isset($staffDetails)) {
                        $staffId = $staff[0]['STAFFID'];
                        $sql = "SELECT * FROM tbl_staff WHERE STAFFID = ?";
                        $result = $da->GetData($sql, array($staffId));
                        if (count($result) > 0) {
                            $staffDetails = $result[0];
                        }
                    }
                    
                    // Display staff list
                    if (count($staff) > 0) {
                        // Check if we need to apply scrolling (if more than 5 staff members)
                        $scrollClass = (count($staff) > 5) ? 'max-h-80 overflow-y-auto' : '';
                        ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                            <div class="hidden md:flex bg-gray-100 text-gray-700 font-medium sticky top-0 z-10">
                                <div class="w-16 px-4 py-3">ID</div>
                                <div class="flex-1 px-4 py-3">Name</div>
                                <div class="flex-1 px-4 py-3">Email</div>
                                <div class="w-32 px-4 py-3">Role</div>
                                <div class="w-44 px-4 py-3 text-right">Actions</div>
                            </div>
                            
                            <div class="<?php echo $scrollClass; ?>">
                                <?php foreach ($staff as $member): ?>
                                <div class="border-t border-gray-200 flex flex-col md:flex-row hover:bg-gray-50">
                                    <div class="w-full md:w-16 px-4 py-3 font-medium text-gray-900 md:border-0 border-b border-gray-200 bg-gray-50 md:bg-transparent">
                                        <span class="md:hidden font-medium text-gray-500">ID: </span>
                                        <?php echo htmlspecialchars($member['STAFFID']); ?>
                                    </div>
                                    <div class="w-full md:flex-1 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Name: </span>
                                        <?php echo htmlspecialchars($member['FIRSTNAME'] . ' ' . $member['SURNAME']); ?>
                                    </div>
                                    <div class="w-full md:flex-1 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Email: </span>
                                        <?php echo htmlspecialchars($member['EMAILADDRESS']); ?>
                                    </div>
                                    <div class="w-full md:w-32 px-4 py-3 md:border-0 border-b border-gray-200">
                                        <span class="md:hidden font-medium text-gray-500">Role: </span>
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            <?php echo htmlspecialchars($member['ROLENAME']); ?>
                                        </span>
                                    </div>
                                    <div class="w-full md:w-44 px-4 py-3 space-y-2 md:space-y-0 md:flex items-center justify-end gap-2">
                                        <?php if ($member['STAFFID'] == 1): ?>
                                            <button type="button" class="w-full md:w-auto bg-gray-400 text-white text-xs font-medium py-1 px-2 rounded cursor-not-allowed">Edit</button>
                                        <?php else: ?>
                                            <button type="button" class="edit-btn w-full md:w-auto bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300" data-id="<?php echo htmlspecialchars($member['STAFFID']); ?>">Edit</button>
                                        <?php endif; ?>
                                            
                                        <?php if (!isset($_SESSION['staff_id']) || ($member['STAFFID'] != $_SESSION['staff_id'] && $member['STAFFID'] != 1)): ?>
                                            <form method="POST" id="delete-staff-<?php echo $member['STAFFID']; ?>" class="w-full md:w-auto">
                                                <input type="hidden" name="staffid" value="<?php echo htmlspecialchars($member['STAFFID']); ?>">
                                                <button type="button" onclick="confirmDeleteStaff('delete-staff-<?php echo $member['STAFFID']; ?>')" class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300">Delete</button>
                                                <input type="hidden" name="btndelete" value="1">
                                            </form>
                                        <?php elseif ($member['STAFFID'] == 1): ?>
                                            <button type="button" class="w-full md:w-auto bg-gray-400 text-white text-xs font-medium py-1 px-2 rounded cursor-not-allowed">Delete</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    } else {
                        echo "<div class='bg-red-50 text-red-700 p-4 rounded-md mb-6'>No staff members found.</div>";
                    }
                } else {
                    // Display initial message when no search has been performed
                    echo "<div class='bg-gray-100 text-gray-700 p-6 rounded-md text-center mb-6'>
                            <p>Use the search box above to find staff members or click \"List All\" to view all staff.</p>
                          </div>";
                }
            } catch (Exception $ex) {
                $msg = $ex->getMessage();
                echo "<div class='bg-red-50 text-red-700 p-4 rounded-md mb-6'>$msg</div>";
            }
            ?>
        </div>
        
        <!-- Right Side: Registration/Edit Form -->
        <div class="lg:w-1/3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4" id="staff-form-title">
                <?php echo isset($staffDetails) ? 'Edit Staff Member' : 'Add New Staff'; ?>
            </h2>
            <form name="staff" id="staffForm" method="POST">
                <input type="hidden" name="staffid" id="staff_id" value="<?php echo htmlspecialchars($staffDetails['STAFFID'] ?? $next_staff_id); ?>" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="firstname" id="firstname" required placeholder="First Name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo htmlspecialchars($staffDetails['FIRSTNAME'] ?? ''); ?>" />
                    </div>
                    
                    <div>
                        <label for="surname" class="block text-sm font-medium text-gray-700 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="surname" id="surname" required placeholder="Last Name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo htmlspecialchars($staffDetails['SURNAME'] ?? ''); ?>" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" id="gender" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select gender</option>
                            <option value="M" <?php echo (isset($staffDetails) && $staffDetails['GENDER'] == 'M') ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo (isset($staffDetails) && $staffDetails['GENDER'] == 'F') ? 'selected' : ''; ?>>Female</option>
                            <option value="O" <?php echo (isset($staffDetails) && $staffDetails['GENDER'] == 'O') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">
                            Date Of Birth <span class="text-red-500">*</span>
                        </label>
                        <?php 
                            // Calculate max date (16 years ago from today)
                            $maxDate = date('Y-m-d', strtotime('-16 years'));
                            // Default to 18 years ago if no date is set
                            $defaultDate = date('Y-m-d', strtotime('-18 years'));
                        ?>
                        <input type="date" name="dob" id="dob" required min="1901-01-01" max="<?php echo $maxDate; ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo htmlspecialchars($staffDetails['DOB'] ?? $defaultDate); ?>" />
                        <span class="text-xs text-gray-500 mt-1 block">Birth year must be after 1900 and staff must be at least 16 years old</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required placeholder="Email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        value="<?php echo htmlspecialchars($staffDetails['EMAILADDRESS'] ?? ''); ?>" />
                </div>

                <div id="password-section" class="mb-4">
                    <?php if (isset($staffDetails)): ?>
                    <h3 class="text-lg font-medium text-gray-700 mb-3 border-t pt-4 mt-4">Reset Password</h3>
                    <p class="text-sm text-gray-500 mb-3">Leave password fields empty to keep the current password. Fill both fields to set a new password.</p>
                    <?php endif; ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                <?php echo isset($staffDetails) ? 'New Password' : 'Password'; ?> <?php echo isset($staffDetails) ? '' : '<span class="text-red-500">*</span>'; ?>
                            </label>
                            <input type="password" name="password" id="password" 
                                <?php echo isset($staffDetails) ? '' : 'required'; ?>
                                placeholder="<?php echo isset($staffDetails) ? 'New Password' : 'Password'; ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm <?php echo isset($staffDetails) ? 'New ' : ''; ?>Password <?php echo isset($staffDetails) ? '' : '<span class="text-red-500">*</span>'; ?>
                            </label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                <?php echo isset($staffDetails) ? '' : 'required'; ?>
                                placeholder="Confirm <?php echo isset($staffDetails) ? 'New ' : ''; ?>Password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="roleid" class="block text-sm font-medium text-gray-700 mb-1">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="roleid" id="roleid" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['ROLEID']; ?>" 
                                    <?php echo (isset($staffDetails) && $staffDetails['ROLEID'] == $role['ROLEID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['ROLE']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="countryid" class="block text-sm font-medium text-gray-700 mb-1">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <select name="countryid" id="countryid" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['COUNTRYID']; ?>" 
                                    <?php echo (isset($staffDetails) && $staffDetails['COUNTRYID'] == $country['COUNTRYID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['COUNTRY']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="dateregistered" value="<?php echo date('Y-m-d'); ?>" />

                <div class="flex justify-end space-x-3">
                    <button type="button" id="reset-form" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-300">
                        Reset
                    </button>
                    <?php if (isset($staffDetails)): ?>
                        <button type="submit" name="btnupdate" id="edit-staff-btn" 
                            class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                            Update Staff
                        </button>
                    <?php else: ?>
                        <button type="submit" name="btnregister" id="add-staff-btn" 
                            class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                            Add Staff
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Capture the content
$content = ob_get_clean();

// Include the master template
require_once 'master.php';
?>
