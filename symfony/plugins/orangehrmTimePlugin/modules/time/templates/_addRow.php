<?php
/**
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
 */
?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/editTimesheet');

    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:Add function for config variables,style for OT,with 2nd and 4th saturday
        Keep Original line here if:-
    */
require_once ROOT_PATH . '/lib/confs/Conf.php';

 $config = new Conf();
?>

<style>
.ot_worker{
    float: left;
    padding-left: 2xp
}
.ot{

    padding-left: 20px
}
.ot_img_worker{

    float: left;
    padding-left: 2px
}
.ot_img{

    padding-left: 2px
}
</style>

<table  class = "data-table" cellpadding ="0" border="0" cellspacing="0">
	<tr>
            <?
                $currentWeekDates=array();
                    $_GET['currentWeekDates']=  explode(",", $_GET['currentWeekDates']) ;
                    $currentWeekDates=$_GET['currentWeekDates'];

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
                        $query="SELECT sat FROM ohrm_work_week";
                        $res = mysql_query($query);

                        while($row= mysql_fetch_object($res)){

                            $work_week[]=$row;
                        }
                        $query="SELECT date FROM ohrm_holiday where compensate=1";
                        $res = mysql_query($query);
                        $working_day=array();
                        while($row= mysql_fetch_object($res))
                        {
                            $working_day[]=$row->date;
                        }
//                        echo $config->emp_work_station."<br><br>";
                        $date_get_conf= explode(",", $config->emp_work_station);

                        $query="SELECT work_station FROM hs_hr_employee where emp_number='".$_GET['employeeId']."' and  work_station IN (".$config->emp_work_station.")";
                        $res = mysql_query($query);
                        while($row= mysql_fetch_object($res)){

                            $work_station[]=$row;
                        }
                        $work_station=$work_station[0]->work_station;
            ?>
	    <td><?php echo $form['initialRows'][$num]['toDelete'] ?></td>
                <?php echo $form['initialRows'][$num]['projectId'] ?><td>&nbsp;<?php echo $form['initialRows'][$num]['projectName']->renderError() ?><?php echo $form['initialRows'][$num]['projectName'] ?></td>
		<?php echo $form['initialRows'][$num]['projectActivityId'] ?><td>&nbsp;<?php echo $form['initialRows'][$num]['projectActivityName']->renderError() ?><?php echo $form['initialRows'][$num]['projectActivityName'] ?></td>

<!--        kartik-->

		<?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                        <?
//                        jugni have made chnages 20130624
                        $data=$currentWeekDates[$j];
                        $month_yr=explode("-", $data);
                        $mon=$month_yr[1];
                        $yea=$month_yr[0];
                        $dat=$month_yr[2];;
                        $dt = new DateTime($yea.'-'.$mon.'-'.$dat);
                        $day=$dt->format('l');
                        ?>
			<?php echo $form['initialRows'][$num]['TimesheetItemId'.$j] ?><td style="<? if((!in_array($work_station,$date_get_conf))){echo 'padding-left:0px';}else{echo 'text-align:center;';} ?>" ><?php echo $form['initialRows'][$num][$j]->renderError() ?>

<!--                        Change by : Rushika patel
                            Date : 18-12-2013
                            Change : make saturday as working day-->

                             <? if($work_week[0]->sat=='12') {
                                 if( ($currentWeekDates[$j]==$sat_second && !in_array($sat_second,$working_day)) || ($currentWeekDates[$j]==$sat_fourth && !in_array($sat_fourth,$working_day)))
                                      {?>

                                        <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $form['initialRows'][$num][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>
                                        </div>
<!--                            Change by : Rushika patel
                            Date : 18-12-2013
                            Change : make sunday as working day-->
                                    <?}else if(($day=="Sunday"  && !in_array($currentWeekDates[$j], $working_day)) ||($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday)) || (in_array($currentWeekDates[$j], $final_holiday))){  ?>

                                        <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $form['initialRows'][$num][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>
                                        </div>


                                    <?}else{?>
                                         <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $form['initialRows'][$num][$j]->render(array("maxlength" => 5)) ?>
                                        </div>

                                    <?}?>
<!--                        Change by : Rushika patel
                            Date : 18-12-2013
                            Change : make sunday as working day-->
                            <?}else if(($day=="Sunday" && !in_array($currentWeekDates[$j], $working_day)) ||($currentWeekDates[$j]>date('Y-m-d')) || (in_array($currentWeekDates[$j], $holiday)) || (in_array($currentWeekDates[$j], $final_holiday))){  ?>
                                        <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $form['initialRows'][$num][$j]->render(array("maxlength" => 5,"readonly"=>readonly)) ?>
                                        </div>


                             <?}else{?>
                                        <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                            <?php echo $form['initialRows'][$num][$j]->render(array("maxlength" => 5)) ?>
                                        </div>

                               <?}?>
                            <? if((in_array($work_station,$date_get_conf))){ ?>
                            <? if($currentWeekDates[$j]>date('Y-m-d')){?>

                                 <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                    <?php echo $form['initialRows_ot'][$num][$j]->render(array("maxlength" => 5,"readonly"=>readonly))?>
                                </div>

                            <?}else{?>
                                 <div  class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_worker';}else{echo 'ot';} ?>">
                                    <?php echo $form['initialRows_ot'][$num][$j] ?>
                                </div>
                            <?}?>
                            <?}?>

<!--                        Change by : Rushika patel
                            Date : 18-12-2013
                            Change : make saturday as working day-->
                                <?if((in_array($currentWeekDates[$j], $holiday)) || (in_array($currentWeekDates[$j], $final_holiday))||($currentWeekDates[$j]==$sat_second && !in_array($sat_second,$working_day)) || ($currentWeekDates[$j]==$sat_fourth && !in_array($sat_fourth,$working_day))){  ?>

                                         <? if((in_array($work_station,$date_get_conf))){ ?>
                                            <div id="img" class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                <?php echo image_tag('callout.png', 'id=commentBtn_'.$j.'_' . $num . " class=commentIcon") ?>
                                            </div>
                                         <?}else{?>
                                            <div id="img" class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                <?php echo image_tag('callout.png') ?>
                                            </div>
                                          <?}?>
                                <?}else if($currentWeekDates[$j]>date('Y-m-d')){?>
                                            <div id="img" class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                <?php echo image_tag('callout.png') ?>
                                            </div>
                                <?}else{?>
                                            <div id="img" class="<? if((in_array($work_station,$date_get_conf))){echo 'ot_img_worker';}else{echo 'ot_img';} ?>">
                                                <?php echo image_tag('callout.png', 'id=commentBtn_'.$j.'_' . $num . " class=commentIcon") ?>
                                            </div>
                                <?}?>
                        </td>
		<?php } ?>
<?/*===================================================================================================================================*/?>
	</tr>
</table>


