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
<section class="inner-top-cat">
  <div class="container">
    <div class="category-circle carousel-7 owl-carousel owl-theme">
      <?php if(count($category) > 0){
            foreach ($category as $key => $value) {
          ?>
        <div class="item">
          <figure>
            <div class="circle-layout">
              <?php 
              $imagePath = base_url('public/'.THEME.'/images/buddies_01_img.jpg');
              if($value->image_name != ''){
                $imagePath = base_url(UPLOAD_CAT_PATH.$value->image_name);
              }?>
              <img src="<?php echo $imagePath;?>" alt="<?php echo $value->acmd_name;?>">
              <figcaption>
                <button><i class="lnr lnr-plus-circle"></i></button>
              </figcaption>
            </div>                  
          </figure>
          <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;?></a></h3>
        </div>
        <?php } } ?>
    </div>
  </div>
</section>
<section class="inner-layout">
	<div class="container">		
		<div class="inner-content">
			<div class="product-listing-layout">
				<div class="row">
					<div class="col-lg-4 col-md-12 col-sm-12">
						<div class="aside-layout">
							<div class="aside-item">
								<div id="verticalTab">
									<ul class="resp-tabs-list">
										<?php if(count($selectedCatDet) > 0){
											foreach ($selectedCatDet as $key => $value) {
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
										<?php }} ?>						
									</div>
								</div>
							</div>
							<div class="aside-item"></div>
						</div>
					</div>
					<div class="col-lg-8 col-md-12 col-sm-12 cont-part">
						<div class="row">
							<div class="col-md-12">
								<h3>Search</h3>
								<div class="pd-search-filter-layout">									
									<form class="row" method="post">
										<div class="col-md-3">
											<div class="form-group">
												<label>Keyword</label>
												<input type="text" placeholder="I'm Looking For">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Region</label>
												<select>
													<option>Select a Region</option>
													<option>Azad Cashmir</option>
													<option>Balochistan</option>
													<option>Islamabad</option>
													<option>Punjab</option>
												</select>
											</div>
										</div>
										<!-- <div class="col-md-3">
											<div class="form-group">
												<label>City</label>
												<select>
													<option>Select City</option>
													<option>Kolkata</option>
													<option>Delhi</option>
													<option>Bombay</option>
													<option>Punjab</option>
												</select>
											</div>
										</div> -->
										<div class="col-md-6">
											<div class="form-group">
												<label>Price</label>
												<div class="example">
										          <form method="post">
										            <input class="range-example-input-2" type="text" min="<?php echo $minMaxPrice[0]->min_price;?>" max="<?php echo $minMaxPrice[0]->max_price;?>" value="" name="points" step="10" />
										          </form>
										        </div>
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
											<li>Personal</li>
											<li>Company</li>
										</ul>
									</div>
								</div>
								<div class="resp-tabs-container">
									<div>
										<div class="row">
											<?php if(count($prodList) > 0){
												foreach ($prodList as $key => $value) {
											?>
											<div class="col-md-12 col-sm-6 col-xs-12">
												<div class="pd-item-box">
													<figure>
														<span class="img-box">
															<span class="img-inner">
																<?php 
													              $imagePath = base_url('public/'.THEME.'/images/cockatiel_01_img.jpg');
													              if($value->ami_path != ''){
													                $imagePath = base_url(UPLOAD_PROD_PATH.$value->ami_path);
													              }?>
																<img src="<?php echo $imagePath;?>"/>
															</span>
														</span>
														<figcaption>
															<div class="content-item item-left">
																<div>
																	<h3><?php echo $value->amd_name;?></h3>
																	<h4><?php echo $value->amd_short_desc;?></h4>
																	<h5><span class="location">coming soon</span>
																		<span class="publshby"><?php echo ($value->am_user_type == 'admin')?'Admin': $value->name;?>Published by Ali </span></h5>
																</div>
															</div>
															<div class="content-item item-right">
																<div>
																	<h2>Rs. <?php echo $value->amd_price;?></h2>
																	<a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="btn btn-danger">View</a>
																	<h6>Budgies</h6>
																	<span class="publsh-date">published <span>yesterday</span></span>
																	<span class="viewed">viewed <span><?php echo $value->am_viewed_count;?>+</span></span>
																</div>
															</div>
														</figcaption>
													</figure>	
												</div>
											</div>
										<?php }} ?>
										</div>
									</div>
									
									<div>
										Personal comming soon
									</div>

									<div>
										Company comming soon
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>