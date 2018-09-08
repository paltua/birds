<script>
$(document).ready(function(){ 
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                     = '<?php echo $this->security->get_csrf_hash(); ?>';
    $.ajaxSetup({
      data: csfrData
    });
});
</script>
<section class="inner-layout">
  <div class="container">   
    <div class="inner-content">
      <div class="row">

  
 <div class="midbox" style="width:340px;margin-left: 447px;">
     <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Forgot Password </h3>
            
          </div>
          <div class="box-body" style="padding-top:0;">
              <form class="form account-form" accept-charset="utf-8" method="post" action="">
                <?php echo $msg;?>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                    <label for="forgot-email" class="placeholder-hidden">Your Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Your Email" tabindex="1">
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" tabindex="2">
                      Reset Password &nbsp; <i class="fa fa-refresh"></i>
                    </button>
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <a href="<?php echo base_url();?>user/auth/login"><i class="fa fa-angle-double-left"></i> &nbsp;Back to Login</a>
                  </div> <!-- /.form-group -->
                </form>
          </div>      
      </div> 
 </div>
 </div>  
    </div>
  </div>
</section>


