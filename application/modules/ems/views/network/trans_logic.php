<?php if($this->session->userdata('user_id') == 2){?>
<div id="snackbar" class="alert alert-info">
    <a href="javascript:void(0);" style="color:#fff;" class="showChart" meter-link="<?php echo base_url();?>ems/transformer">
        <strong>Transformer Performance Curve</strong></a>

  <a href="#" class="close" data-dismiss="alert" aria-label="close" style="color:#fff;padding-left:10px;">&times;</a>
</div>

<script>

    $( document ).ready(function() {
        var x = document.getElementById("snackbar")
        x.className = "show alert alert-info";
        //setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    });
</script>
<?php }?>