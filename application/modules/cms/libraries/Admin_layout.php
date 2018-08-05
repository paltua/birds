<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_layout{
    private $_masterTemplate;
    private $_masterTemplateFolderPath;
    private $_region;
    private static $CI;
    private $_layout;
    public $siteName;
    public $title;
    public $metaData;
    private $_site_config;
    private $_admin_url_con;
    #code
    public function __construct(){
        self::$CI = &get_instance();
        $this->_masterTemplateFolderPath = 'admin_template/';
        $this->_site_config = $this->configArray();
        $this->_admin_url_con = ADMIN_URL_CON;
    }
    
    public function setLayout($view = 'dashboard'){
        $this->_layout = $view;
    }
    
    public function render($filePath = '',$data = array()){
        $this->_masterTemplate = $this->_masterTemplateFolderPath.$this->_layout;
        $this->_region = $this->_getRegion();
        $this->_region['content'] = '';
        if($filePath != ''){
            $this->_region['content'] = $this->_content($filePath,$data);
        }
        $this->_region['body_class'] = $this->_body_class();
        self::$CI->load->view($this->_masterTemplate,$this->_region);
    }
    
    private function _getRegion(){
        $rg = array();
        $rg['header'] = $this->_header();
        $rg['header_login'] = $this->_header_login();
        $rg['navbar'] = $this->_navbar();
        //$rg['main_header'] = $this->_main_header();
        $rg['menu'] = $this->_menu();
        $rg['footer'] = $this->_footer();
        $rg['footer_login'] = $this->_footer_login();
        return $rg;
    }
    
    private function _content($filePath = '',$data = array()){
        $content = '';
        if($filePath != ''){
            $content = self::$CI->load->view($filePath,$data,true);
        }else{
            $message = "File is blank.";
            $status_code = 500;
            show_error($message, $status_code, $heading = 'An Error Was Encountered');
        }
        return $content;
    }
    
    private function _body_class(){
        return $this->_site_config['adminlte']['webmaster']['skin'];
    }
    
    private function _header(){
        $data['page_title'] = $this->title;
        $data['meta_data'] = $this->_site_config['meta'];
        $data['stylesheets'] = $this->_site_config['stylesheets'];
        $data['scripts'] = $this->_site_config['scripts'];
        return self::$CI->load->view('admin_common/header',$data,true);
    }
    
    private function _header_login(){
        $data['page_title'] = $this->title;
        //$data['meta_data'] = $this->_site_config['meta'];
        //$data['stylesheets'] = $this->_site_config['stylesheets'];
        //$data['scripts'] = $this->_site_config['scripts'];
        return self::$CI->load->view('admin_common/header_login',$data,true);
    }
    
    private function _main_header(){
        $data = array();
        $data['name'] = self::$CI->session->userdata('name');
        $data['logout'] = base_url($this->_admin_url_con.'/auth/logout');
        $data['account'] = '';
        return self::$CI->load->view('admin_common/main_header',$data,true);
    }
    
    private function _menu(){
        $data['menu'] = $this->_site_config['menu'];
        $data['current_uri'] = current_full_url();
        $data['ctrler'] = self::$CI->router->fetch_class();
        $data['name'] = self::$CI->session->userdata('name');
        $data['baseUrl'] = base_url().$this->_admin_url_con.'/';
        return self::$CI->load->view('admin_common/menu',$data,true);
    }
    
    private function _footer(){
        $data['scripts'] = $this->_site_config['scripts'];
        return self::$CI->load->view('admin_common/footer',$data,true);
    }
    
    private function _footer_login(){
        //$data['scripts'] = $this->_site_config['scripts'];
        $data = array();
        return self::$CI->load->view('admin_common/footer_login',$data,true);
    }
    
    private function _navbar(){
        //$data['scripts'] = $this->_site_config['scripts'];
        $data = array();
        $data['baseUrl'] = base_url().$this->_admin_url_con.'/';
        return self::$CI->load->view('admin_common/navbar',$data,true);
    }
    
    
    public function set_title($title = 'Admin Panel'){
        if($title != ''){
            $this->title = $title;
        }else{
            $this->title = $thi->_site_config['title'];
        }
    }
    /* This is not used anymore */
    public function configArray(){
        $config = array(

            // Site name
            'name' => 'Admin Panel',
        
            // Default page title
            // (set empty then MY_Controller will automatically generate one based on controller / action)
            'title' => 'Admin Panel || Login',
        
            // Default meta data (name => content)
            'meta'	=> array(
                'author'		=> 'E-cube',
                'description'	=> 'E-cube Admin Panel'
            ),
        
            // Default scripts to embed at page head / end
            'scripts' => array(
                'head'	=> array(
                    'admin_theme/dist/adminlte.min.js',
                    'admin_theme/dist/admin.min.js'
                ),
                'foot'	=> array(
                ),
            ),
        
            // Default stylesheets to embed at page head
            'stylesheets' => array(
                'screen' => array(
                    'admin_theme/dist/adminlte.min.css',
                    'admin_theme/dist/admin.min.css'
                )
            ),
        
            // Multilingual settings (set empty array to disable this)
            'multilingual' => array(),
        
            // AdminLTE settings
            'adminlte' => array(
                'webmaster'	=> array('skin' => 'skin-red'),
                'admin'		=> array('skin' => 'skin-purple'),
                'manager'	=> array('skin' => 'skin-black'),
                'staff'		=> array('skin' => 'skin-blue')
            ),
        
            // Menu items which support icon fonts, e.g. Font Awesome
            // (or directly update view file: /application/modules/admin/views/_partials/sidemenu.php)
            'menu' => array(
                'home' => array(
                    'groups'	=> array('admin', 'manager', 'staff'),
                    'name'		=> 'Home',
                    'url'		=> 'home',
                    'icon'		=> 'fa fa-home',
                    'module'    => ADMIN_URL_CON,
                ),
                'client' => array(
                    'groups'	=> array('admin'),
                    'name'		=> 'Manage Accounts',
                    'url'		=> 'dashboard',
                    'icon'		=> 'fa fa-industry',
                    'module'    => ADMIN_URL_CON,
                    'children'  => array(
                        'List'			=> 'client',
                        /*'Create'		=> 'client/index/add',*/
                        'Create'		=> 'client/create',
                    )
                ),
                
                'logout' => array(
                    'groups'	=> array('admin', 'manager', 'staff'),
                    'name'		=> 'Sign Out',
                    'url'		=> 'auth/logout',
                    'icon'		=> 'fa fa-sign-out',
                    'module'    => ADMIN_URL_CON,
                )
            ),
        
            // default page when redirect non-logged-in user
            'login_url' => ADMIN_URL_CON.'/auth/login',
        
            // Useful links to display at bottom of sidemenu (e.g. to pages outside Admin Panel)
            'useful_links' => array(
                
            ),
        
            // For debug purpose (available only when ENVIRONMENT = 'development')
            'debug' => array(
                'view_data'		=> FALSE,	// whether to display MY_Controller's mViewData at page end
                'profiler'		=> FALSE,	// whether to display CodeIgniter's profiler at page end
            ),
        );
        return $config;
    }
    
    
    public function getMessage($status = 0, $msg = ''){
        $retMsg = '';
        if($status == 2 ){
            $retMsg .= '<div role="alert" class="alert alert-danger">';
            $retMsg .= '<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            $retMsg .= $msg;
            $retMsg .= '</div>';
        }elseif($status == 1){
            $retMsg .= '<div role="alert" class="alert alert-success">';
            $retMsg .= '<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            $retMsg .= $msg;
            $retMsg .= '</div>';
        }
        return $retMsg;
    }
    public function pagination($url = '', $total_rows = 0, $per_page = 10, $uri_segment = 4){
        self::$CI->load->library('pagination');
        $config['base_url'] = $url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $uri_segment;
        $config['num_links'] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="paginate_button" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous">';
        $config['first_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="paginate_button next" aria-controls="dataTables-example" tabindex="0">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = '<li class="paginate_button previous" aria-controls="dataTables-example" tabindex="0">';
        $config['prev_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="paginate_button" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="paginate_button active" aria-controls="dataTables-example" tabindex="0"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="paginate_button" aria-controls="dataTables-example" tabindex="0">';
        $config['num_tag_close'] = '</li>';
        self::$CI->pagination->initialize($config);
        return self::$CI->pagination->create_links();
    }
    
}
