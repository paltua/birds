<section class="innerbanner">
	<div class="banner-cont">
		<h1 class="title">Product List</h1>
		<div class="breadcramb">
			<ul>
				<li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
				<li>Product Details</li>
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
			<div class="product-details-layout">
				<div class="row">
					<div class="col-lg-8 col-md-8 col-sm-12">
						<div id="view">
						  <img src="<?php echo base_url(UPLOAD_PROD_PATH.$prodImg[0]->ami_path);?>" alt="" />
						</div>
						<div id="thumbs">
							<div id="nav-left-thumbs"><i class="lnr lnr-chevron-left"></i></div>
							<div id="pics-thumbs">
								<?php if(count($prodImg)> 0){
									foreach ($prodImg as $key => $value) {
										$imagePath = base_url('public/'.THEME.'/images/sectionbanner01_02.jpg');
										if($value->ami_path != ''){
											$imagePath = base_url(UPLOAD_PROD_PATH.$value->ami_path);
										}
								?>
								<img src="<?php echo $imagePath;?>" alt="" />
							<?php }} ?>
								
							</div>
							<div id="nav-right-thumbs"><i class="lnr lnr-chevron-right"></i></div>
						</div>
						<div class="details-pd">
							<div class="details-item">
								<h2><?php echo $prodDet[0]->amd_name;?></h2>
								<ul>
									<li><label>Category</label><span><?php echo $prodDet[0]->acmd_name;?></span></li>
									<li><label>Price</label><span>Rs. <?php echo $prodDet[0]->amd_price;?></span></li>
									<li><label>Listing ID</label><span>#<?php echo $prodDet[0]->am_code;?></span></li>
									<li><label>Viewed</label><span><?php echo $prodDet[0]->am_viewed_count;?>+</span></li>
									<li><label>Publish date</label><span><?php echo getViewDate($prodDet[0]->am_created_date);?></span></li>
								</ul>
							</div>
							<div class="details-item">
								<h2>Description</h2>
								<p><?php echo $prodDet[0]->amd_short_desc;?></p>
							</div>
						</div>
						<div class="comments-sec">
							<div class="comment-layout">
								<h2>Comments</h2>
								<div class="comment-item">
									<?php if(count($comments) > 0){
										foreach ($comments as $keyCom => $valueCom) {
									?>
									<figure>
										<span class="pic"><img src="<?php echo base_url('public/'.THEME.'/images/ft-img-gallery_04.jpg');?>" alt=""></span>
										<figcaption>
											<p><?php echo $valueCom->comments;?> </p>
											<h3><?php echo $valueCom->name;?></h3>
											<h4><?php echo getViewDate($valueCom->created_date);?></h4>
										</figcaption>
									</figure>
								<?php }} ?>
									
									<!-- <figure>
										<span class="pic"><img src="<?php echo base_url('public/'.THEME.'/images/ft-img-gallery_04.jpg');?>" alt=""></span>
										<figcaption>
											<p>AJAX-ZOOM is a jQuery responsive image zoom & pan software with 360° degree ... 360° product view, mouseover zoom extension and other gallery extensions. ... For embedding the player multiple times between a text and without hover effect.</p>
											<h3>John Jeo</h3>
											<h4>20 August 2018</h4>
										</figcaption>
									</figure> -->
								</div>
							</div>
							<div class="comment-add-layout">
								<h4>Post a Comment</h4>
								<form>
									<div class="form-group">
										<textarea class="form-control" placeholder="Enter Your Comment"></textarea>
									</div>
									<div class="form-submit">
										<input type="submit" id="postButton" value="Post"/>
									</div>
								</form>
							</div>
						</div>		
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12">
						<aside>
							<div class="seller-info-layout">
								<h3>Seller's Info</h3>
								<div class="seller-info-item">
									<h4><?php echo ($prodDet[0]->am_user_type == 'admin')?'Company':$prodDet[0]->user_name;?></h4>
									<?php if($prodDet[0]->am_user_type != 'admin'){?>
									<h6>Private Person</h6>
									<h5>Registered on <?php echo date("F j, Y", strtotime($prodDet[0]->am_created_date));?></h5>
									<h3>Dashboard</h3>
									<?php }?>
									
									<span title="Send this listing to your friend"><a href="javascript:void(0)"><i class="lnr lnr-users"></i></a></span>
									<span title="Report item"><i class="lnr lnr-flag"></i>
										<ul>
											<li><a href="javascript:void(0)">Spam</a></li>
											<li><a href="javascript:void(0)">Misclassified</a></li>											
											<li><a href="javascript:void(0)">Duplicated</a></li>
											<li><a href="javascript:void(0)">Expired</a></li>
											<li><a href="javascript:void(0)">Offensive</a></li>
										</ul>
									</span>
								</div>
								<div class="contact-info">
									<a class="cont-no" href="javascript:void(0)">033 2345 2635</a>
									<a class="cont-mail" href="javascript:void(0)">Contact Seller</a>
								</div>
							</div>
						</aside>							
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	$(document).ready(function(){
		$("#postButton").on('click', function(){

		});
	});
	
</script>
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>

<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/prefixfree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/zoom-slideshow.js"></script>