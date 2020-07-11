<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Product extends MY_Controller {
    public $controller;
    public $perPage;
    public $userId;

    public function __construct() {
        parent::__construct();
        $this->controller = $this->router->fetch_class();
        $this->load->model( 'cms/cms_model' );
        $this->load->model( 'product_model' );
        $this->load->model( 'cms/blog_model' );
        $this->perPage = 10;
        $this->userId = $this->session->userdata( 'user_id' );
        // redirect( base_url() );
    }

    public function index() {
        $this->search();
    }

    public function search( $category_id = 0, $choices = '' ) {
        $data = array();
        $status = '';
        $msg = '';
        $data['selectedCatId'] = $category_id;
        $data['selectedCatDet'] = $this->cms_model->getSelectedCategory( $category_id );
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['blogs'] = $this->blog_model->getCategoryBlog( $category_id );
        $data['category_id'] = $category_id;
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Product Search' );
        $this->template->setLayout( 'cms' );
        $this->template->homeRender( 'user/'.$this->controller.'/search', $data );
    }

    private function _priceArray() {
        $minMaxPrice = $this->product_model->getMinMaxPrice();
        $retData = array(
            'min' => $minMaxPrice[0]->min_price,
            'max' => $minMaxPrice[0]->max_price,
            'min_select' => $minMaxPrice[0]->min_price,
            'max_select' => $minMaxPrice[0]->max_price
        );
        if ( $this->input->post( 'price' ) != '' ) {
            $priceArr = explode( ',', $this->input->post( 'price' ) );
            $price1 = trim( intval( $priceArr[0] ) );
            $price2 = trim( intval( $priceArr[1] ) );
            if ( $price1 >= $price2 ) {
                $retData['min_select'] = $price2;

                $retData['max_select'] = $price1;
            } else {
                $retData['min_select'] = $price1;

                $retData['max_select'] = $price2;
            }
        }
        return $retData;
    }

    public function getAjaxData() {
        //pr( $this->input->post() );
        $category_id = $this->input->post( 'category_id' );
        $postLimit = $this->input->post( 'startPage' );
        $sendLim = $retData['startPage'] = $postLimit + 1;
        $dbLim = $sendLim * $this->perPage;
        $limit = array( 'start' => $dbLim, 'perPage' => $this->perPage );
        $orderBy = array( 'col' => 'AM.am_created_date', 'act' => 'DESC' );
        $search['keyWord'] = $this->input->post( 'keyWord' );
        $search['country_id'] = $this->input->post( 'country_id' );
        $search['state_id'] = $this->input->post( 'state_id' );
        $search['city_id'] = $this->input->post( 'city_id' );
        $search['price'] = $this->_priceArray();
        $search['choices'] = $this->input->post( 'choices' );
        $search['buy_or_sell'] = $this->input->post( 'buy_or_sell' );
        $prodListCount = $this->product_model->getProductCountAll( $category_id, $search );
        if ( $prodListCount > ( $dbLim + $this->perPage ) ) {
            $retData['loaderStatus'] = 'show';
        } else {
            $retData['loaderStatus'] = 'hide';
        }
        $data['list'] = $this->product_model->getProductListAll( $category_id, $search, $limit, $orderBy );
        $html = $this->load->view( 'user/product/single', $data, true );
        $retData['html'] = $html;
        $retData['status'] = 'success';
        echo json_encode( $retData ) ;

    }

    public function details( $am_id = 0 ) {
        $data = array();
        $data['userId'] = $this->userId;
        $status = $this->session->flashdata( 'status' );
        $msg = $this->session->flashdata( 'msg' );
        $this->_addViewedDetails( $am_id );
        $data['prodDet'] = $this->product_model->getProductDetails( $am_id );
        $data['prodImg'] = $this->product_model->getProductImages( $am_id );
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['comments'] = $this->product_model->getCommentList( $am_id );
        $data['am_id'] = $am_id;
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Product Search' );
        $this->template->setLayout( 'cms' );
        $this->template->homeRender( 'user/'.$this->controller.'/details', $data );
    }

    public function getStateList() {
        $country_id = $this->input->post( 'country_id' );
        $childs = $this->input->post( 'selectedChild' );
        $childArr = explode( ',', $childs );
        $state = $this->tbl_generic_model->getStateList( $country_id );
        $html = '<option value="">Select</option>';
        if ( count( $state ) > 0 ) {
            foreach ( $state as $value ) {
                $selected = '';
                if ( in_array( $value->id, $childArr ) ) {
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode( $data );
    }

    public function getCityList() {
        $state_id = $this->input->post( 'state_id' );
        $childs = $this->input->post( 'selectedChild' );
        $childArr = explode( ',', $childs );
        $city = $this->tbl_generic_model->getCityList( $state_id );
        $html = '<option value="">Select</option>';
        if ( count( $city ) > 0 ) {
            foreach ( $city as $value ) {
                $selected = '';
                if ( in_array( $value->id, $childArr ) ) {
                    $selected = 'selected';
                }
                $html .= '<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
            }
        }
        $data['data'] = $html;
        echo json_encode( $data );
    }

    private function _addViewedDetails( $am_id = 0 ) {
        if ( $am_id > 0 ) {
            $data['am_id'] = $am_id;
            $data['user_id'] = ( $this->session->userdata( 'user_id' ) > 0 )?$this->session->userdata( 'user_id' ):0;
            $this->tbl_generic_model->add( 'animal_master_viewed', $data );
            $this->product_model->updateViewedCount( $am_id );
        }
    }

    public function contactToSellerEmail() {
        $data = array();
        $data['am_id'] = $am_id = $this->uri->segment( 4 );
        $where['user_id'] = $this->session->userdata( 'user_id' );
        $user = $this->tbl_generic_model->get( 'user_master', '*', $where );
        if ( count( $user ) > 0 ) {
            $data['user']['name'] = $user[0]->name;

            $data['user']['email'] = $user[0]->email;
            $data['user']['mobile'] = $user[0]->mobile;
        } else {
            $data['user']['name'] = '';

            $data['user']['email'] = '';
            $data['user']['mobile'] = '';
        }
        $this->load->view( 'product/contactToSellerEmail', $data );
    }

    public function submitContactToSellerEmail() {
        $data = array();
        $data['am_id'] = $am_id = $this->uri->segment( 4 );
        $data['product'] = $this->product_model->getProductDetails( $am_id );
        $data['form'] = $this->input->post( 'contact_us' );
        if ( $data['form']['name'] != '' && $data['form']['email'] != '' ) {
            $this->_sendEmail( $data );
            $this->session->set_flashdata( 'status', 'success' );
            $this->session->set_flashdata( 'msg', 'Thank You! You have successfully Submit your query to the Seller for the Listing #'.$data['product'][0]->am_code );
        } else {
            $this->session->set_flashdata( 'status', 'danger' );
            $this->session->set_flashdata( 'msg', 'Please enter the Name and Email field.' );
        }
        redirect( 'user/product/details/'.$am_id );
    }

    private function _sendEmail( $contact_us = array() ) {
        $data = $contact_us;
        if ( $data['product'][0]->am_user_type == 'admin' ) {
            $to = ADMIN_EMAIL;
            $data['to_name'] = ADMIN_NAME;
            $bcc = array();
        } else {
            $to = $data['product'][0]->email;
            $data['to_name'] = $data['product'][0]->user_name;
            $bcc = array( ADMIN_EMAIL );
        }
        $subject = 'Request for #'.$data['product'][0]->am_code.' | Parrot Dipankar';
        $body = $this->load->view( 'user/'.$this->controller.'/email', $data, TRUE );
        $this->tbl_generic_model->sendEmail( $to, $subject, $body, array(), $bcc );
    }

    public function buy( $category_id = 0 ) {
        $data = array();
        $status = '';
        $msg = '';
        //print_r( $this->input->post() );
        $data['limit'] = $limit = array( 'start' => 0, 'perPage' => $this->perPage );
        $data['orderBy'] = $orderBy = array( 'col' => 'AM.am_created_date', 'act' => 'DESC' );
        $data['keyWord'] = $search['keyWord'] = $this->input->post( 'keyWord' );
        $data['country_id'] = $search['country_id'] = $this->input->post( 'country_id' );
        $data['state_id'] = $search['state_id'] = $this->input->post( 'state_id' );
        $data['city_id'] = $search['city_id'] = $this->input->post( 'city_id' );
        $data['buy_or_sell'] = $search['buy_or_sell'] = 'buy';
        $choices = '';
        if ( $this->input->post( 'choices' ) != '' ) {
            $choices = $this->input->post( 'choices' );
        }
        $data['choices'] = $search['choices'] = $choices;
        $data['price'] = $search['price'] = $this->_priceArray();
        //pr( $search );
        $data['selectedCatId'] = $category_id;
        $data['selectedCatDet'] = $this->cms_model->getSelectedCategory( $category_id );
        $data['prodListCount'] = $this->product_model->getProductCountAll( $category_id, $search );
        $data['prodListAll'] = $this->product_model->getProductListAll( $category_id, $search, $limit, $orderBy );

        $data['country'] = $this->tbl_generic_model->getCountryList();
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['category_id'] = $category_id;
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Product Search' );
        $this->template->setLayout( 'cms' );
        $this->template->homeRender( 'user/'.$this->controller.'/search', $data );
    }

    public function sell( $category_id = 0 ) {
        $data = array();
        $status = '';
        $msg = '';
        //print_r( $this->input->post() );
        $data['limit'] = $limit = array( 'start' => 0, 'perPage' => $this->perPage );
        $data['orderBy'] = $orderBy = array( 'col' => 'AM.am_created_date', 'act' => 'DESC' );
        $data['keyWord'] = $search['keyWord'] = $this->input->post( 'keyWord' );
        $data['country_id'] = $search['country_id'] = $this->input->post( 'country_id' );
        $data['state_id'] = $search['state_id'] = $this->input->post( 'state_id' );
        $data['city_id'] = $search['city_id'] = $this->input->post( 'city_id' );
        $data['buy_or_sell'] = $search['buy_or_sell'] = 'sell';
        $choices = '';
        if ( $this->input->post( 'choices' ) != '' ) {
            $choices = $this->input->post( 'choices' );
        }
        $data['choices'] = $search['choices'] = $choices;
        $data['price'] = $search['price'] = $this->_priceArray();
        //pr( $search );
        $data['selectedCatId'] = $category_id;
        $data['selectedCatDet'] = $this->cms_model->getSelectedCategory( $category_id );
        $data['prodListCount'] = $this->product_model->getProductCountAll( $category_id, $search );
        $data['prodListAll'] = $this->product_model->getProductListAll( $category_id, $search, $limit, $orderBy );
        $data['country'] = $this->tbl_generic_model->getCountryList();
        $data['category'] = $this->cms_model->getLevelOneCategory();
        $data['category_id'] = $category_id;
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Product Search' );
        $this->template->setLayout( 'cms' );
        $this->template->homeRender( 'user/'.$this->controller.'/search', $data );
    }

}