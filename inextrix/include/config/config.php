<?php
/**
  * config.php : Variables to be used as configuration variable are declared here. 
  *
  * $Id: config.php,v 1.0 2008/01/02 11:24:24
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * Followings are the variables declared in the file.
  *
  * Application Database Configuration
  * <ul>
  * 	<li> DB_server - Application DB Server Name.
  * 	<li> DB_username - Application DB User Name for MYSQL Authentication.
  * 	<li> DB_database - Application DB Name
  * 	<li> DB_password - Application DB Password for MYSQL Authentication.
  * </ul>
  * Redirection Link and Caption variables
  * <ul>
  * 	<li> rdrctn_lnk - Redirection Link to be given in error.php page, after an error has been encountered.
  * 	<li> rdrctn_cptn - Caption of the redirection Link to be given in error.php page, after an error has been encountered.
  * </ul>
  * Error Handling Related variables
  * <ul>
  * 	<li> php_err_flag - Flag that will decide whether to destroy the session on occurance of an error or not.
  * </ul>
  *
  * @version 1.0
  * @package Generic
  */
?>
<?php
define('ROOT_PATH', dirname(__FILE__));

/**Database Configuration.**/
define('DEF_SERVER',"localhost");
define('DEF_USER',fdbuser");
define('DEF_PASS',"FiV35pas5");
define('DEF_DB',"hrm_fcs");

/**-----------------------------------------------------------------------------------**/
/**PATH variables**/
/**-----------------------------------------------------------------------------------**/
/**
  * Main HTML folder path for Application.
  */
define("APP_HTML_PATH", "html/");
/**
  * Main Include folder path for Application.
  */
define("APP_INCLUDE_PATH", "include/");
/**
  * Main Image folder path for Application.
  */
define("APP_IMAGE_PATH", "html/images/");
/**-----------------------------------------------------------------------------------**/

/**--------------------------------------------------------------------------------------
$document_path = "/inextrix/ihrm/hrm_docs";
/**-----------------------------------------------------------------------------------**/

/**-----------------------------------------------------------------------------------**/
/**Redirection Link and Caption variables**/
/**-----------------------------------------------------------------------------------**/
$rdrctn_lnk="";
$rdrctn_cptn="";
/**-----------------------------------------------------------------------------------**/

/**-----------------------------------------------------------------------------------**/
/**Error Handling Related variables**/
/**-----------------------------------------------------------------------------------**/
$php_err_flag=false;
/**-----------------------------------------------------------------------------------**/

//Define document path 
$document_path = '../documents/';

?>
