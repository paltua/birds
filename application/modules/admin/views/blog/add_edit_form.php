<link rel="stylesheet" href="<?php echo base_url();?>public/admin/vendor/chosen/chosen.min.css">
<script src="<?php echo base_url();?>public/admin/vendor/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });
    $("#animal_cat_id").chosen({
        no_results_text: "Oops, No Parent Category found!"
    });
    $("#animal_p_cat_id").chosen({
        no_results_text: "Oops, No Sub Category found!"
    });

    $("#animal_p_cat_id").change(function() {
        var parent_id = parseInt($(this).val());
        var url = '<?php echo base_url();?>admin/blog/getChildCategory'
        $.post(url, {
            parent_id: parent_id,
            selectedChild: ''
        }, function(data) {
            $("#animal_cat_id").html(data.data);
            $('#animal_cat_id').trigger("chosen:updated");
        }, 'json');
    });
});
</script>

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
});
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $page_title;?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <?php if($msg != ''):?>
    <div class="col-lg-12">
        <?php echo $msg ;?>
    </div>
    <?php endif;?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo ucfirst($action);?>
            </div>
            <form role="form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                    value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="blog_id" value="<?php echo $blog_id;?>">

                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input class="form-control" type="text" name="title"
                                        value="<?php echo $action=='add'?set_value('title'):$editData[0]->title; ?>">
                                    <?php echo form_error('p_acr', '<p class="text-danger">', '</p>'); ?>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Parent Category</label>
                                    <select class="form-control" id="animal_p_cat_id" name="p_cat_id"
                                        placeholder="Select One">
                                        <?php if(count($animal_cat) > 0){
                                        foreach ($animal_cat as $value) {
                                            $selected = '';
                                            if(count($catData) > 0){
                                                foreach ($catData as $keyCat => $valueCat) {
                                                    if($valueCat->acm_id == $value->acm_id){
                                                        $selected = 'selected';
                                                    }
                                                }
                                            }
                                            ?>
                                        <option value="<?php echo $value->acm_id;?>" <?php echo $selected;?>>
                                            <?php echo $value->acmd_name;?>
                                        </option>
                                        <?php }} ?>
                                    </select>
                                    <?php echo form_error('p_acr', '<p class="text-danger">', '</p>'); ?>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Sub Category</label>
                                    <select class="form-control" id="animal_cat_id" name="c_cat_id[]" multiple>
                                        <option value="">Select</option>
                                        <?php if(count($animal_sub_cat) > 0){
                                        foreach ($animal_sub_cat as $value) {
                                            $selected = '';
                                            if(count($catData) > 0){
                                                foreach ($catData as $keyCat => $valueCat) {
                                                    if($valueCat->acm_id == $value->acm_id){
                                                        $selected = 'selected';
                                                    }
                                                }
                                            }
                                            ?>
                                        <option value="<?php echo $value->acm_id;?>" <?php echo $selected;?>>
                                            <?php echo $value->acmd_name;?>
                                        </option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Short Description</label>
                            <textarea class="form-control" rows="5"
                                name="short_desc"><?php echo $action=='add'?set_value('short_desc'):$editData[0]->short_desc; ?> <?php echo set_value('short_desc'); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Long Description</label>
                            <textarea class="form-control contentTextarea" rows="35"
                                name="long_desc"><?php echo $action=='add'?set_value('long_desc'):$editData[0]->long_desc; ?></textarea>
                        </div>
                    </div>
                    <button type="submit" value="about_birds"
                        class="btn btn-default btn-success"><?php echo $action=='add'?'Create':'Update';?></button>
                    <a href="<?php echo base_url('admin/'.$controller.'/index');?>"
                        class="btn btn-default btn-primary">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.panel-body -->
    </div>
</div>
<!-- /.panel -->