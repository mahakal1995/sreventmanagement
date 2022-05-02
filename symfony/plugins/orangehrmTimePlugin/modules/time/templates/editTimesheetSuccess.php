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
<?php
$noOfColumns = sizeof($currentWeekDates);

$width = 450 + $noOfColumns * 75;


?>



<?php echo stylesheet_tag('../orangehrmTimePlugin/css/editTimesheetSuccess'); ?>
<?php echo stylesheet_tag('../orangehrmTimePlugin/css/time'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/editTimesheet'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/editTimesheetPartial'); ?>
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('../../../themes/orange/css/style.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>


<?
    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:Add function for config variables,style for OT,with 2nd and 4th saturday
        Keep Original line here if:-
    */
$timeService = new TimesheetService();
require_once ROOT_PATH . '/lib/confs/Conf.php';
$config = new Conf();
/*================================================================================*/
?>


<?php $Total=array();?>
<?php if ($noOfColumns == 7): ?>
    <?php if (isset($employeeName)): ?>
<h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for'). " " . $employeeName . " ".__('for Week')." " ?><?php echo set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php else: ?>
        <h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for Week'). " " ?><?php echo " " . set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php endif; ?>
<?php endif; ?>
<?php if ($noOfColumns == 30 || $noOfColumns == 31): ?>
    <?php if (isset($employeeName)): ?>
        <h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for')." " . $employeeName . " ".__('for Month starting on'). " " ?><?php echo set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php else: ?>
        <h2> &nbsp;&nbsp;&nbsp;<?php echo __('Edit Timesheet for Month'). " " ?><?php set_datepicker_date_format($currentWeekDates[0]) ?> </h2>
    <?php endif; ?>
<?php endif; ?>

<div class="outerbox" style="width: <?php echo $width . 'px' ?>">
    <form class="timesheetForm" method="post" id="timesheetForm" >

        <table  class = "data-table" cellpadding ="0" border="0" cellspacing="0">

<?
                        /**
                            By:kartik gondalia
                            Date:23-03-2013
                            Purpose:Add function for config variables,style for OT,with 2nd and 4th saturday
                            Keep Original line here if:-
                        */

                        $db_host	= $config->dbhost;
                        $db_user        = $config->dbuser;
                        $db_pwd         = $config->dbpass;
                        $db_name        = $config->dbname;
                        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
                        mysql_select_db($db_name) or die(mysql_error());

                        $month_yr=explode("-", $currentWeekDates[0]);
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=1;
                        $myDays=Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');
                        $sat_second;
                        $sat_fourth;
                        $final_holiday=array();



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

//                         echo "<br><br>2nd====".$sat_second;
//                        echo "<br><br>4th====".$sat_fourth;
//                            echo "<br><br><br>".$config->emp_work_station."<br><br>";
                            $date_get_conf= explode(",", $config->emp_work_station);

                        $query="SELECT work_station FROM hs_hr_employee where emp_number='".$employeeId."' and  work_station IN (".$config->emp_work_station.")";
                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){

                            $work_station[]=$row;
                        }
                        $work_station= $work_station[0]->work_station;
                        if($work_station=="")
                        {
                            $work_station="NULL";
                        }

//                        echo"<pre>";echo $work_station;echo"</pre>";


                        $query="SELECT * FROM ohrm_holiday where compensate!=1";
                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){

//                            echo"<pre>";print_r($row->date);echo"</pre>";

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

                        $query="SELECT sat FROM ohrm_work_week";
                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){
                            $work_week[]=$row;
                        }


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

//                        echo"<pre>";print_r($final_holiday);echo"</pre>";
//                        echo"<pre>";print_r($holiday);echo"</pre>";
//
//                         echo"<pre>";print_r($hours_per_day);echo"</pre>";



                        $allholidays=array_merge($final_holiday,$holiday);

