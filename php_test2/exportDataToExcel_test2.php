<?php

/*
** Used to export scenario data to an .xls file
*/

include 'Classes/PHPExcel/IOFactory.php';
include 'MySQLConnectionDataProvider.php';
$duration = 60; //24;

$SID = $_GET['sessionID']; $scenID = $_GET['scenarioID'];

$credentials = new MySQLConnectionDataProvider();

$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
mysql_select_db($credentials->DB);

//get the last forecast timeID"
	$qry2 = "SELECT historyEnd as TimeID FROM `ConsultingMQ`.`hr_indicator_test2` where IndicatorID = 23";
	$qry2_result = mysql_query($qry2);								
	if ($qry2_result) {	
		$line = mysql_fetch_array($qry2_result, MYSQL_ASSOC);
		$last_timeID = $line['TimeID'] + $duration;				
		$fTimeID = $line['TimeID'];
	} else {
		echo "DB_Error in LastHistoric";
		exit;
	};	
	
	$qry3 = "SELECT TimeID FROM `ConsultingMQ`.`hr_lastHistoric_period_test2` where periodType = 3";
	$qry3_result = mysql_query($qry3);								
	if ($qry3_result) {	
		$line = mysql_fetch_array($qry3_result, MYSQL_ASSOC);		
		$fTimeIDAnnual = $line['TimeID'] + ($duration/12);
	} else {
		echo "DB_Error in LastHistoricAnnual";
		exit;
	};	

//query the database for monthly scenario data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$query  = "SELECT scenarioID, hr3_calc_view_test2.TimeID, TimeName, IndicatorID, DataValue "
		. " FROM ConsultingMQ.hr3_calc_view_test2, ConsultingMQ.hr_timeID_test2 "
		. " WHERE ConsultingMQ.hr_timeID_test2.TimeID = hr3_calc_view_test2.TimeID "
		. " AND ConsultingMQ.hr_timeID_test2.PeriodType = 1 "
		. " AND scenarioID='".$scenID."' "
		. " ORDER BY hr3_calc_view_test2.TimeID";
		
//echo $query;

$select_result = mysql_query($query);

