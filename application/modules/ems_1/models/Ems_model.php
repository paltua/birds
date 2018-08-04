<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ems_model extends CI_Model {

	
	function __construct() {
		parent::__construct();
		$this->ems_cpp_db = $this->load->database('welspun_ems_cpp', TRUE);
		log_message('INFO', 'Page_model enter');
	}
	

    function getDeviceWiseData($date = ''){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,MD.type,AGD1.data,AGD1.end_date_time  
				FROM master_tag MT
				join relation_device_tag RDT on RDT.tag_id=MT.tag_id 
				join master_device MD on MD.device_id = RDT.device_id
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=RDT.tag_id
				where 1 AND MD.type != ''";
		if($date != ''){
			$sql .= " AND AGD1.end_date_time = '".$date."'";
		}		
		$sql .= " ORDER BY MT.short_name ASC";
		return $this->db->query($sql)->result();
    }

    function getDeviceWiseDataCpp($date = ''){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,AGD1.data,AGD1.end_date_time  
				FROM master_tag MT
				join relation_device_tag RDT on RDT.tag_id=MT.tag_id 
				join master_device MD on MD.device_id = RDT.device_id
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=RDT.tag_id
				where 1 ";
		if($date != ''){
			$sql .= " AND AGD1.end_date_time = '".$date."'";
		}		
		$sql .= " ORDER BY MT.short_name ASC";
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getDeviceTypeWiseData($date = '', $typeId = 1){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,MD.type,AGD1.data,AGD1.end_date_time  
				FROM master_tag MT
				join relation_device_tag RDT on RDT.tag_id=MT.tag_id 
				join master_device MD on MD.device_id = RDT.device_id
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=RDT.tag_id
				where 1 ";
		if($date != ''){
			$sql .= " AND AGD1.end_date_time = '".$date."'";
		}	

		if($typeId != ''){
			$sql .= " AND (MD.type = 'G".$typeId."' OR MD.type = 'D".$typeId."')";
		}		
		$sql .= " ORDER BY MT.short_name ASC";
		return $this->db->query($sql)->result();
    }

    public function getDeviceTypeWiseDataReport($startDate = '', $endDate = '', $typeId = 1){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,MD.type,AGD1.data,AGD1.end_date_time  
				FROM master_tag MT
				join relation_device_tag RDT on RDT.tag_id=MT.tag_id 
				join master_device MD on MD.device_id = RDT.device_id
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=RDT.tag_id
				where 1 ";
		if($startDate != ''){
			$sql .= " AND AGD1.end_date_time >= '".$startDate."'";
		}	

		if($endDate != ''){
			$sql .= " AND AGD1.end_date_time < '".$endDate."'";
		}

		if($typeId != ''){
			$sql .= " AND (MD.type = 'G".$typeId."' OR MD.type = 'D".$typeId."')";
		}		
		$sql .= " ORDER BY MT.short_name ASC";
		return $this->db->query($sql)->result();
    }

    public function getShiftWiseDataSetChart($device_id = 0, $short_name = '', $startDate = '', $endDate = ''){
    	$sql = "SELECT MD.device_id, MD.device_name, AGD.* FROM `master_device` MD
				JOIN relation_device_tag RDT ON RDT.device_id=MD.device_id
				JOIN master_tag MT ON MT.tag_id = RDT.tag_id 
					AND MT.short_name ='".$short_name."'
				JOIN aggregate_data_1 AGD ON AGD.tag_id=RDT.tag_id
				WHERE 1 AND MD.device_id = ".$device_id; 
		if($startDate != ''){
			$sql .= " AND AGD.end_date_time >= '".$startDate."'";
		}	

		if($endDate != ''){
			$sql .= " AND AGD.end_date_time < '".$endDate."'";
		}	
			
		$sql .= " Order by AGD.ad_id DESC";
		return $this->db->query($sql)->result();
    }


    public function getCurrent(){
    	$sql = "SELECT MIN(ACH.cron_last_run_1) end_date_time from master_device MD LEFT JOIN relation_device_tag RDT ON RDT.device_id=MD.device_id JOIN aggregate_cron_hist ACH ON ACH.tag_id = RDT.tag_id WHERE MD.type != '' ORDER BY MD.device_id";
		return $this->db->query($sql)->result();
    }

    public function getCurrent32DataSet($device_id = 0, $short_name = ''){
    	$sql = "SELECT MD.device_id, MD.device_name, AGD.* FROM `master_device` MD
				JOIN relation_device_tag RDT ON RDT.device_id=MD.device_id
				JOIN master_tag MT ON MT.tag_id = RDT.tag_id 
					AND MT.short_name ='".$short_name."'
				JOIN aggregate_data_1 AGD ON AGD.tag_id=RDT.tag_id
				WHERE 1 AND MD.device_id = ".$device_id." 
				Order by AGD.ad_id DESC
				LIMIT 32";
		return $this->db->query($sql)->result();		
    }

    public function getDateDetails(){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM aggregate_data_1";
		return $this->db->query($sql)->result();
    }	

    public function getEmsCppLoss($date = '', $ids = ''){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,AGD1.data,AGD1.end_date_time  
			FROM `master_device` MD 
			JOIN relation_device_tag RDT ON RDT.device_id=MD.device_id AND MD.device_id IN(".$ids.")
			JOIN master_tag MT ON MT.tag_id=RDT.tag_id AND MT.short_name = 'KW' 
			LEFT JOIN aggregate_data_1 AGD1 ON AGD1.tag_id=MT.tag_id ";
		if($date != ''){
			$sql .= " AND AGD1.end_date_time = '".$date."'";
		}	
			
		$sql .= " ORDER BY FIELD(MD.device_id,".$ids.")";	
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getEmsLoss($date = '', $ids = ''){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,AGD1.data,AGD1.end_date_time  
			FROM `master_device` MD 
			JOIN relation_device_tag RDT ON RDT.device_id=MD.device_id AND MD.device_id IN(".$ids.")
			JOIN master_tag MT ON MT.tag_id=RDT.tag_id AND MT.short_name = 'KW' 
			LEFT JOIN aggregate_data_1 AGD1 ON AGD1.tag_id=MT.tag_id ";
		if($date != ''){
			$sql .= " AND AGD1.end_date_time = '".$date."'";
		}	
		
		$sql .= " ORDER BY FIELD(MD.device_id,".$ids.")";	
		//echo $sql;
		return $this->db->query($sql)->result();
    }
	
	
}