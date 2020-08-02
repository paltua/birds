<!-- DataTables CSS -->
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-plugins/dataTables.bootstrap.css"
    rel="stylesheet">
<!-- DataTables Responsive CSS -->
<link href="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-responsive/dataTables.responsive.css"
    rel="stylesheet">
<!-- DataTables JavaScript -->
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url().$resourceNameAdmin;?>vendor/datatables-responsive/dataTables.responsive.js"></script>

<script type="text/javascript">
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $("#imageId").show();
            $('#blah')
                .attr('src', e.target.result)
                .width(150)
                .height(100);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    $("#imageId").hide();
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });

    $('#dataTables-example').DataTable({
        responsive: true,
        order: [
            [1, "desc"]
        ],
        columnDefs: [{
            targets: 'no-sort',
            orderable: false
        }],
    });


});
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Gallery</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div id="msgShow"></div>
    <?php if($msg != ''):?>
    <div class="col-lg-12">
        <?php echo $msg ;?>
    </div>
    <?php endif;?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add Image
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <!-- Nav tabs -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>File input</label>
                                <input type="file" name="myFile" onchange="readURL(this);"
                                    accept="image/gif, image/jpeg, image/png">
                                <?php //echo form_error('filename', '<p class="text-danger">', '</p>'); ?>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div id="imageId" class=" alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <img id="blah" src="#" alt="" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="btn btn-success">Save</button>
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
                            <th class="no-sort">Image</th>
                            <th>Image URL</th>
                            <th>Created Date</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($list) > 0){
                            foreach ($list as $key => $value) {
                                if($key%2 == 0){
                                    $listClass = 'gradeC';
                                }else{
                                    $listClass = 'gradeU';
                                }
                        ?>
                        <tr class="<?php echo $listClass;?> ">
                            <td><?php if($value->g_path != ''){?>
                                <img src="<?php echo base_url('uploads/gallery/thumb/'.$value->g_path);?>">
                                <?php }?>
                            </td>
                            <td><?php echo base_url('uploads/gallery/thumb/'.$value->g_path);?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($value->created_date));?></td>
                            <td class="center">
                                <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/delete/<?php echo $value->g_id;?>"
                                    class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>