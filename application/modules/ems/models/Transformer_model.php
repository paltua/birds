<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transformer_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
		log_message('INFO', 'Transformer_model enter');
	}

	public function getTransformerDetails($p_device_id = 0){
    	$sql = "SELECT 
				ND.*,
				MD1.device_name p_device_name
				from network_diogonastic ND
				LEFT JOIN master_device MD1 ON MD1.device_id=ND.p_device_id
				";
		$sql .= " ORDER BY ND.p_device_id ASC, ND.id ASC ";	
		//echo $sql;	
		return $this->db->query($sql)->result();
    }

    public function getDiy3Details($trans_id = 0){
    	$this->db->select('DR.*,ND.trans_name');
    	$this->db->from('diy3_result DR');
    	$this->db->join('network_diogonastic ND','ND.id = DR.trans_id', 'left');
    	$this->db->where('DR.trans_id', $trans_id);
    	$this->db->order_by('DR.id','DESC');
    	$this->db->limit(1);
    	return $this->db->get()->result();
    }

}