<?php
include "act.php";
include "tcx.php";


if(isset($_POST['action']) and $_POST['action'] == 'upload')
{
    if(isset($_FILES['user_file']))
    {
        $file = $_FILES['user_file'];

	$url = $_FILES["user_file"]["tmp_name"]; 
    	$file_act_name = $_FILES["user_file"]["name"];	
 }
}





$act=simplexml_load_file($url);


$XmlAct = new act2tcx($act);
$XmlTcx = new tcx ( $XmlAct );
$PrintTcx = $XmlTcx->GetTcx();

$dom = dom_import_simplexml($PrintTcx)->ownerDocument;
$dom->formatOutput = true;

$file_act_name = preg_replace ("/.act/", ".tcx", $_FILES["user_file"]["name"] );
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$file_act_name );

echo $dom->saveXML();


?>
