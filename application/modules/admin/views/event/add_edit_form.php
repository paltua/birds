<script src="<?php echo base_url(); ?>public/admin/vendor/date-time/moment.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>public/admin/vendor/date-time/bootstrap-datetimepicker.css">
<script src="<?php echo base_url(); ?>public/admin/vendor/date-time/bootstrap-datetimepicker.js"></script>

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
    $("#country_id").chosen({
        no_results_text: "Oops, No Country found!"
    });

    $("#state_id").chosen({
        no_results_text: "Oops, No State found!"
    });

    $("#city_id").chosen({
        no_results_text: "Oops, No City found!"
    });

    $("#program_id").chosen({
        no_results_text: "Oops, No Programme found!"
    });

    $("#country_id").change(function() {
        var parent_id = parseInt($(this).val());
        var url = '<?php echo base_url(); ?>admin/<?php echo $controller; ?>/getStateList'
        $.post(url, {
            country_id: parent_id,
            selectedChild: ''
        }, function(resData) {
            $("#state_id").html(resData.data);
            $('#state_id').trigger("chosen:updated");
            $("#state_id").chosen({
                no_results_text: "Oops, No State found!"
            });
        }, 'json');
    });

    $("#state_id").change(function() {
        var parent_id = parseInt($(this).val());
        var url = '<?php echo base_url(); ?>admin/<?php echo $controller; ?>/getCityList'
        $.post(url, {
            state_id: parent_id,
            selectedChild: ''
        }, function(resData) {
            $("#city_id").html(resData.data);
            $('#city_id').trigger("chosen:updated");
            $("#city_id").chosen({
                no_results_text: "Oops, No City found!"
            });
        }, 'json');
    });
    <?php if ($action == 'add') { ?>
    $('#datetimepicker1').datetimepicker();
    $('#datetimepicker2').datetimepicker();
    // set_value('event_title')
    <?php } else { ?>
    // $('#datetimepicker1').data("DateTimePicker").date('<?php echo $editData[0]->event_start_date_time; ?>');
    // $('#datetimepicker2').data("DateTimePicker").date('<?php echo $editData[0]->event_end_date_time; ?>');
    $('#datetimepicker1').datetimepicker({
        date: new Date('<?php echo $editData[0]->event_start_date_time; ?>')
    });
    $('#datetimepicker2').datetimepicker({
        date: new Date('<?php echo $editData[0]->event_end_date_time; ?>')
    });
    <?php } ?>

});
</script>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.contentTextarea').summernote({
        height: 220, //set editable area's height
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
                <input type="hidden" name="em_id" value="<?php echo $em_id; ?>">

                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <div class="panel panel-default ">
                        <div class="panel-heading">
                            Short Details
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input class="form-control" type="text" name="event_title"
                                                value="<?php echo $action == 'add' ? set_value('event_title') : $editData[0]->event_title; ?>">
                                            <?php echo form_error('event_title', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Programme</label>
                                            <select class="form-control" id="program_id" name="program_id[]"
                                                placeholder="Select One" multiple>
                                                <?php if (count($proData) > 0) {
                                                    foreach ($proData as $value) {
                                                        $selected = '';
                                                        if (count($proAssignData) > 0) {
                                                            foreach ($proAssignData as $keyCat => $valueCat) {
                                                                if ($valueCat->program_id == $value->program_id) {
                                                                    $selected = 'selected';
                                                                }
                                                            }
                                                        }
                                                ?>
                                                <option value="<?php echo $value->program_id; ?>"
                                                    <?php echo $selected; ?>>
                                                    <?php echo $value->program_title; ?>
                                                </option>
                                                <?php }
                                                } ?>
                                            </select>
                                            <?php echo form_error('program_id', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" rows="3"
                                                name="event_short_desc"><?php echo $action == 'add' ? set_value('event_short_desc') : $editData[0]->event_short_desc; ?> </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default ">
                        <div class="panel-heading">
                            Date & Time Details
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Start Date & Time</label>
                                            <div class='input-group date datetimepicker' id='datetimepicker1'>
                                                <input type='text' name="event_start_date_time" class="form-control"
                                                    value="<?php echo $action == 'add' ? set_value('event_start_date_time') : $editData[0]->event_start_date_time; ?>"
                                                    placeholder="<?php echo $action == 'add' ? set_value('event_start_date_time') : $editData[0]->event_start_date_time; ?>" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <?php echo form_error('event_start_date_time', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>End Date & Time</label>
                                            <div class='input-group date datetimepicker' id='datetimepicker2'>
                                                <input type='text' name="event_end_date_time" class="form-control"
                                                    value="<?php echo $action == 'add' ? set_value('event_end_date_time') : $editData[0]->event_end_date_time; ?>"
                                                    placeholder="<?php echo $action == 'add' ? set_value('event_end_date_time') : $editData[0]->event_end_date_time; ?>" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <?php echo form_error('event_end_date_time', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default ">
                        <div class="panel-heading">
                            Address
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select class="form-control" id="country_id" name="country_id"
                                                placeholder="Select One">
                                                <?php if (count($country) > 0) {
                                                    foreach ($country as $value) {
                                                        $selected = '';
                                                        if ($action == 'edit' && $value->id == $editData[0]->country_id) {
                                                            $selected = 'selected';
                                                        }
                                                ?>
                                                <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>>
                                                    <?php echo $value->name; ?>
                                                </option>
                                                <?php }
                                                } ?>
                                            </select>
                                            <?php echo form_error('country_id', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>State</label>
                                            <select class="form-control" id="state_id" name="state_id">
                                                <option value="">Select</option>
                                                <?php if (count($state) > 0) {
                                                    foreach ($state as $value) {
                                                        $selected = '';
                                                        if ($action == 'edit' && $value->id == $editData[0]->state_id) {
                                                            $selected = 'selected';
                                                        }
                                                ?>
                                                <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>>
                                                    <?php echo $value->name; ?>
                                                </option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>City</label>
                                            <select class="form-control" id="city_id" name="city_id"
                                                placeholder="Select One">
                                                <?php if (count($city) > 0) {
                                                    foreach ($city as $value) {
                                                        $selected = '';
                                                        if ($action == 'edit' && $value->id == $editData[0]->city_id) {
                                                            $selected = 'selected';
                                                        }
                                                ?>
                                                <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>>
                                                    <?php echo $value->name; ?>
                                                </option>
                                                <?php }
                                                } ?>
                                            </select>
                                            <?php echo form_error('city_id', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Pin</label>
                                            <input class="form-control" type="text" name="pin"
                                                value="<?php echo $action == 'add' ? set_value('pin') : $editData[0]->pin; ?>">
                                            <?php echo form_error('pin', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea class="form-control" rows="3"
                                                name="address"><?php echo $action == 'add' ? set_value('address') : $editData[0]->address; ?> </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default ">
                        <div class="panel-heading">
                            Long Details
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Long Description</label>
                                    <textarea class="form-control contentTextarea"
                                        name="event_long_desc"><?php echo $action == 'add' ? set_value('event_long_desc') : $editData[0]->event_long_desc; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>About</label>
                                    <textarea class="form-control contentTextarea"
                                        name="event_about"><?php echo $action == 'add' ? set_value('event_about') : $editData[0]->event_about; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Objective</label>
                                    <textarea class="form-control contentTextarea"
                                        name="event_objectives"><?php echo $action == 'add' ? set_value('event_objectives') : $editData[0]->event_objectives; ?></textarea>
                                </div>
                            </div>
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