<!DOCTYPE html>
<html lang="en">

<head>
    <?php if($head != ''): echo $head; endif;?>
    <?php if($google_add != ''): echo $google_add; endif;?>
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
    <div id="myModal" class="modal fade myModalLg" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('public/'.THEME.'/');?>js/owl.carousel.min.js"></script>
    <script src="<?php echo base_url('public/'.THEME.'/');?>js/easy-responsive-tabs.js"></script>
    <script src="<?php echo base_url('public/'.THEME.'/');?>js/custom.js"></script>
</body>

</html>