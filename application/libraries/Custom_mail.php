<?php
    class Custom_mail
    {

        public $Rpath = ''; // Use to store the path to the resource folder


        public function __construct()
        {
            
            $this->Rpath = base_url(); // Creating Full Path to resources folder
            
        }
        
        
        public function resource_path()
        {
            
            return $this->Rpath;
            
        }
        
        public function send_contactus_mail( $name,$mailid,$mobile_no,$subject,$message)
        {

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
            // More headers
            $headers .= 'From: '.SITENAME.' Notifications<'.SUPPORTEMAIL.'>';			

            $msgbody =  '<center>
            <table>
            <tr>
            <td class="padded" style="padding: 0;vertical-align: top;padding-left: 32px;padding-right: 32px">

            <h1 style="Margin-top: 0;color: #565656;font-weight: 700;font-size: 36px;Margin-bottom: 18px;font-family: sans-serif;line-height: 42px"></h1>
            <h2 style="Margin-top: 0;color: #555;font-style: italic;font-weight: normal;font-size: 26px;line-height: 32px;Margin-bottom: 20px;font-family: Georgia,serif">Dear '.$name.',</h2>           

            <p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px"><strong style="font-weight: bold">
                We have received your message and would like to thank you for writing to us.
            </strong></p>

            </td>
            </tr>
            <tr>
                <td bgcolor="#c8c8c8" align="center" style="text-align:center" colspan="3">
                    <p style="font-size:13px;color:rgb(0,0,0);margin:0px;padding-top:10px;padding-bottom:10px">
                        Thank you,
                        <br>
                        <b>'.SITENAME.' Team</b>
                    </p>
                </td>
            </tr>
            </table>

            </center>';

            if (defined('ENVIRONMENT'))
            {
                switch (ENVIRONMENT)
                {
                    case 'development':
                    error_reporting(E_ALL);
                 
                    break;

                    case 'testing':
                    error_reporting(E_ALL);
                    break;
                    case 'staging':
                    error_reporting(E_ALL);
                    break;
                    case 'production':
                    error_reporting(0);
                    break;

                    default:
                    exit('The application environment is not set correctly.');
                }
            }            

            if ( @mail( $mailid, 'Confirmation From SIDBI', $msgbody, $headers ) )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public function send_contactus_admin_mail( $name,$mailid,$mobile_no,$subject,$message)
        {

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
            // More headers
            $headers .= 'From: '.$name.' Notifications<'.$mailid.'>';           

            $msgbody =  '<center>
            <table>
            <tr>
            <td class="padded" style="padding: 0;vertical-align: top;padding-left: 32px;padding-right: 32px">

            <h1 style="Margin-top: 0;color: #565656;font-weight: 700;font-size: 36px;Margin-bottom: 18px;font-family: sans-serif;line-height: 42px"></h1><h2 style="Margin-top: 0;color: #555;font-style: italic;font-weight: normal;font-size: 26px;line-height: 32px;Margin-bottom: 20px;font-family: Georgia,serif">Dear Admin</h2>
            <p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px"><strong style="font-weight: bold">You have a new query from '.$name.'.  </strong></p>
            <p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px"><strong style="font-weight: bold">Following are the details:</strong></p><br />
            <p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px"><strong style="font-weight: bold">Email</strong>: '.$mailid.'<br>
            <strong style="font-weight: bold">Mobile No.</strong>: '.$mobile_no.'<br>
            <strong style="font-weight: bold">Subject</strong>: '.$subject.'<br>
            <strong style="font-weight: bold">Message</strong>: '.$message.'<br>
            </p>

            </td>
            </tr>
            <tr>
                <td bgcolor="#c8c8c8" align="center" style="text-align:center" colspan="3">
                    <p style="font-size:13px;color:rgb(0,0,0);margin:0px;padding-top:10px;padding-bottom:10px">
                        Thank you,
                        <br>
                        <b>'.SITENAME.' Team</b>
                    </p>
                </td>
            </tr>
            </table>

            </center>';

            if (defined('ENVIRONMENT'))
            {
                switch (ENVIRONMENT)
                {
                    case 'development':
                    error_reporting(E_ALL);
                    
                    break;

                    case 'testing':
                    error_reporting(E_ALL);
                    break;
                    case 'staging':
                    error_reporting(E_ALL);
                    break;
                    case 'production':
                    error_reporting(0);
                    break;

                    default:
                    exit('The application environment is not set correctly.');
                }
            }            

            if ( @mail( SUPPORTEMAIL, 'SIDBI-Query', $msgbody, $headers ) )
            {

                return true;

            }
            else
            {

                return false;

            }
        }

        // generate random key
        public function generate_random_key()
        {
            $chars = $numbs = array();
            $chars = range( "a", "z" );
            $numbs = range( 0, 9 );
            $char_num = array_merge( $chars, $numbs );
            shuffle( $char_num );
            $str = implode( '', $char_num );
            return( $str );
        }

        public function send_verification_mail( $user_id,$name,$mailid)
        {
            $CI =& get_instance();
            $CI->load->model('tbl_generic_model');

            $string = $user_id.time();
            $encryptedStr  = modules::load('account/auth/')->getPassword($string);
            $encodedUid = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($encryptedStr), $user_id, MCRYPT_MODE_CBC, md5(md5($encryptedStr))));
            $url = base_url().'msme_unit/registration/verifyRegistrationEmail?uId='.urlencode($encodedUid).'&activationKey='.urlencode($encryptedStr);

            $datasection =array('email_activation_key' => $encryptedStr);
            $where =array('user_id' => $user_id);
            $user_edit_id = $CI->tbl_generic_model->edit('web_user_master',$datasection,$where);
            

            $msgbody =  '<table class="header centered" style="border-collapse: collapse;border-spacing: 0;Margin-left: auto;Margin-right: auto;width: 602px">
            <tbody><tr><td class="border" style="padding: 0;vertical-align: top;font-size: 1px;line-height: 1px;background-color: #e9e9e9;width: 1px">&nbsp;</td></tr>
            <tr><td class="logo" style="padding: 5px; background:#323232; vertical-align: top;mso-line-height-rule: at-least">
            <div class="logo-left" style="font-size: 26px;font-weight: 700;letter-spacing: -0.02em;line-height: 32px;color: #41637e;font-family: sans-serif" align="left" id="emb-email-header">
            <div class="col-md-7 logoContainer">
              <ul class="logo clearfix">
                <li class="sidbi-logo"><a href="https://www.sidbi.in/index.php" target="_blank">
                  <img src="'.base_url().'resources/v1/img/logo.jpg" class="">
                </a></li>
                <li class="shakti-logo"><a href="http://shaktifoundation.in/" target="_blank">
                  <img src="'.base_url().'resources/v1/img/shakti-logo.png" class="">
                </a></li>
                <li class="istsl-logo">
                <a href="http://techsmall.com/" target="_blank">
                  <img src="'.base_url().'resources/v1/img/istsl.jpg" class="">
                </a></li>
              </ul>
            </div>
            </div>
            </td>
            </tr>

            </td>
            </tr>
            <tr>
            <td valign="top" align="left" style="background:#fff; padding:1em;"><h1>Hi '.$name.',</h1><br/><h2>Welcome to '.SITENAME.'. </h2><br/>
            <br/>You have attemped to register at '.SITENAME.' using the e-mail address '. $mailid . '. <br />
            <br />To start using '.SITENAME.', you will need to verify your identity by <a href="'.($url).'" target="_blank" >Click here</a>
            <br/><br/>Please do not reply to this email, replies are not monitored.
            <br /><br />Regards,<br />The '.SITENAME.' Team
            </td>
            </tr>
            </tbody>
            </table>';

            //echo $msgbody; exit();    

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
            // More headers
            $headers .= 'From: '.SITENAME.' Notifications<'.SUPPORTEMAIL.'>';


            // File saved Token starts
            if (defined('ENVIRONMENT'))
            {
                switch (ENVIRONMENT)
                {
                    case 'development':
                    error_reporting(E_ALL);
                    // File saved Token starts
                    //$file=fopen("resources/mail.txt","w") or die("Unable to open file!");
                   // fputs($file,$msgbody);
                   //fclose($file);
                   // return true;
                    //File saved token ends
                    break;

                    case 'testing':
                    error_reporting(E_ALL);
                    break;
                    case 'staging':
                    error_reporting(E_ALL);
                    break;
                    case 'production':
                    error_reporting(0);
                    break;

                    default:
                    exit('The application environment is not set correctly.');
                }
            }            


            if ( @mail( $mailid, 'Activation Mail From Administrator', $msgbody, $headers ) )
            {

                return true;

            }
            else
            {

                return false;

            }        
        
        }
    }
?>