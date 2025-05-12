<?php
if (!isset($_SESSION)) {
    session_start();
}

// Include security check script
require_once 'securityCheck.php';

// Verify that the user is an admin
verifyAdmin();

// Set page-specific variables
$pageTitle = 'Manage FAQs | AI Solutions';

// Include the data access layer
include_once './DataAccess.php';
$da = new DataAccess();

// Get all FAQs
$faqs = $da->GetAllFAQs();

// Get the next FAQ ID
function getNextFaqId($da)
{
    // Get all existing FAQ IDs
    $sql = "SELECT FAQID FROM tbl_faq ORDER BY FAQID";
    $result = $da->GetData($sql);
    
    // Start with ID 1
    $next_id = 1;
    
    // Find the first gap in the sequence
    if (count($result) > 0) {
        $existing_ids = array_map('intval', array_column($result, 'FAQID'));
        
        // Loop through possible IDs until we find a gap
        while (in_array($next_id, $existing_ids)) {
            $next_id++;
        }
    }
    
    return $next_id;
}

// Initialize variables
$next_faq_id = getNextFaqId($da);
$faqDetails = null;

// Get FAQ details if ID is provided in URL
if (isset($_GET['id'])) {
    $faqId = $_GET['id'];
    
    $sql = "SELECT * FROM tbl_faq WHERE FAQID = ?";
    $result = $da->GetData($sql, array($faqId));
    if (count($result) > 0) {
        $faqDetails = $result[0];
    }
}

// Handle form submissions
try {
    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['btnadd'])) {
            // Add new FAQ validation
            $question = trim($_POST["question"] ?? "");
            $answer = trim($_POST["answer"] ?? "");
            
            $errors = [];
            
            if (empty($question)) {
                $errors[] = "Question is required";
            }
            
            if (empty($answer)) {
                $errors[] = "Answer is required";
            }
            
            if (empty($errors)) {
                // Use the AddFAQ method from DataAccess
                $result = $da->AddFAQ($question, $answer);
                
                if ($result > 0) {
                    $_SESSION['faq_message'] = "FAQ added successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['faq_error'] = "FAQ addition failed!";
                }
            } else {
                $_SESSION['faq_error'] = implode("<br>", $errors);
            }
        } else if (isset($_POST['btnupdate'])) {
            // Update FAQ validation
            $faqId = $_POST["faqid"] ?? 0;
            $question = trim($_POST["question"] ?? "");
            $answer = trim($_POST["answer"] ?? "");
            
            $errors = [];
            
            if (empty($faqId) || $faqId <= 0) {
                $errors[] = "Invalid FAQ ID";
            }
            
            if (empty($question)) {
                $errors[] = "Question is required";
            }
            
            if (empty($answer)) {
                $errors[] = "Answer is required";
            }
            
            if (empty($errors)) {
                // Use the UpdateFAQ method from DataAccess
                $result = $da->UpdateFAQ($faqId, $question, $answer);
                
                if ($result > 0) {
                    $_SESSION['faq_message'] = "FAQ updated successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['faq_error'] = "No changes were made or FAQ update failed!";
                }
            } else {
                $_SESSION['faq_error'] = implode("<br>", $errors);
            }
        } else if (isset($_POST['btndelete'])) {
            // Delete FAQ
            $faqId = $_POST["faqid"] ?? 0;
            
            if (!$faqId) {
                $_SESSION['faq_error'] = "Please select a FAQ to delete!";
            } else {
                // Use the DeleteFAQ method from DataAccess
                $result = $da->DeleteFAQ($faqId);
                
                if ($result > 0) {
                    $_SESSION['faq_message'] = "FAQ deleted successfully!";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['faq_error'] = "FAQ deletion failed!";
                }
            }
        }
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    $_SESSION['faq_error'] = $msg;
}