//=========================================
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

                        $final_allworkingdays=implode(",",$working_day);

                        if(!in_array($sat_fourth,$working_day))
                        {
                            $allholidays[]=$sat_fourth;
                        }
                        if(!in_array($sat_second,$working_day))
                        {
                            $allholidays[]=$sat_second;
                        }


//                        $allholidays[]=$sat_fourth;
//                        $allholidays[]=$sat_second;
                        $final_alldays=implode(",",$allholidays);//jagruti make string from array


    /*===================================================================================================*/
?>
<style>
.ot_worker{
    float: left;
    padding-right:2px
}
.ot{
padding-right:  1px;
    float: left;
}

.ot_img_worker{
    float: left;
    padding-right:  2px
}
.ot_img{
/*    float:left;*/
    margin-left:1px;
}



</style>
<?/*===================================================================================================*/?>
            <thead>
                <tr>
                    <td><?php echo ' ' ?></td>
                    <td id="projectName"><?php echo __('Project Name') ?></td>
                    <td id="activityName"><?php echo __('Activity Name') ?></td>
                    <?php foreach ($currentWeekDates as $date): ?>


                        <!--
                            By:kartik gondalia
                            Date:23-03-2013
                            Purpose:Initialize for OT and ST
                            Keep Original line here if:-
                       -->
                        <td  style="padding-right:5px">
                            <span style="padding-left:27px;"><?php echo __(date('D', strtotime($date))); ?></span>
                                <br/>
                            <span style="padding-left:33px;"><?php echo date('j', strtotime($date)); ?></span>
                                <br/>
                                 <? if((in_array($work_station,$date_get_conf))){ ?>
                                <span style="padding-left:12px;"><?php  echo "ST"; ?></span>

                            <span style="margin-left:20px;"><?php  echo "OT"; ?></span>
                            <?}?>
<!--  ===============================================================================================================    -->

                        </td>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tr> <td colspan="100"></td>


                                                   <?

                                                   //$_SESSION['final_holiday']=json_encode($final_holiday);

                                                   $str1 = json_encode($final_holiday);
                                                   //echo $str1;


//                                                   $final_holiday=htmlentities($final_holiday);
                                                   ?>
                 <input type="hidden" name="work_station" id="work_station" value="<? echo $work_station; ?>">
                 <input type="hidden" name="emp_work_hours" id="emp_work_hours" value="<? echo $hours_per_day; ?>">
                 <input type="hidden" name="holiday" id="holiday" value="">
<!--                 <input type="hidden" name="emp_work_hours" id="emp_work_hours" value="<? //echo $config->emp_work_hours; ?>">-->

            </tr>

            <?php $i = 0 ?>
            <?php if ($timesheetItemValuesArray == null): ?>
                <tr>
                    <?//echo"<br>in if";?>
                        <!--
                            By:kartik gondalia
                            Date:23-03-2013
                            Purpose:Initialize for OT with 2nd,4th saturday,holiday,
                            Keep Original line here if:-
                        -->
                    <td ><?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?></td>
                    <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?><td>&nbsp;<?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectName'] ?></td>
                    <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?><td>&nbsp;<?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectActivityName'] ?>


                    </td>
                    <?php for ($j = 0; $j < $noOfDays; $j++) {?>
                        <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                        <?
//                        jugni have made chnages 20130622
                        $data=$currentWeekDates[$j];
                        $month_yr=explode("-", $data);
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=$month_yr[2];;
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');

                        ?>


                    <td style="<? if((!in_array($work_station,$date_get_conf))){echo 'padding-left:0px';}else{echo '';} ?>" ><?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>


                        <?if($work_week[0]->sat=='12') {

//=========================================
//Name:sangani jagruti
//date:2013-12-14
//purpose:add saturday as working day


 if( ($currentWeekDates[$j]==$sat_second && !in_array($sat_second,$working_day)) || ($currentWeekDates[$j]==$sat_fourth && !in_array($sat_fourth,$working_day)))

//if(($currentWeekDates[$j]==$sat_second) || ($currentWeekDates[$j]==//$sat_fourth))
                                     {?>

                                            <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>
                                            </div>

                                    <? }
//                                     else if(($currentWeekDates[6]==$currentWeekDates[$j])||($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))  ){


//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day

                        else if(($day=="Sunday" && !in_array($currentWeekDates[$j], $working_day)) || ($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))  ){ ?>
                                            <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>
                                            </div>


                                      <?}else{?>
                                             <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("maxlength" => 5)) ?>
                                            </div>

                                 <?}?>

                                 <?//}else if(($currentWeekDates[6]==$currentWeekDates[$j])||($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))){
                                 ?>

                        <?}
