<?php /**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */ ?>

<?

//rushika and jugni 20130626
$_SESSION['index_patch']="";
?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>


<?php
$noOfColumns = sizeof($sf_data->getRaw('rowDates'));
$width = 350 + $noOfColumns * 75;

?>
<?php echo stylesheet_tag('../orangehrmTimePlugin/css/viewTimesheetSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/viewTimesheet'); ?>
<?php
use_stylesheet('../../../themes/orange/css/style.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
?>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<!--By:Jagruti Sangani
Date:2013-03-23
Purpose:To show the user's dropdown under login supervisor.
Change:Added new feature
Note:======= this line indicate start and end portion
-->
<!--==========================================================================================================================-->

 <?
    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;

        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());
 ?>
<!--Jagruti-->
<!--==========================================================================================================================    -->
<!-- +++++++++++++++++Rushika++++++++++++++++++ -->
<?php $month=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nev','Dec');?>
<!-- ++++++++++++++++++++++++++++++++++++++++++ -->
<?php $actionName = sfContext::getInstance()->getActionName(); ?>
<?php if (isset($successMessage)) { ?>
    <?php echo templateMessage($successMessage); ?>
<?php } ?>
<?php if (isset($messageData)): ?>
    <?php echo templateMessage($messageData); ?>
<?php else: ?>

    <table id="headingTable">

        <!--    conifigure the heading accoding to the timesheet period using the num of columns-->
        <tr>
	   <?
            $secret =md5(php_uname());
            ?>
            <?php if (isset($employeeName)): ?>
                <td id="headingText"><?php echo __('Timesheet for')." " . $employeeName . " ".__('for') . " " . __($headingText) . " ";
        echo $dateForm['startDates']->render(array('onchange' => 'clicked(event)')); ?>
                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_CREATE, $sf_data->getRaw('allowedToCreateTimesheets'))): ?>
                        <input type="button" class="addTimesheetbutton" name="button" id="btnAddTimesheet"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Add Timesheet') ?>" />
                           <?php endif; ?>
 <!--Jagruti-->
<!--========================================================================================================================================================-->
         <?
            if (isset($toggleDate))
                $start_date=$toggleDate;
            else
                $start_date = $timesheet->getStartDate();

            $end_date=date('Y-m-d',strtotime("+6 day",strtotime($start_date)));

//            echo "start->".$start_date." end->".$end_date;
            $employeeId = $timesheet->getEmployeeId();
            $secret =md5(php_uname());

            $sql="SELECT employee_id,COUNT( timesheet_item_id ) AS count
                    FROM  `ohrm_timesheet_item`
                    WHERE DATE >= "."'".$start_date."'"."
                    AND DATE <= "."'".$end_date."'"."
                    GROUP BY employee_id";
            $sql1 = mysql_query($sql);
            $arr=array();
            while($row= mysql_fetch_object($sql1))
            {
                $arr[]=$row;
            }
           if($_SESSION['isAdmin']=='Yes')
                 $query="SELECT emp_number,CONCAT(emp_firstname,' ',emp_lastname)as name FROM hs_hr_employee";
           else
                 $query="SELECT t1.emp_number,CONCAT(t1.emp_firstname,' ',t1.emp_lastname) as name FROM hs_hr_emp_reportto t, hs_hr_employee  t1 WHERE t.erep_sup_emp_number=".$_SESSION['empNumber']." AND t.erep_sub_emp_number=t1.emp_number";
           $res = mysql_query($query);
        ?>
                    <div style="float:right;">
                        <form action="/fcs/symfony/web/index.php/time/viewEmployeeTimesheet" id="employeeSelectForm" method="post" >
                            <input class="inputFormatHint" id="employee" type="hidden" name="time[employeeName]" value="Type for hints..." />
                            <input type="hidden" name="time[employeeId]" value="2" id="time_employeeId" />
                            <input type="hidden" name="time[_csrf_token]" value="<?=$secret?>" id="time__csrf_token" />
                            <input type="hidden" name="start_date" value="" id="start_date"/>
                            <input type="hidden" name="end_date" vallue="" id="end_date"/>
                            <select name="emp_id" id="emp_id" value ="" onchange="goto_employee_sheet(this.value)">
