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

    function readURL(input) {
    	//console.log(input);
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function(e) {
	            //$('#imagePreview_').css('background-image', 'url('+e.target.result +')');
	            $('#imagePreview_'+input.attr('data-id'))attr('src', e.target.result);
	            $('#imagePreview_'+input.attr('data-id')).hide();
	            $('#imagePreview_'+input.attr('data-id')).fadeIn(650);
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
				<li>Publish Listing</li>
			</ul>
		</div>
	</div>
</section>
<section class="inner-layout">
	<div class="container">		
		<div class="inner-content">
			<div class="add-new-pd">
				<form class="row block">					
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12"><h3 class="title">Add free listing</h3></div>
								<div class="col-md-12 multi-horizontal" data-for="name">
									<div class="form-group">
										<label class="form-control-label ">Category *</label>
		                                <select class="form-control" name="cat_id" id="animal_cat_id" required="">
		                                	<option>Select One</option>
		                                	<?php if(count($category) > 0){
                                            foreach ($category as $value) {
                                                ?>
                                                <option value="<?php echo $value->acm_id;?>"><?php echo $value->acmd_name;?></option>
                                            <?php }} ?>
		                                </select>
	                            	</div>
	                            </div>
	                            <div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Title *</label>
		                                <input class="form-control input" name="amd_name" data-form-field="Title" placeholder="Title" required="" id="Title-form4-4v" type="text">
	                            	</div>
	                            </div>
	                            <div class="col-md-12 multi-horizontal" data-for="price">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Price</label>
		                                <input class="form-control input" name="phone" data-form-field="Price" placeholder="Price" required="" id="Price-form4-4v" type="text">
	                            	</div>
	                            </div>
	                            <!-- <div class="col-md-12">
		                            <div class="form-check">
										<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
									  	<label class="form-check-label" for="exampleRadios1">
									    	Enter price
									  	</label>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-check">
									  	<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
									  	<label class="form-check-label" for="exampleRadios2">
									    	Free
									  	</label>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-check">
									  	<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3">
									  	<label class="form-check-label" for="exampleRadios2">
									    	Check with seller
									  	</label>
									</div>
								</div> -->
								

	                            <div class="col-md-12" data-for="email">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Description *</label>
		                                <textarea class="form-control textarea" name="amd_desc" data-form-field="Textarea" placeholder="Textarea" required="" id="textarea-form4-4v"></textarea>
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
                                                foreach ($country as $value) {
                                            	$selected = '';
                                            	if($value->id == $country_id){
                                            		$selected = 'selected';
                                            	}
                                                ?>
                                                <option value="<?php echo $value->id;?>" <?php echo $selected;?>><?php echo $value->name;?></option>
                                            <?php }} ?>
		                                </select>
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
								        <select class="form-control select" id="animal_city_id" name="city_id[]" multiple></select>
	                            	</div>
	                            </div>
	                            <div class="col-md-12"><h3 class="title">Your's Information</h3></div>
	                            <!-- <div class="col-md-12 multi-horizontal" data-for="name">
									<div class="form-group">
										<label class="form-control-label ">Name</label>
		                                <input class="form-control input" name="name" data-form-field="Name" placeholder="Your Name" required="" id="name-form4-4v" type="text">
	                            	</div>
	                            </div>
	                            <div class="col-md-12 multi-horizontal" data-for="phone">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Phone No</label>
		                                <input class="form-control input" name="phone" data-form-field="Phone" placeholder="Phone" required="" id="phone-form4-4v" type="text">
	                            	</div>
	                            </div>
	                            <div class="col-md-12" data-for="email">
	                            	<div class="form-group">
		                            	<label class="form-control-label ">Email</label>
		                                <input class="form-control input" name="email" data-form-field="Email" placeholder="Email" required="" id="email-form4-4v" type="text">
	                            	</div>
	                            </div> -->
	                            <div class="col-md-12" data-for="phone">
	                            	<div class="custom-control custom-checkbox my-1 mr-sm-2">
									    <input type="checkbox" class="custom-control-input" id="customControlInline">
									    <label class="custom-control-label" for="customControlInline">Show Mobile on listing page</label>
									</div>
	                            </div>
	                    	</div>
						</div>	
						<div class="col-md-12">
							<h3 class="title">Pictures</h3>
							<div class="row">
								
							<?php for ($i=1; $i < 2 ; $i++) { ?>
									
								<div class="col-md-3">
									<input type="file" onchange="readURL(this);" data-id="<?php echo $i;?>" name="ami_path[]" accept=".png, .jpg, .jpeg" />
									<div class="col-md-12">
										<img id="imagePreview_<?php echo $i;?>" src="">
									</div>
								</div>
							<?php }?>
								
							</div>					
						</div>
                        <div class="input-group-btn col-md-12">
                            <button href="" type="submit" class="btn btn-primary btn-form display-4">Publish Item</button>
                        </div>
					</div>
				</form>					
			</div>
		</div>
	</div>
</section>
