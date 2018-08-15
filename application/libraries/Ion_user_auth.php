<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ion_user_auth{
    private $_user_id;
    private $_full_name;
    private $_user_category;
    private $_user_status;
    private static $CI;
	public $module;
    public $controller;
    public $method;
    
    public function __construct(){
        self::$CI = &get_instance();
        //self::$CI->load->model('account/user_model');
        $this->_user_id = self::$CI->session->userdata('user_id');
        $this->_full_name = self::$CI->session->userdata('name');
        $this->_user_category = self::$CI->session->userdata('user_category');
        $this->_user_status = self::$CI->session->userdata('user_status');
		$this->module = self::$CI->router->fetch_module();
        $this->controller = self::$CI->router->fetch_class();
        $this->method = self::$CI->router->fetch_method();
    }
    /*
	 * function activation()
	 * This function is used to login
	 * @param : user Data(Array)
	 * @return : Array
	 */
    public function activation($userMasterForm = array()){
        $userMasterData = self::$CI->user_model->get($userMasterForm);//checking with email & password
        $retData['status'] = '';
        $retData['msg'] = '';
        $retData['category'] = '';
        if(count($userMasterData) > 0){
            $data = $this->checkActiveStatus($userMasterData,$userMasterForm);
            $retData['status'] = $data['status'];
            $retData['msg'] = $data['msg'];
        }else{
            $retData['status'] = 'danger';
            $retData['msg'] = "Invalid email id or password.";
        }
        if($retData['status'] != 'danger'){
        	$retData['category'] = $userMasterData[0]->user_category;
            $this->_setSession($userMasterData);
        }
        return $retData;
    }
    /*
	 * function checkActiveStatus()
	 * This function is used to check status of the user
	 * @param : User ID(INT)
	 * @return : Array
	 */
    public function checkActiveStatus($userMasterData = array(),$userMasterForm = array()){
        $retData['status'] = '';
        $retData['msg'] = '';
        if(!password_verify($userMasterForm['password'],$userMasterData[0]->password)){
        	$retData['status'] = 'danger';
        	$retData['msg'] = 'Invalid email id or password';
        }elseif($userMasterData[0]->user_status == 'Inactive'){
        	$retData['status'] = 'danger';
        	$retData['msg'] = 'Your account is Inactive';
        }
        return $retData;
    }
    /*
	 * function _setSession()
	 * This function is used to set session
	 * @param : User details(Array)
	 * @return : Void
	 */
    private function _setSession($userData = array()){
        self::$CI->session->set_userdata('user_id', $userData[0]->user_id);
        self::$CI->session->set_userdata('email', $userData[0]->email);
        self::$CI->session->set_userdata('full_name', $userData[0]->name);
        self::$CI->session->set_userdata('user_category', '');
        self::$CI->session->set_userdata('user_status', $userData[0]->um_status);
        
    }
	/*
	 * function _setSessionOfFinancialYearID()
	 * This function is used to set session of Financial Year
	 * @param : User details(Array)
	 * @return : Void
	 */

    /*private function _setSessionOfFinancialYearID(){
		$sql = "SELECT * from financial_year where start_year = YEAR(NOW())";
		$data = self::$CI->Tbl_generic_model->ExecuteQuery($sql);
		if($data){
			self::$CI->session->set_userdata('financial_year_id', $data[0]->financial_year_id);
		}
	}*/
	
    /*
	 * function isLogIn()
	 * This function is used to check login status in login/forgot password page
	 * @param : 
	 * @return : Void
	 */
    public function isLogIn(){

    	$cc = $this->checkExpiry();
		if($cc==1){
			redirect(base_url('account/auth/expiry'));
		}

        if($this->_user_id != '' && $this->_user_id > 0) {
        	redirect(base_url('account/dashboard'));
        }
    }
    
    /*
	 * function isLoggedIn()
	 * This function is used check login status in others page
	 * @param : 
	 * @return : Void
	 */
    public function isLoggedIn(){

    	$cc = $this->checkExpiry();
		if($cc==1){
			redirect(base_url('account/auth/expiry'));
		}


        if($this->_user_id == '' || $this->_user_id <= 0){
            redirect(base_url('account/auth/login'));
        }


    }

    private function _dateAdd($startDateTime = '', $insertValMin = ''){
        $date = new DateTime($startDateTime);
        $date->add(new DateInterval($insertValMin));
        return $date->format('Y-m-d H:i:s');
    }

    public function checkExpiry(){
        //$startDate = '2017-11-15';
        $startDate = '2018-03-31';
        $insertValMin = 'P180DT00H00M00S';
        $endDate = $this->_dateAdd($startDate, $insertValMin);
        $currentDate = date('Y-m-d');
        if($currentDate > $endDate){
            //die('Your subscription is expired. Please contact to Umesh Ji to renew your subscription.');
            return 1;
        }else{
            return 2;
        }

    }

    public function isExpired(){
        $cc = $this->checkExpiry();
        if($cc == 2){
            redirect(base_url('account/auth/login'));
        }
    }
   
}