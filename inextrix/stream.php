<?
include("include/config/config.php");
//print_r($_GET);exit;
$file=$_GET['param'];

//$document_path="/root/hrm_docs/";
$file = $document_path."/".$file;
$filename = $file;

  if (!is_file($filename)) { die("<b>404 File not found!</b>"); }

   $len = filesize($file);

   
        header('Content-Type: application/pdf');
 	header('Content-Description: File Transfer');
 	header('Content-Disposition: attachment;filename="' .basename($file) . '"');
 	header('Content-Length: ' . filesize($file));
 	@readfile($file) OR die("could not read");
?>