//if the query is succesful, parse the received data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if ($select_result) {
	//arrays for data
	$timeIDs[] = ""; $timeNames[] = "";
	$inds1[]   = ""; $inds2[]     = ""; $inds3[]  = ""; $inds4[]  = "";
	$inds5[]   = ""; $inds6[]     = "";	$inds7[]  = ""; $inds8[]  = "";
	$inds9[]   = ""; $inds10[]    = "";	 $inds17[]   = ""; $inds18[]    = "";	
	$inds21[]  = ""; $inds22[]    = "";
	$inds23[]  = ""; $inds24[]    = "";	$inds25[] = ""; $inds26[] = "";
	$inds27[]  = ""; 

	$i = 0; $j = 0;
	$currentTime = 1;
	$currentYear = 1984;
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
	
	$t = 1000;
	$t2 = 1000;
	
	$aggregateUnits[] = "";
	$aggUnitsQuat[] = "";
	$aggUnitsAnn[] = "";
	
	$aggregateValue[] = "";
	$aggValueQuat[] = "";
	$aggValueAnn[] = "";
	
	$aggregateValueMrkt[] = "";
	$aggValueQuatMrkt[] = "";
	$aggValueAnnMrkt[] = "";	
	
	$aggTimeIDs[] = "";
	$j2 = -2;
	
	for ($j = 0; $j < count($timeIDs); $j++) {
		if ($inds23[$j] == "") {
			if ($inds25[$j] == "") {
				$inds27[$j] = "";
			}
			else {
				if ($inds22[$j]==0 || $inds22[$j]=="") {$inds27[$j] = "";}
				else {$inds27[$j] = ($inds26[$j]/$inds22[$j]);}
			}
		}
		else {
			if ($inds22[$j]==0 || $inds22[$j]=="") {$inds27[$j] = "";}
			else {$inds27[$j] = ($inds24[$j]/$inds22[$j]);}
		}
		
		if (substr($timeNames[$j],0,4) > $t) {
			$j2 += 1;
			$aggregateUnits[$j2] = 0;
			$aggregateValue[$j2] = 0;
			$aggregateValueMrkt[$j2] = 0;
			///here might be a problem???
			$aggTimeIDs[$j2] = substr($timeNames[$j],0,4) - 1980 + 593;
			$t = substr($timeNames[$j],0,4);			
			if ($j > 12) {
				$aggUnitsAnn[$j2] = $aggregateUnits[$j2 - 1] - $aggUnitsQuat[$j-1] + $aggUnitsQuat[$j - 13];
				$aggValueAnn[$j2] = $aggregateValue[$j2 - 1] - $aggValueQuat[$j-1] + $aggValueQuat[$j - 13];				
				$aggValueAnnMrkt[$j2] = $aggregateValueMrkt[$j2 - 1] - $aggValueQuatMrkt[$j-1] + $aggValueQuatMrkt[$j - 13];				
			} else {				
			};
		}
		//jei idetume paskutiniu triju menesiu suma
		//..ir po to ja atimtume is normaliu metiniu sumos			
		
		if ($inds23[$j] == "") {
			$aggregateUnits[$j2] += round($inds25[$j],0);
			$aggregateValue[$j2] += $inds26[$j];
			$aggregateValueMrkt[$j2] += $inds27[$j];
		//$aggregateValue[$j2] += $inds26[$j];
		}
		else {			
			$aggregateUnits[$j2] += $inds23[$j];
			$aggregateValue[$j2] += $inds24[$j];
			$aggregateValueMrkt[$j2] += $inds27[$j];
			//$aggregateValue[$j2] += $inds24[$j];
		}	
	
		$idas = substr($timeNames[$j],5,strlen($timeNames[$j])-1);		
		if ($idas > 9)  {									
			if ($inds23[$j] == "") {
				$aggUnitsQuat[$j] = $aggUnitsQuat[$j-1] + round($inds25[$j],0);				
				$aggValueQuat[$j] = $aggValueQuat[$j-1] + $inds26[$j];
				$aggValueQuatMrkt[$j] = $aggValueQuatMrkt[$j-1] + $inds27[$j];
			} else {			
				$aggUnitsQuat[$j] = $aggUnitsQuat[$j-1] + $inds23[$j];
				$aggValueQuat[$j] = $aggValueQuat[$j-1] + $inds24[$j];
				$aggValueQuatMrkt[$j] = $aggValueQuatMrkt[$j-1] + $inds27[$j];
			};
		} else {
			$aggUnitsQuat[$j] = 0;		
			$aggValueQuat[$j] = 0;	
			$aggValueQuatMrkt[$j] = 0;	
		};
		
	}

//get monthly indicator and unit names ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$ind_query = "SELECT IndicatorName, UnitName FROM ConsultingMQ.hr_indicator_test2 WHERE IndicatorType=1 ORDER BY IndicatorID";
	
	$result_ind = mysql_query($ind_query);
	
	$indicatorNames[] = ""; $unitNames[] ="";
	$j = 0;
	
	if($result_ind) {
		while($line = mysql_fetch_array($result_ind, MYSQL_ASSOC)) {
			$indicatorNames[$j] = $line['IndicatorName'];
			$unitNames[$j]      = $line['UnitName'];
			$j += 1;
		}
	}
	else {
		echo "DB_Error";
		exit;
	}
	
// get forecast error bound parameters from DB (monthly) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$error_bound_params[] = "";
	$ebp_timeIDs[] = "";
	$i = 0;
	$param_query= "SELECT ConsultingMQ.hr_predictionIntervals_test2.TimeID, Error "
				. " FROM ConsultingMQ.hr_predictionIntervals_test2, ConsultingMQ.hr_timeID_test2 "
				. " WHERE ConsultingMQ.hr_timeID_test2.TimeID=ConsultingMQ.hr_predictionIntervals_test2.TimeID "
				. " AND PeriodType=1"
				. " AND ConsultingMQ.hr_timeID_test2.TimeID <=". $last_timeID
				. " ORDER by TimeID ASC";	
	$p_result = mysql_query($param_query);
	
	$p_result = mysql_query($param_query);	
	//echo  $param_query;
	//exit;	
	if ($p_result) {
		while ($line = mysql_fetch_array($p_result, MYSQL_ASSOC)) {
			$error_bound_params[$i] = $line['Error'];
			$ebp_timeIDs[$i] = $line['TimeID'];
			$i += 1;
		}
	}
	else {
		echo "DB_Error";
		exit;
	}
	
