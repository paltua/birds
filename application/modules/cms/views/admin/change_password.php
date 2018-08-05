<div class="content">
	<div class="content-container">
		<div class="content-header clearfix">
			<h2 class="content-header-title">Change Password : <?php echo $this->session->userdata('org_name'); ?></h2>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php if(isset($msg) && ! empty($msg)){ echo $msg; } ?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<form action="" method="post" accept-charset="utf-8" name="form_change_pswd">
					<div class="form-group">
						<label>Old password</label>
						<input type="password"  class="form-control" name="old_password" data-id="Enter your old password." maxlength="10">
						<div class="clear"></div>
					</div>
					
					<div class="form-group">
						<label>New password</label>
						<input type="password"  class="form-control" name="new_password" data-id="Enter new password." maxlength="10">
						<div class="clear"></div>
					</div>
					
					<div class="form-group">
						<label>Confirm password</label>
						<input type="password"  class="form-control" name="confirm_password" data-id="Enter new password again." maxlength="10">
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