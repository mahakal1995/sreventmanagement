<?
/**
  * class_timesheetReminder.php : This Class file contains function that fetches employee details from DB
  *
  * $Id: class_timesheetReminder.php,v 1.0 2009/04/19
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Timesheets_Selection
  */

/**
 * Includes the Configuration file.
 */
include_once("include/config/config.php");
/**
 * Includes DAL class for DB Connection
 */
include_once("class_dal.php");

/**
  * This class performs DB Transaction operations for getting employee id and name.
  *    - It uses dal class to connect to the DB and perform further query operations.
  *
  * Created Date :</b> 2009-04-17</li>
  *
  * <li><b>Last Modified Date :</b> 2009-04-17</li></ul>
  * @package Timesheets_Selection
  * @see dal
  */
class timesheetReminder
{
    /**
    * @var string
    * DB Connection variable.
    */
    var $sql;
    var $empid;
    //**--Timesheet selection for non admin users--**//
    // Change: New variable has been added at
    // Purpose: Stores Employee Number of non admin user    
    //**---------------------------------------------------------------------------------------------------------------**//
    /**
    * @var string
    * Employee Number of current employee if he is not an admin
    */
    var $emp_no;
    //**---------------------------------------------------------------------------------------------------------------**//

    /**constructor
    *
    * This constructor Opens a connection with DB and sets DB connection variable value.
    * Constructor of DAL class creates the connection with DB.
    * @see dal::dal()
    *
    */
    function timesheetReminder()
    {
        $this->sql = new dal();
    }

    /**
    * employee : The function fetches employee id and name from DB.
    *
    * @return result An associative, two dimensional array filled with data retrieved from DB. With an associative array you can access the field by using their name instead of using the index.
    */
    function employee()
    {
        include('include/sql_query.php');
        $rec = $this->sql->exec_query($empStartDatepopup_search_popup);
        return $rec;
    }

    /**
    * employee_status : The function fetches employee_status id and name from DB.
    *
    * @return result An associative, two dimensional array filled with data retrieved from DB. With an associative array you can access the field by using their name instead of using the index.
    */
    function employee_status()
    {
        include('include/sql_query.php');
        $rec = $this->sql->exec_query($emp_status_select);
        return $rec;
    }
	function fetch_employee()
	{
		include('sql_query.php');
// echo $emp_detail;
        $rec = $this->sql->exec_query($emp_detail);
        return $rec;		
	}
    /**
    * current_employee : The function fetches employee id and name from DB for current employee.
    *
    * @return result An associative, one dimensional array filled with data retrieved from DB. With an associative array you can access the field by using their name instead of using the index.
    */
    function current_employee()
    {
        include('include/sql_query.php');
        $rec = $this->sql->exec_query($current_emp_detail);
        return $rec;
    }

    function fetchStartDate()
    {
        include('sql_query.php');
//  echo "<br>".$FetchTimsheetid_StartDate_NonApproved;
        $rec = $this->sql->exec_query($FetchTimsheetid_StartDate_NonApproved);
        return $rec;
    }

    function fetchEmpEmail()
    {
        include('sql_query.php');
        $rec = $this->sql->exec_query($FetchEmail);
        return $rec;
    }
/**
 ** Purpose: Query to fetch all employee's supervisors [For Monday Timesheet Reminder]
**/
/**********************************************************/
    function fetchSupervisorEmail()
    {
        include('sql_query.php');
        $rec = $this->sql->exec_query($FetchSupervisorEmail);
        return $rec;
    }
/**********************************************************/
	function fetchdetails()
	{
		include('sql_query.php');
		$rec = $this->sql->exec_query($FetchDetails);
        return $rec;
	}
	function fetchapproved()
	{
		include('sql_query.php');
		/**			
			Purpose : This will fetch join date and then get week start date of that join date. and replace start date in query to get the correct result
		*/
		$join = $this->sql->exec_query($FetchJoinedDate);		
		list($start_date, $end_date) = x_week_range($join[0]['joined_date']);
		$date = explode ("-",$start_date);
		$modified_joined_date = date("Y-m-d",mktime(0, 0, 0, $date[1]  , $date[2]+1, $date[0]));
		$fetchapproved = str_replace("<JOIN_DATE>",$modified_joined_date. ' 00:00:00',$fetchapproved);
// 		echo $fetchapproved."<br/><br/>";
		/**
			*************************************************
		*/
		$rec = $this->sql->exec_query($fetchapproved);
                return $rec;
	}
	
	/**		
		Purpose : Added new function to fetch the total not approved status time sheets
	*/
	function fetchnotapproved()
	{
		include('sql_query.php');
		
		/**
			Purpose : This will fetch join date and then get week start date of that join date. and replace start date in query to get the correct result
		*/
		$join = $this->sql->exec_query($FetchJoinedDate);		
		list($start_date, $end_date) = x_week_range($join[0]['joined_date']);
		$date = explode ("-",$start_date);
		$modified_joined_date = date("Y-m-d",mktime(0, 0, 0, $date[1]  , $date[2]+1, $date[0]));
		$FetchTimsheetid_StartDate_NonApproved = str_replace("<JOIN_DATE>",$modified_joined_date. ' 00:00:00',$FetchTimsheetid_StartDate_NonApproved);
// 		echo $fetchapproved."<br/><br/>";
		/**
			*************************************************
		*/
// 		echo $FetchTimsheetid_StartDate_NonApproved;
		$rec = $this->sql->exec_query($FetchTimsheetid_StartDate_NonApproved);
        return $rec;
	}
	/**
		**********************************************
	*/
    //**---------------------------------------------------------------------------------------------------------------**//
}