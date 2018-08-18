<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends CI_Model { 

	function __construct() {
		parent::__construct();
		log_message('INFO', 'Comment_model enter');
	}
	
	
	public function user_details(){
		$this->db->select('*');
		$this->db->from('web_user_master');
		$query = $this->db->get();
		return $query->result();
	}

    public function listing(){
        $this->db->select('*');
        $this->db->from('comments CM');
        $this->db->join('user_master UM','UM.user_id=CM.user_id');
        $this->db->order_by('CM.com_id','DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function details($id){
        $this->db->from('web_user_master');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

	public function role_details(){
		$this->db->select('*');
		$this->db->from('admin_role_master');
		$query = $this->db->get();
		return $query->result();
	}

    function edit_user($data,$user_id){
        $this->db->where('user_id', $user_id);
        $this->db->update('web_user_master', $data);
        return true;
    }

	private function _get_datatables_query()
    {
         
        $this->db->from($this->table);
        /*$this->db->select('*');
        $this->db->from('web_user_master');
        $this->db->join('admin_role_master', 'admin_role_master.arm_id = web_user_master.role_id');*/
        
        $i = 0;
     
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function add_user($data){
    	$this->db->insert('web_user_master', $data); 
    }

    function user_delete($id){
        $this->db->where('user_id', $id);
        $this->db->delete('web_user_master'); 
    }

    function check_email($email){
        $this->db->where('email', $email);
        $this->db->from('web_user_master');
        $query = $this->db->get();
        return $query->num_rows();
    }
}