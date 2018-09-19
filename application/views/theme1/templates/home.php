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
<link href="<?php echo base_url('public/'.THEME.'/');?>css/full-slider.css" rel="stylesheet" type="text/css">
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
<header id="header">
<?php  $this->load->view(THEME.'/common/header');?>
</header>

<?php  $this->load->view(THEME.'/common/publish');?>


<?php if($content != ''): echo $content; endif;?>

<footer id="footer">
	<?php  $this->load->view(THEME.'/common/footer');?>
</footer>

<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/fullpage.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/owl.carousel.min.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/easy-responsive-tabs.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/custom.js"></script>

<script type="text/javascript">
function initialization() {
    var myFullpage = new fullpage('#fullpage', {
    	verticalCentered: false,
		navigation: true,
        navigationPosition: 'right',
        //slidesNavigation:true,
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

</body>
</html>