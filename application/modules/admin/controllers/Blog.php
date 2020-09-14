<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Blog extends MX_Controller {
    public $adminName;
    public $data;
    public $action;

    public function __construct() {
        parent::__construct();
        $this->ion_user_auth_admin->isLoggedIn();
        $this->adminName = ADMIN_NAME;
        $this->load->library( 'Template' );
        $this->_user_id = trim( $this->session->userdata( 'aum_id' ) );
        $this->controller = $this->router->fetch_class();
        $this->load->model( $this->controller.'_model' );
        $this->data['page_title'] = 'Blog';
        $this->data['controller'] = $this->controller;
        $this->data['statusUrl'] = base_url( 'admin/'.$this->controller.'/changeStatus' );
    }

    public function index() {
        $this->data['controller'] = $this->controller;
        $status = $this->session->flashdata( 'status' );
        $msg = $this->session->flashdata( 'msg' );
        $this->data['lang'] = getLanguageArrAnimalMaster();
        $this->data['msg'] = $this->template->getMessage( $status, $msg );
        $this->data['list'] = array();
        $this->data['dataTableUrl'] = base_url( 'admin/'.$this->controller.'/viewListDataTable' );
        $this->template->setTitle( 'Admin : '.$this->data['page_title'] );
        $this->template->setLayout( 'dashboard' );

        $this->template->homeAdminRender( 'admin/'.$this->controller.'/index', $this->data );
    }

    public function viewListDataTable() {
        $requestData = $this->input->post();
        $columns = array(
            0 => 'BREV.title',
            2 => 'BREV.short_desc',
            3 => 'BREV.is_status',
            4 => 'BREV.created_date',
        );
        if ( !isset( $requestData['order'][0]['column'] ) ) {
            $orderBy['col'] = 'BREV.created_date';
            $orderBy['val'] = 'DESC';
        } else {
            $orderBy['col'] = $columns[$requestData['order'][0]['column']];
            $orderBy['val'] = $requestData['order'][0]['dir'];
        }

        $limit['start'] = $requestData['start'];
        $limit['perpage'] = $requestData['length'];
        $searchData = trim( $requestData['search']['value'] );
        $fetchedData = $this->blog_model->getDataTableData( $searchData, $orderBy, $limit );
        $recordsTotal = $fetchedData['recordsTotal'];
        $recordsFiltered = $fetchedData['recordsFiltered'];
        $rowsData = $fetchedData['rowsData'];

        $rows = $this->_getArrayData( $rowsData );
        // print_r( $rows );
        // exit;
        $json_data = array(
            'draw' => intval( $requestData['draw'] ), // for every request/draw by clientside, they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            'recordsTotal' => intval( $recordsTotal ), // total number of records
            'recordsFiltered' => intval( $recordsFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            'data' => $rows  // total data array
        );
        echo json_encode( $json_data );
        exit;
    }

    /*
    * function _getArrayData()
    * This function is used to make array for client listing page
    */

    private function _getArrayData( $data = array() ) {
        $rows = array();
        if ( count( $data ) > 0 ) {
            foreach ( $data as $value ) {
                $actionStr = '';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/image/'.$value->blog_id.'" class="btn btn-warning btn-xs"><i class="fa fa-picture-o"></i> Image</a>';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/edit/'.$value->blog_id.'" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a>';
                $actionStr .= '<a href="'.base_url().'admin/'.$this->controller.'/delete/'.$value->blog_id.'" class="deleteChange btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>';
                $statusStr = '<a class="statusChange btn btn-'.( $value->is_status == 'active'?'info':'warning' ).' btn-xs" href="javascript:void(0);" title="Click to change Status" value="'.( $value->is_status == 'active'?'unlock':'lock' ).'" id="status_'.$value->blog_id.'" name="'.$value->blog_id.'"><i id="i_status_'.$value->blog_id.'" class="fa fa-'.( $value->is_status == 'active'?'unlock':'lock' ).'"></i><span id="span_status_'.$value->blog_id.'">'.ucfirst( $value->is_status ).'</span>';

                $img = '<img height="100" width="200" alt="'.$value->image_alt.'" src="'.base_url( 'uploads/blog/thumb/'.$value->image_path ).'">';
                $nestedData[] = $value->title;
                $nestedData[] = $img;
                $nestedData[] = $value->short_desc;

                $nestedData[] = $statusStr;
                $nestedData[] = date( 'F j, Y, g:i a', strtotime( $value->created_date ) );
                $nestedData[] = $actionStr;
                $rows[] = $nestedData;
                unset( $actionStr );
                unset( $statusStr );
                unset( $nestedData );
            }
        }
        return $rows;
    }

    public function add() {
        $status = '';
        $msg = '';
        $this->action = $this->data['action'] = 'add';
        $this->data['blog_id'] = 0;
        $this->add_edit();
        $this->data['editData'] = $this->blog_model->getSingleData( $this->data['blog_id'] );
        $this->data['catData'] = $this->blog_model->getAssignCat( $this->data['blog_id'] );
        $this->data['animal_cat'] = $this->blog_model->getAllAnimalParentCategory( 0 );
        $this->data['animal_sub_cat'] = [];
        if ( count( $this->data['catData'] ) > 1 ) {
            $this->data['animal_sub_cat'] = $this->blog_model->getAllAnimalParentCategory( $this->data['catData'][0]->acm_id );
        }
        $this->data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Admin : '.ucfirst( $this->data['page_title'] ).' '.ucfirst( $this->action ) );
        $this->template->setLayout( 'dashboard' );
        $this->template->homeAdminRender( 'admin/'.$this->controller.'/add_edit_form', $this->data );
    }

    public function add_edit() {
        $this->load->library( 'form_validation' );
        $this->form_validation->set_rules( 'title', 'Blog Title', 'required|trim' );
        $this->form_validation->set_rules( 'short_desc', 'Short Description', 'required|trim' );
        if ( $this->form_validation->run() == TRUE ) {
            $blogRevData = array();
            $blogRevData['title'] = $this->input->post( 'title' );
            $blogRevData['title_url'] = url_title( $this->input->post( 'title' ), '-', true ).'-'.$this->_getCode();
            $blogRevData['short_desc'] = $this->input->post( 'short_desc' );
            $blogRevData['long_desc'] = $this->input->post( 'long_desc' );
            $blogRevData['blog_id'] = $this->input->post( 'blog_id' );
            $blog_revision_id = $this->tbl_generic_model->add( 'blog_revisions', $blogRevData );
            if ( $this->action == 'add' ) {
                $blogData['blog_revision_id'] = $blog_revision_id;
                $blog_id = $this->tbl_generic_model->add( 'blogs', $blogData );
                $upData['blog_id'] = $blog_id;
                $where['blog_revision_id'] = $blog_revision_id;
                $this->tbl_generic_model->edit( 'blog_revisions', $upData, $where );
            } elseif ( $this->action == 'edit' ) {
                $where['blog_id'] = $blogRevData['blog_id'];
                $upData['blog_revision_id'] = $blog_revision_id;
                $this->tbl_generic_model->edit( 'blogs', $upData, $where );
            }
            if ( $blog_revision_id > 0 ) {
                $this->_addCategory( $blog_revision_id );
            }
            $status = 'success';
            if ( $this->action == 'add' ) {
                $msg = 'Successfully Added';
            } else {
                $msg = 'Successfully Updated';
            }
            $this->session->set_flashdata( 'status', $status );
            $this->session->set_flashdata( 'msg', $msg );
            redirect( base_url().'admin/'.$this->controller );
        }
    }

    private function _addCategory( $blog_revision_id = 0 ) {
        $data = $this->input->post( 'c_cat_id' );
        $inData = array();
        $p_acr = $this->input->post( 'p_cat_id' );
        if ( $p_acr > 0 ) {
            $inData[] = array(
                'blog_revision_id' => $blog_revision_id,
                'acm_id' => $p_acr
            );
        }
        if ( $p_acr !== 1 ) {
            if ( is_array( $data ) ) {
                if ( count( $data ) > 0 ) {
                    foreach ( $data as $key => $value ) {
                        $inData[] = array(
                            'blog_revision_id' => $blog_revision_id,
                            'acm_id' => $value
                        );
                    }
                }
            }
        }
        if ( count( $inData ) > 0 ) {
            $this->tbl_generic_model->add_batch( 'blog_animal_categorys', $inData );
        }
        return true;
    }

    public function getChildCategory() {
        $parent_id = $this->input->post( 'parent_id' );
        $childs = $this->input->post( 'selectedChild' );
        $childArr = explode( ',', $childs );
        $childCat = $this->blog_model->getAllAnimalParentCategory( $parent_id );
        $html = '<option value="">Select Some Options</option>';
        if ( count( $childCat ) > 0 ) {
            foreach ( $childCat as $value ) {
                $selected = '';
                if ( in_array( $value->acm_id, $childArr ) ) {
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->acm_id.'" '.$selected.'>'.$value->acmd_name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode( $data );
    }

    private function _getCode() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ( $i = 0; $i < 12; $i++ ) {
            $index = rand( 0, strlen( $characters ) - 1 );
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function edit( $blog_id = 0 ) {
        $status = '';
        $msg = '';
        $this->action = $this->data['action'] = 'edit';
        $this->data['blog_id'] = $blog_id;
        $this->add_edit();
        $this->data['editData'] = $this->blog_model->getSingleData( $this->data['blog_id'] );
        $this->data['catData'] = $this->blog_model->getAssignCat( $this->data['blog_id'] );
        $this->data['animal_cat'] = $this->blog_model->getAllAnimalParentCategory( 0 );
        $this->data['animal_sub_cat'] = [];
        if ( count( $this->data['catData'] ) > 0 ) {
            if ( $this->data['catData'][0]->acm_id != 1 ) {
                $this->data['animal_sub_cat'] = $this->blog_model->getAllAnimalParentCategory( $this->data['catData'][0]->acm_id );
            }
        }
        $this->data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Admin : '.ucfirst( $this->data['page_title'] ).' '.ucfirst( $this->action ) );
        $this->template->setLayout( 'dashboard' );
        $this->template->homeAdminRender( 'admin/'.$this->controller.'/add_edit_form', $this->data );
    }

    public function changeStatus() {
        $am_id = $this->input->post( 'am_id' );
        // blog id
        $this->updateActionStatusDelete( $am_id, 'status' );
        $data['msg'] = $this->template->getMessage( 'success', 'Successfully changed the status .' );
        echo json_encode( $data );
    }

    public function delete( $id = 0 ) {
        $status = 'success';
        $msg = 'Successfully Deleted';
        $this->updateActionStatusDelete( $id, 'delete' );
        $this->session->set_flashdata( 'status', $status );
        $this->session->set_flashdata( 'msg', $msg );
        redirect( base_url().'admin/'.$this->controller );
    }

    public function updateActionStatusDelete( $blog_id = 0, $action = 'delete' ) {
        if ( $blog_id > 0 ) {
            $editData = $this->blog_model->getSingleData( $blog_id );
            $blogRevData = array();
            $blogRevData['title'] = $editData[0]->title;
            $blogRevData['title_url'] = $editData[0]->title_url.'-'.$this->_getCode();
            $blogRevData['short_desc'] = $editData[0]->short_desc;
            $blogRevData['long_desc'] = $editData[0]->long_desc;
            $blogRevData['blog_id'] = $editData[0]->blog_id;
            if ( $action == 'delete' ) {
                $blogRevData['is_status'] = 'delete';
            } else {
                $blogRevData['is_status'] = $editData[0]->is_status == 'active'?'inactive':'active';
            }
            $blog_revision_id = $this->tbl_generic_model->add( 'blog_revisions', $blogRevData );

            $where['blog_id'] = $blog_id;
            $upData['blog_revision_id'] = $blog_revision_id;
            $this->tbl_generic_model->edit( 'blogs', $upData, $where );
            if ( $action != 'delete' ) {
                $catData = $this->blog_model->getAssignCatForStatus( $editData[0]->blog_revision_id );
                if ( count( $catData ) > 0 ) {
                    $inData = array();
                    foreach ( $catData as $key => $value ) {
                        $inData[] = array(
                            'blog_revision_id' => $blog_revision_id,
                            'acm_id' => $value->acm_id
                        );
                    }
                    if ( count( $inData ) > 0 ) {
                        $this->tbl_generic_model->add_batch( 'blog_animal_categorys', $inData );
                    }
                }
            }
        }
    }

    public function image( $blog_id = 0 ) {
        $data = array();
        $data['controller'] = $this->controller;
        $status = $this->session->flashdata( 'status' );
        $msg = $this->session->flashdata( 'msg' );

        $data['editData'] = $this->blog_model->getSingle( $blog_id );
        $data['page_title'] = ucfirst( $this->controller ).' Images of '.$data['editData'][0]->title;
        $this->_upload( $blog_id );
        $data['blog_id'] = $blog_id;
        $data['list'] = $this->blog_model->getImageList( $blog_id );
        $data['msg'] = '';
        if ( $msg != '' ) {
            $data['msg'] = $this->template->getMessage( $status, $msg );
        }
        $data['defaultPath'] = base_url( 'admin/blog/setDefaultImage' );
        $this->template->setTitle( 'Admin : '.$data['page_title'] );
        $this->template->setLayout( 'dashboard' );

        $this->template->homeAdminRender( 'admin/'.$this->controller.'/image', $data );
    }

    private function _upload( $blog_id = 0 ) {
        $config['upload_path']          = UPLOAD_BLOG_PATH;
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['min_width']            = 1000;
        $config['min_height']           = 500;
        $config['file_name']            = date( 'YmdHis' ).$blog_id;
        $this->load->library( 'upload', $config );
        $this->load->library( 'image_lib' );
        if ( $_FILES ) {
            if ( ! $this->upload->do_upload( 'myFile' ) ) {
                $this->session->set_flashdata( 'status', 'danger' );
                $this->session->set_flashdata( 'msg', $this->upload->display_errors() );
            } else {
                $this->session->set_flashdata( 'status', 'success' );
                $this->session->set_flashdata( 'msg', 'Successfully Uploaded' );
                $inData['image_path'] = $path = $this->upload->data( 'file_name' );
                $inData['blog_id'] = $blog_id;
                $this->image_lib->clear();
                $this->_resizeImage( $path, '1000', '500', '' );
                $this->_resizeImage( $path, '250', '125', 'thumb' );
                $this->tbl_generic_model->add( 'blog_images', $inData );
            }
            redirect( base_url().'admin/'.$this->controller.'/image/'.$blog_id );
        }
    }

    private function _resizeImage( $imageName = '', $width = '1000', $height = '500', $folder = '' ) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = UPLOAD_BLOG_PATH.$imageName;
        $config['new_image'] = UPLOAD_BLOG_PATH.$folder;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = $width;
        $config['height']       = $height;
        $this->load->library( 'image_lib' );
        $this->image_lib->initialize( $config );
        $this->image_lib->resize();
    }

    public function image_delete( $blog_image_id = 0 ) {
        $where['blog_image_id'] = $blog_image_id;
        $data = $this->tbl_generic_model->get( 'blog_images', '*', $where );
        if ( !empty( $data ) ) {
            $this->session->set_flashdata( 'status', 'success' );
            $this->session->set_flashdata( 'msg', 'Successfully Deleted' );
            $this->tbl_generic_model->delete( 'blog_images', $where );
            @unlink( UPLOAD_BLOG_PATH.$data[0]->image_path );
            @unlink( UPLOAD_BLOG_PATH.'thumb/'.$data[0]->image_path );
            redirect( base_url().'admin/'.$this->controller.'/image/'.$data[0]->blog_id );
        } else {
            $this->session->set_flashdata( 'status', 'danger' );
            $this->session->set_flashdata( 'msg', 'Wrong Parameter' );
            redirect( base_url().'admin/'.$this->controller.'/index' );
        }
    }

    public function setDefaultImage() {
        $blog_image_id = $this->input->post( 'blog_image_id' );
        $blog_id = $this->input->post( 'blog_id' );
        $data['msg'] = $this->template->getMessage( 'success', 'Successfully set the default Image.' );
        $this->blog_model->setDefaultImage( $blog_id, $blog_image_id );
        echo json_encode( $data );
    }
}