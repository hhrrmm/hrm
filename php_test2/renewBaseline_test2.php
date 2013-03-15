<?php

function renew_Baseline() {
    $duration = 60;//24;
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

//retrieve historic from 2008M12 to the end of forecast period ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$Hist_query  = "SELECT TimeID, IndicatorID, DataValue "
			. " FROM ConsultingMQ.hr_historic_test2 "
			. " WHERE ConsultingMQ.hr_historic_test2.TimeID >= 348 AND ConsultingMQ.hr_historic_test2.TimeID <= "
			.$last_timeID
			." AND IndicatorID < 20 ORDER BY TimeID";
			
	$result = mysql_query($Hist_query);
	
	if ($result) {
		//arrays for data
		$timeIDs[]      = "";
		$inds1[]        = ""; $inds2[]     = "";
		$inds3[]        = ""; $inds4[]     = "";
		$inds5[]        = ""; $inds6[]     = "";
		$inds7[]        = ""; $inds8[]     = "";
		$inds9[]        = ""; $inds10[]    = "";
		$inds21[]       = ""; $inds22[]    = "";

		$out = "";
		$i = 0;
		$currentTime = 1;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$time = $row['TimeID'];
			if ($time > $currentTime) {
				$i += 1;
				$currentTime = $time;
			}
			$timeIDs[$i] = $currentTime;

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

				case "21":
					$inds21[$i] = $row['DataValue'];
					break;

				case "22":
					$inds22[$i] = $row['DataValue'];
					break;
			}// switch row[IndicatorID];
		}//while row
	}//if result
	else {
		return "ErrorDB";
	}

//retrieve growth rates of baseline data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$rate_query = "SELECT TimeID, IndicatorID, Rate FROM ConsultingMQ.hr_growthRates_test2 "
				. " WHERE IndicatorID < 20 AND TimeID<=".$last_timeID." ORDER BY TimeID";
	
	$r_result = mysql_query($rate_query);
	
	if ($r_result) {
		//arrays for data
		$rTimeIDs[]      = ""; 
		$rates1[]        = ""; $rates2[]     = "";
		$rates3[]        = ""; $rates4[]     = "";
		$rates5[]        = ""; $rates6[]     = "";
		$rates7[]        = ""; $rates8[]     = "";
		$rates9[]        = ""; $rates10[]    = "";
		$rates21[]       = ""; $rates22[]    = "";

		$i = 1;
		$currentTime = 1;
		while ($row = mysql_fetch_array($r_result, MYSQL_ASSOC)) {
			$time = $row['TimeID'];
			if ($time > $currentTime) {
				$i += 1;
				$currentTime = $time;
			}
			$rTimeIDs[$i] = $currentTime;

			$indicator = $row['IndicatorID'];
			switch($indicator) {
				case "1":
					$rates1[$i] = $row['Rate'];
					break;

				case "2":
					$rates2[$i] = $row['Rate'];
					break;
				
				case "3":
					$rates3[$i] = $row['Rate'];
					break;

				case "4":
					$rates4[$i] = $row['Rate'];
					break;

				case "5":
					$rates5[$i] = $row['Rate'];
					break;

				case "6":
					$rates6[$i] = $row['Rate'];
					break;

				case "7":
					$rates7[$i] = $row['Rate'];
					break;

				case "8":
					$rates8[$i] = $row['Rate'];
					break;

				case "9":
					$rates9[$i] = $row['Rate'];
					break;

				case "10":
					$rates10[$i] = $row['Rate'];
					break;

				case "21":
					$rates21[$i] = $row['Rate'];
					break;

				case "22":
					$rates22[$i] = $row['Rate'];
					break;
			}// switch row[IndicatorID];
		}//while row
	}// if r_result
	else {
		return "ErrorDB";
	}
	
