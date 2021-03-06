<script type="text/javascript">
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $("#imageDivId").show();
            $("#imageId").show();
            $('#blah')
                .attr('src', e.target.result)
                .width(200)
                .height(100);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    $("#imageDivId").hide();
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });
    $(".amiDefault").click(function() {
        var url = '<?php echo $defaultPath; ?>';
        $.post(url, {
            ei_id: $(this).val(),
            em_id: '<?php echo $em_id; ?>'
        }, function(data) {
            //alert(data.msg);
            $("#msgShow").html(data.msg);
        }, "json");
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
    <div id="msgShow"></div>
    <?php if ($msg != '') : ?>
    <div class="col-lg-12">
        <?php echo $msg; ?>
    </div>
    <?php endif; ?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add Image
            </div>

            <div class="panel-body">
                <form role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <!-- Nav tabs -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>File input</label>
                                <input type="file" name="myFile" onchange="readURL(this)"
                                    accept="image/gif, image/jpeg, image/png">
                                <?php //echo form_error('filename', '<p class="text-danger">', '</p>'); 
                                ?>
                            </div>

                        </div>
                        <div class="col-lg-3 " id="imageDivId">
                            <div id="imageId" class=" alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <img width="200" height="100" id="blah" src="#" alt="" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="btn btn-success">Save</button>
                            <a href="<?php echo base_url('admin/' . $controller . '/index'); ?>"
                                class="btn btn-warning"><i class="fa fa-undo"></i> Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Image Gallery
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th>Image </th>
                            <th>Image Path</th>
                            <th>Default</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($list) > 0) {
                            foreach ($list as $key => $value) {
                                if ($key % 2 == 0) {
                                    $listClass = 'gradeC';
                                } else {
                                    $listClass = 'gradeU';
                                }
                        ?>
                        <tr class="<?php echo $listClass; ?> ">
                            <td>
                                <div class="col-sm-3">
                                    <img width="200" height="100"
                                        src="<?php echo base_url(); ?><?php echo UPLOAD_EVENT_PATH; ?>thumb/<?php echo $value->ei_image_name; ?>"
                                        alt="" />
                                </div>
                            </td>
                            <td>Full Image :
                                <?php echo base_url(); ?><?php echo UPLOAD_EVENT_PATH; ?><?php echo $value->ei_image_name; ?><br>
                                Thumbnail Image :
                                <?php echo base_url(); ?><?php echo UPLOAD_EVENT_PATH; ?>thumb/<?php echo $value->ei_image_name; ?>
                            </td>
                            <td>
                                <div class="radio">
                                    <label><input type="radio" class="amiDefault" name="ami_default"
                                            value="<?php echo $value->ei_id; ?>"
                                            <?php if ($value->is_default == 1) {
                                                                                                                                                        echo 'checked';
                                                                                                                                                    } ?>>
                                    </label>
                                </div>
                            </td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($value->created_date)); ?></td>
                            <td class="center">
                                <a onclick="return confirm('Are you sure to delete this image?')"
                                    href="<?php echo base_url(); ?>admin/<?php echo $controller; ?>/image_delete/<?php echo $value->ei_id; ?>"
                                    class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                            </td>
                        </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>