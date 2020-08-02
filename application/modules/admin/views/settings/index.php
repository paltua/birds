<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.contentTextarea').summernote({
        height: 500, //set editable area's height
        codemirror: { // codemirror options
            theme: 'monokai'
        }
    });
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';

    $.ajaxSetup({
        data: csfrData
    });
});
</script>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Site Settings
            <div class="pull-right">
                <!-- <a href="<?php echo base_url('admin/'.$controller.'/add');?>" class="btn btn-info">
        <i class="fa fa-plus-circle"></i> Add</a> -->
            </div>
        </h1>

    </div>
    <!-- /.col-lg-12 -->
</div>
<?php if($msg != ''):?>
<div class="col-lg-12">
    <?php echo $msg ;?>
</div>
<?php endif;?>
<div id="msgShow"></div>
<div class="row">
    <div class="col-lg-12">
        <?php $this->load->view('settings/you_tube');?>
        <?php $this->load->view('settings/about_us');?>
        <?php $this->load->view('settings/pd_charitable_trust');?>
    </div>
</div>