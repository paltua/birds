<?php

function getEmsData($data = ''){	
	$retData = '';
    if($data == 'end_date_time'){
        $retData = 'Date & Time';
    }else{
        $retData = str_replace('data_', '', $data);
    }
  	return $retData;
}



?>