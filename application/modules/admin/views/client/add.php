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



  <div class="">
    <div class="htext"><img src="<?php echo base_url();?>resources/<?php echo CURRENT_THEME;?>/img/st_2.png" class="icn"> ADD New Account Admin</div>
    <div class="widget_content" style="padding-bottom:50px">
      <div id="msg"><?php if($msg != ''){ echo $msg;} ?></div>
      <div class="row">
  
    		<form action="" method="post" accept-charset="utf-8">
    		  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    		  
    			  <div class="col-md-3">
                      <div class="form-group">
                        <label for="name" class="inputLabel">Account Name <i class="red">*</i></label>
                        <input type="text" name="org_master[org_name]" value="<?php echo set_value('org_master[org_name]'); ?>" id="org_master_name" class="form-control parsley-validated" data-required="true">
                      </div>
                      <div class="form-group">
                        <label for="name" class="inputLabel">Contact person <i class="red">*</i></label>
                        <input type="text" name="user_master[user_name]" value="<?php echo set_value('user_master[user_name]'); ?>" id="user_master_user_name" class="form-control parsley-validated" data-required="true">
                      </div>
                      <div class="form-group">
                        <label for="name" class="inputLabel">Contact email (login id) <i class="red">*</i></label>
                        <input type="text" name="user_master[email]" value="<?php echo set_value('user_master[email]'); ?>" id="user_master_email" class="form-control parsley-validated" data-required="true">
                      </div>
                      <div class="form-group">
                        <label for="name" class="inputLabel">Password <i class="red">*</i></label>
                        <input type="password" name="user_master[pwd]" value="<?php echo set_value('user_master[pwd]'); ?>" id="user_master_pwd" class="form-control parsley-validated" data-required="true">
                      </div>
                      <div class="form-group">
                        <label for="name" class="inputLabel">Phone</label>
                        <input type="text" name="org_master[phone]" value="" id="client_master_phone" class="form-control parsley-validated" >
                      </div>
                      <div class="form-group">
                        <label for="name" class="inputLabel">Allocate Points</label>
                        <input type="text" name="add_point" value="" id="add_point" class="form-control parsley-validated" >
                      </div>
            </div>
    			  <div class="col-md-12">
                <button type="submit"  class="btn btn-success">Add New</button>
                <button type="reset"  class="btn btn-primary">Reset</button>
            </div>
        </form>
    	</div>
	  </div>
	</div>
 



