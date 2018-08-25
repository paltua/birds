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
                Add
            </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs">
                        <?php if(count($lang) > 0){
                                foreach ($lang as $key => $value) {
                        ?>
                        <li class="<?php if($key == 'en'):?>active<?php endif;?>">
                            <a href="#<?php echo $key;?>" data-toggle="tab"><?php echo $value;?></a>
                        </li>
                        <?php }} ?>
                        
                    </ul>

                    <!-- Tab panes -->
                    <form role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="tab-content">
                        
                            <?php if(count($lang) > 0){
                                    foreach ($lang as $key => $value) {
                            ?>
                            <div class="tab-pane fade in <?php if($key == 'en'):?>active<?php endif;?>" id="<?php echo $key;?>">
                                <h4><?php echo $value;?></h4>
                                <div class="row">
                                    <div class="col-lg-6">
                                        
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input class="form-control" type="text" name="acmd_name[<?php echo $key;?>]" value="<?php echo set_value('acmd_name['.$key.']'); ?>">
                                            <?php echo form_error('acmd_name['.$key.']', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                        <?php if($key == 'en'):?>
                                        <div class="form-group">
                                            <label>Parent Category</label>
                                            <select class="form-control" id="parent_id_<?php echo $key;?>" name="parent_id_<?php echo $key;?>">
                                                <option value="0">Select One</option>
                                                <?php if(count($parentCat) > 0){
                                                    foreach ($parentCat as $key => $value) {
                                                        if(count($value) > 0 && $key == 0){
                                                            foreach ($value as $key1 => $value1) {
                                                                
                                                        ?>
                                                        <option value="<?php echo $key1;?>"><?php echo $value1;?></option>
                                                        <?php if(isset($parentCat[$key1])){
                                                            foreach ($parentCat[$key1] as $key2 => $value2) {
                                                        ?>
                                                        <option value="<?php echo $key2;?>">---<?php echo $value2;?></option>
                                                        <?php }} ?>
                                                    <?php }}}} ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="exampleFormControlFile1">Category image</label>
                                            <input type="file" name="image_name" class="form-control-file" id="exampleFormControlFile1">
                                        </div>
                                    <?php endif;?>
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" rows="3" name="acmd_short_desc[<?php echo $key;?>]"><?php echo set_value('acmd_short_desc['.$key.']'); ?></textarea>
                                        </div>
                                    </div>
                                    <!-- /.col-lg-6 (nested) -->
                                </div>
                            </div>
                            <?php }} ?>
                            <button type="submit" class="btn btn-default btn-success">Save</button>
                            <button type="reset" class="btn btn-default btn-info">Reset</button>
                            
                        </div>
                    </form>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            
        </div>
    </div>
</div>