//if there are no historic values, calculate new baseline values ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	for ($j=1; $j <= count($rTimeIDs); $j++) {
		if ($inds1[$j]  == "") { $inds1[$j]  = $inds1[$j-1] *$rates1[$j];}
		if ($inds2[$j]  == "") { $inds2[$j]  = $inds2[$j-1] *$rates2[$j];}
		if ($inds3[$j]  == "") { $inds3[$j]  = $inds3[$j-1] *$rates3[$j];}
		if ($inds4[$j]  == "") { $inds4[$j]  = $inds4[$j-1] *$rates4[$j];}
		if ($inds5[$j]  == "") { $inds5[$j]  = $inds5[$j-1] *$rates5[$j];}
		if ($inds6[$j]  == "") { $inds6[$j]  = $inds6[$j-1] *$rates6[$j];}
		if ($inds7[$j]  == "") { $inds7[$j]  = $inds7[$j-1] *$rates7[$j];}
		if ($inds8[$j]  == "") { $inds8[$j]  = $inds8[$j-1] *$rates8[$j];}
		if ($inds9[$j]  == "") { $inds9[$j]  = $inds9[$j-1] *$rates9[$j];}
		if ($inds10[$j] == "") { $inds10[$j] = $inds10[$j-1]*$rates10[$j];}
		if ($inds21[$j] == "") { $inds21[$j] = $inds21[$j-1]*$rates21[$j];}
		if ($inds22[$j] == "") { $inds22[$j] = $inds22[$j-1]*$rates22[$j];}
	}

//update baseline data table ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	for ($k=1; $k <= count($rTimeIDs); $k++) {
		$u_qry1 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds1[$k]
				. " WHERE IndicatorID=1 AND TimeID=".$timeIDs[$k];
		$u_qry2 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds2[$k]
				. " WHERE IndicatorID=2 AND TimeID=".$timeIDs[$k];
		$u_qry3 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds3[$k]
				. " WHERE IndicatorID=3 AND TimeID=".$timeIDs[$k];
		$u_qry4 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds4[$k]
				. " WHERE IndicatorID=4 AND TimeID=".$timeIDs[$k];
		$u_qry5 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds5[$k]
				. " WHERE IndicatorID=5 AND TimeID=".$timeIDs[$k];
		$u_qry6 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds6[$k]
				. " WHERE IndicatorID=6 AND TimeID=".$timeIDs[$k];
		$u_qry7 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds7[$k]
				. " WHERE IndicatorID=7 AND TimeID=".$timeIDs[$k];
		$u_qry8 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds8[$k]
				. " WHERE IndicatorID=8 AND TimeID=".$timeIDs[$k];
		$u_qry9 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds9[$k]
				. " WHERE IndicatorID=9 AND TimeID=".$timeIDs[$k];
		$u_qry10= "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds10[$k]
				. " WHERE IndicatorID=10 AND TimeID=".$timeIDs[$k];
		$u_qry21= "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds21[$k]
				. " WHERE IndicatorID=21 AND TimeID=".$timeIDs[$k];
		$u_qry22= "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$inds22[$k]
				. " WHERE IndicatorID=22 AND TimeID=".$timeIDs[$k];

		$r1  = mysql_query($u_qry1);  $r2  = mysql_query($u_qry2);
		$r3  = mysql_query($u_qry3);  $r4  = mysql_query($u_qry4);
		$r5  = mysql_query($u_qry5);  $r6  = mysql_query($u_qry6);
		$r7  = mysql_query($u_qry7);  $r8  = mysql_query($u_qry8);
		$r9  = mysql_query($u_qry9);  $r10 = mysql_query($u_qry10);
		$r21 = mysql_query($u_qry21); $r22 = mysql_query($u_qry22);
	}//for k
	
	
// perform similar operations for annual data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//retrieve annual growth rates of baseline data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$rate_query = "SELECT TimeID, IndicatorID, Rate FROM ConsultingMQ.hr_growthRates_test2 "
				. " WHERE IndicatorID < 20 AND TimeID>=621 ORDER BY TimeID";
	
	$r_result = mysql_query($rate_query);
	
	if ($r_result) {
		//arrays for data
		$raTimeIDs[] = ""; 
		$arates2[]   = "";
		$arates3[]   = ""; 
		$arates4[]   = "";
		$arates5[]   = ""; 		
		$arates7[]   = ""; 
		$arates10[]  = "";

		//$i = 0;
		$i = 0;
		$currentTime = 1;
		while ($row = mysql_fetch_array($r_result, MYSQL_ASSOC)) {
			$time = $row['TimeID'];
			if ($time > $currentTime) {
				$i += 1;
				$currentTime = $time;
			}
			$raTimeIDs[$i] = $currentTime;

			$indicator = $row['IndicatorID'];
			switch($indicator) {
				case "2":
					$arates2[$i] = $row['Rate'];
					break;
				
				case "3":
					$arates3[$i] = $row['Rate'];
					break;

				case "4":
					$arates4[$i] = $row['Rate'];
					break;

				case "5":
					$arates5[$i] = $row['Rate'];
					break;

				case "7":
					$arates7[$i] = $row['Rate'];
					break;

				case "10":
					$arates10[$i] = $row['Rate'];
					break;
			}// switch row[IndicatorID];
		}//while row
	}// if r_result
	else {
		return "ErrorDB";
	}
	
