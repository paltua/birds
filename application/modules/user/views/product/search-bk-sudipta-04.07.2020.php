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
        <?php if($country_id > 0){?>
        	var country_id = parseInt(<?php echo $country_id;?>);
            var state_id = parseInt(<?php echo $state_id;?>);
            stateList(country_id, state_id);
        <?php } ?>
        <?php if($country_id > 0 && $state_id > 0){
        	$city_ids = '';
        	if(count($city_id) > 0){
        		$city_ids = implode(',', $city_id);
        	}
        	?>
            var state_id = parseInt(<?php echo $state_id;?>);
            var city_ids = '<?php echo $city_ids;?>';
            cityList(state_id, city_ids);
        <?php } ?>

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

		$("#loadMoreId").click(function(){
			var formData = $("#searchForm").serialize();
			var url = "<?php echo base_url('user/product/getAjaxData');?>";
			$.post( url, formData, function(result){
				$("#productListDivId").append(result.html);
				$("#startPage").val(result.startPage);
				if(result.loaderStatus == 'hide'){
					$("#loadMoreId").hide();
				}
			},'json');
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

</script>

<section class="innerbanner">
	<div class="banner-cont">
		<h1 class="title">Product List</h1>
		<div class="breadcramb">
			<ul>
				<li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
				<li>Product</li>
			</ul>
		</div>
	</div>
</section>


<?php $this->load->view('cms/category');?>

<section class="inner-layout">
	<div class="container">		
		<div class="inner-content">
			<div class="product-listing-layout">
				<div class="row">
					<?php if(count($selectedCatDet) > 0){ ?>
						<div class="col-lg-4 col-md-12 col-sm-12">
							<div class="aside-layout">
								<div class="aside-item">
									<div id="verticalTab">
										<ul class="resp-tabs-list">
											<?php foreach ($selectedCatDet as $key => $value) {
												?>
										<li><?php echo $value->lang_name;?></li>
									<?php }} ?>
										</ul>
										<div class="resp-tabs-container">
											<?php if(count($selectedCatDet) > 0){
												foreach ($selectedCatDet as $key => $value) {
												?>
											<div>
												<p><?php echo $value->acmd_name;?></p>
												<p><?php echo $value->acmd_short_desc;?></p>
											</div>
											<?php } ?>						
										</div>
									</div>
								</div>
								<div class="aside-item"></div>
							</div>
						</div>
					<?php } ?>	
					<div class="col-lg-<?php if($selectedCatId > 0){ ?>8<?php }else{?>12<?php }?> col-md-12 col-sm-12  <?php if($selectedCatId > 0){ ?>cont-part<?php }?>">
						<!-- <div class="row">
							<div class="col-md-12">
								<?php $postType = (($buy_or_sell == 'sell')?'Sale':$buy_or_sell);?>
								<h3> Post Search <?php if($buy_or_sell != ''){ echo 'for '.ucwords($postType); }?></h3>
								<div class="pd-search-filter-layout">									
									<form class="row" method="post" id="searchForm">
										<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="startPage" id="startPage" value="<?php echo $limit['start'];?>">
										<input type="hidden" name="category_id" value="<?php echo $category_id;?>">
										<input type="hidden" name="buy_or_sell" value="<?php echo $buy_or_sell;?>">
										<div class="col-md-3">
											<div class="form-group">
												<label>Keyword</label>
												<input type="text" name="keyWord" value="<?php echo $keyWord;?>" placeholder="I'm Looking For">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Country</label>
												<select id="animal_country_id" name="country_id">
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
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>State</label>
												<select id="animal_state_id" name="state_id">
													
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>City</label>
												<select id="animal_city_id" name="city_id[]" multiple>
													
												</select>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label>Price</label>
												<div class="example">
										            <input class="range-example-input-2" type="text" min="<?php echo $price['min'];?>" max="<?php echo $price['max'];?>" value="<?php echo $price['min_select'];?>,<?php echo $price['max_select'];?>" name="price" step="10" />
										        </div>
											</div>
										</div>
										<div class="commmon wrapbutton">
										<div class="col-md-3 pull-left">
											<div class="form-group pet-listss-gr">
												<input class="" type="radio" name="choices" value="pet" <?php if($choices == 'pet'){ echo 'checked';}?> /><label>Pet's Listings</label>
												
											</div>
										</div>
										<div class="col-md-3 pull-left">	
											<div class="form-group pet-listss-gr">
												<input class="" type="radio" name="choices" value="dip" <?php if($choices == 'dip'){ echo 'checked';}?> /><label>Dipankar's Choice</label>
												
											</div>
										</div>
										<div class="col-md-3 pull-left">	
											<div class="form-group pet-listss-gr">
												<input class="" type="radio" name="choices" value="food" <?php if($choices == 'food'){ echo 'checked';}?> /><label>Foods & Accessories Listings</label>
												
											</div>
										</div>
													</div>
										
											
										<div class="col-md-12 but-mar-bottom pull-left">
											<div class="example">
									            <button type="submit" class="btn btn-info">Search</button>
									        </div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="product-item">
							<div id="horizontalTab" class="pd-list-tab">
								<div class="row">
									<div class="col-md-12">
										<ul class="resp-tabs-list clearfix">
											<li>All Results</li>
											<!-- <li>Personal</li>
											<li>Company</li> -->
										</ul>
										<span class="tot-res-right">Total Result <?php echo $prodListCount;?></span>
									</div>
									
								</div>
								<div class="resp-tabs-container">
									<div>
										<div class="row" id="productListDivId">
											<?php 
											$viewData['list'] = $prodListAll;
											$this->load->view('user/product/single', $viewData);
											?>
										</div>
										<div class="buttonLoadMoreClass">
											<?php if($prodListCount > $limit['perPage']){?>
											<a href="javascript:void(0);" id="loadMoreId" class="btn btn-primary float-right">Load More</a>
											<?php } ?>
										</div>
										
									</div>
									
									
									<!-- <div>
										<div class="row">
											<?php 
											// $viewData['list'] = $prodListUser;
											// $this->load->view('user/product/single', $viewData);
											?>
										</div>
									</div> -->

									<!-- <div>
										<div class="row">
											<?php 
											/*$viewData['list'] = $prodListComp;
											$this->load->view('user/product/single', $viewData);*/
											?>
										</div>
									</div> -->
								</div>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>