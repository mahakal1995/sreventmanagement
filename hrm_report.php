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
        $DEFAULT_OT     =$config->DEFAULT_OT;
        $workstation    =$config->emp_work_station;
        $work_arr=explode(",", $workstation);



        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());
//include 'config.php';
// header("Content-Disposition: filename=jugni.xls");
//global $color_arr;
$proj_arr=array();
$proj_query="SELECT t1.project_id,t1.customer_id,t1.name,t1.is_deleted,t2.customer_id,t2.name as cname FROM ohrm_project t1,ohrm_customer t2 where t1.customer_id=t2.customer_id and t1.is_deleted=0 order by t1.name ASC";
//$proj_query="SELECT project_id,name FROM project where is_deleted=0";
// echo $proj_query;

$proj_result =mysql_query($proj_query);
if($proj_result)
{
    while($proj_row= mysql_fetch_object($proj_result ))
    {
       $proj_arr[]=array("project_id"=>$proj_row->project_id,"name"=>$proj_row->name." - ".$proj_row->cname);
    }
}
?>
<a id="dlink"  style="display:none;"></a>
<form id="theForm" method="POST" action="hrm_report.php">
    <table style="background-color: #B7FFED">
        <tr><td colspan="2" style="text-align: center;"><b>REPORT</b></td></tr>
        <tr><td style="text-align: right;" width="50%" ><b>Project Name</b><label style="color: red">*</label></td>
            <td><select name="project_id" id="project_id">

<!--          Changed By :Rushika Changes: replace --Select Project Name-- with All     -->
                    <option value="">All</option>
                    <?
                    foreach ($proj_arr as $val)
                    {?>
                    <option value="<?=$val['project_id']?>"><?=  strtoupper($val['name']);?></option>
                    <?}?>
                </select></td>
        </tr>
        <tr>
            <td style="text-align: right;"><b>Project Date Range:</b><label style="color: red">*</label></td>
            <td style="text-align: left;"><b>From:&nbsp;</b><input placeholder="yyyy-mm-dd" title="Enter From date" type="date" name="from_date" id="from_date" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : null; ?>"/>
            <b>To:&nbsp;</b><input placeholder="yyyy-mm-dd" title="Enter To date" type="date" name="to_date" id="to_date" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : null; ?>"/></td>
        </tr>
        <tr>
	     <td style="text-align: right;"><b>Include Hours:</b><label style="color: red">*</label></td>
	     <td><select name="approved" id="approved">
	      <option value="">All</option>
	      <option value="1">Approved</option>
	     </select></td>
        </tr>
        <tr><td colspan="2"  style="text-align: center;">
                <input type="button" value="Search" onclick="submitform();"/>
                <input type="button" id="reset" name="reset" value="Reset" onclick="resetdiv();" /></td> </tr>
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

   (function() {
      var elem = document.createElement('input');
      elem.setAttribute('type', 'date');

      if (elem.type === 'text' ) {
          $('#from_date').datepicker({
                dateFormat: 'yy-mm-dd',
                maxDate: +0
//                changeMonth: true,
//                changeYear: true
        });
         $('#to_date').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: +0
         });
      }
   })();
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
        , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
        return function (table, name, filename) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }

            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();

        }
    })();

</script>
<?
    $start="";
    $end="";
    $project_id="";
    $approved="";


    //print_r($_POST);
 if(isset($_POST) && (isset($_POST['project_id']) || isset($_POST['approved'])))
 {
// 	echo("<pre>");print_r($_POST);
// 	exit;
        $project_id=$_POST['project_id'];
        $start=$_POST['from_date'];
        $end=$_POST['to_date'];
        $approved=trim($_POST['approved']);
        ?>
    <script type="text/javascript">
        var project_id="<?=$project_id?>";
        $('#project_id').val(project_id);
        var approved="<?=$approved?>";
        $('#approved').val(approved);
    </script>
    <?php
 }
 //jagruti 20131014
