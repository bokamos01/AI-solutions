<?php
/**
 * ReportClass.php - Handles report generation
 * Contains SQL queries for various reports
 */

class ReportClass
{
    /**
     * Get SQL for selected report
     * 
     * @param int $reportId The report ID to generate
     * @return string SQL query for the selected report
     */
    function GetReportSQL($reportId)
    {
        $sql = "";
        
        switch ($reportId) {
            case 1: // Staff Information
                $sql = "SELECT 
                          s.STAFFID, 
                          s.FIRSTNAME, 
                          s.SURNAME, 
                          s.GENDER,
                          s.EMAILADDRESS, 
                          r.ROLE, 
                          c.COUNTRY, 
                          s.DATEREGISTERED
                        FROM tbl_staff s
                        JOIN tbl_roles r ON s.ROLEID = r.ROLEID
                        JOIN tbl_countries c ON s.COUNTRYID = c.COUNTRYID
                        ORDER BY s.STAFFID";
                break;
                
            case 2: // Events Summary
                $sql = "SELECT 
                          e.EVENTID,
                          e.EVENTTITTLE AS 'EVENT_TITLE', 
                          e.VANUE AS 'VENUE',
                          e.EVENTDATE, 
                          e.EVENTTIME,
                          COUNT(er.REGISTRATIONID) AS 'REGISTRATIONS'
                        FROM tbl_events e
                        LEFT JOIN tbl_eventregistry er ON e.EVENTID = er.EVENTID
                        GROUP BY e.EVENTID, e.EVENTTITTLE, e.VANUE, e.EVENTDATE, e.EVENTTIME
                        ORDER BY e.EVENTDATE DESC";
                break;
                
            case 3: // Event Registrations
                $sql = "SELECT 
                          e.EVENTTITTLE AS 'EVENT_TITLE',
                          e.EVENTDATE,
                          er.FIRSTNAME,
                          er.SURNAME,
                          er.EMAILADDRESS,
                          er.PHONENUMBER,
                          er.COMPANYNAME AS 'COMPANY',
                          c.COUNTRY
                        FROM tbl_eventregistry er
                        JOIN tbl_events e ON er.EVENTID = e.EVENTID
                        JOIN tbl_countries c ON er.COUNTRYID = c.COUNTRYID
                        ORDER BY e.EVENTDATE DESC, er.SURNAME, er.FIRSTNAME";
                break;
                
            case 4: // Demonstrations Requested
                $sql = "SELECT 
                          d.DEMONSTRATIONID,
                          d.FIRSTNAME,
                          d.LASTNAME,
                          d.EMAILADDRESS,
                          d.PHONENUMBER,
                          d.COMPANYNAME AS 'COMPANY',
                          c.COUNTRY,
                          CASE d.DEMOSTATE 
                            WHEN '0' THEN 'Pending' 
                            WHEN '1' THEN 'Assigned' 
                            WHEN '2' THEN 'Completed' 
                          END AS STATUS,
                          CONCAT(s.FIRSTNAME, ' ', s.SURNAME) AS 'ASSIGNED_TO'
                        FROM tbl_demonstration d
                        JOIN tbl_countries c ON d.COUNTRYID = c.COUNTRYID
                        JOIN tbl_staff s ON d.STAFFID = s.STAFFID
                        ORDER BY d.DEMONSTRATIONID DESC";
                break;
                
            case 5: // Completed Demonstrations
                $sql = "SELECT 
                          d.DEMONSTRATIONID,
                          d.FIRSTNAME,
                          d.LASTNAME,
                          d.EMAILADDRESS,
                          d.COMPANYNAME AS 'COMPANY',
                          c.COUNTRY,
                          CONCAT(s.FIRSTNAME, ' ', s.SURNAME) AS 'COMPLETED_BY'
                        FROM tbl_demonstration d
                        JOIN tbl_countries c ON d.COUNTRYID = c.COUNTRYID
                        JOIN tbl_staff s ON d.STAFFID = s.STAFFID
                        WHERE d.DEMOSTATE = '2'
                        ORDER BY d.DEMONSTRATIONID DESC";
                break;
                
            case 6: // Customer Interests
                $sql = "SELECT 
                          d.FIRSTNAME,
                          d.LASTNAME,
                          d.INTERESTDESCRIPTION AS 'INTEREST'
                        FROM tbl_demonstration d
                        ORDER BY d.LASTNAME, d.FIRSTNAME";
                break;
                
            case 7: // Feedback Analysis
                $sql = "SELECT 
                          f.FEEDBACKTYPE AS 'TYPE',
                          f.EMAILADDRESS AS 'EMAIL',
                          f.PHONENUMBER AS 'PHONE',
                          COUNT(*) AS 'COUNT',
                          ROUND(AVG(f.RATING), 1) AS 'AVG_RATING',
                          MIN(f.RATING) AS 'MIN_RATING',
                          MAX(f.RATING) AS 'MAX_RATING'
                        FROM tbl_feedback f
                        WHERE f.RATING > 0
                        GROUP BY f.FEEDBACKTYPE, f.EMAILADDRESS, f.PHONENUMBER
                        ORDER BY COUNT(*) DESC";
                break;
                
            default:
                $sql = "SELECT 'No report found for ID: $reportId' AS Message";
                break;
        }
        
        return $sql;
    }
}
