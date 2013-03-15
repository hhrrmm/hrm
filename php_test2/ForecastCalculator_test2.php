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
								$i9, $i10, $i21, $i22, $i23, $i24, $i25, $i26) 
	{
		$timeIDs = $tIDs; $this->timeNames = $tNames;
		
		$this->inds1  = $i1;  $this->inds2  = $i2;  $this->inds3  = $i3;  $this->inds4  = $i4;
		$this->inds5  = $i5;  $this->inds6  = $i6;  $this->inds7  = $i7;  $this->inds8  = $i8;
		$this->inds9  = $i9;  $this->inds10 = $i10; $this->inds21 = $i21; $this->inds22 = $i22;
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
							. " AND PeriodType=1 AND IndicatorID=12 AND ConsultingMQ.hr_timeID_test2.TimeID>=".$startID
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
		for ($j = $startID-12; $j < $endID; $j++) {
			if ($this->inds23[$j] == "") {$this->inds24[$j] = "";}
			else {$this->inds24[$j] = $this->inds23[$j]*$this->inds21[$j];}
			
			
			$A = $coefA*($this->inds2[$j-12] - $this->inds2[$j-24]);
			$B = $coefB*($this->inds3[$j-12] - $this->inds3[$j-24]);
			$C = $coefC*($this->inds4[$j-24]);
			$D = $coefD*(log($this->inds5[$j-36]) - log($this->inds5[$j-48]));
				
			if ($this->inds10[$j-10] == 0 || $this->inds10[$j-10] == "" || $this->inds10[$j-22] == 0 || $this->inds10[$j-22] =="") {
				$E = "";
			}
			else {
				$E = $coefE*(log($this->inds6[$j-10]/$this->inds10[$j-10])-log($this->inds6[$j-22]/$this->inds10[$j-22]));
			}
				
			if ($this->inds10[$j-24] == 0 || $this->inds10[$j-24] == "" || $this->inds10[$j-36] == 0 || $this->inds10[$j-36] == ""
				|| $this->inds1[$j-24] == 0 || $this->inds1[$j-24] == "" || $this->inds1[$j-36] == 0 || $this->inds1[$j-36] == "") {
				$F = "";
			}
			else {
				$F = $coefF*(log($this->inds7[$j-24]/$this->inds1[$j-24]/100/$this->inds10[$j-24]) - log($this->inds7[$j-36]/$this->inds1[$j-36]/100/$this->inds10[$j-36]));
			}
			$G = $coefG*log($this->inds8[$j-5]);
			$H = $coefH*log($this->inds8[$j-10]+$this->inds8[$j-11]+$this->inds8[$j-12]);
				
			if ($this->inds1[$j-16] ==0 || $this->inds1[$j-16] =="" || $this->inds10[$j-16] == 0 || $this->inds10[$j-16] == ""
				|| $this->inds1[$j-28] ==0 || $this->inds1[$j-28] =="" || $this->inds10[$j-28] == 0 || $this->inds10[$j-28] == "") {
				$I = "";
			}
			else {
				$I = $coefI*(log($this->inds9[$j-16]/$this->inds1[$j-16]/100/$this->inds10[$j-16]) - log($this->inds9[$j-28]/$this->inds1[$j-28]/100/$this->inds10[$j-28]));
			}
				
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
				$this->inds25[$j] = exp($intercept + $A + $B + $C + $D + $E + $F + $G + $H + $I + $modifier)*$rescale;
			}
			else {
				$this->inds25[$j] = exp($intercept + $A + $B + $C + $D + $E + $F + $G + $H + $I + $modifier);
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