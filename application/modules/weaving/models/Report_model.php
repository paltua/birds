<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model {
	public $plantKwId = 151;
	public $loomKwId = 152;
	public $airCfmId = 10;
	public $kwIdsQuery = '';
	public $cfmIdsQuery = '';
	public $weavingShift_1 ;
	public $weavingShift_2 ;
	public $weavingShift_3 ;
	public $otherShift ;
	function __construct() {
		parent::__construct();
		$this->kwIdsQuery = ' AND (EMD.device_id='.$this->plantKwId.' OR EMD.device_id='.$this->loomKwId.') ';
		$this->cfmIdsQuery = ' AND AMD.meter_id = '.$this->airCfmId;
		$this->weavingShift_1 = '00:45:00';
		$this->weavingShift_2 = '08:45:00';
		$this->weavingShift_3 = '16:45:00';
		$this->otherShift = '00:30:00';
		log_message('INFO', 'Report_model enter');
	}

	public function getCurrent(){
		$this->db->select('*');
		$this->db->from('welspun_weaving.cron_hist');
		$this->db->where('id',1);
		return $this->db->get()->result();
	}

	public function getTotalCmpxProduce($startDate = '', $endDate = ''){
		$sql = "SELECT 
			        SUM(IF(CP.cmpx > 0, CP.cmpx,0)) cmpx
			    FROM
			        welspun_weaving.curprod CP
			    WHERE
			        CP.end_date_time > '".$startDate."'
			            AND CP.end_date_time <= '".$endDate."'
			    ";		    
		return $this->db->query($sql)->result();
	}

	public function getTotalCfmConsumption($startDate = '', $endDate = ''){
		$sql = "SELECT AMD.ttl_flow, AMD.flow, AMD.p_pressure, AMD.t_temp 
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= '".$startDate."'
					AND AMD.end_date_time < '".$endDate."'";
		//echo $sql;			
		return $this->db->query($sql)->result();			
	}

	public function getTotalCfmConsumptionShift($startDate = '', $endDate = ''){
		$sql = "SELECT 
				    MAX(data_cfm) - MIN(data_cfm) cfm
				FROM
				    welspun_weaving.air_meter_data_cfm
				WHERE
				    end_date_time = '".$startDate."'
				        OR end_date_time = '".$endDate."'";
		return $this->db->query($sql)->result();		        
	}

	public function getTotalPressureShift($startDate = '', $endDate = ''){
		$sql = "SELECT AVG(IFNULL(AMD.p_pressure, 0)) pres
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= '".$startDate."'
					AND AMD.end_date_time < '".$endDate."'";
		//echo $sql;			
		return $this->db->query($sql)->result();			
	}

	public function getTotalKwConsumption($startDate = '', $endDate = ''){
		$sql = "SELECT SUM(EMD.data_KW) sum_kw 
				FROM welspun_ems.ems_meter_data  EMD
				WHERE 1 ".$this->kwIdsQuery." 
					AND EMD.end_date_time >= '".$startDate."' 
					AND EMD.end_date_time < '".$endDate."'";
		return $this->db->query($sql)->result();
	}

	public function getTotalKwConsumptionShift($startDate = '', $endDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(sum_kw) * 8 sum_kw
				FROM
				    (SELECT 
				        AVG(IFNULL(EMD.data_KW, 0)) sum_kw
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE 1 
				    	".$this->kwIdsQuery." 
			            AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
				    GROUP BY EMD.device_id) NT1";			
		return $this->db->query($sql)->result();
	}

	public function getLoomWiseProduction($startDate = '', $endDate = ''){
		$sql = "SELECT 
			    CP.machine_id,
			    CP.style_id,
			    AVG(((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100)) eff,
			    AVG((CP.picks * 60) / CP.rtime) rpm,
			    SSD.style,
			    SUM(IF(CP.cmpx > 0, CP.cmpx, 0)) cmpx
			FROM
			    welspun_weaving.curprod CP
			        LEFT JOIN
			    welspun_weaving.std_style SSD ON SSD.style_id = CP.style_id
			WHERE
			    CP.end_date_time > '".$startDate."'
			        AND CP.end_date_time <= '".$endDate."'
			GROUP BY CP.machine_id";     
			//exit;		    
		return $this->db->query($sql)->result();        
	}

	public function getStoppageDetails($startDate = '', $endDate = '', $machine_id = ''){
		$sql = "SELECT CS.*, SCD.descr
				FROM welspun_weaving.curstop CS
				LEFT JOIN welspun_weaving.std_scode SCD ON SCD.scode = CS.scode
				WHERE end_date_time >= '".$startDate."'
			        AND end_date_time < '".$endDate."'
			        AND machine_id = ".$machine_id." 
			    ORDER BY CS.BEGTIME DESC";
		return $this->db->query($sql)->result();  		        
	}

	public function getSingleLoomProduction($machine_id = '', $startDate = '', $endDate = '', $type = ''){
		$select = '';
		if($type == 'rpm'){
			$select = ', ((CP.picks * 60) / CP.rtime) data';
		}elseif($type == 'picks'){
			$select = ', CP.picks data';
		}elseif($type == 'eff'){
			$select = ', ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) data';
		}

		$sql = "SELECT 
				    CP.machine_id,
				    CP.style_id,
				    CP.end_date_time
				    ".$select."
				FROM welspun_weaving.curprod CP
				WHERE 1 
					AND CP.machine_id = ".$machine_id."
				    AND end_date_time > '".$startDate."'
				    AND end_date_time <= '".$endDate."'
				ORDER BY  CP.end_date_time ASC";
		//echo $sql;		    
		return $this->db->query($sql)->result();
	}

	public function getStyleDetails($style_id = 0){
		$this->db->select('*');
		$this->db->from('welspun_weaving.std_style');
		$this->db->where('style_id', $style_id);
		return $this->db->get()->result();
	}

	public function getStyleColumnName(){
		$sql = "SHOW COLUMNS FROM welspun_weaving.std_style;";
		return $this->db->query($sql)->result();
	}

	public function getTotalCmpxProduceDateWiseShift($startDate = '', $endDate = ''){
		$sql = "SELECT 
				    SUM(IF(CP.cmpx > 0, CP.cmpx, 0)) data,
				    CP.end_date_time
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time > '".$startDate."'
				        AND CP.end_date_time <= DATE_ADD('".$startDate."', INTERVAL 8 HOUR)
				GROUP BY CP.end_date_time";
		return $this->db->query($sql)->result();
	}

	public function getRunningKwDateWiseShift($startDate = '', $endDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
			        AVG(EMD.data_KW) data,
			            EMD.end_date_time orginal_end_date_time,
			            DATE_ADD(EMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_ems.ems_meter_data EMD
			    WHERE 1 ".$this->kwIdsQuery."
			            AND EMD.end_date_time < '".$endDate."'
			            AND EMD.end_date_time >= DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
			    GROUP BY EMD.end_date_time ";
		//echo $sql;	    
		return $this->db->query($sql)->result();
	}	

	public function getKwCmpxDateWiseShift($startDate = '', $endDate = ''){
 		$sql = "SELECT 
				    *
				FROM
				    (SELECT 
				        SUM(CP.PICKS) sum_picks, CP.end_date_time, (SELECT SUM(picks)  FROM welspun_weaving.curprod WHERE end_date_time = DATE_ADD(CP.end_date_time,
				        INTERVAL - 15 MINUTE)) prev_sum_picks
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        CP.end_date_time < '".$endDate."'
				            AND CP.end_date_time >= DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				    GROUP BY CP.end_date_time) AS newCurprod
				        LEFT JOIN
				    (SELECT 
				        SUM(EMD.data_KW) sum_kw,
				            EMD.end_date_time orginal_end_date_time,
				            DATE_ADD(EMD.end_date_time, INTERVAL 15 MINUTE) new_end_date_time
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE
				        1
				            ".$this->kwIdsQuery."
				            AND EMD.end_date_time < '".$endDate."'
				            AND EMD.end_date_time >= DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				    GROUP BY EMD.end_date_time) AS newEms ON newEms.new_end_date_time = newCurprod.end_date_time";
		return $this->db->query($sql)->result();
	}

	public function getCfmDateWiseShift($startDate = '', $endDate = '', $type = 'flow'){
		$select = '';
		if($type == 'flow'){
			$select = ' IFNULL(AMD.flow / 3,0) data, ';
		}elseif($type == 'pres'){
			$select = ' IFNULL(AMD.p_pressure,0) data, ';
		}
		$sql = "SELECT 
			        ".$select."
		            AMD.end_date_time orginal_end_date_time,
		            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time >= '".$startDate."'
		            AND AMD.end_date_time < '".$endDate."'
		        GROUP BY AMD.end_date_time ";
		//echo $sql;	    
		return $this->db->query($sql)->result();
	}	

	public function getCfmCmpxDateWiseShift($startDate = '', $endDate = ''){
		$sql = "SELECT 
				    *
				FROM
				    (SELECT 
				        SUM(CP.PICKS) sum_picks, CP.end_date_time, (SELECT SUM(picks)  FROM welspun_weaving.curprod WHERE end_date_time = DATE_ADD(CP.end_date_time,
				        INTERVAL - 15 MINUTE)) prev_sum_picks
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        CP.end_date_time < '".$endDate."'
				            AND CP.end_date_time >= DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				    GROUP BY CP.end_date_time) AS newCurprod
				        LEFT JOIN
				    (SELECT 
				        AMD.flow,
				            AMD.end_date_time orginal_end_date_time,
				            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) new_end_date_time
				    FROM
				        welspun_air.air_meter_data AMD
				    WHERE
				        1 ".$this->cfmIdsQuery."
				            AND AMD.end_date_time < '".$endDate."'
				            AND AMD.end_date_time >= DATE_ADD('".$endDate."', INTERVAL - 8 HOUR)
				    GROUP BY AMD.end_date_time) AS newAir ON newAir.new_end_date_time = newCurprod.end_date_time
				WHERE
				    newAir.new_end_date_time IS NOT NULL";		      
		return $this->db->query($sql)->result();		            
	}

	public function getCmpxDateWiseShift($machine_id = '', $endDate = ''){
		$sql = "SELECT 
				    CP.end_date_time,
				    CP.cmpx
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time <= '".$endDate."'
				        AND CP.end_date_time > DATE_ADD('".$endDate."',
				        INTERVAL - 8 HOUR)
				        AND CP.machine_id = ".$machine_id;
		return $this->db->query($sql)->result();		        
	}

	public function getSingleLoomProductionShift($machine_id = '', $startDate = '', $endDate = '', $type = ''){
		$select = '';
		if($type == 'rpm'){
			$select = ', ((CP.picks * 60) / CP.rtime) data';
		}elseif($type == 'picks'){
			$select = ', CP.picks data';
		}elseif($type == 'eff'){
			$select = ', ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) data';
		}

		$sql = "SELECT 
				    CP.machine_id,
				    CP.style_id,
				    CP.end_date_time
				    ".$select."
				FROM welspun_weaving.curprod CP
				LEFT JOIN welspun_weaving.std_style SSD ON SSD.style_id = CP.style_id  
				WHERE 1 
					AND CP.machine_id = ".$machine_id."
				    AND end_date_time >= '".$startDate."'
				    AND end_date_time < '".$endDate."'
				ORDER BY  CP.end_date_time ASC";
		//echo $sql;		    
		return $this->db->query($sql)->result();
	}

	public function getTotalCmpxProduceDay($startDate = '', $endDate = ''){

		$search = " AND CP.end_date_time > '".$startDate."' 
					AND CP.end_date_time <= '".$endDate."' ";

		$sql = "SELECT 
				    AVG(IFNULL(NT4.data,0)) cmpx
				FROM
				    (SELECT 
			        SUM(NT3.cmpx) data, NT3.days end_date_time
			    FROM
			        (SELECT 
			        NT2.days,
			            NT2.shifts,
			            SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
			    FROM
			        (SELECT 
			        NT1.machine_id,
			            NT1.days,
			            NT1.shifts,
			            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
			    FROM
			        (SELECT 
			        CP.machine_id,
			            CP.cmpx,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.") NT1
			    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
			    ORDER BY NT1.machine_id) NT2
			    GROUP BY NT2.days , NT2.shifts) NT3
			    GROUP BY NT3.days) NT4";
		return $this->db->query($sql)->result();


		/*
		$sql = "SELECT 
				    SUM(NT1.cmpx) cmpx
				FROM
				    (SELECT 
				        (SUBSTRING_INDEX(GROUP_CONCAT(CAST(CP.picks AS SIGNED)
				                ORDER BY CP.end_date_time DESC), ',', 1) - SUBSTRING_INDEX(GROUP_CONCAT(CAST(CP.picks AS SIGNED)
				                ORDER BY CP.end_date_time), ',', 1))  cmpx
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        CP.end_date_time >= '".$startDate."'
				            AND CP.end_date_time < '".$endDate."'
				    GROUP BY CP.machine_id) NT1";		    
		return $this->db->query($sql)->result();*/
	}

	public function getTotalCfmConsumptionDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
				    AVG(IFNULL(NT2.data,0)) cfm
				FROM
				    (SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm) data,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= '".$startDate."'
				            AND AMDCFM.end_date_time <= DATE_ADD('".$endDate."', INTERVAL 30 MINUTE)
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL) NT2";

		

		return $this->db->query($sql)->result();
					
	}

	public function getTotalKwConsumptionDay($startDate = '', $endDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(sum_kw) sum_kw
				FROM
				    (SELECT 
				        AVG(IFNULL(EMD.data_KW, 0)) sum_kw
				    FROM
				        welspun_ems.ems_meter_data EMD
				    WHERE 1 
				    	".$this->kwIdsQuery." 
			            AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
				    GROUP BY EMD.device_id) NT1";			
		$data = $this->db->query($sql)->result();
		return $data[0]->sum_kw;
	}

	public function getDateDiff($startDate = '', $endDate = ''){
    	$sql = "SELECT DATEDIFF('".$endDate."','".$startDate."') as days";
		return $this->db->query($sql)->result();
    }


	public function getTotalCmpxProduceDateWiseDay($startDate = '', $endDate = ''){
		$search = " AND CP.end_date_time >= '".$startDate."'
					AND CP.end_date_time < '".$endDate."'";

		$sql = "SELECT 
			        SUM(NT3.cmpx) data, NT3.days end_date_time
			    FROM
			        (SELECT 
			        NT2.days,
			            NT2.shifts,
			            SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
			    FROM
			        (SELECT 
			        NT1.machine_id,
			            NT1.days,
			            NT1.shifts,
			            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
			    FROM
			        (SELECT 
			        CP.machine_id,
			            CP.cmpx,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.") NT1
			    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
			    ORDER BY NT1.machine_id) NT2
			    GROUP BY NT2.days , NT2.shifts) NT3
			    WHERE NT3.days between DATE_FORMAT('".$startDate."', '%Y-%m-%d') AND DATE_FORMAT('".$endDate."', '%Y-%m-%d')
			    GROUP BY NT3.days";
		//echo $sql;exit;
		return $this->db->query($sql)->result();
	}

	public function getTotalCmpxProduceSingleDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
				    SUM(CP.cmpx) data,
				    CP.end_date_time
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time >= '".$startDate."'
				        AND CP.end_date_time < '".$endDate."'
				GROUP BY CP.end_date_time ";
		return $this->db->query($sql)->result();
	}

	public function getRunningKwChartSingleDay($startDate = '', $endDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
			        SUM(EMD.data_KW) data,
			            EMD.end_date_time orginal_end_date_time,
			            DATE_ADD(EMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_ems.ems_meter_data EMD
			    WHERE 1 ".$this->kwIdsQuery."
			            AND EMD.end_date_time >= '".$startDate."'
			            AND EMD.end_date_time < '".$endDate."'
			    GROUP BY EMD.end_date_time ";
		//echo $sql;	    
		return $this->db->query($sql)->result();
	}	

	public function getRunningKwChartDay($startDate = '', $endDate = '', $type = 0){
		if($type == 1){
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->plantKwId;
		}elseif ($type == 2) {
			$this->kwIdsQuery = ' AND EMD.device_id='.$this->loomKwId;
		}
		$sql = "SELECT 
				    SUM(NT1.data_KW) data, DATE_FORMAT(NT1.end_date_time,'%Y-%m-%d') end_date_time
				FROM
				    (
				SELECT 
				    EMD.device_id, AVG(EMD.data_KW) data_KW, EMD.end_date_time
				FROM
				    welspun_ems.ems_meter_data EMD
				WHERE
				    1
				        ".$this->kwIdsQuery."
				        AND EMD.end_date_time >= '".$startDate."'
				        AND EMD.end_date_time < '".$endDate."'
				GROUP BY EMD.device_id,DATE_FORMAT(EMD.end_date_time,'%Y-%m-%d') ) NT1
				GROUP BY NT1.end_date_time";
		return $this->db->query($sql)->result();
	}

	public function getCfmDateWiseDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm) data,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= '".$startDate."'
				            AND AMDCFM.end_date_time <= '".$endDate."'
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL
				    ORDER BY NT1.end_date_time " ;       

		//echo $sql;	    
		return $this->db->query($sql)->result();
		/*$sql = "SELECT 
			        AVG(IFNULL(AMD.flow,0)) data,
		            AMD.end_date_time orginal_end_date_time,
		            DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d') end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time >= '".$startDate."'
			        AND AMD.end_date_time < '".$endDate."'
		        GROUP BY DATE_FORMAT(AMD.end_date_time,'%Y-%m-%d')";

		//echo $sql;	    
		return $this->db->query($sql)->result();*/
	}

	public function getTotalPressDay($startDate = '', $endDate = ''){
		$sql = "SELECT AVG(NULLIF(AMD.P_pressure,0)) pressure
				FROM welspun_air.air_meter_data AMD
				WHERE 1 ".$this->cfmIdsQuery."
					AND AMD.end_date_time >= '".$startDate."'
				    AND AMD.end_date_time < '".$endDate."'";
		return $this->db->query($sql)->result();
	}

	public function getCfmDateWiseSingleDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
			        IFNULL(AMD.flow,0) data,
		            AMD.end_date_time orginal_end_date_time,
		            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time < '".$endDate."'
		            AND AMD.end_date_time >= '".$startDate."'
		        GROUP BY AMD.end_date_time ";
		//echo $sql;	    
		return $this->db->query($sql)->result();
	}

	public function getCfmCmpxChartDay($startDate = '', $endDate = ''){
		$search = " AND CP.end_date_time >= '".$startDate."'
					AND CP.end_date_time < '".$endDate."'";

		$sql = " SELECT IF(NTCMPX1.data != 0,NTCFM1.data/(NTCMPX1.data/".WEAVING_CMPX."),0) data, NTCMPX1.end_date_time 
				FROM (

				SELECT 
			        SUM(NT3.cmpx) data, NT3.days end_date_time
			    FROM
			        (SELECT 
			        NT2.days,
			            NT2.shifts,
			            SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
			    FROM
			        (SELECT 
			        NT1.machine_id,
			            NT1.days,
			            NT1.shifts,
			            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
			    FROM
			        (SELECT 
			        CP.machine_id,
			            CP.cmpx,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.") NT1
			    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
			    ORDER BY NT1.machine_id) NT2
			    GROUP BY NT2.days , NT2.shifts) NT3
			    WHERE NT3.days between DATE_FORMAT('".$startDate."', '%Y-%m-%d') AND DATE_FORMAT('".$endDate."', '%Y-%m-%d')
			    GROUP BY NT3.days) NTCMPX1 LEFT JOIN (

					SELECT 
					    (NT1.last_data_cfm - NT1.data_cfm) data,
					    DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
					FROM
					    (SELECT 
					    AMDCFM.*,
					        (SELECT 
					                AMDCFM1.data_cfm
					            FROM
					                welspun_weaving.air_meter_data_cfm AMDCFM1
					            WHERE
					                AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
					FROM
					    welspun_weaving.air_meter_data_cfm AMDCFM
					WHERE
					    1
					    AND AMDCFM.end_date_time >= '".$startDate."'
					    AND AMDCFM.end_date_time <= '".$endDate."'
					    AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
					WHERE
					    NT1.last_data_cfm IS NOT NULL ) NTCFM1 ON NTCFM1.end_date_time = NTCMPX1.end_date_time
					WHERE 1 AND NTCMPX1.end_date_time != DATE_FORMAT(DATE_ADD('".$startDate."', INTERVAL - 1 HOUR), '%Y-%m-%d')     ";
		return $this->db->query($sql)->result();
	}

	public function getKwCmpxChartDay($startDate = '', $endDate = ''){
		$search = " AND CP.end_date_time >= '".$startDate."'
					AND CP.end_date_time < '".$endDate."'";

		$sql = " SELECT IF(NTCMPX1.data != 0,NTKW1.data/(NTCMPX1.data/".WEAVING_CMPX."),0) data, NTCMPX1.end_date_time 
				FROM (

				SELECT 
			        SUM(NT3.cmpx) data, NT3.days end_date_time
			    FROM
			        (SELECT 
			        NT2.days,
			            NT2.shifts,
			            SUM(CASE
			                WHEN NT2.total >= 0 THEN NT2.total
			                ELSE 0
			            END) cmpx
			    FROM
			        (SELECT 
			        NT1.machine_id,
			            NT1.days,
			            NT1.shifts,
			            SUBSTRING_INDEX(GROUP_CONCAT(CAST(NT1.picks AS SIGNED)
			                ORDER BY NT1.end_date_time DESC), ',', 1) - SUBSTRING_INDEX(GROUP_CONCAT(CAST(NT1.picks AS SIGNED)
			                ORDER BY NT1.end_date_time), ',', 1) AS total
			    FROM
			        (SELECT 
			        CP.machine_id,
			            CP.picks,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.") NT1
			    GROUP BY NT1.machine_id , NT1.days , NT1.shifts
			    ORDER BY NT1.machine_id) NT2
			    GROUP BY NT2.days , NT2.shifts) NT3
			    GROUP BY NT3.days) NTCMPX1 LEFT JOIN (
			    SELECT 
				    SUM(NT1.data_KW) data, DATE_FORMAT(NT1.end_date_time,'%Y-%m-%d') end_date_time
				FROM
				    (
				SELECT 
				    EMD.device_id, AVG(EMD.data_KW) data_KW, EMD.end_date_time
				FROM
				    welspun_ems.ems_meter_data EMD
				WHERE
				    1
				        ".$this->kwIdsQuery."
				        AND EMD.end_date_time >= '".$startDate."'
				        AND EMD.end_date_time < '".$endDate."'
				GROUP BY EMD.device_id,DATE_FORMAT(EMD.end_date_time,'%Y-%m-%d') ) NT1
				GROUP BY NT1.end_date_time) NTKW1 ON NTKW1.end_date_time = NTCMPX1.end_date_time
					WHERE 1 AND NTCMPX1.end_date_time != DATE_FORMAT(DATE_ADD('".$startDate."', INTERVAL - 1 HOUR), '%Y-%m-%d')     ";
		return $this->db->query($sql)->result();
	}

	public function getCfmCmpxChartSingleDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
					IF((NTCMPX1.data) != 0, NTCFM1.data/((NTCMPX1.data)/".WEAVING_CMPX."),0) data,
					NTCMPX1.end_date_time
				FROM (SELECT 
				    IF(CP.cmpx > 0,CP.cmpx,0) data,
				    CP.end_date_time
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time >= '".$startDate."'
				        AND CP.end_date_time < '".$endDate."'
				GROUP BY CP.end_date_time) NTCMPX1 
				LEFT JOIN ( SELECT 
			        IFNULL(AMD.flow,0) data,
		            AMD.end_date_time orginal_end_date_time,
		            DATE_ADD(AMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_air.air_meter_data AMD
			    WHERE 1 ".$this->cfmIdsQuery."
		            AND AMD.end_date_time < '".$endDate."'
		            AND AMD.end_date_time >= '".$startDate."'
		        GROUP BY AMD.end_date_time ) NTCFM1 ON NTCFM1.end_date_time = NTCMPX1.end_date_time
		        ORDER BY NTCMPX1.end_date_time";		
		return $this->db->query($sql)->result();
	}

	public function getKwCmpxChartSingleDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
					IF((NTCMPX1.data - NTCMPX1.prev_data) != 0, NTKW1.data/((NTCMPX1.data - NTCMPX1.prev_data)/".WEAVING_CMPX."),0) data,
					NTCMPX1.end_date_time
				FROM (SELECT 
				    SUM(CP.picks) data,
				    CP.end_date_time,
				    (SELECT SUM(picks) FROM welspun_weaving.curprod WHERE end_date_time = DATE_ADD(CP.end_date_time, INTERVAL - 15 MINUTE)) prev_data
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time >= '".$startDate."'
				        AND CP.end_date_time < '".$endDate."'
				GROUP BY CP.end_date_time) NTCMPX1 
				LEFT JOIN ( SELECT 
			        SUM(EMD.data_KW) data,
			            EMD.end_date_time orginal_end_date_time,
			            DATE_ADD(EMD.end_date_time, INTERVAL 15 MINUTE) end_date_time
			    FROM
			        welspun_ems.ems_meter_data EMD
			    WHERE 1 ".$this->kwIdsQuery."
			            AND EMD.end_date_time >= '".$startDate."'
			            AND EMD.end_date_time < '".$endDate."'
			    GROUP BY EMD.end_date_time ) NTKW1 ON NTKW1.end_date_time = NTCMPX1.end_date_time
			    ORDER BY NTCMPX1.end_date_time";
		return $this->db->query($sql)->result();
	}

	public function getLoomStyleWiseDataDay($startDate = '', $endDate = ''){

		$sql = " SELECT 
				    NT4.machine_id,
				    NT4.style_id,
				    NT4.eff,
				    NT4.rpm,
				    NT4.data cmpx,
				    SSD.style
				FROM
				    (SELECT 
				        NT3.machine_id,
				            NT3.style_id,
				            AVG(NT3.eff) eff,
				            AVG(NT3.rpm) rpm,
				            SUM(NT3.cmpx) data
				    FROM
				        (SELECT 
				        NT2.machine_id,
				            NT2.style_id,
				            AVG(NT2.eff) eff,
				            AVG(NT2.rpm) rpm,
				            NT2.days,
				            SUM(CASE
				                WHEN NT2.total >= 0 THEN NT2.total
				                ELSE 0
				            END) cmpx
				    FROM
				        (SELECT 
				        NT1.machine_id,
				            NT1.style_id,
				            AVG(NT1.eff) eff,
				            AVG(NT1.rpm) rpm,
				            NT1.days,
				            NT1.shifts,
				            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
				    FROM
				        (SELECT 
				        CP.machine_id,
				            CP.style_id,
				            ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) eff,
				            ((CP.picks * 60) / CP.rtime) rpm,
				            CP.cmpx,
				            CP.end_date_time,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'), DATE_FORMAT(DATE_ADD(CP.end_date_time, INTERVAL - 1 HOUR), '%Y-%m-%d'), DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
				            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
				                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
				    FROM
				        welspun_weaving.curprod CP
				    WHERE
				        1
				            AND CP.end_date_time >= '".$startDate."'
				            AND CP.end_date_time < '".$endDate."') NT1
				    GROUP BY NT1.machine_id , NT1.style_id , NT1.days , NT1.shifts
				    ORDER BY NT1.machine_id) NT2
				    GROUP BY NT2.machine_id , NT2.style_id , NT2.days) NT3
				    GROUP BY NT3.machine_id , NT3.style_id) NT4
				        LEFT JOIN
				    welspun_weaving.std_style SSD ON SSD.style_id = NT4.style_id";
		return $this->db->query($sql)->result();
	}

	public function getLoomStyleWiseEffRpmChartDay($startDate = '', $endDate = '', $machine_id = 175, $style_id = 155){
		$sql = "SELECT 
				    CP.machine_id,
				    CP.style_id,
				    ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) Efficiency,
				    ((CP.picks * 60) / CP.rtime) RPM,
				    DATE_FORMAT(CP.end_date_time, '%Y-%m-%d') end_date_time
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    1
				        AND CP.end_date_time >= '".$startDate."'
				        AND CP.end_date_time < '".$endDate."'
				        AND CP.machine_id = ".$machine_id."
				        AND CP.style_id = ".$style_id."
				GROUP BY DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')";
		return $this->db->query($sql)->result();		            
	}

	public function getLoomStyleCmpxChartDay($startDate ='0', $endDate = '0', $machine_id = 0, $style_id = 0){
		$search = " AND (CP.end_date_time >= '".$startDate."') AND (CP.end_date_time < '".$endDate."') 
					AND (CP.machine_id = ".$machine_id.") AND (CP.style_id = ".$style_id.")";

		$sql = "SELECT 
		            NT2.days end_date_time,
		            SUM(CASE
		                WHEN NT2.total >= 0 THEN NT2.total
		                ELSE 0
		            END) cmpx
			    FROM
			        (SELECT 
			            NT1.days,
			            NT1.shifts,
			            SUM(IF(NT1.cmpx > 0,NT1.cmpx,0)) total
			    FROM
			        (SELECT 
			        	CP.machine_id,
			        	CP.style_id,
			            CP.cmpx,
			            CP.end_date_time,
			            IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '00:00:00'
					            AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_1."'),
					        DATE_FORMAT(DATE_ADD(CP.end_date_time,
					                    INTERVAL - 1 HOUR),
					                '%Y-%m-%d'),
					        DATE_FORMAT(CP.end_date_time, '%Y-%m-%d')) days,
			           	IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_1."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_2."'), 1, IF((DATE_FORMAT(CP.end_date_time, '%H:%i:00') >= '".$this->weavingShift_2."'
			                AND DATE_FORMAT(CP.end_date_time, '%H:%i:00') < '".$this->weavingShift_3."'), 2, 3)) shifts
			    FROM
			        welspun_weaving.curprod CP
			    WHERE 1 ".$search.")  NT1
			    GROUP BY NT1.days, NT1.shifts
			    ORDER BY NT1.days) NT2
			    GROUP BY NT2.days";

		return $this->db->query($sql)->result();
	}

	public function getLoomStyleWiseEffRpmChartSingleDay($startDate ='0', $endDate = '0', $machine_id = 0, $style_id = 0){
		$sql = "SELECT 
				    CP.machine_id,
				    CP.style_id,
				    CP.end_date_time,
				    ((CP.picks * 60) / CP.rtime) RPM,
				    ((CP.rtime / (CP.rtime + CP.pstime + CP.npstime)) * 100) Efficiency
				FROM welspun_weaving.curprod CP
				LEFT JOIN welspun_weaving.std_style SSD ON SSD.style_id = CP.style_id  
				WHERE 1 
					AND CP.machine_id = ".$machine_id."
					AND CP.style_id = ".$style_id."
				    AND CP.end_date_time >= '".$startDate."'
				    AND CP.end_date_time < '".$endDate."'
				ORDER BY  CP.end_date_time ASC";
		//echo $sql;		    
		return $this->db->query($sql)->result();
	}

	public function getLoomStyleCmpxChartSingleDay($startDate ='0', $endDate = '0', $machine_id = 0, $style_id = 0){
		$sql = "SELECT 
				    CP.end_date_time,
				    CP.cmpx
				FROM
				    welspun_weaving.curprod CP
				WHERE
				    CP.end_date_time >= '".$startDate."'
			        AND CP.end_date_time < '".$endDate."' 
			        AND CP.machine_id = ".$machine_id."
			        AND CP.style_id = ".$style_id;
		return $this->db->query($sql)->result();
	}

	public function getCfmPressChartDay($startDate = '', $endDate = ''){
		$sql = "SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm) data,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= '".$startDate."'
				            AND AMDCFM.end_date_time <= '".$endDate."'
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL
				    ORDER BY NT1.end_date_time " ; 

		$sql = "SELECT 
				    NT2.*,NT3.pressure
				FROM
				    (SELECT 
				        (NT1.last_data_cfm - NT1.data_cfm)/".WEAVING_CFM." data,
				            DATE_FORMAT(NT1.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        (SELECT 
				        AMDCFM.*,
				            (SELECT 
				                    AMDCFM1.data_cfm
				                FROM
				                    welspun_weaving.air_meter_data_cfm AMDCFM1
				                WHERE
				                    AMDCFM1.end_date_time = DATE_ADD(AMDCFM.end_date_time, INTERVAL 1 DAY)) AS last_data_cfm
				    FROM
				        welspun_weaving.air_meter_data_cfm AMDCFM
				    WHERE
				        1
				            AND AMDCFM.end_date_time >= '".$startDate."'
				            AND AMDCFM.end_date_time <= '".$endDate."'
				            AND DATE_FORMAT(AMDCFM.end_date_time, '%H:%i:00') = '".$this->otherShift."') NT1
				    WHERE
				        NT1.last_data_cfm IS NOT NULL
				    ORDER BY NT1.end_date_time) NT2
				        LEFT JOIN
				    (SELECT 
				        AVG(NULLIF(AMD.P_pressure, 0)) pressure,
				            DATE_FORMAT(AMD.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        welspun_air.air_meter_data AMD
				    WHERE
				        1 AND AMD.meter_id = 10
				            AND AMD.end_date_time >= '".$startDate."'
				            AND AMD.end_date_time < '".$endDate."'
				    GROUP BY DATE_FORMAT(AMD.end_date_time, '%Y-%m-%d')) NT3 ON NT3.end_date_time = NT2.end_date_time";		          

		//echo $sql;	 exit;   
		return $this->db->query($sql)->result();
	}
	
	
}