//retrieve annual historic from 2008 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$Hist_query = "SELECT TimeID, IndicatorID, DataValue "
				. " FROM ConsultingMQ.hr_historic_test2 "
				. " WHERE TimeID >=621 AND DataValue IS NOT NULL ORDER BY TimeID";
			
	$result = mysql_query($Hist_query);
	
	if ($result) {
		//arrays for data
		$aTimeIDs[] = "";
		$ainds2[]   = "";
		$ainds3[]   = "";
		$ainds4[]   = "";
		$ainds5[]   = "";
		$ainds7[]   = ""; 
		$ainds10[]   = "";

		for ($j=1; $j < count($raTimeIDs); $j++) {
		//for ($j=1; $j <= count($raTimeIDs); $j++) {		
			if ($raTimeIDs) {
				$aTimeID[$j] = $raTimeIDs[$j];
			};
			
			$ainds2[$j] = ""; 
			$ainds3[$j]  = "";
			$ainds4[$j] = ""; 
			$ainds5[$j]  = "";
			$ainds7[$j] = ""; 
			$ainds10[$j] = "";
		}
		
		$i = 0;
		$currentTime = 1;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$time = $row['TimeID'];
			if ($time > $currentTime) {
				$i += 1;
				$currentTime = $time;
			}
			$atimeIDs[$i] = $currentTime;
			

			$indicator = $row['IndicatorID'];
			switch($indicator) {
				case "2":
					$ainds2[$i] = $row['DataValue'];
					break;
				
				case "3":
					$ainds3[$i] = $row['DataValue'];
					break;

				case "4":
					$ainds4[$i] = $row['DataValue'];
					break;

				case "5":
					$ainds5[$i] = $row['DataValue'];
					break;

				case "7":
					$ainds7[$i] = $row['DataValue'];
					break;

				case "10":
					$ainds10[$i] = $row['DataValue'];
					break;
					
				default:
					break;
			}// switch row[IndicatorID];
		}//while row
	}//if result
	else {
		return "ErrorDB";
	}
	
//if there are no historic values, calculate new annual baseline values ~~~~~~~~~~~~~~~~~~~~~~~~~~~

	//for ($j=2; $j <= count($raTimeIDs); $j++) {
	for ($j=1; $j < count($raTimeIDs); $j++) {
		if ($ainds2[$j]  == "") { $ainds2[$j]  = $ainds2[$j-1] *$arates2[$j];}
		if ($ainds3[$j]  == "") { $ainds3[$j]  = $ainds3[$j-1] *$arates3[$j];}
		if ($ainds4[$j]  == "") { $ainds4[$j]  = $ainds4[$j-1] *$arates4[$j];}
		if ($ainds5[$j]  == "") { $ainds5[$j]  = $ainds5[$j-1] *$arates5[$j];}
		if ($ainds7[$j]  == "") { $ainds7[$j]  = $ainds7[$j-1] *$arates7[$j];}
		if ($ainds10[$j] == "") { $ainds10[$j] = $ainds10[$j-1]*$arates10[$j];}
	}

//update baseline data table ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	for ($k=1; $k < count($raTimeIDs); $k++) {
		$u_qry2 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$ainds2[$k]
				. " WHERE IndicatorID=2 AND TimeID=".$raTimeIDs[$k];
		$u_qry3 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$ainds3[$k]
				. " WHERE IndicatorID=3 AND TimeID=".$raTimeIDs[$k];
		$u_qry4 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$ainds4[$k]
				. " WHERE IndicatorID=4 AND TimeID=".$raTimeIDs[$k];
		$u_qry5 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$ainds5[$k]
				. " WHERE IndicatorID=5 AND TimeID=".$raTimeIDs[$k];
		$u_qry7 = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$ainds7[$k]
				. " WHERE IndicatorID=7 AND TimeID=".$raTimeIDs[$k];
		$u_qry10= "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$ainds10[$k]
				. " WHERE IndicatorID=10 AND TimeID=".$raTimeIDs[$k];

		$r2  = mysql_query($u_qry2);
		$r3  = mysql_query($u_qry3);  
		$r4  = mysql_query($u_qry4);
		$r5  = mysql_query($u_qry5);
		$r7  = mysql_query($u_qry7);  
		$r10 = mysql_query($u_qry10);	
	}//for k

	return "Success";
}// function

?>
