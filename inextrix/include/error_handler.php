<?
/**
  * error_handler.php : This file contains code to be executed when a PHP error occurs. 
  *
  *   - A function is created to handle PHP errors. This function is set as default error handler of PHP.
  *
  * $Id: error_handler.php,v 1.0 2008/01/02 11:24:24
  *
  * This file should work with PHP 4.x versions and 5.x versions.
  *
  * @version 1.0
  * @package Error_Handling
  */

?>
   <?php
    /**
      * userErrorHandler : This function catches error details as its parameters and handles processing after error has occured.
      *     - The errors are logged in error.log file that is particular to the application.
      *     - Error details are stored in session and page is redirected to error.php file to give error details to user.
      *
      * @param integer $errno Error Number(code) of PHP Error
      * @param string  $errmsg Error Description of PHP Error
      * @param string $filename Name and location of file where PHP error has occured.
      * @param integer $linenum Line Number in the file where exactly the PHP error has occured.
      * @param string $vars Error Variables for User errors.
      */
    function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
    {
        // timestamp for the error entry
        $dt = date('Y-m-d H:i:s (T)');
       // define an assoc array of error string
       // in reality the only entries we should
       // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
       // E_USER_WARNING and E_USER_NOTICE
       $errortype = array (
                   E_ERROR => 'Error',
                   E_WARNING => 'Warning',
                   E_PARSE => 'Parsing Error',
                   E_NOTICE => 'Notice',
                   E_CORE_ERROR => 'Core Error',
                   E_CORE_WARNING => 'Core Warning',
                   E_COMPILE_ERROR => 'Compile Error',
                   E_COMPILE_WARNING => 'Compile Warning',
                   E_USER_ERROR => 'User Error',
                   E_USER_WARNING => 'User Warning',
                  E_USER_NOTICE => 'User Notice',
                  E_STRICT => 'Runtime Notice'
                  );
	
       // set of errors for which a var trace will be saved.
       $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
       $err = "<errorentry>\n";  
       $err .= "\t<datetime>" .$dt. "</datetime>\n";
       $err .= "\t<errornum>" .$errno. "</errornum>\n";
       $err .= "\t<errortype>" .$errortype[$errno]. "</errortype>\n";
       $err .= "\t<errormsg>" .$errmsg. "</errormsg>\n";
       $err .= "\t<scriptname>" .$filename. "</scriptname>\n";
       $err .= "\t<scriptlinenum>" .$linenum. "</scriptlinenum\n";
       if (in_array($errno, $user_errors)) {
           $err .="\t<vartrace>".wddx_serialize_value($vars,'Variables')."</vartrace>\n";
      }
       $err .= "</errorentry>\n\n";
       // save to the error log file, and e-mail me if there is a critical user error.
       error_log($err, 3, 'error.log');
       if ($errno == E_USER_ERROR) {
           mail('bgates@gmail.com', 'Critical User Error', $err);  
       }
	//For critical errors, error details are stored in session variables and flow is redirected to error.php to display errors to user.
	if($errno!=8&&$errno!=2048)
	{

		$filename="File - ".$filename;
		$tmp_err_msg=explode('~',$errmsg);
		$errmsg=$tmp_err_msg[0];
		if(count($tmp_err_msg)>1)
		{
			$filename="Function Trace - ";
			$tmp_fun_arr=explode('#',$tmp_err_msg[1]);
			for($i=1;$i<count($tmp_fun_arr);$i++)
			{
				$filename=$filename.$tmp_fun_arr[$i]."() -> ";
			}
			$linenum="N/A";
		}
		
		$_SESSION['err_time']=$dt;
		$_SESSION['err']=$errortype[$errno];
		$_SESSION['err_msg']=$errmsg;
		$_SESSION['err_file']=$filename;
		$_SESSION['err_line']=$linenum;
		
		header('Location:error.php');exit;
	}
   }
   //Function is set as default error handler for this application.
   set_error_handler('userErrorHandler');


  ?>