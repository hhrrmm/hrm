<?php

/*
** Used to clean up the database tables
*/
include 'MySQLConnectionDataProvider.php';

$SID = $_GET['sessionID'];

$credentials = new MySQLConnectionDataProvider();

$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
mysql_select_db($credentials->DB);

//Select current session data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$select_query1 = "SELECT SessionID, TimeID, IndicatorID FROM hr_input_test2 WHERE SessionID='".$SID."'";

$result1 = mysql_query($select_query1);

if ($result1) {
	while ($rows = mysql_fetch_array($result1, MYSQL_ASSOC)) {
//update the LastUpdated field for this session so that it doesn't get scrapped ~~~~~~~~~~~~~~~~~~~
		$upd_query  = "UPDATE hr_input_test2 "
					. " SET LastUpdated=CURRENT_TIMESTAMP "
					. " WHERE SessionID='".$SID."' "
					. " AND TimeID=".$rows['TimeID']
					. " AND IndicatorID=".$rows['IndicatorID'];
					
		mysql_query($upd_query);
	}//while rows

//Select sessionIDs of sessions that haven't been updated in more than 1000s ~~~~~~~~~~~~~~~~~~~~
	$select_query2 = "SELECT DISTINCT SessionID AS SID FROM hr_input_test2 WHERE LastUpdated < (CURRENT_TIMESTAMP - 5000)";
	
	$result2 =  mysql_query($select_query2);
	
	if ($result2) {
		$n_rows = mysql_num_rows($result2);
	
		if ($n_rows > 0) {
			while ($lines = mysql_fetch_array($result2, MYSQL_ASSOC)) {		
//delete old data from scenario table ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				$del_scn_q  = "DELETE FROM hr_scenario_test2 "
							. " WHERE sessionID='".$lines['SID']."' "
							. " AND scenarioID<>'a'"
							. " AND TimeID>0"
							. " AND IndicatorID>0";
						
				$a = mysql_query($del_scn_q);
				
				/*if ($a) {
					echo $a;
				}*/
			
//delete old data from input table ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				$del_inp_q  = "DELETE FROM hr_input_test2 "
							. " WHERE sessionID='".$lines['SID']."' "
							. " AND TimeID>0"
							. " AND IndicatorID>0"; 
			
				$b = mysql_query($del_inp_q);
				
				/*if ($b) {
					echo $b;
				}*/
			}//while
		}
	}
	else {
		exit;
	}
}
else {
	exit;
}

//if all goes well...
echo "Success";
?>