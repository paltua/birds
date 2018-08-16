<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view('head');?>
    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap/bootstrap.min.css">
    <script src="<?php echo base_url();?>resource/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>resource/bootstrap/bootstrap.min.js"></script>

    
    

    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.css"></script>
    <script src="<?php echo base_url();?>resource/date-time/moment.js"></script>
    <script src="<?php echo base_url();?>resource/date-time/bootstrap-datetimepicker.js"></script>

<script>

$(document).ready(function(){
    // this bit needs to be loaded on every page where an ajax POST may happen
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                     = '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
      data: csfrData
    });
});
</script>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            
        </div>
    </nav>
    <section class="pad">
        <div class="container">

              <div class="row">
                <div class="col-xs-4"></div>
                <div class="col-xs-4">
                      <div class="login-box-body">
                        <p class="login-box-msg">Login</p>
                        <form class="form account-form" accept-charset="utf-8" method="post" action="">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <?php echo $msg;?>
                            <?php echo $this->session->flashdata('acc_activation_succ'); ?>
                            <div class="form-group">
                              <label for="username">Email</label>
                              <input type="text"  class="form-control" id="login-username" name="user_master[email]" placeholder="Username" tabindex="1">
                            </div>
                            <div class="form-group">
                              <label for="username">Password</label>
                              <input type="password" class="form-control" id="login-password" value="" name="user_master[password]" placeholder="Password" tabindex="2">
                            </div>
                            <div class="row">
                              

                              <div class="col-xs-12">
                                
                                <button type="submit" class="btn btn-primary" tabindex="4">Sign In</button>
                                <!-- <div class="pull-right links">
                                    <a href="<?php echo base_url() ?>account/auth/forgotPassword">Forgot Password</a>
                                </div> -->
                              </div>
                            </div>
                            <div></div>
                        </form>
                      </div>
                </div>    
                <div class="col-xs-4">

                </div>              
              </div>
              

</div>
<!-- <p class="links"><a href="<?php echo base_url(); ?>msme_unit/registration">Register as MSME</a> | <a href="<?php echo base_url(); ?>bank_unit/registration">Register as Bank/FI</a> | <a href="<?php echo base_url(); ?>tech_unit/registration">Register as Technical Expert</a></p> -->
</div>
</div>
    </section>
</body>
</html> 

