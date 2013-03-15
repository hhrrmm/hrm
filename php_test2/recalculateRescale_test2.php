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

//retrieve historic/baseline data necessary for calculations ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$query  = "SELECT ConsultingMQ.hr_timeID_test2.TimeID, TimeName, IndicatorID, DataValue "
		. " FROM ConsultingMQ.hr1_baseline_scenario_test2, ConsultingMQ.hr_timeID_test2 "
		. " WHERE ConsultingMQ.hr_timeID_test2.TimeID = ConsultingMQ.hr1_baseline_scenario_test2.TimeID "
		. " AND ConsultingMQ.hr_timeID_test2.PeriodType = 1 "
		. " ORDER BY ConsultingMQ.hr_timeID_test2.TimeID";

$select_result = mysql_query($query);

if ($select_result) {
	//arrays for data
	$timeIDs[]      = ""; $timeNames[] = "";
	$inds1[]        = ""; $inds2[]     = "";
	$inds3[]        = ""; $inds4[]     = "";
	$inds5[]        = ""; $inds6[]     = "";
	$inds7[]        = ""; $inds8[]     = "";
	$inds9[]        = ""; $inds10[]    = "";
	$inds21[]       = ""; $inds22[]    = "";
	$inds23[]       = ""; $inds24[]    = "";
	$inds25[]       = ""; $inds26[]    = "";
	$round25[]      = "";

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
				$round25[$i] = round($row['DataValue'],0);
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

//retrieve seasonal modifiers ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$seasonalModifierIDs[] = ""; $seasonalModifierValues[] = "";

$season_query = "SELECT modifierID, modifierName, modifierValue FROM ConsultingMQ.hr_seasonalModifiers_test2";

$season_result = mysql_query($season_query);

if ($season_result) {
	$i = 0;
	while ($r = mysql_fetch_array($season_result, MYSQL_ASSOC)) {
		$seasonalModifierIDs[$i] = $r['modifierID'];
		$seasonalModifierValues[$i] = $r['modifierValue'];
		$i += 1;
	}
}
else {
	echo "DB_Error";
	exit;
}

// calculate new values ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//calculation coefficients
		$intercept = 5.310481021; //4.80479202886946;		
		$coefA     = 3.299078287; //2.59834001426802;
		$coefB     = 3.371723152; //4.07205295839845;
		$coefC     = -1.221606389; //-0.742384224939517;
		$coefD     = 2.508731058; //2.15395050631486;
		$coefE     = 2.720947649; //2.80312241123395;
		$coefF     = 1.326854777; //1.38455041198899;
		$coefG     = 0;
		$coefH     = 0.344012193; //0.38391959058166;
		$coefI     = 0.266116467; //0.263113639572815;


/* old values
$intercept = 5.46634784330756;
$coefA     = 3.12726492447761;
$coefB     = 5.15367869858139;
$coefC     = -0.956204332035116;
$coefD     = 2.22520544195423;
$coefE     = 2.12190045615154;
$coefF     = 1.54408016870127;
$coefG     = 0;
$coefH     = 0.317862538254253;
$coefI     = 0.233914247951205;
*/

for ($j = $from-12; $j < $to; $j++) {	
	$A = $coefA*($inds2[$j-12] - $inds2[$j-24]);
	$B = $coefB*($inds3[$j-12] - $inds3[$j-24]);
	$C = $coefC*($inds4[$j-24]);
	$D = $coefD*(log($inds5[$j-36]) - log($inds5[$j-48]));
	
	if ($inds10[$j-10] == 0 || $inds10[$j-10] == "" || $inds10[$j-22] == 0 || $inds10[$j-22] =="") {
		$E = "";
	}
	else {
		$E = $coefE*(log($inds6[$j-10]/$inds10[$j-10])-log($inds6[$j-22]/$inds10[$j-22]));
	}
	
	if ($inds10[$j-24] == 0 || $inds10[$j-24] == "" || $inds10[$j-36] == 0 || $inds10[$j-36] == ""
		|| $inds1[$j-24] == 0 || $inds1[$j-24] == "" || $inds1[$j-36] == 0 || $inds1[$j-36] == "") {
		$F = "";
	}
	else {
		$F = $coefF*(log($inds7[$j-24]/$inds1[$j-24]/100/$inds10[$j-24]) - log($inds7[$j-36]/$inds1[$j-36]/100/$inds10[$j-36]));
	}
	$G = $coefG*log($inds8[$j-5]);
	$H = $coefH*log($inds8[$j-10]+$inds8[$j-11]+$inds8[$j-12]);
	
	if ($inds1[$j-16] ==0 || $inds1[$j-16] =="" || $inds10[$j-16] == 0 || $inds10[$j-16] == ""
		|| $inds1[$j-28] ==0 || $inds1[$j-28] =="" || $inds10[$j-28] == 0 || $inds10[$j-28] == "") {
		$I = "";
	}
	else {
		$I = $coefI*(log($inds9[$j-16]/$inds1[$j-16]/100/$inds10[$j-16]) - log($inds9[$j-28]/$inds1[$j-28]/100/$inds10[$j-28]));
	}
	
	$modifierID = "";
	$modifier = 0;
	if ( (substr($timeNames[$j], 0, 4) == 2003 && substr($timeNames[$j], 5, strlen($timeNames[$j])-5) > 10) 
		|| (substr($timeNames[$j], 0, 4) > 2003) ) {
		$modifierID .= "2M".substr($timeNames[$j], 5, strlen($timeNames[$j])-5);
	}
	else {
		$modifierID .= "1M".substr($timeNames[$j], 5, strlen($timeNames[$j])-5);
	}
	
	if (substr($timeNames[$j], 5, strlen($timeNames[$j])-5) == "12") {
		$modifier = 0;
	}
	else {
		$key = -1;
		for ($m = 0; $m < count($seasonalModifierIDs); $m++) {
			if ($seasonalModifierIDs[$m] == $modifierID) {
				$key = $m;
				$modifier = $seasonalModifierValues[$key];
				$m = count($seasonalModifierIDs);
			}
		}
	}
	
	$inds25[$j] = exp($intercept + $A + $B + $C + $D + $E + $F + $G + $H + $I + $modifier);

	$round25[$j] = round($inds25[$j], 0);

	$inds26[$j] = $inds25[$j]*$inds21[$j];

	if ($inds23[$j] == "") {
		$inds8[$j] = $inds25[$j];
	} else {
		$inds8[$j] = $inds23[$j];
	}
}//for j

//retrieve the periods to be used in calculating the rescale value ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$period_query   = "SELECT TimeID, TimeName "
				. " FROM ConsultingMQ.hr4_test2 INNER JOIN ConsultingMQ.hr_timeID_test2 "
				. " ON TimeID=mtime";
				
$p_result = mysql_query($period_query);

if ($p_result) {
	while ($r = mysql_fetch_array($p_result, MYSQL_ASSOC)) {
		$pID = $r['TimeID'];
		$pName = $r['TimeName'];
	}
}//if p_result
else {
	echo "DB_Error";
	exit;
}

$periodIDs[]="";

$a = substr($pName, 5, strlen($pName));
$b = substr($pName, 0, 4);

if ($b > 2011) {
	for ($i=1; $i<=12; $i++) {
		$periodIDs[$i-1] = $pID - $i;
	}
} else {
	for ($i=0; $i<12; $i++) {
		if ($i == $a) { $periodIDs[$i] = $pID; }
		else if ($i < $a) { $periodIDs[$i] = $pID-($a-$i); }
		else { $periodIDs[$i] = $pID+($i-$a); }
	}
}

//calculate new rescale value
$rescale = 0.998614802334848;//0.998614802334848; //0.990729507991746;
//default value

$sum1 = 0;
$sum2 = 0;
$outasF = 0;
$outasH = 0;

for ($j=0; $j<12; $j++) {
	if ($inds23[$periodIDs[$j]] == "") { 
	// cia sumuojam forecasta
		$sum1 += $inds25[$periodIDs[$j]]; 
	} else { 
	// cia istorinius
		
		if (($periodIDs[$j] == 380)) {//||($periodIDs[$j] == 381)) {
			$outasF += $inds25[$periodIDs[$j]]; 
			$outasH += $inds23[$periodIDs[$j]]; 
			
			$sum1 += $inds25[$periodIDs[$j]]; 
		} else {
			$sum1 += $inds23[$periodIDs[$j]]; 
		}
	}
	
	$sum2 += $inds25[$periodIDs[$j]];
}

//echo $periodIDs;

$rescale = $sum1/$sum2;
//testinimas
//$rescale = 1.25; 

//update the rescale table
$msg = "";

$up_query = "UPDATE ConsultingMQ.hr_rescale_test2 SET RescaleValue=".$rescale." WHERE ID=1";

$up_result = mysql_query($up_query);

if ($up_query) {$msg="Success";}
else {
	echo "DB_Error"; exit;
}

echo $msg;
//echo "rescale =".$rescale;
//echo "hist = ".$sum1."   fore = ".$sum2."| outasF = ".$outasF." outasH = ".$outasH;


?>