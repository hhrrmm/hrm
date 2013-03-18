<?php

/*
** Used to perform forecast calculations for several different scripts
*/

Class ForecastCalculator_test2 {
	//arrays for indicator data
	private $timeIDs; 
	private $timeNames;
	
	private $inds1;  private $inds2;
	private $inds3;  private $inds4;
	private $inds5;  private $inds6;
	private $inds7;  private $inds8;
	private $inds9;  private $inds10;
	private $inds21; private $inds22;
	private $inds23; private $inds24;
	private $inds25; private $inds26;

	//constructor
	public function __construct($tIDs, $tNames, $i1, $i2, $i3, $i4, $i5, $i6, $i7, $i8, 
								$i9, $i10, $i17, $i18,  $i21, $i22, $i23, $i24, $i25, $i26) 
	{
		$timeIDs = $tIDs; $this->timeNames = $tNames;
		
		$this->inds1  = $i1;  $this->inds2  = $i2;  $this->inds3  = $i3;  $this->inds4  = $i4;
		$this->inds5  = $i5;  $this->inds6  = $i6;  $this->inds7  = $i7;  $this->inds8  = $i8;
		$this->inds9  = $i9;  $this->inds10 = $i10; $this->inds17 = $i17; $this->inds18 = $i18; 
		$this->inds21 = $i21; $this->inds22 = $i22;
		$this->inds23 = $i23; $this->inds24 = $i24; $this->inds25 = $i25; $this->inds26 = $i26;
	}//constructor

	public function calculateForecasts($startID, $endID, $adjust=true, $applyRescale=true) {
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
			return "DB_Error";
		}
		
		if ($adjust) {
			// retrieve baseline share data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			$baselineShares[] = "";

			$bShares_query  = "SELECT DataValue "
							. " FROM ConsultingMQ.hr1_baseline_scenario_test2, ConsultingMQ.hr_timeID_test2 "
							. " WHERE ConsultingMQ.hr1_baseline_scenario_test2.TimeID = ConsultingMQ.hr_timeID_test2.TimeID "
							. " AND PeriodType=1 AND IndicatorID=22 AND ConsultingMQ.hr_timeID_test2.TimeID>=".$startID
							. " ORDER BY ConsultingMQ.hr_timeID_test2.TimeID";
							
			$bShares_result = mysql_query($bShares_query);

			if ($bShares_result) {
				$i = $startID-1;
				while ($v = mysql_fetch_array($bShares_result, MYSQL_ASSOC)) {
					$baselineShares[$i] = $v['DataValue'];
					$i += 1;
				}
			}
			else {
				return "DB_Error";
			}
		}//if adjust

		if($applyRescale) {
			//get rescale value ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			$rescale_query = "SELECT RescaleValue FROM ConsultingMQ.hr_rescale_test2 WHERE ID=1";
			$rescale = 0.998614802334848;//0.998614802334848; //0.990729507991746;					   

				
			$resc_result = mysql_query($rescale_query);
				
			if ($resc_result) {
				while ($r = mysql_fetch_array($resc_result, MYSQL_ASSOC)) {
					$rescale = $r['RescaleValue'];
				}
			}
			else {
				return "DB_Error";
			}
		}//if applyRescale
		
//calculate scenario outputs ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	

//calculation coefficients

		$intercept  = 5.09094078328746; //5.310481021; 
		$coefA5     = 1.99155002929997; //3.299078287; 
		$coefB6     = 3.77469387405107; //3.371723152; 
		$coefC7     = 1.1860139802366; //-1.221606389;
		$coefD8     = 0.331090906793777; //2.508731058; 
		$coefE9     = 0.332977428828686; //2.720947649; 
		$coefF18    = 0.00221584541758416; //1.326854777; 
		$coefG17    = 0.175223084144976; //0;
		$linet		= -0.0887666835237632;
		
		for ($j = $startID-12; $j < $endID; $j++) {
			if ($this->inds23[$j] == "") {$this->inds24[$j] = "";}
			else {$this->inds24[$j] = $this->inds23[$j]*$this->inds21[$j];}			
			
			$A5 = $coefA5 * (log($this->inds5[$j-36]) - log($this->inds5[$j-48])); // lag 36, diff 12
			
			if ($this->inds10[$j-10] == 0 || $this->inds10[$j-10] == "")  {
				$B6 = "";
			} else {			
				$B6 = $coefB6 * (log($this->inds6[$j-10]/ $this->inds10[$j-10]) - log($this->inds6[$j-22]/$this->inds10[$j-22])); 
			}; // lag 10, diff 12
			
			if ($this->inds10[$j-24] == 0 || $this->inds10[$j-24] == "" || $this->inds1[$j-24] == 0 || $this->inds1[$j-24] == "" ||
				$this->inds10[$j-36] == 0 || $this->inds10[$j-36] == "" || $this->inds1[$j-36] == ""|| $this->inds1[$j-36] == 0)   {
				$C7 = "";
			} else 	{
				$C7 = $coefC7 * (log($this->inds7[$j-24]/($this->inds10[$j-24]*$this->inds1[$j-24]*10)) - log($this->inds7[$j-36]/($this->inds10[$j-36]*$this->inds1[$j-36]*10))); 
			}; // lag 24, diff 12
			
			$D8 = $coefD8 * (log( $this->inds8[$j-10] + $this->inds8[$j-11] + $this->inds8[$j-12])); // lag 10, diff agg 3
			
			if ($this->inds10[$j-24] == 0 || $this->inds10[$j-24] == "" || $this->inds1[$j-24] == 0 || $this->inds1[$j-24] == "" ||
				$this->inds10[$j-36] == 0 || $this->inds10[$j-36] == "" || $this->inds1[$j-36] == ""|| $this->inds1[$j-36] == 0)   {
				$E9 = "";
			} else {
				$E9 = $coefE9 * (log($this->inds9[$j-24]/($this->inds10[$j-24]*$this->inds1[$j-24]*10)) - log($this->inds9[$j-36]/($this->inds10[$j-36]*$this->inds1[$j-36]*10))); 
				};
			// lag 16, diff 12			
			$F18 = $coefF18 * ($this->inds18[$j-12] - $this->inds18[$j-24]) ; // lag 12, diff 12
			$G17 = $coefG17 * (log($this->inds17[$j-12]) - log($this->inds17[$j-24])); // lag 12, diff 12								
				
//apply seasonal modifiers where needed
			$modifierID = "";
			$modifier = 0;
			if ( (substr($this->timeNames[$j], 0, 4) == 2003 && substr($this->timeNames[$j], 5, strlen($this->timeNames[$j])-5) > 10) 
				|| (substr($this->timeNames[$j], 0, 4) > 2003) ) {
				$modifierID .= "2M".substr($this->timeNames[$j], 5, strlen($this->timeNames[$j])-5);
			}
			else {
				$modifierID .= "1M".substr($this->timeNames[$j], 5, strlen($this->timeNames[$j])-5);
			}
				
			if (substr($this->timeNames[$j], 5, strlen($this->timeNames[$j])-5) == "12") {
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
			
//apply rescale if needed
			if($applyRescale) {
				$this->inds25[$j] = exp($intercept + $A5 + $B6 + $C7 + $D8 + $E9 + $F18 + $G17 + $modifier + $linet) * $rescale;
			}
			else {
				$this->inds25[$j] = exp($intercept + $A5 + $B6 + $C7 + $D8 + $E9 + $F18 + $G17 + $modifier + $linet);
			}
			
//adjust according to shares if needed

			if ($adjust) {
				if($j >= $startID-1) {
					if ($this->inds22[$j] <> $baselineShares[$j]) {
						if ($this->inds22[$j] == "" || $this->inds22[$j] == 0) {$this->inds25[$j] = "";}
						else {
							$this->inds25[$j] = $this->inds25[$j]*(1/$baselineShares[$j])*$this->inds22[$j];
						}
					}
				}
			}
				
			$this->inds26[$j] = $this->inds25[$j]*$this->inds21[$j];
				
			if ($this->inds23[$j] == "") {
				$this->inds8[$j] = $this->inds25[$j];
			}	else {
				$this->inds8[$j] = $this->inds23[$j];
			}
		}//for j
	
		return  $this->inds25; //"success";//$this->inds25;
	}//calculateForecasts
}

?>