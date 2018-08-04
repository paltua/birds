<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ems_model extends CI_Model {
	private $air_db;
	
	function __construct() {
		parent::__construct();
		log_message('INFO', 'Ems_model enter');
		$this->air_db = $this->load->database('welspun_air', TRUE);
	}
	

    function getDeviceWiseData($date = ''){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,MD.type,AGD1.data,AGD1.end_date_time  
				FROM master_tag MT
				join relation_device_tag RDT on RDT.tag_id=MT.tag_id 
				join master_device MD on MD.device_id = RDT.device_id
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=MT.tag_id
				where 1 ";
		if($date != ''){
			$sql .= " AND AGD1.end_date_time = '".$date."'";
		}		
		$sql .= " ORDER BY MT.short_name ASC";
		return $this->db->query($sql)->result();
    }

    public function getDeviceTypeWiseData($date = '', $typeId = 1){
    	$sql = "SELECT MT.tag_id,MT.tag_name,MT.short_name,MD.device_id,MD.device_name,MD.type,AGD1.data,AGD1.end_date_time  
				FROM master_tag MT
				join relation_device_tag RDT on RDT.tag_id=MT.tag_id 
				join master_device MD on MD.device_id = RDT.device_id
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=MT.tag_id
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
                left JOIN aggregate_data_1 AGD1 on AGD1.tag_id=MT.tag_id
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
    	$sql = "SELECT max(AGD1.end_date_time) end_date_time FROM aggregate_data_1 AGD1 where 1";
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

    public function getDevice(){
    	$sql = "SELECT * FROM master_device";
    	return $this->db->query($sql)->result();
    }

    public function getLastDateDataTypeWise($device_id,$startDate = '', $endDate = ''){				
		$sql = "SELECT * FROM `master_device` MD
				left join relation_device_tag MDT ON MDT.device_id=MD.`device_id`
				left join master_tag MT ON MT.tag_id=MDT.tag_id
				join aggregate_data_1 AD1 ON AD1.tag_id=MDT.tag_id
				WHERE MD.device_id='".$device_id."' AND AD1.end_date_time >= '".$startDate."' AND AD1.end_date_time < '".$endDate."' ORDER BY MT.short_name ASC";
		return $this->db->query($sql)->result();	
	}

	public function getKwhReadingData($startDate = '', $endDate = ''){
		$sql = "SELECT 
				    MD.device_id,
				    MD.device_name,
				    MD.type_text,
				    MD.type_level,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time,
				    DATE_FORMAT(EMDKWH.end_date_time, '%Y-%m-%d') end_date_time_1
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND EMDKWH.end_date_time >= '".$startDate."'
				        AND EMDKWH.end_date_time <= '".$endDate."'
				        AND TIME(EMDKWH.end_date_time) = '00:00:00'
				        AND MD.type != ''
				ORDER BY EMDKWH.end_date_time DESC, MD.type_text DESC, MD.type_level ASC, MD.device_name ASC";
		return $this->db->query($sql)->result();
	}

	public function getKwhConsumptionData($startDate = '', $endDate = ''){
    	$sql = "SELECT 
				    MD.device_id,
				    MD.device_name,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time,
				    DATE_FORMAT(EMDKWH.end_date_time, '%Y-%m-%d') end_date_time_1
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND EMDKWH.end_date_time >= '".$startDate."'
				        AND EMDKWH.end_date_time <= '".$endDate."'
				        AND TIME(EMDKWH.end_date_time) = '00:00:00'
				ORDER BY EMDKWH.end_date_time ASC";
		return $this->db->query($sql)->result();		
    }

    public function getCountGenDist(){
    	$sql = "SELECT 
				    type_text, COUNT(device_id) total
				FROM
				    master_device
				WHERE
				    type_text != ''
				GROUP BY type_text
				ORDER BY type_text DESC";
		return $this->db->query($sql)->result();		
    }

    public function getKwAvgData($startDate = '', $endDate = ''){
    	$sql = "SELECT 
				    MD.device_id,
					MD.device_name,
					MD.type_text,
					MD.type_level,
					AVG(EMD.data_KW) data_kw,
					EMD.end_date_time,
					DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time_1
				FROM
				    master_device MD
				    LEFT JOIN ems_meter_data EMD ON EMD.device_id=MD.device_id
				WHERE
				    MD.type_text != ''
				    AND DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') >= '".$startDate."'
					AND DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') <= '".$endDate."'
				GROUP BY  MD.device_id, DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d')
				ORDER BY DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') DESC, MD.type_text DESC, MD.type_level ASC, MD.device_name ASC";
		return $this->db->query($sql)->result();
    }

    public function getKwhReadingAirEmsData($startDate = '', $endDate = '', $deviceIds = ''){
    	$sql = "SELECT 
				    MD.device_id,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time,
				    DATE_FORMAT(EMDKWH.end_date_time, '%Y-%m-%d') end_date_time_1
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND EMDKWH.end_date_time >= '".$startDate."'
				        AND EMDKWH.end_date_time <= '".$endDate."'
				        AND MD.device_id IN (".$deviceIds.")
				        AND TIME(EMDKWH.end_date_time) = '00:00:00'
				        AND MD.type != ''
				ORDER BY EMDKWH.end_date_time DESC, MD.type_text DESC, MD.type_level ASC, MD.device_name ASC";
		return $this->db->query($sql)->result();
    }

    public function getAirMeter($type = 'gen'){
    	$this->air_db->select('*');
    	$this->air_db->from('air_meter');
    	$this->air_db->where('type', $type);
    	$this->air_db->order_by('meter_id','ASC');
    	return $this->air_db->get()->result();
    }

    public function getAirEmsKwhData($startDate = '', $endDate = '', $deviceIds = ''){
		$sql = "SELECT 
				    MD.device_id,
				    MD.device_name,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time,
				    DATE_FORMAT(EMDKWH.end_date_time, '%Y-%m-%d') end_date_time_1
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND EMDKWH.end_date_time >= '".$startDate."'
				        AND EMDKWH.end_date_time <= '".$endDate."'
				        AND TIME(EMDKWH.end_date_time) = '00:00:00'
				        AND MD.device_id IN (".$deviceIds.")
				ORDER BY EMDKWH.end_date_time DESC, MD.device_name ASC";
		return $this->db->query($sql)->result();
	}

	public function getLastDate(){
		$sql = "SELECT date_format(DATE_SUB(last_run_date_time, INTERVAL 1 DAY),'%Y-%m-%d') max FROM cron_hist WHERE id=1";
		return $this->db->query($sql)->result();
	}
    
	
}