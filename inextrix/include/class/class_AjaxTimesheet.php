<?
/**
  * class_AjaxTimesheet.php : This Class file contains function that returns timsheet_id and Start_date value to process.php
  *
  * This file gets timesheet_id and Start_date value according to employee_id and timesheet status.
  *
  * $Id: class_AjaxTimesheet.php,v 1.0 2008/01/09 13:36:23
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
// require_once("class_dal.php");

class AjaxTimesheet
{	
	/**
	* @var string
	* DB Connection variable.
	*/
	var $sql;
	
	//search variables
	/**
	* @var string
	* Employee ID as search criteria.
	*/
	var $empid;
	/**
	* @var string
	* Timesheet Status as search criteria.
	*/
	var $timesheetstatus;
	
	//**--Employee selecion based on employee status--**//
	// Change: A class variable added.
	// Purpose: Variable stores employee status code as employee filter criteria
	// Date: 10-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
	/**
	* @var string
	* Employee search criteria
	*/
	var $statcd;
	//**---------------------------------------------------------------------------------------------------------------**//

	/**constructor 
	*
	* This constructor Opens a connection with DB and sets DB connection variable value.
   	* Constructor of DAL class creates the connection with DB.
	* @see dal::dal()
	*
	*/
	function AjaxTimesheet()
	{

		$this->sql = new dal();
		//echo "<br/>here";
	}

	/**
	* FetchTimsheetid_StartDate : The function fetches timesheet ids and start dates based on search criteria.
	*
	* @return result An associative, one dimensional array filled with data retrieved from DB. With an associative array you can access the field by using their name instead of using the index. 
	*/
	function FetchTimsheetid_StartDate()
	{
		include(APP_INCLUDE_PATH.'sql_query.php');
		if($this->timesheetstatus == "ALL")
		{
		$rec = $this->sql->exec_query($FetchTimsheetid_StartDate_ALL);
		}
		else
		{
		$rec = $this->sql->exec_query($FetchTimsheetid_StartDate);
		}
		return $rec;
	}

	//**--Employee selecion based on employee status--**//
	// Change: A function added 
	// Purpose: Function fetches employee list based on selected employee status
	// Date: 10-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
	/**
	* FetchEmployee : The function fetches employees based on search criteria.
	*
	* @return result An associative, one dimensional array filled with data retrieved from DB. With an associative array you can access the field by using their name instead of using the index. 
	*/
	function FetchEmployee()
	{
		include(APP_INCLUDE_PATH.'sql_query.php');
		if($this->statcd == "ALL")
		{
		$rec = $this->sql->exec_query($FetchEmployee_ALL);
		}
		else
		{
		$rec = $this->sql->exec_query($FetchEmployee);
		}
		return $rec;
	}
	//**---------------------------------------------------------------------------------------------------------------**//
        
        
}