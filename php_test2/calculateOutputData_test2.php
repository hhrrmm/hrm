<?php

/*
** Used to calculate model outputs
*/

include 'ForecastCalculator_test2.php';
include 'XMLgeneration.php';
include 'MySQLConnectionDataProvider.php';
include 'dbFunctions_test2.php';

$adjust_values = true;
$duration = 60; //24;

$SID = $_GET['sessionID']; $scenID = $_GET['scenarioID'];

$credentials = new MySQLConnectionDataProvider();

$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
mysql_select_db($credentials->DB);

$cleanup_query  = "DELETE FROM ConsultingMQ.hr_scenario_test2 WHERE sessionID='"
		.$SID."' AND TimeID>0 AND IndicatorID>0";

mysql_query($cleanup_query);

//if the input data is not in the scenario table, insert it ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$check_query = "SELECT * FROM ConsultingMQ.hr_scenario_test2 WHERE ScenarioID='".$scenID."'";

$check = mysql_query($check_query);

if ($check) {
	$n_rows = mysql_num_rows($check);
	if ($n_rows == 0) {
		$insert_input  = "INSERT INTO ConsultingMQ.hr_scenario_test2"
						."(ScenarioID, TimeID, IndicatorID, DataValue, sessionID)"
						." SELECT '".$scenID."', TimeID, IndicatorID, DataValue, SessionID"
						." FROM ConsultingMQ.hr_input_test2"
						." WHERE SessionID='".$SID."'";
		
		mysql_query($insert_input);
	} //if n_rows = 0
	/*else {
		echo "DB_Error";
		exit;
	}*/
}//if check
else {
	echo "DB_Error";
	exit;
}

//get the necessary data from the database ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//query the database for the data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$query  = "SELECT scenarioID, hr3_calc_view_test2.TimeID, TimeName, IndicatorID, DataValue "
		. " FROM ConsultingMQ.hr3_calc_view_test2, ConsultingMQ.hr_timeID_test2 "
		. " WHERE ConsultingMQ.hr_timeID_test2.TimeID = hr3_calc_view_test2.TimeID "
		. " AND ConsultingMQ.hr_timeID_test2.PeriodType = 1 "
		. " AND scenarioID='".$scenID."' "
		. " ORDER BY ConsultingMQ.hr3_calc_view_test2.TimeID";

$select_result = mysql_query($query);

//if the query is succesful, parse the received data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if ($select_result) {
	//arrays for data
	$timeIDs[]      = ""; $timeNames[] = "";
	$inds1[]        = ""; 
	//$inds2[]     = "";	$inds3[]        = ""; $inds4[]     = "";  // tb rm
	$inds5[]        = ""; $inds6[]     = "";
	$inds7[]        = ""; $inds8[]     = "";
	$inds9[]        = ""; $inds10[]    = "";
// updates go here	
	$inds17[]        = ""; $inds18[]    = "";	
	$inds21[]       = ""; $inds22[]    = "";
	$inds23[]       = ""; $inds24[]    = "";
	$inds25[]       = ""; $inds26[]    = "";

	$i = 0;
	$currentTime = 1;
	while ($row = mysql_fetch_array($select_result, MYSQL_ASSOC)) {
		$time = $row['TimeID'];
		if ($time > $currentTime) {
			$i += 1;
			$currentTime = $time;
		}
		$timeIDs[$i] = $currentTime;
		$timeNames[$i] = $row['TimeName'];

		$indicator = $row['IndicatorID'];
		switch($indicator) {
			case "1":
				$inds1[$i] = $row['DataValue'];
				break;

		/*	case "2":  // tb rm
				$inds2[$i] = $row['DataValue'];
				break;
			
			case "3": // tb rm
				$inds3[$i] = $row['DataValue'];
				break;

			case "4": // tb rm
				$inds4[$i] = $row['DataValue'];
				break;*/

			case "5":
				$inds5[$i] = $row['DataValue'];
				break;

			case "6":
				$inds6[$i] = $row['DataValue'];
				break;

			case "7":
				$inds7[$i] = $row['DataValue'];
				break;

			case "8":
				$inds8[$i] = $row['DataValue'];
				break;

			case "9":
				$inds9[$i] = $row['DataValue'];
				break;

			case "10":
				$inds10[$i] = $row['DataValue'];
				break;

			case "17":
				$inds17[$i] = $row['DataValue'];
				break;

			case "18":
				$inds18[$i] = $row['DataValue'];
				break;				
				
			case "21":
				$inds21[$i] = $row['DataValue'];
				break;

			case "22":
				$inds22[$i] = $row['DataValue'];
				break;
				
            case "23":
				$inds23[$i] = $row['DataValue'];
				break;
				
			case "24":
				$inds24[$i] = $row['DataValue'];
				break;
				
			case "25":
				$inds25[$i] = $row['DataValue'];
				break;
				
			case "26":
				$inds26[$i] = $row['DataValue'];
				break;
		}// switch row[IndicatorID];
	}//while row
	
}//if fetch_result
else {
	echo "DB_Error";
	exit;
}

//get the last forecast timeID
	$qry2 = "SELECT historyEnd as TimeID FROM `ConsultingMQ`.`hr_indicator_test2` where IndicatorID = 23";
	$qry2_result = mysql_query($qry2);								
	if ($qry2_result) {	
		$line = mysql_fetch_array($qry2_result, MYSQL_ASSOC);
		$last_timeID = $line['TimeID'] + $duration;		
	} else {
		echo "DB_Error in LastHistoric";
		exit;
	};	
	
//get the timeIDs of the first and last non-historic periods ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$first_timeID = 0;
//$last_timeID  = 444;

