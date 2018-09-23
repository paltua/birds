<!DOCTYPE html>
<html>
<head>
<?php if($head != ''): echo $head; endif;?>
</head>
	
<body>
<section class="inner-page-wrap">
	<?php if($search != ''): echo $search; endif;?>
	<div class="menusection">
	<?php if($menu != ''): echo $menu; endif;?>
	</div>
	<header id="header">
		<?php if($header != ''): echo $header; endif;?>
	</header>


	<?php if($content != ''): echo $content; endif;?>

	<footer id="footer">
		<?php if($footer != ''): echo $footer; endif;?>
	</footer>
</section>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/owl.carousel.min.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/easy-responsive-tabs.js"></script>
<script src="<?php echo base_url('public/'.THEME.'/');?>js/custom.js"></script>
</body>
</html>