//calculate forecast error bounds (monthly) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$upper_bounds[] = "";
	$lower_bounds[] = "";
	
	for ($n = 0; $n < count($error_bound_params); $n++) {
		$key = array_keys($timeIDs, $ebp_timeIDs[$n]);
		
		if ($key) {
			if ($inds23[$key[0]] == "") {
				if ($inds25[$key[0]] <> "") {
					$a = $inds25[$key[0]]*$error_bound_params[$n];

					$upper_bounds[$n] = $inds25[$key[0]]+$a;
					$lower_bounds[$n] = $inds25[$key[0]]-$a;
				}
				else {
					$upper_bounds[$n] = "";
					$lower_bounds[$n] = "";
				}
			}
			else {
				$upper_bounds[$n] = "";
				$lower_bounds[$n] = "";
			}
		}
		else {
			$upper_bounds[$n] = "";
			$lower_bounds[$n] = "";
		}
	}

//query the database for annual scenario data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$query  = "SELECT scenarioID, hr3_calc_view_test2.TimeID, TimeName, IndicatorID, DataValue "
		. " FROM ConsultingMQ.hr3_calc_view_test2, ConsultingMQ.hr_timeID_test2 "
		. " WHERE ConsultingMQ.hr_timeID_test2.TimeID = hr3_calc_view_test2.TimeID "
		. " AND ConsultingMQ.hr_timeID_test2.PeriodType = 3 "
		. " AND scenarioID='".$scenID."' "
		. " ORDER BY hr3_calc_view_test2.TimeID";

$select_result = mysql_query($query);

//if the query is succesful, parse the received data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if ($select_result) {
	//arrays for data
	$annTimeIDs[]      = ""; $annTimeNames[]    = "";
	$inds2a[]           = ""; $inds3a[]           = "";
	$inds4a[]           = ""; $inds5a[]           = ""; 
	$inds7a[]           = ""; $inds10a[]          = "";
	$inds18a[]          = "";
	
	$i = 0;
	$currentTime = 1;
	while ($row = mysql_fetch_array($select_result, MYSQL_ASSOC)) {
		$time = $row['TimeID'];
		if ($time > $currentTime) {
			$i += 1;			
			$currentTime = $time;
		}
		$annTimeIDs[$i] = $currentTime;
		$annTimeNames[$i] = $row['TimeName'];

		$indicator = $row['IndicatorID'];
		switch($indicator) {
			case "2":
				$inds2a[$i] = $row['DataValue'];
				break;

			case "3":
				$inds3a[$i] = $row['DataValue'];
				break;

			case "4":
				$inds4a[$i] = $row['DataValue'];
				break;

			case "5":
				$inds5a[$i] = $row['DataValue'];
				break;

			case "7":
				$inds7a[$i] = $row['DataValue'];
				break;

			case "10":
				$inds10a[$i] = $row['DataValue'];
				break;
				
			case "18":
				$inds18a[$i] = $row['DataValue'];
				break;				
		}// switch row[IndicatorID];		
	}//while row
}
else {
	echo "DB_Error";
	exit;
}

//get annual indicator and unit names ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$ind_query = "SELECT IndicatorName, UnitName FROM ConsultingMQ.hr_indicator_test2 WHERE IndicatorType=3 ORDER BY IndicatorID";
	
	$result_ind = mysql_query($ind_query);
	
	$annIndicatorNames[] = ""; $annUnitNames[] ="";
	$j = 0;
	
	if($result_ind) {
		while($line = mysql_fetch_array($result_ind, MYSQL_ASSOC)) {
			$annIndicatorNames[$j] = $line['IndicatorName'];
			$annUnitNames[$j]      = $line['UnitName'];
			$j += 1;
		}
	}
	else {
		echo "DB_Error";
		exit;
	}
	
