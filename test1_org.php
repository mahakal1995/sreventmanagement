<?php
//mysql_connect("localhost","root","inextrix") or die(mysql_error()); 
//mysql_select_db("hrm") or die(mysql_error()); 

 define('ROOT_PATH', dirname(__FILE__));
 require_once ROOT_PATH . '/lib/confs/Conf.php';
 $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
         $color_arr      = $config->color_arr;
        $DEFAULT_OT     =$config->DEFAULT_OT;
        
        
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());

//include 'config.php';
if($_REQUEST['function']=="session_remove")
{
     session_remove();
    
}
if($_REQUEST['function']=="get_expand")
{
     $id=$_REQUEST['id'];
     get_expand($id);
    
}
if($_REQUEST['function']=="get_collapse")
{
     $id=$_REQUEST['id'];
     get_collapse($id);
}

function get_expand($id)
{
//    global $color_arr;
require_once ROOT_PATH . '/lib/confs/Conf.php';
$config = new Conf();    
 $color_arr      = $config->color_arr;  
$workstation    =$config->emp_work_station;
$work_arr=explode(",", $workstation);
$str=$id;
$strlevel=explode("/",$str);
$level=$strlevel[0];

$background=$color_arr[$level];
$id=$strlevel[1];
$level++;
$flag=0;
$sl="'/'";
$count=0;
$arr=array();

$blank_html="<html> <body><table  ><tr><td>&nbsp</td></tr></table></body></html>";


$html="<html> <body><table ><tr style='background-color:".$color_arr[$level-1].";'>";
//  jagu                  $query = "SELECT id,name FROM ohrm_subunit WHERE  id='".$id."'";
//                    $main_result =mysql_query($query);
//                    if(mysql_num_rows($main_result) > 0)
//                    {
//                        while($lastrow= mysql_fetch_object($main_result ))
//                        {
//                          //$html.="<td style='text-align: center;background-color:".$color_arr[$level-2].";'><b>".wordwrap($lastrow->name,17,"<br>",true)."</b></td>";  
//                        }
//                    }

                    $count=0;
                    $query = "SELECT id,name FROM ohrm_subunit WHERE level=".$level." AND lft > (select lft from ohrm_subunit where id='".$id."') AND rgt < (select rgt from ohrm_subunit where id='".$id."')";
                    $main_result =mysql_query($query);
                    if(mysql_num_rows($main_result) > 0)
                    {
                        while($lastrow= mysql_fetch_object($main_result ))
                        { 
                            $query1 = "SELECT id,name FROM ohrm_subunit WHERE lft > (select lft from ohrm_subunit where id='".$lastrow->id."') AND rgt < (select rgt from ohrm_subunit where id='".$lastrow->id."')";
                            $main_result1 =mysql_query($query1);
                            
                            $count++;
                            $arr[]=array("id"=>$lastrow->id,"name"=>$lastrow->name);
                            $flag=1;
                            
                               $html.="<td style='text-align: center;'><b>".wordwrap($lastrow->name,17,"<br>",true)."</b>";
                               if(mysql_num_rows($main_result1) > 0)
                                    {
                                       $html.="<input type='button' value='+' name=".$level."/".$lastrow->id."/"." id="."btn".$level."/".$lastrow->id." onclick='level_exapnd(this.name+this.value);' />";
                                     }  
                                 $html.="</td>";     
                        }
                   }    
                $html.="</tr> </table> </body></html>";
                
                $html1="<table ><tr >";
// jagu                   <td style='background-color:".$color_arr[$level-2].";'><table><tr><td id='span1'><b>ST</b></td><td id='span2'><b>OT</b></td></tr></table></td>";   
                foreach($arr as $val)
                {
                    if(in_array($val['id'], $work_arr))
                    {
                      $html1.="<td id="."stot".$val['id']." style='background-color:".$background.";'><div><table><tr><td id='span1'  ><b>ST</b></td><td id='span2' ><b>OT</b></td></tr></table></div></td>";
                    }
                    else
                    {
                       $html1.="<td id="."stot".$val['id']." style='background-color:".$background.";'><div><table><tr><td id='span1'  ><b>ST</b></td></tr></table></div></td>";
                    }
                }
                $html1.="</tr></table>";
                if($flag==1)
                {
                      $body_arr=calculate_data($arr,$id,1);
                      $json=json_encode($body_arr);
                      echo $html."@".$html1."@".$json;
                }
                else echo "0";
}
function get_collapse($id)
{
    $str=$id;
    $strlevel=explode("/",$str);
    $level=$strlevel[0];
    $id=$strlevel[1];
    $dep_arr[]=array("id"=>$id);	
    $arr=array();
   
   
   $query = "SELECT id,level as level  FROM ohrm_subunit WHERE  lft > (select lft from ohrm_subunit where id='".$id."') AND rgt < (select rgt from ohrm_subunit where id='".$id."')";
                    $main_result =mysql_query($query);
                    if(mysql_num_rows($main_result) > 0)
                    {
                        while($lastrow= mysql_fetch_object($main_result ))
                        {
                            $val=$lastrow->level."/".$lastrow->id;
                            $arr[]=$val;
                        }
                    }
   $body_arr=calculate_data($dep_arr,$id,0);
   $json=json_encode($body_arr);
//   print_r(array_values($arr));
   echo json_encode($arr)."@".$json;
}
function calculate_data($dep_arr,$id,$flag)
{
require_once ROOT_PATH . '/lib/confs/Conf.php';
$config = new Conf();  
$workstation    =$config->emp_work_station;
$work_arr=explode(",", $workstation);

 session_start(); 
 $where=$_SESSION['where'];
 $proj_where=$_SESSION['proj_where'];
 $approved=$_SESSION['approved'];
 
 
    
$count=0;
$TST=0;$TOT=0;
$ST=0;$OT=0;

$final_arr=array();
//$proj_query="SELECT project_id,name FROM project".$proj_where;
$proj_query="SELECT t1.project_id,t1.customer_id,t1.name,t1.is_deleted,t2.customer_id,t2.name as cname FROM ohrm_project t1,ohrm_customer t2 where t1.customer_id=t2.customer_id and t1.is_deleted=0".$proj_where." order by t1.name ASC";
$proj_result =mysql_query($proj_query);
if($proj_result)
{
    while($proj_row= mysql_fetch_object($proj_result ))
    {
        $TST=0;$TOT=0;
 	if(count($dep_arr) > 0)
	{
	      $html="<table><tr style='background-color: #FFD1A6'>";
                   
//              if($flag==1)
//              {
//                   $side_st=0;$side_ot=0;
//		    $side_dep_query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM ohrm_timesheet_item WHERE employee_id
//                    IN (select emp_number from employee where work_station='".$id."') AND project_id ='".$proj_row->project_id."' ".$where;
//		    
//                    $side_dep_result1 =mysql_query($side_dep_query);
//                    if($side_dep_result1)
//                    {
//                      while($side_deprow1= mysql_fetch_object($side_dep_result1))
//                      {
//			$side_st=$side_deprow1->ST;
//                        $side_ot=$side_deprow1->OT;
//		      }
//		    } 
//              $html.="<td ><div><table><tr>
//                    <td id='span1'>".number_format((float)$side_st, 2, '.', '')."</td>
//                     <td  id='span2'>".number_format((float)$side_ot, 2, '.', '')."</td></tr></table></div></td>";
//              }  
               foreach($dep_arr as $lastrow)
	       { 
                    $ST=0;$OT=0;
                    if($approved=="")
                    {
                    $dep_query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM ohrm_timesheet_item WHERE employee_id
                    IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."'".$where;
		    }
		    else
		    {
		    $dep_query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM ohrm_timesheet_item WHERE employee_id
                    IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND timesheet_id IN (Select timesheet_id from ohrm_timesheet_action_log where action='APPROVED') ".$where;
		    }
                    $dep_result =mysql_query($dep_query);
                    
                    if($dep_result)
                    {
                      while($deprow= mysql_fetch_object($dep_result))
                      {		    							   			 
                        $ST=$deprow->ST;
                        $TST=$TST+$ST;
                        $OT=$deprow->OT;$TOT=$TOT+$OT;
                       }
                    }
                    $html.="<td id=P".$proj_row->project_id."/".$lastrow['id']."><div>
                    <table><tr>
                    <td id='span1'>";
		    if($ST==""){  $html.=number_format((float)0, 2, '.', '');}else{
		      $html.=number_format((float)$ST, 2, '.', '');
		    }
		    
                    if(in_array($lastrow['id'], $work_arr)){
                            $html.="</td><td  id='span2' >";
                            if($OT==""){$html.=number_format((float)0, 2, '.', '');}else{$html.=number_format((float)$OT, 2, '.', '');}
                             $html.="</td></tr></table>";
                    }
                    else
                    {
                        $html.="</td></tr></table>";
                    }
             }
             
//$html.="<div id='totalst' style='display: none'>".number_format((float)$TST, 2, '.', '')."</div><div id='totalot' style='display: none'>".number_format((float)$TOT, 2, '.', '')."</div>
  $html.="</div></td></tr></table>";

     }
     $html_total="<table><tr><td id=TOTALP".$proj_row->project_id."><div><table><tr>
         <td id='span1'>".number_format((float)$TST, 2, '.', '')."</td><td  id='span2' >".number_format((float)$TOT, 2, '.', '')."</td></tr></table></div></td></tr></table>";

$pid="P".$proj_row->project_id."/".$id;
$tid="TOTALP".$proj_row->project_id;
$final_arr[]=array("id"=>$pid,"table"=>$html);
//$final_arr[]=array($pid=>$html);
//$final_arr[]=array($tid=>$html_total);


                $diu_query="SELECT diu_id as id,name as diu_name FROM ohrm_diu where is_deleted=0 and project_id='".$proj_row->project_id."'";
		$diu_result =mysql_query($diu_query);
		if($diu_result)
		{
		    while($diu_row= mysql_fetch_object($diu_result ))
		    {
      $TST=0;$TOT=0;
     if(count($dep_arr) > 0)
	{
	  $dhtml="<table><tr style='background-color: #FFFAB3'>";
          
//          if($flag==1)
//              {
//                    $side_st=0;$side_ot=0;
//	   $side_diu_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT,project_id FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station='".$id."') AND project_id ='".$proj_row->project_id."' AND activity_id  IN (SELECT activity_id FROM activity WHERE diu_id='".$diu_row->id."' AND project_id='". $proj_row->project_id."') ".$where;
//	   
//		     $side_diu_result1 =mysql_query($side_diu_query1);
//			if($side_diu_result1)
//			{
//			    while($side_diurow= mysql_fetch_object($side_diu_result1 ))
//			    {	$side_st=$side_diurow->ST;
//				$side_ot=$side_diurow->OT;
//			    }
//			}
//              
//                $dhtml.="<td ><div><table><tr>
//                    <td id='span1'>".number_format((float)$side_st, 2, '.', '')."</td>
//                    <td  id='span2'>".number_format((float)$side_ot, 2, '.', '')."</td></tr></table></div></td>";
//              }
           foreach($dep_arr as $key=>$lastrow)
	    {   $ST=0;$OT=0;
                if($approved=="")
                {
                  $diu_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT,project_id FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND activity_id  IN (SELECT activity_id FROM activity WHERE diu_id='".$diu_row->id."' AND project_id='". $proj_row->project_id."') ".$where;
                }
                else
                {
                 $diu_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT,project_id FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND activity_id  IN (SELECT activity_id FROM activity WHERE diu_id='".$diu_row->id."' AND project_id='". $proj_row->project_id."') AND timesheet_id IN (Select timesheet_id from ohrm_timesheet_action_log where action='APPROVED') ".$where;
                }
		     $diu_result1 =mysql_query($diu_query1);
	             if($diu_result1)
			{
			    while($diurow= mysql_fetch_object($diu_result1 ))
			    {		    							   			 
                              $ST=$diurow->ST;$TST=$TST+$ST;
                              $OT=$diurow->OT; $TOT=$TOT+$OT; 
                            }
		        }
		         $dhtml.="<td id=P".$proj_row->project_id."D".$diu_row->id."/".$lastrow['id']."><div>
                             <table><tr><td id='span1'>";
		    if($ST==""){  $dhtml.=number_format((float)0, 2, '.', '');}else{
		      $dhtml.=number_format((float)$ST, 2, '.', '');
		    }
                    if(in_array($lastrow['id'], $work_arr))
                    {
                            $dhtml.="</td><td  id='span2' >";
                            if($OT==""){$dhtml.=number_format((float)0, 2, '.', '');}else{$dhtml.=number_format((float)$OT, 2, '.', '');}
                             $dhtml.="</td></tr></table>";
                    }
                    else
                    {
                        $dhtml.="</td></tr></table>";
                    }  
  }
//$dhtml.="<div id='totalst' style='display: none'>".number_format((float)$TST, 2, '.', '')."</div>
//    <div id='totalot' style='display: none'>".number_format((float)$TOT, 2, '.', '')."</div>
    $dhtml.="</div></td></tr></table>";	  
	}
 $dhtml_total="<table><tr><td id=TOTALP".$proj_row->project_id."D".$diu_row->id."><div><table><tr>
     <td id='span1'>".number_format((float)$TST, 2, '.', '')."</td>
     <td  id='span2' >".number_format((float)$TOT, 2, '.', '')."</td></tr></table></div></td></tr></table>";

$did="P".$proj_row->project_id."D".$diu_row->id."/".$id;
$tid="TOTALP".$proj_row->project_id."D".$diu_row->id;
$final_arr[]=array("id"=>$did,"table"=>$dhtml);
//$final_arr[]=array($did=>$dhtml);
//$final_arr[]=array($tid=>$dhtml_total);

                $act_query="SELECT activity_id,name FROM activity WHERE is_deleted='0' AND  diu_id='".$diu_row->id."' AND project_id='".$proj_row->project_id."'";
                $act_result =mysql_query($act_query);
                if($act_result)
                {
                    while($act_row= mysql_fetch_object($act_result ))
                    {
      $TST=0;$TOT=0;
        if(count($dep_arr) > 0)
	{
	   $ahtml="<table><tr>";
//           if($flag==1)
//              {
//                    $side_st=0;$side_ot=0;
//                   $side_act_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT,project_id FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station='".$id."') AND project_id ='".$proj_row->project_id."' AND 	activity_id  IN (SELECT activity_id FROM activity WHERE diu_id='".$diu_row->id."' AND project_id='". $proj_row->project_id."') AND activity_id='".$act_row->activity_id."' ".$where;
//                    $side_act_result1 =mysql_query($side_act_query1);
//                    
//                    if($side_act_result1)
//                    {
//                        while($side_actrow= mysql_fetch_object($side_act_result1 ))
//                        {
//                          $side_st=$side_actrow->ST;
//                          $side_ot=$side_actrow->OT;
//                        }
//                    } 
//
//                $ahtml.="<td ><div><table><tr>
//                    <td id='span1'>".number_format((float)$side_st, 2, '.', '')."</td>
//                     <td  id='span2'>".number_format((float)$side_ot, 2, '.', '')."</td></tr></table></div></td>";
//              }
           
           
          foreach($dep_arr as $key=>$lastrow)
	    {  $ST=0;$OT=0;
                if($approved=="")
                {
                    $act_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT,project_id FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND 	activity_id  IN (SELECT activity_id FROM activity WHERE diu_id='".$diu_row->id."' AND project_id='". $proj_row->project_id."') AND activity_id='".$act_row->activity_id."' ".$where;
                }
                else
                {
                    $act_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT,project_id FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND 	activity_id  IN (SELECT activity_id FROM activity WHERE diu_id='".$diu_row->id."' AND project_id='". $proj_row->project_id."') AND activity_id='".$act_row->activity_id."' AND timesheet_id IN (Select timesheet_id from ohrm_timesheet_action_log where action='APPROVED')".$where;
                }
            	$act_result1 =mysql_query($act_query1);
                if($act_result1)
                {
                    while($actrow= mysql_fetch_object($act_result1 ))
                    {
                        $ST=$actrow->ST;$TST=$TST+$ST;
                        $OT=$actrow->OT;$TOT=$TOT+$OT;
                    }
                }
                
                $ahtml.="<td id=P".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."/".$lastrow['id']."><div><table><tr>
                    <td id='span1'>";
		    if($ST==""){  $ahtml.=number_format((float)0, 2, '.', '');}else{
		      $ahtml.=number_format((float)$ST, 2, '.', '');
		    }
                    if(in_array($lastrow['id'], $work_arr))
                    {
                    
                        $ahtml.="</td><td  id='span2' >";
                        if($OT==""){$ahtml.=number_format((float)0, 2, '.', '');}else{$ahtml.=number_format((float)$OT, 2, '.', '');}
                        $ahtml.="</td></tr></table>";
                    }
                    else
                    {
                        $ahtml.="</td></tr></table>";
                    }
 }
//$ahtml.="<div id='totalst' style='display: none'>".number_format((float)$TST, 2, '.', '')."</div><div id='totalot' style='display: none'>".number_format((float)$TOT, 2, '.', '')."</div>
   $ahtml.=" </div></td></tr></table>";	  
	}
$ahtml_total="<table><tr><td id=TOTALP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."><div><table><tr>
    <td id='span1'>".number_format((float)$TST, 2, '.', '')."</td><td  id='span2' >".number_format((float)$TOT, 2, '.', '')."</td></tr></table></div></td></tr></table>";

$aid="P".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."/".$id;
$tid="TOTALP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id;
$final_arr[]=array("id"=>$aid,"table"=>$ahtml);		      
//$final_arr[]=array($aid=>$ahtml);
//$final_arr[]=array($tid=>$ahtml_total);


                $emp_query="SELECT t1.employee_id,t2.emp_firstname as name FROM `ohrm_timesheet_item` t1,employee t2 WHERE t1.activity_id='".$act_row->activity_id."' and t1.employee_id=t2.emp_number group by t1.employee_id ";
                $emp_result =mysql_query($emp_query);
                if($emp_result)
                {
                    while($emp_row= mysql_fetch_object($emp_result ))
                    {
                        $TST=0;$TOT=0;
                        if(count($dep_arr) > 0)
                        {
                            
                            $ehtml="<table><tr>";
//                            if($flag==1)
//                            {
//                                $side_st=0;$side_ot=0;
//                                $side_emp_query1="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station='".$id."')  AND project_id ='".$proj_row->project_id."' AND  activity_id='".$act_row->activity_id."' AND  employee_id='".$emp_row->employee_id."' ".$where;
//                                $side_emp_result1 =mysql_query($side_emp_query1);
//
//                                if($side_emp_result1)
//                                {
//                                    while($side_emprow= mysql_fetch_object($side_emp_result1 ))
//                                    {
//                                        $side_st=$side_emprow->ST;
//                                        $side_ot=$side_emprow->OT;
//                                    }
//                                } 
//
//                                $ehtml.="<td><div><table><tr>
//                                    <td id='span1'>".number_format((float)$side_st, 2, '.', '')."</td>
//                                    <td  id='span2'>".number_format((float)$side_ot, 2, '.', '')."</td></tr></table></div></td>";
//                            }
                          foreach($dep_arr as $key=>$lastrow)
                            { 
                                $ST=0;$OT=0;
                                if($approved=="")
                                {
                                   $emp_query1="SELECT date,sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND  activity_id='".$act_row->activity_id."' AND  employee_id='".$emp_row->employee_id."' ".$where;
                                }
                                else
                                {
                                   $emp_query1="SELECT date,sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT FROM ohrm_timesheet_item WHERE employee_id IN (select emp_number from employee where work_station IN(select id from ohrm_subunit where lft >= (select lft from ohrm_subunit where id='".$lastrow['id']."') AND rgt <= (select rgt from ohrm_subunit where id='".$lastrow['id']."'))) AND project_id ='".$proj_row->project_id."' AND  activity_id='".$act_row->activity_id."' AND  employee_id='".$emp_row->employee_id."' AND timesheet_id IN (Select timesheet_id from ohrm_timesheet_action_log where action='APPROVED')  " .$where;
                                }
                                $emp_result1 =mysql_query($emp_query1);
                                $totalst=0;$totalot=0;
                                if($emp_result1)
                                {
                                    while($emprow= mysql_fetch_object($emp_result1 ))
                                    {
                                        $ST=$emprow->ST;$TST=$TST+$ST;
                                        $OT=$emprow->OT;$TOT=$TOT+$OT;
                                        $totalst+=$ST;$totalot+=$OT;
                                    }
                                }
 $ehtml.="<td id=P".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."E".$emp_row->employee_id."/".$lastrow['id']."><div><table><tr>
     <td id='span1'>";
		    if($ST==""){  $ehtml.=number_format((float)0, 2, '.', '');}else{
		      $ehtml.=number_format((float)$ST, 2, '.', '');
		    }
                     if(in_array($lastrow['id'], $work_arr))
                     {       
                            $ehtml.="</td><td  id='span2' >";
                            if($OT==""){$ehtml.=number_format((float)0, 2, '.', '');}else{$ehtml.=number_format((float)$OT, 2, '.', '');}
                            $ehtml.="</td></tr></table>";                                
                     }
                     else
                     {
                            $ehtml.="</td></tr></table>"; 
                     }  
                               
                 
}
//$ehtml.="<div id='totalst' style='display: none'>".number_format((float)$TST, 2, '.', '')."</div><div id='totalot' style='display: none'>".number_format((float)$TOT, 2, '.', '')."</div>
    $ehtml.="</div></td></tr></table>";	  

}
$ehtml_total="<table><tr><td id=TOTALP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."E".$emp_row->employee_id."><div><table><tr>
    <td id='span1'>".number_format((float)$TST, 2, '.', '')."</td><td  id='span2' >".number_format((float)$TOT, 2, '.', '')."</td></tr></table></div></td></tr></table>";

$eid="P".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."E".$emp_row->employee_id."/".$id;
$tid="TOTALP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."E".$emp_row->employee_id;
$final_arr[]=array("id"=>$eid,"table"=>$ehtml);		      

        }}}}}}
    }

}
   return $final_arr;
}
function session_remove()
{
$_SESSION['where']="";
$_SESSION['proj_where']="";
$_SESSION['approved']="";
}



?> 
 