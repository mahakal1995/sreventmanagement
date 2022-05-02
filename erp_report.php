<?php
//print_r($_POST);
if(isset($_POST['export_flag']) && $_POST['export_flag']==1)
{
                $file_name="erp_export.csv";
                header('Content-Type: application/download');
                header('Content-Disposition: attachment; filename='.$file_name);
                header("Content-Length: " . filesize($file_name));

                $fp = fopen($file_name, "r");
                fpassthru($fp);
                fclose($fp);
                exit;
?>
<script>
document.getElementById('export_flag').value='0';
</script>
<?php
}
?>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="scripts/jquery/jquery-1.7.1.js"> </script>
<script type="text/javascript" src="scripts/jquery/jquery-ui-1.10.2.custom.min.js"></script>
<script type="text/javascript" src="scripts/jquery/jquery-ui-1.10.2.custom.js"></script>
<link href="themes/orange/css/ui-lightness/jquery-ui-1.10.2.custom.css" rel="stylesheet" />
<?php
define('ROOT_PATH', dirname(__FILE__));
    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
        $color_arr      = $config->color_arr;
        $str="";
        $month=array(
            "1"=>"January",
            "2"=>"February",
            "3"=>"March",
            "4"=>"April",
            "5"=>"May",
            "6"=>"June",
            "7"=>"July",
            "8"=>"August",
            "9"=>"September",
            "10"=>"Octomber",
            "11"=>"November",
            "12"=>"December"
        );
        if(isset($_POST) && count($_POST)>0)
        {
           
            $sd="1";
            $mm=$_POST['month'];
            $yy=$_POST['year'];
            $ed=cal_days_in_month(CAL_GREGORIAN,$mm,$yy);
            $start_date=$yy."-".$mm."-".$sd;
            $end_date=$yy."-".$mm."-".$ed;
            $str1="";
            if($start_date!="")
            {
               $str1.=" AND date >= '".$start_date."'";
            }
            if($end_date!="")
            {
                $str1.=" AND date <= '".$end_date."'";
            }
            $_SESSION['where']=$str1; 
        }
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());
?>
<form id="theForm" method="POST" action="erp_report.php">
    <table style="background-color: #B7FFED">
        <tr><td colspan="2" style="text-align: center;"><b>ERP REPORT</b>
            <input type="hidden" name="reset_flag" id="reset_flag" value="0" />
            <input type="hidden" name="export_flag" id="export_flag" value="0" />
            </td></tr>
      
        <tr style="text-align:center">
            <td colspan="2"><b>Month:&nbsp;</b>
                <select name="month" id="month" >
                    <?php
                    for($i=1;$i<13;$i++)
                    {
                        if(isset($_POST['month']) && $_POST['month']!="" && $_POST['month']==$i && $_POST['reset_flag']==0)
                        {?>
                            <option value="<?=$i?>" selected=""><?= $month[$i];?></option>
                        <?php }else{
                        ?>
                         <option value="<?=$i?>"><?= $month[$i];?></option>
                        <?php }}?>
                </select>
            <b>Year:&nbsp;</b>
                 <select name="year" id="year" >
                    <?php
                    $year=date('Y');
                    for($j=(int)$year;$j>=2012;$j--)
                    {
                        if(isset($_POST['year']) && $_POST['year']!="" && $_POST['year']==$j && $_POST['reset_flag']==0) {?>
                            <option value="<?=$j?>" selected="true"><?=  strtoupper($j);?></option>
                        <?php }else if($year==$j){ ?> 
                                <option value="<?=$j?>" selected=""><?=  strtoupper($j);?></option>
                        <?php }
                                else
                                {?>
                                     <option value="<?=$j?>"><?=  strtoupper($j);?></option>  
                            <?php }
                    }?>
                </select>
            </td>
           
        </tr>
       
        <tr><td colspan="2"  style="text-align: center;">
                <input type="button" value="Search" style="height:25px;text-align: center;" onclick="search()"/>
                <input type="reset" id="reset" name="reset" value="Reset" onclick="resetdiv();" style="height:25px;text-align: center;"/></td> </tr>
    </table>
</form>

<style>
  #span2 { float: right; }
  #span1 { float: left; }


 table {
    border-collapse: collapse;
    width:100%;
    border: 1px solid black;
}
td {
  border: 1px solid black;
  white-space: nowrap;
}
</style>
<script>
function search()
{
     document.getElementById('reset_flag').value=0;
     document.getElementById('export_flag').value=0;
     document.getElementById("theForm").submit();
}
function resetdiv()
{
     document.getElementById('reset_flag').value=1;
     document.getElementById('export_flag').value=0;
     document.getElementById("theForm").submit();
}
function exportdata()
{
    document.getElementById('reset_flag').value=0;
    document.getElementById('export_flag').value=1;
    document.getElementById("theForm").submit();
}
</script>
<?php
$flag=0;

