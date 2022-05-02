<?
/**
  * process.php : Process Functions
  *
  *   - Functions that are to be executed for a particular action selected are stored in this file. The funcions will be selected for execution by index file depending on URL Parameter values.
  *
  * $Id: process.php,v 1.0 2008/01/02 11:24:24
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Parse_Flow
  */

/**
 * empStartDatepopup: This function Lists out all employee id and name.
 *
 *   - To retireve records. ( Used Function empStartDatepopup::employee() )
 * @see empStartDatepopup::employee()
 *
 */
include_once("config/mailConf.php");
function empStartDatepopup()
{
	/**
 	  * Includes Timesheet selection popup class file.
 	  */
	include "class/class_empStartDatepopup.php";
	
	$obj_empstartdate = new empStartDatepopup();
	//**--Employee selecion based on employee status--**//
	// Change: Called employee_status function
	// Purpose: The function will retrieve all employee statuses to populate the employee status selection box.	
	//**---------------------------------------------------------------------------------------------------------------**//
	$res_emp_stat = $obj_empstartdate->employee_status();
	$res_emp = $obj_empstartdate->employee();
	//**---------------------------------------------------------------------------------------------------------------**//

	//**--Timesheet selection for non admin users--**//
	// Change: If emp url parameter is set, $res_emp is filled using current_employee() function
	// Purpose: For a non admin employee only his detail will get selected in employee selection box. So if current employee is not an admin then only employee selection facility is not allowed for him.	
	//**---------------------------------------------------------------------------------------------------------------**//
	if(isset($_GET['emp']))
	{
		$obj_empstartdate->emp_no=$_GET['emp'];
		$res_emp = $obj_empstartdate->current_employee();
	}
	//**---------------------------------------------------------------------------------------------------------------**//
	/**
 	  * Includes Timesheet selection popup file.
 	  */
	include('empStartDatepopup.php');
	
}


function timesheetReminder()
{
    include "class/class_timesheetReminder.php";

    $obj_tsReminder = new timesheetReminder();

    $res_emp_stat = $obj_tsReminder->employee_status();
    $res_emp = $obj_tsReminder->employee();

    if(isset($_GET['emp']))
    {
        $obj_tsReminder->emp_no=$_GET['emp'];
        $res_emp = $obj_tsReminder->current_employee();
    }

    include('timesheetReminder.php');
}

