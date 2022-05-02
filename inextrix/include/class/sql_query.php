<?
/**
  * sql_query.php : List of SQL Select Queries used for master table transaction.
  *
  * Here, SQL Queries for Data Selection being used are listed out.
  *
  * $Id: sql_query.php,v 1.0 2008/01/09 15:00:24
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * Followings are the variables declared in the file.
  * <ul>
  * 	<li> empStartDatepopup_search_popup - For hs_hr_employee Table employee search.
  * 	<li> FetchTimsheetid_StartDate - For hs_hr_timesheet Table timesheet_id, start_date search
  * 	<li> current_emp_detail - For hs_hr_employee Table to get current employee information from DB
  * </ul>
  *
  * @version 1.0
  * @package Generic
  */
?>

<?

//---------------------For employee, Table : hs_hr_employee-----------------------------------

/********************************************************
   Purpose : To distinguish query for admin and supervisor
**********************************************************/
$empStartDatepopup_search_popup = (isset($_SESSION['empID']))?" select employee_id,emp_number,emp_lastname,emp_firstname,emp_middle_name,emp_nick_name from hs_hr_employee where emp_number in(select erep_sub_emp_number from hs_hr_emp_reportto where erep_sup_emp_number=".$_SESSION['empID'].") order by emp_number ":" select employee_id,emp_number,emp_lastname,emp_firstname,emp_middle_name,emp_nick_name from hs_hr_employee order by emp_number ";

// $emp_detail = "select employee_id,emp_number,emp_lastname,emp_firstname,emp_middle_name,emp_nick_name from hs_hr_employee where emp_number in (select erep_sub_emp_number from hs_hr_emp_reportto )  and emp_status != 'EST000' and !isnull(emp_status) order by emp_number";


/**
*	Purpose : Add new field in query named : joined_date
*/
$emp_detail = "select employee_id,hs_hr_employee.emp_number,emp_lastname,emp_firstname,emp_middle_name,emp_nick_name,joined_date from hs_hr_employee, hs_hr_users where hs_hr_employee.emp_number= hs_hr_users.emp_number and hs_hr_employee.emp_number in (select erep_sub_emp_number from hs_hr_emp_reportto ) and emp_status != 'EST000' and isnull(hs_hr_users.userg_id) order by hs_hr_employee.emp_number";
// echo $emp_detail;exit;
//  echo $emp_detail;//exit;
/*********************************************************/
//---------------------For timesheet_id, start_date Table : hs_hr_timesheet-----------------------------------

	//**--Filling Timesheets on employee and status selecion--**//
	// Change: Query changed to join query for $FetchTimsheetid_StartDate and $FetchTimsheetid_StartDate_ALL
	// Purpose: Our orangeHRM fires a join query so here we need to fire the same query.	
	//**---------------------------------------------------------------------------------------------------------------**//
//For timesheet_id, start_date search 
$FetchTimsheetid_StartDate="SELECT distinct a.timesheet_id,a.start_date FROM hs_hr_timesheet a join hs_hr_time_event b on a.timesheet_id = b.timesheet_id WHERE a.employee_id = '".$this->empid."' and a.status = '".$this->timesheetstatus."' order by a.start_date desc";

//For All timesheet_id, start_date search 
$FetchTimsheetid_StartDate_ALL="SELECT distinct a.timesheet_id,a.start_date FROM hs_hr_timesheet a join hs_hr_time_event b on a.timesheet_id = b.timesheet_id WHERE a.employee_id = '".$this->empid."' order by a.start_date desc";

/*******************************************************************************************/
// For finding timesheets which are in status other than approved
// $FetchTimsheetid_StartDate_NonApproved="SELECT distinct a.start_date FROM hs_hr_timesheet a JOIN hs_hr_users b ON a.employee_id = b.emp_number where a.start_date NOT IN(select start_date from hs_hr_timesheet where status=20 and employee_id = ".$this->empid.") AND cast(a.start_date as DATE) > cast(b.date_entered as DATE) AND a.employee_id = ".$this->empid." order by a.start_date desc";
/**
	New change
	Purpose : Resolved joining week time sheet reminder issue
*/
$FetchTimsheetid_StartDate_NonApproved = "SELECT DISTINCT a.start_date, a.status FROM hs_hr_timesheet a JOIN hs_hr_employee b ON a.employee_id = b.emp_number WHERE a.start_date NOT IN ( SELECT start_date FROM hs_hr_timesheet WHERE STATUS = 20 AND employee_id = ".$this->empid.") AND a.start_date >= '<JOIN_DATE>' AND a.employee_id =".$this->empid." ORDER BY a.start_date DESC";