// get forecast error bound parameters from DB (annual) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$annError_bound_params[] = "";
	$aebp_timeIDs[] = "";
	$i = 0;
	$param_query= "SELECT ConsultingMQ.hr_predictionIntervals_test2.TimeID, Error "
				. " FROM ConsultingMQ.hr_predictionIntervals_test2, ConsultingMQ.hr_timeID_test2 "
				. " WHERE ConsultingMQ.hr_timeID_test2.TimeID=ConsultingMQ.hr_predictionIntervals_test2.TimeID "
				. " AND PeriodType=3"
				."  AND ConsultingMQ.hr_predictionIntervals_test2.TimeID <=".$fTimeIDAnnual;
		
	$p_result = mysql_query($param_query);
		
	if ($p_result) {
		while ($line = mysql_fetch_array($p_result, MYSQL_ASSOC)) {
			$annError_bound_params[$i] = $line['Error'];
			$aebp_timeIDs[$i] = $line['TimeID'];
			$i += 1;
		}
	}
	else {
		echo "DB_Error";
		exit;
	}

//calculate aggregate forecast error bounds ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$annUpper_bounds[] = "";
$annLower_bounds[] = "";

for ($n = -1; $n < count($aggTimeIDs)-1; $n++) {
	$annUpper_bounds[$n] = "";
	$annLower_bounds[$n] = "";
	
	for ($n2 = 0; $n2 < count($aebp_timeIDs); $n2++) {
		if ($aebp_timeIDs[$n2] == $aggTimeIDs[$n]) {
		
			//$a = $aggregateUnits[$n]*$annError_bound_params[$n2];
			//cia pakeichiam eroru skaichiavimus
			$a = $aggUnitsAnn[$n + 1]*$annError_bound_params[$n2];			
						
			//$annUpper_bounds[$n] = $aggregateUnits[$n]+$a;
			//$annLower_bounds[$n] = $aggregateUnits[$n]-$a;
			
			$annUpper_bounds[$n] = $aggUnitsAnn[$n + 1] + $a;
			$annLower_bounds[$n] = $aggUnitsAnn[$n + 1] - $a;
		}
	}
}	
	
//get rescale value	
	$applyRescale = true;;
	if($applyRescale) {
		//get rescale value ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		$rescale_query = "SELECT RescaleValue FROM ConsultingMQ.hr_rescale_test2 WHERE ID=1";
		$rescale = 1.00217395060107; //0.99870059566961; //0.990729507991746;			
		$resc_result = mysql_query($rescale_query);
			
		if ($resc_result) {
			while ($r = mysql_fetch_array($resc_result, MYSQL_ASSOC)) {
				$rescale = $r['RescaleValue'];
			}
		}
		else {
			//return "DB_Error3";
		}
	}//if applyRescale	

	
