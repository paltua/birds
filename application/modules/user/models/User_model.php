<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
    function __construct() {
        parent::__construct();
		log_message('INFO', 'User_model enter');
    }
    
    /*
	 * Use this function to get number of total user role under a specific client
	 * @param : Array
	 * @return : INT
	 */
    public function getAjax($where = array()){ 
		log_message('INFO', 'getAjax enter');
		try {
            $this->db->select('UM.user_id');
            $this->db->from('user_master UM');
            $this->db->where('UM.role_master_id', $where['role_master_id']);
            $query = $this->db->get();
            log_message('debug','getAjax Query: ' . $this->db->last_query());
        }catch(Exception $e){
            log_message('error','getAjax_err ' . $e->getMessage()); 
        }
        log_message('INFO', 'getAjax exit');
		return $query->num_rows();
    }
    
    /*
	 * Use this function to get user role details under a specific client
	 * @param : String,Array,Array,Array
	 * @return : object Array
	 */
    public function getAjaxSearch($searchData = '',$orderBy = array(),$limit = array(), $where = array())
    {
        log_message('INFO', 'getAjaxSearch enter');
        $data = array();
		try {
            $this->db->select('UM.user_id, UM.full_name, UM.user_name, UM.contact_no, UM.user_status, 
                                UM.created_date, (SELECT Count(WM.web_id) FROM website_master WM 
                                WHERE WM.web_user_master_id = UM.user_id) as webcounter');
            $this->db->from('web_user_master UM');
            $this->db->where('UM.role_master_id', $where['role_master_id']);
            if($searchData != ''){
				$like = "(`UM`.`user_name` LIKE '%".$searchData."%' ESCAPE '!'
							OR  `UM`.`contact_no` LIKE '%".$searchData."%' ESCAPE '!' 
                            OR  `UM`.`full_name` LIKE '%".$searchData."%' ESCAPE '!' 
                        )";
				$this->db->where($like);
            }
            if(! empty($orderBy)) $this->db->order_by($orderBy['col'],$orderBy['val']);
            if(! empty($limit)) $this->db->limit($limit['perpage'],$limit['start']);
            $query = $this->db->get();
			//echo $this->db->last_query(); die;
            log_message('debug','getAjaxSearch Query: ' . $this->db->last_query());
        }catch(Exception $e){
            log_message('error','getAjaxSearch_err ' . $e->getMessage());
        }
        log_message('INFO', 'getAjaxSearch exit');
        $data['rows'] = $query->result();

        // total no. records of employee for that organization
        $this->db->select('count(UM.user_id) as total_accounts');
        $this->db->from('web_user_master UM');
        $this->db->where('UM.role_master_id', $where['role_master_id']);
        if($searchData != ''){
           $like = "(`UM`.`user_name` LIKE '%".$searchData."%' ESCAPE '!'
                            OR  `UM`.`contact_no` LIKE '%".$searchData."%' ESCAPE '!' 
                            OR  `UM`.`full_name` LIKE '%".$searchData."%' ESCAPE '!' 
                        )";
            $this->db->where($like);
        }
        $query = $this->db->get();
        $data['total'] = $query->row()->total_accounts;
        return $data;
    }
    
    /*
	 * Use this function to get single user role details under a specific client
	 * @param : INT,INT
	 * @return : object Array
	 */
    public function getSingle($where = array()){
        log_message('INFO', 'getSingle enter');
        try{
            $this->db->select('UM.full_name, WM.domain_name, WM.website_title, WM.web_status, WM.created_date, WM.tracking_Id');
            $this->db->from('website_master WM');
			$this->db->join('web_user_master UM','WM.user_master_id = UM.user_id','left');
            $this->db->where('UM.user_id', $where['user_master_id']);
            $query = $this->db->get();
        }catch(Exception $e){
            log_message('error','getSingle_err ' . $e->getMessage());
        }
        log_message('INFO', 'getSingle exit');
        return $query->result();
    }
    

    /*
    * function add()
    * this function is used add a new entry to table
    * @param : data array
    * @return : user_org_rel_id number
    */
    function add($data) {
        $table = 'web_user_master';
        $this->db->insert($table, $data);
        return ($this->db->affected_rows() == '1') ? $this->db->insert_id() : FALSE;         
    }



    /*
    * function setOrganizationSettings($org_id, $fy, $setting_param,, $val)
    * This function is used to set organization specific settings based on FY
    * @param : $org_id, $fy, $setting_param, $val
    * @return : TRUE or FALSE
    */ 

    function setOrganizationSettings($org_id, $fy, $setting_param, $val)
    {
        $table = 'org_settings';
        $where = array('org_id' => $org_id, 'financial_year' => $fy, 'settings_constant' => $setting_param);
        $data = array('org_id' => $org_id, 'financial_year' => $fy, 'value' => $val, 'settings_constant' => $setting_param);

        $this->db->select('id');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();

        if(empty($query->row()->id)) {
            $this->db->insert($table, $data);
            if($this->db->affected_rows() > 0)
                return TRUE;
            else
                return FALSE;
        } else {
            $this->db->update($table, $data, $where);
            if($this->db->affected_rows() >= 0)
                return TRUE;  
            else
                return FALSE;
        }
    }


    /*
    * function getOrganizationSettings($org_id)
    * This function is used to get all organization specific settings
    * @param : $org_id, $fy, $setting_param, $val
    * @return : settings array based on FY
    */ 

    function getOrganizationSettings($org_id)
    {
        $table = 'org_settings';
        $where = array('org_id' => $org_id);
        $org_settings_arr = array();

        $this->db->select('settings_constant, financial_year, value');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();


        if($query->num_rows() > 0) {
            foreach($query->result_array() as $row) {
                $settings = $row['settings_constant'];
                $fy = $row['financial_year'];
                $value = $row['value'];
                $org_settings_arr[$settings][$fy] = $value;
            }
        }

        return $org_settings_arr;
    }


    /*
    * function getMaxPaysheetUpMonth($org_id, $sel_fy)
    * This function is used to get max month of paysheet upload
    * @param : $org_id
    * @return : month integer if found else 0
    */ 

    function getMaxPaysheetUpMonth($org_id, $sel_fy)
    {
        $query  = $this->db->select('pasheet_up_month')
                ->from('user_org_paysheet uop')
                ->join('user_org_relation uor', 'uor.user_org_rel_id = uop.user_org_id')
                ->where(array('uor.org_id' => $org_id, 'uop.pasheet_fy' => $sel_fy))
                ->group_by('uop.pasheet_up_month')
                ->order_by('uop.pasheet_up_month', 'ASC')
                ->limit(1)
                ->get();

        $result = $query->result();
        return (! empty($result)) ? array_pop($result)->pasheet_up_month : 0;
    }

     public function get($where = array()){
        $this->db->select('UM.*');
        $this->db->from('user_master UM');
        $this->db->where('UM.email',strtolower($where['email']));
        $this->db->where('UM.um_deleted','0');
        $query = $this->db->get();
        return $query->result();
    }
    
    
    
    public function getUserByUserId($user_id = 0){
        $this->db->select('UM.*');
        $this->db->from('web_user_master UM');
        $this->db->where('UM.user_id',$user_id);
        $query = $this->db->get();
        return $query->result();
    }
}
