<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Network_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
		log_message('INFO', 'Network_model enter');
	}

	public function getParentDeviceList(){
		$this->db->distinct('p_device_id');
		$this->db->select('MD.device_id,MD.device_name');
		$this->db->from('network_diogonastic ND');
		$this->db->join('master_device MD','MD.device_id=Nd.p_device_id', 'left');
		$this->db->order_by('MD.device_name');
		return $this->db->get()->result();
	}

	public function getDateDetails($meter_id = 0){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM aggregate_data_1 ";
		if($meter_id > 0){
			$sql .= " where meter_id=".$meter_id." group by date_format(end_date_time,'%Y-%m-%d') ";
		}
		return $this->db->query($sql)->result();
    }

    public function getDeviceWiseTransferInData($device_id = 0, $startDate = '', $endDate = ''){
		$sql = "SELECT ND.*,MD.device_id,ND.t_in_device_capacity,MD.device_name ,MT.tag_id, MT.short_name,AGD1.data,AGD1.end_date_time
					FROM network_diogonastic ND 
					LEFT JOIN master_device MD ON MD.device_id = ND.t_in_device_id 
					LEFT JOIN relation_device_tag RDT ON RDT.device_id= ND.t_in_device_id
					JOIN master_tag MT on MT.tag_id = RDT.tag_id AND (MT.short_name='KW' || MT.short_name='PF')
					LEFT JOIN aggregate_data_1 AGD1 ON AGD1.tag_id = MT.tag_id
					WHERE ND.p_device_id = '".$device_id."' 
					AND AGD1.end_date_time >= '".$startDate."' 
					AND AGD1.end_date_time < '".$endDate."'
					ORDER BY ND.id ASC";
		return $this->db->query($sql)->result();

    }

    public function getDeviceWiseTransferOutData($device_id = 0, $startDate = '', $endDate = ''){
		$sql = "SELECT ND.*,MD.device_id,MD.device_name ,MT.tag_id, MT.short_name,AGD1.data,AGD1.end_date_time
					FROM network_diogonastic ND 
					LEFT JOIN master_device MD ON MD.device_id = ND.t_out_device_id 
					LEFT JOIN relation_device_tag RDT ON RDT.device_id= ND.t_out_device_id
					JOIN master_tag MT on MT.tag_id = RDT.tag_id AND (MT.short_name='KW')
					LEFT JOIN aggregate_data_1 AGD1 ON AGD1.tag_id = MT.tag_id
					WHERE ND.p_device_id = '".$device_id."' 
					AND AGD1.end_date_time >= '".$startDate."' 
					AND AGD1.end_date_time < '".$endDate."'
					ORDER BY ND.id ASC";
		return $this->db->query($sql)->result();
    }

    public function getDeviceWiseTransferParentData($device_id = 0, $startDate = '', $endDate = ''){
		$sql = "SELECT MD.device_id, MD.device_name ,MT.tag_id, MT.short_name,AGD1.data,AGD1.end_date_time
					FROM  master_device MD 
					LEFT JOIN relation_device_tag RDT ON RDT.device_id= MD.device_id
					JOIN master_tag MT on MT.tag_id = RDT.tag_id AND (MT.short_name='KW')
					LEFT JOIN aggregate_data_1 AGD1 ON AGD1.tag_id = MT.tag_id
					WHERE MD.device_id = '".$device_id."' 
					AND AGD1.end_date_time >= '".$startDate."' 
					AND AGD1.end_date_time < '".$endDate."'
					";
		return $this->db->query($sql)->result();
    }


    public function getTransformerDetails($p_device_id = 0){
    	$sql = "SELECT 
				ND.*,
				MD1.device_name p_device_name,
				MD2.device_name t_in_device_name,
				MD3.device_name t_out_device_name
				from network_diogonastic ND
				LEFT JOIN master_device MD1 ON MD1.device_id=ND.p_device_id
				LEFT JOIN master_device MD2 ON MD2.device_id=ND.t_in_device_id
				LEFT JOIN master_device MD3 ON MD3.device_id=ND.t_out_device_id
				WHERE ND.p_device_id=".$p_device_id;
		$sql .= " ORDER BY ND.id DESC ";	
		//echo $sql;	
		return $this->db->query($sql)->result();
    }

}