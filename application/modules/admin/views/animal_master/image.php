<script type="text/javascript">
    $("#imageId").hide();
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#imageId").show();
                $('#blah')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(100);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Animal Image</h1>
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
                Add Image
            </div>
                <!-- /.panel-heading -->
            <div class="panel-body">
                <form role="form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <!-- Nav tabs -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>File input</label>
                                <input type="file" name="myFile" onchange="readURL(this);" accept="image/gif, image/jpeg, image/png">
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
                            <a href="" class="btn btn-warning">Cancel</a> 
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
                            <!-- <th>Image Title</th> -->
                            <th>Created Date</th>
                            <th>Action</th>
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
                            <td><div class="col-sm-3" >
                                <img width="150" height="100" src="<?php echo base_url();?>uploads/animal/<?php echo $value->ami_path;?>" alt="" /></div></td>
                            <!-- <td><?php echo $value->ami_title;?></td> -->
                            <td><?php echo date("F j, Y, g:i a", strtotime($value->ami_created_date));?></td>
                            <td class="center">
                                <a href="<?php echo base_url();?>admin/<?php echo $controller;?>/image_delete/<?php echo $value->ami_id;?>" class="btn btn-primary btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>