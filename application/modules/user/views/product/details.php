<section class="innerbanner">
	<div class="banner-cont">
		<h1 class="title">Product Details</h1>
		<div class="breadcramb">
			<ul>
				<li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
				<li><?php echo $prodDet[0]->amd_name;?></li>
			</ul>
		</div>
	</div>
</section>

<?php $this->load->view('cms/category');?>

<section class="inner-layout">
	<div class="container">		
		<div class="inner-content">
			<?php if($msg != ''){ echo $msg;}?>
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
								<div class="comment-item" id="commentList">
									<?php if(count($comments) > 0){
										foreach ($comments as $keyCom => $valueCom) {
									?>
									<figure>
										<span class="pic"><img src="<?php echo base_url('public/'.THEME.'/images/site-logo.png');?>" alt=""></span>
										<figcaption>
											<p><?php echo $valueCom->comments;?> </p>
											<h3><?php echo $valueCom->name;?></h3>
											<h4><?php echo getViewDate($valueCom->created_date);?></h4>
										</figcaption>
									</figure>
								<?php }} ?>
									
								</div>
							</div>
							<div class="comment-add-layout">
								<h4>Post a Comment</h4>
								<form>
									<div class="form-group">
										<textarea class="form-control" name="comments" id="comments" placeholder="Enter Your Comment"></textarea>
									</div>
									<div class="form-submit">
										<input type="button" id="postButton" value="Post"/>
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

									<!-- AddToAny BEGIN -->
									<span>
										<div class="a2a_kit a2a_kit_size_32 a2a_default_style">
											<a class="a2a_dd" href="https://www.addtoany.com/share"></a>
											<a class="a2a_button_facebook"></a>
											<a class="a2a_button_twitter"></a>
											<a class="a2a_button_google_plus"></a>	
										</div>
										<script async src="https://static.addtoany.com/menu/page.js"></script>
									</span>
<!-- AddToAny END -->

								</div>
								<div class="contact-info">
									<?php 
									$tel = ($prodDet[0]->am_user_type == 'admin')? SITEMOBILE :$prodDet[0]->mobile;
									$showTel = substr($tel,0,7).'XXX';
									?>
									<a class="cont-no sellersPhone" title="Click to view the Phone Number" href="#" data-phone="<?php echo $tel;?>">
									<?php echo $showTel;?></a>

									<a class="cont-mail modalLink" href="javascript:void(0);" data-url="<?php echo base_url('user/product/contactToSellerEmail/'.$prodDet[0]->am_id);?>"> &nbsp;
										<?php echo ($prodDet[0]->am_user_type == 'admin')? SUPPORTEMAIL :$prodDet[0]->email;?>
										</a>
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
        var csfrData = {};
        csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                         = '<?php echo $this->security->get_csrf_hash(); ?>';
        //alert('<?php echo $this->security->get_csrf_hash(); ?>');
        $.ajaxSetup({
          data: csfrData
        });

        $(".modalLink").click(function(){
            var url = $(this).attr("data-url");
            //alert(url);
            $('.myModalLg').modal('show');
            $('.myModalLg').find(".modal-content").load(url);
        });

		$("#postButton").on('click', function(){
			addComments();
		});
		$('#comments').keypress(function(e) {
		    var key = e.which;
		    if (key == 13) // the enter key code
		    {
		      $("#postButton").click();
		      return true;
		    }
		});
		$('.sellersPhone').click(function(){
            if($(this).attr('href') == '#') {
			  $(this).text($(this).attr('data-phone'));
			  $(this).attr('title', 'Click to Call');
			  $(this).attr('href', 'tel:' + $(this).attr('data-phone'));
			  return false;
            }
          });
	});

	function addComments(){
		var comments = 	$("#comments").val();
		var am_id = '<?php echo $am_id;?>';
		var url = '<?php echo base_url();?>user/comment/add';
		<?php if($this->session->userdata('user_id') <= 0){?>
			window.location.href = '<?php echo base_url('user/auth/login');?>';
		<?php }?>
		if(comments != ''){
			$.post( url, { comments : comments, am_id : am_id}, function(data) {
	            $('#commentList').append(data.html);
	        },'json');
		}
	}
	
</script>
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>

<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/prefixfree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/zoom-slideshow.js"></script>