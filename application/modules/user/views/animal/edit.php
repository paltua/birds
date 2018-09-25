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

    <?php if($details[0]->state_id > 0){?>
    	stateList(<?php echo $details[0]->country_id;?>, <?php echo $details[0]->state_id;?>);
    	cityList(<?php echo $details[0]->state_id;?>, <?php echo $details[0]->city_id;?>);
    <?php } ?>
        
    });

    function deleteImage(ami_id, id){
    	var conStatus = confirm('Are you sure to delete this Image.');
    	if(conStatus){
    		var url = '<?php echo base_url();?>user/animal/deleteImage';
	        $.post( url, { ami_id: ami_id}, function( data ) {
	        	$('#imagePreview_'+id).attr('src', '<?php echo base_url('public/'.THEME.'/images/add-image.jpg');?>');
	        	$('#buttunId_'+ami_id).remove();
	            $("#msgId").html(data.msg);
	        },'json');
    	}
    }

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
        	$("#animal_city_id").html('');
            $('#animal_city_id').trigger("chosen:updated");
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
				<li>Edit</li>
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
							<div class="col-lg-12" id="msgId">
						    <?php echo $msg ;?>
						    </div>
						<?php }?>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12"><h3 class="title">Edit</h3></div>
								<div class="col-md-12 multi-horizontal" data-for="name">
									<div class="form-group">
										<label class="form-control-label ">Category *</label>
		                                <select class="form-control form-check-input" name="cat_id" id="animal_cat_id" required="">
		                                	<option value="">Select One</option>
		                                	<?php if(count($category) > 0){
                                            foreach ($category as $value) {
                                            	$selectedCat = '';
                                            	if($value->acm_id == $details[0]->acm_id){
                                            		$selectedCat = 'selected';
                                            	}
                                                ?>
                                                <option value="<?php echo $value->acm_id;?>" <?php echo $selectedCat;?>><?php echo $value->acmd_name;?></option>
                                            <?php }} ?>
		                                </select>
		                                <?php echo form_error('cat_id', '<p class="text-danger">', '</p>'); ?>
	                            	</div>
	                            </div>
	                            <div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Title *</label>
		                                <input class="form-control input" name="amd_name" data-form-field="Title" placeholder="Title" value="<?php echo $details[0]->amd_name;?>" required="" id="Title-form4-4v" type="text">
		                                <?php echo form_error('amd_name', '<p class="text-danger">', '</p>'); ?>
	                            	</div>
	                            </div>
	                            <div class="col-md-12">
									<div class="form-check">
									  	<input class="form-check-input" type="radio" name="buy_or_sell" id="exampleRadios2" value="sell" <?php if($details[0]->buy_or_sell == 'sell'){?> checked <?php }?>>
									  	<label class="form-check-label" for="exampleRadios2">
									    	Sell
									  	</label>
									</div>
									<div class="form-check">
									  	<input class="form-check-input" type="radio" name="buy_or_sell" id="exampleRadios1" value="buy" <?php if($details[0]->buy_or_sell == 'buy'){?> checked <?php }?>>
									  	<label class="form-check-label" for="exampleRadios1">
									    	Buy
									  	</label>
									</div>
								</div>
	                            
								
	                            <div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Price</label>
		                                <input class="form-control input" name="amd_price" data-form-field="Price" placeholder="Price" value="<?php echo $details[0]->amd_price;?>" id="Price-form4-4v" type="text">
		                                
	                            	</div>
	                            </div>
	                            
	                            <div class="col-md-12" data-for="email">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Description *</label>
		                                <textarea class="form-control textarea" name="amd_short_desc" data-form-field="Textarea" placeholder="Description" required="" id="textarea-form4-4v">
		                                	<?php echo $details[0]->amd_short_desc;?>
		                                </textarea>
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
		                                <select class="form-control select" name="country_id" id="animal_country_id" required="">
		                                	<option>Select One</option>
		                                	<?php if(count($country) > 0){
		                                		$country_id = $details[0]->country_id;
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
								        <select class="form-control select" id="animal_city_id" name="city_id" ></select>
	                            	</div>
	                            </div>

	                    	</div>
						</div>	
						<div class="col-md-12">
							<h3 class="title">Pictures</h3>
							<div class="row">
							<?php if(count($images) > 0){?>
								<?php foreach ($images as $keyIm => $valueIm) {?>
									<div class="col-md-2">
										<input type="file" onchange="readURL(this, '<?php echo $keyIm + 1;?>');" data-id="<?php echo $keyIm + 1;?>" name="ami_path_<?php echo $keyIm + 1;?>" />
										<input type="hidden" name="addedImage[<?php echo $keyIm + 1;?>]" value="<?php echo $valueIm->ami_id;?>">
										<div class="col-md-12">
											<img id="imagePreview_<?php echo $keyIm + 1;?>" src="<?php echo base_url(UPLOAD_PROD_PATH.$valueIm->ami_path);?>">
										</div>
										<input type="radio" name="default" <?php if($valueIm->ami_default == 1){?>checked=""<?php }?> value="<?php echo $keyIm + 1;?>">Default
										<input type="button" class="btn btn-danger deleteImg" id="buttunId_<?php echo $valueIm->ami_id;?>" value="Delete" onclick="return deleteImage(<?php echo $valueIm->ami_id;?>, <?php echo $keyIm + 1;?>)" name="" >
									</div>
								<?php } ?>
							<?php } ?>	
							<?php for ($i=count($images) + 1; $i < 7  ; $i++) { ?>
									
								<div class="col-md-2">
									<input type="file" onchange="readURL(this, '<?php echo $i;?>');" data-id="<?php echo $i;?>" name="ami_path_<?php echo $i;?>" />
									<input type="hidden" name="addedImage[<?php echo $i;?>]" value="0">
									<div class="col-md-12">
										<img id="imagePreview_<?php echo $i;?>" src="<?php echo base_url('public/'.THEME.'/images/add-image.jpg');?>">
									</div>
									<input type="radio" name="default" <?php if($i == 1){?>checked=""<?php }?> value="<?php echo $i;?>">Default
								</div>
							<?php }?>
								
							</div>					
						</div>
                        <div class="input-group-btn col-md-4">
                        	<div class="row">
                            <button type="submit" class="btn btn-primary btn-form display-4">Publish Item</button>
                            <a href="<?php echo base_url('user/animal/listing');?>" class="btn btn-primary btn-form">Back</a>
                            </div>
                        </div>
					</div>
				</form>					
			</div>
		</div>
	</div>
</section>
