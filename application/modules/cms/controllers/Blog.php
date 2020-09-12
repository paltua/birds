<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Blog extends MY_Controller {
    public $controller;
    public $data;

    public function __construct() {
        parent::__construct();
        $this->controller = $this->router->fetch_class();
        $this->load->model( $this->controller.'_model' );
        $this->data['controller'] = $this->controller;
    }

    public function index( $title_url = '' ) {

    }

    public function details( $title_url = '' ) {
        $data = array();
        $status = '';
        $msg = '';
        $data['details'] = $this->blog_model->getDetails( $title_url );
        $data['images'] =  [];
        $data['comments'] =  [];
        if ( $data['details'][0]->blog_id > 0 ) {
            $data['images'] = $this->blog_model->getDetailsImages( $data['details'][0]->blog_id );
            $data['list'] = $this->blog_model->getOthersBlog( $data['details'][0]->blog_id );
        } else {
            redirect( base_url() );
        }
        $data['msg'] = $this->template->getMessage( $status, $msg );
        $this->template->setTitle( 'Blog:'.$data['details'][0]->title );
        $this->template->setLayout( 'cms' );
        $this->template->homeRender( 'cms/'.$this->controller.'/details', $data );
    }

    public function getComments() {
        $blog_id = trim( $this->input->post( 'blog_id' ) );
        $data['html'] = '';
        $comments = $this->blog_model->getComments( $blog_id );
        if ( $blog_id > 0 ) {
            if ( count( $comments ) > 0 ) {
                foreach ( $comments as $key => $value ) {
                    $data['html'] .= '<div class="comments comments-list-inner w-100">
                    <h4>'.$value->name.'</h4>
                    <p>'.$this->getReplaceOfEmailMobile( $value->comments ).'</p>
                    <p class="comment-time">'.getViewDate( $value->created_date, $value->time_sec, $value->time_min, $value->time_hour ).'</p>
                </div>';
                }
            } else {
                $data['html'] = '';
            }
        }
        $data['status'] = 'success';
        echo json_encode( $data );
    }

    private function getReplaceOfEmailMobile( $text = '' ) {
        //Your string
        preg_match_all( '/(?:[0-9]{10})+/s', $text, $result, PREG_PATTERN_ORDER );
        if ( count( $result ) > 0 ) {
            foreach ( $result[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 4 ).'XXXXXX', $text );
            }
        }
        preg_match_all( '/(?:[0-9]{9})+/s', $text, $resultNine, PREG_PATTERN_ORDER );
        if ( count( $resultNine ) > 0 ) {
            foreach ( $resultNine[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 3 ).'XXXXXX', $text );
            }
        }
        preg_match_all( '/(?:[0-9]{8})+/s', $text, $resultNine, PREG_PATTERN_ORDER );
        if ( count( $resultNine ) > 0 ) {
            foreach ( $resultNine[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 2 ).'XXXXXX', $text );
            }
        }
        preg_match_all( '/(?:[0-9]{7})+/s', $text, $resultNine, PREG_PATTERN_ORDER );
        if ( count( $resultNine ) > 0 ) {
            foreach ( $resultNine[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 1 ).'XXXXXX', $text );
            }
        }
        preg_match_all( '/(?:[0-9]{6})+/s', $text, $resultNine, PREG_PATTERN_ORDER );
        if ( count( $resultNine ) > 0 ) {
            foreach ( $resultNine[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 1 ).'XXXXX', $text );
            }
        }
        preg_match_all( '/(?:[0-9]{5})+/s', $text, $resultNine, PREG_PATTERN_ORDER );
        if ( count( $resultNine ) > 0 ) {
            foreach ( $resultNine[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 1 ).'XXXX', $text );
            }
        }
        preg_match_all( '/(?:[0-9]{4})+/s', $text, $resultNine, PREG_PATTERN_ORDER );
        if ( count( $resultNine ) > 0 ) {
            foreach ( $resultNine[0] as $key => $value ) {
                $text = str_replace( $value, substr( $value, 0, 1 ).'XXX', $text );
            }
        }

        preg_match_all( '/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i', $text, $matches );
        if ( count( $matches ) > 0 ) {
            foreach ( $matches[0] as $key => $value ) {
                $data = explode( '@', $value );
                $lastStr = '@'.$data[1];
                if ( strlen( $data[0] ) > 0 ) {
                    $cross = '';
                    for ( $i = 0; $i <strlen( $data[0] ) ;
                    $i++ ) {
                        $cross .= 'X';
                    }
                }
                $text = str_replace( $value, $cross.$lastStr, $text );
            }
        }
        return $text;
    }

}