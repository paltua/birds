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
  
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><b>Forgot Password</b></h3>
            </div>
            <div class="panel-body">
              <?php if($msg != ''): echo $msg; endif;?>
              <form class="form account-form" accept-charset="utf-8" method="post" action="">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                    <label for="forgot-email" class="placeholder-hidden">Your Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Your Email" tabindex="1">
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" tabindex="2">
                      Reset Password &nbsp; <i class="fa fa-refresh"></i>
                    </button>
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <a href="<?php echo base_url(ADMIN_NAME);?>"><i class="fa fa-angle-double-left"></i> &nbsp;Back to Login</a>
                  </div> <!-- /.form-group -->
                </form>
          </div>      
      </div> 
    </div>
 </div>




