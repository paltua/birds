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
						<div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
							<div class="aside-layout">
								<div class="aside-item">
									<div id="verticalTab">
										<ul class="resp-tabs-list list-left-sud">
											<?php foreach ($selectedCatDet as $key => $value) {
												?>
										<li><?php echo $value->lang_name;?></li>
									<?php }} ?>
										</ul>
										<div class="resp-tabs-container right-details-sud">
											<?php if(count($selectedCatDet) > 0){
												foreach ($selectedCatDet as $key => $value) {
												?>
											<div>
												<h3><?php echo $value->acmd_name;?></h3>
												<p><?php echo $value->acmd_short_desc;?></p>
												<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec luctus felis id dolor dignissim vel vulputate eros feugiat. Mauris accumsan aliquam ultrices. Vivamus sit amet pulvinar mi. Nam at placerat urna. Sed rutrum, ante eget<br><br> fermentum sodales, est eros condimentum velit, nec consectetur lorem augue ac sapien. Morbi et arcu sit amet lacus ornare malesuada. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec blandit sem purus. Pellentesque quis magna odio, non mattis mi. In et dui mauris, sit amet ullamcorper nisl.<br><br>Duis a orci nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi feugiat ultrices elementum. Nullam nisi elit, semper nec eleifend et, auctor aliquet risus. Curabitur placerat lacus et orci blandit ac lacinia sem dignissim. Nam nec odio elit. Pellentesque dapibus commodo leo quis feugiat. In hac habitasse platea dictumst. Integer id tortor sit amet purus viverra aliquam nec ac elit. Fusce facilisis urna sed ligula pellentesque molestie. Duis ac risus elit. Proin ut felis diam. Ut felis diam, convallis sit amet hendrerit id, euismod id mi. <br><br>Nullam nisl purus, semper et tristique a, ullamcorper vitae metus. 
Sed non vulputate nisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In hac habitasse platea dictumst. Nullam neque erat, tempor eget dictum sit amet, laoreet vitae leo. Sed eu pretium purus. Pellentesque vestibulum arcu eu lacus aliquam sollicitudin vel ac metus. Maecenas pellentesque, nibh eget eleifend pharetra, mauris nisl ornare orci, sit amet suscipit mauris ante id nulla.  </p>

<div class="w-100 wrap-blog-slider product-wrap-sud">
    <h4>See other Products</h4>
    <div class="w-100 blog-slider-owl-sud">
    <div class="owl-carousel owl-theme owl-slider-blog-sud">
        <div class="item">
        <a class="w-100 blog-inner-wap-slider" href="#">
           <div class="w-100 img-blog-sli"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg" alt="Image"></div>
           <div class="w-100 content-blog-sli">
            <h5>Title of the blog</h5>
           </div>
        </a>
        </div>
        <div class="item">
        <a class="w-100 blog-inner-wap-slider" href="#">
           <div class="w-100 img-blog-sli"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg" alt="Image"></div>
           <div class="w-100 content-blog-sli">
            <h5>Title of the blog</h5>
           </div>
        </a>
        </div>
        <div class="item">
        <a class="w-100 blog-inner-wap-slider" href="#">
           <div class="w-100 img-blog-sli"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg" alt="Image"></div>
           <div class="w-100 content-blog-sli">
            <h5>Title of the blog</h5>
           </div>
        </a>
        </div>
        <div class="item">
        <a class="w-100 blog-inner-wap-slider" href="#">
           <div class="w-100 img-blog-sli"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg" alt="Image"></div>
           <div class="w-100 content-blog-sli">
            <h5>Title of the blog</h5>
           </div>
        </a>
        </div>
        <div class="item">
        <a class="w-100 blog-inner-wap-slider" href="#">
           <div class="w-100 img-blog-sli"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg" alt="Image"></div>
           <div class="w-100 content-blog-sli">
            <h5>Title of the blog</h5>
           </div>
        </a>
        </div>
    </div>
    </div>
    </div>
											</div>
											
											<?php } ?>						
										</div>
									</div>
								</div>
								<div class="aside-item"></div>
							</div>
						</div>
					<?php } ?>	
					
				</div>
			</div>
		</div>
	</div>
</section>
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>