// $FetchTimsheetid_StartDate_NonApproved = "SELECT DISTINCT a.start_date, a.status FROM hs_hr_timesheet a JOIN hs_hr_employee b ON a.employee_id = b.emp_number WHERE a.start_date NOT IN ( SELECT start_date FROM hs_hr_timesheet WHERE STATUS = 20 AND employee_id = ".$this->empid.") AND cast( a.start_date AS DATE ) >= cast(b.date_entered as DATE) AND a.employee_id =".$this->empid." ORDER BY a.start_date DESC";


/**	
	Purpose : Change the query because with old query it returns dates with status 20 and 0. But we need only dats with status 20 (and a.STATUS != 0)
*/
//Org Query
// $fetchapproved = "SELECT DISTINCT a.start_date, a.status FROM hs_hr_timesheet a JOIN hs_hr_employee b ON a.employee_id = b.emp_number WHERE a.start_date IN ( SELECT start_date FROM hs_hr_timesheet WHERE STATUS =20 AND employee_id = ".$this->empid." ) AND cast( a.start_date AS DATE ) > cast( b.joined_date AS DATE ) AND a.employee_id =".$this->empid." ORDER BY a.start_date DESC";

/**
	New change
	Purpose : Resolved joining week time sheet reminder issue
*/
// $fetchapproved = "SELECT DISTINCT a.start_date, a.status FROM hs_hr_timesheet a JOIN hs_hr_employee b ON a.employee_id = b.emp_number WHERE a.start_date IN ( SELECT start_date FROM hs_hr_timesheet WHERE STATUS =20 AND employee_id = ".$this->empid." ) AND cast( a.start_date AS DATE ) >= cast( b.joined_date AS DATE ) AND a.employee_id =".$this->empid." and a.STATUS != 0 ORDER BY a.start_date DESC";

$fetchapproved = "SELECT DISTINCT a.start_date, a.status FROM hs_hr_timesheet a JOIN hs_hr_employee b ON a.employee_id = b.emp_number WHERE a.start_date IN ( SELECT start_date FROM hs_hr_timesheet WHERE STATUS =20 AND employee_id = ".$this->empid." ) AND a.start_date >= '<JOIN_DATE>' AND a.employee_id =".$this->empid." and a.STATUS != 0 ORDER BY a.start_date DESC";


/**
	**************************************
*/
//$FetchTimsheetid_StartDate_NonApproved="SELECT distinct a.start_date FROM hs_hr_timesheet a join hs_hr_time_event b on a.timesheet_id = b.timesheet_id WHERE a.employee_id = '".$this->empid."' AND a.status<>20 order by a.start_date desc";

/**	
	Purpose : Added new query to fetch join date of emp
*/
$FetchJoinedDate = "SELECT joined_date from hs_hr_employee where emp_number=".$this->empid;
/**
	**********************************
*/
$FetchEmail = "SELECT emp_work_email from hs_hr_employee where emp_number=".$this->empid;

$FetchDetails = "SELECT * from hs_hr_employee where emp_number=".$this->empid;
/*******************************************************************************************/
	//**---------------------------------------------------------------------------------------------------------------**//

	//**--Employee selecion based on employee status--**//
	// Change: Four queries are added
	// Purpose: Queries are to fetch employee status and employee list based on selected employee status	
	//**--Filling Timesheets on employee and status selecion--**//
//For current employee detail for non admin users.

/* *******************************************************************************************
Purpose : Supervisor can see timesheets of subordinates
**********************************************************************************************/
$current_emp_detail = " select employee_id,emp_number,emp_lastname,emp_firstname,emp_middle_name,emp_nick_name from hs_hr_employee where emp_number = '".$_SESSION['empID']."'";
//********************************************************************************************

//For employee status select
$emp_status_select = " select estat_code, estat_name from hs_hr_empstat order by estat_name";

//For employees search 
$FetchEmployee="SELECT employee_id,emp_number,emp_firstname,emp_lastname from hs_hr_employee where emp_status='".$this->statcd."' order by emp_number";

//For All employees search 
$FetchEmployee_ALL="SELECT employee_id,emp_number,emp_firstname,emp_lastname from hs_hr_employee order by emp_number";
	//**--Filling Timesheets on employee and status selecion--**//
	/**
 ** Purpose: Query to fetch all employee's supervisors
**/
/**************************************************************************/
$FetchSupervisorEmail = "SELECT `emp_work_email`,`emp_number`,`employee_id`
FROM hs_hr_employee, hs_hr_emp_reportto
WHERE emp_number = erep_sup_emp_number
AND erep_sub_emp_number = ".$this->empid;
/**************************************************************************/
?>