function timesheetFridayReminder()
{

	include "class/class_timesheetReminder.php";

        $stdate = new timesheetReminder();
		
		$employees = $stdate->fetch_employee();
// 		echo "<pre>"; print_r($employees);echo "</pre>";
		for($k=0;$k<count($employees);$k++)
		{
 			$stdate->empid = $employees[$k]['emp_number'];
 			
			$res_date = $stdate->fetchapproved();			
			$res_date_not = $stdate->fetchnotapproved();
			
			$result_date = array_merge($res_date, $res_date_not);

			$total_date = fetchTotalDate('Friday',$employees[$k]['joined_date']);
			$res_date = get_remain_date ($result_date,$total_date);
			if(count($res_date) > 0)
			{
				$datavalue="";
// 				echo count($res_date);
				for($id=0;$id<count($res_date);$id++)
				{
					if($id==0)
					{
						$datavalue = $res_date[$id]['start_date']."#".$res_date[$id]['status'];
					}
					else
					{
						$datavalue = $datavalue.",".$res_date[$id]['start_date']."#".$res_date[$id]['status'];
					}
				}
// 				echo $datavalue;
				/**				
					Send the reminder if any timesheet is not in approved status
				*/
				$email = $stdate->fetchEmpEmail();
				$emp_email = $email[0]['emp_work_email'];
				$res=send_mail($emp_email, $datavalue, $stdate->empid,false);

				/**
					***********************
				*/
			}
	}
}
function send_mail($email_address,$sheets,$emp_id,$hr=false)
{

	include_once("class/class.phpmailer.inc.php");
	include_once("class/class_timesheetReminder.php");
    
 	global $port,$username,$password,$smtpHost,$hr_address,$path;

	$stdate = new timesheetReminder();

	$stdate->empid = $emp_id;
	$employee_details = $stdate->fetchdetails();
	$employee_name = $employee_details[0]['emp_firstname']." ".$employee_details[0]['emp_lastname'];

	 $templateFile = file_get_contents($path."templates/time/mails/body.txt");

		$txt = $templateFile;

		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);

		$startdate = explode(',',$sheets);
		
		for($k=0;$k<count($startdate);$k++)
		{			
			$data = explode('#',$startdate[$k]);

			$status = $data[1];

 			$date = substr($data[0],0,10);

			if($status == 0)
				$notsubmitted[] = $date;
			if($status == 10)
				$submitted[] = $date;
			if($status == 30)
				$rejected[] = $date;
		}


		$recordTxt = $txtArr[1];
		$recordArr = null;

		if(count($notsubmitted) > 0)
		{
			$recordArr[] = '<br><b>Not submitted:</b><br>';
			for($i=0;$i<sizeof($notsubmitted);$i++)
			{ 
				$recordArr[] = preg_replace(array('/#startdate/'), array($notsubmitted[$i]), $recordTxt);
			}
		}
		if(count($submitted) > 0)
		{
			$recordArr[] .= '<br><b>Submitted:</b><br>';
			for($i=0;$i<sizeof($submitted);$i++)
			{ 
				$recordArr[] = preg_replace(array('/#startdate/'), array($submitted[$i]), $recordTxt);
			}
		}
		if(count($rejected) > 0)
		{
			$recordArr[] .= '<br><b>Rejected:</b><br>';
			for($i=0;$i<sizeof($rejected);$i++)
			{ 
				$recordArr[] = preg_replace(array('/#startdate/'), array($rejected[$i]), $recordTxt);
			}
		}
//        $recordArr[] = preg_replace(array('/#startdate/', '/#employee_name/'), array($this->startdate, $fname),$recordTxt);

// 		$recordTxt = $txtArr[1];
// 		$recordArr = null;
// 		for($i=0;$i<sizeof($startdate);$i++)
// 		{ 
// 			$recordArr[] = preg_replace(array('/#startdate/'), array($startdate[$i]), $recordTxt);
// 		}
		$recordTxt = "";
		if (isset($recordArr)) {
			$recordTxt = join("\r\n", $recordArr);
		}

		$txt = $txtArr[0].$recordTxt.$txtArr[2];
		$txt = preg_replace('/#employee_name/', $employee_name, $txt);
		$body = $txt;

//   echo $body;

		$subject = file_get_contents($path."templates/time/mails/subject.txt");
		
		$mail = new PHPMailer();

		
		if($hr == true)
		{
		/**		 
		 ** Purpose: Implemented to fetch all employee's supervisor and send Timesheet Reminder to all supervisors also
		**/
		/***************************************************************/
		$supervisoremail = $stdate->fetchSupervisorEmail();
			
		$mail->AddAddress($hr_address);
		for($k=0;$k<count($supervisoremail);$k++)
		{
			$mail->AddAddress($supervisoremail[$k]['emp_work_email']);
		}
		$mail->AddCC($email_address);
		/***************************************************************/
		}
		else
		{
			$mail->AddAddress($email_address);
		}

		$mail->Subject = $subject;
		$mail->Body = $body;
 //echo $mail->Body;
            $mail->IsSMTP();
            $mail->Host = $smtpHost;

            $mail->Username = $username;
            $mail->Password = $password; 
            $mail->Port = $port;
			$mail->FromName = "E-Notification";
        	$mail->From = $username;
        	$mail->IsHTML(true);

 		if (!$mail->Send()) {
             $message = 'Sorry, there was a problem trying to send the e-mail. Please try again later.';
            }else{
         	$message = 'E-mail has been successfuly sent to '.$email_address;
        		}