<!--                            <option value=""> --Select Employee--</option>-->
                            <?

                            while($row= mysql_fetch_object($res))
                            {
                                $count=0;
                               foreach($arr as $key=>$value)
                               {
                                   if($value->employee_id==$row->emp_number)
                                   {
                                       $count=$value->count;
                                       break;
                                   }
                               }
                            ?>
                            <option value="<?=$row->emp_number?>" <?if($employeeId==$row->emp_number){?>selected="true"<?}if($count<=0){?> style="background-color:#FFA28D;"<?}?>>
                                     <?echo $row->name;?>
                               </option>
                            <?}?>
                        </select>
                            </form>
                    </div>
<!--========================================================================================================================================================-->

                </td>

            <?php else: ?>
                <td id="headingText"><?php echo __('Timesheet for') . " " . __($headingText) . " ";
        echo $dateForm['startDates']->render(array('onchange' => 'clicked(event)')); ?>
                    <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_CREATE, $sf_data->getRaw('allowedToCreateTimesheets'))): ?>
                        <input type="button" class="addTimesheetbutton" name="button" id="btnAddTimesheet"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Add Timesheet') ?>" />
                           <?php endif; ?>
                </td>


            <?php endif; ?>
        </tr>
    </table>


    <div id="createTimesheet">
        <br class="clear"/>
        <form  id="createTimesheetForm" action=""  method="post">
            <?//php echo $createTimesheetForm['_csrf_token']; ?>
            <?//php echo $createTimesheetForm['date']->renderLabel(__('Select a Day to Create Timesheet')); ?>
            <?//php echo $createTimesheetForm['date']->render(); ?>
<!--             <input id="DateBtn" type="button" name="" value="" class="calendarBtn"style="display: inline;float:none;margin-bottom:-4px;margin-left:4px;"/> -->
            <?php //echo $createTimesheetForm['date']->renderError() ?>
<!-- jugni have made chages date:20130621 ============================================================================================================-->
             <?php echo '&nbsp &nbsp &nbsp &nbsp &nbsp Select Month and Year to create Timesheet: &nbsp ';?>
              <select id="drpmonth" name="drpmonth">
            <?php

            foreach($month as $key=>$mon)
            {
            if($mon==date("M"))
            {
            ?>
            <option value="<?php echo $key+1;?>" selected="selected"><?php echo $mon;?></option>
            <?php } else{?>
            <option value="<?php echo $key+1;?>"><?php echo $mon;?></option>
            <?}}?>
            </select>
            <?php echo '&nbsp';?>
            <select id="drpyear" name="drpyear">
            <?php for($i=2012;$i<=date("Y")+1;$i++)
            {
            if($i==date("Y"))
	    {
	      ?>
	      <option value="<?php echo $i;?>" selected="selected"><?php echo $i;?></option>
	      <?php
	    }
	    else
	    {
            ?>

            <option value="<?php echo $i;?>"><?php echo $i;?></option>
            <?}}?>
            </select>
            <?php echo '&nbsp';?>
            <input type="button" class="submitbutton" name="btnCreateTimesheet" id="btnCreateTimesheet"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Submit') ?>" />
<!--==============================================================================================================================================     -->
            <br class="clear"/>
        </form>

    </div>

    <div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>
    <div class="outerbox" style="width: <?php echo $width . 'px' ?>;">
        <div class="maincontent">
            <table  border="0" cellpadding="5" cellspacing="0" class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <td id="projectColumn" ><?php echo __("Project Name") ?></td>
                        <td id ="activityColumn" ><?php echo __("Activity Name") ?></td>

                        <?

                        /**
                            By:kartik gondalia
                            Date:23-03-2013
                            Purpose:Add function for config variables,style for OT,with 2nd and 4th saturday
                            Keep Original line here if:-
                        */

                        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
                        mysql_select_db($db_name) or die(mysql_error());

                         $employeeId = $timesheet->getEmployeeId();

//                        echo $config->emp_work_station."<br><br>";
                        $date_get_conf= explode(",", $config->emp_work_station);

                        $query="SELECT work_station FROM hs_hr_employee where emp_number='".$employeeId."' and  work_station IN (".$config->emp_work_station.")";
                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){

                            $work_station[]=$row;
                        }
                        $work_station=$work_station[0]->work_station;

