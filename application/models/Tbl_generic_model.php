<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tbl_generic_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    

    function get($table,$fields,$where='',$orderby='',$perpage=0,$start=0,$one=false,$array='false'){
        $this->db->select($fields);
        $this->db->from($table);

        if($perpage==0 && $start==0) {

        } else {
            $this->db->limit($perpage,$start);
        }    
        
        if($where) {
			$this->db->where($where);
        }

        if($orderby){
            $orderby_str = '';
            foreach ($orderby as $key => $value) {
                $orderby_str .= $key." ".$value.",";
            }
            $orderby_str = substr($orderby_str, 0,-1);
            $this->db->order_by($orderby_str);
        }

        
        
        $query = $this->db->get();
        //echo $this->db->last_query();
        //exit();
       
       //return $result = ( ! $one) ? $query->result() : $query->row();

		if( ! $one){
            if($array === true) {
                $result = $query->result_array();
            } else {
                $result = $query->result();
            }
		} else {
            $result = $query->row() ; 
		}
        return $result;
    }
    
    function add($table,$data){
        $this->db->set($data);
        $this->db->insert($table,$data);
        //echo $this->db->last_query();
		if ($this->db->affected_rows() == '1') return $this->db->insert_id();		
		return FALSE;      
    }

    function add_batch($table,$data){
        $this->db->insert_batch($table, $data);
        return true;
    }


    
    function edit($table,$data,$where=array()){
	   
        $this->db->set($data);
        $this->db->update($table, $data, $where);
        // echo $this->db->last_query();
        // exit();
        if ($this->db->affected_rows() >= 0) return TRUE;
			
		return FALSE;       
    }
	
	
	function delete($table,$where=array()){
        $this->db->delete($table, $where);
        //echo $this->db->last_query();
        // die;
      
        if ($this->db->affected_rows() == '1') return TRUE;
		
				return FALSE;        
    }   
	
	function count($table){
		return $this->db->count_all($table);
	}

    function countWhere($table = '', $where = array()){
        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->db->from($table);
        return $this->db->count_all_results();
    }
	
	function ExecuteQuery($queryStatement, $result_type='object') {
		$q = $this->db->query($queryStatement);
	  // echo $this->db->last_query();
		if(is_object($q)) {
			if($result_type == 'object')
				return $q->result();
			else
			  return $q->result_array();
		} else {
			return $q;
		}
	}


    function callProcedure( $procedure ){
        $result = @$this->db->conn_id->query( $procedure );

        while ( $this->db->conn_id->more_results() && $this->db->conn_id->next_result() )
        {
            //free each result.
            $not_used_result = $this->db->conn_id->use_result();

            if ( $not_used_result instanceof mysqli_result )
            {
                $not_used_result->free();
            }
        }

        return $result;
    }

    /*Get loss details*/
    public function callProcedureListing($procedure){
        $query = $this->db->query($procedure);
        $data = $query->result();
        $this->callAfterProcedure($query);
        return $data;  
    }

    /*get common function*/
    public function callAfterProcedure($query){
        $query->next_result();
        $query->free_result();
    }

    public function truncate($table = ''){
        $this->db->from($table);
        $this->db->truncate();
        return true;
    }

    public function sendEmail($to = '', $subject = '', $body = '', $cc = array(), $bcc = array()){
        $this->load->library('email');
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);
        $this->email->from('info@parrotdipankar.com', 'Parrot Dipankar');
        $this->email->to($to);
        if(count($cc) > 0){
            $this->email->cc($cc);
        }

        if(count($bcc) > 0){
            $this->email->bcc($bcc);
        }

        $this->email->subject($subject);
        $this->email->message($body);

        $this->email->send();
        
    }
}