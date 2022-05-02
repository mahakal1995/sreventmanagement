<?php
/**
  * timesheetReminder.php : Popup window which returns timsheet_id and Start_date value.
  *
  * $Id: timesheetReminder.php,v 1.0 2009/04/17
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Timesheets_Selection
  */

session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>

  <!-- File That contains Ajax script that returns start_date and timesheet_id function -->
<script type="text/javascript" src="html/js/ajax.js"></script>

<script type="text/javascript">



    function checkRemindTime()
    {
        if(document.frmTSpopup.lb.length == 0)
        {
            alert('There is no timesheet to remind about.');
            return false;
        }
        document.frmTSpopup.submit();
    }

</script>

<link href="../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<body style="padding-left:4; padding-right:4;">
<form name="frmTSpopup" id="frmTimesheet" method="post" action="index.php?action=tsFillStartdate&ispost=true">
	<p>
	<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
	<tr>
		<td>
		<table>
			<tr height="20"><td>&nbsp;</tr></td>
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

<!-- 
     Purpose : Admin can see employment status but the supervisor can't.     
******************************************************************************************-->
  			<? if($_SESSION['isAdmin']==='Yes') {?>
<!-- ************************************************************************************ -->
			<? if(!isset($_GET['emp'])) {?>
<? if(isset($_REQUEST['result']) && ($_REQUEST['result']==1 || $_REQUEST['res']==true)) {
       echo "<center><font color=#666666 size=3>Reminder Mail has been sent successfully</font></center><br>";
 } else if(isset($_REQUEST['result']) && ($_REQUEST['result']==0 || $_REQUEST['res']==false)) {
       echo "<center><font color=#666666 size=3>Some problem occurred</font></center><br>";
 } ?>
			  <td>Employment Status :&nbsp;</td>
			  <td>
				  <select id="CmbEmployeeStatus" name="CmbEmployeeStatus"  onchange="GetEmpValue(this.value);">
					  <option value="ALL">---Select Status---</option>
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
				<select name="txtRepEmpID"  onchange="tsGetValue(this.value);">
					<?
					if(count($res_emp)>1) {
					?>
					<option value="x">---Select Employee---</option>	
					<?}?>
					<?
					if(count($res_emp)>0)
					{
						for($id=0;$id<count($res_emp);$id++)
						{?>
					<option value="<?=$res_emp[$id]['emp_number']?>"><?=$res_emp[$id]['employee_id']?> - <?=$res_emp[$id]['emp_firstname']?> <?=$res_emp[$id]['emp_lastname']?></option>
					<?
						}
					}?>		
				</select>
  			</td>
		</tr>
		<tr height="10"><td colspan="3"></td></tr>
		<tr id=id_for_timesheet>
			<td>&nbsp;</td>
			<td valign="top">Timesheets (Start Date) :&nbsp;</td>
			<td>
				<select id='lb' name='lb' size='7' style='width: 220px' onclick='' disabled='disabled'>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">

    <input type="submit" name="btntimesheet" value="Send Reminder" onclick='checkRemindTime(); return false;' >

			</td>
		</tr>
		<tr height="30"><td colspan="3"></td></tr>
		</table>
	</td>
	</tr>
</table>
</form>

</body>
</html>
