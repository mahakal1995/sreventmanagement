<?
/**
  * class_empStartDatepopup.php : This Class file contains function that fetches employee details from DB
  *
  * $Id: class_empStartDatepopup.php,v 1.0 2008/01/09 13:40:17
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Timesheets_Selection
  */

/**
 * Includes the Configuration file.
 */
include("include/config/config.php");
/**
 * Includes DAL class for DB Connection
 */
include(APP_INCLUDE_PATH."class/class_dal.php");

/**
  * This class performs DB Transaction operations for getting employee id and name.
  *    - It uses dal class to connect to the DB and perform further query operations. 
  * 
  * Created Date :</b> 2008-01-02</li>
  * 
  * <li><b>Last Modified Date :</b> 2008-01-02</li></ul>
  * @package Timesheets_Selection
  * @see dal
  */
class empStartDatepopup
{	
	/**
	* @var string
	* DB Connection variable.
	*/
	var $sql;

	//**--Timesheet selection for non admin users--**//
	// Change: New variable has been added at
	// Purpose: Stores Employee Number of non admin user
	// Date: 8-Jan-2008
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
	function empStartDatepopup()
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

	//**--Employee selecion based on employee status--**//
	// Change: New function has been added
	// Purpose: Fetches employee status values from DB
	// Date: 8-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
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
	//**---------------------------------------------------------------------------------------------------------------**//
	
	//**--Timesheet selection for non admin users--**//
	// Change: New function has been added
	// Purpose: To fetch details of current employee for non admin users
	// Date: 8-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
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
	//**---------------------------------------------------------------------------------------------------------------**//
}