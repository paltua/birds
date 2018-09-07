<div class="col-md-6 register-block">
          <h2 class="title">Register <span>an account for free</span></h2>
          <form class="block" action="" method="post">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="action" value="register">
            <div class="row">
              <?php if($action == 'register'):?>
              <?php echo $msg;?>
              <?php endif;?>
              <div class="col-md-12 multi-horizontal" data-for="name">
                <div class="form-group">
                  <label class="form-control-label ">Name *</label>
                    <input class="form-control input" name="user_master[name]" data-form-field="Name" placeholder="Your Name" required="" id="name-form4-4v" type="text" value="<?php echo set_value('user_master[name]');?>">
                    <?php echo form_error('user_master[name]', '<p class="text-danger">', '</p>'); ?>
                </div>
              </div>
              <div class="col-md-12" data-for="email">
                <div class="form-group">
                  <label class="form-control-label ">Email *</label>
                    <input class="form-control input" name="user_master[email]" data-form-field="Email" placeholder="Email" required="" id="email-form4-4v" type="email" value="<?php echo set_value('user_master[email]');?>">
                    <?php echo form_error('user_master[email]', '<p class="text-danger">', '</p>'); ?>
                </div>
              </div>
              <div class="col-md-12 multi-horizontal" data-for="phone">
                <div class="form-group">
                  <label class="form-control-label ">Mobile No *</label>
                    <input class="form-control input" name="user_master[mobile]" data-form-field="Mobile" placeholder="Mobile" required="" id="phone-form4-4v" type="text" value="<?php echo set_value('user_master[mobile]');?>">
                    <?php echo form_error('user_master[mobile]', '<p class="text-danger">', '</p>'); ?>
                </div>
              </div>
              <div class="col-md-12 multi-horizontal" data-for="password">
                <div class="form-group">
                  <label class="form-control-label ">Password</label>
                  <input class="form-control input" name="password" data-form-field="Password" placeholder="Password" required="" id="password-form4-4v" type="password">
                  <?php echo form_error('password', '<p class="text-danger">', '</p>'); ?>
                </div>
              </div>
              <div class="col-md-12 multi-horizontal" data-for="repassword">
                <div class="form-group">
                  <label class="form-control-label ">Confirm password</label>
                  <input class="form-control input" name="cnfPassword" data-form-field="repassword" placeholder="Confirm Password" required="" id="repassword-form4-4v" type="password">
                  <?php echo form_error('cnfPassword', '<p class="text-danger">', '</p>'); ?>
              </div>
            </div>         
            <div class="input-group-btn col-md-12">
                <button href="" type="submit" class="btn btn-primary btn-form display-4">Create Account</button>
            </div>
          </div>
          </form>
        </div>        
      