// 		echo $message;
		return true;
}

function timesheetMondayReminder()
{
		include "class/class_timesheetReminder.php";
        $stdate = new timesheetReminder();
		
		$employees = $stdate->fetch_employee();
 

		for($k=0;$k<count($employees);$k++)
		{
			$stdate->empid = $employees[$k]['emp_number'];

// 			$res_date = $stdate->fetchStartDate();
			$res_date = $stdate->fetchapproved();
						
			$res_date_not = $stdate->fetchnotapproved();
			$result_date = array_merge($res_date, $res_date_not);
			$total_date = fetchTotalDate('Monday',$employees[$k]['joined_date']);
			$res_date = get_remain_date ($result_date,$total_date);
// 			echo $employees[$k]['emp_firstname'].".....send mail<br/>";
// 			echo "<pre>";print_r($res_date);echo "</pre>";
// 			echo $employees[$k]['emp_firstname']."complete send mail<br/>";
			/**
				*********************************************
			*/
			if(count($res_date)>0)
			{
				$datavalue="";
				for($id=0;$id<count($res_date);$id++)
				{
					if($id==0)
					{
						$datavalue = $res_date[$id]['start_date']."#".$res_date[$id]['status'];
					}
					else
					{
						$datavalue = $datavalue.",".$res_date[$id]['start_date']."#".$res_date[$id]['status'];
					}
				}				
				$email = $stdate->fetchEmpEmail();
				$emp_email = $email[0]['emp_work_email'];
// 				echo $employees[$k]['emp_firstname']."send mail<br/>";
				$res  = send_mail($emp_email, $datavalue, $stdate->empid,true);
				/**
				*****************************
				*/
			}
		}
}

function tsFillStartdate()
{

     if(isset($_REQUEST['ispost']))
     {
        include "class/class_timesheetReminder.php";
        $stdate = new timesheetReminder();
        $stdate->empid = $_REQUEST['txtRepEmpID'];

        $res_date = $stdate->fetchStartDate();

        if(count($res_date)>0)
        {
            $datavalue="";
            for($id=1;$id<count($res_date);$id++)
            {
                if($datavalue=="")
                {
                    $datavalue = $res_date[$id]['start_date'];
                }
                else
                {
                    $datavalue = $datavalue.",".$res_date[$id]['start_date'];
                }
            }
        }

        if($datavalue=="")
        {
            $response="";
        }
        else
        {
            $part = explode(',',$datavalue);
            for($i=0;$i<sizeof($part);$i++)
               $part[$i] = substr($part[$i],0,10);
            $response = implode(',',$part);
        }

        $email = $stdate->fetchEmpEmail();
        $emp_email = $email[0]['emp_work_email'];
// echo "header('location:../lib/controllers/CentralController.php?timecode=Time&action=send_reminder&emp_email='.$emp_email.'&startdate='.$response.'&employeeId='.$stdate->empid)";exit;
header('location:../lib/controllers/CentralController.php?timecode=Time&action=send_reminder&emp_email='.$emp_email.'&startdate='.$response.'&employeeId='.trim($stdate->empid));exit;
        
     }
     else
     {
        include "class/class_timesheetReminder.php";

        $stdate = new timesheetReminder();
        $stdate->empid = $_GET['empid'];
        $res_date = $stdate->fetchStartDate();

        if(count($res_date)>0)
        {
            $datavalue="";
            for($id=1;$id<count($res_date);$id++)
            {
                if($datavalue=="")
                {
                    $datavalue = $res_date[$id]['timesheet_id'].",".$res_date[$id]['start_date'];
                }
                else
                {
                    $datavalue = $datavalue.";".$res_date[$id]['timesheet_id'].",".$res_date[$id]['start_date'];
                }

            }
        }

        if($datavalue=="")
        {
            $response="";
        }
        else
        {
            $response=$datavalue;
        }
        echo $response; 
    }
}
/************************************************************************************************************************/

