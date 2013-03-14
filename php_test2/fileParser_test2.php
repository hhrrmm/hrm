<?php
include 'Classes/PHPExcel/IOFactory.php';
include 'MySQLConnectionDataProvider.php';

/*
** function for parsing uploaded data files and uploading the data into the DB
*/
function parseDataFile($path, $type, $SessionID) { //type - historic=0, scenario=1; 
													//set $SessionID to any value for historic - doesn't matter
	$inputFileName = $path;
	
	$sheetName="";
	
	if($type == 0) { $sheetName="for flex ACTUAL";}
	else if ($type == 1) {	$sheetName = "for flex SCENARIO";}

	$credentials = new MySQLConnectionDataProvider();

	$link = mysql_connect($credentials->address, $credentials->usr, $credentials->pw);
	mysql_select_db($credentials->DB);

//get data for input verification from the DB ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	
	$tIDs[]=""; $tNames[] = ""; $pTypes[] = "";
	
	$query  = "SELECT TimeID, TimeName, PeriodType FROM ConsultingMQ.hr_timeID_test2";

	$select_result = mysql_query($query);
	
	$i=0;
	if($select_result) {
		while ($line = mysql_fetch_array($select_result, MYSQL_ASSOC)) {
			$tIDs[$i] = $line['TimeID'];
			$tNames[$i] = $line['TimeName'];
			$pTypes[$i] = $line['PeriodType'];
			
			$i += 1;
		}//while line
	}//if select_result
	else { return "ErrorDB1"; }
	
	$iIDs[]=""; $iNames[] = ""; $iTypes[] = "";
	
	$query  = "SELECT IndicatorID, IndicatorName, IndicatorType FROM ConsultingMQ.hr_indicator_test2";

	$select_result = mysql_query($query);
	
	$i=0;
	if($select_result) {
		while ($line = mysql_fetch_array($select_result, MYSQL_ASSOC)) {
			$iIDs[$i] = $line['IndicatorID'];
			$iNames[$i] = $line['IndicatorName'];
			$iTypes[$i] = $line['IndicatorType'];

			$i += 1;
		}//while line
	}//if select_result
	else { return "ErrorDB2"; }
	
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
	
//set the right sheet as active. Otherwise, echo an error message ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$worksheets = $objPHPExcel->getSheetNames();

	$sheetExists = false;
	for ($i = 0; $i < count($worksheets); $i++) {
		if ($worksheets[$i] == $sheetName) {
			$sheetExists = true;
		}
	}

	$objWorksheet = null;
	if ($sheetExists) {
		$objWorksheet = $objPHPExcel->getSheetByName($sheetName);
	}
	else {
		return "ErrorSheet";
	}
	
//loop through file, try to parse it ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$time_IDs[] = "";
	$indicator_IDS[] = "";
	$values[] = "";
	$periodTypes[] = "";
	$indicatorTypes[] = "";
	$first_row = true;
	$column_names[] = "";
	$column_count = 0;
	$periodInd = -1;
	$parsing_problem = false;
	$entries = 0;

	foreach ($objWorksheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);

		if ($first_row) {
			$i = 0;
			foreach ($cellIterator as $cell) {
				$a = $cell->getValue();
				$b = $cell->getCalculatedValue();
				if ($a <> "") {
					if(substr($a, 0, 1) == "=") {$column_names[$i]=$b;}
					else { $column_names[$i]=$a; }
							
					if ($column_names[$i] == "Period"){$periodInd = $i;}
					$i += 1;
				}
			}// for each cell

			$column_count = $i;
			if ($column_count == 0 || $periodInd == -1) {$parsing_problem = true;}
				$first_row = false;
		} // if first row			
		else {
			if(!$parsing_problem) {
				$i = 0;
				$datavalues[] = "";
						
  				foreach ($cellIterator as $cell) {
					$a = $cell->getValue();
					if ($i <= $column_count) {
						if(substr($a, 0, 1) == "=") {
							$b = $cell->getCalculatedValue();
							$datavalues[$i]=$b;
						}
						else {$datavalues[$i] = $a;}
						$i += 1;
					}
  				}//for each cell

				$pKey = array_keys($tNames, $datavalues[$periodInd]);
				
				for($j = 0; $j < $column_count; $j++) {
				//check if the indicators and periods in the file correspond to the ones in the database
					if ($j <> $periodInd) {
						if($pKey) {
							$iKey = array_keys($iNames, $column_names[$j]);
							
							if($iKey) {
								$timeIDs[$entries] = $tIDs[$pKey[count($pKey)-1]];
								$indicatorIDs[$entries] = $iIDs[$iKey[0]];
								$values[$entries] = $datavalues[$j];
								$periodTypes[$entries] = $pTypes[count($pKey)-1];
								$indicatorTypes[$entries] = $iTypes[$iKey[0]];
									
								$entries += 1;
							}
							else {return "ErrorDB3";}
						}
						else {return "ErrorDB4";}
						
					} //if j <> periodInd
				}//for j
			} // if not parsing problem
		}//else
	} // for each row