//                        echo"<pre>";echo $work_station;echo"</pre>";
//                        echo"<pre>";print_r($timesheetItemObjects);echo"</pre>";
//                        echo "======>".$config->emp_work_hours;


                         $query="SELECT * FROM ohrm_employee_work_shift where emp_number=".$employeeId;

                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){
                            $work_shift_id=$row->work_shift_id;
                        }


                        $query="SELECT * FROM ohrm_work_shift where id=".$work_shift_id;
                        $res = mysql_query($query);

                        while($row= mysql_fetch_object($res)){
                            $hours_per_day=$row->hours_per_day;
                        }

                        if($hours_per_day=="")
                        {
                            $hours_per_day = $config->emp_work_hours;
                        }
                        else
                        {
                            $hours_per_day=$hours_per_day;
                        }
                        $hours_per_day=3600*$hours_per_day;

                         $hours_per_day=$timeService->convertDurationToHours($hours_per_day) ;

                        $hours_per_day=  explode(":", $hours_per_day);
                        $hours_per_day=  implode(".", $hours_per_day);
//                        echo"<pre>";print_r($hours_per_day);echo"</pre>";


                        $query="SELECT * FROM ohrm_holiday where compensate!=1";
                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){
                            $holiday[]=$row->date;
                            $yr= date('Y',strtotime($row->date));
                            $current_yr= date('Y');
                            if(($current_yr > $yr) && ($row->recurring==1))
                            {
                                $exp_holiday=  explode("-",$row->date);
                                $exp_holiday[0]=$current_yr;
                                $holiday_impload=  implode("-", $exp_holiday);
                                $final_holiday[]= $holiday_impload;
                            }
                        }
                        $allholidays=array_merge($final_holiday,$holiday);
                         //echo"<pre>";print_r($allholidays);echo"</pre>";

/*==========================================================================================================================*/
//Name:sangani jagruti
//date:2013-12-14
//purpose:add saturday as working day

            $query="SELECT date FROM ohrm_holiday where compensate=1";
                        $res = mysql_query($query);
                        $working_day=array();
                        while($row= mysql_fetch_object($res))
                        {
                            $working_day[]=$row->date;
                        }

                        ?>


                        <?php foreach ($rowDates as $data): ?>
                        <!--
                            By:kartik gondalia
                            Date:23-03-2013
                            Purpose:Initialize for OT and ST
                            Keep Original line here if:-
                       -->
                            <td>
                                <span  style="padding-left:30px;margin-right:10px;"><?php echo __(date('D', strtotime($data))); ?></span>
                                <br/>
                                <span style="padding-left:12px;"><?php echo date('j', strtotime($data)); ?></span>
                                 <? if((in_array($work_station,$date_get_conf))){ ?>
                                <br/>
                                <div style="margin-left:9px;">
                                    <span style="margin-right:8px;"><?php  echo "ST"; ?></span>
                                    <span style="margin-left:7px;"><?php  echo "OT"; ?></span>
                                </div>
                                <?}?>
                                <? //echo"<pre>";print_r($data);echo"</pre>";   ?>
          <!--  ===============================================================================================================    -->
                            </td>

                            <td class="commentIcon"></td>
                        <?php endforeach; ?>

                        <td><?php echo __("Total") ?></td>
                    </tr>
                </thead>
                <tr><td id="noRecordsColumn" colspan="100"></td></tr>
                <?php if (isset($toggleDate)): ?>
                    <?php $selectedTimesheetStartDate = $toggleDate ?>
                <?php else: ?>
                    <?php $selectedTimesheetStartDate = $timesheet->getStartDate() ?>
                <?php endif; ?>


<?php
//jagruti add logic for get the second and fourth saturday
$month_yr=explode("-", $selectedTimesheetStartDate );
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=1;
                        $myDays=Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');
                        $sat_second;
                        $sat_fourth;



                        switch($day)
                        {
                        case "Sunday":
                                $sat_second = $yea.'-'.$mon.'-07';
                                $sat_fourth = $yea.'-'.$mon.'-21';
                                break;
                        case "Monday":
                                $sat_second = $yea.'-'.$mon.'-06';
                                $sat_fourth = $yea.'-'.$mon.'-20';
                                break;
                        case "Tuesday":
                                $sat_second = $yea.'-'.$mon.'-05';
                                $sat_fourth = $yea.'-'.$mon.'-19';
                                break;
                        case "Wednesday":
                                $sat_second = $yea.'-'.$mon.'-04';
                                $sat_fourth = $yea.'-'.$mon.'-18';
                                break;
                        case "Thursday":
                                $sat_second = $yea.'-'.$mon.'-03';
                                $sat_fourth = $yea.'-'.$mon.'-17';
                                break;
                        case "Friday":
                                $sat_second = $yea.'-'.$mon.'-02';
                                $sat_fourth = $yea.'-'.$mon.'-16';
                                break;
                        case "Saturday":
                                $sat_second = $yea.'-'.$mon.'-01';
                                $sat_fourth = $yea.'-'.$mon.'-15';
                                break;
                        default:
                                break;
                        }
