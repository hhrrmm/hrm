<?php

/*
** Used to upload new historical data (stored in an Excel file) into the database
*/

include 'renewBaseline_test2.php';
include 'fileParser_test2.php';

$duration = 60; //24;

error_reporting(E_ALL);
ini_set("display_errors", 1);

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

$a = 0;

if (/*$extension == "xls" ||*/ $extension == "xlsx" /*|| $extension == "csv"*/) {
	if ($file_size > 21000000) {$message = "ErrorSize";}
	else {
		$moved  =  move_uploaded_file($temp_name, $file_path);
		
		if ($moved) {
			$message = parseDataFile($file_path, 0, 0);
		}
		else {$message = "ErrorOther";}
		
	}//else (filesize)
} //if extension
else {$message = "ErrorFiletype";}

if($message == "Success") {
//if upload was succesful, get the latest historical period and adjust database tables ~~~~~~~~~~~~
	$message = renew_baseline();

	//get the latest hist. monthly period
	$query  = "SELECT MAX(ConsultingMQ.hr_historic_test2.TimeID) AS maxTime "
			. " FROM ConsultingMQ.hr_historic_test2, ConsultingMQ.hr_timeID_test2 "
			. " WHERE ConsultingMQ.hr_historic_test2.TimeID=ConsultingMQ.hr_timeID_test2.TimeID "
			. " AND PeriodType=1 "
			//. " AND IndicatorID = 13" // if only on HR units
			. " AND DataValue IS NOT NULL";
	$last_period_month = 1;
								
	$qry_result = mysql_query($query);
								
	if ($qry_result) {
		while ($row = mysql_fetch_array($qry_result, MYSQL_ASSOC)) {
			$last_period_month = $row['maxTime'];
		}
	}
	else {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	//get the last forecast timeID
	$qry2 = "SELECT TimeID FROM ConsultingMQ.hr_lastHistoric_period_test2 WHERE PeriodType=1";
	$qry2_result = mysql_query($qry2);								
	if ($qry2_result) {	
		$line = mysql_fetch_array($qry2_result, MYSQL_ASSOC);
		$last_timeID = $line['TimeID'] + $duration;		
	} else {
		echo "DB_Error in LastHistoric";
		exit;
	};	
	
	//clean up historic table monthly data
	$clean1 = "DELETE FROM ConsultingMQ.hr_historic_test2 "
			. " WHERE IndicatorID>0 "
			. " AND TimeID > ".$last_period_month
			. " AND TimeID <= 444";//.$last_timeID; //here i should clean all monthly data; id < 1000
			
	$cl1_result = mysql_query($clean1);
	
	if (!$cl1_result) {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	// upd. the last hist. period table for monthly data
	$qry2 = "SELECT TimeID FROM ConsultingMQ.hr_lastHistoric_period_test2 WHERE PeriodType=1";
								
	$qry2_result = mysql_query($qry2);
								
	if ($qry2_result) {
		while ($line = mysql_fetch_array($qry2_result, MYSQL_ASSOC)) {
			if($last_period_month != $line['TimeID']){
				$upd = "UPDATE ConsultingMQ.hr_lastHistoric_period_test2 SET TimeID=".$last_period_month
					 . " WHERE TimeID=".$line['TimeID']." AND PeriodType=1";
						
				mysql_query($upd);
				if (!$qry2_result) {
					echo "ErrorFinalize";
					if (file_exists($file_path)){unlink($file_path);}
					exit;
				}
			}
		}//while
	}
	else {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	//get the last hist. annual period
	$query3 = "SELECT TimeName FROM ConsultingMQ.hr_timeID_test2 WHERE TimeID=".$last_period_month;
	//$last_period_quarter = 1;
	$last_period_year = 1;
	$last_month_name = "";
	
	$qry3_result = mysql_query($query3);
	
	if ($qry3_result) {
		while ($row = mysql_fetch_array($qry3_result, MYSQL_ASSOC)) {
			$last_month_name = $row['TimeName'];
		}
		
		if (substr($last_month_name, 4, strlen($last_month_name)-4) == "M12") {
			$last_period_year = substr($last_month_name, 0, 4);
		}
		else {
			$last_period_year = substr($last_month_name, 0, 4) - 1;
		}
	}
	else {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	$query4 = "SELECT TimeID FROM ConsultingMQ.hr_timeID_test2 WHERE TimeName=".$last_period_year;
	
	$qry4_result = mysql_query($query4);
	
	if ($qry4_result) {
		while ($row = mysql_fetch_array($qry4_result, MYSQL_ASSOC)) {
			$lp = $row['TimeID'];
		}
		
		$last_period_year = $lp;
	}
	else {
		//echo "ErrorFinalize";
		echo $qry4;
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	//clean up historic table annual data 
	$minAnnual = 1;
	$get_minAnnual = "SELECT MIN(TimeID) AS MinAnnual FROM ConsultingMQ.hr_timeID_test2 WHERE PeriodType=3";
	$get_result = mysql_query($get_minAnnual);
	
	if ($get_result) {
		while($l =  mysql_fetch_array($get_result, MYSQL_ASSOC)) {
			$minAnnual = $l['MinAnnual'];
		}
		
		$qry5 =  "DELETE FROM ConsultingMQ.hr_historic_test2 "
				." WHERE IndicatorID>0"
				. " AND TimeID>".$last_period_year;
	
		$qry5_result = mysql_query($qry5);
	
		if (!$qry5_result) {
			echo "ErrorFinalize";
			if (file_exists($file_path)){unlink($file_path);}
			exit;
		}
	}
	else {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	// upd. the last hist. period table for annual data
	$qry5 = "SELECT TimeID FROM ConsultingMQ.hr_lastHistoric_period_test2 WHERE PeriodType=3";
								
	$qry5_result = mysql_query($qry5);
								
	if ($qry5_result) {
		while ($line = mysql_fetch_array($qry5_result, MYSQL_ASSOC)) {
			if($last_period_year != $line['TimeID']){
				$upd = "UPDATE ConsultingMQ.hr_lastHistoric_period_test2 SET TimeID=".$last_period_year
					 . " WHERE TimeID=".$line['TimeID']." AND PeriodType=3";
						
				$up_result = mysql_query($upd);
				if (!$up_result) {
					echo "ErrorFinalize";
					if (file_exists($file_path)){unlink($file_path);}
					exit;
				}
			}
		}//while
	}
	else {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}
	
	//find latest historic data period without any nulls in the inputs
	$done = false;
//??????
	$periodNeeded = $last_period_month;
	while (!$done) {
		$null_counter   = "SELECT 15-COUNT(ConsultingMQ.hr_historic_test2.DataValue) AS NullNum "
						. " FROM ConsultingMQ.hr_historic_test2 "
						. " WHERE TimeID=".$periodNeeded
						. " GROUP BY TimeID";
						
		$null_result = mysql_query($null_counter);
		
		if ($null_result) {
			while($row = mysql_fetch_array($null_result, MYSQL_ASSOC)) {
				if ($row['NullNum'] > 2) {
					$periodNeeded -= 1;
				}
				else {
					$done = true;
				}
			}
		}
	}
	
	//update indicator historyEnd attribute
	$qry8   = "SELECT Max(TimeID) AS TimeID, IndicatorID FROM ConsultingMQ.hr_historic_test2 "
			. "WHERE DataValue IS NOT NULL GROUP BY IndicatorID";
			
	$qry8_result = mysql_query($qry8);
	
	if ($qry8_result) {
		while ($row = mysql_fetch_array($qry8_result, MYSQL_ASSOC)) {
			$p = $row['TimeID'];
			$ind = $row['IndicatorID'];
			
			$upd_q = "UPDATE ConsultingMQ.hr_indicator_test2 SET historyEnd=".$p." WHERE IndicatorID=".$ind;
			
			$u_r = mysql_query($upd_q);
			if (!$u_r) {
				echo "ErrorFinalize";
				if (file_exists($file_path)){unlink($file_path);}
				exit;
			}
		}
	}
	else {
		echo "ErrorFinalize";
		if (file_exists($file_path)){unlink($file_path);}
		exit;
	}

}

if (file_exists($file_path)){unlink($file_path);}

echo $message;
?>