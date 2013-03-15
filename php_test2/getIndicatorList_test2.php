<?php

/*
** Used to retrieve the indicator list
*/

$link = mysql_connect('192.168.44.200', 'cmq', 'cmq1');
mysql_select_db('ConsultingMQ');

$query = "SELECT IndicatorID, IndicatorName, UnitName, IOType, IndicatorType, IsRatio, AggregationType, TimeName AS histEnd "
		. " FROM ConsultingMQ.hr_indicator_test2 INNER JOIN ConsultingMQ.hr_timeID_test2 "
		. " ON ConsultingMQ.hr_indicator_test2.historyEnd=ConsultingMQ.hr_timeID_test2.TimeID "
		. " WHERE (IndicatorID <= 23)
			ORDER BY IndicatorID";

$result = mysql_query($query);

$answer = "<entries>";

if ($result) {
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ( $row['IndicatorID'] < 23 ) {
			$answer .="<entry>"
					. "<IndicatorID>".$row['IndicatorID']."</IndicatorID>"
					. "<IndicatorName>".$row['IndicatorName']."</IndicatorName>"
					. "<UnitName>".$row['UnitName']."</UnitName>"
					. "<IOType>".$row['IOType']."</IOType>"
					. "<IndicatorType>".$row['IndicatorType']."</IndicatorType>"
					. "<IsRatio>".$row['IsRatio']."</IsRatio>"
					. "<AggType>".$row['AggregationType']."</AggType>"
					. "<histEnd>".$row['histEnd']."</histEnd>"
					. "</entry>";
		}
		else {
			$answer .= "<entry>"
					. "<IndicatorID>23</IndicatorID>"
					. "<IndicatorName>Number of hospital beds</IndicatorName>"
					. "<UnitName></UnitName>"
					. "<IOType>1</IOType>"
					. "<IndicatorType>1</IndicatorType>"
					. "<AggType>1</AggType>"
					. "<histEnd>".$row['histEnd']."</histEnd>"
					. "</entry>";
					
			$answer .= "<entry>"
					. "<IndicatorID>24</IndicatorID>"
					. "<IndicatorName>Sales value of hospital beds</IndicatorName>"
					. "<UnitName>USD</UnitName>"
					. "<IOType>1</IOType>"
					. "<IndicatorType>1</IndicatorType>"
					. "<AggType>1</AggType>"
					. "<histEnd>".$row['histEnd']."</histEnd>"
					. "</entry>";
					
			$answer .= "<entry>"
					. "<IndicatorID>25</IndicatorID>"
					. "<IndicatorName>Total sales value of hospital beds in the United States</IndicatorName>"
					. "<UnitName></UnitName>"
					. "<IOType>1</IOType>"
					. "<IndicatorType>1</IndicatorType>"
					. "<AggType>1</AggType>"
					. "<histEnd>".$row['histEnd']."</histEnd>"
					. "</entry>";
		}
	}
}
else {
	echo "DB_Error";
	exit;
}

$answer .= "</entries>";

echo $answer;

?>