?>
                <?php if ($timesheetRows == null) : ?>
                    <!-- colspan should be based on  the fields in a timesheet-->
                    <tr>
                        <td id="noRecordsColumn" colspan="100"><br><?php echo __("No Records Found") ?></td>
                    </tr>

                <?php else: ?>
                    <?php $class = 'odd'; ?>

                    <?php foreach ($timesheetRows as $timesheetItemRow): ?>
                        <?php if ($format == '1') { ?>
                            <?php $total = '0:00'; ?>
                        <?php } ?>
                        <?php if ($format == '2') { ?>
                            <?php $total = 0; ?>
                        <?php } ?>

                        <tr class="<?php echo $class; ?>">
                            <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>
				<td id="columnName"><?php echo str_replace("##", "", html_entity_decode($timesheetItemRow['projectName'])); ?>
                            <td id="columnName"><?php echo html_entity_decode($timesheetItemRow['activityName']); ?>
                                <?php foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects): ?>
                                    <?php if ($format == '1') { ?>
                                    <td class="duration">

                                        <span><?php echo ($timesheetItemObjects->getDuration() == null ) ? "0:00" : $timesheetItemObjects->getConvertTime(); ?></span>
                                        <!--
                                            By:kartik gondalia
                                            Date:23-03-2013
                                            Purpose:Initialize for OT and ST
                                            Keep Original line here if:-
                                        -->
                                         <? if((in_array($work_station,$date_get_conf))){ ?>
                                            <span  style="margin-left:12px;"><?php echo ($timesheetItemObjects->getDuration_ot() == null ) ? "0:00" : $timesheetItemObjects->getConvertTime_ot(); ?></span>
                                        <?}?>
                                         <!-- =======================================================================================================================  -->

                                    </td>

                                    <td class="commentIcon">
                                    <?php
                                    if ($timesheetItemObjects->getComment() != null) {
                                       echo image_tag('callout.png',
                                                 array('id' => 'callout_'. $timesheetItemObjects->getTimesheetItemId(),
                                                       'class' => 'icon'));
                                    } ?>
                                    </td>
                                <?php } ?>
                                <?php if ($format == '2') { ?>
                                    <td class="duration">
                                        <span><?php echo ($timesheetItemObjects->getDuration() == null ) ? "0.00" : $timesheetItemObjects->getDuration(); ?></span>

<!--                                        kartik-->
                                        <? if((in_array($work_station,$date_get_conf))){ ?>
                                            <span><?php echo ($timesheetItemObjects->getDuration_ot() == null ) ? "0.00" : $timesheetItemObjects->getConvertTime_ot(); ?></span>
                                        <?}?>
 <!--                                        kartik-->
                                        </td>
                                    <td class="commentIcon">
                                    <?php
                                      if ($timesheetItemObjects->getComment() != null) {
                                          echo image_tag('callout.png',
                                                 array('id' => 'callout_'. $timesheetItemObjects->getTimesheetItemId(),
                                                       'class' => 'icon'));

                                      } ?>
                                    </td>
                                <?php } ?>
                                <?php if ($format == '1') { ?>
                                    <?php $total+=$timesheetItemObjects->getDuration(); ?>
                                     <? if((in_array($work_station,$date_get_conf))){ ?>
                                        <?php $total+=$timesheetItemObjects->getDuration_ot(); ?>
                                    <?}?>
                                <?php } ?>

                                <?php if ($format == '2') { ?>
                                    <?php $total+=$timesheetItemObjects->getConvertTime(); ?>
                                     <? if((in_array($work_station,$date_get_conf))){ ?>
                                        <?php $total+=$timesheetItemObjects->getConvertTime_ot(); ?>
                                    <?}?>
                                <?php } ?>
                            <?php endforeach; ?>

                            <?php if ($format == '1') { ?>
                                <td id= "total"><?php echo $timeService->convertDurationToHours($total) ?><td>
                                <?php } ?>
                                <?php if ($format == '2') { ?>
                                <td id="total"><?php echo number_format($total, 2, '.', ''); ?><td>
                                <?php } ?>


                        </tr>

                    <?php endforeach; ?>
                    <tr><td colspan="100"></tr>
                    <tr class="even">
                        <td id="totalVertical"><?php echo __('Total'); ?></td>
                        <td></td>
                        <?php if ($format == '1') { ?>
                            <?php $weeksTotal = '0:00' ?>
                        <?php } ?>
                        <?php if ($format == '2') { ?>
                            <?php $weeksTotal = 0.00 ?>
                        <?php } ?>
                        <?php foreach ($rowDates as $data): ?>
                            <?php if ($format == '1') { ?>
                                <?php $verticalTotal = '0:00'; ?>
                                <?php $verticalTotal_sum = '0:00'; ?>
                            <?php } ?>
                            <?php if ($format == '2') { ?>
                                <?php $verticalTotal = 0.00; ?>
                                <?php $verticalTotal_sum = '0:00'; ?>
                            <?php } ?>



                            <?php foreach ($timesheetRows as $timesheetItemRow): ?>
                                <?php foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects): ?>
                                    <?php if ($data == $timesheetItemObjects->getDate()): ?>
                                        <?php if ($format == '1') { ?>
                                            <?php $verticalTotal+=$timesheetItemObjects->getDuration(); ?>

                                            <?php $verticalTotal_sum+=$timesheetItemObjects->getDuration(); ?>
                                            <? if((in_array($work_station,$date_get_conf))){ ?>
                                                <?php $verticalTotal+=$timesheetItemObjects->getDuration_ot(); ?>
                                            <?}?>


                                        <?php } ?>

                                        <?php if ($format == '2') { ?>
                                            <?php $verticalTotal+=$timesheetItemObjects->getConvertTime(); ?>
                                            <? if((in_array($work_station,$date_get_conf))){ ?>
                                                <?php $verticalTotal+=$timesheetItemObjects->getConvertTime_ot(); ?>
                                            <?}?>
                                        <?php } ?>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>



                            <?php if ($format == '1') { ?>

                        <?
                        $sum_st=$timeService->convertDurationToHours($verticalTotal_sum);
                        $sum_st=  explode(":", $sum_st);
                        $sum_st=  implode(".", $sum_st);
                        $data == $timesheetItemObjects->getDate();


				$month_yr=explode("-", $data );
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=$month_yr[2];
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');


//                        echo"<br>".$hours_per_day."-------".$sum_st;
                        ?>
<!--jagruti add condition            -->
<!--Name:jagruti sangani
Date:2013-12-14
Purpose:Add saturday as working day-->

<!--
                                Change by : Rushika Patel
                                Date : 17-12-2013
                                Change : make sunday as working day-->

    <td id ="totalVerticalValue"><?php if($sum_st<($hours_per_day) && (!in_array($data, $allholidays)) && ((($data!=$sat_second) ||  (in_array($sat_second,$working_day))) && (($data!=$sat_fourth) ||  (in_array($sat_fourth,$working_day)))) && ($data!=$selectedTimesheetStartDate ||  (in_array($selectedTimesheetStartDate,$working_day))))
        {
            echo  "<span style=color:red;>".$timeService->convertDurationToHours($verticalTotal);
        }
    else{echo $timeService->convertDurationToHours($verticalTotal);} ?> </td>
<!--                                 <td id ="totalVerticalValue_sum"><?//php if(($hours_per_day) <($timeService->convertDurationToHours($verticalTotal_sum))){echo $timeService->convertDurationToHours($verticalTotal_sum);}else{ echo  "<span style=color:red;>".$timeService->convertDurationToHours($verticalTotal_sum);} ?> </td>-->
                            <?php } ?>
                            <?php if ($format == '2') { ?>
                                <td id ="totalVerticalValue"><?php echo number_format($verticalTotal, 2, '.', ''); ?> </td>
                            <?php } ?>

                            <td></td>

                            <?php $weeksTotal+=$verticalTotal; ?>
                        <?php endforeach; ?>
                        <?php if ($format == '1') { ?>
                            <td id="total"><?php echo $timeService->convertDurationToHours($weeksTotal); ?></td>
                        <?php } ?>
                        <?php if ($format == '2') { ?>
                            <td id="total"><?php echo number_format($weeksTotal, 2, '.', ''); ?></td>
                        <?php } ?>
                        <td></td></tr>
                <?php endif; ?>

            </table>


            <form id="timesheetFrm"  method="post">

                <?php echo $formToImplementCsrfToken['_csrf_token']; ?>

                <div class="formbuttons">

                    <div><h4><?php echo __('Status').': ' ?><?php echo __(ucwords(strtolower($timesheet->getState()))); ?></h4></div>
                    <br class="clear">
