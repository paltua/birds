<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ConvertJson extends MX_Controller 
{
    /*
     * Constructor
    */
    public function __construct(){
        parent::__construct();

        // Allow some methods?
        $allowed = array(
            'index'
        );

        
    }
    
    

    /*
    * function index()
    * This function is used to insert data in database
    */
    public function index() 
    {  
        $sql = "SELECT * FROM `unit_recomendation_raw_data` ORDER BY id LIMIT 10; ";
        //$table = 'unit_recomendation_raw_data';
        //$fields = "*";
        $row = $this->tbl_generic_model->ExecuteQuery($sql); 
        $tbl_insert_field ='';
        $tbl_data_str ='';


        foreach ($row as $key => $value) {
            //echo $value->unit_id.'</br>';
            pr($value->rawdatas);
            $rawData = json_decode($value->rawdatas);
        }
    }

   


    
    
}