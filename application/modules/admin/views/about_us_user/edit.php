<link rel="stylesheet" href="<?php echo base_url();?>public/admin/vendor/chosen/chosen.min.css">
<script src="<?php echo base_url();?>public/admin/vendor/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript"> 

    $(document).ready(function(){
        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        //alert('<?php echo $this->security->get_csrf_hash(); ?>');
        $.ajaxSetup({
          data: csfrData
        });
        $("#parent_id_en").chosen({no_results_text: "Oops, No Transformer found!"});
    });
</script>  

    
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Product Types</h1>
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
                Edit
            </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Tab panes -->
                    <form role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input class="form-control" type="text" name="data[name]" value="<?php echo $editData[0]->name;?>">
                                            <?php echo form_error('data[name]', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Mobile</label>
                                            <input class="form-control" type="text" name="data[mobile]" value="<?php echo $editData[0]->mobile;?>">
                                            <?php echo form_error('data[mobile]', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input class="form-control" type="text" name="data[email]" value="<?php echo $editData[0]->email;?>">
                                            <?php echo form_error('data[email]', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Position</label>
                                            <input class="form-control" type="text" name="data[position]" value="<?php echo $editData[0]->position;?>">
                                            <?php echo form_error('data[position]', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                    </div>
                                    <!-- /.col-lg-6 (nested) -->
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input class="form-control" type="text" name="data[name]" value="<?php echo $editData[0]->name;?>">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-success">Save</button>
                            <a href="<?php echo base_url('admin/'.$controller);?>" class="btn btn-default btn-info">Cancel</a>
                            
                        </div>
                    </form>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            
        </div>
    </div>
</div>