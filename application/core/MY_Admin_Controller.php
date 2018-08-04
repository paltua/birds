<?php

/**
 * Base controllers for different purposes
 * 	- MY_Controller: 
 * 	- Admin_Controller: 
 * 	- API_Controller: 
 */
class MY_Admin_Controller extends MX_Controller {
	
    public function __construct(){
        
    }
    
    public function verify_login($redirect_url = NULL){
		
	}
    
    public function verify_auth($group = 'members', $redirect_url = NULL)
	{
		
	}
}

// include base controllers
