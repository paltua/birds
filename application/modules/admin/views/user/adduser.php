  <style>
    .centered-form{
     margin-top: 60px;
     }
     .centered-form .panel{
     background: rgba(255, 255, 255, 0.8);
     box-shadow: rgba(0, 0, 0, 0.3) 20px 20px 20px;
    }
   
  </style> 


   <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url();?>resources/datatable/css/formValidation.css"/>
    <script type="text/javascript" src="<?php echo base_url();?>resources/datatable/js/jquery-2.2.3.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resources/datatable/js/formValidation.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resources/datatable/js/bootstrap.js"></script>


  <div class="container">
        <div class="row centered-form">
        <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                        <h3 class="panel-title">Add New Users <small></small></h3>
                        </div>
                        <div class="panel-body">
                        <form id="userform" role="form" method="post" action="<?php echo base_url().ADMIN_NAME;?>/user/save">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                       <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                       <input type="email" name="email" id="email" class="form-control input-sm" placeholder="Email Address" >
                                    </div>
                                    <div id="disp"></div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                       <input type="password" name="password" id="password" class="form-control input-sm" placeholder="Password">
                                   </div>
                               </div>
                           </div>

                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="phone_no" id="phone_no" class="form-control input-sm" placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <select class="form-control input-sm" name="role_id">
                                            <option value=''>---Select Role---</option>
                                                <?php foreach($list as $value) { ?>
                                                    <option value="<?php echo $value->arm_id ?>">
                                                  <?php echo $value->role_name ?></option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="submit" value="Register" class="btn btn-info btn-block">
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
var csfrData = {};
 csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                  = '<?php echo $this->security->get_csrf_hash(); ?>';

 $.ajaxSetup({
   data: csfrData
 });

var jq14 = jQuery.noConflict(true); 
$=jQuery;
(function ($) {
$(document).ready(function() {
    // Generate a simple captcha
    /*function randomNumber(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    };
    $('#captchaOperation').html([randomNumber(1, 100), '+', randomNumber(1, 200), '='].join(' '));*/

    $('#userform').formValidation({
        message: 'This value is not valid',
        /*icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },*/
        fields: {
            first_name: {
                validators: {
                    notEmpty: {
                        message: 'Your first name is required'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z]+$/,
                        message: 'Your first name cannot have numbers or symbols'
                    }
                }
            },
            last_name: {
                validators: {
                    notEmpty: {
                        message: 'Your last name is required'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z]+$/,
                        message: 'Your last name cannot have numbers or symbols'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email is required'
                    },
                    regexp: {
                        regexp: /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i,
                        message: 'The input is not a valid email address'
                    },
                    //email: true,
                    remote: {
                        type: 'POST',
                        data: {'email':function(){return $('#email').val()}},
                        url: '<?php echo base_url();?>admin/user/Ajax_CheckEmail',
                        //Send { username: 'its value', email: 'its value' }
                        data: function(validator) {
                          return {
                              'email': validator.getFieldElements('email').val()
                            };
                        },
                        
                        //async:false
                        message: 'The email is not available'
                    }
                }
            },
            phone_no: {
                validators: {
                    notEmpty: {
                        message: 'Phone number is required'
                    },
                    regexp: {
                        regexp: /^[0-9]+$/,
                        message: 'Phone number should have only numbers '
                    }
                }
            },
            role_id: {
                validators: {
                    notEmpty: {
                        message: 'Role is required'
                    },
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The password is required'
                    },
                }
            },
             /*captcha: {
                validators: {
                    callback: {
                        message: 'Mauvaise reponse',
                        callback: function(value, validator, $field) {
                            var items = $('#captchaOperation').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
                            return value == sum;
                        }
                    }
                }
            },*/
        }
    });
});
}(jq14));

</script>





  