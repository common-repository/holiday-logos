<?php
function hdl_thanksgiving_holiday($year, $format ='m/d/Y'){
	$datetext = 'fourth thursday of november '.$year;
	return date($format, strtotime($datetext));
}

function hdl_easter_hoiday($year, $format ='m/d/Y'){
	return date($format, easter_date(2016));
}

function hdl_martin_luther_king_holiday($year, $format ='m/d/Y'){
	$datetext = 'third monday of january '.$year;
	return date($format, strtotime($datetext));
}

function hdl_presidents_holiday($year, $format ='m/d/Y'){
	$datetext = 'third monday of february '.$year;
	return date($format, strtotime($datetext));
}

function hdl_memorial_holiday($year, $format ='m/d/Y'){
	$datetext = 'last monday of may '.$year;
	return date($format, strtotime($datetext));
}

function hdl_labor_holiday($year, $format ='m/d/Y'){
	$datetext = 'first monday of september '.$year;
	return date($format, strtotime($datetext));
}

function hdl_columbus_holiday($year, $format ='m/d/Y'){
	$datetext = 'second monday of october '.$year;
	return date($format, strtotime($datetext));
}

function hdl_list_floating_holidays(){
	$current_year = date('Y');
	$list = array(
                 "President's Day"=>hdl_presidents_holiday($current_year),
                "Easter"=>hdl_easter_hoiday($current_year),
                "Memorial Day"=>hdl_memorial_holiday($current_year),
                "Labor Day"=>hdl_labor_holiday($current_year),
                "Columbus Day"=>hdl_columbus_holiday($current_year),
                "Thanksgiving"=>hdl_thanksgiving_holiday($current_year)
            );

	return $list;

}
?>	

