<?php

/*
** Functions used for building output XMLs
*/

function formMonthlyDataXML($period, $i1, $i2, $i3, $i4, $i5, $i6, $i7, $i8, $i9, $i10, $i11, $i12, $i13, $i14, $i15, $i16) {
	$xmltext= "<period>".$period."</period>"
			. "<ind1>".$i1."</ind1>"
			. "<ind2>".$i2."</ind2>"
			. "<ind3>".$i3."</ind3>"
			. "<ind4>".$i4."</ind4>"
			. "<ind5>".$i5."</ind5>"
			. "<ind6>".$i6."</ind6>"
			. "<ind7>".$i7."</ind7>";
			
	if ($i13 == "") {
		if ($i15 == "") {
			$xmltext .= "<ind8></ind8>";
		}
		else {
			//$xmltext .= "<ind8>".round($i15, 0)."</ind8>";
			$xmltext .= "<ind8>".$i15."</ind8>";
		}
	}
	else {
		//$xmltext .= "<ind8>".round($i13, 0)."</ind8>";
		$xmltext .= "<ind8>".$i13."</ind8>";
	}
			
	$xmltext.= "<ind9>".$i9."</ind9>"
			. "<ind10>".$i10."</ind10>"
			. "<ind11>".$i11."</ind11>"
			. "<ind12>".$i12."</ind12>";
			
	if ($i13 == "") {
		if ($i15 == "") {
			$xmltext .= "<ind13></ind13>";
		}
		else {
		//	$xmltext .= "<ind13>".round($i15, 0)."</ind13>";
			$xmltext .= "<ind13>".$i15."</ind13>";
		}
	}
	else {
		//$xmltext .= "<ind13>".round($i13, 0)."</ind13>";
		$xmltext .= "<ind13>".$i13."</ind13>";
	}
	
	if ($i14 == "") {
		if ($i16 == "") {
			$xmltext.= "<ind14></ind14>"
					."<ind15></ind15>";
		}
		else {
			$xmltext.= "<ind14>".$i16."</ind14>"
					."<ind15>".($i16/$i12)."</ind15>";
		}
	}
	else {
		$xmltext .= "<ind14>".$i14."</ind14>";
				 
		if ($i12==0 || $i12=="") {$xmltext .= "<ind15></ind15>";}
		else {$xmltext .= "<ind15>".$i14/$i12."</ind15>";}
	}

	return $xmltext;
}

function formAnnualDataXML($annPeriod, $i2, $i3, $i4, $i5, $i7, $i10) {
	$xmltext= "<annualInd2>".$i2."</annualInd2>"
			. "<annualInd3>".$i3."</annualInd3>"
			. "<annualInd4>".$i4."</annualInd4>"
			. "<annualInd5>".$i5."</annualInd5>"
			. "<annualInd7>".$i7."</annualInd7>"
			. "<annualInd10>".$i10."</annualInd10>"
			. "<annualPeriod>".$annPeriod."</annualPeriod>";
					
	return $xmltext;
}

?>