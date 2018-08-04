<?php
function ToAssociativeArray($str,$delimOuter,$delimInner){
	$result = array();
	
	foreach (explode($delimOuter, $str) as $couple) {
		list ($key, $val) = explode($delimInner, $couple);
		$result[$key] = $val;
	}
	
	return $result;	
}


function pr($value=array())
{
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}


function roundOffArray($element=array()){
	//pr($element); //Show Before 
	foreach(array_keys($element) AS $ek){ //Get key of each value so it can be set back to itself 
	$element[$ek] = round($element[$ek]); //Do rounding 		
	} 
	return $element;
	//print_r($element);
}



function round_10($in)
{
    //return ceil($in / 10) * 10;
    return round($in, -1);
}

/**********GENERATE RANDOM PASSWORD****************/

function randStringGen($len){
    $result = "";
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $charArray = str_split($chars);

        for($i = 0; $i < $len; $i++){
            $randItem = array_rand($charArray);
            $result .= "".$charArray[$randItem];
        }

    return $result;
}


function getStateName($state_id)
{	
	$CI = & get_instance();
    $CI->load->model('tbl_generic_model');
    $table 		= 'web_state';
  	$fields		= 'StateName';
  	$where 		= array('StateID' => $state_id);

  	$state = $CI->tbl_generic_model->get($table, $fields, $where, 0, 0, true);
  	return $state->StateName;
}


function getCountryName($country_id)
{	
	$CI = & get_instance();
    $CI->load->model('tbl_generic_model');
    $table 		= 'country';
  	$fields		= 'country_name';
  	$where 		= array('country_id' => $country_id);

  	$country = $CI->tbl_generic_model->get($table, $fields, $where, 0, 0, true);
  	return $country->country_name;
}





function diffinmonths ($startdate, $enddate){
	$timeEnd = strtotime($enddate);
	$timeStart = strtotime($startdate);
	// Adding current month + all months in each passed year
	$numMonths = 1 + (date("Y",$timeEnd)-date("Y",$timeStart))*12;
	// Add/subtract month difference
	$numMonths += date("m",$timeEnd)-date("m",$timeStart);

	return $numMonths;
}

function clean_string($string) {

	$string = str_replace([' ', '(', ')', '/'], '_', trim($string)); // Replaces all spaces with underscore.

	$string = strtolower( preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.
	
	$last_char = substr($string, -1); // returns the last char

	if($last_char == '_'){
		$string = substr($string, 0, -1);
	}

	$string = str_replace(['___','__'], '_', trim($string)); // Replaces more than one underscore with underscore.
	return $string;
}







?>