<?php
                      if(count($sf_data->getRaw('allowedActions')) > 0)
                      {
                       if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_MODIFY, $sf_data->getRaw('allowedActions'))) : ?>
                        <input type="submit" class="editbutton" name="button" id="btnEdit"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Edit'); ?>" />
                    <?php endif;}
                    else
                    {?>
                         <input type="submit" class="editbutton" name="button" id="btnEdit"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Edit'); ?>" />
                   <? }

                    ?>
                    <?php
                    if(count($sf_data->getRaw('allowedActions')) > 0)
                      {
                    if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT, $sf_data->getRaw('allowedActions'))) : ?>
                        <input type="button" class="submitbutton" name="button" id="btnSubmit"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Submit'); ?>" />
                    <?php endif;}
                    else
                    {?>
                        <input type="button" class="submitbutton" name="button" id="btnSubmit"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Submit'); ?>" />
                   <?}
                    ?>
                    <?php
                    if(count($sf_data->getRaw('allowedActions')) > 0 )
                      {
                    if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_RESET, $sf_data->getRaw('allowedActions'))) : ?>
                        <input type="button" class="resetButton"  name="button" id="btnReset"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Reset') ?>" />
                        <br class="clear"/>
                    <?php endif;}
                    else
                    {?>
<!--                        <input type="button" class="resetButton"  name="button" id="btnReset"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php// echo __('Reset') ?>" />
                        <br class="clear"/>-->
                    <?}
                    ?>
                    <br class="clear"/>
                    <br class="clear"/>
                    <div>
                        <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_APPROVE, $sf_data->getRaw('allowedActions')) || (in_array(WorkflowStateMachine::TIMESHEET_ACTION_REJECT, $sf_data->getRaw('allowedActions')))) : ?>

                            <div class="commentHeading">
                                <b><?php echo __("Comment") ?></b>
                            </div>
                            <textarea name="Comment" id="txtComment" rows="3" cols="70" onkeyup="validateComment()"></textarea>

                        <?php endif; ?>
                        <div id="actionBtns" style="padding-top: 3px">
                            <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_APPROVE, $sf_data->getRaw('allowedActions'))): ?>
                                <input type="button" class="approvebutton" name="button" id="btnApprove"
                                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                       value="<?php echo __('Approve') ?>" />


                            <?php endif; ?>


                            <?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_REJECT, $sf_data->getRaw('allowedActions'))) : ?>


                                <input type="button" class="rejectbutton"  name="button" id="btnReject"
                                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                       value="<?php echo __('Reject') ?>" />
                                <br class="clear"/>


                            <?php endif; ?>

                        </div>

                    </div>


                </div>
            </form>
        </div>
    </div>

    <br class="clear">
    <br class="clear">

    <?php if ($actionLogRecords != null): ?>

        <h2 id="actionLogHeading">
            &nbsp;&nbsp;&nbsp;<?php echo __("Actions Performed on the Timesheet"); ?>
        </h2>
        <div class="outerbox" style="width: auto">
            <div class="maincontent" style="width: auto">
                <table border="0" cellpadding="5" cellspacing="0" class="actionLog-table">
                    <thead>
                        <tr>
        <!--                                    <td id="actionlogStatusAlignment"> </td>-->
                            <td id="actionlogStatus"><?php echo __('Action'); ?></td>
                            <td id="actionlogPerform"><?php echo __('Performed By'); ?></td>
                            <td id="actionLogDate"><?php echo __('Date'); ?></td>
                            <td id="actionLogComment"><?php echo __('Comment'); ?></td>
                        </tr>
                    </thead>

                    <?php foreach ($actionLogRecords as $row): ?>
                        <?php

                        $performedBy = $row->getUsers()->getEmployee()->getFullName();

                        if (empty($performedBy) && $row->getUsers()->getIsAdmin() == 'Yes') {
                            $performedBy = __("Admin");
                        }

                        ?>

                        <tr>
            <!--                    <td id="actionlogStatusAlignment"> </td>-->
                            <td id="actionlogStatus"><?php echo __(ucfirst(strtolower($row->getAction()))); ?></td>
                            <td id="actionlogPerform"><?php echo $performedBy; ?></td>
                            <td id="actionLogDate"><?php echo set_datepicker_date_format($row->getDateTime()); ?></td>
                            <td id="actionLogComment"><?php echo $row->getComment(); ?></td>
                        </tr>

                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    <?php endif; ?>
    <div id="commentDialog" title="<?php echo __('Comment'); ?>">
        <form action="updateComment" method="post" id="frmCommentSave">
            <div>
                <table>
                    <tr><td><?php echo __("Project Name ") ?></td><td><span id="commentProjectName"></span></td></tr>
                    <tr><td><?php echo __("Activity Name ") ?></td><td><span id="commentActivityName"></span></td></tr>
                    <tr><td><?php echo __("Date ") ?></td><td><span id="commentDate"></span></td></tr>
                </table>
            </div>
            <textarea name="leaveComment" id="timeComment" cols="35" rows="5" class="commentTextArea" ONKEYUP="adjustRows(this)"  WRAP="hard"></textarea>
            <br class="clear" />
            <div class="error" id="commentError"></div>
            <div><input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Close'); ?>" /></div>
        </form>
    </div>
    <script type="text/javascript">

        var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
        var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
        var submitNextState = "<?php echo $submitNextState; ?>";
        var approveNextState = "<?php echo $approveNextState; ?>";
