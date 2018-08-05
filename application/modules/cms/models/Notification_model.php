<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notification_model extends CI_Model {
    public $fm_db;
    public $air_db;
    public $ems_db;
    public $ems_cpp_db;
    public $data_log_db;
    public $noti_db;
    public $day;
    function __construct() {
        parent::__construct();
		log_message('INFO', 'account/models/Notification_model enter');
        $this->fm_db = 'welspun_fm';
        $this->air_db = 'welspun_air';
        $this->ems_db = 'welspun_ems';
        $this->ems_cpp_db = 'welspun_ems_cpp';
        $this->data_log_db = 'welspun_datalog';
        $this->noti_db = 'welspun_metererror';
        $this->day = 1;
    }

    public function getAirMeterError(){
        $sql = "SELECT 
                    COUNT(TBL1.meter_id) total_meter
                FROM
                   (SELECT 
                        WMA.meter_id,
                        (SUM(IF(WMA.pressure_alert = 'YES', 1, 0)) + SUM(IF(WMA.temp_alert = 'YES', 1, 0)) + SUM(IF(WMA.flow_alert = 'YES', 1, 0))) total
                    FROM
                        ".$this->noti_db.".air WMA
                    WHERE
                        DATE_FORMAT(WMA.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMA.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMA.meter_id
                    HAVING total > 0) TBL1 ";
                //echo $sql;
        return $this->db->query($sql)->result();        
    }

    public function getDataLogMeterError(){
        $sql = "SELECT 
                    COUNT(TBL1.meter_id) total_meter
                FROM
                   (SELECT 
                        WMA.meter_id,
                        (SUM(IF(WMA.pressure_alert = 'YES', 1, 0)) + SUM(IF(WMA.temp_alert = 'YES', 1, 0)) + SUM(IF(WMA.flow_alert = 'YES', 1, 0))) total
                    FROM
                        ".$this->noti_db.".datalog WMA
                    WHERE
                        DATE_FORMAT(WMA.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMA.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMA.meter_id
                    HAVING total > 0) TBL1 ";
                //echo $sql;
        return $this->db->query($sql)->result();        
    }

    public function getSteamMeterError(){
        $sql = "SELECT 
                    COUNT(TBL1.meter_id) total_meter
                FROM
                    (SELECT 
                        WMS.meter_id,
                        (SUM(IF(WMS.pressure_alert = 'YES', 1, 0)) + SUM(IF(WMS.temp_alert = 'YES', 1, 0)) + SUM(IF(WMS.flow_alert = 'YES', 1, 0))) total
                    FROM
                        ".$this->noti_db.".steam WMS
                    WHERE
                        DATE_FORMAT(WMS.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMS.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMS.meter_id
                    HAVING total > 0) TBL1 ";
        return $this->db->query($sql)->result();        
    }

    public function getEmsMeterError(){
        $sql = "SELECT 
                    COUNT(TBL1.device_id) total_meter
                FROM
                    (SELECT 
                        WME.device_id,
                        SUM(IF(WME.data_HZ_alert = 'YES', 1, 0)) HZ,
                        SUM(IF(WME.data_Volt_alert = 'YES', 1, 0)) Volt,
                        (SUM(IF(WME.data_Amps_alert = 'YES', 1, 0)) + SUM(IF(WME.data_HZ_alert = 'YES', 1, 0)) + SUM(IF(WME.data_KW_alert = 'YES', 1, 0)) + SUM(IF(WME.data_PF_alert = 'YES', 1, 0)) + SUM(IF(WME.data_Volt_alert = 'YES', 1, 0))) total
                    FROM
                        ".$this->noti_db.".ems WME
                    WHERE
                        DATE_FORMAT(WME.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WME.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WME.device_id
                    HAVING total > 0 AND (HZ > 0 OR Volt > 0)) TBL1 ";
                //echo $sql;
        return $this->db->query($sql)->result();        
    }

    public function getEmsCppMeterError(){
        $sql = "SELECT 
                    COUNT(TBL1.device_id) total_meter
                FROM
                    (SELECT 
                        WMEC.device_id,
                        SUM(IF(WMEC.data_HZ_alert = 'YES', 1, 0)) HZ,
                        SUM(IF(WMEC.data_Volt_alert = 'YES', 1, 0)) Volt,
                        (SUM(IF(WMEC.data_Amps_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_HZ_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_KW_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_PF_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_Volt_alert = 'YES', 1, 0))) total
                    FROM
                        ".$this->noti_db.".ems_cpp WMEC
                    WHERE
                        DATE_FORMAT(WMEC.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMEC.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMEC.device_id
                    HAVING total > 0 AND (HZ > 0 OR Volt > 0)) TBL1 ";
            //echo $sql;    
        return $this->db->query($sql)->result();        
    }

    public function getMeterErrorListing_air(){
        $sql = "SELECT 
                    WAAM.name, TBL1.*
                FROM
                    ".$this->air_db.".air_meter WAAM
                        JOIN
                    (SELECT 
                        WMA.meter_id,
                            SUM(IF(WMA.pressure_alert = 'YES', 1, 0)) sum_pressure_alert,
                            SUM(IF(WMA.temp_alert = 'YES', 1, 0)) sum_temp_alert,
                            SUM(IF(WMA.flow_alert = 'YES', 1, 0)) sum_flow_alert,
                            (SUM(IF(WMA.pressure_alert = 'YES', 1, 0)) + SUM(IF(WMA.temp_alert = 'YES', 1, 0)) + SUM(IF(WMA.flow_alert = 'YES', 1, 0))) total, 
                            DATE_FORMAT(WMA.created_on, '%Y-%m-%d')
                    FROM
                        ".$this->noti_db.".air WMA
                    WHERE
                        DATE_FORMAT(WMA.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMA.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMA.meter_id
                    HAVING total > 0) TBL1 ON TBL1.meter_id = WAAM.meter_id
                        ORDER BY TBL1.total DESC,WAAM.name ASC
                ";
        return $this->db->query($sql)->result();        
    }

    public function getMeterErrorListing_data_log(){
        $sql = "SELECT 
                    WAAM.name, TBL1.*
                FROM
                    ".$this->data_log_db.".datalog_meter WAAM
                        JOIN
                    (SELECT 
                        WMA.meter_id,
                            SUM(IF(WMA.pressure_alert = 'YES', 1, 0)) sum_pressure_alert,
                            SUM(IF(WMA.temp_alert = 'YES', 1, 0)) sum_temp_alert,
                            SUM(IF(WMA.flow_alert = 'YES', 1, 0)) sum_flow_alert,
                            (SUM(IF(WMA.pressure_alert = 'YES', 1, 0)) + SUM(IF(WMA.temp_alert = 'YES', 1, 0)) + SUM(IF(WMA.flow_alert = 'YES', 1, 0))) total, 
                            DATE_FORMAT(WMA.created_on, '%Y-%m-%d')
                    FROM
                        ".$this->noti_db.".datalog WMA
                    WHERE
                        DATE_FORMAT(WMA.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMA.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMA.meter_id
                    HAVING total > 0 ) TBL1 ON TBL1.meter_id = WAAM.id
                        ORDER BY TBL1.total DESC,WAAM.name ASC
                ";
        return $this->db->query($sql)->result();        
    }

    public function getMeterErrorListing_steam(){
        $sql = "SELECT 
                    WASM.name, TBL1.*
                FROM
                    ".$this->fm_db.".steam_meter WASM
                        JOIN
                    (SELECT 
                        WMS.meter_id,
                            SUM(IF(WMS.pressure_alert = 'YES', 1, 0)) sum_pressure_alert,
                            SUM(IF(WMS.temp_alert = 'YES', 1, 0)) sum_temp_alert,
                            SUM(IF(WMS.flow_alert = 'YES', 1, 0)) sum_flow_alert,
                            (SUM(IF(WMS.pressure_alert = 'YES', 1, 0)) + SUM(IF(WMS.temp_alert = 'YES', 1, 0)) + SUM(IF(WMS.flow_alert = 'YES', 1, 0))) total,
                            DATE_FORMAT(WMS.created_on, '%Y-%m-%d')
                    FROM
                        ".$this->noti_db.".steam WMS
                    WHERE
                        DATE_FORMAT(WMS.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMS.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMS.meter_id
                    HAVING total > 0 ) TBL1 ON TBL1.meter_id = WASM.meter_id
                        ORDER BY TBL1.total DESC,WASM.name ASC
                ";
        return $this->db->query($sql)->result();        
    }

    public function getMeterErrorListing_ems(){
        $sql = "SELECT 
                    WSMD.device_name name, TBL1.*
                FROM
                    ".$this->ems_db.".master_device WSMD
                        JOIN
                    (SELECT 
                        WME.device_id,
                            SUM(IF(WME.data_Amps_alert = 'YES', 1, 0)) Amps,
                            SUM(IF(WME.data_HZ_alert = 'YES', 1, 0)) HZ,
                            SUM(IF(WME.data_KW_alert = 'YES', 1, 0)) KW,
                            SUM(IF(WME.data_PF_alert = 'YES', 1, 0)) PF,
                            SUM(IF(WME.data_Volt_alert = 'YES', 1, 0)) Volt,
                            (SUM(IF(WME.data_Amps_alert = 'YES', 1, 0)) + SUM(IF(WME.data_HZ_alert = 'YES', 1, 0)) + SUM(IF(WME.data_KW_alert = 'YES', 1, 0)) + SUM(IF(WME.data_PF_alert = 'YES', 1, 0)) + SUM(IF(WME.data_Volt_alert = 'YES', 1, 0))) total,
                            DATE_FORMAT(WME.created_on, '%Y-%m-%d')
                    FROM
                        ".$this->noti_db.".ems WME
                    WHERE
                        DATE_FORMAT(WME.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WME.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WME.device_id
                    HAVING total > 0 AND (HZ > 0 OR Volt > 0) ) TBL1 ON TBL1.device_id = WSMD.device_id
                        ORDER BY TBL1.total DESC,WSMD.device_name";
                //echo $sql;
        return $this->db->query($sql)->result();        
    }

    public function getMeterErrorListing_ems_cpp(){
        $sql = "SELECT 
                    WSMD.device_name name, TBL1.*
                FROM
                    ".$this->ems_cpp_db.".master_device WSMD
                        JOIN
                    (SELECT 
                        WMEC.device_id,
                            SUM(IF(WMEC.data_Amps_alert = 'YES', 1, 0)) Amps,
                            SUM(IF(WMEC.data_HZ_alert = 'YES', 1, 0)) HZ,
                            SUM(IF(WMEC.data_KW_alert = 'YES', 1, 0)) KW,
                            SUM(IF(WMEC.data_PF_alert = 'YES', 1, 0)) PF,
                            SUM(IF(WMEC.data_Volt_alert = 'YES', 1, 0)) Volt,
                            (SUM(IF(WMEC.data_Amps_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_HZ_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_KW_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_PF_alert = 'YES', 1, 0)) + SUM(IF(WMEC.data_Volt_alert = 'YES', 1, 0))) total,
                            DATE_FORMAT(WMEC.created_on, '%Y-%m-%d')
                    FROM
                        ".$this->noti_db.".ems_cpp WMEC
                    WHERE
                        DATE_FORMAT(WMEC.created_on, '%Y-%m-%d') <= CURDATE()
                            AND DATE_FORMAT(WMEC.created_on, '%Y-%m-%d') >= DATE_ADD(CURDATE(), INTERVAL - ".$this->day." DAY)
                    GROUP BY WMEC.device_id
                    HAVING total > 0 AND (HZ > 0 OR Volt > 0)) TBL1 ON TBL1.device_id = WSMD.device_id
                        ORDER BY TBL1.total DESC,WSMD.device_name";
            //echo $sql;    
        return $this->db->query($sql)->result();        
    }
    
    
}
