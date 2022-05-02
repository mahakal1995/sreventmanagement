<?php
//echo "<pre>";
//print_r($res_emp_stat);
//exit;

/**
  * empSartDatepopup.php : Popup window which returns timsheet_id and Start_date value. 
  *
  * This file uses our architecture and ajax script to returns timsheet_id and Start_date value to selectEmployee.php file. 
  *
  * $Id: empSartDatepopup.php,v 1.0 2008/01/09 12:36:46
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Timesheets_Selection
  */

session_start();
?>

<?
//**--Timesheet popup form name change--**//
// Change: Changed form name to frmTSpopup
//**---------------------------------------------------------------------------------------------------------------**//
?>

<script type="text/javascript">
/**
 *  select_this(); takes timesheet_id as argument and returns timsheet_id and Start_date value to selectEmployee.php file.
 * click on list box calls this function.
*/
	function select_this(timesheet_id,employee_id)
	{
		if(window.opener)
		{
//alert(employee_id);

//By : Hiten Patel
//Date : 12-02-2013
//Purpose : open following form
	window.opener.document.forms["frmTimesheet"].elements["txtTimesheetId"].value=timesheet_id; 
	window.opener.document.forms["frmTimesheet"].elements["txtStartDate"].value=frmTSpopup.lb.options[frmTSpopup.lb.selectedIndex].text;
        window.opener.document.forms["frmTimesheet"].elements["txtEmployeeId"].value=employee_id; //hiten patel
        //
        window.opener.document.forms["frmEmp"].elements["TimesheetId"].value=timesheet_id; 
        window.opener.document.forms["frmEmp"].elements["EmployeeId"].value=employee_id; //hiten patel
	window.opener.document.forms["frmEmp"].elements["StartDate"].value=frmTSpopup.lb.options[frmTSpopup.lb.selectedIndex].text;        
        //
        //
//			window.opener.document.forms["frmTimesheet"].elements["txtTimesheetId"].value=timesheet_id; 
//			window.opener.document.forms["frmTimesheet"].elements["txtStartDate"].value=frmTSpopup.lb.options[frmTSpopup.lb.selectedIndex].text;
                        //window.opener.document.forms["frmTimesheet"].elements["txtEmployeeId"].value=employee_id; //hiten patel
			window.close();
                        
                        
		}
		else
		{
			alert('There has been a problem with this page.');
		}
		
	}
	function CheckValue()
	{
	if(frmTSpopup.lb.selectedIndex==-1)
	{
	alert('No Timesheet selected.');
	}
	else
	{
        var employee_id=document.getElementById('txtRepEmpID').value;
        select_this(document.frmTSpopup.lb.value,employee_id);
	}
	}
</script>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>

  <!-- File That contains Ajax script that returns start_date and timesheet_id function -->
 <script type="text/javascript" src="html/js/ajax.js"></script>

<link href="../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<body style="padding-left:4; padding-right:4;">
<form name="frmTSpopup" id="frmEmp" method="post">
	<p>
	<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
	<tr>
		<td>
		<table>
			<tr height="20"><td>&nbsp;</td></tr>
		</table>
		<p>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<?
		//**--Employee selecion based on employee status--**//
		// Change: Added a table row to form
		// Purpose: Row contains employee status selection box. 		
		//**---------------------------------------------------------------------------------------------------------------**//
		?>
		<tr>
			<td>&nbsp;</td>
			<!-- condition placed to hide employment status for individual employees-->
	<!-- condition placed to hide employment status for individual employees-->
<!-- 
     Purpose : Admin can see employment status but the supervisor can't.     
******************************************************************************************-->
  			<? if($_SESSION['isAdmin'] === 'Yes') {?>




<!-- ************************************************************************************ -->
			<? if(isset($_GET['emp'])) {?>
			  <td>Employment Status :&nbsp;</td>
			  <td>
				  <select id="CmbEmployeeStatus" name="CmbEmployeeStatus"  onchange="GetEmpValue(this.value);">
					  <option value="x">---Select Status---</option>
					    <?
					  if(count($res_emp_stat)>0)
					  {
						  for($id=0;$id<count($res_emp_stat);$id++)
						  {?>
					  <option value="<?=$res_emp_stat[$id]['estat_code']?>"><?=$res_emp_stat[$id]['estat_name']?></option>
					  <?
						  }
					  }?>		
				  </select>
			  </td>
			<? } ?>
			<? } ?>
		</tr>
		<tr height="10"><td colspan="3"></td></tr>
		<?
		//**---------------------------------------------------------------------------------------------------------------**//
		?>
  		<tr>
			<td width="12%">&nbsp;</td>
			<td width="28%">Employee :&nbsp;</td>
			<td width="60%">
<!-- 
		* GetValue(); function is in ajax.js file.
		* GetValue() takes CmbEmpName and CmbTimesheetStatus values as arguments.
		* Onchange of  CmbEmpName and CmbTimesheetStatus comboboxes using ajax it returns timsheet_id and Start_date value to se<lectEmployee.php file.
-->					
				
				<select name="txtRepEmpID"  id="txtRepEmpID" onchange="GetValue(this.value,document.frmTSpopup.CmbTimesheetStatus.value);">
					<?
                                        
					if(count($res_emp)>1) {
					?>
					<option value="x">---Select Employee---</option>
                                        
					<?}?>
					<?
					if(count($res_emp)>0)
					{
						for($id=0;$id<count($res_emp);$id++)
						{
                                                    
                                        
                                                    ?>
					<option value="<?=$res_emp[$id]['emp_number']?>"><?=$res_emp[$id]['employee_id']?> - <?=$res_emp[$id]['emp_firstname']?> <?=$res_emp[$id]['emp_lastname']?></option>
					<?
						}
					}
                                        
                                        ?>		
				</select>
  			</td>
		</tr>
		<tr height="10"><td colspan="3"></td></tr>
		<tr>
			<td>&nbsp;</td>
			<td>Timesheet Status :&nbsp;</td>
			<td>
				<select name="CmbTimesheetStatus"  onchange="GetValue(document.frmTSpopup.txtRepEmpID.value,this.value);">
					<option value="x">---Select Status---</option>
                                        <option value="0">Not Submitted</option>
					<option value="10">Submitted</option>
					<option value="20">Approved</option>
					<option value="30">Rejected</option>
					<option value="ALL">ALL</option>
				</select>
			</td>
		</tr>
		<tr height="10"><td colspan="3"></td></tr>
		<tr>
			<td>&nbsp;</td>
			<td valign="top">Timesheets (Start Date) :&nbsp;</td>
			<td>
				
				<select id='lb' name='lb'  multiple='multiple' size='7' style='width: 220px' onclick=''>
				</select>	
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
			
	<input type="button" name="btntimesheet" value="Select Timesheet" onclick = 'CheckValue();' > 
			</td>
		</tr>
		</table>
	

</form>
</body>
</html>
