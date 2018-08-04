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
                <h3 class="panel-title"><b>Admin Login</b></h3>
            </div>
            <div class="panel-body">
              <?php if($msg != ''): echo $msg; endif;?>
            <form class="form account-form" accept-charset="utf-8" method="post" action="">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <fieldset>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="email"  class="form-control" id="login-username" name="admin_user_master[email]" placeholder="Username" tabindex="1" autofocus>
                        </div>
                        <div class="form-group">
                        <label for="username">Password</label>
                        <input type="password" class="form-control" id="login-password" value="" name="admin_user_master[password]" placeholder="Password" tabindex="2">
                        </div>
                        <!-- <div class="checkbox">
                            <label>
                                <input name="remember" type="checkbox" value="Remember Me">Remember Me
                            </label>
                        </div> -->
                        <!-- Change this to a button or input when using this as a form -->
                        <button type="submit" class="btn btn-lg btn-success btn-block" tabindex="4">Login</button>
                        <div class="pull-right links">
                            <a href="<?php echo base_url(ADMIN_NAME.'/auth/forgotPassword') ?>">Forgot Password</a>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>