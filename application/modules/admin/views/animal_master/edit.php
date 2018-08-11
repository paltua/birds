<link rel="stylesheet" href="<?php echo base_url();?>public/admin/vendor/chosen/chosen.min.css">
<script src="<?php echo base_url();?>public/admin/vendor/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript"> 

    $(document).ready(function(){
        // $('#allDataId').on('click','.showChart', function() { 
        //     $('.myMeterModal').find(".modal-content").html('');
        //     $('.myMeterModal').modal('show');
        //     var meter_link = $(this).attr("meter-link");
        //     $('.myMeterModal').find(".modal-content").load(meter_link);
        // });
        $("#animal_cat_id").chosen({no_results_text: "Oops, No Transformer found!"});
    });

</script>
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
                Edit
            </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs">
                        <?php if(count($editData) > 0){
                                foreach ($editData as $key => $value) {
                        ?>
                        <li class="<?php if($value->language == 'en'):?>active<?php endif;?>">
                            <a href="#<?php echo $value->language;?>" data-toggle="tab"><?php echo $value->lang_name;?></a>
                        </li>
                        <?php }} ?>
                        
                    </ul>

                    <!-- Tab panes -->
                    <form role="form" method="post">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="tab-content">
                        
                            <?php if(count($editData) > 0){
                                    foreach ($editData as $key => $value) {
                            ?>
                            <input type="hidden" name="data[<?php echo $value->amd_id;?>][language]" value="<?php echo $value->language;?>">
                            <?php if($value->language == 'en'):?>
                            <input type="hidden" name="eng_lang_id" value="<?php echo $value->amd_id;?>">
                            <?php endif;?>
                            <div class="tab-pane fade in <?php if($value->language == 'en'):?>active<?php endif;?>" id="<?php echo $value->language;?>">
                                <h4><?php echo $value->lang_name;?></h4>
                                <div class="row">
                                    <div class="col-lg-4">
                                        
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input class="form-control" type="text" name="data[<?php echo $value->amd_id;?>][amd_name]" value="<?php echo $value->amd_name; ?>">
                                            <?php echo form_error('data['.$value->amd_id.'][amd_name]', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" rows="3" name="data[<?php echo $value->amd_id;?>][amd_short_desc]"><?php echo $value->amd_short_desc; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input class="form-control" type="text" name="data[<?php echo $value->amd_id;?>][amd_price]" value="<?php echo $value->amd_price; ?>">
                                            <?php echo form_error('acmd_name['.$value->amd_id.'][amd_price]', '<p class="text-danger">', '</p>'); ?>
                                        </div>
                                        
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control" id="animal_cat_id" name="acr[]" multiple>
                                                <option value="">Select
                                                <?php if(count($animal_cat) > 0){
                                                    $catsArr = array();
                                                    if($value->all_cat != ''){
                                                        $catsArr = explode(',', $value->all_cat);
                                                    }
                                                    foreach ($animal_cat as $key => $values) {
                                                        $selected = '';
                                                        if(in_array($values->acm_id, $catsArr)){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $values->acm_id;?>" <?php echo $selected;?>><?php echo $values->acmd_name;?></option>
                                                    <?php }} ?>
                                            </select>
                                        </div>
                                    </div>    
                                    </div>
                                    <!-- /.col-lg-6 (nested) -->
                                </div>
                            </div>
                            <?php }} ?>
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