<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Parrot Dipankar</title>
<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Damion" rel="stylesheet">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/linearicons.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/fullpage.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/owl.carousel.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/owl.theme.default.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/easy-responsive-tabs.css">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('public/'.THEME.'/');?>css/responsive-style.css" rel="stylesheet" type="text/css">

<!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
<!-- Custom JS for this template -->

</head>
	
<body>
<div class="topsearch">
	<button  class="scrchclsbtn clsbtn"><em></em><em></em></button>
	<div class="topsearchlayout">
		<form>
			<div class="search-group">
				<input type="text" value="" placeholder="Search">
				<input type="submit" value="Search">
			</div>
		</form>
	</div>
</div>
<div class="menusection">
	<button  class="menuclsbtn clsbtn"><em></em><em></em></button>
	<div class="menulayout">
		<nav>
			<ul>
				<li><a href="<?php echo base_url();?>">Home</a></li>
				<li><a href="<?php echo base_url('cms/about_us');?>">About US</a></li>
				<!-- <li><a href="javascript:void(0)">Our Services</a></li>
				<li><a href="javascript:void(0)">Our Projects</a></li>
				<li><a href="javascript:void(0)">Offres & Events</a></li> 
				<li><a href="javascript:void(0)">Our Location</a></li>
				<li><a href="javascript:void(0)">Blog</a></li> -->
				<li><a href="javascript:void(0)">Disclaimer</a></li>
				<li><a href="javascript:void(0)">Privacy Policy</a></li>
				<li><a href="<?php echo base_url('cms/contact_us');?>">Contact Us</a></li>
			</ul>
		</nav>
	</div>
</div>
<header id="header">
	<div class="outer-container">
		<div class="headerleft"><a href="javascript:void(0)" class="logo"><img src="<?php echo base_url('public/'.THEME.'/');?>images/site-logo.png" alt="ParrotDipankar"/></a></div>
		<div class="headerright">
			<div class="inline-elmnt">
				<button class="searchbtn">Search</button>
			</div>
			<div class="inline-elmnt">
				<a href="javascript:void(0)" class="locationbtn">Location</a>
			</div>
			<div class="inline-elmnt">
				<a href="javascript:void(0)" class="logbtn">Login</a>
			</div>
			<div class="inline-elmnt">
				<button class="navbtn"><em></em><em></em><em></em></button>
			</div>
		</div>
	</div>
</header>
<footer id="footer">
	<div class="outer-container clearfix">
		<div class="pull-left"><p>Copyright © 2018 ParrotDipankar</p></div>
		<div class="pull-right">
			<ul>
				<li class="fb"><a href="javascript:void(0)">Facebook</a></li>
				<li class="twt"><a href="javascript:void(0)">Twitter</a></li>
				<li class="inst"><a href="javascript:void(0)">Instagram</a></li>
				<li class="linkd"><a href="javascript:void(0)">Linkdin</a></li>
				<li class="utube"><a href="javascript:void(0)">YouTube</a></li>
			</ul>
		</div>
	</div>
</footer>

<div class="left-static">
	<h5><a href="javascript:void(0)">Publish Listing</a></h5>
</div>

