<?php

/**
 * Base controllers for different purposes
 * 	- MY_Controller: 
 * 	- Admin_Controller: 
 * 	- API_Controller: 
 */
class MY_Controller extends MX_Controller {
	
    public function __construct(){
        parent::__construct();
        if($this->config->item('maintenance_mode')){
            $data = array();
            echo $this->load->view('under_maintenance', $data, true);
            die();
        }
        //$this->_addBrowserDetails();
    }

    private function _addBrowserDetails(){
    	
    	/*---add ip details-----*/
		$table= 'web_ip_master';
        $fields ="*";
        $where = array("ip_address"=>$_SERVER['REMOTE_ADDR']);
        $ip_data = $this->tbl_generic_model->get($table,$fields,$where);

        if(!empty($ip_data)){
            $ip_exists = $ip_data[0]->ip_id;
        }else{
            $ip_exists = "N";
        }

        if($ip_exists=="N"){
            $ip_table = 'web_ip_master';
            $ip_datasection =  array('ip_address '=>$_SERVER['REMOTE_ADDR']);

            $insert_id_ip =  $this->tbl_generic_model->add($ip_table,$ip_datasection);
        }else{
            $insert_id_ip = $ip_exists;
        }

        /*---add cookie details-----*/
        $insert_id_cookie = '';
        if(isset($_COOKIE['sidbi-coockie'])){
            if($_COOKIE['sidbi-coockie']!=''&& $_COOKIE['sidbi-coockie'] !='NULL' && $_COOKIE['sidbi-coockie'] !=NULL  && !empty($_COOKIE['sidbi-coockie'])){
            	$table= 'cookie_master';
    	        $fields ="*";
    	        $where = array("cookie_no"=>$_COOKIE['sidbi-coockie'], 'ip_id'=>$insert_id_ip);
    	        $cookie_data = $this->tbl_generic_model->get($table,$fields,$where);

    	        if(!empty($cookie_data)){
    	            $cookie_exists = $cookie_data[0]->cookie_id;
    	        }else{
    	            $cookie_exists = "N";
    	        }

    	        if($cookie_exists=="N"){
    	            $cookie_table = 'cookie_master';
    	            $ip_datasection =  array('cookie_no '=>$_COOKIE['sidbi-coockie'], 'ip_id'=>$insert_id_ip);

    	            $insert_id_cookie =  $this->tbl_generic_model->add($cookie_table,$ip_datasection);
    	        }else{
    	            $insert_id_cookie = $cookie_exists;
    	        }
            }
        }      

        /*---add browser details-----*/
        if($insert_id_ip && $insert_id_cookie){
        	$bh_table = 'web_browsing_history';
	        $bh_datasection =  array('ip_id '=>$insert_id_ip, 
	        						'cookie_id'=>$insert_id_cookie,
	        						'HTTP_USER_AGENT'=>$_SERVER['HTTP_USER_AGENT'],
	        						'HTTP_REFERER'=>isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '',
	        						'REQUEST_URI'=>$_SERVER['REQUEST_URI'],
	        						'REQUEST_TIME'=>$_SERVER['REQUEST_TIME']);

	        $insert_id_bh =  $this->tbl_generic_model->add($bh_table,$bh_datasection);
        }
        
    }
    
}

// include base controllers