// if there was a problem with parsing the file, quit; otherwise attempt to upload the data to DB ~
	if ($parsing_problem) {return "ErrorParsing";}
	
	else if ($type == 0) {
		for($k = 0; $k < $entries; $k++) {
			//only upload data for monthly periods, unless it's an annual indicator
			if ($periodTypes[$k] == 1 || ($periodTypes[$k] == 3 && $indicatorTypes[$k] == 3) ){
				$val = "";
				if ($values[$k] == "" || $values[$k] == "NA") {	$val="NULL";}
				else {$val = $values[$k];}

				$clean_query= "DELETE FROM ConsultingMQ.hr_historic_test2 WHERE "
							." TimeID=".$timeIDs[$k]
							." AND IndicatorID=".$indicatorIDs[$k];
							
				$insert_query	= "INSERT INTO ConsultingMQ.hr_historic_test2"
								."(TimeID, IndicatorID, DataValue) "
								."VALUES (".$timeIDs[$k].", ".$indicatorIDs[$k].", ".$val.")";
								
				$check = mysql_query($clean_query);
				
				if($check) {
					$insert = mysql_query($insert_query);
					if(!$insert) {return "ErrorDB5";}
				}
				else { return "ErrorDB6";}
					
			} // if periodType==1 or periodType==indicatorType==3
		}//for k < entries
	} //if type == 0 (historic file)
	
	else if ($type == 1) {
		$monthlyDataStart = 0;
		$annualDataStart = 0; //$quarterlyDataStart = 0;
				
		$timeStart_query = "SELECT mTimeID, PeriodType FROM `ConsultingMQ`.`hr0_test2`";
				
		$tsq_result = mysql_query($timeStart_query);
				
		if ($tsq_result) {
			while($line = mysql_fetch_array($tsq_result, MYSQL_ASSOC)) {
				if ($line['PeriodType'] == 1) {	$monthlyDataStart = $line['mTimeID'];}
				else if ($line['PeriodType'] == 3) {$annualDataStart = $line['PeriodType'];}
			}
		} //if tsq_result
		else { 	return "ErrorDB7"; }
		
		$clean_query = "";
		$insert_query = "";
		
		for($k = 0; $k < $entries; $k++) {
			$val = "";
			if ($values[$k] == "" || $values[$k] == "NA") {$val="NULL";}
			else {$val = $values[$k];}
			
			if ($k == 0) {
				$clean_query= "DELETE FROM ConsultingMQ.hr_input_test2 WHERE"
							." (SessionID='".$SessionID
							."' AND TimeID=".$timeIDs[$k]
							." AND IndicatorID=".$indicatorIDs[$k].") ";
							
				$insert_query = "INSERT INTO ConsultingMQ.hr_input_test2"
								."(SessionID, LastUpdated, TimeID, IndicatorID, DataValue) "
								."VALUES ('".$SessionID."', CURRENT_TIMESTAMP, "
								.$timeIDs[$k].", ".$indicatorIDs[$k].", ".$val.")";
			}
			else {
				$clean_query .= " OR (SessionID='".$SessionID
							 ."' AND TimeID=".$timeIDs[$k]
							 ." AND IndicatorID=".$indicatorIDs[$k].")";
							
				$insert_query .= ", ('".$SessionID."', CURRENT_TIMESTAMP, ".$timeIDs[$k].", ".$indicatorIDs[$k].", ".$val.")";
			}
		}// for k
		
		$check = mysql_query($clean_query);
				
		if($check) {
			$insert = mysql_query($insert_query);
			if(!$insert) {return "ErrorDB8";}
		}
		else { return "ErrorDB9";}
	} //if type == 1 (scenario file)

	//if all went well...
	return "Success";
	
}//function
?>