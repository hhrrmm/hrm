<?php

/*
** function to update scenario/baseline table with apropriate data 
*/
function updateTable($inds14, $inds15, $inds16, $timeIDs, $first_timeID, $last_timeID, $type, $scenID) { 
	//$type - baseline=0, scenario=1; if updating baseline, scenID can be any value
	for ($k = $first_timeID - 1; $k < $last_timeID; $k++) {
		$check_query = "";
		if ($type==0) {
			$check_query	=  "SELECT * FROM ConsultingMQ.hr_baseline_test2 "
							. "WHERE TimeID=".$timeIDs[$k]
							. " AND IndicatorID>12";
		}
		else if ($type == 1) {
			$check_query	=  "SELECT * FROM ConsultingMQ.hr_scenario_test2 WHERE ScenarioID='".$scenID
							. "' AND TimeID=".$timeIDs[$k]
							. " AND IndicatorID>13";
		}
	
		$check = mysql_query($check_query);
		
		$i14 = 0; $i15 = 0; $i16 = 0;
		
		if ($inds14[$k] == "") { $i14 = "NULL"; }
		else { $i14 = $inds14[$k];}
		
		if ($inds15[$k] == "") { $i15 = "NULL"; }
		else { $i15 = $inds15[$k];}
		
		if ($inds16[$k] == "") { $i16 = "NULL"; }
		else { $i16 = $inds16[$k];}
		
		if ($check) {
			$n_rows = mysql_num_rows($check);
			if ($n_rows == 0) {
				$upload_queryA = ""; $upload_queryB = ""; $upload_queryc = "";
				
				if($type == 0) {
					$upload_queryA  = "INSERT INTO ConsultingMQ.hr_baseline_test2 (TimeID, IndicatorID, DataValue)"
									." VALUES(".$timeIDs[$k].", 14, ".$i14.")";
					$upload_queryB  = "INSERT INTO ConsultingMQ.hr_baseline_test2 (TimeID, IndicatorID, DataValue)"
									." VALUES(".$timeIDs[$k].", 15, ".$i15.")";
					$upload_queryC  = "INSERT INTO ConsultingMQ.hr_baseline_test2 (TimeID, IndicatorID, DataValue)"
									." VALUES(".$timeIDs[$k].", 16, ".$i16.")";
				}
				else if ($type == 1) {
					$upload_queryA  = "INSERT INTO ConsultingMQ.hr_scenario_test2 (scenarioID, TimeID, IndicatorID, DataValue, sessionID)"
									." VALUES( '".$scenID."', ".$timeIDs[$k].", 14, ".$i14.", '".$SID."')";
				
					$upload_queryB  = "INSERT INTO ConsultingMQ.hr_scenario_test2 (scenarioID, TimeID, IndicatorID, DataValue, sessionID)"
									." VALUES( '".$scenID."', ".$timeIDs[$k].", 15, ".$i15[$k].", '".$SID."')";

					$upload_queryC  = "INSERT INTO ConsultingMQ.hr_scenario_test2 (scenarioID, TimeID, IndicatorID, DataValue, sessionID)"
									." VALUES( '".$scenID."', ".$timeIDs[$k].", 16, ".$i16[$k].", '".$SID."')";
				}
				
				$a = mysql_query($upload_queryA);
				$b = mysql_query($upload_queryB);
				$c = mysql_query($upload_queryC);
				if (!$a) {return "DB_Error in dbF11";}
				if (!$b) {return "DB_Error in dbF12";}
				if (!$c) {return "DB_Error in dbF13";}
				
			}//if n_rows = 0
			else {
				$update_queryA = ""; $update_queryB = ""; $update_queryC = "";
				
				if($type == 0) {
					$update_queryA  = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$i14
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=14";
					$update_queryB  = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$i15
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=15";
					$update_queryC  = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$i16
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=16";
				}
				else if ($type == 1) {
					$update_queryA  = "UPDATE ConsultingMQ.hr_scenario_test2 SET DataValue=".$i14
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=14 AND scenarioID='".$scenID."'";
					$update_queryB  = "UPDATE ConsultingMQ.hr_scenario_test2 SET DataValue=".$i15
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=15 AND scenarioID='".$scenID."'";
					$update_queryC  = "UPDATE ConsultingMQ.hr_scenario_test2 SET DataValue=".$i16
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=16 AND scenarioID='".$scenID."'";
				}
				
				$uA = mysql_query($update_queryA);
				$uB = mysql_query($update_queryB);
				$uC = mysql_query($update_queryC);
				
				if (!$uA) {return "DB_Error in dbF1";}
				if (!$uB) {return  $update_queryB; } //"DB_Error in dbF2";}
				if (!$uC) {return "DB_Error in dbF3";}
			}//if n_rows != 0
		}//if check
		else {return "DB_Error in dbFunctions";}
	}//for k
	
	return "Success";
}

?>