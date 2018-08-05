<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ion_user_auth{
    private $_user_id;
    private $_user_type_id;
    private static $CI;
	public $module;
    public $controller;
    public $method;
    
    public function __construct(){
        self::$CI = &get_instance();
        self::$CI->load->model('account/auth_model');
        $this->_user_id = self::$CI->session->userdata('user_id');
        $this->_user_type_id = self::$CI->session->userdata('user_type_id');
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
        $userMasterData = self::$CI->auth_model->get($userMasterForm);//checking with email & password
        $retData['status'] = 0;
        $retData['msg'] = '';
        if(count($userMasterData) > 0){
            $data = $this->checkActiveStatus($userMasterData,$userMasterForm);
            $retData['status'] = $data['status'];
            $retData['msg'] = $data['msg'];
        }else{
            $retData['status'] = 2;
            $retData['msg'] = "Invalid email id or password.";
        }
        if($retData['status'] == 0){
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
        $retData['status'] = 0;
        $retData['msg'] = '';
        if(!password_verify($userMasterForm['pwd'],$userMasterData[0]->pwd)){
        	$retData['status'] = 2;
        	$retData['msg'] = 'Invalid email id or password.';
        }elseif($userMasterData[0]->user_status == 'inactive'){
        	$retData['status'] = 2;
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
        self::$CI->session->set_userdata('user_name', $userData[0]->name);
        self::$CI->session->set_userdata('user_email', $userData[0]->email);
        self::$CI->session->set_userdata('user_type_id', $userData[0]->user_type_id);
    }

   
    
    /*
	 * function isLogIn()
	 * This function is used to check login status in login/forgot password page
	 * @param : 
	 * @return : Void
	 */
    public function isLogIn(){
        if($this->_user_id != '' && $this->_user_id > 0){
            $this->autoLogOut();
        	if($this->_user_type_id == 1){
        		redirect(base_url('admin/dashboard'));
        	}else{
        		redirect(base_url('account/dashboard'));
        	}
            
        }
    }

    
    
    /*
	 * function isLoggedIn()
	 * This function is used to check login status in others page also checking for mobile
	 * @param : 
	 * @return : Void
	 */
    public function isLoggedIn(){
        if($this->_user_id == '' || $this->_user_id <= 0){
            redirect(base_url('account/auth/login'));
        }else{
            $this->autoLogOut();
        }
        /*self::$CI->load->library('mobile_detect');
        if(self::$CI->mobile_detect->checkAll()){
        	$class = self::$CI->router->fetch_class();
        	$method = self::$CI->router->fetch_method();
        	if($class == 'dashboard' &&  $method == 'others'){
        		return true;
        	}else{
        		redirect(base_url('account/dashboard/others'));
        	}        	
        }*/
    }

    /*
     * function autoLogOut()
     * This function is used to logout automatically if this is inactive 
     * @param : 
     * @return : Void
     */
    
	public function autoLogOut(){
        $status = 0;
        $user_id = $this->_user_id;
        $userMasterData = self::$CI->auth_model->getUserByUserId($user_id);
        if(count($userMasterData) > 0){
            if($userMasterData[0]->user_status == 'inactive'){
                $status = 1;
            }
        }else{
            $status = 1;
        }
        if($status == 1){
            self::$CI->session->sess_destroy();
            redirect(base_url('account/auth/login'));
        }
        return true;
    }
	
	/*
	 * function _getControllerId()
	 * This function is used to get controllerId
	 * @param : null
	 * @return : Void
	 */
    private function _getControllerId(){
        $cont_name = trim($this->controller);
		return self::$CI->auth_model->getControllerId($cont_name);
    }
	
	/*
	 * function _getMethodId()
	 * This function is used to get methodId
	 * @param : null
	 * @return : Void
	 */
    private function _getMethodId(){
        $method_name = trim($this->method);
		return self::$CI->auth_model->getPermissionId($method_name);
    }

}