<section id="fullpage">	
	<div class="section homeBanner" id="section0">	
		<!-- <div class="slide" id="slide1">
			<iframe width="1920" height="1080" src="https://www.youtube.com/embed/ICIKly4Mh4k?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
		</div> -->
		<div class="slide" id="slide2" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_01.jpg); background-repeat: no-repeat;">
			<div class="banner-container">
				<div class="bannertxt"><h2>Chossing The Right Bird</h2><h3>For You And Your Family</h3></div>
			</div>
		</div>
		<div class="slide" id="slide3" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_02.jpg); background-repeat: no-repeat;">
			<div class="banner-container">
				<div class="bannertxt"><h2>Chossing The Right Bird</h2><h3>For You And Your Family</h3></div>
			</div>
		</div>
		<div class="slide" id="slide4" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_03.jpg); background-repeat: no-repeat;">
			<div class="banner-container">
				<div class="bannertxt"><h2>Chossing The Right Bird</h2><h3>For You And Your Family</h3></div>
			</div>
		</div>
	</div>
	<!-- BANNER SECTION -->

	<div class="section homecategory" id="section1">
		<div class="container">
			<div class="content-sec clearfix">
				<h2 class="title text-center">Browse Categories</h2>
				<div class="category-circle carousel-4 owl-carousel owl-theme">
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
				    	<h3><a href="javascript:void(0)"><?php echo $value->acmd_name;?></a></h3>
				    </div>
				<?php } } ?>
				    <!-- <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/lovebird_01_img.jpg" alt="Lovebird">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Lovebird</a></h3>
				    </div>
				    <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/cockatiel_01_img.jpg" alt="Cockatiel">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Cockatiel</a></h3>
				    </div>
				    <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/ringneck_01_img.jpg" alt="Ring neck and RFaw">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Ring neck and Raw</a></h3>
				    </div>
				    <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/buddies_01_img.jpg" alt="Buddies">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Buddies</a></h3>
				    </div>
				    <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/lovebird_01_img.jpg" alt="Lovebird">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Lovebird</a></h3>
				    </div>
				    <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/cockatiel_01_img.jpg" alt="Cockatiel">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Cockatiel</a></h3>
				    </div>
				    <div class="item">
				    	<figure>
				    		<div class="circle-layout">
					    		<img src="<?php echo base_url('public/'.THEME.'/');?>images/ringneck_01_img.jpg" alt="Ring neck and RFaw">
					    		<figcaption>
					    			<button><i class="lnr lnr-plus-circle"></i></button>
					    		</figcaption>
				    		</div>					    		
				    	</figure>
				    	<h3><a href="javascript:void(0)">Ring neck and Raw</a></h3>
				    </div> -->
				</div>
			</div>
		</div>
	</div>
	<!-- CATEGORY SECTION -->

	<div class="section homediscover" id="section2">		
		<div class="container">
			<div class="content-sec">
				<h2 class="title text-center">Discover new products</h2>
				<h5 class="subtitle text-center">Browse our classifieds and find best deal for you - buy, sell or exchange items</h5>
				<div class="search-layout">
					<div class="search-group">
						<input type="text" name="" placeholder="What are you looking for?">
						<input type="submit" name="" name="" value="Search">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- WAHT WE DO SECTION -->

	<div class="section homepdlist" id="section3">
		<div class="container">
			<div class="content-sec">

				<div id="horizontalTab" class="homepdlist-tab">
					<ul class="resp-tabs-list">
						<li>Premium listings</li>
						<li>Latest listings</li>
					</ul>
					<div class="resp-tabs-container">
						<div>
							<div class="homelist-box carousel-4 owl-carousel owl-theme">
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_01.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_02.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_03.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_04.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_01.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_02.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_03.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							    <div class="item">
							    	<figure>
							    		<div class="box-layout">
								    		<span class="pdimg"><img src="<?php echo base_url('public/'.THEME.'/');?>images/list-img_04.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
								    		<figcaption>
								    			<h3><a href="javascript:void(0)">Red eye male</a></h3>
								    			<h5>RS 300.00</h5>
								    		</figcaption>
							    		</div>					    		
							    	</figure>
							    </div>
							</div>
						</div>
						<div>
						
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- HOMELIST SECTION -->
	

	<div class="section homeGetTouch" id="section7">
		<div class="gridwrap">
			<div class="two-grid secleft">
				<div class="img-grid clearfix">
					<ul>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_01.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_02.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_03.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_04.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_05.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_06.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_07.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_08.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_09.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_10.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_11.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_12.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_13.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_14.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_15.jpg" alt=""/></li>
						<li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_16.jpg" alt=""/></li>
					</ul>
				</div>
			</div>
			<div class="two-grid secright">
				<div class="content-sec">
					<div class="box-wrap">
						<div class="botm-logo"><a href="javascript:void(0)"><img src="<?php echo base_url('public/'.THEME.'/');?>images/site-white-logo.png" alt="Logo"/></a></div>
						<div class="botm-links">
							<ul>
								<li><a href="javascript:void(0)">Disclaimer</a></li>
								<li><a href="javascript:void(0)">Privacy Policy</a></li>
								<li><a href="javascript:void(0)">Contact</a></li>
							</ul>
						</div>
						<div class="botm-social">
							<ul>
								<li class="fb"><a href="javascript:void(0)">Facebook</a></li>
								<li class="twt"><a href="javascript:void(0)">Twitter</a></li>
								<li class="inst"><a href="javascript:void(0)">Instagram</a></li>
								<li class="linkd"><a href="javascript:void(0)">Linkdin</a></li>
								<li class="utube"><a href="javascript:void(0)">YouTube</a></li>
							</ul>
						</div>					
						<div class="botm-copyright">
							<h4>Copyright © 2018 ParrotDipankar</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- GET IN TOUCH SECTION -->
</section>


<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/fullpage.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/owl.carousel.min.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/easy-responsive-tabs.js"></script>

<script type="text/javascript">
	function initialization() {
	    var myFullpage = new fullpage('#fullpage', {
	    	verticalCentered: false,
			navigation: true,
	        navigationPosition: 'right',
	        slidesNavigation:true,
	        controlArrows: false,
	        responsiveWidth: 1025,
	        css3:false,
	        afterResponsive: function (isResponsive) {
	    	}

		});
	}
	//fullPage.js initialization
	initialization();

</script>


<script type="text/javascript">
    jQuery(document).ready(function ($) { 

    	$('.carousel-4').owlCarousel({
		    loop:true,
		    margin:30,
		    responsiveClass:true,
		    dots: false,
		    responsive:{
		        0:{
		            items:1,
		            nav:true
		        },
		        600:{
		            items:3,
		            nav:false
		        },
		        1000:{
		            items:4,
		            nav:true,
		            loop:false
		        }
		    }
		})

    	$('#horizontalTab').easyResponsiveTabs({
			type: 'default', //Types: default, vertical, accordion           
			width: 'auto', //auto or any width like 600px
			fit: true,   // 100% fit in a container
			closed: 'accordion', // Start closed if in accordion view
			activate: function(event) { // Callback function if tab is switched
				var $tab = $(this);
				var $info = $('#tabInfo');
				var $name = $('span', $info);
				$name.text($tab.text());
				$info.show();
			}		
		});
    	
		$('.searchbtn').click(function() {
		    $('.topsearch').addClass('scrchboxopen')
		    $(this).hide();
		});
		$('.scrchclsbtn').click(function() {
		    $('.topsearch').removeClass('scrchboxopen');
		    $('.searchbtn').show();
		});
		$('.navbtn').click(function() {
		    $('.menusection').addClass('menuopen')
		    $(this).hide();
		});
		$('.menuclsbtn').click(function() {
		    $('.menusection').removeClass('menuopen');
		    $('.navbtn').show();
		});

		function i() {
        windowHeight = $(window).innerHeight(), $(".botmlayoutsec").css("min-height", windowHeight)
	    }
	    i(), $(window).resize(function() {
	        i()
	    })

        
    });
</script>
</body>
</html>