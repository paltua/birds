

<div class="widget">
    <div class="htext"><img src="<?php echo base_url();?>resources/<?php echo CURRENT_THEME;?>/img/st_2.png" class="icn">Manage Super Admin</div>
    <div class="widget_content" style="padding-bottom:50px">
      <div class="listingMsg">
		<div id="msg"><?php if($msg != ''){ echo $msg;} ?></div>
		</div>
        <div class="row">
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Account Info</h3>
				</div>
				<div class="box-body">
					<form accept-charset="utf-8" method="post" action="">
						<input type="hidden" name="update_mode" value="acc_info">
						<div class="form-group">
							<label for="first_name" class="inputLabel">Name <i class="red">*</i></label>
							<input type="text" class="form-control" id="first_name" value="<?php echo $user[0]->name;?>" name="admin_users[name]">
						</div>
						<div class="form-group">
							<label for="last_name" class="inputLabel">Email <i class="red">*</i></label>
							<input type="text" class="form-control" id="last_name" value="<?php echo $user[0]->email;?>" name="admin_users[email]">
						</div>
						<button class="btn btn-green" type="submit">Update</button>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Change Password</h3>
				</div>
				<div class="box-body">
					<form accept-charset="utf-8" method="post" action="">
						<input type="hidden" name="update_mode" value="acc_pwd">
						<div class="form-group">
							<label for="new_password" class="inputLabel">New Password <i class="red">*</i></label>
							<input type="password" class="form-control" id="new_password" value="" name="new_password"></div>
						<div class="form-group">
							<label for="retype_password" class="inputLabel">Retype Password <i class="red">*</i></label>
							<input type="password" class="form-control" id="retype_password" value="" name="retype_password"></div>
						<button class="btn btn-green" type="submit">Update</button>
					</form>
				</div>
			</div>
		</div>
    </div>
	</div>
  </div>


<script>
	$(document).ready(function(){
		$(".form-control").each(function(){
      	var inputVal = $.trim($(this).val());
        var inputId = $.trim($(this).attr('id'));
        if(inputVal == ''){
            $(this).parent().find('label[class="inputLabel"]').show();
        }else{
            $(this).parent().find('label[class="inputLabel"]').hide();
        }
    });

    $(".form-control").focus(function() {
        var inputId = $.trim($(this).attr('id'));
        $(this).parent().find('label[class="inputLabel"]').hide();
    });
    
    $(".form-control").blur(function() {
        var inputVal = $.trim($(this).val());
        var inputId = $.trim($(this).attr('id'));
        if(inputVal == ''){
            $(this).parent().find('label[class="inputLabel"]').show();
        }else{
            $(this).parent().find('label[class="inputLabel"]').hide();
        }
    });

		<?php if($msg != ''){ ?>
			setTimeout('$("#msg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
		<?php } ?>
	});	
</script>