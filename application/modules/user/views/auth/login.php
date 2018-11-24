
<div class="col-md-6 login-block">
  <h2 class="title">Login</h2>

  <form class="block" action="" method="post">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="action" value="login">
    <div class="row">
      <?php if($action == 'login'):?>
      <?php echo $msg;?>
      <?php endif;?>
      <div class="col-md-12 multi-horizontal" data-for="name">
        <div class="form-group">
          <label class="form-control-label ">Email</label>
            <input class="form-control input" name="user_master[email]" placeholder="Email" tabindex="1" data-form-field="Email" required="" id="name-form4-4v" type="email">
        </div>
      </div>
      <div class="col-md-12 multi-horizontal" data-for="phone">
        <div class="form-group">
          <label class="form-control-label ">Password</label>
            <input class="form-control input" id="password-field" value="" name="user_master[password]" placeholder="Password" tabindex="2" data-form-field="Password" placeholder="Password" required="" id="phone-form4-4v" type="password">
            <span toggle="#password-field" class="fas fa fa-fw fa-eye field-icon toggle-password"></span>
        </div>
      </div>      
      <div class="input-group-btn col-md-6">
          <button href="" type="submit" class="btn btn-primary btn-form display-4">Login</button>
      </div>
      <div class="col-md-6 forgot-pass">
        <a href="<?php echo base_url('user/auth/forgotPassword');?>">Forgot Password</a>
      </div>
  </div>
  </form>
</div>