$max_level=0;

if(isset($_POST) && count($_POST)>0)
{
//    echo "in post";
//print_r($_POST);
//exit;
$str="";
$proj_str="";

if($start!="")
{
  $str.=" AND date >= '".$start."'";
}
if($end!="")
{
  $str.=" AND date <= '".$end."'";
}
$where=$str." GROUP BY project_id";

if($project_id!="")
{
  $proj_str.=" AND  t1.project_id='".$project_id."' ";
}
//else
//{
//    $proj_str.=" is_deleted=0";
//}
$proj_where=$proj_str;
//echo $proj_where;

session_start();
$_SESSION['where']=$where;
$_SESSION['proj_where']=$proj_where;
$_SESSION['approved']=$approved;


    $first_level=array();
    $dep_arr=array();

    $count=0;
    $max_level=0;
    $max_query = "SELECT level,count(level) as count FROM ohrm_subunit group by level";
    $max_result =mysql_query($max_query);
    if(mysql_num_rows($max_result) > 0)
    {
        while($maxrow= mysql_fetch_object($max_result ))
        {
            $level_count[]=array("level"=>$maxrow->level,"level_count"=>$maxrow->count);
        }
    }

   $max_level=max(array_keys($level_count));
   $max_level++;
   $level=array();
    for($i=1;$i<$max_level;$i++)
    {
        $level[$i]=getarray($i);
    }
?>

    <input type="button" onclick="exportDiv('dvdata')" value="Export" />
    <input type="button" onclick="printDiv('dvdata')" value="Print" />
<html>
    <body>
        <div id="dvdata">

        <table  id="tbl-1">

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
                <tr>
                    <td>project/Organization</td>
                    <?php
                    $query = "SELECT id, name, lft, rgt, level FROM subunit WHERE level=1";
                    $main_result =mysql_query($query);
                   if(mysql_num_rows($main_result) > 0)
                    {
                        while($lastrow= mysql_fetch_object($main_result ))
                        {
                            $flag=0;
                            $query1 = "SELECT id,name FROM ohrm_subunit WHERE lft > (select lft from ohrm_subunit where id='".$lastrow->id."') AND rgt < (select rgt from ohrm_subunit where id='".$lastrow->id."')";
                            $main_result1 =mysql_query($query1);
                        ?>
                        <td  style="background-color: #BBFFA8"><b><?=wordwrap($lastrow->name,17,"<br>",true);?></b>
                        <?if(mysql_num_rows($main_result1) > 0)
                         {
                            $flag=1;
                            ?>
                             <input type="button" value="+" name="<?="1/".$lastrow->id."/"?>" id="<?="btn1/".$lastrow->id?>" onclick="level_exapnd(this.name+this.value);" />
                        <?}?>
                        </td>
                        <?
                            $level1[]=array("id"=>$lastrow->id);
                        }
                    }?>
                  <td style="background-color: #BBFFA8"><b>Total</b></td>
                   <td style="background-color: #BBFFA8"><b>Total</b></td>
                </tr>
                <?
              for($i=1;$i<$max_level;$i++)
                {
                ?>
                    <tr style="display: none;background-color:<?=$color_arr[$i]?>"><td></td>
                            <?
                            $arr=array();
                            foreach($level1 as $key=> $val)
                            {
                               $arr=get_id_count($val['id'],$i);
                                if(count($arr)>1){?>
                                <td style="background-color: ''">
                                    <table>
                                        <tr>
                                            <?foreach($arr as $key1=>$val1)
                                            {
                                                if(!array_key_exists("count",$val1))
                                                {
                                                ?>
                                            <td colspan="0" style="text-align: center;" colspan="2" id="<?=$i."/".$val1['id']?>"></td>
                                           <?}}?>
                                        </tr>
                                    </table>
                                </td>
                                <?}else{?>
                                <td colspan="0" id="<?=$i."/".$val['id']?>">  </td>
                              <?}}?>
                      <td ></td>
                      <td ></td>
                    </tr>
                <?}?>
                    <tr style="background-color: #BBFFA8" >
                        <td></td>
                    <?foreach($level1 as $key=> $val)
                        {


                         ?><td id="<?="stot".$val['id'];?>">
                         <?if(in_array($val['id'], $work_arr))
			  {?>
                             <table style="border: 0px solid black; "><tr><td style='background-color: #BBFFA8;border:none; '><b>ST</b></td><td style='background-color: #BBFFA8;border:none '><b>OT</b></td></tr></table>
                             <?}else{?>
                             <table style="border: 0px solid black; "><tr><td style='background-color: #BBFFA8;border:none; '><b>ST</b></td></tr></table>
                             <?}?>
                           </td><?
                         }
                        ?>
                     <td >

                     <table style='border: none;'><tr><td style='border: none;'><b>ST</b></td><td style='border: none;'><b>OT</b></td></tr></table>
                     </td>
                     <td ><b>COST(INR)</b></td>
                    </tr>

<?php
$dep_arr=$level1;
//echo "<pre>";
//print_r($dep_arr);
$proj_query="SELECT t1.project_id,t1.customer_id,t1.name,t1.is_deleted,t2.customer_id,t2.name as cname FROM ohrm_project t1,ohrm_customer t2 where t1.customer_id=t2.customer_id and t1.is_deleted=0".$proj_where." order by t1.name ASC";
//$proj_query="SELECT project_id,name FROM project WHERE ".$proj_where;

$proj_result =mysql_query($proj_query);
if($proj_result)
{
    while($proj_row= mysql_fetch_object($proj_result ))
    {
        $pt=get_total($proj_row->project_id,$where);
        if($pt)
        {
        ?>

<tr style="background-color:#FFD1A6;">
    <td align="right">
        <div>
            <div id='span1'><input type="checkbox" name="chk" id="<?="chk_id".$proj_row->project_id?>"/></div>
            <div id='span2' ><b><?=$proj_row->name." - ".$proj_row->cname;?></b><input  type="button" id="<?="project_id".$proj_row->project_id?>" name="pclick" value="+" onclick="expand(this.id);"/></div>
        </div>
    </td>

<?php
        $TST=0;$TOT=0;$totalprojcost=0;
 	if(count($dep_arr) > 0)
	{
               foreach($dep_arr as $key=>$lastrow)
	       {
                    $ST=0;$OT=0;
//                    echo $_POST['approved'];
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

//echo  $dep_query;exit;
                    $dep_result =mysql_query($dep_query);
//                    $totalst=0;$totalot=0;
                    if($dep_result)
                    {
                      while($deprow= mysql_fetch_object($dep_result))
                      {
                        $ST=$deprow->ST;
                        $TST=$TST+$ST;
                        $OT=$deprow->OT;$TOT=$TOT+$OT;
//                        $totalst+=$ST;$totalot+=$OT;
                       }
                    }
                    ?>
    <td id="<?="P".$proj_row->project_id."/".$lastrow['id']?>">
        <table style="border: none;">
            <tr>

		<td style="border: none;"><?if($ST==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$ST, 2, '.', '');}?></td>
		<?if(in_array($lastrow['id'], $work_arr)){?>
                <td style="border: none;"><?if($OT==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$OT, 2, '.', '');}?></td>
                <?}?>
            </tr>
        </table>
<!--            <div id="totalst" style="display: none"><?//=number_format((float)$totalst, 2, '.', '');?></div><div id="totalot" style="display: none"><?//=number_format((float)$totalot, 2, '.', '');?></div>-->
    </td>

            <?}
     }?>
<td id="<?="TOTALP".$proj_row->project_id;?>">
    <table style="border: none;">
        <tr>
            <td style="border: none;"><?=number_format((float)$TST, 2, '.', '');?></td>
            <td style="border: none;"><?=number_format((float)$TOT, 2, '.', '');?></td>
        </tr>
    </table>
</td>
<td id="<?="COSTP".$proj_row->project_id;?>"></td>
</tr>
<?php
// 		echo $proj_row->project_id;
                $diu_query="SELECT diu_id as id ,name as diu_name FROM ohrm_diu where is_deleted=0 and project_id='".$proj_row->project_id."'";
//                echo $diu_query;
                $diu_result =mysql_query($diu_query);

		if($diu_result)
		{
		    while($diu_row= mysql_fetch_object($diu_result ))
		    {
                        $dt=get_total_diu($proj_row->project_id,$diu_row->id,$where);
                        if($dt)
                        {

?>
    <tr id="<?="duirow".$proj_row->project_id?>" class="<?="diurow".$proj_row->project_id?>" style="display:none;background-color: #FFFAB3";><td align="right"><font color="purpole"><?=$diu_row->diu_name?></font><input  type="button" id="<?="diu_id".$diu_row->id?>" class="<?="btndiu".$proj_row->project_id?>" name="diuclick" value="+" onclick="expand(this.id);"/></td>
<?php
      $TST=0;$TOT=0;$totaldiucost=0;
     if(count($dep_arr) > 0)
	{
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


//                echo $diu_query1."\n";
		     $diu_result1 =mysql_query($diu_query1);
//			 $totalst=0;$totalot=0;
                        if($diu_result1)
			{
			    while($diurow= mysql_fetch_object($diu_result1 ))
			    {
                              $ST=$diurow->ST;$TST=$TST+$ST;
                              $OT=$diurow->OT; $TOT=$TOT+$OT;
//                              $totalst+=$ST;$totalot+=$OT;
                            }
		        }
                      ?>
                    <td id="<?="P".$proj_row->project_id."D".$diu_row->id."/".$lastrow['id']?>">
                         <table style="border: none;">
                            <tr>
                                <td style="border: none;"><?if($ST==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$ST, 2, '.', '');}?></td>
                                    <?if(in_array($lastrow['id'], $work_arr)){?>

                                    <td style="border: none;"><?if($OT==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$OT, 2, '.', '');}?></td>
                                <?}?>
                            </tr>
                         </table>

                    </td>
<? }}?>
<td id="<?="TOTALP".$proj_row->project_id."D".$diu_row->id?>">
    <table style='border: none;'>
        <tr>
            <td style='border: none;'><?=number_format((float)$TST, 2, '.', '');?></td>
            <td style='border: none;'><?=number_format((float)$TOT, 2, '.', '');?></td>
        </tr>
    </table>
</td>
<td id="<?="COSTP".$proj_row->project_id."D".$diu_row->id?>"></td>
</tr>
<?php
                $act_query="SELECT activity_id,name FROM activity WHERE is_deleted='0' AND diu_id='".$diu_row->id."' AND project_id='".$proj_row->project_id."'";
                $act_result =mysql_query($act_query);
                if($act_result)
                {
                    while($act_row= mysql_fetch_object($act_result ))
                    {

                        $at=get_total_act($proj_row->project_id,$diu_row->id,$act_row->activity_id,$where);
                        if($at)
                        {
?>
<tr id="<?="actrow".$proj_row->project_id;?>" class="<?="actrow".$diu_row->id?>" style="display:none;background-color:#FED9FF"><td align="right"><font><?=$act_row->name?></font><input  type="button" id="<?="activity_id".$act_row->activity_id?>" class="<?="btnact".$diu_row->id?>" name="actclick" value="+" onclick="expand(this.id);"/></td>
 <?php
        $TST=0;$TOT=0;$totalactcost=0;
        if(count($dep_arr) > 0)
	{
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
//		 $totalst=0;$totalot=0;
                if($act_result1)
                {
                    while($actrow= mysql_fetch_object($act_result1 ))
                    {
                        $ST=$actrow->ST;$TST=$TST+$ST;
                        $OT=$actrow->OT;$TOT=$TOT+$OT;
//                        $totalst+=$ST;$totalot+=$OT;
                        }
                }

                ?>
                 <td id="<?="P".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."/".$lastrow['id']?>" >

                    <table style="border: none;">
                            <tr>
                                <td style="border: none;"><?if($ST==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$ST, 2, '.', '');}?></td>
                                 <?if(in_array($lastrow['id'], $work_arr)){?>
                                <td style="border: none;"><?if($OT==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$OT, 2, '.', '');}?></td>
                                <?}?>
                            </tr>
                    </table>
                 </td>
<?}}?>
<td id="<?="TOTALP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id?>">
 <table style='border: none;'>
        <tr>
            <td style='border: none;'><?=number_format((float)$TST, 2, '.', '');?></td>
            <td style='border: none;'><?=number_format((float)$TOT, 2, '.', '');?></td>
        </tr>
    </table>
</td>
<td id="<?="COSTP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id?>"></td>
</tr>
<?php
                $emp_query="SELECT t1.employee_id,t2.emp_firstname as name,t2.rate_per_hours FROM `ohrm_timesheet_item` t1,employee t2 WHERE t1.activity_id='".$act_row->activity_id."' and t1.employee_id=t2.emp_number group by t1.employee_id ";
//                echo $emp_query;

                $emp_result =mysql_query($emp_query);
                if($emp_result)
                {
                    while($emp_row= mysql_fetch_object($emp_result ))
                    {
                        $emp=get_total_emp($proj_row->project_id,$diu_row->id,$act_row->activity_id,$emp_row->employee_id,$where);
                        if($emp)
                        {
?>
<tr id="<?="emprow".$proj_row->project_id;?>" class="<?="emprow".$act_row->activity_id?>" style="display:none;background-color:wheat"><td align="right"><font color="blue"><?=$emp_row->name?></font></td>
  <?php
        $TST=0;$TOT=0;$totalcost=0;
        if(count($dep_arr) > 0)
	{
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

                if($emp_result1)
                {
                    while($emprow= mysql_fetch_object($emp_result1 ))
                    {
                        $ST=$emprow->ST;$TST=$TST+$ST;
                        $OT=$emprow->OT;$TOT=$TOT+$OT;
                        $totalcost+=get_total_cost($emp_row->rate_per_hours,$ST,$emprow->date,$OT);
                        $totalactcost+=$totalcost;
                        $totaldiucost+=$totalcost;
                        $totalprojcost+=$totalcost;
                    }
                }
                ?>
                 <td id="<?="P".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."E".$emp_row->employee_id."/".$lastrow['id']?>" >
                     <table style="border: none;">
                            <tr>
                                <td style="border: none;"><?if($ST==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$ST, 2, '.', '');}?></td>
                                  <?if(in_array($lastrow['id'], $work_arr)){?>
                                <td style="border: none;"><?if($OT==""){echo number_format((float)0, 2, '.', '');}else{echo number_format((float)$OT, 2, '.', '');}?></td>
                                <?}?>
                            </tr>
                     </table>
                 </td>
<?}}?>
<td id="<?="TOTALP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id."E".$emp_row->employee_id?>">
 <table style='border: none;'>
        <tr>
            <td style='border: none;'><?=number_format((float)$TST, 2, '.', '');?></td>
            <td style='border: none;'><?=number_format((float)$TOT, 2, '.', '');?></td>
        </tr>
    </table>
</td>
<td><?=number_format((float)$totalcost, 2, '.', '');?></td>
</tr>
<?}}}
?>
  <script type="text/javascript">
        var acost='<?=$totalactcost?>';
        var aid='<?="COSTP".$proj_row->project_id."D".$diu_row->id."A".$act_row->activity_id?>';
        document.getElementById(aid).innerHTML = acost;
  </script>