//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day

                        else if(($day=="Sunday"  && !in_array($currentWeekDates[$j], $working_day)) || ($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))){  ?>
                                            <div class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>
                                            </div>


                                      <?}else{?>
                                             <div class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("maxlength" => 5)) ?>
                                            </div>


                                 <?}?>

                                 <? if((in_array($work_station,$date_get_conf))){ ?>
                                    <?if($currentWeekDates[$j]>date('Y-m-d')){?>

                                            <div id="ot_cal"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                    <?php echo $timesheetForm['initialRows_ot'][$i][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>

                                            </div>

                                    <?}else{?>
                                            <div id="ot_cal" class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                    <?php echo $timesheetForm['initialRows_ot'][$i][$j]->render(array("maxlength" => 5)) ?>
                                            </div>
                                    <?}?>


                                <?}?>

                                <? if((in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday)) ||($currentWeekDates[$j]==$sat_second) || ($currentWeekDates[$j]==$sat_fourth)){  ?>

                                    <? if((in_array($work_station,$date_get_conf))){ ?>
                                        <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                        <?php echo image_tag('callout.png', 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon") ?>
                                        </div>
                                    <?}else{?>
                                        <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                        <?php echo image_tag('callout.png')?>
                                        </div>
                                    <?}?>
                                <?}else if($currentWeekDates[$j]>date('Y-m-d')){?>

                                    <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                    <?php echo image_tag('callout.png')?>
                                    </div>

                                <?} else {?>
                                    <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                    <?php echo image_tag('callout.png', 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon") ?>
                                    </div>
                                <?/*==========================================================================================================================================*/?>
                                <?}?>
                    </td>
                    <?php } ?>
                </tr>

                <?php $i++ ?>

            <?php else: ?>
                <?


                ?>
                <?php foreach ($timesheetItemValuesArray as $row): ?>
                <?php $dltClassName = ($row['isProjectDeleted'] == 1 || $row['isActivityDeleted'] == 1) ? "deletedRow" : ""?>
                    <tr>
                        <?//echo "<br> in else";?>
                        <td id="<?php echo $row['projectId'] . "_" . $row['activityId'] . "_" . $timesheetId . "_" . $employeeId ?>"><?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?></td>
                        <?php echo $timesheetForm['initialRows'][$i]['projectId']	 ?><td><?php if ($row['isProjectDeleted'] == 1) { ?><span class="required">*</span><?php } else{?>&nbsp;<?php } ?><?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectName']->render(array("class" => $dltClassName." "."project"))?></td>
                        <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?><td><?php if (($row['isActivityDeleted'] == 1)) { ?><span class="required">*</span><?php } else{?>&nbsp;<?php } ?><?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?><?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->render(array("class" => $dltClassName." "."projectActivity")) ?></td>
                        <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                        <?
//                        jugni have made chnages 20130622
                        $data=$currentWeekDates[$j];
                        $month_yr=explode("-", $data);
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=$month_yr[2];;
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');

                        ?>

                             <td style="<? if((!in_array($work_station,$date_get_conf))){echo 'padding-left:0px';}else{echo '';} ?>" class="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>">

                                 <?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>

                                 <?php $val1=$timesheetForm['initialRows'][$i][$j]->getValue();
                                 $val2=$timeService->convertDurationToHours_ot($row['timesheetItems'][$currentWeekDates[$j]]->getDuration_ot());

                                 $hours=explode(":",$val1);
				 $Total[$j]+=($hours[0]*60)+$hours[1];

				 $hours1=explode(":",$val2);
				 $Total[$j]+=($hours1[0]*60)+$hours1[1];
                                 ?>

                                 <?if($work_week[0]->sat=='12')
{

//=========================================
//Name:sangani jagruti
//date:2013-12-14
//purpose:add saturday as working day

//if(($currentWeekDates[$j]==//$sat_second) || ($currentWeekDates[$j]==//$sat_fourth))
if( ($currentWeekDates[$j]==$sat_second && !in_array($sat_second,$working_day)) || ($currentWeekDates[$j]==$sat_fourth && !in_array($sat_fourth,$working_day)))
                                     {?>

                                            <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'items',"","readonly"=>readonly))?>
                                            </div>



                                    <? }
