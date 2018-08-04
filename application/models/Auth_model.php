<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model {

    function __construct() {
        parent::__construct();
		log_message('INFO', 'Auth_model enter');
    }
	
	/*
	 * function get()
	 * this function is used for fetching user details with respect to email and password
	 * @param : Array
	 * @return : Array
	 */
    public function get($where = array()){
		$this->db->select('UM.user_id,  UM.full_name');
		$this->db->from('web_user_master UM');
		//$this->db->join('role_master RM','UM.role_master_id = RM.role_id');
		$db_where = "(UM.email = '".$where['email']."' AND UM.password ='". $where['pwd'] ."')";
		$this->db->where($db_where);
		$query = $this->db->get();

		return $query->result();
	}


	
	/*function isExistEmailPan()
	* This function is used to get email*/
	
	public function isExistEmailPan($post_email)
	{
		$this->db->select('UM.user_id, UM.role_master_id, UM.user_name, UM.user_status, UM.full_name');
		$this->db->from('web_user_master UM');
		$db_where = "(UM.user_name = '".$post_email."')";
		$this->db->where($db_where);
		$query = $this->db->get();
		//echo $this->db->last_query();

		return $query->row_array();
	}


	
	/*
	 * function getRolePermissionDetails()
	 * this function is used for fetching user role with Permission
	 * @param : Array
	 * @return : Array
	 */
	public function getRolePermissionDetails($where = array()){
		$this->db->select('RPR.*,PM.url_name');
		$this->db->from('role_permission_relation RPR');
		$this->db->join('permission_master PM','PM.permission_id = RPR.permission_id');
		if($where['role_id'] > 0){
			$this->db->where('RPR.role_id',$where['role_id']);
		}
		if($where['cont_id'] > 0){
			$this->db->where('RPR.cont_id',$where['cont_id']);
		}
		if($where['permission_id'] > 0){
			$this->db->where('RPR.permission_id',$where['permission_id']);
		}
		$query = $this->db->get();
		return $query->result();
	}
	
	/*
	 * function getControllerId()
	 * this function is used for fetching controller Id
	 * @param : Array
	 * @return : Array
	 */
	public function getControllerId($cont = ''){
		$this->db->select('PCM.cont_id');
		$this->db->from('permission_cont_master PCM');
		if($cont != ''){
			$this->db->where('PCM.url_cont_name',$cont);
		}
		$query = $this->db->get();
		$data = $query->result();
		return (!empty($data)?$data[0]->cont_id:0);
	}
	
	/*
	 * function getMethodId()
	 * this function is used for fetching permission id
	 * @param : Array
	 * @return : Array
	 */
	public function getPermissionId($method = ''){
		$this->db->select('PM.permission_id');
		$this->db->from('permission_master PM');
		if($method != ''){
			$this->db->where('PM.url_name',$method);
		}
		$query = $this->db->get();
		$data = $query->result();
		return (!empty($data)?$data[0]->permission_id:0);
	}
	
    
}