/**
 * AjaxTimesheet: This function fills all timesheet_id and start_date in selection box.
 *
 * Timesheet id and startdate are fetched based on employee id and time sheet status criteria. ( Used Function AjaxTimesheet::FetchTimsheetid_StartDate() )
 *
 * @see AjaxTimesheet::FetchTimsheetid_StartDate() 
 *
 */
function AjaxTimesheet()
{

include "class/class_AjaxTimesheet.php";

/**
   $obj_AjaxTimesheet is an object variable of class file class_AjaxTimesheet.php file.
   In ajax.js we have given employee_id and status values according to which  AjaxTimesheet() will fetch data, so here
  $obj_AjaxTimesheet->empid and $obj_AjaxTimesheet->timesheetstatus are object variables which gets that url values.
**/

$obj_AjaxTimesheet = new AjaxTimesheet();
$obj_AjaxTimesheet->empid =$_GET["empid"];
$obj_AjaxTimesheet->timesheetstatus=$_GET["timesheetstatus"];
$res_dateid = $obj_AjaxTimesheet->FetchTimsheetid_StartDate();

if(count($res_dateid)>0)
{
	$datavalue="";
	for($id=0;$id<count($res_dateid);$id++)
	{
		if($datavalue=="")
		{
			$datavalue = $res_dateid[$id]['timesheet_id'].",".$res_dateid[$id]['start_date'];
		}
		else
		{
			$datavalue = $datavalue.";".$res_dateid[$id]['timesheet_id'].",".$res_dateid[$id]['start_date'];
		}
		
	}
}

if($datavalue=="")
{
	$response="";
}
else
{
	$response=$datavalue;
}
//The variable echoed here will be caught by ajax.js file.
echo $response;

}


	//**--Employee selecion based on employee status--**//
	// Change: New function AjaxEmployee() is added
	// Purpose: To Fetch employee records with selected status and fill it to the employee selection box using Ajax
	// Date: 10-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
/**
 * AjaxEmployee: This function fills employee details in employee selection box depending on selected employee status
 *
 * employee details are fetched based on employee status criteria. ( Used Function AjaxTimesheet::FetchEmployee() )
 *
 * @see AjaxTimesheet::FetchEmployee() 
 *
 */
function AjaxEmployee()
{

include "class/class_AjaxTimesheet.php";
/**
   $obj_AjaxEmployee is an object variable of class file class_AjaxTimesheet.php file.
   In ajax.js we have given employee status value according to which  FetchEmployee() will fetch data, 
**/

$obj_AjaxEmployee = new AjaxTimesheet();
$obj_AjaxEmployee->statcd =$_GET["statcd"];
$res_dateid = $obj_AjaxEmployee->FetchEmployee();

if(count($res_dateid)>0)
{
	$datavalue="";
	for($id=0;$id<count($res_dateid);$id++)
	{
		if($datavalue=="")
		{
			$datavalue = $res_dateid[$id]['emp_number'].",".$res_dateid[$id]['employee_id']." - ".$res_dateid[$id]['emp_firstname']." ".$res_dateid[$id]['emp_lastname'];
		}
		else
		{
			$datavalue = $datavalue.";".$res_dateid[$id]['emp_number'].",".$res_dateid[$id]['employee_id']." - ".$res_dateid[$id]['emp_firstname']." ".$res_dateid[$id]['emp_lastname'];
		}
		
	}
}

if($datavalue=="")
{
	$response="";
}
else
{
	$response=$datavalue;
}
//The variable echoed here will be caught by ajax.js file.
echo $response;

}



function otconfig()
{
	include "class/class_otconfig.php";
	$otconfig = new otconfig();              
        $list_ot_config = $otconfig->list_ot_config();        
 	include('otconfig.php');
    
}


function del_otconfig()
{
	include "class/class_otconfig.php";
	$otconfig = new otconfig();      
           
        $delete_ot_config = $otconfig->delete_ot_config();        
        header("Location:index.php?action=otconfig");
        exit;
    
}

