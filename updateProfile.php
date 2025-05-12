<?php
/**
 * profileUpdate.php - Allow staff members to update their profiles
 */

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

// Set page-specific variables
$pageTitle = 'Update Profile - AI Solutions';

// Include the DataAccess class
require_once 'DataAccess.php';
$da = new DataAccess();

// Initialize variables
$success_message = '';
$error_message = '';
$staff_details = null;
$is_admin_user = ($_SESSION["staff_id"] == 1); // Check if user is the primary admin (staffid=1)

// Get staff details of the logged in user
$current_staff_id = $_SESSION["staff_id"] ?? 0;

if ($current_staff_id > 0) {
    $sql = "SELECT a.*, b.ROLE, c.COUNTRY 
            FROM tbl_staff a 
            JOIN tbl_roles b ON a.ROLEID = b.ROLEID 
            JOIN tbl_countries c ON a.COUNTRYID = c.COUNTRYID 
            WHERE a.STAFFID = ?";
    $result = $da->GetData($sql, array($current_staff_id));
    if (count($result) > 0) {
        $staff_details = $result[0];
    }
}

// Process form submission only if not the primary admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnupdate']) && !$is_admin_user) {
    try {
        // Get form data
        $staffId = $_POST["staff_id"] ?? 0;
        $firstName = $_POST["firstname"] ?? "";
        $lastName = $_POST["surname"] ?? "";
        $gender = $_POST["gender"] ?? "";
        $dob = $_POST["dob"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";
        
        // Validation
        if (empty($firstName) || empty($lastName) || empty($email)) {
            throw new Exception("First name, last name, and email are required!");
        }
        
        // If password is provided, check if confirmation matches
        if (!empty($password)) {
            if ($password !== $confirmPassword) {
                throw new Exception("Passwords do not match!");
            }
        }
        
        // Prepare for update
        if (!empty($password)) {
            // Update including password
            $sql = "UPDATE tbl_staff SET 
                    FIRSTNAME = ?, 
                    SURNAME = ?, 
                    GENDER = ?, 
                    DOB = ?, 
                    EMAILADDRESS = ?, 
                    PASSWORD = ? 
                    WHERE STAFFID = ?";
            // Hash the password with SHA-256
            $hashedPassword = hash('sha256', $password);
            $params = [$firstName, $lastName, $gender, $dob, $email, $hashedPassword, $staffId];
        } else {
            // Update without changing password
            $sql = "UPDATE tbl_staff SET 
                    FIRSTNAME = ?, 
                    SURNAME = ?, 
                    GENDER = ?, 
                    DOB = ?, 
                    EMAILADDRESS = ? 
                    WHERE STAFFID = ?";
            $params = [$firstName, $lastName, $gender, $dob, $email, $staffId];
        }
        
        // Execute update
        $count = $da->ExecuteCommand($sql, $params);
        
        if ($count > 0) {
            // Update session variables
            $_SESSION["firstname"] = $firstName;
            $_SESSION["surname"] = $lastName;
            $_SESSION["staff_name"] = $firstName . ' ' . $lastName;
            $_SESSION["staff_email"] = $email;
            
            $success_message = "Your profile has been updated successfully!";
            
            // Refresh staff details
            $sql = "SELECT a.*, b.ROLE, c.COUNTRY 
                    FROM tbl_staff a 
                    JOIN tbl_roles b ON a.ROLEID = b.ROLEID 
                    JOIN tbl_countries c ON a.COUNTRYID = c.COUNTRYID 
                    WHERE a.STAFFID = ?";
            $result = $da->GetData($sql, array($current_staff_id));
            if (count($result) > 0) {
                $staff_details = $result[0];
            }
        } else {
            $error_message = "No changes were made or update failed!";
        }
    } catch (Exception $ex) {
        $error_message = $ex->getMessage();
    }
}

// Start output buffering for the master template
ob_start();
?>

<div class="bg-gray-50 py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            <?php echo $is_admin_user ? 'View Profile' : 'Update Profile'; ?>
        </h1>
        
        <?php if ($is_admin_user): ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
            <p>This is the primary administrator account. Profile details cannot be modified for security reasons.</p>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-4 mx-6">
                <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 my-4 mx-6">
                <p><?php echo $error_message; ?></p>
            </div>
            <?php endif; ?>
            
            <div class="px-4 py-5 sm:p-6">
                <?php if ($staff_details): ?>
                <form method="POST" action="">
                    <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff_details['STAFFID']); ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                            <?php if ($is_admin_user): ?>
                            <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                                <?php echo htmlspecialchars($staff_details['FIRSTNAME']); ?>
                            </div>
                            <?php else: ?>
                            <input type="text" name="firstname" id="firstname" required 
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                value="<?php echo htmlspecialchars($staff_details['FIRSTNAME']); ?>">
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="surname" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <?php if ($is_admin_user): ?>
                            <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                                <?php echo htmlspecialchars($staff_details['SURNAME']); ?>
                            </div>
                            <?php else: ?>
                            <input type="text" name="surname" id="surname" required 
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                value="<?php echo htmlspecialchars($staff_details['SURNAME']); ?>">
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <?php if ($is_admin_user): ?>
                            <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                                <?php echo htmlspecialchars($staff_details['EMAILADDRESS']); ?>
                            </div>
                            <?php else: ?>
                            <input type="email" name="email" id="email" required 
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                value="<?php echo htmlspecialchars($staff_details['EMAILADDRESS']); ?>">
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$is_admin_user): ?>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" 
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Leave blank to keep current password">
                            <p class="mt-1 text-xs text-gray-500">Only fill this if you want to change your password</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$is_admin_user): ?>
                    <div class="mt-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" 
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="Confirm your new password">
                        <p class="mt-1 text-xs text-gray-500">Re-enter your new password for confirmation</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                            <?php echo $staff_details['GENDER'] == 'M' ? 'Male' : 'Female'; ?>
                            <input type="hidden" name="gender" value="<?php echo htmlspecialchars($staff_details['GENDER']); ?>">
                            <p class="mt-1 text-xs text-gray-500">gender cannot be changed please contact the administrator for help</p>
                        </div>
                    </div>
                        
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                            <?php echo htmlspecialchars($da->FormatDate($staff_details['DOB'])); ?>
                            <input type="hidden" name="dob" value="<?php echo htmlspecialchars($staff_details['DOB']); ?>">
                            <p class="mt-1 text-xs text-gray-500">date of birth cannot be changed please contact the administrator for help</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                            <?php echo htmlspecialchars($staff_details['ROLE']); ?>
                            <p class="mt-1 text-xs text-gray-500">Role cannot be changed by the user</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Country</label>
                        <div class="mt-1 block w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-md">
                            <?php echo htmlspecialchars($staff_details['COUNTRY']); ?>
                            <p class="mt-1 text-xs text-gray-500">user's country cannot be changed please contact the administrator for help</p>
                        </div>
                    </div>
                    
                    <?php if (!$is_admin_user): ?>
                    <div class="mt-8 flex justify-end">
                        <button type="reset" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Reset
                        </button>
                        <button type="submit" name="btnupdate" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Profile
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
                <?php else: ?>
                <div class="text-center py-6">
                    <p class="text-red-600">Error retrieving your profile information. Please try again later or contact support.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Get the content and clean the buffer
$content = ob_get_clean();

// Include the master page
include 'master.php';
?>
