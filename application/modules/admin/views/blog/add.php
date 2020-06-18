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
        // $('#allDataId').on('click','.showChart', function() { 
        //     $('.myMeterModal').find(".modal-content").html('');
        //     $('.myMeterModal').modal('show');
        //     var meter_link = $(this).attr("meter-link");
        //     $('.myMeterModal').find(".modal-content").load(meter_link);
        // });
        $("#animal_cat_id").chosen({no_results_text: "Oops, No Parent Category found!"});
        $("#animal_p_cat_id").chosen({no_results_text: "Oops, No Sub Category found!"});

        $("#animal_p_cat_id").change(function(){
            var parent_id = parseInt($(this).val());
            var url = '<?php echo base_url();?>admin/animal_master/getChildCategory'
            $.post( url, { parent_id: parent_id, selectedChild : ''}, function( data ) {
                $("#animal_cat_id").html(data.data);
                $('#animal_cat_id').trigger("chosen:updated");
            },'json');
        });

        $("#animal_country_id").chosen({no_results_text: "Oops, No Country found!"});
        $("#animal_state_id").chosen({no_results_text: "Oops, No State found!"});
        $("#animal_city_id").chosen({no_results_text: "Oops, No City found!"});

        $("#animal_country_id").change(function(){
            var country_id = parseInt($(this).val());
            var url = '<?php echo base_url();?>admin/animal_master/getStateList'
            $.post( url, { country_id: country_id, selectedCountry : ''}, function( data ) {
                $("#animal_state_id").html(data.data);
                $('#animal_state_id').trigger("chosen:updated");
                $("#animal_city_id").html('');
                $('#animal_city_id').trigger("chosen:updated");
            },'json');
        });

        $("#animal_state_id").change(function(){
            var state_id = parseInt($(this).val());
            var url = '<?php echo base_url();?>admin/animal_master/getCityList'
            $.post( url, { state_id: state_id, selectedCountry : ''}, function( data ) {
                $("#animal_city_id").html(data.data);
                $('#animal_city_id').trigger("chosen:updated");
            },'json');
        });
    });

</script>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.contentTextarea').summernote({
            height: 500,   //set editable area's height
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
                Add
            </div>
               
                    

<form role="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="about_bird">
    
        
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- Nav tabs -->
                
                
                <div class="col-lg-12">
                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea class="form-control" rows="5" name="about_bird_"> </textarea>
                            </div>
                            <div class="form-group">
                                <label>Long Description</label>
                                <textarea class="form-control contentTextarea" rows="35" name="about_bird_"></textarea>
                            </div>
                        </div>
                    <button type="submit" value="about_birds" class="btn btn-default btn-success">Update</button>
                </div>
            </div>
            <!-- /.panel-body -->
        
    </div>
</form>
                
            </div>
            <!-- /.panel -->
            
        </div>
    </div>
</div>