<?
}}}
?>
  <script type="text/javascript">

        var dcost='<?=$totaldiucost?>';
        var did='<?="COSTP".$proj_row->project_id."D".$diu_row->id?>';
        document.getElementById(did).innerHTML = dcost;
  </script>
<?
}}}?>
<?
?>
  <script type="text/javascript">

        var pcost='<?=$totalprojcost?>';
        var pid='<?="COSTP".$proj_row->project_id?>';
        document.getElementById(pid).innerHTML = pcost;
  </script>
<?

}}}?>
        </table>
            <div>
    </body>
</html>
<br><br>

<?
}
//jagruti 20131014
?>

<!--==========================================================================================================================-->
<!--==========================================================================================================================-->
<!--==========================================================================================================================-->

<script type="text/javascript">
function hideButton(id)
{
//    alert(id);
}
function printDiv(id) {

     var printContents = document.getElementById(id).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;

}
function exportDiv(id) {
	tableToExcel('tbl-1', 'worksheetname', 'myfile.xls');
}
function resetdiv()
{
    $('#project_id').val("");
    $('#from_date').val("");
    $('#to_date').val("");
    $('#approved').val("");
    $.ajax({
           url: "test1.php",
           async: false,
           data:"function=session_remove",
           success: function(data)
            {
                submitform();
           }
       });
}
function submitform()
{
//alert("hello search");
  document.getElementById("theForm").submit();
}
function level_exapnd(val)
{
    var tbl = document.getElementById("tbl-1");
    var flag=0;
    var work_station="<?=$workstation?>";
    var work_arr = new Array();
    var work_arr=work_station.split(",");



    var main_stot="<table><tr><td>\n\
        <table style='border: none;'><tr><td style='border: none;'><b>ST</b></td></tr></table></td></tr></table>";


    var level="<?=$max_level-1;?>";
    var ret,td,td1,ind,stot,id1,stot_index;
    var str=val.split("/");
    var rownum=parseInt(str[0]);
    var id=str[1];

    var sign=str[2];
    td=str[0]+"/"+id;
//    alert(td);

    td1="stot"+id;
    var jsArray =new Array();
    var json_body=new Array();
    if(sign=="+")
    {
        tbl.rows[rownum].style.display = '';
         document.getElementById("btn"+td).value = '-';
        $.ajax({
           url: "test1.php",
           async: false,
           data:"id="+td+ "&function=get_expand",
           success: function(data) {
//               alert(data);
            if(data!=0)
                {
                    var html=data.split("@");
                    document.getElementById(td).innerHTML = html[0];
                    document.getElementById(td1).innerHTML = html[1];
                    var json_body=$.parseJSON(html[2]);
                    json_body.filter(function (prj)
                    {
                        var kid=prj.id;
                        var ktable=prj.table;
//                        var tid=prj.tid;
//                        var ttable=prj.ttable;

                       document.getElementById(kid).innerHTML = ktable;
                    });
                }
           }
       });
    }
    else
    {
    for(i=0; i < work_arr.length; i++){
    if(work_arr[i] === id)

      main_stot="<table><tr><td>\n\
<table style='border: none;'><tr><td style='border: none;'><b>ST</b></td><td style='border: none;'><b>OT</b></td></tr></table></td></tr></table>";
     };
       document.getElementById("btn"+td).value = '+';
        $.ajax({
           url: "test1.php",
           async: false,
           data:"id="+td+ "&function=get_collapse",
           success: function(data) {
            var html=data.split("@");
            jsArray=JSON.parse(html[0]);
              for(j=0;j<jsArray.length;j++)
              {
                  ind=jsArray[j];
                  stot=ind.split("/");
                  id1=stot[1];
                  stot_index="stot"+id1;
                  if(document.getElementById(stot_index)!=null)
                     document.getElementById(stot_index).innerHTML =main_stot;
                     document.getElementById(jsArray[j]).innerHTML ="";
              }

              var json_body=$.parseJSON(html[1]);
                json_body.filter(function (prj) {
                    var kid=prj.id;
                    var ktable=prj.table;
//                    var tid=prj.tid;
//                    var ttable=prj.ttable;
                   document.getElementById(kid).innerHTML = ktable;
                });
           }
       });
        document.getElementById(td).innerHTML ="";
        document.getElementById(td1).innerHTML =main_stot;
    }
}

