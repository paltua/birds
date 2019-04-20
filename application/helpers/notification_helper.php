<?php


function pr($value=array()){
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}





function addPageDetails(){
  $CI = & get_instance();
  $data['url'] =  current_url();
  $data['email'] = $CI->session->userdata('email');
  $data['ip'] = $CI->input->ip_address();
  $CI->tbl_generic_model->add('page_hit_status', $data);
}


function getLanguageArr(){
  $retArr = array('en' => 'English', 'ben' => 'Bengali', 'hi' => 'Hindi');
  //$retArr = array('en' => 'English');
  return $retArr;
}

function getLanguageArrAnimalMaster(){
  //$retArr = array('en' => 'English', 'ben' => 'Bengali', 'hi' => 'Hindi');
  $retArr = array('en' => 'English');
  return $retArr;
}

function getViewDate($date = "now", $time_sec=0,$time_min=0,$time_hour=0){
  $showDate = date("F j, Y", strtotime($date));
  if($time_sec <= 60){
    $showDate = $time_sec.' seconds ago';
  }elseif ($time_min <= 60) {
    if($time_min == 1){
      $showDate = $time_min.' minute ago';
    }else{
      $showDate = $time_min.' minutes ago';
    }
  }elseif ($time_hour <= 24) {
    if($time_hour == 1){
      $showDate = $time_hour.' hour ago';
    }else{
      $showDate = $time_hour.' hours ago';
    }
  }
  return $showDate;
}

function getViewDateBCK($date = "now"){
    $showDate = date("F j, Y", strtotime($date));
    $today = 'now';
    $datetime1 = new DateTime($today);
    $datetime2 = new DateTime($date);
    /*echo "<pre>";
    print_r($datetime1->diff($datetime2));*/
    if($datetime1->diff($datetime2)->format('%d') == 0){
      if($datetime1->diff($datetime2)->format('%h') != 0){
        if($datetime1->diff($datetime2)->format('%h') == 1){
          $showDate = $datetime1->diff($datetime2)->format('%h').' hour ago';
        }else{
          $showDate = $datetime1->diff($datetime2)->format('%h').' hours ago';
        }
      }elseif($datetime1->diff($datetime2)->format('%i') != 0){
        if($datetime1->diff($datetime2)->format('%i') == 1){
          $showDate = $datetime1->diff($datetime2)->format('%i').' minute ago';
        }else{
          $showDate = $datetime1->diff($datetime2)->format('%i').' minutes ago';
        }
      }else{
        $showDate = $datetime1->diff($datetime2)->format('%s').' seconds ago';
      }
    }
    return $showDate;
}

function showLocation($country_name = '', $state_name = '', $city_name = ''){
    $retString = $country_name;
    if($state_name != ''){
        $retString .= ', '.$state_name;
    }

    if($city_name != ''){
        $retString .= ', '.$city_name;
    }
    return $retString;
}




?>