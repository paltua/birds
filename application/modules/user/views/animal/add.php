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
        $("#animal_cat_id").chosen({no_results_text: "Oops, No Category found!"});

        $("#animal_country_id").chosen({no_results_text: "Oops, No Country found!"});
        $("#animal_state_id").chosen({no_results_text: "Oops, No State found!"});
        $("#animal_city_id").chosen({no_results_text: "Oops, No City found!"});

        $("#animal_country_id").change(function(){
            var country_id = parseInt($(this).val());
            var state_id = '';
            stateList(country_id, state_id);
        });

        $("#animal_state_id").change(function(){
            var state_id = parseInt($(this).val());
            var city_ids = '';
            cityList(state_id, city_ids);
        });
        
    });

    function stateList(country_id, state_id){
    	var url = '<?php echo base_url();?>user/product/getStateList';
        $.post( url, { country_id: country_id, selectedChild : state_id}, function( data ) {
            $("#animal_state_id").html(data.data);
            $('#animal_state_id').trigger("chosen:updated");
            $("#animal_city_id").html('');
            $('#animal_city_id').trigger("chosen:updated");
        },'json');
    }

    function cityList(state_id, city_ids){
    	var url = '<?php echo base_url();?>user/product/getCityList';
        $.post( url, { state_id: state_id, selectedChild : city_ids}, function( data ) {
            $("#animal_city_id").html(data.data);
            $('#animal_city_id').trigger("chosen:updated");
        },'json');
    }

    function readURL(input, id) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function(e) {
	            //$('#imagePreview_').css('background-image', 'url('+e.target.result +')');
	            $('#imagePreview_'+id).attr('src', e.target.result);
	            $('#imagePreview_'+id).hide();
	            $('#imagePreview_'+id).fadeIn(650);
	        }
	        reader.readAsDataURL(input.files[0]);
	    }
	}
	

</script>

<section class="innerbanner">
	<div class="banner-cont">
		<h1 class="title">Publish Listing</h1>
		<div class="breadcramb">
			<ul>
				<li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
				<li><a href="<?php echo base_url('user/animal/listing');?>">Publish Listing</a></li>
				<li>Add</li>
			</ul>
		</div>
	</div>
</section>
<?php $this->load->view('animal/disc');?>
<section class="inner-layout">
	<div class="container">		
		<div class="inner-content">
			<div class="add-new-pd">
				<form class="row block" method="post" action="" enctype="multipart/form-data">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">					
					<div class="row">
						<?php if($msg != ''){?>
							<div class="col-lg-12">
						    <?php echo $msg ;?>
						    </div>
						<?php }?>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12"><h3 class="title">Add free listing</h3></div>
								<div class="col-md-6 multi-horizontal" data-for="name">
									<div class="form-group">
										<label class="form-control-label ">Category *</label>
		                                <select class="col-lg-12 form-control  edit-pushclass" name="cat_id" id="animal_cat_id" >
		                                	<option value="">Select One</option>
		                                	<?php if(count($category) > 0){
                                            foreach ($category as $value) {
                                                ?>
                                                <option value="<?php echo $value->acm_id;?>"><?php echo $value->acmd_name;?></option>
                                            <?php }} ?>
		                                </select>
		                                <?php echo form_error('cat_id', '<p class="text-danger">', '</p>'); ?>
	                            	</div>
	                            </div>
	                            <div class="col-md-6 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Title *</label>
		                                <input class="form-control input" name="amd_name" value="<?php echo set_value('amd_name'); ?>" data-form-field="Title" placeholder="Title"  id="Title-form4-4v" type="text">
		                                <?php echo form_error('amd_name', '<p class="text-danger">', '</p>'); ?>
	                            	</div>
	                            </div>
	                            <div class="col-md-12 ">
								
									<label class="form-control-label "><b>Transaction Type *</b></label><br>
									<div class="form-check">
									  	<input class="form-check-input" type="radio" name="buy_or_sell" id="exampleRadios2" value="sell" >
									  	<label class="form-check-label" for="exampleRadios2">
									    	Want to Sell
									  	</label>
									</div>
									<div class="form-check">
									  	<input class="form-check-input" type="radio" name="buy_or_sell" id="exampleRadios1" value="buy">
									  	<label class="form-check-label" for="exampleRadios1">
									    	Want to Buy
									  	</label>
									</div>
									<?php echo form_error('buy_or_sell', '<p class="text-danger">', '</p>'); ?>
									
								</div>
	                            
								
	                            <div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Price</label>
		                                <input class="form-control input" name="amd_price" value="<?php echo set_value('amd_price'); ?>" data-form-field="Price" placeholder="Price" id="Price-form4-4v" type="text">
		                                
	                            	</div>
	                            </div>
	                            
	                            <div class="col-md-12" data-for="email">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Description *</label>
		                                <textarea class="form-control textarea" name="amd_short_desc" data-form-field="Textarea" placeholder="Description" id="textarea-form4-4v"><?php echo set_value('amd_short_desc'); ?></textarea>
		                                <?php echo form_error('amd_short_desc', '<p class="text-danger">', '</p>'); ?>
	                            	</div>
	                            </div>
	                            
                        	</div>
                    	</div>					
                    	
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12"><h3 class="title">Location</h3></div>
								<div class="col-md-12 multi-horizontal" data-for="name">
									<div class="form-group">
										<label class="form-control-label ">Country *</label>
		                                <select class="form-control select" name="country_id" id="animal_country_id" >
		                                	<option value="">Select One</option>
		                                	<?php if(count($country) > 0){
                                                foreach ($country as $value) {
                                            	$selected = '';
                                            	if($value->id == $country_id){
                                            		$selected = 'selected';
                                            	}
                                                ?>
                                                <option value="<?php echo $value->id;?>" <?php echo $selected;?>><?php echo $value->name;?></option>
                                            <?php }} ?>
		                                </select>
		                                <?php echo form_error('country_id', '<p class="text-danger">', '</p>'); ?>
	                            	</div>
	                            </div>
	                            <div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
								      <label for="disabledTextInput">State</label>
								      <select class="form-control select" id="animal_state_id" name="state_id"></select>
								    </div>
	                            </div>
								<div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">City</label>
								        <select class="form-control select" id="animal_city_id" name="city_id"></select>
	                            	</div>
	                            </div>
	                            
	                    	</div>
						</div>	
						<div class="col-md-12">
							<h3 class="title">Pictures</h3>
							<div class="row">
								
							<?php for ($i=1; $i < 7 ; $i++) { ?>
									
								<div class="col-md-2">
									
									<div class="upldimg-box">
										<img id="imagePreview_<?php echo $i;?>" src="<?php echo base_url('public/'.THEME.'/images/add-image.jpg');?>">
										<div class="upload-btn-wrapper wr-upload">
										<button class="btn-dflt"></button>
										<input type="file" onchange="readURL(this, '<?php echo $i;?>');" data-id="<?php echo $i;?>" name="ami_path_<?php echo $i;?>" />
									</div>
									</div>
									<div class="dflt">
										<input type="radio" name="default" <?php if($i == 1){?>checked=""<?php }?> value="<?php echo $i;?>">Default
									</div>
									
									
								</div>
							<?php }?>
								
							</div>					
						</div>
                        <div class="input-group-btn col-md-12">
                            <button type="submit" class="btn btn-primary btn-form display-4">Publish Item</button>
							<a href="<?php echo base_url('user/animal/listing');?>" class="btn btn-warning btn-form display-4">Cancel</a>
                        </div>
					</div>
				</form>					
			</div>
		</div>
	</div>
</section>
