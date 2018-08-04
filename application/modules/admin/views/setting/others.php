<div class="container">
  	<div class="content">
	    <div class="content-container">
			<div class="content-header clearfix">
			<h2 class="content-header-title">Others Setting</h2>
			</div>
			<div id="msg"><?php if($msg != ''){ echo $msg;} ?></div>
			<div class="row">
				<form action="" method="post" accept-charset="utf-8" id="setting_form">
					<div class="form-group col-md-3">
		                <label for="name">Site Title <i class="red">*</i></label>
		                <input type="text" id="site_title" name="data[<?php echo $OtherDetails[0]->setting_id;?>]" value="<?php echo $OtherDetails[0]->key_value;?>" class="form-control parsley-validated" data-required="true" >
						<span id="motor_master_tage_name_span"></span>
		            </div>
		            <div class="form-group col-md-3">
		                <label for="name">Footer Caption <i class="red">*</i></label>
		                <input type="text" id="footer_caption" name="data[<?php echo $OtherDetails[1]->setting_id;?>]" value="<?php echo $OtherDetails[1]->key_value;?>" class="form-control parsley-validated" data-required="true" >
						<span id="motor_master_tage_name_span"></span>
		            </div>
		            <div class="form-group col-md-3">
		                <label for="name">Contact Number <i class="red">*</i></label>
		                <input type="text" id="contact_number" name="data[<?php echo $OtherDetails[2]->setting_id;?>]" value="<?php echo $OtherDetails[2]->key_value;?>" class="form-control parsley-validated" data-required="true" >
						<span id="motor_master_tage_name_span"></span>
		            </div>
		            <div class="form-group col-md-3">
		                <label for="name">From Mail-ID <i class="red">*</i></label>
		                <input type="text" id="from_email" name="data[<?php echo $OtherDetails[3]->setting_id;?>]" value="<?php echo $OtherDetails[3]->key_value;?>" class="form-control parsley-validated" data-required="true" >
						<span id="motor_master_tage_name_span"></span>
		            </div>
		            <button type="submit" id="button_part_1_id" class="btn btn-success">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>