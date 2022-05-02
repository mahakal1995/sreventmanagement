<?php

define('ROOT_PATH', dirname(__FILE__));
    require_once ROOT_PATH . '/lib/confs/Conf.php';
    $config = new Conf();
        $db_host	= $config->dbhost;
        $db_user        = $config->dbuser;
        $db_pwd         = $config->dbpass;
        $db_name        = $config->dbname;
        
        mysql_connect($db_host, $db_user, $db_pwd) or die(mysql_error());
        mysql_select_db($db_name) or die(mysql_error());
        

	$q=$_REQUEST['q'];
	$my_data=$q;
	//Changed By :: Rushika
	//Changes :: interchange t1.name and t2.name in concat function
	$sql="SELECT t1.project_id,t1.customer_id,concat(t1.name,' - ',t2.name) as name,t1.is_deleted,t2.customer_id FROM ohrm_project t1,ohrm_customer t2 where t1.customer_id=t2.customer_id and t1.is_deleted=0 and (t1.name LIKE '%$my_data%' OR t2.name LIKE '%$my_data%')";
//	$sql="SELECT name,project_id FROM ohrm_project WHERE is_deleted=0 and name LIKE '%$my_data%' ORDER BY name";
	$result = mysql_query($sql) or die(mysql_error());
	
	if($result)
	{
		while($row=mysql_fetch_object($result))
		{
//                    $arr[]=array("name"=>$row->name,"id"=>$row->project_id);
			echo $row->name."\n";
		}
//                echo json_encode($arr);
	}
?>









