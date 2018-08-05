<?php $this->lang->load('Fieldname');?>

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

<div>

  <div class="content">

    <div class="content-container">

      <div class="content-header clearfix">
        <h2 class="content-header-title">Update User</h2>
      </div>
		<?php if($msg != ''){ echo $msg;} ?>
      <div class="row">
		<form action="" method="post" accept-charset="utf-8">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="account_user_id" value="<?php echo $user[0]->user_id; ?>">
          <div>
			<div class="col-md-3">
				<div class="form-group"><label for="contact_person"><?php echo $this->lang->line('user_name');?></label><input type="text" name="user_master[user_name]" value="<?php echo $user[0]->user_name;?>" id="user_master_user_name"  class="form-control parsley-validated" data-required="true"  /></div>
				<div class="form-group"><label for="contact_email"> Email</label><input type="text" name="user_master[email]" value="<?php echo $user[0]->email;?>" id="user_master_email"  class="form-control parsley-validated" data-required="true" /></div>
				<div class="form-group"><label for="contact_email">Password</label><input type="password" name="new_pwd" value="" id="user_master_pwd"  class="form-control parsley-validated" data-required="true" /></div>
				
				<div class="form-group">
					<label>Status</label>
					<label class="radio-inline">
						<input type="radio" name="user_master[user_status]" id="optionsRadiosInline1" value="active" <?php if($user[0]->user_status == 'active'){?>checked=""<?php }?>>Active
					</label>
					<label class="radio-inline">
						<input type="radio" name="user_master[user_status]" id="optionsRadiosInline2" value="inactive" <?php if($user[0]->user_status == 'inactive'){?>checked=""<?php }?>>Inactive
					</label>
				</div>
			</div>
			<div class="col-md-12">
				  <button type="submit"  class="btn btn-info">Update</button>
              </div>
		  </div>
		</form>
	  </div>
	</div>
  </div>
</div>


