<?php

class DataAccess
{
    //global date format
    public $mariadb_dateformat = "Y-m-d"; //maria_db date format

    //connection method
    public function GetConnection()
    {
        try {
            //connection parameters
            $server     = "127.0.0.1";
            $database   = "AIAssistant";
            $username   = "root";
            $password   = "@Galeasiama01234";
            $port       = "3306";

            //connectionstring
            $conn = new PDO("mysql:host={$server}:{$port}; dbname={$database}", $username, $password);

            //catch exceptions
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);

            //return connection
            return $conn;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //function to get database data
    function GetDataSQL($sql)
    {
        try {
            $conn = $this->GetConnection();

            //execute sql to get data | SELECT
            $result = $conn->query($sql);

            //fetch into associative array
            $arrdata = $result->fetchAll(PDO::FETCH_ASSOC);

            //free objects
            $result->closeCursor();
            $conn = null;

            return $arrdata;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //function to save, update and delete data
    function ExecuteSQL($sql)
    {
        try {
            //get connection
            $conn = $this->GetConnection();

            //execute SQL to insert, update, delete
            $count = $conn->exec($sql);

            //free objects
            $conn = null;

            //return count of rows affected
            return $count;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //function to get database data
    function GetData($sql, $params = null)
    {
        try {
            $conn = $this->GetConnection();

            /* handle parameters */
            $values = is_array($params) ? $params : ((is_null($params)) ? array() : array($params));
            $stmt   = $conn->prepare($sql);
            $stmt->execute($values);
            $arr_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            //free objects
            $stmt->closeCursor();
            $conn = null;

            return $arr_data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //function to save update delete
    function ExecuteCommand($sql, $params = null)
    {
        try {
            $conn = $this->GetConnection();

            /* handle parameters */
            $values = is_array($params) ? $params : ((is_null($params)) ? array() : array($params));
            //prepare and execute
            $stmt = $conn->prepare($sql);
            $stmt->execute($values);
            $count = $stmt->rowCount();

            //free objects
            $stmt->closeCursor();
            $conn = null;

            return $count;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //execute multiple queries as transaction
    public function ExecuteTransaction($arrsqls, $params = null)
    {
        try {
            //get connection
            $conn = $this->GetConnection();
            $conn->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

            // begin a transaction
            $conn->beginTransaction();

            /* handle parameters */
            $arrparams = is_array($params) ? $params : ((is_null($params)) ? array() : array($params));

            // set of sqls; if one fails, an exception should be thrown - loop through executing
            $count = 0;
            for ($k = 0; $k < count($arrsqls); $k++) {
                //get arrvalues for current sql if present
                $values = $arrparams[$k];
                $sql = $arrsqls[$k];

                //prepare and execute
                $stmt = $conn->prepare($sql);
                $stmt->execute($values);

                $rowcount = $stmt->rowCount();
                if ($rowcount > 0) {
                    $count += $rowcount;
                } else {
                    throw new Exception("One of the transaction actions failed, overall transaction failed and was rolled back!!");
                }
            }
            //commit after last execution
            $conn->commit();

            //free objects
            $stmt->closeCursor();
            $conn = null;

            //return count of rows affected
            return $count;
        } catch (Exception $ex) {
            // rollback and throw back exception
            $conn->rollback();
            throw $ex;
        }
    }

    /* Authentication functions */

    function GetUserDetails($emailAddress, $password)
    {
    try {
        $sql = "SELECT a.STAFFID, a.FIRSTNAME, a.SURNAME, a.GENDER, a.DOB, 
                       a.EMAILADDRESS, a.PASSWORD, a.ROLEID, b.ROLE, 
                       a.COUNTRYID, c.COUNTRY, a.DATEREGISTERED
                FROM tbl_staff a 
                JOIN tbl_roles b ON a.ROLEID = b.ROLEID 
                JOIN tbl_countries c ON a.COUNTRYID = c.COUNTRYID
                WHERE a.EMAILADDRESS = ?";

        $arrvalues = array($emailAddress);
        $arruser = $this->GetData($sql, $arrvalues);

        //check if user exists and password is correct
        $logged_user = array();
        if (count($arruser) > 0) {
            $stored_password = $arruser[0]["PASSWORD"];
            // Hashing the input password with SHA-256 before comparing
            $hashed_password = hash('sha256', $password);
            if ($hashed_password === $stored_password) {
                $logged_user = $arruser;
            }
        }

        return $logged_user;
    } catch (Exception $ex) {
        throw $ex;
   }
}


    /* AI Solutions Specific Functions */

    // Get all countries for dropdown
    function GetAllCountries()
    {
        try {
            $sql = "SELECT COUNTRYID, COUNTRY FROM tbl_countries ORDER BY COUNTRY";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all staff members
    function GetAllStaff()
    {
        try {
            $sql = "SELECT a.STAFFID, a.FIRSTNAME, a.SURNAME, a.GENDER, a.EMAILADDRESS, 
                           b.ROLE, c.COUNTRY, a.DATEREGISTERED
                    FROM tbl_staff a
                    JOIN tbl_roles b ON a.ROLEID = b.ROLEID
                    JOIN tbl_countries c ON a.COUNTRYID = c.COUNTRYID
                    ORDER BY a.STAFFID";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get demonstrators
    function GetDemonstrators()
    {
        try {
            $sql = "SELECT a.STAFFID, CONCAT(a.FIRSTNAME, ' ', a.SURNAME) AS FULLNAME
                    FROM tbl_staff a
                    WHERE a.ROLEID = 2
                    ORDER BY FULLNAME";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all roles
    function GetAllRoles()
    {
        try {
            $sql = "SELECT ROLEID, ROLE FROM tbl_roles ORDER BY ROLEID";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all events
    function GetAllEvents()
    {
        try {
            $sql = "SELECT EVENTID, EVENTTITTLE as EVENTTITLE, EVENTDESCRIPTION, VANUE, 
                           EVENTDATE, EVENTTIME
                    FROM tbl_events
                    ORDER BY EVENTDATE DESC";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get upcoming events
    function GetUpcomingEvents()
    {
        try {
            $currentDate = date("Y-m-d");
            $sql = "SELECT EVENTID, EVENTTITTLE as EVENTTITLE, EVENTDESCRIPTION, VANUE, 
                           EVENTDATE, EVENTTIME
                    FROM tbl_events
                    WHERE EVENTDATE >= ?
                    ORDER BY EVENTDATE ASC";
            return $this->GetData($sql, [$currentDate]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all demonstrations
    function GetAllDemonstrations()
    {
        try {
            $sql = "SELECT a.DEMONSTRATIONID, a.FIRSTNAME, a.LASTNAME, a.EMAILADDRESS, 
                           a.PHONENUMBER, a.COMPANYNAME, a.INTERESTDESCRIPTION,
                           b.COUNTRY, 
                           CASE a.DEMOSTATE 
                             WHEN '0' THEN 'Pending' 
                             WHEN '1' THEN 'Assigned' 
                             WHEN '2' THEN 'Completed' 
                           END AS STATUS,
                           CONCAT(c.FIRSTNAME, ' ', c.SURNAME) AS ASSIGNED_TO
                    FROM tbl_demonstration a
                    JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                    LEFT JOIN tbl_staff c ON a.STAFFID = c.STAFFID
                    ORDER BY a.DEMONSTRATIONID DESC";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all FAQs
    function GetAllFAQs()
    {
        try {
            $sql = "SELECT FAQID, FAQUESTION, FAQANSWER FROM tbl_faq ORDER BY FAQID";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all feedback
    function GetAllFeedback()
    {
        try {
            $sql = "SELECT FEEDBACKID, FIRSTNAME, LASTNAME, EMAILADDRESS, PHONENUMBER, 
                          FEEDBACK, FEEDBACKTYPE, RATING 
                    FROM tbl_feedback 
                    ORDER BY FEEDBACKID DESC";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get all reports
    function GetAllReports()
    {
        try {
            $sql = "SELECT REPORTID, REPORTNAME, CATEGORY, PREVIEW FROM tbl_reports ORDER BY CATEGORY, REPORTID";
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Get event registrations for a specific event
    function GetEventRegistrations($eventId)
    {
        try {
            $sql = "SELECT a.REGISTRATIONID, a.FIRSTNAME, a.SURNAME, a.EMAILADDRESS, 
                           a.PHONENUMBER, a.COMPANYNAME, b.COUNTRY
                    FROM tbl_eventregistry a
                    JOIN tbl_countries b ON a.COUNTRYID = b.COUNTRYID
                    WHERE a.EVENTID = ?
                    ORDER BY a.REGISTRATIONID";
            return $this->GetData($sql, [$eventId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Add a new staff member
    function AddStaffMember($staffId, $firstName, $lastName, $gender, $dob, $email, $password, $roleId, $countryId)
    {
    try {
        $sql = "INSERT INTO tbl_staff (STAFFID, FIRSTNAME, SURNAME, GENDER, DOB, EMAILADDRESS, PASSWORD, ROLEID, COUNTRYID, DATEREGISTERED) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $currentDate = date("Y-m-d");
        $params = [$staffId, $firstName, $lastName, $gender, $dob, $email, $password, $roleId, $countryId, $currentDate];

        return $this->ExecuteCommand($sql, $params);
    } catch (Exception $ex) {
        throw $ex;
    	}
    }

   // Update a staff member
    function UpdateStaffMember($staffId, $firstName, $lastName, $gender, $dob, $email, $roleId, $countryId)
    {
        try {
            $sql = "UPDATE tbl_staff 
                    SET FIRSTNAME = ?, SURNAME = ?, GENDER = ?, DOB = ?, 
                        EMAILADDRESS = ?, ROLEID = ?, COUNTRYID = ? 
                    WHERE STAFFID = ?";

            $params = [$firstName, $lastName, $gender, $dob, $email, $roleId, $countryId, $staffId];

            return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Update staff password
    function UpdateStaffPassword($staffId, $newPassword)
    {
        try {
            $sql = "UPDATE tbl_staff SET PASSWORD = ? WHERE STAFFID = ?";
            return $this->ExecuteCommand($sql, [$newPassword, $staffId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Delete a staff member
    function DeleteStaffMember($staffId)
    {
        try {
            $sql = "DELETE FROM tbl_staff WHERE STAFFID = ?";
            return $this->ExecuteCommand($sql, [$staffId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Add a new event
    function AddEvent($eventId, $title, $description, $venue, $date, $time)
    {
        try {
            $sql = "INSERT INTO tbl_events (EVENTID, EVENTTITTLE, EVENTDESCRIPTION, VANUE, EVENTDATE, EVENTTIME) 
                VALUES (?, ?, ?, ?, ?, ?)";

            $params = [$eventId, $title, $description, $venue, $date, $time];

             return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Update an event
    function UpdateEvent($eventId, $title, $description, $venue, $date, $time)
    {
        try {
            $sql = "UPDATE tbl_events 
                    SET EVENTTITTLE = ?, EVENTDESCRIPTION = ?, VANUE = ?, 
                        EVENTDATE = ?, EVENTTIME = ? 
                    WHERE EVENTID = ?";

            $params = [$title, $description, $venue, $date, $time, $eventId];

            return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Delete an event
    function DeleteEvent($eventId)
    {
        try {
            $sql = "DELETE FROM tbl_events WHERE EVENTID = ?";
            return $this->ExecuteCommand($sql, [$eventId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Register for an event
    function RegisterForEvent($firstName, $lastName, $email, $phone, $company, $eventId, $countryId)
    {
        try {
            // Get the next available registration ID
            $sql = "SELECT MAX(REGISTRATIONID) + 1 AS NEXTID FROM tbl_eventregistry";
            $result = $this->GetDataSQL($sql);
            $registrationId = isset($result[0]['NEXTID']) ? $result[0]['NEXTID'] : 1;

            $sql = "INSERT INTO tbl_eventregistry 
                    (REGISTRATIONID, FIRSTNAME, SURNAME, EMAILADDRESS, PHONENUMBER, 
                     COMPANYNAME, EVENTID, COUNTRYID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [$registrationId, $firstName, $lastName, $email, $phone, $company, $eventId, $countryId];

            return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Request a demonstration
    function RequestDemonstration($firstName, $lastName, $email, $phone, $company, $interest, $countryId)
    {
        try {
            // Get the next available demonstration ID
            $sql = "SELECT MAX(DEMONSTRATIONID) + 1 AS NEXTID FROM tbl_demonstration";
            $result = $this->GetDataSQL($sql);
            $demoId = isset($result[0]['NEXTID']) ? $result[0]['NEXTID'] : 1;

            // For a new demonstration, set STAFFID to 1 (default admin) and DEMOSTATE to 0 (pending)
            $sql = "INSERT INTO tbl_demonstration 
                    (DEMONSTRATIONID, FIRSTNAME, LASTNAME, EMAILADDRESS, PHONENUMBER, 
                     COMPANYNAME, INTERESTDESCRIPTION, COUNTRYID, STAFFID, DEMOSTATE) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, '0')";

            $params = [$demoId, $firstName, $lastName, $email, $phone, $company, $interest, $countryId];

            return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Assign a demonstration to a staff member
    function AssignDemonstration($demoId, $staffId)
    {
        try {
            $sql = "UPDATE tbl_demonstration 
                    SET STAFFID = ?, DEMOSTATE = '1' 
                    WHERE DEMONSTRATIONID = ?";

            return $this->ExecuteCommand($sql, [$staffId, $demoId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Mark a demonstration as complete
    function CompleteDemonstration($demoId)
    {
        try {
            $sql = "UPDATE tbl_demonstration 
                    SET DEMOSTATE = '2' 
                    WHERE DEMONSTRATIONID = ?";

            return $this->ExecuteCommand($sql, [$demoId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Submit feedback 
    function SubmitFeedback($firstName, $lastName, $email, $phone, $feedback, $feedbackType, $rating = 0)
    {
        try {
            $sql = "INSERT INTO tbl_feedback 
                    (FIRSTNAME, LASTNAME, EMAILADDRESS, PHONENUMBER, FEEDBACK, FEEDBACKTYPE, RATING) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $params = [$firstName, $lastName, $email, $phone, $feedback, $feedbackType, $rating];

            return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Add a new FAQ
    function AddFAQ($question, $answer)
    {
        try {
            // Get the next available FAQ ID
            $sql = "SELECT MAX(FAQID) + 1 AS NEXTID FROM tbl_faq";
            $result = $this->GetDataSQL($sql);
            $faqId = isset($result[0]['NEXTID']) ? $result[0]['NEXTID'] : 1;

            $sql = "INSERT INTO tbl_faq (FAQID, FAQUESTION, FAQANSWER) VALUES (?, ?, ?)";

            $params = [$faqId, $question, $answer];

            return $this->ExecuteCommand($sql, $params);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Update an FAQ
    function UpdateFAQ($faqId, $question, $answer)
    {
        try {
            $sql = "UPDATE tbl_faq SET FAQUESTION = ?, FAQANSWER = ? WHERE FAQID = ?";
            return $this->ExecuteCommand($sql, [$question, $answer, $faqId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Delete an FAQ
    function DeleteFAQ($faqId)
    {
        try {
            $sql = "DELETE FROM tbl_faq WHERE FAQID = ?";
            return $this->ExecuteCommand($sql, [$faqId]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /** Report Functions **/

    /** 
        * Get report details 
        *
        *@param int $reportId The report ID to retrieve
        *@return array Report details
     */
    function GetReportDetails($reportId)
    {
        try {
            $sql = "SELECT REPORTID, REPORTNAME, CATEGORY, PREVIEW FROM tbl_reports WHERE REPORTID = ?";
            $result = $this->GetData($sql, [$reportId]);

            return !empty($result) ? $result[0] : [];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Execute a report and return results
     * 
     * @param int $reportId The report ID to execute
     * @return array Report data
     */
    function ExecuteReport($reportId)
    {
        try {
            require_once 'ReportClass.php';
            $reportClass = new ReportClass();

            // Get SQL for the report
            $sql = $reportClass->GetReportSQL($reportId);

            // Execute the query
            return $this->GetDataSQL($sql);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

/** Initialize reports in the database **/
    function InitializeReports()
    {
        try {
            // First check if reports already exist
            $existingReports = $this->GetAllReports();
            if (!empty($existingReports)) {
                return true; // Reports already initialized
            }

            // Array of reports to create
            $reports = [
                [1, 'Staff Information', 'Staff', 'Detailed information about all staff members including roles and registration dates'],
                [2, 'Events Summary', 'Events', 'Summary of all events with registration counts'],
                [3, 'Event Registrations', 'Events', 'Detailed list of users registered for each event'],
                [4, 'Demonstrations Requested', 'Demonstrations', 'All demonstration requests with status information'],
                [5, 'Completed Demonstrations', 'Demonstrations', 'List of all completed demonstrations'],
                [6, 'Customer Interests', 'Demonstrations', 'Analysis of customer interests based on demonstration requests'],
                [7, 'Feedback Analysis', 'Feedback', 'Statistical analysis of customer feedback by type and rating']
            ];

            // Begin transaction
            $conn = $this->GetConnection();
            $conn->beginTransaction();

            // Insert each report
            $stmt = $conn->prepare("INSERT INTO tbl_reports (REPORTID, REPORTNAME, CATEGORY, PREVIEW) VALUES (?, ?, ?, ?)");

            foreach ($reports as $report) {
                $stmt->execute($report);
            }

            // Commit transaction
            $conn->commit();

            return true;
        } catch (Exception $ex) {
            // Rollback on error
            if (isset($conn)) {
                $conn->rollback();
            }
            throw $ex;
        }
    }

    /** Session and security helper functions **/

    // Function to check if user is logged in
    function isLoggedIn()
    {
        return isset($_SESSION['staff_id']);
    }

    // Function to check if user has specific role
    function hasRole($roleId)
    {
        return isset($_SESSION['role_id']) && $_SESSION['role_id'] == $roleId;
    }

    // Function to check if user is an administrator
    function isAdmin()
    {
        return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
    }

    // Function to check if user is a demonstrator
    function isDemonstrator()
    {
        return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2;
    }

    /* Utility Functions */

    function GetCurrentDate()
    {
        try {
            return date($this->mariadb_dateformat);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function GetCurrentTime()
    {
        try {
            return date("H:i:s"); // Hours:minutes:seconds
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Format date for display
    function FormatDate($date)
    {
        return date("F j, Y", strtotime($date));
    }

    // Format time for display
    function FormatTime($time)
    {
        return date("g:i A", strtotime($time));
    }

    // Display a message to the user
    function ShowMessage($msg, $type = "Error", $terminate = true)
    {
        try {
            $type = strtolower($type[0]); // get first letter
            $back_color = ($type == "i") ? "#d9ff66" : "yellow";
            $msg = "<fieldset>
                        <legend><font color=blue>USER MESSAGE</font></legend>
                        <marquee scrollamount=0 bgcolor='$back_color' height=''>
                            <center>
                                <font size='4' face='Arial' color='red'>$msg</font>
                            </center>
                        </marquee>
                    </fieldset>";
            ($terminate == true) ? die($msg) : print($msg);
        } catch (Exception $ex) {
            // Silent catch
        }
    }

    // Generate unique alphanumeric ID
    function GenerateUniqueId($prefix = "")
    {
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        return $prefix . $timestamp . $random;
    }

    // Populate dropdown with countries
    function PopulateCountryDropdown($selectedCountry = null)
    {
        try {
            $countries = $this->GetAllCountries();
            $output = "<option value=''>Select a country</option>";

            foreach ($countries as $country) {
                $selected = ($selectedCountry == $country['COUNTRYID']) ? 'selected' : '';
                $output .= "<option value='{$country['COUNTRYID']}' {$selected}>{$country['COUNTRY']}</option>";
            }

            return $output;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // Populate dropdown with demonstrators
    function PopulateDemonstratorDropdown($selectedDemonstrator = null)
    {
        try {
            $demonstrators = $this->GetDemonstrators();
            $output = "<option value=''>Select a demonstrator</option>";

            foreach ($demonstrators as $demonstrator) {
                $selected = ($selectedDemonstrator == $demonstrator['STAFFID']) ? 'selected' : '';
                $output .= "<option value='{$demonstrator['STAFFID']}' {$selected}>{$demonstrator['FULLNAME']}</option>";
            }

            return $output;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
