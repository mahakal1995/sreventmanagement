<?
/**
  * error.php : Error Page 
  *
  *   - On occurrance of an error, application flow is breaked down and is redirected to this file.
  *   - User can view details of the error occured so that appropriate actions can be taken.
  *   - Depending on the configuration decided for the application, running session of the application will be destroyed here on occurrance of an error.
  *
  * $Id: error.php,v 1.0 2008/01/02 11:24:24
  *
  * This file should work with PHP 4.x versions and 5.x versions and HTML 1.0 versions.
  *
  * @version 1.0
  * @package Error_Handling
  */

session_start();
/**
 * Includes configuration file.
 */
include("include/config/config.php");
?>
<html>
	<head>
    		<title></title>
    		<meta content="">
    		<style></style>
	</head>
<!--If values for error details are not posted, the page is submitted on body load so that Posted values are got.-->
<body <?if(!(isset($_POST['err']))){?> onload="document.err_frm.submit();" <?}?>>
	<h1 align="center">ERROR</h1>
	<!--Form for error detail elements, where session variable values are stored and whose values are posted when session is destroyed-->
	
	<form name="err_frm" method="post">
		<input name="err_msg" type="hidden" value="<?=$_SESSION['err_msg']?>">
		<input name="err" type="hidden" value="<?=$_SESSION['err'];?>">
		<input name="err_time" type="hidden" value="<?=$_SESSION['err_time'];?>">
		<input name="err_file" type="hidden" value="<?=$_SESSION['err_file']?>">
		<input name="err_line" type="hidden" value="<?=$_SESSION['err_line']?>">
	</form>
	<?if(isset($_POST['err'])){
	//Explode the error message to separate it from query if any.
	$tmp_err_msg=explode('QUERY',$_POST['err_msg']);?>
	<?=$_POST['err'];?> - <?=$tmp_err_msg[0]?>.
	<!--If Query has been found in error message, it is displayed separately here.-->
	<?if(count($tmp_err_msg)>1){?>
	<br/>
	QUERY<?=$tmp_err_msg[1]?>.<?}?>
	<br/>
	Time: <?=$_POST['err_time'];?>.<br/>
	Location: <?=$_POST['err_file']?>, Line No. - <?=$_POST['err_line']?>.
	<?}?>
	<br/>
	<a href="<?=$rdrctn_lnk?>"><?=$rdrctn_cptn?></a>
	<?
		//IF flag for session destroy is set, then only session is destroyed.
		if($php_err_flag==true)
		{
			session_destroy();
		}
	?>
</body>
</html>