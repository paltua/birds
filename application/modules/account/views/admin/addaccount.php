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


<div class="row">
	<div class="col-md-12">
		<?php if(isset($msg) && ! empty($msg)){ echo $msg; } ?>
	</div>
</div>


<div class="row">
	<div class="col-md-6">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Web Master Registration</h3>
			</div>
			<div class="box-body">
				<form accept-charset="utf-8" method="post" action="<?php echo base_url() ?>account/sadmin/createWebMaster" name="form_create_wmaster">
					<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="form-group">
						<label>Name</label>
						<input type="text"  class="form-control" name="full_name" maxlength="150" value="<?php echo set_value('full_name'); ?>">
						<div class="clear"></div>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="text"  class="form-control" name="user_name" maxlength="70" value="<?php echo set_value('user_name'); ?>">
						<div class="clear"></div>
					</div>
					<div class="form-group">
						<label>Contact Number</label>
						<input type="text"  class="form-control" name="contact_no" maxlength="20" value="<?php echo set_value('contact_no'); ?>">
						<div class="clear"></div>
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password"  class="form-control" name="pwd" maxlength="10" value="<?php echo set_value('pwd'); ?>">
						<div class="clear"></div>
					</div>
					<div>
			            <button type="submit" class="btn btn-primary" name="save_btn" value="save">Save</button>
			            <button type="reset" class="btn btn-danger">Reset</button>
		        	</div>
				</form>
			</div>
		</div>
	</div>
</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
