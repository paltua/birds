<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Animal_master extends MY_Controller 
{
    public $controller;
    public function __construct(){
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->load->library('Template');
        $this->_user_id = trim($this->session->userdata('aum_id'));
        $this->controller = $this->router->fetch_class();
        $this->load->model($this->controller.'_model');
    }
    
    
    public function index() 
    {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg');
        $data['lang'] = getLanguageArrAnimalMaster();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $data['list'] = array();//$this->animal_master_model->getAllData();
        $data['dataTableUrl'] = base_url('admin/'.$this->controller.'/viewListDataTable');
        //pr($data['list']);
        $data['controller'] = $this->controller;
        $this->template->setTitle('Admin : Pets and Pet Accessories');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/index',$data);
    }

    public function viewListDataTable(){
        $requestData = $this->input->post();
        $columns = array(
            0 => 'AM.am_code',
            2 => 'AMD.amd_name',
            3 => 'AMD.amd_price',
            5 => 'AM.am_viewed_count',
            6 => 'AM.am_status',
            7 => 'AM.am_dip_choice',
            8 => 'AM.am_created_date'
        );
        if (!isset($requestData['order'][0]['column'])) {
            $orderBy['col'] = 'AM.am_created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit['start'] = $requestData['start'];
        $limit['perpage'] = $requestData['length'];
        $searchData = trim($requestData['search']['value']);
        $where['AM.am_deleted'] = '0';
        $where['AMD.language'] = 'en';
        $recordsTotal = $this->animal_master_model->getDataTableTotalCount($where);
        $recordsFiltered = $this->animal_master_model->getDataTableFilteredCount($searchData, $where);
        $rowsData = $this->animal_master_model->getDataTableData($searchData, $where, $orderBy, $limit);

        $rows = $this->_getArrayData($rowsData);
        //print_r($rows);
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval($recordsTotal), // total number of records
            "recordsFiltered" => intval($recordsFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $rows  // total data array
        );
        echo json_encode($json_data);
        exit;
    }

    /*
     * function _getArrayData()
     * This function is used to make array for client listing page
     */

    private function _getArrayData($data = array()) {
        $rows = array();
        if (count($data) > 0) {
            /*pr($data);*/
            foreach ($data as $value) {
                $actionStr = '';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/image/'.$value->am_id.'" class="btn btn-warning btn-xs"><i class="fa fa-picture-o"></i> Image</a>';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/edit/'.$value->am_id.'" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/delete/'.$value->am_id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                
                $statusStr = '<a class="statusChange btn btn-'.($value->am_status == "active"?"info":"warning").' btn-xs" href="javascript:void(0);" title="Click to change Status" value="'.($value->am_status == "active"?"unlock":"lock").'" id="status_'.$value->am_id.'" name="'.$value->am_id.'"><i id="i_status_'.$value->am_id.'" class="fa fa-'.($value->am_status == "active"?"unlock":"lock").'"></i><span id="span_status_'.$value->am_id.'">'.ucfirst($value->am_status).'</span>'; 

                $code = '#'.$value->am_code;
                if($value->days < 8){
                    $code .= '<br><span class="badge badge-pill badge-danger">New</span><br>';
                }
                $code .= '<span class="badge badge-pill badge-danger">Active for '.$value->days.' Days</span><br>';
                if($value->buy_or_sell == 'buy'){
                    $code .= '<span class="badge badge-pill badge-warning"><i class="fa fa-tag"></i>To '.ucfirst($value->buy_or_sell).'</span><br>';
                }elseif($value->buy_or_sell == 'sell'){
                    $code .= '<span class="badge badge-pill badge-warning"><i class="fa fa-tag"></i>For '.ucfirst($value->buy_or_sell).'</span><br>';
                }
                
                $nestedData[] = $code;
                $img = '';
                if($value->default_image != ''){
                    if($value->am_is_booked == 'yes'){
                        $img .= '<span class="booked"></span>';
                    }
                    $img .= '<img height="125" width="125" src="'.base_url('uploads/animal/thumb/'.$value->default_image).'">';
                }
                $nestedData[] = $img;
                $nestedData[] = $value->amd_name;
                $nestedData[] = $value->amd_short_desc; 
                $nestedData[] = $value->amd_price; 
                if($value->am_user_type == 'user'){
                    $nestedData[] = '<i class="fa fa-user"></i>'.$value->user_name.'<br>
                            <i class="fa fa-phone"></i>'.$value->mobile.'<br>
                            <i class="fa fa-at"></i>'.$value->email.'<br>';
                 }else{
                    $nestedData[] = 'Admin';
                }
                        
                
                $nestedData[] = $value->am_viewed_count;
                $nestedData[] = $statusStr;
                $dipChoiceStatus = '';
                if($value->am_dip_choice == 'yes'){
                    $dipChoiceStatus = 'checked';
                }
                $dipPetStatus = '';
                if($value->am_pet_choice == 'yes'){
                    $dipPetStatus = 'checked';
                }
                $dipFoodStatus = '';
                if($value->am_food_choice == 'yes'){
                    $dipFoodStatus = 'checked';
                }
                $nestedData[] = '<p><input class="dipChoice" type="checkbox" name="" value="'.$value->am_id.'" '.$dipChoiceStatus.'>Dip choice</p><p>
                <input class="petChoice" type="checkbox" name="" value="'.$value->am_id.'" '.$dipPetStatus.'>Pets</p>
                <input class="foodChoice" type="checkbox" name="" value="'.$value->am_id.'" '.$dipFoodStatus.'>Foods</p>';
                $nestedData[] = date("F j, Y, g:i a", strtotime($value->am_created_date));
                $nestedData[] = $actionStr;
                $rows[] = $nestedData;
                unset($actionStr);unset($statusStr);unset($nestedData);
            }
        }
        return $rows;
    }

    public function add() {
        $am_id = 0;
        $data = array();
        $data['page_title'] = 'Dashboard';
        $status = '';
        $msg = '';
        $data['lang'] = getLanguageArrAnimalMaster();
        $this->load->library('form_validation');
        foreach($data['lang'] as $key => $value){
            $this->form_validation->set_rules('amd_name['.$key.']', 'Name in '.$value, 'required|trim');
            $this->form_validation->set_rules('amd_price['.$key.']', 'Price in '.$value, 'required|trim');
            $this->form_validation->set_rules('amd_short_desc['.$key.']', 'Short Description in '.$value, 'required|trim');  
        }
        $this->form_validation->set_rules('p_acr', 'Parent category', 'required|trim');
        $this->form_validation->set_rules('country_id', 'Country', 'required|trim');
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('amd_name');
            $nameCheck = $this->name_check($nameArr['en'], $am_id);
            if($nameCheck){
                $shortDescArr = $this->input->post('amd_short_desc');
                $priceArr = $this->input->post('amd_price');
                $maData['am_status'] = 'active';
                $maData['am_title'] = url_title($nameArr['en']);
                $maData['am_viewed_count'] = 0;
                $insertId = $this->tbl_generic_model->add('animal_master', $maData);
                $this->_updateProductCode($insertId);
                $inData = array();
                if(count($nameArr)){
                    foreach ($nameArr as $key => $value) {
                        $inData[] = array(
                            'language' => $key,
                            'am_id' => $insertId,
                            'amd_name' => $value,
                            'amd_price' => $priceArr[$key],
                            'amd_short_desc' => $shortDescArr[$key],
                        );
                    }
                    $this->tbl_generic_model->add_batch('animal_master_details', $inData);
                    $this->_addCategory($insertId);
                    $this->_addUpdateLocation($insertId, 'add');
                }
                $status = 'success';
                $msg = 'Successfully Added';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'admin/'.$this->controller);
            }else{
                $status = 'danger';
                $msg = 'This name is already used in English';
            }
        }
        $data['animal_cat'] = $this->animal_master_model->getAllAnimalParentCategory(0);
        $data['country'] = $this->animal_master_model->getCountryList();
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Pets and Pet Accessories Add');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/add',$data);
    }

    public function edit($am_id = 0) {
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = '';
        $msg = '';
        $data['editData'] = $this->animal_master_model->getSingle($am_id);
        $this->load->library('form_validation');
        foreach($data['editData'] as $key => $value){
            $this->form_validation->set_rules('data['.$value->amd_id.'][amd_name]', 'Name in '.$value->lang_name, 'required|trim');
        }
        $this->form_validation->set_rules('p_acr', 'Parent category', 'required|trim');
        $this->form_validation->set_rules('country_id', 'Country', 'required|trim');
        if ($this->form_validation->run() == TRUE){
            $nameArr = $this->input->post('data');
            $eng_lang_id = $this->input->post('eng_lang_id');
            $nameCheck = $this->name_check($nameArr[$eng_lang_id]['amd_name'], $am_id);
            if($nameCheck){
                $amData['am_title'] = url_title($nameArr[$eng_lang_id]['amd_name']);
                $amWhere['am_id'] = $am_id;
                $this->tbl_generic_model->edit('animal_master', $amData, $amWhere);
                if(count($nameArr)){
                    foreach ($nameArr as $key => $value) {
                        $where = $upData = array();
                        $where['amd_id'] = $key;
                        $upData['amd_name'] = $value['amd_name'];
                        $upData['amd_price'] = $value['amd_price'];
                        $upData['amd_short_desc'] = $value['amd_short_desc'];
                        $this->tbl_generic_model->edit('animal_master_details', $upData, $where);
                    }
                    $this->_addCategory($am_id);
                    $this->_addUpdateLocation($am_id, 'edit');
                }
                $status = 'success';
                $msg = 'Successfully Updated';
                $this->session->set_flashdata('status', $status);
                $this->session->set_flashdata('msg', $msg);
                redirect(base_url().'admin/'.$this->controller);
            }else{
                $status = 'danger';
                $msg = 'This name is already used in English';
            }
        }
        //pr($data);
        //$data['editData'] = $this->animal_master_model->getSingle($am_id);
        $data['country'] = $this->animal_master_model->getCountryList();
        $data['state'] = $this->animal_master_model->getStateList($data['editData'][0]->country_id);
        $data['city'] = $this->animal_master_model->getCityList($data['editData'][0]->state_id);
        $data['animal_cat'] = $this->animal_master_model->getAllAnimalParentCategory(0);
        $data['animal_child_cat'] = $this->animal_master_model->getAllAnimalChildCategory($am_id);
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Pets and Pet Accessories Update');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/edit',$data);
    }

    private function _addCategory($am_id = 0){
        $where['am_id'] = $am_id;
        $this->tbl_generic_model->delete('animal_category_relation', $where);
        $data = $this->input->post('acr');
        $inData = array();
        $p_acr = $this->input->post('p_acr');
        if($p_acr > 0){
            $inData[] = array(
                'am_id' => $am_id,
                'acm_id' => $p_acr
            );
        }
        if(count($data) > 0){
            foreach ($data as $key => $value) {
                $inData[] = array(
                    'am_id' => $am_id,
                    'acm_id' => $value
                );
            }
        }
        if(count($inData) > 0){
            $this->tbl_generic_model->add_batch('animal_category_relation', $inData);
        }
        return true;
    }

    private function _addUpdateLocation($am_id = 0, $action = 'add'){
        $location['country_id'] = $this->input->post('country_id');
        $location['state_id'] = $this->input->post('state_id');
        $location['city_id'] = $this->input->post('city_id');
        if($action == 'add'){
            $location['am_id'] = $am_id;
            $this->tbl_generic_model->add('animal_location', $location);
        }else{
            $where['am_id'] = $am_id;
            $this->tbl_generic_model->edit('animal_location', $location, $where);
        }
    }

    public function delete($id = 0) {
        $where['am_id'] = $id;
        $data['am_deleted'] = '1';
        $status = 'success';
        $msg = 'Successfully Deleted';
        $this->tbl_generic_model->edit('animal_master', $data, $where);
        $this->session->set_flashdata('status', $status);
        $this->session->set_flashdata('msg', $msg);
        redirect(base_url().'admin/'.$this->controller);
    }


    public function name_check($str, $am_id = 0){
        $data = url_title($str);
        $ret = $this->animal_master_model->check_name_url($data, $am_id);
        if ($ret > 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }


    public function image($am_id = 0){
        $data = array();
        $data['controller'] = $this->controller;
        $data['page_title'] = 'Dashboard';
        $status = $this->session->flashdata('status');
        $msg = $this->session->flashdata('msg'); 
        $data['editData'] = $this->animal_master_model->getSingle($am_id);
        /*$this->load->library('form_validation');
        $this->form_validation->set_rules('filename', 'File Name', 'required|trim');*/
        $this->_upload($am_id);
        $data['am_id'] = $am_id;
        $data['list'] = $this->animal_master_model->getImageList($am_id);
        $data['msg'] = $this->template->getMessage($status, $msg);
        $this->template->setTitle('Admin : Pets and Pet Accessories Image');
        $this->template->setLayout('dashboard');    
        $this->template->homeAdminRender('admin/'.$this->controller.'/image',$data);
    }

    private function _upload($am_id = 0){
        $config['upload_path']          = UPLOAD_PROD_PATH;
        $config['allowed_types']        = 'gif|jpg|png';
        /*$config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;*/
        $config['file_name']            = date('YmdHis').$am_id;
        $this->load->library('upload', $config);
        $this->load->library('image_lib');
        if($_FILES){
            if ( ! $this->upload->do_upload('myFile')){
                $this->session->set_flashdata('status', 'danger');
                $this->session->set_flashdata('msg', $this->upload->display_errors());
            }else{
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('msg', 'Successfully Uploaded');
                $inData['ami_path'] = $path = $this->upload->data('file_name');
                $inData['am_id'] = $am_id;
                $this->_resizeImage($path);
                $this->tbl_generic_model->add('animal_master_images', $inData);
                redirect(base_url().'admin/'.$this->controller.'/image/'.$am_id);
            }
        }
    }

    private function _resizeImage($imageName = ''){
        $config['image_library'] = 'gd2';
        $config['source_image'] = UPLOAD_PROD_PATH.$imageName;
        $config['new_image'] = UPLOAD_PROD_PATH.'thumb';
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 250;
        $config['height']       = 250;

        //$this->load->library('image_lib', $config);
        $this->image_lib->initialize($config);  
        $this->image_lib->resize();
    }


    public function image_delete($ami_id = 0){
        $where['ami_id'] = $ami_id;
        $data = $this->tbl_generic_model->get('animal_master_images','*', $where);
        if(!empty($data)){
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg', 'Successfully Deleted');
            $this->tbl_generic_model->delete('animal_master_images', $where);
            @unlink(UPLOAD_PROD_PATH.$data[0]->ami_path);
            @unlink(UPLOAD_PROD_PATH.'thumb/'.$data[0]->ami_path);
            $am_id = $data[0]->am_id;
            redirect(base_url().'admin/'.$this->controller.'/image/'.$am_id);
        }else{
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg', 'Wrong Parameter');
            redirect(base_url().'admin/'.$this->controller.'/index');
        }
    }

    public function getChildCategory(){
        $parent_id = $this->input->post('parent_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $childCat = $this->animal_master_model->getAllAnimalParentCategory($parent_id);
        $html = '<option value="">Select</option>';
        if(count($childCat) > 0){
            foreach ($childCat as $value) {
                $selected = '';
                if(in_array($value->acm_id, $childArr)){
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->acm_id.'" '.$selected.'>'.$value->acmd_name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }

    public function setDefaultImage(){
        $am_id = $this->input->post('am_id');
        $ami_id = $this->input->post('ami_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully set the default Image.');
        $this->animal_master_model->setDefaultImage($am_id, $ami_id);
        echo json_encode($data);
    }

    public function changeStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully changed the status.');
        $this->animal_master_model->changeStatus($am_id);
        echo json_encode($data);
    }

    public function changeDipankarChoiceStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully updated to the choice list of Dipankar.');
        $this->animal_master_model->changeDipankarChoiceStatus($am_id);
        echo json_encode($data);
    }
    public function changePetChoiceStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully updated this product for Pet listing');
        $this->animal_master_model->changePetChoiceStatus($am_id);
        echo json_encode($data);
    }
    public function changeFoodChoiceStatus(){
        $am_id = $this->input->post('am_id');
        $data['msg'] = $this->template->getMessage('success', 'Successfully updated this product for Foods & Accessories listing.');
        $this->animal_master_model->changeFoodChoiceStatus($am_id);
        echo json_encode($data);
    }

    private function _updateProductCode($am_id = 0){
        $lentgh = strlen($am_id);
        $code = "P";
        for ($i=0; $i < 8-$lentgh; $i++) { 
            $code .= '0';
        }
        $inData['am_code'] = $code.$am_id;
        $where['am_id'] = $am_id;
        $this->tbl_generic_model->edit('animal_master', $inData, $where);
    }

    public function getStateList(){
        $country_id = $this->input->post('country_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $state = $this->animal_master_model->getStateList($country_id);
        $html = '<option value="">Select</option>';
        if(count($state) > 0){
            foreach ($state as $value) {
                $selected = '';
                if(in_array($value->id, $childArr)){
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }

    public function getCityList(){
        $state_id = $this->input->post('state_id');
        $childs = $this->input->post('selectedChild');
        $childArr = explode(',', $childs);
        $city = $this->animal_master_model->getCityList($state_id);
        $html = '<option value="">Select</option>';
        if(count($city) > 0){
            foreach ($city as $value) {
                $selected = '';
                if(in_array($value->id, $childArr)){
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode($data);
    }
    

    
    
}