<?
    define('ROOT_PATH', dirname(__FILE__));
    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
        
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());



        
    if(isset($_REQUEST['id']))
    {
       
        $act_arr=array();
        $id=$_REQUEST['id'];
        $id=trim($id);
// Changed By = Rushika
// Changes = change concat(t2.name,' - ',t1.name) to concat(t1.name,' - ',t2.name)
        $query="SELECT t3.project_id,t3.activity_id,t3.name FROM `ohrm_project_activity` t3 WHERE t3.is_deleted='0' AND t3.project_id = (SELECT t1.project_id FROM ohrm_project t1,ohrm_customer t2 where t1.customer_id=t2.customer_id and t1.is_deleted=0 and (concat(t1.name,' - ',t2.name)='".$id."'))";
                        $main_result =mysql_query($query);
                        if($main_result)
                        {
                            while($lastrow= mysql_fetch_object($main_result ))
                            { 
                             $act_arr[]=array("id"=>$lastrow->activity_id,"name"=>$lastrow->name,"project_id"=>$lastrow->project_id);   
                            }
                        } 
                        
                        
    echo json_encode($act_arr);
    }
    elseif(isset($_REQUEST['date']))
    {
        $empid=$_REQUEST['emp_id'];
        $date=$_REQUEST['date'];
        $st=0;
        $ot=0;
        $dbflag=0;
        
                                
//for get the st an dot total hours based on employee_id and date                        
                        $check="SELECT sum(duration) as ST,sum(duration_ot) as OT FROM `ohrm_timesheet_item` WHERE employee_id='".$empid."' and date='".$date."'group by employee_id";
                        $main_result1 =mysql_query($check);
                        if($main_result1)
                        {
                            while($row= mysql_fetch_object($main_result1))
                            {  $dbflag=1;
                               $st=$row->ST;
                               $ot=$row->OT;
                            }
                        }
                        
                        if($dbflag==1)
                        {
                            $st=convertDurationToHours($st);
                            $ot=convertDurationToHours($ot);
                        }    
        
        
        
        echo $st."/".$ot;
        
        
    }
    elseif(isset($_REQUEST['cdate']))
    {
         $holiday=array();
         $final_holiday=array();
        $query="SELECT * FROM ohrm_holiday where compensate!=1";  
        $res = mysql_query($query);
        if($res)
        {    
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
            $final_holiday=array_merge($holiday,$final_holiday);
        }
        
        
        $date=$_REQUEST['cdate'];
        $holiday=$final_holiday;
        $allow=1;
                        $month_yr=explode("-", $date);                       
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
                                $sat_second = $yea.'-'.$mon.'-14';
                                $sat_fourth = $yea.'-'.$mon.'-28';
                                break;
                        case "Monday":
                                $sat_second = $yea.'-'.$mon.'-13';
                                $sat_fourth = $yea.'-'.$mon.'-27';
                                break;
                        case "Tuesday":  
                                $sat_second = $yea.'-'.$mon.'-12';
                                $sat_fourth = $yea.'-'.$mon.'-26';
                                break;
                        case "Wednesday":
                                $sat_second = $yea.'-'.$mon.'-11';
                                $sat_fourth = $yea.'-'.$mon.'-25';   
                                break;
                        case "Thursday":
                                $sat_second = $yea.'-'.$mon.'-10';
                                $sat_fourth = $yea.'-'.$mon.'-24';
                                break;
                        case "Friday":
                                $sat_second = $yea.'-'.$mon.'-09';
                                $sat_fourth = $yea.'-'.$mon.'-23';
                                break;
                        case "Saturday":
                                $sat_second = $yea.'-'.$mon.'-08';
                                $sat_fourth = $yea.'-'.$mon.'-22';
                                break;
                        default:
                                break;   
                        }
                        if($date==$sat_fourth || $date ==$sat_second || in_array($date,$holiday))
                        {
                            $allow=0;
                        }
                       $dt = new DateTime($date);
                        if($dt->format('l')=="Sunday")
                        {
                           $allow=0; 
                        }
                        echo $allow;
 
    } 
    else
    {
         if(isset($_REQUEST['empid']))
         {
             require_once ROOT_PATH . '/lib/confs/Conf.php';
             $config = new Conf();
             $work_shift_id="";
             $hours_per_day="";
             $work_station=0;
             
             $config_workstation=$config->emp_work_station;
            
        
                        $employeeId=$_REQUEST['empid'];
                        
                        $query="SELECT * FROM ohrm_employee_work_shift where emp_number=".$employeeId;                       
                        $res = mysql_query($query);
                        if($res)
                        {
                            while($row= mysql_fetch_object($res)){                              
                                $work_shift_id=$row->work_shift_id;                            
                            }      
                        }
                        
                        $query="SELECT * FROM ohrm_work_shift where id=".$work_shift_id;                     
                        $res = mysql_query($query);
                        if($res)
                        {
                            while($row= mysql_fetch_object($res)){
                                $hours_per_day=$row->hours_per_day;

                            }
                        }   
                        
                        if($hours_per_day=="")
                        {
                            $hours_per_day = $config->emp_work_hours;
                        }
                        else
                        {
                            $hours_per_day=$hours_per_day;
                        }
                        
                        $query="SELECT work_station FROM hs_hr_employee where emp_number=".$employeeId;                       
                        $res = mysql_query($query);
                        if($res)
                        {
                            while($row= mysql_fetch_object($res)){                              
                               $work_station=$row->work_station;                            
                            }      
                        }
                        
                        $set=0;
                        $arr=explode(",",$config_workstation);
                        if(in_array($work_station, $arr)) 
                        {
                            $set=1;
                        }
                        $hours_per_day= str_pad($hours_per_day, 2, "0", STR_PAD_RIGHT);
                        echo $hours_per_day."/".$set;
         }
         else
         {
             echo $config->emp_work_hours."/0";
         }
    }   
    
    
    
    
function convertDurationToHours($durationInSecs)
{
    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
    
            $padHours = false;
            $hms = "";
            $hours = intval(intval($durationInSecs) / 3600);
            $hms .= ( $padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';
            $minutes = intval(($durationInSecs / 60) % 60);
            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
         
            $arr=explode(":",$hms);
            $hr=$arr[0];
            $mn=$arr[1];

            $fnlmn=round(($mn*100)/60);
            $final=$hr.".".$fnlmn;
            return $final;  
    
//    $timesheetTimeFormat = $config->timeformat;
//
//        if ($timesheetTimeFormat == ':') {
//            $padHours = false;
//            $hms = "";
//            $hours = intval(intval($durationInSecs) / 3600);
//            $hms .= ( $padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';
//            $minutes = intval(($durationInSecs / 60) % 60);
//            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
//            return $hms;
//        } elseif ($timesheetTimeFormat == '.') {
//
//            $padHours = false;
//            $hms = "";
//            $hours = intval(intval($durationInSecs) / 3600);
//            $hms .= ( $padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . '.' : $hours . '.';
//            $minutes = intval(($durationInSecs / 60) % 60);
//            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
//            return $hms;
//        }
//        else
//        {
//            $durationInHours = number_format($durationInSecs / (60 * 60), 2, '.', '');
//            return $durationInHours;
//        }

} 



?>