<?php

/*
** used for retrieving new scenario data from the database
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

$use_baseline = false;

//check if there is any new scenario input data for this session in the DB ~~~~~~~~~~~~~~~~~~~~~~~~
$check_query  =  "SELECT COUNT(*) AS DataCount"
				." FROM ConsultingMQ.hr_input_test2 "
				." WHERE sessionID='".$SID."'";
				
$check_result = mysql_query($check_query);

if ($check_result) {
	$use_baseline = true; 
	while ($row = mysql_fetch_array($check_result, MYSQL_ASSOC)) {
//if there's no data, insert baseline data as default new scenario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if($row['DataCount'] == 0) {
			$insert_query =  " INSERT INTO ConsultingMQ.hr_input_test2 "
							." (sessionID, LastUpdated, TimeID, IndicatorID, DataValue) "
							." SELECT '". $SID."' AS sessionID, CURRENT_TIMESTAMP, TimeID, IndicatorID, DataValue "
							." FROM ConsultingMQ.hr_baseline_test2 ";
						
			$insert_result = mysql_query($insert_query);
		
			if(!$insert_result) {
				echo "DB_Error";
				exit;
			}
		}
	}//while row
}
else {
	echo "DB_Error";
	exit;
}

//check if there is any new scenario data for this session in the DB ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$check_scn_query="SELECT COUNT(*) AS DataCount"
				." FROM ConsultingMQ.hr_scenario_test2 "
				." WHERE scenarioID='".$scenID."'";
				
$check_scn_result = mysql_query($check_scn_query);

if ($check_scn_result) {
	$use_baseline = true;
	while ($row = mysql_fetch_array($check_scn_result, MYSQL_ASSOC)) {
//if there's no data, insert baseline data as default new scenario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if($row['DataCount'] == 0) {
			$insert_scn_query =  " INSERT INTO ConsultingMQ.hr_scenario_test2 "
							." (scenarioID,  TimeID, IndicatorID, DataValue, sessionID) "
							." SELECT '".$scenID."' AS scenarioID, TimeID, IndicatorID, DataValue, '"
							. $SID."' AS sessionID "
							." FROM ConsultingMQ.hr_baseline_test2 ";
						
			$insert_scn_result = mysql_query($insert_scn_query);
		
			if(!$insert_scn_result) {
				echo "DB_Error";
				exit;
			}
		}
	}//while row
}
else {
	echo "DB_Error";
	exit;
}

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
	$inds1[]        = ""; $inds2[]     = "";
	$inds3[]        = ""; $inds4[]     = "";
	$inds5[]        = ""; $inds6[]     = "";
	$inds7[]        = ""; $inds8[]     = "";
	$inds9[]        = ""; $inds10[]    = "";
	$inds17[]        = ""; $inds18[]    = "";
// new indicators IDs go here
	$inds21[]       = ""; $inds22[]    = "";
	$inds23[]       = ""; $inds24[]    = "";
	$inds25[]       = ""; $inds26[]       = "";

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

			case "2":
				$inds2[$i] = $row['DataValue'];
				break;
			
			case "3":
				$inds3[$i] = $row['DataValue'];
				break;

			case "4":
				$inds4[$i] = $row['DataValue'];
				break;

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

$f = new ForecastCalculator_test2($timeIDs, $timeNames, $inds1, $inds2, $inds3, $inds4, $inds5, $inds6, $inds7, $inds8,
						$inds9, $inds10, $inds21, $inds22, $inds23, $inds24, $inds25, $inds26);

$ind25 = $f->calculateForecasts($first_timeID, $last_timeID, true, true);

if($ind25 == "DB_Error") {
	echo "DB_Error";
	exit;
} else {
	for ($j = $first_timeID-12; $j < $last_timeID; $j++) {
		if ($inds23[$j] == "") {$inds24[$j] = "";}
		else {$inds24[$j] = $inds23[$j]*$inds21[$j];}
		
		$inds26[$j] = $inds25[$j]*$inds21[$j];
					
		if ($inds23[$j] == "") {
			$inds8[$j] = $inds25[$j];
		}	else {
			$inds8[$j] = $inds23[$j];
		}
	}
}

if ($use_baseline) {
	$update_result = updateTable($inds24, $inds25, $inds26, $timeIDs, $first_timeID, $last_timeID, 1, $scenID);

	if ($update_result != "Success") {
		echo $update_result;
		exit;
	}
}//if use_baseline

//get annual data values ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$annualTimeIDs[]   = "";
$annualTimeNames[] = "";
$annualInds2[]     = ""; $annualInds3[]  = ""; 
$annualInds4[]     = ""; $annualInds5[]  = "";
$annualInds7[]     = ""; $annualInds10[] = "";

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
			case "2":
				$annualInds2[$i] = $row['DataValue'];
				break;
			
			case "3":
				$annualInds3[$i] = $row['DataValue'];
				break;

			case "4":
				$annualInds4[$i] = $row['DataValue'];
				break;

			case "5":
				$annualInds5[$i] = $row['DataValue'];
				break;

			case "7":
				$annualInds7[$i] = $row['DataValue'];
				break;
				
			case "10":
				$annualInds10[$i] = $row['DataValue'];
				break;
		}// switch row[IndicatorID];
	}//while row
}
else {
	echo "DB_Error";
	exit;
}

//prepare and echo the results ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$results = "<entries>";
for ($l = 0; $l < count($timeNames); $l++) {
	$results.="<entry>";
	
	$results .= formMonthlyDataXML($timeNames[$l], $inds1[$l], $inds2[$l], $inds3[$l], $inds4[$l], 
									$inds5[$l], $inds6[$l], $inds7[$l], $inds8[$l],
									$inds9[$l], $inds10[$l], $inds17[$l], $inds18[$l], 
									$inds21[$l], $inds22[$l], 
									$inds23[$l], $inds24[$l], $inds25[$l], $inds26[$l]);

	//if data is for the 12th month, append annual data as well
	if (substr($timeNames[$l], 4, strlen($timeNames[$l])-4) == "M12") {
		$m = array_keys($annualTimeNames, substr($timeNames[$l], 0, 4));
		if ($m) {
			$results.= formAnnualDataXML($annualTimeNames[$m[0]], $annualInds2[$m[0]], $annualInds3[$m[0]],
										$annualInds4[$m[0]], $annualInds5[$m[0]], $annualInds7[$m[0]], $annualInds10[$m[0]]);
		}		
	}

	$results.= "</entry>";
}

$results .= "</entries>";

echo $results;

?>