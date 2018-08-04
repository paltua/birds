<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ion_user_auth_admin{
    private $_user_id;
    private $_role_id;
    private static $CI;
	public $module;
    public $controller;
    public $method;
    
    public function __construct(){
        self::$CI = &get_instance();
        self::$CI->load->model('admin/auth_model');
        $this->_user_id = self::$CI->session->userdata('aum_id');
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
        	$this->_setSession($userMasterData);
        }
        return $retData;
    }

    public function checkActiveStatus($userMasterData = array(),$userMasterForm = array()){
        $retData['status'] ='';
        $retData['msg'] = '';
        if(!password_verify($userMasterForm['password'],$userMasterData[0]->password)){
        	$retData['status'] = 'danger';
        	$retData['msg'] = 'Invalid email id or password';
        }elseif($userMasterData[0]->status == 'inactive'){
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
		self::$CI->session->set_userdata('aum_id', $userData[0]->aum_id);
		self::$CI->session->set_userdata('aum_name', $userData[0]->name);
		self::$CI->session->set_userdata('aum_email', $userData[0]->email);
        //self::$CI->session->set_userdata('aum_role_id', $userData[0]->role_id);
        
    }
	
    /*
	 * function isLogIn()
	 * This function is used to check login status in login/forgot password page
	 * @param : 
	 * @return : Void
	 */
    public function isLogIn(){
        if($this->_user_id != '' && $this->_user_id > 0) {
        	redirect(base_url(ADMIN_NAME.'/dashboard'));
        }
    }
    
    /*
	 * function isLoggedIn()
	 * This function is used check login status in others page
	 * @param : 
	 * @return : Void
	 */
    public function isLoggedIn(){
        if($this->_user_id == '' || $this->_user_id <= 0){
            redirect(base_url(ADMIN_NAME));
        }
    }
    
    /*
	 * function permissionControl()
	 * This function is used to set permission for the user Type
	 * @param : null
	 * @return : Void
	 */
    /*public function permissionControl(){
        //1=super admin;2=Account admin;3=Client Admin
        if($this->_role_id > 3){
            $retData = array();
            $where['role_id'] = $this->_role_id;
            $where['cont_id'] = $this->_getControllerId();
			if($where['cont_id'] == 0){
				$this->_accessRedirect();
			}
            $where['permission_id'] = $this->_getMethodId();
            $perData = self::$CI->auth_model->getRolePermissionDetails($where);
            if(count($perData) <= 0){
                $this->_accessRedirect();
            }
        }
		
		return true;
    }*/
	
	/*
	 * function _accessRedirect()
	 * This function is used to set error message
	 * @param : null
	 * @return : Void
	 */
	/*private function _accessRedirect(){
		self::$CI->session->set_flashdata('status','2');
		self::$CI->session->set_flashdata('msg','Permission denied.');
		redirect(base_url('account/auth/login'));
	}*/
	
    /*
	 * function setPermission()
	 * This function is used to set permission for the user Type in listing page
	 * @param : null
	 * @return : Void
	 */
    /*public function getPermission(){
        //1=super admin;2=Account admin;3=Client Admin
		$retData = $this->_setDefaultPermission();
        if($this->_role_id > 3){
            $where['role_id'] = $this->_role_id;
            $where['cont_id'] = $this->_getControllerId();
            $where['permission_id'] = $this->_getMethodId();
            $perData = self::$CI->auth_model->getRolePermissionDetails($where);
            if(count($perData) > 0){
                foreach($perData as $v){
					if($v->url_name == 'all'){
						$retData = $this->_setAllPermission();
					}elseif($v->url_name != 'all'){
						if($v->url_name != ''){ $retData->{$v->url_name} = 1;}
					}
				}
            }
        }else{
			$retData = $this->_setAllPermission();
		}
		return $retData;
    }*/
	
	/*
	 * function _setDefaultPermission()
	 * This function is used to set default permission as no permission
	 * @param : null
	 * @return : Void
	 */
	/*private function _setDefaultPermission(){
		$data = self::$CI->Tbl_generic_model->get('permission_master','*');
		$retData = new stdClass;
		if(count($data) > 0){
			foreach($data as $v){
				$retData->{$v->url_name} = 0;
			}
		}
		return $retData;
	}*/
	
	/*
	 * function _setAllPermission()
	 * This function is used to set permission for all
	 * @param : null
	 * @return : Void
	 */
	/*private function _setAllPermission(){
		$data = self::$CI->Tbl_generic_model->get('permission_master','*');
		$retData = new stdClass;
		if(count($data) > 0){
			foreach($data as $v){
				$retData->{$v->url_name} = 1;
			}
		}
		return $retData;
	}*/
	
	/*
	 * function _getControllerId()
	 * This function is used to get controllerId
	 * @param : null
	 * @return : Void
	 */
    /*private function _getControllerId(){
        $cont_name = trim($this->controller);
		return self::$CI->auth_model->getControllerId($cont_name);
    }*/
	
	/*
	 * function _getMethodId()
	 * This function is used to get methodId
	 * @param : null
	 * @return : Void
	 */
    /*private function _getMethodId(){
        $method_name = trim($this->method);
		return self::$CI->auth_model->getPermissionId($method_name);
    }*/
}