//                                     else if(($currentWeekDates[6]==$currentWeekDates[$j])||($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))){
//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day

                                       else if(($day=="Sunday"  && !in_array($currentWeekDates[$j], $working_day)) || ($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))){  ?>
                                             <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'items',"","readonly"=>readonly))?>
                                            </div>



                                      <?}else{?>
                                            <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'items'))?>
                                            </div>


                                 <?}?>

                                 <?//}else if(($currentWeekDates[6]==$currentWeekDates[$j])||($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))){

//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day
                                 }else if(($day=="Sunday"  && !in_array($currentWeekDates[$j], $working_day)) || ($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday))){  ?>
                                            <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'items',"","readonly"=>readonly))?>
                                            </div>



                                      <?}else{?>
                                            <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName." ".'items'))?>
                                            </div>


                                 <?}?>
                                         <? if((in_array($work_station,$date_get_conf))){ ?>
                                            <?if($currentWeekDates[$j]>date('Y-m-d')){?>

                                                <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                    <?php echo $timesheetForm['initialRows_ot'][$i][$j]->render(array("class" => $dltClassName." ".'items',"readonly"=>readonly),$timeService->convertDurationToHours_ot($row['timesheetItems'][$currentWeekDates[$j]]->getDuration_ot()))?>
                                                </div>

                                            <?}else{?>

                                                <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                                    <?php echo $timesheetForm['initialRows_ot'][$i][$j]->render(array("class" => $dltClassName." ".'items'),$timeService->convertDurationToHours_ot($row['timesheetItems'][$currentWeekDates[$j]]->getDuration_ot()))?>
                                                </div>
                                            <?}?>

                                        <?}?>
                                            <? if((in_array($currentWeekDates[$j], $holiday))|| (in_array($currentWeekDates[$j], $final_holiday)) ||($currentWeekDates[$j]==$sat_second) || ($currentWeekDates[$j]==$sat_fourth)){  ?>

                                                        <? if((in_array($work_station,$date_get_conf))){ ?>
                                                            <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                                <?php echo image_tag('callout.png', 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon") ?>
                                                            </div>

                                                        <?}else{?>

                                                            <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                                <?php echo image_tag('callout.png')?>
                                                            </div>
                                                        <?}?>
                                              <?}else if($currentWeekDates[$j]>date('Y-m-d')){?>

                                                             <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                                <?php echo image_tag('callout.png')?>
                                                            </div>

                                            <?} else {?>
                                                    <div id="img"  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                        <?php echo image_tag('callout.png', 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon") ?>
                                                    </div>
                        <?/*==========================================================================================================================================*/?>
                                            <?}?>

                             </td>
                        <?php } ?>
                    </tr>
                    <?php $i++

                    ?>

                <?php endforeach; ?>
            <?php endif; ?>

            <td colspan="100">
                <div id="extraRows"/>
            </td>
            <!--
            <td id ="totalVerticalValue"><?php// if($sum_st<($hours_per_day) && (!in_array($data, $allholidays)) && ($data!=$sat_second) && ($data!=$sat_fourth) && ($day!="Sunday")){ echo  "<span style=color:red;>".$timeService->convertDurationToHours($verticalTotal);}else{echo $timeService->convertDurationToHours($verticalTotal);} ?> </td>
            -->
<!--          ++++++++++++++++++++++++++++Rushika++++++++++++++    -->
	      <tr><td></td>
	      <td><B>Total</B></td>
	      <td></td>

	      <?php

	      for($i=0;$i<$noOfDays;$i++)
	      {
		$Hours=floor($Total[$i]/60);
		$minut=$Total[$i]%60;
		if($minut>=0 && $minut<=9)
		{
		  $minut="0".$minut;
		}
		$alldates.=$currentWeekDates[$i].",";
		$data=$currentWeekDates[$i];
		$month_yr=explode("-", $data);
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=$month_yr[2];;
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');
                        //rushika 20130702
                        $finalMinute=($minut*100)/60;
                        if(strlen($finalMinute)<=1)
                        {
			    $finalMinute='0'.$finalMinute;
                        }
                        $TotalTime=$Hours.".".$finalMinute;
                      //___________________________________________
                      //  $TotalTime=$Hours.".".($minut*100)/60;
                        //echo $hours_per_day." ".$TotalTime;
//                         $hours_for_day=explode(".",$hours_per_day);

	      ?>

	      <td id="totalVerticalValue">

<!--	      Change by : Rushika Patel
              Date : 2013-12-15
              Change : Make sunday as working day-->

              <div class="<? echo "ColumnTotal".$i;?>" ><?php if($TotalTime<($hours_per_day) && (!in_array($data, $allholidays)) && ($day!='Sunday' || ($day=='Sunday' && (in_array($data, $working_day))))){echo "<span id='spanColor$i' style=color:red;><b>".$Hours.":".$minut."</b>";}else{echo "<span id='spanColor$i' style=color:black;><b>".$Hours.":".$minut."</b>";}?></div></td>
	      <?}
//	      echo $final_alldays;
	      ?>
	      <input id="alldates" value="<?php echo substr($alldates,0,strlen($alldates)-1); ?>" type="hidden" ></input>
	      <input id="allholidays" value="<?php echo $final_alldays; ?>" type="hidden" ></input>
              <input id="allworkdays" value="<?php echo $final_allworkingdays; ?>" type="hidden" ></input>
	      <input id="work_hours" value="<?php echo $hours_per_day; ?>" type="hidden" ></input>
               <input id="noOfDays" value="<?php echo $noOfDays; ?>" type="hidden" ></input>

	      </tr>
	      <!--          ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    -->
        </table>

        <div class="formbuttons">
            <?php echo button_to(__('Cancel'), 'time/' . $backAction . '?timesheetStartDate=' . $startDate . '&employeeId=' . $employeeId, array('class' => 'plainbtn', 'id' => 'btnBack')) ?>
            <?php sfContext::getInstance()->getUser()->setFlash('employeeId', $employeeId); ?>
            <input class="plainbtn" type="submit" value="<?php echo __('Save') ?>" name="btnSave" id="submitSave"/>
            <input type="button" class="plainbtn" id="btnAddRow" value="<?php echo __('Add Row') ?>" name="btnAddRow">
            <input type="button" class="plainbtn" id="submitRemoveRows" value="<?php echo __('Remove Rows') ?>" name="btnRemoveRows">
            <?php echo button_to(__('Reset'), 'time/editTimesheet?timesheetId=' . $timesheetId . '&employeeId=' . $employeeId . '&actionName=' . $backAction, array('class' => 'plainbtn', 'id' => 'btnReset')) ?>
        </div>

    </form>
</div>
  <div class="paddingLeftRequired"><span class="required">*</span><?php echo " ".__('Deleted project activities are not editable') ?> </div>
<!-- comment dialog -->

<div id="commentDialog" title="<?php echo __('Comment'); ?>">
    <form action="updateComment" method="post" id="frmCommentSave">
        <div>
            <table>
                <tr><td><?php echo __("Project Name") ?></td><td><span id="commentProjectName"></span></td></tr>
                <tr><td><?php echo __("Activity Name") ?></td><td><span id="commentActivityName"></span></td></tr>
                <tr><td><?php echo __("Date") ?></td><td><span id="commentDate"></span></td></tr>
            </table>
        </div>
        <textarea name="leaveComment" id="timeComment" cols="35" rows="5" class="commentTextArea"></textarea>


        <div class="error" id="commentError"></div>
        <div>
            <br class="clear" /><input type="button" id="commentSave" class="plainbtn" value="<?php echo __('Save'); ?>" />
            <input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>
    </form>
</div>

<!-- end of comment dialog-->
<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var rows = <?php echo $timesheetForm['initialRows']->count() + 1 ?>;

    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:Initialize for OT
        Keep Original line here if:-
    */
    var rows_ot = <?php echo $timesheetForm['initialRows_ot']->count() + 1 ?>;
    /*==================================================================================*/
    var link = "<?php echo url_for('time/addRow') ?>";
    var commentlink = "<?php echo url_for('time/updateTimesheetItemComment') ?>";
    var projectsForAutoComplete=<?php echo $timesheetForm->getProjectListAsJson(); ?>;
    var projects = <?php echo $timesheetForm->getProjectListAsJsonForValidation(); ?>;
    var projectsArray = eval(projects);
    var getActivitiesLink = "<?php echo url_for('time/getRelatedActiviesForAutoCompleteAjax') ?>";

    var timesheetId="<?php echo $timesheetId; ?>"
    var lang_not_numeric = '<?php echo __('Should Be Less Than 24 and in HH:MM or Decimal Format'); ?>';

    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:Initialize for OT
        Keep Original line here if:-
    */
    var st_ot_addition = '<?php echo __('ST And OT Addition Shouldn`t Be Greater Than 24 Hours'); ?>';
    var st_Time_check= '<?php echo __('ST Shouldn`t Be Greater Than '.$hours_per_day.' Hours'); ?>';
    var st_fill_first= '<?php echo __('Fill first ST Hours'); ?>';
    /*==================================================================================================*/

    var rows_are_duplicate = "<?php echo __('Duplicate Records Found'); ?>";
    var project_name_is_wrong = '<?php echo __('Select a Project and an Activity'); ?>';
    var please_select_an_activity = '<?php echo __('Select a Project and an Activity'); ?>';
    var select_a_row = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
    var employeeId = '<?php echo $employeeId; ?>';
    var linkToGetComment = "<?php echo url_for('time/getTimesheetItemComment') ?>";
    var linkToDeleteRow = "<?php echo url_for('time/deleteRows') ?>";
    var editAction = "<?php echo url_for('time/editTimesheet') ?>";
    var currentWeekDates = new Array();
    var startDate='<?php echo $startDate ?>';
    var backAction='<?php echo $backAction ?>';
    var endDate='<?php echo $endDate ?>';
    var erorrMessageForInvalidComment="<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)); ?>";
    var numberOfRows='<?php echo $i ?>';
    var incorrect_total="<?php echo __('Total Should Be Less Than 24 Hours'); ?>";
    var typeForHints='<?php echo __('Type for hints').'...'; ?>';
    var lang_selectProjectAndActivity='<?php echo __('Select a Project and an Activity'); ?>';
    var lang_enterExistingProject='<?php echo __("Select a Project and an Activity"); ?>';
    var lang_noRecords='<?php echo __('Select Records to Remove'); ?>';
    var lang_removeSuccess = '<?php echo __('Successfully removed')?>';
    var lang_noChagesToDelete = '<?php echo __('No Changes to Delete');?>';
<?php
for ($i = 0; $i < count($currentWeekDates); $i++) {
    echo "currentWeekDates[$i]='" . $currentWeekDates[$i] . "';\n";
}
?>

    var str1 = <?=json_encode($allholidays)?>;
    var json = JSON.stringify(str1);
    document.getElementById('holiday').setAttribute('value', json);
   //jagruti
</script>