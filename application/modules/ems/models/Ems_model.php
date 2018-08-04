<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ems_model extends CI_Model {

	
	function __construct() {
		parent::__construct();
		$this->ems_cpp_db = $this->load->database('welspun_ems_cpp', TRUE);
		log_message('INFO', 'Ems_model enter');
	}

    function getGenDistAllData($startDate = '', $endDate = ''){

    	$sql = "SELECT 
				    MD.type_text,
				    MD.type_level,
				    SUM(EMD.data_KW) KW,
				    AVG(NULLIF(EMD.data_PF, 0)) avg_PF,
				    SUM(EMD.data_KW / EMD.data_PF) PF,
				    AVG(NULLIF(EMD.data_Volt, 0)) Volt,
				    SUM(EMD.data_Amps) Amps,
				    AVG(NULLIF(EMD.data_HZ, 0)) HZ
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE
				    1 AND MD.type != ''
				        AND EMD.end_date_time >= '".$startDate."'
				        AND EMD.end_date_time < '".$endDate."'
				GROUP BY MD.type
				ORDER BY MD.type_text DESC , MD.type_level ASC";
		return $this->db->query($sql)->result();
    }

    function getDeviceWiseDataCpp($date = ''){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
				    EMD.data_KW KW,
				    EMD.data_PF PF,
				    if(EMD.data_PF > 0,EMD.data_KW/EMD.data_PF,0) KWPF,
				    EMD.data_Volt Volt,
				    EMD.data_Amps Amps,
				    EMD.data_HZ HZ
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE
				    1 AND EMD.end_date_time = '".$date."'     
				ORDER BY  MD.device_name ASC";
				//echo $sql; 	
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getDeviceTypeWiseData($date = '', $typeId = 1){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
    				MD.type,
				    EMD.data_KW KW,
				    EMD.data_PF PF,
				    if(EMD.data_PF > 0,EMD.data_KW/EMD.data_PF,0) KWPF,
				    EMD.data_Volt Volt,
				    EMD.data_Amps Amps,
				    EMD.data_HZ HZ
				FROM
				    master_device MD
				        JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				        AND EMD.end_date_time = '".$date."'
				WHERE 1 ";

		if($typeId != ''){
			$sql .= " AND (MD.type = 'G".$typeId."' OR MD.type = 'D".$typeId."')";
		}		
		
		return $this->db->query($sql)->result();
    }

    public function getDeviceTypeWiseDataReport($startDate = '', $endDate = '', $typeId = 1){
    	$sql = " SELECT 
    				MD.device_id,
    				MD.device_name,
    				MD.type,
				    AVG(EMD.data_KW) KW,
				    AVG(NULLIF(EMD.data_PF,0)) PF,
				    SUM(EMD.data_KW/EMD.data_PF) KWPF,
				    AVG(NULLIF(EMD.data_Volt,0)) Volt,
				    AVG(NULLIF(EMD.data_Amps,0)) Amps,
				    AVG(NULLIF(EMD.data_HZ,0)) HZ
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE 1 ";
		if($startDate != ''){
			$sql .= " AND EMD.end_date_time >= '".$startDate."'";
		}	

		if($endDate != ''){
			$sql .= " AND EMD.end_date_time < '".$endDate."'";
		}

		if($typeId != ''){
			$sql .= " AND (MD.type = 'G".$typeId."' OR MD.type = 'D".$typeId."')";
		}	
		$sql .= " GROUP BY MD.device_id ";	
		$sql .= " ORDER BY MD.device_name ASC";
		//echo $sql;
		return $this->db->query($sql)->result();
    }

    

    public function getDayWiseDataSetChartLine($device_id = 0, $short_name = '', $startDate = '', $endDate = ''){
    	$field = "EMD.data_".$short_name."";
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,".$field." data,EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE MD.device_id = ".$device_id."
				AND EMD.end_date_time >= '".$startDate."'
				AND EMD.end_date_time < '".$endDate."'
				ORDER BY EMD.emd_id DESC ";
		//echo $sql;			
		return $this->db->query($sql)->result();
    }

    public function getDayWiseDataSetChartBar($device_id = 0, $field = '', $startDate = '', $endDate = ''){
    	if($field == 'KW'){
			$fields = "AVG(EMD.data_".$field.")";
		}else{
			$fields = "AVG(NULLIF(EMD.data_".$field.",0))";
		}
		if($field == 'KW' || $field == 'Amps'){
			$fieldNew = " SUM(TBL1.data)  ";
		}else{
			$fieldNew = " AVG(NULLIF(TBL1.data,0))  ";
		}
		$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time, TBL1.device_id,TBL1.device_name
				FROM
				    (SELECT 
				        EMD.device_id,
				        MD.device_name,
				            ".$fields." data,
				            DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        ems_meter_data EMD
				        JOIN master_device MD ON MD.device_id = EMD.device_id
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
						AND EMD.device_id = '".$device_id."'
			    GROUP BY DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d'),EMD.device_id) TBL1
			    GROUP BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d')
			    ORDER BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d') DESC";  	    
		return $this->db->query($sql)->result();



    	/*if($short_name == 'KW' || $short_name == 'Amps'){
    		$field = " SUM(EMD.data_".$short_name.")";
    	}else{
    		$field = " AVG(NULLIF(EMD.data_".$short_name.",0))";
    	}
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,".$field." data,DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time
				FROM master_device MD
				LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE EMD.device_id = ".$device_id." 
				AND EMD.end_date_time >= '".$startDate."' 
				AND EMD.end_date_time < '".$endDate."' 
				GROUP BY DATE_FORMAT(EMD.end_date_time, '%Y%m%d')
				ORDER BY EMD.emd_id DESC,EMD.end_date_time DESC ";
				//echo $sql;
		return $this->db->query($sql)->result();*/
    }

    public function getShiftWiseDataSetChart($device_id = 0, $short_name = '', $startDate = '', $endDate = ''){
    	$field = "EMD.data_".$short_name." ";
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,".$field." data,EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE MD.device_id = ".$device_id."
				AND EMD.end_date_time >= '".$startDate."'
				AND EMD.end_date_time < '".$endDate."'
				ORDER BY EMD.emd_id DESC  ";
		return $this->db->query($sql)->result();
    }


    public function getCurrent(){
    	$sql = "SELECT last_view_date_time end_date_time FROM cron_hist";
    	//$sql = "SELECT max(end_date_time) end_date_time FROM ems_meter_data";
		$data = $this->db->query($sql)->result();
		$data1 = $this->ems_cpp_db->query($sql)->result();
		$sql = "SELECT TIMESTAMPDIFF(MINUTE,'".$data[0]->end_date_time."','".$data1[0]->end_date_time."') aa";
		//echo $sql;
		$minutes = $this->db->query($sql)->result();
		if($minutes[0]->aa < 0){
			return $data1;
		}else{
			return $data;
		}
    }

    public function getCurrent32DataSetPP($device_id = 0, $short_name = '', $type = 'ems'){
    	if(($short_name == 'KW' || $short_name == 'Volt') && ($type != 'ems')){
    		$field = "ABS(EMD.data_".$short_name.") * 1000 ";
    	}else{
    		$field = "EMD.data_".$short_name." ";
    	}
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,".$field." data,EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE MD.device_id = ".$device_id."
				ORDER BY EMD.emd_id DESC    
				LIMIT 32";
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}	
    }

    public function getCurrent32DataSet($device_id = 0, $short_name = '', $type = 'ems'){
    	$field = "EMD.data_".$short_name." ";
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,".$field." data,EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE MD.device_id = ".$device_id."
				ORDER BY EMD.emd_id DESC    
				LIMIT 32";
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}	
    }

    

    public function getDateDetails($type = 'ems'){
    	$sql = "SELECT max(date_format(end_date_time,'%Y-%m-%d')) max_date,min(date_format(end_date_time,'%Y-%m-%d')) min_date 
		FROM ems_meter_data";
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}
		
    }	

    public function getEmsCppLoss($date = '', $ids = ''){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
				    EMD.data_KW data
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$ids.") AND EMD.end_date_time = '".$date."'";
		$sql .= " ORDER BY FIELD(MD.device_id,".$ids.")";	
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getEmsLoss($date = '', $ids = ''){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
				    EMD.data_KW data
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$ids.") AND EMD.end_date_time = '".$date."'";
		$sql .= " ORDER BY FIELD(MD.device_id,".$ids.")";
		//echo $sql;
		return $this->db->query($sql)->result();
    }


    public function getCppDataReportDay($startDate = '', $endDate = ''){
    	$sql = " SELECT 
    				MD.device_id,
    				MD.device_name,
    				MD.type,
				    AVG(abs(EMD.data_KW)) * 1000 KW,
				    AVG(NULLIF(EMD.data_PF,0)) PF,
				    SUM(EMD.data_KW/EMD.data_PF) KWPF,
				    AVG(NULLIF(EMD.data_Volt,0)) * 1000 Volt,
				    AVG(NULLIF(EMD.data_Amps,0)) Amps,
				    AVG(NULLIF(EMD.data_HZ,0)) HZ
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE 1 ";
		if($startDate != ''){
			$sql .= " AND EMD.end_date_time >= '".$startDate."'";
		}	

		if($endDate != ''){
			$sql .= " AND EMD.end_date_time < '".$endDate."'";
		}
		
		$sql .= " GROUP BY MD.device_id ";	
		$sql .= " ORDER BY MD.device_name ASC";
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getCppDataReportShift($startDate = '', $endDate = ''){
    	$sql = " SELECT 
    				MD.device_id,
    				MD.device_name,
    				MD.type,
				    AVG(abs(EMD.data_KW)) * 1000 KW,
				    AVG(NULLIF(EMD.data_PF,0)) PF,
				    SUM(EMD.data_KW/EMD.data_PF) KWPF,
				    AVG(NULLIF(EMD.data_Volt,0)) * 1000 Volt,
				    AVG(NULLIF(EMD.data_Amps,0)) Amps,
				    AVG(NULLIF(EMD.data_HZ,0)) HZ
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE 1 ";
		if($startDate != ''){
			$sql .= " AND EMD.end_date_time >= '".$startDate."'";
		}	

		if($endDate != ''){
			$sql .= " AND EMD.end_date_time < '".$endDate."'";
		}
		
		$sql .= " GROUP BY MD.device_id ";	
		$sql .= " ORDER BY MD.device_name ASC ";
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getShiftWiseDataSetChartCpp($device_id = 0, $short_name = '', $startDate = '', $endDate = ''){
    	if($short_name == 'KW' || $short_name == 'Volt'){
    		$field = "EMD.data_".$short_name." * 1000 ";
    	}else{
    		$field = "EMD.data_".$short_name." ";
    	}
    	
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,abs(".$field.") data,EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE MD.device_id = ".$device_id."
				AND EMD.end_date_time >= '".$startDate."'
				AND EMD.end_date_time < '".$endDate."'
				ORDER BY EMD.emd_id DESC  ";
		//echo $sql;		
		return $this->ems_cpp_db->query($sql)->result();
    }


    public function getDayWiseDataSetChartLineCpp($device_id = 0, $short_name = '', $startDate = '', $endDate = ''){
    	if($short_name == 'KW' || $short_name == 'Volt'){
    		$field = "EMD.data_".$short_name." * 1000 ";
    	}else{
    		$field = "EMD.data_".$short_name." ";
    	}
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,abs(".$field.") data,EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE MD.device_id = ".$device_id."
				AND EMD.end_date_time >= '".$startDate."'
				AND EMD.end_date_time < '".$endDate."'
				ORDER BY EMD.emd_id DESC ";
		//echo $sql;			
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getDayWiseDataSetChartBarCpp($device_id = 0, $short_name = '', $startDate = '', $endDate = ''){
    	if($short_name == 'KW'){
    		$field = " AVG(abs(EMD.data_".$short_name.")) * 1000";
    	}elseif($short_name == 'Volt'){
    		$field = " AVG(NULLIF(EMD.data_".$short_name.",0)) * 1000";
    	}else{
    		$field = " AVG(NULLIF(EMD.data_".$short_name.",0))";
    	}
    	$sql = "SELECT 
				    MD.device_id, MD.device_name,".$field." data,DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time
				FROM master_device MD
				LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE EMD.device_id = ".$device_id." 
				AND EMD.end_date_time >= '".$startDate."' 
				AND EMD.end_date_time < '".$endDate."' 
				GROUP BY DATE_FORMAT(EMD.end_date_time, '%Y%m%d')
				ORDER BY EMD.emd_id DESC,EMD.end_date_time DESC ";
				//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getCppDayDataReportTotal($field = 'KW', $startDate = '', $endDate = ''){
    	if($field  == 'KW'){
    		$fields = " AVG(abs(EMD.data_".$field." )) * 1000 ";
    	}elseif($field  == 'Volt'){
    		$fields = " AVG(NULLIF(EMD.data_".$field.",0)) * 1000 ";
    	}else{
    		$fields = " AVG(NULLIF(EMD.data_".$field.",0)) ";
    	}
    	if($field == 'KW' || $field == 'Amps'){
    		$fieldNew = " SUM(TBL1.data)  ";
    	}else{
    		$fieldNew = " AVG(NULLIF(TBL1.data,0))  ";
    	}
    	$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$fields." data,
				            EMD.end_date_time
				    FROM
				        ems_meter_data EMD
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
			    GROUP BY EMD.end_date_time,EMD.device_id) TBL1
			    GROUP BY TBL1.end_date_time 
			    ORDER BY TBL1.end_date_time DESC";    
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getCppDateRangeDataReportTotal($field = 'KW', $startDate = '', $endDate = ''){
    	if($field  == 'KW'){
    		$fields = " AVG(abs(EMD.data_".$field." )) * 1000 ";
    	}elseif($field  == 'Volt'){
    		$fields = " AVG(NULLIF(EMD.data_".$field.",0)) * 1000 ";
    	}else{
    		$fields = " AVG(NULLIF(EMD.data_".$field.",0)) ";
    	}

    	if($field == 'KW' || $field == 'Amps'){
    		$fieldNew = " SUM(TBL1.data)  ";
    	}else{
    		$fieldNew = " AVG(NULLIF(TBL1.data,0))  ";
    	}
    	$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$fields." data,
				            DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        ems_meter_data EMD
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
			    GROUP BY DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d'),EMD.device_id) TBL1
			    GROUP BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d') 
			    ORDER BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d') DESC";    
		return $this->ems_cpp_db->query($sql)->result();				    
    }

    public function getShiftWiseDataSetChartCppReportTotal($field = 'KW', $startDate = '', $endDate = ''){
    	if($field  == 'KW'){
    		$fields = " AVG(abs(EMD.data_".$field." )) * 1000 ";
    	}elseif($field  == 'Volt'){
    		$fields = " AVG(NULLIF(EMD.data_".$field.",0)) * 1000 ";
    	}else{
    		$fields = " AVG(NULLIF(EMD.data_".$field.",0)) ";
    	}

    	if($field == 'KW' || $field == 'Amps'){
    		$fieldNew = " SUM(TBL1.data)  ";
    	}else{
    		$fieldNew = " AVG(NULLIF(TBL1.data,0))  ";
    	}
    	$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$fields." data,
				            EMD.end_date_time
				    FROM
				        ems_meter_data EMD
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
			    GROUP BY EMD.end_date_time,EMD.device_id) TBL1
			    GROUP BY TBL1.end_date_time 
			    ORDER BY TBL1.end_date_time DESC";    
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getDeviceTypeWiseDateRangeDataReportTotal($startDate = '', $endDate = '', $type = 'G', $typeId = 1, $field = 'KW'){
    	if($field == 'KW'){
    		$fields = "AVG(EMD.data_".$field.")";
    	}else{
    		$fields = "AVG(NULLIF(EMD.data_".$field.",0))";
    	}
    	if($field == 'KW' || $field == 'Amps'){
    		$fieldNew = " SUM(TBL1.data)  ";
    	}else{
    		$fieldNew = " AVG(NULLIF(TBL1.data,0))  ";
    	}
    	$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$fields." data,
				            DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        ems_meter_data EMD
				        JOIN master_device MD ON MD.device_id = EMD.device_id
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
						AND MD.type = '".$type.$typeId."'
			    GROUP BY DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d'),EMD.device_id) TBL1
			    GROUP BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d')
			    ORDER BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d') DESC";  	    
		return $this->db->query($sql)->result();
    }

    public function getDeviceTypeWiseDayDataReportTotal($startDate = '', $endDate = '', $type = 'G', $typeId = 1, $field = 'KW'){
    	if($field == 'KW'){
    		$fields = "AVG(EMD.data_".$field.")";
    	}else{
    		$fields = "AVG(NULLIF(EMD.data_".$field.",0))";
    	}
    	if($field == 'KW' || $field == 'Amps'){
    		$fieldNew = " SUM(TBL1.data)  ";
    	}else{
    		$fieldNew = " AVG(NULLIF(TBL1.data,0))  ";
    	}
    	$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$fields." data,
				            EMD.end_date_time
				    FROM
				        ems_meter_data EMD
				        JOIN master_device MD ON MD.device_id = EMD.device_id
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
						AND MD.type = '".$type.$typeId."'
			    GROUP BY EMD.end_date_time,EMD.device_id) TBL1
			    GROUP BY TBL1.end_date_time 
			    ORDER BY TBL1.end_date_time DESC";    
		return $this->db->query($sql)->result();
    }

    /* This function is used in Report and Dashboard controller*/
    public function getShiftWiseDataSetChartTotal($startDate = '', $endDate = '', $type = 'G', $typeId = 1, $field = 'KW'){
    	if($field == 'KW'){
    		$fields = "AVG(EMD.data_".$field.")";
    	}else{
    		$fields = "AVG(NULLIF(EMD.data_".$field.",0))";
    	}
    	if($field == 'KW' || $field == 'Amps'){
    		$fieldNew = " SUM(TBL1.data)  ";
    	}else{
    		$fieldNew = " AVG(TBL1.data)  ";
    	}
    	$sql = "SELECT 
				    ".$fieldNew." data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$fields." data,
				            EMD.end_date_time
				    FROM
				        ems_meter_data EMD
				        JOIN master_device MD ON MD.device_id = EMD.device_id
				    WHERE 1 AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
						AND MD.type = '".$type.$typeId."'
			    GROUP BY EMD.end_date_time,EMD.device_id) TBL1
			    GROUP BY TBL1.end_date_time 
			    ORDER BY TBL1.end_date_time DESC"; 
		return $this->db->query($sql)->result();
    }

    public function getCurrent32DataSetGenDist($type_text = 'G', $type_level = '1', $short_name = 'KW', $startDate = '', $endDate = ''){
    	if($short_name == 'KW' || $short_name == 'Amps'){
    		$columnIn = "EMD.data_".$short_name;
    		$columnOut = " SUM(TBL1.data) data ";
    	}else{
    		$columnIn = " EMD.data_".$short_name;
    		$columnOut = " AVG(TBL1.data) data ";
    	}
    	$sql = "SELECT 
			    TBL1.type, ".$columnOut.", TBL1.end_date_time
			FROM
			    (SELECT 
			        MD.type, AVG(NULLIF(".$columnIn.", 0)) data, EMD.end_date_time
			    FROM
			        master_device MD
			    JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
			    WHERE
			        1
			            AND EMD.end_date_time >= '".$startDate."'
			            AND EMD.end_date_time < '".$endDate."'
			            AND MD.type = '".$type_text.$type_level."'
			    GROUP BY EMD.end_date_time , MD.device_id) TBL1
			GROUP BY TBL1.end_date_time
			ORDER BY TBL1.end_date_time ASC";
			//echo $sql;
		return $this->db->query($sql)->result();
    }

    public function showPPtotalChart($type = 'ems_cpp', $short_name = 'KW', $startDate = '', $endDate = ''){
    	if($short_name == 'KW' || $short_name == 'Amps'){
    		if($short_name == 'Amps'){
    			$innerSql = " SUM(ABS(EMD.data_".$short_name.")) data ";
    		}else{
    			$innerSql = " SUM(ABS(EMD.data_".$short_name.")) *1000 data ";
    		}
    	}elseif($short_name == 'Volt'){
    		$innerSql = " AVG(NULLIF(EMD.data_".$short_name.", 0)) * 1000 data ";
    	}else{
    		$innerSql = " AVG(NULLIF(EMD.data_".$short_name.", 0)) data ";
    	}

    	$sql = "SELECT 
		    ".$innerSql.", EMD.end_date_time
		FROM
		    ems_meter_data EMD
		WHERE
		    1
		        AND EMD.end_date_time >= '".$startDate."'
		        AND EMD.end_date_time < '".$endDate."'
		GROUP BY EMD.end_date_time";
    	if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}
    }

    public function showGenDistChartTotal($type_text = 'G', $short_name = 'KW', $startDate = '', $endDate = ''){
    	if($short_name == 'KW' || $short_name == 'Amps'){
    		$innerSql = " SUM(ABS(EMD.data_".$short_name."))  data ";
    	}else{
    		$innerSql = " AVG(NULLIF(EMD.data_".$short_name.", 0)) data ";
    	}

    	$sql = "SELECT 
		    ".$innerSql.", EMD.end_date_time
		FROM
			master_device MD
		    JOIN ems_meter_data EMD ON MD.device_id = EMD.device_id
		WHERE
		    1
		        AND EMD.end_date_time >= '".$startDate."'
		        AND EMD.end_date_time < '".$endDate."'
		        AND MD.type_text = '".$type_text."'
		GROUP BY EMD.end_date_time";
    	
		return $this->db->query($sql)->result();
		
    }

    public function showGenDistChartLoss($startDate = '', $endDate = '', $type_text = 'G'){
    	$sql = "SELECT 
		    SUM(ABS(EMD.data_KW))  data , EMD.end_date_time
		FROM
			master_device MD
		    JOIN ems_meter_data EMD ON MD.device_id = EMD.device_id
		WHERE
		    1
		        AND EMD.end_date_time >= '".$startDate."'
		        AND EMD.end_date_time < '".$endDate."'
		        AND MD.type_text = '".$type_text."'
		GROUP BY EMD.end_date_time";
		return $this->db->query($sql)->result();
    }

    public function showCppWillChartLossTotal($startDate = '', $endDate = '', $meterIds = '', $type = 'ems'){
    	if($type == 'ems'){
    		$innerSelect = " SUM(abs(EMD.data_KW)) ";
    	}else{
    		$innerSelect = " SUM(abs(EMD.data_KW)) * 1000 ";
    	}	
    	$sql = "SELECT 
				    ".$innerSelect." data,
				    EMD.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$meterIds.") 
					AND  EMD.end_date_time >= '".$startDate."' 
					AND EMD.end_date_time < '".$endDate."'";
		$sql .= " GROUP BY EMD.end_date_time";	
		//echo $sql;exit();		
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}
    }

    public function getGenDistAllDataReport($startDate = '', $endDate = ''){
    	$sql = "SELECT 
				    TBL1.type_text,
				    TBL1.type_level,
				    SUM(TBL1.KW) KW,
				    SUM(TBL1.sum_KW) sum_KW,
				    AVG(NULLIF(TBL1.PF, 0)) PF,
				    SUM(TBL1.KWPF) KWPF,
				    AVG(NULLIF(TBL1.Volt, 0)) Volt,
				    SUM(TBL1.Amps) Amps,
				    AVG(NULLIF(TBL1.HZ, 0)) HZ
				FROM
				    (SELECT 
				        MD.type,
						MD.type_text,
						MD.type_level,
						AVG(EMD.data_KW) KW,
						SUM(EMD.data_KW) sum_KW,
						AVG(NULLIF(EMD.data_PF, 0)) PF,
						SUM(EMD.data_KW / EMD.data_PF) KWPF,
						AVG(NULLIF(EMD.data_Volt, 0)) Volt,
						AVG(NULLIF(EMD.data_Amps, 0)) Amps,
						AVG(NULLIF(EMD.data_HZ, 0)) HZ
				    FROM
				        master_device MD
				    LEFT JOIN ems_meter_data EMD ON EMD.device_id = MD.device_id
				    WHERE
				        1 AND MD.type != ''
				            AND EMD.end_date_time >= '".$startDate."'
				            AND EMD.end_date_time < '".$endDate."'
				    GROUP BY MD.device_id) TBL1
				GROUP BY TBL1.type
				ORDER BY TBL1.type_text DESC , TBL1.type_level ASC
				";
		return $this->db->query($sql)->result();		
    }

    public function getDeviceWiseDataCppAllReport($startDate = '', $endDate = ''){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
				    AVG(ABS(EMD.data_KW)) KW,
				    AVG(NULLIF(EMD.data_PF,0)) PF,
				    SUM(EMD.data_KW/EMD.data_PF) KWPF,
				    AVG(NULLIF(EMD.data_Volt,0)) Volt,
				    AVG(NULLIF(EMD.data_Amps,0)) Amps,
				    AVG(NULLIF(EMD.data_HZ,0)) HZ
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id
				WHERE 1 
					AND EMD.end_date_time >= '".$startDate."'
					AND EMD.end_date_time < '".$endDate."'
				GROUP BY MD.device_id	
				ORDER BY  MD.device_name ASC";
		return $this->ems_cpp_db->query($sql)->result();
    }


    public function getEmsCppLossAllReport($startDate = '', $endDate = '', $ids = ''){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
				    AVG(ABS(EMD.data_KW)) data
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$ids.") 
					AND EMD.end_date_time >= '".$startDate."'
					AND EMD.end_date_time < '".$endDate."'";
		$sql .= " GROUP BY MD.device_id ORDER BY FIELD(MD.device_id,".$ids.")";	
		//echo $sql;
		return $this->ems_cpp_db->query($sql)->result();
    }

    public function getEmsLossAllReport($startDate = '', $endDate = '', $ids = ''){
    	$sql = "SELECT 
    				MD.device_id,
    				MD.device_name,
				    AVG(EMD.data_KW) data
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$ids.") 
					AND EMD.end_date_time >= '".$startDate."'
					AND EMD.end_date_time < '".$endDate."'";
		$sql .= " GROUP BY MD.device_id ORDER BY FIELD(MD.device_id,".$ids.")";
		//echo $sql;
		return $this->db->query($sql)->result();
    }

    public function getGenDistAllTotalDayDataReportTotal($column = 'KW', $startDate = '', $endDate = '', $type = 'D'){
    	if($column == 'KW'){
    		$columns = "AVG(EMD.data_".$column.")";
    	}else{
    		$columns = "AVG(NULLIF(EMD.data_".$column.",0))";
    	}
    	if($column == 'KW' || $column == 'Amps'){
    		$select = ' SUM(TBL1.data) ';
    	}else{
    		$select = ' AVG(NULLIF(TBL1.data, 0)) ';
    	}
    	$sql = " SELECT TBL1.end_date_time,".$select." data FROM (
    		SELECT 
				EMD.end_date_time,
			    ".$columns." data
			FROM
			    master_device MD
			        LEFT JOIN
			    ems_meter_data EMD ON EMD.device_id = MD.device_id
			WHERE MD.type_text = '".$type."' 
				AND EMD.end_date_time >= '".$startDate."'
				AND EMD.end_date_time < '".$endDate."'
			GROUP BY MD.device_id, EMD.end_date_time) TBL1
					GROUP BY TBL1.end_date_time"; 		
		return $this->db->query($sql)->result();
    }

    public function getGenDistAllTotalDateRangeDataReportTotal($column = 'KW', $startDate = '', $endDate = '', $type = 'D'){
    	if($column == 'KW'){
    		$columns = "AVG(EMD.data_".$column.")";
    	}else{
    		$columns = "AVG(NULLIF(EMD.data_".$column.",0))";
    	}
    	if($column == 'KW' || $column == 'Amps'){
    		$select = ' SUM(TBL1.data) ';
    	}else{
    		$select = ' AVG(NULLIF(TBL1.data, 0)) ';
    	}
    	$sql = "SELECT TBL1.end_date_time,".$select." data FROM (
					SELECT 
					    DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time,
					    ".$columns." data
					FROM
					    master_device MD
					        LEFT JOIN
					    ems_meter_data EMD ON EMD.device_id = MD.device_id
					WHERE
					    MD.type_text = '".$type."'
					    AND EMD.end_date_time >= '".$startDate."'
						AND EMD.end_date_time < '".$endDate."'
					GROUP BY MD.device_id, DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d')
					) TBL1
					GROUP BY TBL1.end_date_time"; 
			//echo $sql;exit;
		return $this->db->query($sql)->result();
    }

    public function showCppWillChartLossTotalAllReport($startDate = '', $endDate = '', $meterIds = '', $type = 'ems', $day = 1){
    	if($type == 'ems'){
    		$innerSelect = " AVG(abs(EMD.data_KW)) ";
    	}else{
    		$innerSelect = " AVG(abs(EMD.data_KW)) * 1000 ";
    	}	

    	if($day == 1){
			$sqlDate = " EMD.end_date_time";
		}else{
			$sqlDate = " DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time";
		}

    	$sql = "SELECT 
				    ".$innerSelect." data,
				     ".$sqlDate." 
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data EMD ON EMD.device_id = MD.device_id";
		$sql .= " WHERE MD.device_id IN (".$meterIds.") 
					AND  EMD.end_date_time >= '".$startDate."' 
					AND EMD.end_date_time < '".$endDate."'";
		if($day == 1){
			$sql .= " GROUP BY EMD.end_date_time";
		}else{
			$sql .= " GROUP BY  DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d')";
		}			
			
		//echo $sql;exit();		
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}
    }

    public function getCppDayDataReportTotalAll($field = 'KW', $startDate = '', $endDate = '', $meterIds = '', $type = 'ems'){
    	if($type == 'ems'){
    		$fields = " abs(EMD.data_".$field.") ";
    	}else{
    		$fields = " abs(EMD.data_".$field.") * 1000 ";
    	}
    	if($field == 'KW'){
    		$newFields = "AVG(".$fields.")";
    	}else{
    		$newFields = "AVG(NULLIF(".$fields.",0))";
    	}
    	
    	$sql = "SELECT 
				    SUM(TBL1.data) data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$newFields." data,
				            EMD.end_date_time
				    FROM
				        ems_meter_data EMD
				    WHERE 1 AND EMD.device_id IN (".$meterIds.") AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
			    GROUP BY EMD.end_date_time,EMD.device_id) TBL1
			    GROUP BY TBL1.end_date_time 
			    ORDER BY TBL1.end_date_time ASC";    
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}
    }

    public function getCppDateRangeDataReportTotalAll($field = 'KW', $startDate = '', $endDate = '', $meterIds = '', $type = 'ems'){
    	if($type == 'ems'){
    		$fields = " abs(EMD.data_".$field.") ";
    	}else{
    		$fields = " abs(EMD.data_".$field.") * 1000 ";
    	}
    	if($field == 'KW'){
    		$newFields = "AVG(".$fields.")";
    	}else{
    		$newFields = "AVG(NULLIF(".$fields.",0))";
    	}
    	$sql = "SELECT 
				    SUM(TBL1.data) data, TBL1.end_date_time
				FROM
				    (SELECT 
				        EMD.device_id,
				            ".$newFields." data,
				            DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d') end_date_time
				    FROM
				        ems_meter_data EMD
				    WHERE 1 AND EMD.device_id IN (".$meterIds.") AND EMD.end_date_time >= '".$startDate."' 
						AND EMD.end_date_time < '".$endDate."'
			    GROUP BY DATE_FORMAT(EMD.end_date_time, '%Y-%m-%d'),EMD.device_id) TBL1
			    GROUP BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d') 
			    ORDER BY DATE_FORMAT(TBL1.end_date_time, '%Y-%m-%d') ASC";    
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}			    
    }

    public function getDeviceTypeWiseKwhDataReport($startDate = '', $endDate = '', $typeId = 1){
    	$sql = "SELECT 
				    MD.device_id,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND (EMDKWH.end_date_time = '".$startDate."'
				        OR EMDKWH.end_date_time = '".$endDate."')
				        AND (MD.type = 'G".$typeId."' OR MD.type = 'D".$typeId."')
				ORDER BY MD.device_id ASC,EMDKWH.end_date_time ASC";
		//echo $sql;		
		return $this->db->query($sql)->result();
    }

    public function getGraphKwhData($startDate = '', $endDate = '', $device_id = 1){
    	$sql = "SELECT 
				    MD.device_id,
				    MD.device_name,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND (EMDKWH.end_date_time = '".$startDate."'
				        OR EMDKWH.end_date_time = '".$endDate."')
				        AND MD.device_id = ".$device_id."
				ORDER BY EMDKWH.end_date_time ASC";
		return $this->db->query($sql)->result();
    }

    public function getGraphKwhDataDay($startDate = '', $endDate = '', $device_id = 1, $type = 'ems'){
    	$sqlSelect = 'EMDKWH.data_kwh';
    	if($type == 'ems_cpp'){
			$sqlSelect = 'EMDKWH.data_kwh * 1000  data_kwh';
		}	
    	$sql = "SELECT 
				    MD.device_id,
				    MD.device_name,
				    ".$sqlSelect." ,
				    EMDKWH.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND EMDKWH.end_date_time >= '".$startDate."'
				        AND EMDKWH.end_date_time <= '".$endDate."'
				        AND TIME(EMDKWH.end_date_time) = '00:00:00'
				        AND MD.device_id = ".$device_id."
				ORDER BY EMDKWH.end_date_time ASC";
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}	
    }

    public function getDeviceWiseKwhDataReport($startDate = '', $endDate = '', $device_ids = '', $type = 'ems'){
    	$sql = "SELECT 
				    MD.device_id,
				    EMDKWH.data_kwh ,
				    EMDKWH.end_date_time
				FROM
				    master_device MD
				        LEFT JOIN
				    ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
				WHERE
				    1
				        AND (EMDKWH.end_date_time = '".$startDate."'
				        OR EMDKWH.end_date_time = '".$endDate."')
				        AND MD.device_id IN (".$device_ids.")
				ORDER BY MD.device_id ASC,EMDKWH.end_date_time ASC";
		//echo $sql;		
		if($type == 'ems'){
			return $this->db->query($sql)->result();
		}else{
			return $this->ems_cpp_db->query($sql)->result();
		}
    }

    public function getDateDiff($startDate = '', $endDate = ''){
    	$sql = "SELECT DATEDIFF('".$endDate."','".$startDate."') as days";
		return $this->db->query($sql)->result();
    }

    public function getAllReportKWHData($startDate = '', $endDate = '', $type = 'ems'){
    	if($type == 'ems'){
    		$sql = "SELECT MD.device_id,EMDKWH.data_kwh,EMDKWH.end_date_time
					FROM master_device MD
					LEFT JOIN ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
					WHERE 1
					    AND (EMDKWH.end_date_time = '".$startDate."' OR EMDKWH.end_date_time = '".$endDate."')
					    AND MD.type != ''
					ORDER BY MD.device_id ASC,EMDKWH.end_date_time ASC";
			//echo $sql;			    
    	}else{
    		$sql = "SELECT MD.device_id,EMDKWH.data_kwh * 1000 data_kwh,EMDKWH.end_date_time
					FROM master_device MD
					LEFT JOIN ems_meter_data_kwh EMDKWH ON EMDKWH.device_id = MD.device_id
					WHERE 1
					    AND (EMDKWH.end_date_time = '".$startDate."' OR EMDKWH.end_date_time = '".$endDate."')
					ORDER BY MD.device_id ASC,EMDKWH.end_date_time ASC";
			//echo $sql;
    	}

    	if($type == 'ems'){
    		return $this->db->query($sql)->result();
    	}else{
    		return $this->ems_cpp_db->query($sql)->result();
    	}

    }
	
	
}