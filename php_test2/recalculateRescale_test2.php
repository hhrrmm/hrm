<?php

/*
** Used to renew the values in hr_rescale table
*/

$link = mysql_connect('192.168.44.200', 'cmq', 'cmq1');
mysql_select_db('ConsultingMQ');

$duration = 60; //24;
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

$from = 360; $to = $last_timeID;
$msg ="";

//calculate new rescale value
$rescale = 1.00217395060107;//0.998614802334848; //0.990729507991746;

$msg = "";
$up_query = "UPDATE ConsultingMQ.hr_rescale_test2 SET RescaleValue=".$rescale." WHERE ID=1";
$up_result = mysql_query($up_query);

if ($up_query) {$msg="Success";}
else {
	echo "DB_Error"; exit;
}

//echo $msg;
echo  "Success";
//echo "rescale =".$rescale;
//echo "hist = ".$sum1."   fore = ".$sum2."| outasF = ".$outasF." outasH = ".$outasH;


?>