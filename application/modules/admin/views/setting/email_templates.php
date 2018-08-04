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
    <?php if($msg != ''){ ?>
      setTimeout('$("#msg").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
    <?php } ?>
});
</script>

<div class="container">

  <div class="content">

    <div class="content-container">

      <div class="content-header clearfix">
        <h2 class="content-header-title">Email Template</h2>
      </div>

      <div class="row">
		<div id="msg"><?php if($msg != ''){ echo $msg;} ?></div>
		<form action="" method="post" accept-charset="utf-8">
		  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
		  <div>
			  <div class="col-md-12">
            <div class="portlet">
              <div class="portlet-header">
                <h3>Forgot Password</h3>
              </div> <!-- /.portlet-header -->
              <div class="portlet-content ">
                  <div>
                      <div id="msgCss"></div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="name">Subject<i class="red">*</i></label>
                        </div>
                        <div class="form-group">
                          <label for="name">Temptale <i class="red">*</i></label>
                        </div>
                        
                      </div>
                  </div>
              </div> <!-- /.portlet-content -->
          </div>
        </div>
			  <div class="col-md-12">
            <button type="submit"  class="btn btn-success">Save</button>
        </div>
		  </div>
		</form>
	  </div>
	</div>
  </div>
</div>