// Start output buffering to capture content for master template
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage FAQs</h1>
    
    <?php if (isset($_SESSION['faq_message'])): ?>
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
                            echo $_SESSION['faq_message'];
                            unset($_SESSION['faq_message']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['faq_error'])): ?>
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
                            echo $_SESSION['faq_error'];
                            unset($_SESSION['faq_error']);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Side: FAQ List -->
        <div class="lg:w-2/3">
            <!-- FAQ List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-medium text-gray-700">All FAQs</h2>
                </div>
                
                <?php if (count($faqs) > 0): ?>
                    <div class="max-h-screen overflow-y-auto">
                        <div class="hidden md:flex bg-gray-100 text-gray-700 font-medium">
                            <div class="w-16 px-4 py-3">ID</div>
                            <div class="flex-1 px-4 py-3">Question</div>
                            <div class="w-44 px-4 py-3 text-right">Actions</div>
                        </div>
                        
                        <?php foreach ($faqs as $faq): ?>
                        <div class="border-t border-gray-200 flex flex-col md:flex-row hover:bg-gray-50">
                            <div class="w-full md:w-16 px-4 py-3 font-medium text-gray-900 md:border-0 border-b border-gray-200 bg-gray-50 md:bg-transparent">
                                <span class="md:hidden font-medium text-gray-500">ID: </span>
                                <?php echo htmlspecialchars($faq['FAQID']); ?>
                            </div>
                            <div class="w-full md:flex-1 px-4 py-3 md:border-0 border-b border-gray-200">
                                <span class="md:hidden font-medium text-gray-500">Question: </span>
                                <?php echo htmlspecialchars($faq['FAQUESTION']); ?>
                                
                                <div class="mt-2 md:hidden">
                                    <span class="font-medium text-gray-500">Answer: </span>
                                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($faq['FAQANSWER'])); ?></p>
                                </div>
                            </div>
                            <div class="w-full md:w-44 px-4 py-3 space-y-2 md:space-y-0 md:flex items-center justify-end gap-2">
                                <button type="button" class="edit-btn w-full md:w-auto bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300" data-id="<?php echo htmlspecialchars($faq['FAQID']); ?>">Edit</button>
                                
                                <form method="POST" id="delete-faq-<?php echo $faq['FAQID']; ?>" class="w-full md:w-auto inline-block">
                                    <input type="hidden" name="faqid" value="<?php echo htmlspecialchars($faq['FAQID']); ?>">
                                    <button type="button" onclick="confirmDeleteFAQ('delete-faq-<?php echo $faq['FAQID']; ?>')" class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded transition duration-300">Delete</button>
                                    <input type="hidden" name="btndelete" value="1">
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        No FAQs found. Add your first FAQ using the form.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Side: Add/Edit FAQ Form -->
        <div class="lg:w-1/3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4" id="faq-form-title">
                <?php echo isset($faqDetails) ? 'Edit FAQ' : 'Add New FAQ'; ?>
            </h2>
            <form name="faq" id="faqForm" method="POST">
                <input type="hidden" name="faqid" id="faq_id" value="<?php echo htmlspecialchars($faqDetails['FAQID'] ?? $next_faq_id); ?>" />

                <div class="mb-4">
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-1">
                        Question <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="question" id="question" required placeholder="Enter FAQ question"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        value="<?php echo htmlspecialchars($faqDetails['FAQUESTION'] ?? ''); ?>" />
                </div>

                <div class="mb-6">
                    <label for="answer" class="block text-sm font-medium text-gray-700 mb-1">
                        Answer <span class="text-red-500">*</span>
                    </label>
                    <textarea name="answer" id="answer" required placeholder="Enter FAQ answer" rows="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                    ><?php echo htmlspecialchars($faqDetails['FAQANSWER'] ?? ''); ?></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="reset-form" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-300">
                        Reset
                    </button>
                    <?php if (isset($faqDetails)): ?>
                        <button type="submit" name="btnupdate" id="edit-faq-btn" 
                            class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                            Update FAQ
                        </button>
                    <?php else: ?>
                        <button type="submit" name="btnadd" id="add-faq-btn" 
                            class="bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                            Add FAQ
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
