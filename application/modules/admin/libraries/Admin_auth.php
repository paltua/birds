<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_auth{
    private $_user_id;
    private $_user_name;
    private $_user_email;
    private static $CI;
    private $admin_url_con;
    
    #code
    public function __construct(){
        self::$CI = &get_instance();
        $this->_user_id = self::$CI->session->userdata('admin_id');
        $this->_user_name = self::$CI->session->userdata('name');
        $this->_user_email = self::$CI->session->userdata('email');
        $this->admin_url_con = ADMIN_URL_CON;
    }
    
    public function login_verify(){
        if($this->_user_id == ''){
            redirect(base_url($this->admin_url_con.'/auth/login'));
        }else{
            redirect(base_url($this->admin_url_con.'/home'));
        }
        return true;
    }
    
    public function is_logged(){
        if($this->_user_id != ''){
            redirect(base_url($this->admin_url_con.'/home'));
        }
        return true;
    }
    
    public function is_logged_in(){
        if($this->_user_id == ''){
            redirect(base_url($this->admin_url_con.'/auth/login'));
        }
        return true;
    }
    
   
}
