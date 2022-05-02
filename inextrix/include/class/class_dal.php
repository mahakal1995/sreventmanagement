<?
/**
  * class_dal.php : PHP MYSQL Functions. 
  *
  * This file contains functionality to access MYSQL.
  *
  * $Id: class_dal.php,v 1.2 2008/01/05 14:30:46
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.2
  * @package Data_Access_Layer
  */


/**
  * dal : It is Data Access Layer, which does database operations for PHP.
  * It provides database connectivity and
  * by executing query it does following operations :-
		*	- Insert
		*	- Update
		*	- Delete
		*	- Select
		*	- Get total number of fields
		*	- Get total number of rows
		*	- Number of  affected rows
		*	- Name of field(column name)
		*	- Number of queries executed
		*	- Last inserted ID
		*	- Errors generated while doing database operations
		*	- Records of the tables
  *
  * <ul><li><b>
  * Created Date :</b> 2007-12-17</li>
  * 
  * <li><b>Last Modified Date :</b> 2008-01-05</li></ul>
  *
  */
class dal
{
	/**
	* @var string
	* The hostname of the database server.
	* It can be domain name or IP address.
	*
	* example :- localhost
	*
	* example :- www.test.com
	*
	* example :- 192.168.1.11
	*/
	var $dbhost;

	/**
	* @var string
	* The username which is used to connect to the database server.
	* 
	* example :- root 
	*/	 
	var $dbuser;

	/**
	* @var string
	* Password for the username of MYSQL.
	*/
	var $dbpass;

	/**
	* @var string
	* Name of database to be used.
	*/ 
	var $dbase;	

	/**
	* @var string
	*  The query to be executed.
	*/ 
	var $sql_query;	

	/**
	* Local connection variable.
	*/
	var $mysql_link;

	/**
	* @var array
	* Local variable which contains the result of executed query.
	*/ 
	var $sql_result;	

	/**
	* @var integer
	* Total number of queries executed.
	*/ 
	var $query_count;

	/**
	* @var string
	* The name of the specified field in a result.
	*/	
	var $fieldname;

	/**
	* @var integer
	* Numeric field offset.
	*/
	var $k;		
	
	/**constructor 
	*
	* This constructor sets the default value of the class properties 
	* It opens the connection using given parameters.
	* If no parameters are given, then it uses the constants named DEF_SERVER, DEF_USER, DEF_DB, DEF_PASS as default value, declared in 'config.php' or some other file, which should be included before including this class file.
   	* It also selects the database by calling connection().
	*
	* @param string $server_name Hostname of database server.
	* @param string $username Username used to connect to the database server.
	* @param string $database_name Name of database to be used.
	* @param string $database_password Password for the username of MYSQL.
	* @see class_dal.php
	*/
	function dal($server_name=DEF_SERVER, $username=DEF_USER, $database_name=DEF_DB, $database_password=DEF_PASS)
	{
	
		$this->dbhost = $server_name;
	
		$this->dbuser = $username;
	
		$this->dbpass = $database_password;
	
		$this->dbase = $database_name; 
	
		$this->mysql_link = '0';
	
		$this->query_count = '0';
	
		$this->sql_result = '';
	
		$this->connection();
	
	}
		
	/**
	* connection : It opens the connection with MYSQL server and sets active MYSQL database.
	*/
	function connection()
	{
	
		$this->mysql_link = @mysql_connect( $this->dbhost, $this->dbuser, $this->dbpass ) or $this->error( mysql_error( $this->mysql_link ), $this->sql_query, mysql_errno( $this->mysql_link ) );
	
		@mysql_select_db( $this->dbase ) or $this->error( mysql_error( $this->mysql_link ), $this->sql_query, mysql_errno( $this->mysql_link ) );
	
	}
	
	/**
	* error : If any error is generated while doing database operation this function is called automatically.
	* 
	* Here we are generating user level error(PHP error) by trigger_error function, which should be catched by php error handler.
	* 
	* error message merged with MYSQL error description and PHP trace, passed as parameter of trigger_error function.
	* 
	*
	* @param string $error_msg MYSQL error message while attempt any sql operation.
	* @param string $sql_query The sql query which contains error.
	* @param string $error_no MYSQL error number.
	* 
	*/
	function error( $error_msg, $sql_query, $error_no )
	{
		
		$error_msg = $error_msg."QUERY : ".$sql_query;
		$error_msg .= '~';
		$trace=debug_backtrace();
		for($i=0;$i<sizeof($trace);$i++)
		{
			$error_msg .= '#'.$trace[$i]['class']. "=>".$trace[$i]['function'];
		}
		trigger_error($error_msg, E_USER_ERROR);		
	}
	
	/**
	* close : close MYSQL connection.
	*/
	function close()
	{
	
		mysql_close( $this->mysql_link );
	
	}


        /**
	* sql_query : This function executes a MYSQL query and if any error comes while executing query it calls error().
	* @param string $sql_query sql query to be executed.
	* @return string If sql query executes successfully then it returns TRUE otherwise it returns error.
	*/
	function sql_query( $sql_query )
	{
	
		$this->sql_query = $sql_query;
	
		$this->sql_result = @mysql_query( $sql_query, $this->mysql_link );
	
		if (!$this->sql_result)
	
		{
	
		$flag = $this->error( mysql_error( $this->mysql_link ), $this->sql_query, mysql_errno( $this->mysql_link ) );
			
		}
	
		else
		{
		$count = $this->query_count;
	
		$count = ($count + 1);
	
		$this->query_count = $count;
		$flag = TRUE;
		}
		return $flag;
	}