//         var submitNextState = "<?//php echo $submitNextState; ?>";jugni have made changes 20130621
	var submitNextState = "<?php echo "SUBMITTED"; ?>";
        var rejectNextState = "<?php echo $rejectNextState; ?>";
        var resetNextState = "<?php echo $resetNextState; ?>";
        var employeeId = "<?php echo $timesheet->getEmployeeId(); ?>";
        var timesheetId = "<?php echo $timesheet->getTimesheetId(); ?>";
        var linkForViewTimesheet="<?php echo url_for('time/' . $actionName) ?>";
        var linkForEditTimesheet="<?php echo url_for('time/editTimesheet') ?>";
        var linkToViewComment="<?php echo url_for('time/showTimesheetItemComment') ?>";
//jugni have made chages 20130621
	var linkToCreateSlot="<?php echo url_for('time/createSlot') ?>";
        var date = "<?php echo $selectedTimesheetStartDate ?>";
        var actionName = "<?php echo $actionName; ?>";
        var erorrMessageForInvalidComment="<?php echo __("Comment should be less than 250 characters"); ?>";
        var validateStartDate="<?php echo url_for('time/validateStartDate'); ?>";
        var createTimesheet="<?php echo url_for('time/createTimesheet'); ?>";
        var returnEndDate="<?php echo url_for('time/returnEndDate'); ?>";
        var currentDate= "<?php echo $currentDate; ?>";
        var lang_noFutureTimesheets= "<?php echo __("Failed to Create: Future Timesheets Not Allowed"); ?>";
	var lang_overlappingTimesheets= "<?php echo __("Timesheet Overlaps with Existing Timesheets"); ?>";
	var lang_timesheetExists= "<?php echo __("Timesheet Already Exists"); ?>";
	var lang_invalidDate= "<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))); ?>";
        var dateList  = <?php echo json_encode($dateForm->getDateOptions()); ?>;
//jugni have made chages 20130621
	var slotlink  = "<?php echo "/fcs/symfony/web/index.php/time/viewEmployeeTimesheet"; ?>";
        var secret="<?echo $secret;?>";



//By:Jagruti Sangani
//Date:2013-03-23
//Purpose:On change event of dropdown show selected user's timesheet
//Change:Added new feature
//Note:======= this line indicate start and end portion

//========================================================================================================================================================
         function goto_employee_sheet(val1)
         {

            var employeeId=val1;
//            alert(employeeId);
            $("#time_employeeId").val(employeeId);
            document.getElementById('employeeSelectForm').submit();
         }
//         ========================================================================================================================================================

    </script>

<?php endif; ?>
