<div class="row">
	<div class="col-md-12">
		<?php if(isset($msg) && ! empty($msg)){ echo $msg; } ?>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Account Info</h3>
			</div>
			<div class="box-body">
				<div class="form-group">
					<label for="first_name">Account Holder Name</label>
					<input disabled type="text" class="form-control" id="first_name" value="<?php echo $user_details[0]->full_name ?>" name="first_name">
				</div>
				<div class="form-group">
					<label for="last_name">Registered Email / User Id</label>
					<input disabled type="text" class="form-control" id="last_name" value="<?php echo $user_details[0]->user_name ?>" name="last_name">
				</div>
			</div>

			<div class="box-header">
				<h3 class="box-title">Change Password</h3>
			</div>
			<div class="box-body">
				<form accept-charset="utf-8" method="post" action="<?php echo base_url() ?>account/sadmin/setpersonal" name="form_change_pswd">
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