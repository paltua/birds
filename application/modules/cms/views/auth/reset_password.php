<script>
$(document).ready(function(){ 
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                     = '<?php echo $this->security->get_csrf_hash(); ?>';
    $.ajaxSetup({
      data: csfrData
    });

    <?php if($msg != ''){ ?>
      setTimeout('$("#msg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
    <?php } ?>
    $('.abc, .flexContaner').height($(window).height());
});
</script>

<div class="midbox" style="width:340px;margin-left: 447px;">
     <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Password Reset</h3>
            
          </div>
          <div class="box-body" style="padding-top:0;">
              <form class="form account-form" accept-charset="utf-8" method="post" action="">
                <?php echo $msg;?>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $this->uri->segment(4) ?>">
                <input type="hidden" name="one_time_check" id="one_time_check" value="<?php echo $this->uri->segment(5) ?>">
                <div class="form-group">
                    <label for="forgot-email" class="placeholder-hidden">New Password</label>
                    <input type="text" name="password" class="form-control" placeholder="New Password" tabindex="1">
                  </div> 

                  <div class="form-group">
                    <label for="forgot-email" class="placeholder-hidden">Confirm Password</label>
                    <input type="text" name="passconf" class="form-control" placeholder="Confirm Password" tabindex="1">
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" tabindex="2">
                      Reset Password &nbsp; <i class="fa fa-refresh"></i>
                    </button>
                  </div> 
                  <div class="form-group">
                    <a href="<?php echo base_url();?>account/auth/login"><i class="fa fa-angle-double-left"></i> &nbsp;Back to Login</a>
                  </div> 
                </form>
          </div>      
      </div> 
 </div>

