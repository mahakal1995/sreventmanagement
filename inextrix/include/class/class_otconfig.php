<?

if(!isset($_SESSION['user'])){
//    redirect('./login.php');
    header( 'Location: '.$_SESSION['WPATH'].'/login.php' );

}
/**
  * class_empStartDatepopup.php : This Class file contains function that fetches employee details from DB
  *
  * $Id: class_empStartDatepopup.php,v 1.0 2008/01/09 13:40:17
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Timesheets_Selection
  */

/**
 * Includes the Configuration file.
 */
include("include/config/config.php");
/**
 * Includes DAL class for DB Connection
 */
include(APP_INCLUDE_PATH."class/class_dal.php");

/**
  * This class performs DB Transaction operations for getting employee id and name.
  *    - It uses dal class to connect to the DB and perform further query operations. 
  * 
  * Created Date :</b> 2008-01-02</li>
  * 
  * <li><b>Last Modified Date :</b> 2008-01-02</li></ul>
  * @package Timesheets_Selection
  * @see dal
  */
class otconfig
{	
	/**
	* @var string
	* DB Connection variable.
	*/
	var $sql;
	var $search_by;
	var $search_val;

	//**--Timesheet selection for non admin users--**//
	// Change: New variable has been added at
	// Purpose: Stores Employee Number of non admin user
	// Date: 8-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
	/**
	* @var string
	* Employee Number of current employee if he is not an admin
	*/
	var $emp_no;
	//**---------------------------------------------------------------------------------------------------------------**//

	/**constructor 
	*
	* This constructor Opens a connection with DB and sets DB connection variable value.
   	* Constructor of DAL class creates the connection with DB.
	* @see dal::dal()
	*
	*/
	function otconfig()
	{
		$this->sql = new dal();
	}

	/**
	* employee : The function fetches employee id and name from DB.
	*
	* @return result An associative, two dimensional array filled with data retrieved from DB. With an associative array you can access the field by using their name instead of using the index. 
	*/
	
        
      function insert_ot_config()
	{
          
//          echo"<pre>";print_r($_POST);echo"</pre>";
              $rec_get="";
          if($_POST['start_date']!="" && $_POST['end_date']!="" && $_POST['multiply']!="")
             {

                    $fromDate = $_POST['start_date'];
                    $toDate = $_POST['end_date'];

                    $dateMonthYearArr = array();
                    $fromDateTS = strtotime($fromDate);
                    $toDateTS = strtotime($toDate);
                    for ($currentDateTS = $fromDateTS; $currentDateTS <= $toDateTS; $currentDateTS += (60 * 60 * 24)) {
                    // use date() and $currentDateTS to format the dates in between
                    $currentDateStr = date('Y-m-d',$currentDateTS);
                    $dateMonthYearArr[] = $currentDateStr;
                    //print $currentDateStr.”<br />”;
                    }
                                                            
                    foreach ($dateMonthYearArr as $key => $value) {
                    
                        $date_query = "select start_date,end_date from ohrm_ot_config where '".$value."' between start_date and end_date  OR 
                                '".$value."' between end_date and start_date";              
                        $rec_date = $this->sql->exec_query($date_query);
                    
                        if (array_key_exists('0', $rec_date)) 
                        {
                        $rec_get="0";

                        }
                        $test_demo[]=$rec_get;

                        }

                        if($rec_get=="")
                        {                        
//                            echo"<br><br><br><br>in if for insert";
//                            exit;
                            $start_date=$_POST['start_date'];
                            $end_date=$_POST['end_date'];
                            $multiply=$_POST['multiply'];                  
                            $query = "insert into ohrm_ot_config(start_date,end_date,multiply) values ('$start_date','$end_date','$multiply')"; 
                            echo $query;
                            $rec = $this->sql->exec_query($query);                          

                        }
                        else
                        {
                            $rec['error']="This date range already taken.....";
//                            exit;

                        }
                      return $rec;
             }
             
        }
        
        
       function list_ot_config()
	{
             
             $query = "select * from ohrm_ot_config";              
             $rec = $this->sql->exec_query($query);             
	     return $rec;
        }
        
        
            
       function delete_ot_config()
	{

             $query = "delete from ohrm_ot_config where id=".$_GET['del'];              
             $rec = $this->sql->exec_query($query);
	     return $rec;
           
        }
        
        
       function edit_ot_config()
	{          
            $query = "select * from ohrm_ot_config where id=".$_GET['edit'];                          
            $rec = $this->sql->exec_query($query);                           
            
            if (isset($_POST['submit'])) 
            {                               
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $multiply = $_POST['multiply'];

                $upd = "update ohrm_ot_config set start_date='$start_date',end_date='$end_date',multiply='$multiply' where id = " . $_REQUEST['edit'];
                $rec = $this->sql->exec_query($upd);                                               
            }
	     return $rec;
           
        }
        
        
	
	//**--Employee selecion based on employee status--**//
	// Change: New function has been added
	// Purpose: Fetches employee status values from DB
	// Date: 8-Jan-2008
	//**---------------------------------------------------------------------------------------------------------------**//
	
	//**---------------------------------------------------------------------------------------------------------------**//
} 
