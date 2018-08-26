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
                        <p class="login-box-msg">Sign up</p>
                        <form class="form account-form" accept-charset="utf-8" method="post" action="">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <?php echo $msg;?>
                            <div class="form-group">
                              <label for="username">Name</label>
                              <input type="text"  class="form-control" id="login-username" name="contact_us[name]" placeholder="Please enter Name" tabindex="1">
                              <?php echo form_error('contact_us[name]', '<p class="text-danger">', '</p>'); ?>
                            </div>
                            <div class="form-group">
                              <label for="username">Mobile</label>
                              <input type="text"  class="form-control" id="login-username" name="contact_us[mobile]" placeholder="Please enter Mobile" tabindex="2">
                              <?php echo form_error('contact_us[mobile]', '<p class="text-danger">', '</p>'); ?>
                            </div>
                            <div class="form-group">
                              <label for="username">Email</label>
                              <input type="email"  class="form-control" id="login-username" name="contact_us[email]" placeholder="Please enter Email" tabindex="3">
                              <?php echo form_error('contact_us[email]', '<p class="text-danger">', '</p>'); ?>
                            </div>
                            <div class="form-group">
                              <label for="username">Message</label>
                              <textarea class="form-control" rows="3" name="contact_us[desccription]"><?php echo set_value('contact_us[desccription]'); ?></textarea>
                              <?php echo form_error('contact_us[desccription]', '<p class="text-danger">', '</p>'); ?>
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

