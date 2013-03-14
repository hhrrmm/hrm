<?php

/*
** Used to upload new scenario data (stored in an Excel file) into the database
*/

include 'fileParser_test2.php';

error_reporting(E_ALL);
ini_set("display_errors", 1);

$SID = $_GET['sessionID'];

$upload_dir = $_SERVER['DOCUMENT_ROOT'] .  '/Consulting/tmp/';
$upload_url = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . '/';

$temp_name = $_FILES['Filedata']['tmp_name'];
$file_name = $_FILES['Filedata']['name']; 
$file_size = $_FILES['Filedata']['size'];

$extension = end(explode(".", $file_name));

//generate new name for file upload to avoid collisions
$new_name = "";
$new_name .= time().rand(100,999).".".$extension;

$file_path = $upload_dir.$new_name;

$message = "";

if (/*$extension == "xls" ||*/ $extension == "xlsx" /*|| $extension == "csv"*/) {
	
	if ($file_size > 21000000) {
		$message = "ErrorSize";
	}
	else {
		$moved  =  move_uploaded_file($temp_name, $file_path);

		if ($moved) {
			$message = parseDataFile($file_path, 1, $SID);
		} //if moved
		else {$message = "ErrorOther";}

	}//else (filesize)

} //if filetype
else {$message = "ErrorFiletype";}

if (file_exists($file_path)){unlink($file_path);}

echo $message;

?> 