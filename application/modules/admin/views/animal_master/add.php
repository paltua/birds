<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Animal </h1>
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
                    <form role="form" method="post">
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