	/**
	* num_rows : It gets the total number of rows in result of last query execution.
	* @return mix If any error is generated while getting number of rows it returns error otherwise it returns total number of rows.
	*/
	function num_rows()
	{
	
		$mysql_rows = mysql_num_rows( $this->sql_result );

		if (!isset($mysql_rows))
	
		{
	
		$flag = $this->error( mysql_error( $this->mysql_link ), $this->sql_query, mysql_errno( $this->mysql_link ) );
		return $flag;
		}
		else
		{
			return $mysql_rows;
		}
	}

	/**
	* num_fields : It gets the total number of fields in result of last query execution.
	* @return mix If any error is generated while getting number of fields it returns error otherwise it returns total number of fields.
	*/
	function num_fields()
	{
	
		$mysql_fields = mysql_num_fields( $this->sql_result );
	
		if (!isset($mysql_fields))
	
		{
	
		$flag = $this->error( mysql_error( $this->mysql_link ), $this->sql_query, mysql_errno( $this->mysql_link ) );
	
		}
		else
		{
			return $mysql_fields;
		}	
	}
	

	/**
	* fieldvalue : Get the name of the field at offset given as parameter, in result of last executed query.
	* @param array $k It is numerical field offset. The offset starts at 0.
	* @return string If any error is generated while getting name of specified field it returns error otherwise it returns name of specified field.
	*/
	function fieldvalue($k)
	{
	
	
		$fieldname = mysql_field_name($this->sql_result, $k);
		
		if (!$fieldname)
	
		{
	
		$flag = $this->error( mysql_error( $this->mysql_link ), $this->sql_query, mysql_errno( $this->mysql_link ) );
		return $flag;
		}
		else
		{
			return $fieldname;
		}
	}
	
	/**
	* affected_rows : It can be used to find out number of affected rows after executing any query.
	*@return integer It returns number of affected rows.
	*/
	function affected_rows()
	{
	
		$mysql_affected_rows = mysql_affected_rows( $this->mysql_link );
	
		return $mysql_affected_rows;
	
	}
	
	/**
	* fetch_array :  Fetch a result row as an associative array.
	* @return mix If any error is generated while fetching result of query it returns FALSE otherwise it returns result(records) as associative array.
	*/
	function fetch_array()
	{
	
		if ( $this->num_rows() > 0 )
	
		{
	
		$mysql_array = mysql_fetch_assoc( $this->sql_result );
	
		if (!is_array( $mysql_array ))
		{
			return FALSE;
		}
		return $mysql_array;
		} 
	}


	/**
	* fetch_rows : Get a result row as an enumerated array.
	* @return mix If any error is generated while fetching result of query it returns FALSE otherwise it returns result(records) as enumerated array.
	*/
	function fetch_rows()
	{
		if ( $this->num_rows() > 0 )
	
		{
	
		$mysql_array = mysql_fetch_row( $this->sql_result );
	
		if (!is_array( $mysql_array ))
		{
			return FALSE;
		}
		return $mysql_array;
		}
	}

	
	/**
	* query_count : It gives the number of executed queries from connection opens until it closes.
	* @return integer It returns number of queries executed.
	*/
	function query_count()
	{
	
		return $this->query_count;
	
	}

	/**
	* lastinserted_id : Get the ID generated for an AUTO_INCREMENT column by the previous INSERT query.
	* @return integer Last inserted ID of AUTO_INCREMENT column.
	*/
	function lastinserted_id()
	{
	
		$id = mysql_insert_id();
		return $id;
	
	}

	/**
	* exec_query : MYSQL specific function that executes the query and returns a two-dimensional array if query is 'select ...', or a boolean value if the query is 'insert...','update...','delete...'.
	* If any error generated while executing query it calls show_error() function.
	* @param string $query MYSQL query to be executed.
	* @return array Two dimensional array it returns result as two dimensional array.
   	*/
	function exec_query($query)
	{
	$result = mysql_query($query, $this->mysql_link);
	if(!$result)
	{
		$flag = $this->error( mysql_error( $this->mysql_link ), $query, mysql_errno( $this->mysql_link ) );
		return $flag;
	}
	if ( is_resource($result) )
	{
		//the query was a 'select' and has returned some records,
		//convert the results to a two dimensional table
		$table = $this->result2table($result);
		return $table;
	}
	else 
	{
		//the query was a command (insert, delete, update, etc.)
		//and it has returned TRUE or FALSE indicating success or failure
		return $result;
	}
	}
	
	/**
	* result2table : Convert the mysql $result to a two-dimensional table.
	* @param output_parameter &$result  
	* @return array Two-dimensional array of result.
	*/
	function result2table(&$result)
	{
	$arr_result = array();
	$i = 0;
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) )
	{
		while ( list($fld_name, $fld_value) = each($row) )
		{
		if (!isset($fld_value))  $fld_value = NULL_VALUE;
		$arr_result[$i][$fld_name] = $fld_value;
		}
		$i++; //next row
	}
	mysql_free_result($result);
	
	return $arr_result;
	}

}
?>