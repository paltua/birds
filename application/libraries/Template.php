<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template{
    private $_masterTemplate;
    private $_masterTemplateFolderPath;
    private $_region;
    private static $_CI;
    private $_layout;
    public $org_id;
    public $user_id;
    public $user_category;
    public $title ;
    public $module;
    public $controller;
    public $method;
    public $themeName;
    public $resourceName;
    public $data;
    public $themeNameAdmin;
    public $resourceNameAdmin;
    private $_masterTemplateFolderPathAdmin;
    #code
    public function __construct(){
        self::$_CI = &get_instance();
        $this->_setInitVal();
        $this->adminName = ADMIN_NAME;
    }
    
    private function _setInitVal(){
        $this->themeName = THEME.'/';
        $this->resourceName = 'public/'.$this->themeName;
        $this->masterTemplateFolderPath = $this->themeName.'templates/';

        $this->themeNameAdmin = 'admin/';
        $this->resourceNameAdmin = 'public/admin/';
        $this->masterTemplateFolderPathAdmin = $this->themeNameAdmin.'templates/';

        $this->user_id = self::$_CI->session->userdata('user_id');
        $this->user_category = self::$_CI->session->userdata('user_category');

        if($this->user_id!=''){
            $this->isLoggedIn = 'Y';
        }else{
            $this->isLoggedIn = 'N';
        }
        $this->user_name = trim(self::$_CI->session->userdata('full_name'));
        $this->module = self::$_CI->router->fetch_module();
        $this->controller = self::$_CI->router->fetch_class();
        $this->method = self::$_CI->router->fetch_method();
        
        $this->title = 'Home';
        $this->data = $this->_setData();
    }
    
    public function setLayout($layout = 'home'){
        $this->_layout = $layout;
    }
    
    public function setTitle($title = 'SIDBI'){
        $this->title = $title;
    }
    
    public function homeRender($filePath = '',$data = array()){
        $this->masterTemplate = $this->masterTemplateFolderPath.$this->_layout;
        $this->_region = $this->_getHomeRegion();
        $this->_region['content'] = $this->_content($filePath,$data);
        self::$_CI->load->view($this->masterTemplate,$this->_region);
    }

    public function homeAdminRender($filePath = '',$data = array()){
        $this->masterTemplate = $this->masterTemplateFolderPathAdmin.$this->_layout;
        $this->_region = $this->_getHomeAdminRegion();
        $this->_region['content'] = $this->_content($filePath,$data);
        self::$_CI->load->view($this->masterTemplate,$this->_region);
    }

    
    private function _getHomeRegion(){
        $rg = array();
        
        $rg['head'] = $this->_getHead();
        $rg['header'] = $this->_getHeader();
        // $rg['slider'] = $this->_getSlider();
        // $rg['headerLogin'] = $this->_getHeaderLogin();
        // $rg['footer'] = $this->_getFooter();
        // $rg['footerLogin'] = $this->_getFooterLogin();
        $rg['hide_content_area'] = 'no';
        return $rg;
    }

    private function _getHomeAdminRegion(){
        $rg = array();
        $rg['head'] = $this->_getAdminHead();
        $rg['header'] = $this->_getAdminHeader();
        $rg['menu'] = $this->_getAdminMenu();
        //$rg['slider'] = $this->_getSlider();
        //$rg['headerLogin'] = $this->_getAdminHeaderLogin();
        $rg['footer'] = $this->_getAdminFooter();
        $rg['footerLogin'] = $this->_getFooterAdminLogin();
        return $rg;
    }

    
    private function _getLoginRegion(){
        $rg = array();
        $rg['head'] = $this->_getHead();
        $rg['header'] = $this->_getHeader();
        //$rg['slider'] = $this->_getSlider();
        $rg['headerLogin'] = $this->_getHeaderLogin();
        $rg['footer'] = $this->_getFooter();
        $rg['footerLogin'] = $this->_getFooterLogin();
        return $rg;
    }

    private function _getLoginAdminRegion(){
        $rg = array();
        $rg['head'] = $this->_getAdminHead();
        $rg['header'] = $this->_getAdminHeader();
        //$rg['slider'] = $this->_getSlider();
        $rg['headerLogin'] = $this->_getAdminHeaderLogin();
        $rg['footer'] = $this->_getAdminFooter();
        $rg['footerLogin'] = $this->_getFooterAdminLogin();
        return $rg;
    }

    public function loginRender($filePath = '',$data = array()){
        $this->masterTemplate = $this->masterTemplateFolderPath.$this->_layout;
        $this->_region = $this->_getLoginRegion();
        $this->_region['content'] = $this->_content($filePath,$data);
        self::$_CI->load->view($this->masterTemplate,$this->_region);
    }

    public function loginAdminRender($filePath = '',$data = array()){
        $this->masterTemplate = $this->masterTemplateFolderPathAdmin.$this->_layout;
        $this->_region = $this->_getLoginAdminRegion();
        $this->_region['content'] = $this->_content($filePath,$data);
        self::$_CI->load->view($this->masterTemplate, $this->_region);
    }
    
    private function _getDashboardRegion(){
        $rg = array();
        $rg['head'] = $this->_getHead();
        $rg['header'] = $this->_getHeader();
        //$rg['slider'] = $this->_getSlider();
        $rg['headerLogin'] = $this->_getHeaderLogin();
        $rg['footer'] = $this->_getFooter();
        $rg['footerLogin'] = $this->_getFooterLogin();
        return $rg;
    }

    private function _getAdminDashboardRegion(){
        $rg = array();
        $rg['head'] = $this->_getHead();
        $rg['header'] = $this->_getAdminHeader();
        //$rg['slider'] = $this->_getSlider();
        $rg['headerLogin'] = $this->_getAdminHeaderLogin();
        $rg['footer'] = $this->_getAdminFooter();
        $rg['footerLogin'] = $this->_getFooterAdminLogin();
        return $rg;
    }

    public function dashboardRender($filePath = '',$data = array()){
        $this->masterTemplate = $this->masterTemplateFolderPath.$this->_layout;
        $this->_region = $this->_getHomeRegion();
        $this->_region['content'] = $this->_content($filePath,$data);
        self::$_CI->load->view($this->masterTemplate,$this->_region);
    }

    public function dashboardAdminRender($filePath = '',$data = array()){
        $this->masterTemplate = $this->masterTemplateFolderPath.$this->_layout;
        $this->_region = $this->_getHomeAdminRegion();
        $this->_region['content'] = $this->_content($filePath,$data);
        self::$_CI->load->view($this->masterTemplate,$this->_region);
    }
    
    private function _content($filePath = '',$data = array()){
        $content = '';
        if($filePath != ''){
            $retData = $data;
            $retData['resourceName'] = $this->resourceName;
            $content = self::$_CI->load->view($filePath,$retData,true);
        }else{
            $message = "File is blank.";
            $status_code = 500;
            show_error($message, $status_code, $heading = 'An Error Was Encountered');
        }
        return $content;
    }


    private function _getHead(){
        $this->data['title'] = $this->title;
        return self::$_CI->load->view($this->themeName.'common/head',$this->data,true);        
    }

    
    private function _getHeader(){
        $this->data['title'] = $this->title; 
        $this->data['menu'] = '';//$this->_menuArray();  
        // if($this->isLoggedIn=='Y'){
        //     $this->data['afterLogInMenu'] = $this->_getAfterLoginMenu();
        // }else{
        //     $this->data['afterLogInMenu'] = '';
        // }
        
        return self::$_CI->load->view($this->themeName.'common/header',$this->data,true);        
    }

    private function _getAdminHead(){
        $this->data['title'] = $this->title;
        return self::$_CI->load->view($this->themeNameAdmin.'common/head',$this->data,true);        
    }

    private function _getAdminHeader(){
        $this->data['title'] = $this->title;      
        return self::$_CI->load->view($this->themeNameAdmin.'common/header',$this->data,true);        
    }

    private function _getAfterLoginMenu(){
        $this->data['title'] = $this->title; 
        $this->data['afterLoginMenu'] = $this->_menuArrayAfterLogin();   
        return self::$_CI->load->view($this->themeNameAdmin.'common/afterLoginMenu',$this->data,true);        
    }

    


    private function _getSlider(){
        $sql = "SELECT 
                    COUNT(DISTINCT USR.unit_id) total,
                    SUM(RM.actual_annual_ghg_reduction_tco2e) emission_offset,
                    SUM(IF((RM.mv_status_yes_no = 'Yes'
                            AND (RM.working_status = 'Implemented'
                            OR RM.working_status = 'In Progress')),
                        1,
                        0)) ecm_count,
                    SUM(actual_total_energy_savings_mtoe) energy_savings_mtoe,
                    SUM(actual_monetary_savings_rs_lakh) monetary_savings_rs_lakh,
                    SM.sector_name
                FROM
                    unit_sector_relation USR
                        JOIN
                    recommendation_master RM ON RM.unit_id = USR.unit_id
                        LEFT JOIN
                    sector_master SM ON SM.sector_id = USR.sector_subsector_id
                GROUP BY USR.sector_subsector_id
                HAVING total >= 25
                ORDER BY SM.sector_name";
        $this->data['slideData'] = self::$_CI->tbl_generic_model->ExecuteQuery($sql);        
        return self::$_CI->load->view($this->themeName.'common/slider',$this->data,true);        
    }
    
    
    
    private function _getHeaderLogin(){
        $this->data['title'] = $this->title;
        return self::$_CI->load->view($this->themeName.'common/headerLogin',$this->data,true);
    }

    private function _getAdminMenu(){
        $this->data['title'] = $this->title;
        return self::$_CI->load->view($this->themeNameAdmin.'common/menu', $this->data, true);
    }



    private function _getAdminHeaderLogin(){
        $this->data['title'] = $this->title;
        return self::$_CI->load->view($this->themeNameAdmin.'common/headerLogin',$this->data,true);
    }
    
    
    
    private function _getFooter(){
        $this->data['footerMenu'] = $this->_footerMenuArray();
        $this->data['footerConnect'] = $this->_footerConnectArray();
        return self::$_CI->load->view($this->themeName.'common/footer',$this->data,true);        
    }

    private function _getAdminFooter(){
        $this->data['footerMenu'] = $this->_footerMenuAdminArray();
        $this->data['footerConnect'] = $this->_footerConnectArray();
        return self::$_CI->load->view($this->themeNameAdmin.'common/footer',$this->data,true);        
    }


    private function _footerMenuArray(){
        $menu = array(
                'home' => array('href' => base_url(),'title' => 'Home','name' => 'Home','active' => $this->controller == ''?'active':''),
                'about' => array('href' => base_url().'cms/page/about_us','title' => 'About us','name' => 'About us','active' => ($this->controller == 'page' && $this->method == 'about_us')?'active':''),
                'contact' => array('href' => base_url().'cms/page/contact_us','title' => 'Contact','name' => 'Contact Us','active' => ($this->controller == 'page' && $this->method == 'contact_us')?'active':''),
                'privacy' => array('href' => base_url().'cms/page/privacy_policy','title' => 'Privacy Policy','name' => 'Privacy Policy','active' => ($this->controller == 'page' && $this->method == 'privacy_policy')?'active':''),
            );
        return $menu;
    }

    private function _footerMenuAdminArray(){
        if((self::$_CI->session->userdata('aum_role_id')) == 1){
             $menu = array(
                'dasboard' => array('href' => base_url().$this->adminName.'/dashboard','title' => 'Dashboard','name' => 'Dashboard','active' => $this->controller == ''?'active':''),
                'User' => array('href' => base_url().$this->adminName.'/user','title' => 'User','name' => 'User','active' => $this->controller == ''?'active':''),
                'Query' => array('href' => base_url().$this->adminName.'/querycontent','title' => 'Querycontent','name' => 'Query','active' => $this->controller == ''?'active':''),
                'Logout' => array('href' => base_url().$this->adminName.'/auth/logout','title' => 'Logout','name' => 'Logout','active' => $this->controller == ''?'active':''),
                
            );
        }else{
            $menu = array(
                'dasboard' => array('href' => base_url().$this->adminName.'/dashboard','title' => 'Dashboard','name' => 'Dashboard','active' => $this->controller == ''?'active':''),
                'Query' => array('href' => base_url().$this->adminName.'/querycontent','title' => 'Querycontent','name' => 'Query','active' => $this->controller == ''?'active':''),
                'Logout' => array('href' => base_url().$this->adminName.'/auth/logout','title' => 'Logout','name' => 'Logout','active' => $this->controller == ''?'active':''),
                
            );
        }
        return $menu;
    }


    private function _footerConnectArray(){

    }

    
    private function _getFooterLogin(){
        return self::$_CI->load->view($this->themeName.'common/footerLogin',$this->data,true);
    }

    private function _getFooterAdminLogin(){
        return self::$_CI->load->view($this->themeNameAdmin.'common/footerLogin',$this->data,true);
    }

    
    private function _setData(){
        $data['user_id'] = $this->user_id;
        $data['module'] = $this->module;
        $data['class'] = $this->controller;
        $data['method'] = $this->method;
        //$data['org_name'] = $this->org_name;
        $data['user_name'] = $this->user_name;
        $data['user_category'] = $this->user_category;
        //$data['role_name'] = $this->role_name;
        //$data['user_role_id'] = $this->user_role_id;
        $data['resourceName'] = $this->resourceName;
        $data['resourceNameAdmin'] = $this->resourceNameAdmin;
        $data['isLoggedIn'] = $this->isLoggedIn;
        return $data;
    }
    
    public function getMessage($status = 'success', $msg = ''){
        $retMsg = '';
        if($status != '' && $msg != '' ){
            $retMsg .= '<div role="alert" class="alert alert-'.$status.'">';
            $retMsg .= '<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            $retMsg .= $msg;
            $retMsg .= '</div>';
        }
        return $retMsg;
    }

    
}
