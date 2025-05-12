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
            $server     =   "127.0.0.1";
            $database   =   "jewelery";
            $username   =   "root";
            $password   =   "";
            $port       =   "3307";

            //connectionstring
            $conn = new PDO("mysql:host={$server}:{$port}; dbname={$database}", $username, $password);

            //catch exceptions
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            //var_dump($conn->query('SELECT @@autocommit')->fetchAll());

            //return connection
            return $conn;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    //function to get database data using QUERY method | NO PARAMETERS
    function GetDataSQL($sql)
    {
        try {
            //$conn = (new DataAccess)->GetConnection(); 
            $conn = $this->GetConnection();

            //execute sql to get data | SELECT
            $result = $conn->query($sql);

            //fetch into associative array
            $arrdata = $result->fetchAll(PDO::FETCH_ASSOC); //fetch returns Array so use count to get count

            //free objects
            $result->closeCursor();
            $conn = null;

            return $arrdata;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //fuction to save, update and delete data using EXEC method | NO PARAMETERS
    function ExecuteSQL($sql)
    {
        try {
            //get connection
            $conn = (new DataAccess())->GetConnection();

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

    //function to get database data using EXECUTE method | WITH PARAMETERS
    function GetData($sql, $params = null)
    {
        try {
            $conn = (new DataAccess())->GetConnection();

            /* handle parameters */
            $values = is_array($params) ? $params : ((is_null($params)) ? array() : array($params));
            $stmt   = $conn->prepare($sql); //strtolower($sql)
            $stmt->execute($values);
            $arr_data = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch returns Array so use count to get count
            //free objects
            $stmt->closeCursor();
            $conn = null;

            return $arr_data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //function to save update delete using EXECUTE method | WITH PARAMETERS
    function ExecuteCommand($sql, $params = null)
    {
        try {
            $conn = $this->GetConnection();
            //$conn = (new DataAccess())->GetConnection();

            /* handle parameters */
            $values = is_array($params) ? $params : ((is_null($params)) ? array() : array($params));
            //prepare and execute
            $stmt = $conn->prepare($sql); //strtolower($sql)
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
            //var_dump($conn->query('SELECT @@autocommit')->fetchAll());

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
                    throw new Exception("One of the transaction actions failed, overall transaction failed and was rolled back !!");
                }
            }
            //commit after last execution -- // arriving here means no exception was thrown i.e. no sql failed, so commit transaction
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

    function GetUserDetails($username, $password)
    {
        try {
            //union sql
            $sql1 = "SELECT * FROM(
                        SELECT a.STAFFID AS USERID, a.FIRSTNAME, a.SURNAME, a.GENDER, a.DOB, a.USERNAME, a.PASSWORD, a.ROLEID, b.ROLE AS USERTYPE, a.COUNTRYID, c.COUNTRY, a.DATEREGISTERED
                        FROM TBL_STAFF a, TBL_ROLES b, TBL_COUNTRIES c
                        WHERE a.ROLEID = b.ROLEID AND a.COUNTRYID    = c.COUNTRYID

                        UNION ALL

                        SELECT a.CUSTOMERID AS USERID, a.FIRSTNAME, a.SURNAME, a.GENDER, a.DOB, a.USERNAME, a.PASSWORD, 5 AS ROLEID, 'Customer' AS USERTYPE, a.COUNTRYID, b.COUNTRY, a.DATEREGISTERED
                        FROM tbl_customers a, tbl_countries b
                        WHERE a.COUNTRYID = b.COUNTRYID
                    ) x
                    WHERE x.USERNAME = ?";

            $arrvalues = array($username);
            $arruser = $this->GetData($sql1, $arrvalues);

            //check if password is correct
            $logged_user = array();
            if (count($arruser) > 0) {
                $hashed_password = $arruser[0]["PASSWORD"];
                if (password_verify($password, $hashed_password)) {
                    $logged_user = $arruser;
                }
            }

            //var_dump($arruser);
            return $logged_user;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //generate HTML Table
    public function LoadHtmlTable($arrdata)
    {
        try {

            //work with data to load in table
            print("<table border=1 cellspacing=0 cellpadding =5>");
            $keys = array_keys($arrdata[0]);
            print("<tr><th>" . implode("</th><th>", $keys) . "</th></tr>");
            foreach ($arrdata as $row) {
                print("<tr><td>" . implode("</td><td>", $row) . "</td></tr>");
            }
            print("</table>");
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    //generate HTML Table
    public function LoadHtmlTableRowForm($arrdata, $EnableSelectButton = true, $ButtonCaption = "SELECT", $SubmitTargetForm = "")
    {
        try {

            //work with data to load in table
            print("<table border=1 cellspacing=0 cellpadding =5>");
            $keys    = array_keys($arrdata[0]);
            $columns = array_keys(array_change_key_case($arrdata[0], CASE_LOWER));
            $ButtonHeader = ($EnableSelectButton == true) ? "<th>SELECT</th>" : "";
            print("<tr><th>" . implode("</th><th>", $keys) . "</th>$ButtonHeader</tr>");
            foreach ($arrdata as $row) {
                print("<form method='post' action='$SubmitTargetForm'><tr>");
                foreach ($columns as $col) {
                    $colval = $row[strtoupper($col)];
                    print("<td><input type='hidden' name='$col' id='$col' value='$colval' />$colval</td>");
                }
                if ($EnableSelectButton == true) {
                    print("<td><input type='submit' name='btnselect' id='btnselect' value='$ButtonCaption' /></td>");
                }
                print("</tr></form>");
            }
            print("</table>");
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    //generate HTML Table
    public function LoadDropDown($arrdata, $keycolpos = 0, $valcolpos = 1, $ctl = null)
    {
        try {

            print("<option value='0'>Select .... </option>");
            $arrcolumns = (count($arrdata) > 0) ? array_keys($arrdata[0]) : array();
            foreach ($arrdata as $row) {
                $keycolumn      = $arrcolumns[$keycolpos];
                $displaycolumn  = $arrcolumns[$valcolpos];
                $datacolumn     = $arrcolumns[$keycolpos];
                $keyvalue       = $row[$keycolumn];
                $displayvalue   = $row[$displaycolumn];
                $datavalue      = $row[$datacolumn];
                $selected       = (isset($_REQUEST[$ctl]) && $_REQUEST[$ctl] == "$keyvalue") ? 'selected' : ''; //"selected='selected'" : '';
                echo "<option value='$keyvalue' data-$datacolumn='$datavalue' $selected>$displayvalue</option>";
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //function to get array key matching | get attached key to selected button
    function GetSelectedRowKeyFromPost($postarray, $pattern, $separator = "|")
    {
        $pattern = "/{$pattern}/"; //  '/$pattern(.*)/';
        $keys = array_keys($postarray);
        $resultarray = preg_grep($pattern, $keys);
        $resultarray = array_values($resultarray); //re-index
        //found??
        if (count($resultarray) > 0) {
            $selectedkey = explode($separator, $resultarray[0])[1];
        }
        return $selectedkey ?? 0;
    }

    function FilterArrayData($arrdata, $usemainkey = false, $mainkey = null, $datakey = null, $datavalue = null)
    {
        try {
            $matches = array();
            if ($usemainkey == true) {
                $matches = array_filter($arrdata, function ($key) use ($mainkey) {
                    return $key == $mainkey;
                }, ARRAY_FILTER_USE_KEY); // search by key.
            } else {
                $matches = array_filter($arrdata, function ($value) use ($datakey, $datavalue) {
                    return $value[$datakey] == $datavalue;
                }, ARRAY_FILTER_USE_BOTH); // search by value.
            }
            return $matches;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    function GetCurrentTime()
    {
        try {
            return date("H:i:s"); //Hours:minutes:seconds
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function GetCurrentDate()
    {
        try {
            $dateformat  = "Y-m-d";
            $currentdate = (new DateTime())->format($dateformat);
            $currentdate = date($dateformat);
            return $currentdate;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //get gender array
    function GetGenderData()
    {
        try {
            $gender = array(
                0 => array("CODE" => "F", "GENDER" => "Female"),
                1 => array("CODE" => "M", "GENDER" => "Male")
            );
            return $gender;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    function ShowMessage($msg, $type = "Error", $terminate = true)
    {
        try {
            $type = strtolower($type[0]); //get first letter
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
        }
    }

    // Function to check if user is logged in
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Function to check if user has specific role
    function hasRole($roleId)
    {
        return isset($_SESSION['role_id']) && $_SESSION['role_id'] == $roleId;
    }

    // Function to check if user is a customer
    function isCustomer()
    {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer';
    }

    // Function to check if link should be shown based on permissions
    function shouldShowLink($restrictedRoles = [], $hideIfLoggedIn = false, $hideFromCustomer = false)
    {
        if (!empty($restrictedRoles)) {
            if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $restrictedRoles)) {
                return false;
            }
        }

        return true;
    }

    public function isStaff() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'staff';
    }

    public function isAdminOrManager() {
        return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 2]); // 1=Admin, 2=Manager
    
        if ($hideFromCustomer && $this->isCustomer()) {
            return false;
        }
        
        if (!empty($restrictedRoles)) {
            if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $restrictedRoles)) {
                return false;
            }
        }
        
        return true;
    }

}
