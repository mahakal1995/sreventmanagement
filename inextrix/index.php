<?
/**
  * index.php : Opens file accroding to action set in function pop_up() from selectEmployee.php file . 
  *
  * From this file our architecture starts 
  *
  * $Id: index.php,v 1.0 2008/01/09 13:14:35
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Parse_Flow
  */

session_start();
error_reporting(0);
error_reporting(0);
/**
 * Includes configuration file.
 */
include('include/config/config.php');
/**
 * Includes PHP Error Handling file.
 */
include(APP_INCLUDE_PATH.'error_handler.php');
/**
 * Includes Process functions file.
 */
include(APP_INCLUDE_PATH.'process.php');
	
	if(isset($_REQUEST['action']))
	{
		if (function_exists($_REQUEST['action']))
		{
			$_REQUEST['action']();
		}
		//First page after login
		
		else
		{
				/**
 				  * Includes Welcome file When no function matches action value.
 				  */
				include(APP_HTML_PATH.'welcome.html');
		}
	}
	//First page after login
	else
	{
		/**
 		  * Includes Welcome file When no function matches action value.
 		  */
		include(APP_HTML_PATH.'welcome.html');
	}
	

//----------------------------------------
?>