// put the data into a PHPExcel object ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$phpExcel = new PHPExcel();
	$phpExcel->setActiveSheetIndex(0);
	$phpExcel->getActiveSheet()->setTitle("New scenario ".$scenID);

	//add column names to first row
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Period');
	for ($k = 0; $k < count($indicatorNames)-4; $k++) {
		$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+1, 1, $indicatorNames[$k]);
	}
	//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+1, 1, "Sales volume of medical equipment");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+1, 1, "Sales value of medical equipment");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+2, 1, "Total sales value of of medical equipment");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+3, 1, "Upper forecast error bound");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+4, 1, "Lower forecast error bound");
	//cia rescale	
	//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+6, 1, "current rescale");

	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(14, 1, 'Period');
	for ($k = 0; $k < count($annIndicatorNames); $k++) {
		$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+15, 1, $annIndicatorNames[$k]);
	}
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+15, 1, "Sales volume of medical equipment, Annual");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+16, 1, "Sales value of medical equipment, Annual");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+17, 1, "Total sales value of medical equipment, Annual");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+18, 1, "Upper forecast error bound, Annual");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($k+19, 1, "Lower forecast error bound, Annual");

	//add unit names to second row
	for ($l = 0; $l < count($indicatorNames)-4; $l++) {
		$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($l+1, 2, $unitNames[$l]);
	}
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($l+1, 2, "");
	$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($l+2, 2, "USD");
	
	for ($l = 0; $l < count($annIndicatorNames); $l++) {
		$phpExcel->getActiveSheet()->setCellValueByColumnAndRow($l+15, 2, $annUnitNames[$l]);
	}
	
	$n = 0;
	//add monthly data values
	for ($m = 0; ( ($m < count($timeIDs)) && ($m < $last_timeID) ) ; $m++) {
		if (substr($timeNames[$m], 0, 4) >= 1985 && substr($timeNames[$m], 0, 4) < 2020) {
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $m-$n+3, $timeNames[$m]);
			//cia idejom timenames
			//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $m-$n+3,$aggUnitsQuat[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $m-$n+3, $inds1[$m]);			
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $m-$n+3, $inds6[$m]);
		
			if ($inds23[$m] == "") { // instead of indi8
				if ($inds25[$m] <> "") {
					$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(3,  $m-$n+3, round($inds25[$m],0));
				}
			}
			else{
				$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(3,  $m-$n+3, $inds23[$m]);
			};
		
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $m-$n+3, $inds9[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $m-$n+3, $inds17[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $m-$n+3, $inds21[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $m-$n+3, $inds22[$m]);			
			
			//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $m-$n+3, $inds25[$m]);
			//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(8 - 1, $m-$n+3, $inds26[$m]);
			
			if ($inds23[$m] == "") {
				if ($inds25[$m] <> "") {
					//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $m-$n+3, round($inds25[$m],0));
					$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $m-$n+3, $inds26[$m]);
				}
			}
			else {
				//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $m-$n+3, round($inds23[$m],0));
				$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $m-$n+3, $inds24[$m]);
			}
			
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $m-$n+3, $inds27[$m]);

						
			if ($m >= $fTimeID) {				
				//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(10 - 1, $m-$n+3, $m - $fTimeID);
				//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(11 - 1, $m-$n+3, $m);
				$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $m-$n+3, $lower_bounds[$m - $fTimeID]);	
				$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(11 , $m-$n+3, $upper_bounds[$m - $fTimeID]);
			}						
			//-------------------------cia rescale---------------------------
			//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $m-$n+3, $rescale);	
			//===============================
			
		} //if 1985<=year<2020
		else { $n = $n + 1; }
	}

	$n = 0;
	//add annual data values
	for ($m = 0; $m < count($annTimeIDs); $m++) {
		
		if ($annTimeNames[$m] >=1985 && ($annTimeIDs[$m] <= $fTimeIDAnnual)) {
		//if ($annTimeNames[$m] >=1985 && $annTimeNames[$m] <= 2020) {
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $m-$n+3, $annTimeNames[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $m-$n+3, $inds2a[$m]);
			//$aggUnitsAnn			
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $m-$n+3, $inds3a[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $m-$n+3, $inds4a[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $m-$n+3, $inds5a[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $m-$n+3, $inds7a[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $m-$n+3, $inds10a[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $m-$n+3, $inds18a[$m]);
			
			//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $m-$n+3, $aggregateUnits[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $m-$n+3, $aggUnitsAnn[$m + 1]);			
			//$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $m-$n+3, $aggregateValue[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $m-$n+3, $aggValueAnn[$m + 1]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $m-$n+3, $aggValueAnnMrkt[$m + 1]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $m-$n+3, $annUpper_bounds[$m]);
			$phpExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $m-$n+3, $annLower_bounds[$m]);
		}
		else { $n = $n + 1; }
	}	

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"consulting".time().".xls\"");
	header("Cache-Control: max-age=0");
	$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
	$objWriter->save("php://output");
	exit;
	
}// if select result
else {
	echo "DB_Error";
	exit;
}

?>