function edit_otconfig()
{
	include "class/class_otconfig.php";
	$otconfig = new otconfig();    
//echo "edit_otconfig in process.php";
//        echo $_GET['edit'];
        if(isset($_GET['edit']))
        {
            $edit_ot_config = $otconfig->edit_ot_config(); 
             $list_ot_config = $otconfig->list_ot_config();   
            include('otconfig.php');exit;
        }
        else
        {
            $insert_ot_config = $otconfig->insert_ot_config();  
           
        }
                        
        
//        exit;
        header("Location:index.php?action=otconfig");
        exit;
    
}


/*****************************************************************************************************************************************************/



//#################################################################################

function x_week_range($date) {
$ts = strtotime($date);
    $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
    return array(date('Y-m-d', $start),
                 date('Y-m-d', strtotime('next saturday', $start)));
}

function fetchTotalDate($day,$emp_join_date)
{
	list($start_date, $end_date) = x_week_range($emp_join_date);
	$date = explode ("-",$start_date);
	$emp_cal_start_date = date("Y-m-d",mktime(0, 0, 0, $date[1]  , $date[2]+1, $date[0]));
	$emp_week_start_date  = explode("-",date("Y-m-d",mktime(0, 0, 0, $date[1]  , $date[2]+1, $date[0])));
	
	//Current date
	list($start_date, $end_date) = x_week_range(date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y"))));
	$date = explode ("-",$start_date);
	$current_week_start_date  = explode("-",date("Y-m-d",mktime(0, 0, 0, $date[1]  , $date[2]+1, $date[0])));
	
	$days = gregoriantojd($current_week_start_date[1],$current_week_start_date[2],$current_week_start_date[0]) - gregoriantojd($emp_week_start_date[1],$emp_week_start_date[2],$emp_week_start_date[0]);
	$total_week = ceil($days / 7);
	// echo $total_week;
	
	if($day == "Monday"){
 		$total_week = $total_week-1;
 	}
	/**
		***********************************************************************
	*/
	$increment = 1;         //  # of weeks to increment by
	
	$startdate = strtotime($emp_cal_start_date);
	
	
	$all_weeks = array();
	
	for ($week = 0; $week <= $total_week; $week += $increment)
	{
	$week_data = array();
	$week_data['start'] = strtotime("+$week weeks", $startdate);
	$week_data['end'] = strtotime("+6 days", $week_data['start']);
	
	$all_weeks[$week + 1] = $week_data;
	}
	
	$arr = array();
	$i = 0;
	foreach ($all_weeks as $week => $week_data)
	{
		$arr[$i] = date("Y-m-d", $week_data['start']);
		$i++;
	}
	return $arr;
}

/**
	Purpose : $arr = Approved + Not Approved time sheets dates array
		$full_arr = Array of Current Date - Joined Date of array
		Return the not approved status time sheets with status
*/
function get_remain_date($arr,$full_arr)
{
	$final_arr = array();
	$k = 0;
	for($i=1;$i<count($full_arr);$i++)
	{
		$date = $full_arr[$i]." 00:00:00";
		$flag = 0;
		for($j=0;$j<count($arr);$j++)
		{
			
			if($date == $arr[$j]['start_date'])
			{
				$flag = 1;
				break;
			}else{
				$flag = 0;
			}
		}
		if($flag == 1 )
		{
			if($arr[$j]['status'] != 20)
			{
				$final_arr[$k] = array("start_date" => $date,"status" => $arr[$j]['status']);
				$k++;
			}
		}
		if($flag == 0)
		{
			$final_arr[$k] = array("start_date" => $date,"status" => 0);
			$k++;			
		}
	}
	return $final_arr;
}
//**---------------------------------------------------------------------------------------------------------------**//

//#######################################################################################################
//**---------------------------------------------------------------------------------------------------------------**//
?>