$str_exp="";
$str="<table>";
$str.="<tr><td><b>Project nr</b></td><td><b>Date</b</td><td><b>Code Worker</b></td><td><b>nr hours</b></td><td><b>GroupCode</b></td><td><b>Activity Code</b></td><td><b>Rate</b></td></tr>";
$records=0;
$total_hr=0;
$total_rate=0;
if(isset($_POST['month']) && $_POST['reset_flag']==0)
{
    $date=$newDate = date("d/m/Y", strtotime($end_date));
    $where=$_SESSION['where'];

    $proj_query="SELECT t1.project_id,t1.name FROM ohrm_project t1 where t1.is_deleted=0 order by t1.name ASC";
    $proj_result =mysql_query($proj_query);
    if($proj_result)
    {
        while($proj_row= mysql_fetch_object($proj_result ))
        {
                    $act_query="SELECT activity_id,name,activity_code FROM ohrm_project_activity WHERE is_deleted='0' AND project_id='".$proj_row->project_id."'";
                    $act_result =mysql_query($act_query);
                    if($act_result)
                    {
                        while($act_row= mysql_fetch_object($act_result ))
                        {
                               $emp_query="SELECT t1.employee_id,t2.work_station,t2.rate_per_hours,t2.employee_id as code_worker FROM `ohrm_timesheet_item` t1,hs_hr_employee t2 WHERE t1.activity_id='".$act_row->activity_id."' and t1.employee_id=t2.emp_number group by t1.employee_id ";

                               $emp_result =mysql_query($emp_query);
                               if($emp_result)
                               {
                                   while($emp_row= mysql_fetch_object($emp_result ))
                                   {
                                       $flag=1;
                                       $hours=get_hour($emp_row->employee_id,$act_row->activity_id,$proj_row->project_id,$where);
                                       $hr_arr=explode("->",$hours);
                                       $rate=$hr_arr[0]*$emp_row->rate_per_hours+$hr_arr[1]*$emp_row->rate_per_hours;
                                       $total_hours=$hr_arr[0]+$hr_arr[1];
                                       $unit_code=get_group_code($emp_row->work_station);
                                       $total_hours=number_format((float)$total_hours, 2, '.', '');   
                                       $rate=number_format((float)$rate, 2, '.', '');  
                                       if($total_hours > 0)
                                       {
                                        $records++;
                                        $total_hr=$total_hr+$total_hours;
                                        $total_rate=$total_rate+$rate;
                                        $str.="<tr><td>".$proj_row->name."</td><td>".$date."</td><td>".$emp_row->code_worker."</td><td>".$total_hours."</td><td>".$unit_code."</td><td>".$act_row->activity_code."</td><td>".$rate."</td></tr>";
                                        $str_exp.=$proj_row->name.",".$date.",".$emp_row->code_worker.",".$total_hours.",".$unit_code.",".$act_row->activity_code.",".$rate."\n";
                                       }

                                   }
                               }
                        }

                    }
        }
    }
}
$str.="</table>";
if($flag==1 && isset($_POST['month']) && $_POST['reset_flag']==0 && $records > 0)
{?>

<input type="button" name="export" value="Export to CSV" style="height: 30px;width:100px;" onclick="exportdata();"/>
<span style="margin-left: 60px;"><b>No. of Records:</b> <?php echo $records;?></span>
<span style="margin-left: 50px;"><b>Total Hours:</b> <?php echo $total_hr;?></span>
<span style="margin-left: 50px;"><b>Total Rate:</b> <?php echo $total_rate;?></span>
<div>&nbsp;</div>
<?php


$file = fopen("erp_export.csv","w");
fwrite($file,$str_exp);
fclose($file);
if(isset($_POST['month']) && $_POST['reset_flag']==0 && $records > 0)
    echo $str;
}
else if(isset($_POST['month']) && $_POST['reset_flag']==0 && $records==0)
{
    echo "<center><b>No Records found...!!!</b></center>";
}    
?>


<?php
function get_hour($emp_id,$act_id,$proj_id,$where)
{
    $query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT FROM ohrm_timesheet_item WHERE employee_id=".$emp_id." and activity_id=".$act_id." and project_id=".$proj_id.$where;
//    echo "<br>".$query;
    $emp_result1 =mysql_query($query);
    $ST=0;
    $OT=0;
    if($emp_result1)
    {
        while($emprow= mysql_fetch_object($emp_result1 ))
        {
            $ST=$emprow->ST;
            $OT=$emprow->OT;
        }
    }
    if($ST=="")
        $ST=0;
    if($OT=="")
        $OT=0;
    return $ST."->".$OT;
}  

function get_group_code($work_station)
{
   $unit_code="";
   $query="SELECT unit_id from ohrm_subunit where id=".$work_station;
   $work_result =mysql_query($query);
    if($work_result)
    {
        while($workrow= mysql_fetch_object($work_result ))
        {
            $unit_code=$workrow->unit_id;
        }
    }
    return $unit_code;
}


?>