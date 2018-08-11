<?php


function pr($value=array()){
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}



function getHeaderNotification(){	
	$CI = & get_instance();
    $CI->load->model('account/notification_model');
    $air = $CI->notification_model->getAirMeterError();
    $steam = $CI->notification_model->getSteamMeterError();
    $ems = $CI->notification_model->getEmsMeterError();
    $ems_cpp = $CI->notification_model->getEmsCppMeterError();
    $data_log = $CI->notification_model->getDataLogMeterError();
    $retData['li']['air'] = array('name' => 'Air','data' => $air);
    $retData['li']['steam'] = array('name' => 'Steam','data' => $steam);
    $retData['li']['ems'] = array('name' => 'EMS','data' => $ems);
    $retData['li']['ems_cpp'] = array('name' => 'EMS CPP','data' => $ems_cpp);
    $retData['li']['data_log'] = array('name' => 'Data Log','data' => $data_log);
    $retData['total'] = $air[0]->total_meter + $steam[0]->total_meter + $ems[0]->total_meter + $ems_cpp[0]->total_meter + $data_log[0]->total_meter;

  	return $retData;
}

function addPageDetails(){
  $CI = & get_instance();
  $data['url'] =  current_url();
  $data['email'] = $CI->session->userdata('email');
  $data['ip'] = $CI->input->ip_address();
  $CI->tbl_generic_model->add('page_hit_status', $data);
}


function getLanguageArr(){
  //$retArr = array('en' => 'English', 'ben' => 'Bengali', 'hi' => 'Hindi');
  $retArr = array('en' => 'English');
  return $retArr;
}




?>