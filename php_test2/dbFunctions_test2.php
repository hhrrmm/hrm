<?php

/*
** function to update scenario/baseline table with apropriate data 
*/
function updateTable($inds24, $inds25, $inds26, $timeIDs, $first_timeID, $last_timeID, $type, $scenID) { 
	//$type - baseline=0, scenario=1; if updating baseline, scenID can be any value
	for ($k = $first_timeID - 1; $k < $last_timeID; $k++) {
		$check_query = "";
		if ($type==0) {
			$check_query	=  "SELECT * FROM ConsultingMQ.hr_baseline_test2 "
							. "WHERE TimeID=".$timeIDs[$k]
							. " AND IndicatorID>20";
		}
		else if ($type == 1) {
			$check_query	=  "SELECT * FROM ConsultingMQ.hr_scenario_test2 WHERE ScenarioID='".$scenID
							. "' AND TimeID=".$timeIDs[$k]
							. " AND IndicatorID>20";
		}
	
		$check = mysql_query($check_query);
		
		$i24 = 0; $i25 = 0; $i26 = 0;
		
		if ($inds24[$k] == "") { $i24 = "NULL"; }
		else { $i24 = $inds24[$k];}
		
		if ($inds25[$k] == "") { $i25 = "NULL"; }
		else { $i25 = $inds25[$k];}
		
		if ($inds26[$k] == "") { $i26 = "NULL"; }
		else { $i26 = $inds26[$k];}
		
		if ($check) {
			$n_rows = mysql_num_rows($check);
			if ($n_rows == 0) {
				$upload_queryA = ""; $upload_queryB = ""; $upload_queryc = "";
				
				if($type == 0) {
					$upload_queryA  = "INSERT INTO ConsultingMQ.hr_baseline_test2 (TimeID, IndicatorID, DataValue)"
									." VALUES(".$timeIDs[$k].", 24, ".$i24.")";
					$upload_queryB  = "INSERT INTO ConsultingMQ.hr_baseline_test2 (TimeID, IndicatorID, DataValue)"
									." VALUES(".$timeIDs[$k].", 25, ".$i25.")";
					$upload_queryC  = "INSERT INTO ConsultingMQ.hr_baseline_test2 (TimeID, IndicatorID, DataValue)"
									." VALUES(".$timeIDs[$k].", 26, ".$i26.")";
				}
				else if ($type == 1) {
					$upload_queryA  = "INSERT INTO ConsultingMQ.hr_scenario_test2 (scenarioID, TimeID, IndicatorID, DataValue, sessionID)"
									." VALUES( '".$scenID."', ".$timeIDs[$k].", 24, ".$i24.", '".$SID."')";
				
					$upload_queryB  = "INSERT INTO ConsultingMQ.hr_scenario_test2 (scenarioID, TimeID, IndicatorID, DataValue, sessionID)"
									." VALUES( '".$scenID."', ".$timeIDs[$k].", 25, ".$i25[$k].", '".$SID."')";

					$upload_queryC  = "INSERT INTO ConsultingMQ.hr_scenario_test2 (scenarioID, TimeID, IndicatorID, DataValue, sessionID)"
									." VALUES( '".$scenID."', ".$timeIDs[$k].", 26, ".$i26[$k].", '".$SID."')";
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
					$update_queryA  = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$i24
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=24";
					$update_queryB  = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$i25
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=25";
					$update_queryC  = "UPDATE ConsultingMQ.hr_baseline_test2 SET DataValue=".$i26
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=26";
				}
				else if ($type == 1) {
					$update_queryA  = "UPDATE ConsultingMQ.hr_scenario_test2 SET DataValue=".$i24
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=24 AND scenarioID='".$scenID."'";
					$update_queryB  = "UPDATE ConsultingMQ.hr_scenario_test2 SET DataValue=".$i25
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=25 AND scenarioID='".$scenID."'";
					$update_queryC  = "UPDATE ConsultingMQ.hr_scenario_test2 SET DataValue=".$i26
									." WHERE TimeID=".$timeIDs[$k]." AND IndicatorID=26 AND scenarioID='".$scenID."'";
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