<?php
//Rushika & jagruti
//echo "hiiiiiii";exit;
  require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());
	$sql="select max(timesheet_id) as timesheet_id from ohrm_timesheet";
        $maxTimesheetId = mysql_query($sql);
        $arr=array();
        $max=0;         
        
        while($row= mysql_fetch_object($maxTimesheetId))
        {
	    $max=$row->timesheet_id;    
        }
        $arr=explode("/",$employeeId);  
        
        $numberOfDays=cal_days_in_month(CAL_GREGORIAN, $arr[1], $arr[2]);
	
        $j=1;    
       
        for($i=1;$i<6;$i++)
        {
	    if($i==5)
	    {
	      
		if($numberOfDays>28)
		{
		  
		    $startday=$j;
		    $endday=$numberOfDays;
		    $startDate=$arr[2]."-".$arr[1]."-".$startday;
		    $endDate=$arr[2]."-".$arr[1]."-".$endday;
                    $sel_sql="Select count(timesheet_id) as timesheet_id from  ohrm_timesheet where start_date='".$startDate."' and end_date='".$endDate."' and employee_id='".$arr[0]."'";
//                    echo $sel_sql;exit;
		    $sel_result = mysql_query($sel_sql);
                    while($row= mysql_fetch_object($sel_result))
                    {
                    $timesheet_id=$row->timesheet_id;    
                    } 
                    if($timesheet_id <= 0 )
                    {
                            $max++;
          		    $sql="Insert into ohrm_timesheet values(".$max.",'NOT SUBMITED','".$startDate."','".$endDate."',".$arr[0].")";
                	    $sql1 = mysql_query($sql);
                    }       
		}
	    }
	    else
	    {	
                
                $startday=$j;
                $endday=$i*7;
                $startDate=$arr[2]."-".$arr[1]."-".$startday;
                $endDate=$arr[2]."-".$arr[1]."-".$endday;
                $sel_sql="Select count(timesheet_id) as timesheet_id from ohrm_timesheet where start_date='".$startDate."' and end_date='".$endDate."' and employee_id='".$arr[0]."'";
                $sel_result = mysql_query($sel_sql);
                    while($row= mysql_fetch_object($sel_result))
                    {
                    $timesheet_id=$row->timesheet_id;    
                    }
                    if($timesheet_id <= 0 )
                    {
                        $max++;
                        $sql="Insert into ohrm_timesheet values(".$max.",'NOT SUBMITED','".$startDate."','".$endDate."',".$arr[0].")";
                        $sql1 = mysql_query($sql);
                    }   
	    }
	    $j+=7;
        }
//        echo "completed";
?>
