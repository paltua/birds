<link rel="stylesheet" href="<?php echo base_url(); ?>public/admin/vendor/chosen/chosen.min.css">
<script src="<?php echo base_url(); ?>public/admin/vendor/chosen/chosen.jquery.min.js"></script>
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
        var url = '<?php echo base_url(); ?>admin/blog/getChildCategory'
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
        height: 250, //set editable area's height
        codemirror: { // codemirror options
            theme: 'monokai'
        }
    });
});
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $page_title; ?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <?php if ($msg != '') : ?>
    <div class="col-lg-12">
        <?php echo $msg; ?>
    </div>
    <?php endif; ?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo ucfirst($action); ?>
            </div>
            <form role="form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                    value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="program_id" value="<?php echo $program_id; ?>">

                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input class="form-control" type="text" name="program_title"
                                        value="<?php echo $action == 'add' ? set_value('program_title') : $editData[0]->program_title; ?>">
                                    <?php echo form_error('program_title', '<p class="text-danger">', '</p>'); ?>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Type</label>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioInline2" name="program_status"
                                            value="upcoming"
                                            <?php if ($action == 'add' || ($action == 'edit' && $editData[0]->program_status == 'upcoming')) { ?>
                                            checked <?php } ?> class="custom-control-input">
                                        <label class="custom-control-label" for="customRadioInline2">Upcoming</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioInline1" name="program_status"
                                            value="ongoing"
                                            <?php if ($action == 'edit' && $editData[0]->program_status == 'ongoing') { ?>
                                            checked <?php } ?> class="custom-control-input">
                                        <label class="custom-control-label" for="customRadioInline1">Ongoing
                                        </label>
                                    </div>

                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioInline2" name="program_status"
                                            value="completed"
                                            <?php if ($action == 'edit' && $editData[0]->program_status == 'completed') { ?>
                                            checked <?php } ?> class="custom-control-input">
                                        <label class="custom-control-label" for="customRadioInline2">Completed</label>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Short Description</label>
                            <textarea class="form-control" rows="5"
                                name="program_short_desc"><?php echo $action == 'add' ? set_value('program_short_desc') : $editData[0]->program_short_desc; ?> <?php echo set_value('program_short_desc'); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Long Description</label>
                            <textarea class="form-control contentTextarea" rows="25"
                                name="program_desc"><?php echo $action == 'add' ? set_value('program_desc') : $editData[0]->program_desc; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Objectives</label>
                            <textarea class="form-control contentTextarea" rows="25"
                                name="program_objectives"><?php echo $action == 'add' ? set_value('program_objectives') : $editData[0]->program_objectives; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>About</label>
                            <textarea class="form-control contentTextarea" rows="25"
                                name="program_about"><?php echo $action == 'add' ? set_value('program_about') : $editData[0]->program_about; ?></textarea>
                        </div>
                    </div>
                    <button type="submit" value="about_birds"
                        class="btn btn-default btn-success"><?php echo $action == 'add' ? 'Create' : 'Update'; ?></button>
                    <a href="<?php echo base_url('admin/' . $controller . '/index'); ?>"
                        class="btn btn-default btn-primary">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.panel-body -->
    </div>
</div>
<!-- /.panel -->