function expand(val)
{
// alert(val);
 var id=val.replace(/[^0-9]+/ig,"");
 var i=0,j=0;
 var org_var= document.getElementById(val).value;
 var tr=document.getElementsByTagName('tr');
 var btn="pclick";
 var btn1="empclick";
 var thisname="emprow";
 if(org_var=="+")
  {
    document.getElementById(val).value = '-';
        if (val.indexOf("project_id") >= 0)
        {
            for (i=0;i<tr.length;i++)
            {
                if (tr[i].className == "diurow"+id)
                {
                   tr[i].style.display = '';
                }
                if (tr[i].id == "actrow"+id)
                {
                   tr[i].style.display = 'none';
                }
                if (tr[i].id == "emprow"+id)
                {
                   tr[i].style.display = 'none';
                }
            }
            btn=document.getElementsByName("diuclick");
            for(j=0;j<btn.length;j++)
            {
                if(btn[j].className == "btndiu"+id)
                     btn[j].value = '+';
            }
            btn=document.getElementsByName("actclick");
            for(j=0;j<btn.length;j++)
            {
                if(btn[j].className == "btnact"+id)
                     btn[j].value = '+';
            }
        }
        if(val.indexOf("diu_id") >= 0)
        {
           for (i=0;i<tr.length;i++)
            {
                if (tr[i].className == "actrow"+id)
                {
                   tr[i].style.display = '';
                }
                if (tr[i].id == "emprow"+id)
                {
                   tr[i].style.display = 'none';
                }
            }
            btn=document.getElementsByName("actclick");
            for(j=0;j<btn.length;j++)
            {
                 if(btn[j].className == "btnact"+id)
                  btn[j].value = '+';
            }
        }

        if(val.indexOf("activity_id") >= 0)
        {
           for (i=0;i<tr.length;i++)
            {
                if (tr[i].className == "emprow"+id)
                {
                   tr[i].style.display = '';
                }

            }
        }
  }
 else
   {
        document.getElementById(val).value = '+';
        if (val.indexOf("project_id") >= 0)
        {
            for (i=0;i<tr.length;i++)
            {
                if (tr[i].className == "diurow"+id)
                {
                   tr[i].style.display = 'none';
                }
                if (tr[i].id == "actrow"+id)
                {
                   tr[i].style.display = 'none';
                }
                if (tr[i].id == "emprow"+id)
                {
                   tr[i].style.display = 'none';
                }
            }
        }
        if(val.indexOf("diu_id") >= 0)
        {
           for (i=0;i<tr.length;i++)
            {
                if (tr[i].className == "actrow"+id)
                {
                   tr[i].style.display = 'none';
                }
                if (tr[i].id == "emprow"+id)
                {
                   tr[i].style.display = 'none';
                }
            }
        }
        if(val.indexOf("activity_id") >= 0)
        {
           for (i=0;i<tr.length;i++)
            {
                if (tr[i].className == "emprow"+id)
                {
                   tr[i].style.display = 'none';
                }
            }
        }


    }
}
</script>
<?
function getarray($level)
{
    $count=0;
    $level1=array();

    $query = "SELECT id, name, lft, rgt, level FROM subunit WHERE level='".$level."'";
                $main_result =mysql_query($query);
                if(mysql_num_rows($main_result) > 0)
                {
                    while($lastrow= mysql_fetch_object($main_result ))
                    {
                        $count++;
                        $level1[]=array("id"=>$lastrow->id);
                    }
                }
    $level1[]=array("count"=>$count);
    return $level1;
}
function get_id_count($id,$level)
{
    $count=0;
    $level1=array();
    $query = "SELECT id from ohrm_subunit where level='".$level."' and lft > (select lft from ohrm_subunit where id='".$id."') AND rgt < (select rgt from ohrm_subunit where id='".$id."')";
                $main_result =mysql_query($query);
                if(mysql_num_rows($main_result) > 0)
                {
                    while($lastrow= mysql_fetch_object($main_result ))
                    {
                        $count++;
                        $level1[]=array("id"=>$lastrow->id);
                    }
                }
    $level1[]=array("count"=>$count);
    return $level1;
}
function get_total_cost($ST_RATE,$ST,$DATE,$OT)
{
    global $DEFAULT_OT;

    $OT_RATE=$DEFAULT_OT;
//    if($ST_RATE == 0 || $ST_RATE == null || $ST_RATE == "")
//       $ST_RATE=1;

    $totalst=$ST*$ST_RATE;
    $query="SELECT multiply FROM ohrm_ot_config WHERE start_date <= '".$DATE."' AND end_date >= '".$DATE."'";
    $main_result =mysql_query($query);

    if(mysql_num_rows($main_result) > 0)
    {
        while($row= mysql_fetch_object($main_result))
        {
           $OT_RATE= $row->multiply;
        }
    }
//    if($OT_RATE == 0 || $OT_RATE == null || $OT_RATE == "")
//       $OT_RATE=1;
    $totalot=$OT*$OT_RATE;
    $totalot=$totalot*$ST_RATE;
    $final=$totalst+$totalot;
    return $final;

}
function get_total($pid,$where)
{
    $st=0;
    $ot=0;
    $query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM
        ohrm_timesheet_item WHERE project_id ='".$pid."'".$where;
    $main_result =mysql_query($query);
       while($row= mysql_fetch_object($main_result))
        {
           $st=$row->ST;
           $ot=$row->OT;
        }
    if($st > 0 || $ot >0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
function get_total_diu($pid,$dui_id,$where)
{
//    return true;
    $st=0;
    $ot=0;
    $query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM
        ohrm_timesheet_item WHERE project_id ='".$pid."' AND activity_id IN(select activity_id from ohrm_project_activity where project_id='".$pid."' and diu_id > 0 and diu_id='".$dui_id."')".$where;
    $main_result =mysql_query($query);
       while($row= mysql_fetch_object($main_result))
        {
           $st=$row->ST;
           $ot=$row->OT;
        }
    if($st > 0 || $ot > 0)
    {
        return true;
    }
    else
    {
        return false;
    }

}
function get_total_act($pid,$did,$aid,$where)
{
    $st=0;
    $ot=0;
    $query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM
        ohrm_timesheet_item WHERE project_id ='".$pid."'  AND activity_id IN(select activity_id from ohrm_project_activity where project_id='".$pid."' and diu_id > 0 and diu_id='".$did."') AND activity_id='".$aid."'".$where;

    $main_result =mysql_query($query);
       while($row= mysql_fetch_object($main_result))
        {
           $st=$row->ST;
           $ot=$row->OT;
        }
//          echo $st."::".$ot."\n<br>";
    if($st > 0 || $ot > 0)
    {
        return true;
    }
    else
    {
        return false;
    }

}
function get_total_emp($pid,$did,$aid,$eid,$where)
{
    $st=0;
    $ot=0;
    $query="SELECT sum(duration)/3600 AS ST,sum(duration_ot)/3600 AS OT, project_id FROM
        ohrm_timesheet_item WHERE project_id ='".$pid."'  AND activity_id IN(select activity_id from ohrm_project_activity where project_id='".$pid."' and diu_id > 0 and diu_id='".$did."') AND activity_id='".$aid."' AND employee_id='".$eid."'".$where;

    $main_result =mysql_query($query);
       while($row= mysql_fetch_object($main_result))
        {
           $st=$row->ST;
           $ot=$row->OT;
        }

    if($st > 0 || $ot > 0)
    {
        return true;
    }
    else
    {
        return false;
    }

}
?>