$period_query   = "SELECT mTimeID AS minTimeID, Max(ConsultingMQ.hr_scenario_test2.TimeID) AS maxTimeID "
				. " FROM ConsultingMQ.hr0_test2, ConsultingMQ.hr_scenario_test2 INNER JOIN ConsultingMQ.hr_timeID_test2 "
				. " ON ConsultingMQ.hr_timeID_test2.TimeID=ConsultingMQ.hr_scenario_test2.TimeID "
				. " WHERE ConsultingMQ.hr0_test2.PeriodType=1 AND ConsultingMQ.hr_timeID_test2.PeriodType=1 AND scenarioID='".$scenID."'";

$period_result = mysql_query($period_query);

if ($period_result) {
	while ($row = mysql_fetch_array($period_result, MYSQL_ASSOC)) {
		$first_timeID = $row['minTimeID'];
		//$last_timeID  = $row['maxTimeID'];
	}	
}
else {
	echo "DB_Error";
	exit;
}

$f = new ForecastCalculator_test2($timeIDs, $timeNames, $inds1, $inds5, $inds6, $inds7, $inds8, 
						$inds9, $inds10, $inds17, $inds18, $inds21, $inds22, $inds23, $inds24, $inds25, $inds26);

$inds25 = $f->calculateForecasts($first_timeID, $last_timeID, true, true);

if($inds25 == "DB_Error") {
	echo "DB_Error";
	exit;
}
else{
	for ($j = $first_timeID-12; $j < $last_timeID; $j++) {
		if ($inds23[$j] == "") {
			$inds24[$j] = "";
		} else {
			$inds24[$j] = $inds23[$j]*$inds21[$j];
		}
		
		$inds26[$j] = $inds25[$j]*$inds21[$j];
					
		if ($inds23[$j] == "") {
			$inds8[$j] = $inds25[$j];
		}	else {
			$inds8[$j] = $inds23[$j];
		}
	}
}

$update_result = updateTable($inds24, $inds25, $inds26, $timeIDs, $first_timeID, $last_timeID, 1, $scenID);

if ($update_result != "Success") {
	echo $update_result;
	exit;
}

//get annual data values ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$annualTimeIDs[]   = "";
$annualTimeNames[] = "";
//$annualInds2[]     = ""; $annualInds3[]  = ""; $annualInds4[]     = "";  // tb rm
$annualInds5[]  = "";
$annualInds7[]     = ""; $annualInds10[] = ""; $annualInds18[] = "";

$annualData_query   = "SELECT ConsultingMQ.hr_timeID_test2.TimeID, TimeName, IndicatorID, DataValue "
					. " FROM ConsultingMQ.hr3_calc_view_test2, ConsultingMQ.hr_timeID_test2 "
					. " WHERE ConsultingMQ.hr_timeID_test2.TimeID=ConsultingMQ.hr3_calc_view_test2.TimeID "
					. " AND PeriodType=3 AND scenarioID='".$scenID."' ORDER BY ConsultingMQ.hr_timeID_test2.TimeID";
					
$ad_result = mysql_query($annualData_query);

$i = 0;
if ($ad_result) {
	while($row = mysql_fetch_array($ad_result, MYSQL_ASSOC)) {
				$time = $row['TimeID'];
		if ($time > $currentTime) {
			$i += 1;
			$currentTime = $time;
		}
		$annualTimeIDs[$i] = $currentTime;
		$annualTimeNames[$i] = $row['TimeName'];

		$indicator = $row['IndicatorID'];
		switch($indicator) {
			/*case "2": // tb rm
				$annualInds2[$i] = $row['DataValue'];
				break;
			
			case "3": // tb rm
				$annualInds3[$i] = $row['DataValue'];
				break;

			case "4": // tb rm
				$annualInds4[$i] = $row['DataValue'];
				break;*/

			case "5":
				$annualInds5[$i] = $row['DataValue'];
				break;

			case "7":
				$annualInds7[$i] = $row['DataValue'];
				break;
				
			case "10":
				$annualInds10[$i] = $row['DataValue'];
				break;
				
			case "18":
				$annualInds18[$i] = $row['DataValue'];
				break;				
		}// switch row[IndicatorID];
	}//while row
}
else {
	echo "DB_Error9";
	exit;
}

//prepare and echo the results ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$results = "<entries>";
for ($l = 0; $l < count($timeNames); $l++) {
	$results.="<entry>";
	
	$results .= formMonthlyDataXML($timeNames[$l], $inds1[$l], 
									//$inds2[$l], $inds3[$l], $inds4[$l],  // tb rm
									$inds5[$l], $inds6[$l], $inds7[$l], $inds8[$l],
									$inds9[$l], $inds10[$l], $inds17[$l], $inds18[$l], 
									$inds21[$l], $inds22[$l], 
									$inds23[$l], $inds24[$l], $inds25[$l], $inds26[$l]);

	//if data is for the 12th month, append annual data as well
	if (substr($timeNames[$l], 4, strlen($timeNames[$l])-4) == "M12") {
		$m = array_keys($annualTimeNames, substr($timeNames[$l], 0, 4));
		if ($m) {		 
			$results.= formAnnualDataXML($annualTimeNames[$m[0]], 
										//$annualInds2[$m[0]], $annualInds3[$m[0]], $annualInds4[$m[0]], // tb rm
										$annualInds5[$m[0]], $annualInds7[$m[0]], $annualInds10[$m[0]],
										$annualInds18[$m[0]]);
		}		
	}

	$results.= "</entry>";	
}

$results .= "</entries>";

echo $results;

?>
