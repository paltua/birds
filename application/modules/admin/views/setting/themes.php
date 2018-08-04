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
    
    $("#logo").change(function () {
        readURLlogo(this);
    });

  
});

$(document).on('click', '#update_logo', function() {
    $("#msgLogo").html('');
    $("#logo_preview").show();
    var data = fileTypeCheck(1);
    if(data == 0){
        var logo = $("#logo")[0].files[0];
        var form_data = new FormData();                  
        form_data.append("logo", logo);              
        $.ajax({
            url: "<?php echo $url;?>/setting/upload_logo",
            dataType: 'html',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(data){
                $("#msgLogo").html(data);
                setTimeout('$("#msgLogo").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
            }
        });
    }else{
      $("#logo_preview").hide();
      $("#msgLogo").html('<div class="alert alert-danger" role="alert"><a aria-label="close" data-dismiss="alert" class="close" href="javascript:void(0)">×</a>Invalid extension!</div>');
      setTimeout('$("#msgLogo").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
    }
    
});

$(document).on('click', '#update_css_file', function() {
    $("#msgCss").html('');
    var data = fileTypeCheck(2);
    if(data == 0){
      var css = $("#css_file")[0].files[0];
      var form_data = new FormData();                  
      form_data.append("css_file", css);
      $.ajax({
          url: "<?php echo $url;?>/setting/upload_css_file",
          dataType: 'html',
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,                         
          type: 'post',
          success: function(data){
              $("#msgCss").html(data);
              setTimeout('$("#msgCss").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
          }
      });
    }else{
      $("#msgCss").html('<div class="alert alert-danger" role="alert"><a aria-label="close" data-dismiss="alert" class="close" href="javascript:void(0)">×</a>Invalid extension!</div>');
      setTimeout('$("#msgCss").hide("slow").html("")',<?php echo MSG_HIDE_TIME;?>);
    }
});



function readURLlogo(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
        $("#logo_preview").show();
        $('#logo_preview').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}



function fileTypeCheck(type = 1){
  var retStatus = 0;
  if(type == 1){
    var files = $('#logo')[0].files;
    var len = $('#logo').get(0).files.length;
    var exten = ['png'];
  }else if(type == 2){
    var files = $('#css_file')[0].files;
    var len = $('#css_file').get(0).files.length;
    var exten = ['css'];
  }
  for (var i = 0; i < len; i++) {
      f = files[i];
      var ext = f.name.split('.').pop().toLowerCase();
      if ($.inArray(ext, exten) == -1) {
          retStatus = 1;
      }
  }
  return retStatus;
}


</script>

<div class="">

  <div class="content">

    <div class="content-container">

      <div class="content-header clearfix">
        <h2 class="content-header-title">Theme settings</h2>
      </div>

      <div class="row">
		<div id="msg"><?php if($msg != ''){ echo $msg;} ?></div>
		<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
		  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
		  <div class="col-md-6 grid-item" id="col_md_6_id_1">
      <div class="portlet">
          <div class="portlet-header">
            <h3>Logo</h3>
          </div> <!-- /.portlet-header -->
          <div class="portlet-content ">
                  <div>
                      <div id="msgLogo"></div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="file" id="logo" name="logo" class="form-control_1 parsley-validated" data-required="true">
                          <img id="logo_preview" height="50" width="75" src="<?php echo !empty($themeDetails[0]->key_value)?base_url().'resources/settings/'.$themeDetails[0]->key_value:'';?>"> 
                        </div>
                        <button type="button" id="update_logo" class="btn btn-success">Save</button>

                      </div>
                  </div>                       
          </div> <!-- /.portlet-content -->
      </div>
    </div>

    <div class="col-md-6 grid-item" id="col_md_6_id_1">
      <div class="portlet">
          <div class="portlet-header">
            <h3>
              CSS File
            </h3>
          </div> <!-- /.portlet-header -->
          <div class="portlet-content ">
              <div>
                  <div id="msgCss"></div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="file" id="css_file" name="css_file" class="form-control_1 parsley-validated" data-required="true">
                    </div>
                    <button type="button" id="update_css_file" class="btn btn-success">Save</button>
                  </div>
              </div>
          </div> <!-- /.portlet-content -->
      </div>
    </div>
		</form>
	  </div>
	</div>
  </div>
</div>


