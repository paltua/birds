<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tbl_client_model extends CI_Model {
    /* Here all org_id is equal to account_id */

    function __construct() {
        parent::__construct();
    }

    /*
     * function get()
     * This is used to get Organization
     */

    public function get($search = array(), $perpage = 0, $start = 0) {
        $this->db->select('OM.org_id, OM.org_name, OM.client_status,
                          OM.subscription_status, UM.user_id,
                          UM.user_name, UM.email');
        $this->db->from('org_master OM');
        $this->db->join('user_org_relation UOR', 'UOR.org_id = OM.org_id');
        $this->db->join('web_user_master UM', 'UM.user_id = UOR.user_id');
        $this->db->join('user_role_relation URR', 'URR.user_id = UM.user_id');
        $this->_setSearchWhere($search);
        $this->db->order_by('OM.org_id', 'DESC');
        $this->db->limit($perpage, $start);
        $query = $this->db->get();
        return $query->result();
    }

    /*
     * function getTotal()
     * This is used to get total Organization
     */

    public function getTotal($search = array()) {
        $this->db->select('COUNT(OM.org_id) total');
        $this->db->from('org_master OM');
        $this->db->join('user_org_relation UOR', 'UOR.org_id = OM.org_id');
        $this->db->join('web_user_master UM', 'UM.user_id = UOR.user_id');
        $this->db->join('user_role_relation URR', 'URR.user_id = UM.user_id');
        $this->_setSearchWhere($search);
        $query = $this->db->get();
        $result = $query->result();
        return $result[0]->total;
    }

    /*
     * function _setSearchWhere()
     * This is used to set search condition
     */

    private function _setSearchWhere($search = array()) {
        if (count($search) > 0) {
            foreach ($search as $key => $val) {
                if ($val != '') {
                    $this->db->where($key, $val);
                }
            }
        }
        $this->db->where('URR.role_id', 2);
    }

    /*
     * function getSingleClient()
     * This is used to get single client
     */

    public function getSingleClient($org_id = 0) {
        $this->db->select('OM.org_id, OM.org_name, OM.client_status, 
                          OM.subscription_status, OM.phone, UM.user_id, UM.user_name, UM.email, UM.pwd');
        $this->db->from('org_master OM');
        $this->db->join('user_org_relation UOR', 'UOR.org_id = OM.org_id');
        $this->db->join('web_user_master UM', 'UM.user_id = UOR.user_id');
        $this->db->join('user_role_relation URR', 'URR.user_id = UM.user_id');
        $this->db->where('OM.org_id', $org_id);
        $this->db->where('URR.role_id', 2);
        $query = $this->db->get();
        return $query->result();
    }

    /*
     * function getOrganizationAjax()
     * This is used to get total Account Admin 
     */

    public function getOrganizationAjax() {
        $sql = "SELECT OM.org_id
                FROM org_master OM
                JOIN user_org_relation UOR ON UOR.org_id = OM.org_id
                JOIN web_user_master UM ON UM.user_id = UOR.user_id
                JOIN user_role_relation URR ON URR.user_id = UM.user_id
                WHERE 1 AND URR.role_id = 2";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    /*
     * function getOrganizationAjaxSearch()
     * This is used to get Account Admin 
     */

    public function getOrganizationAjaxSearch($searchData = '', $orderBy = array(), $limit = array()) {
        $sql = "SELECT OM.org_id, OM.org_name, OM.client_status, OM.subscription_status,
                        UM.user_id, UM.user_name, UM.email
                FROM org_master OM
                JOIN user_org_relation UOR ON UOR.org_id = OM.org_id
                JOIN web_user_master UM ON UM.user_id = UOR.user_id
                JOIN user_role_relation URR ON URR.user_id = UM.user_id
                WHERE 1 AND URR.role_id = 2 ";
        if ($searchData != '') {
            $sql .= " AND (OM.org_name LIKE '%" . $searchData . "%'
                            OR UM.user_name LIKE '%" . $searchData . "%'
                            OR UM.email LIKE '%" . $searchData . "%'
                            )";
        }
        $sql .= " ORDER BY " . $orderBy['col'] . " " . $orderBy['val'];
        $sql .= " LIMIT " . $limit['start'] . ", " . $limit['perpage'];
        $query = $this->db->query($sql);
        $data['total'] = $query->num_rows();
        $data['rows'] = $query->result();
        return $data;
    }

    /*
     * This is used to get assign services of Account Admin 
     */

    public function getAssignServices($org_id = 0) {
        $this->db->select('SM.service_id,SM.name,SM.service_expiry,SMR.id,SMR.org_id,SMR.expiry_days,SMR.units,SMR.points,SMR.total_points,SMR.cm_id');
        $this->db->from('service_master SM');
        $this->db->join('service_account_relation SMR', 'SMR.service_id=SM.service_id AND SMR.org_id = ' . $org_id, 'LEFT');
        $query = $this->db->get();
        return $query->result();
    }

    /*
     * This is used to get point details of Account Admin 
     */

    public function getPointDetails($org_id = 0) {
        $this->db->select("sum(IF(PH.action = 'add', PH.point, 0)) total_add");
        $this->db->from("point_history PH");
        $this->db->where("PH.org_id", $org_id);
        //$this->db->group_by("PH.org_id");
        return $this->db->get()->result();
    }

    /*
     * This is used to get points used of Account Admin 
     */

    public function getPointUsed($org_id = 0) {
        $this->db->select("sum(IF(PH.action = 'minus', PH.point, 0)) total_minus");
        $this->db->from("org_parent_child_relation OPCR");
        $this->db->join("point_history PH", "PH.org_id = OPCR.org_id");
        $this->db->where("OPCR.parent_org_id", $org_id);
        //$this->db->group_by("PH.org_id");
        return $this->db->get()->result();
    }

    /*
     * This is used to get parent id of child
     */

    public function getClientAdminId($to_org_id = 0) {
        $retData = 0;
        $role_id = 2;
        $this->db->select('UOG.user_id');
        $this->db->from('user_org_relation UOG');
        $this->db->join('user_role_relation URR', 'URR.user_id=UOG.user_id');
        $this->db->where('UOG.org_id', $to_org_id);
        $this->db->where('URR.role_id', $role_id);
        $data = $this->db->get()->result();
        if (!empty($data)) {
            $retData = $data[0]->user_id;
        }
        return $retData;
    }

    /*
     * This is used to get parent id of child
     */

    public function deleteMotorDetail($motor_id) {
        $proc = "CALL  	deleteMotorDetails  (" . $motor_id . ", @msg);";
        $this->db->query($proc);
        $msg = "SELECT @msg";
        $query = $this->db->query($msg);
        $responce = $query->row_array();        
        return $responce['@msg'];
    }

}
