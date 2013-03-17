<?php

/*
** Functions used for building output XMLs
*/

function formMonthlyDataXML($period, $i1, $i2, $i3, $i4, $i5, $i6, $i7, $i8, $i9, $i10, $i17, $i18, $i21, $i22, $i23, $i24, $i25, $i26) {
	$xmltext= "<period>".$period."</period>"
			. "<ind1>".$i1."</ind1>"
			. "<ind2>".$i2."</ind2>"
			. "<ind3>".$i3."</ind3>"
			. "<ind4>".$i4."</ind4>"
			. "<ind5>".$i5."</ind5>"
			. "<ind6>".$i6."</ind6>"
			. "<ind7>".$i7."</ind7>";
			
	if ($i23 == "") {
		if ($i25 == "") {
			$xmltext .= "<ind8></ind8>";
		}
		else {
			//$xmltext .= "<ind8>".round($i25, 0)."</ind8>";
			$xmltext .= "<ind8>".$i25."</ind8>";
		}
	}
	else {
		//$xmltext .= "<ind8>".round($i23, 0)."</ind8>";
		$xmltext .= "<ind8>".$i23."</ind8>";
	}
			
	$xmltext.= "<ind9>".$i9."</ind9>"
			. "<ind10>".$i10."</ind10>"
			. "<ind17>".$i17."</ind17>"
			. "<ind18>".$i18."</ind18>"
			. "<ind21>".$i21."</ind21>"
			. "<ind22>".$i22."</ind22>";
			
	if ($i23 == "") {
		if ($i25 == "") {
			$xmltext .= "<ind23></ind23>";
		}
		else {
		//	$xmltext .= "<ind13>".round($i25, 0)."</ind13>";
			$xmltext .= "<ind23>".$i25."</ind23>";
		}
	}
	else {
		//$xmltext .= "<ind13>".round($i23, 0)."</ind13>";
		$xmltext .= "<ind23>".$i23."</ind23>";
	}
	
	if ($i24 == "") {
		if ($i26 == "") {
			$xmltext.= "<ind24></ind24>"
					."<ind25></ind25>";
		}
		else {
			$xmltext.= "<ind24>".$i26."</ind24>"
					."<ind25>".($i26/$i22)."</ind25>";
		}
	}
	else {
		$xmltext .= "<ind24>".$i24."</ind24>";
				 
		if ($i22==0 || $i22=="") {$xmltext .= "<ind25></ind25>";}
		else {$xmltext .= "<ind25>".$i24/$i22."</ind25>";}
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