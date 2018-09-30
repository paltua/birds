
<script>
    $(document).ready(function() {
        
        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        
        $.ajaxSetup({
          data: csfrData
        });
    });
</script>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Site Settings
            <div class="pull-right"><!-- <a href="<?php echo base_url('admin/'.$controller.'/add');?>" class="btn btn-info">
        <i class="fa fa-plus-circle"></i> Add</a> --></div></h1>
        
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <?php if($msg != ''):?>
    <div class="col-lg-12">
    <?php echo $msg ;?>
    </div>
    <?php endif;?>
    <div id="msgShow"></div>
    <div class="col-lg-12">
        <?php $this->load->view('settings/you_tube');?>
        <?php $this->load->view('settings/about_bird');?>
    </div>
</div>