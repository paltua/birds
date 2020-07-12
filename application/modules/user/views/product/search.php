<link rel="stylesheet" href="<?php echo base_url();?>public/admin/vendor/chosen/chosen.min.css">
<script src="<?php echo base_url();?>public/admin/vendor/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });
});
</script>

<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Category</h1>
        <div class="breadcramb">
            <ul>
                <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
                <li>Home</li>
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
                                        </div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if(count($blogs) > 0){?>
                    <div class="w-100 wrap-blog-slider product-wrap-sud">
                        <h4>Related Blogs</h4>
                        <div class="w-100 blog-slider-owl-sud">
                            <div class="owl-carousel owl-theme owl-slider-blog-sud">
                                <?php foreach ($blogs as $key => $value) {?>
                                <div class="item">
                                    <a class="w-100 blog-inner-wap-slider"
                                        href="<?php echo base_url('cms/blog/details/'.$value->title_url);?>">
                                        <div class="w-100 img-blog-sli"><img
                                                src="<?php echo $value->image_path != '' ?base_url(UPLOAD_BLOG_PATH.$value->image_path):base_url('public/theme1/images/sectionbanner01_01.jpg');?>"
                                                alt="<?php echo $value->title;?>"></div>
                                        <div class="w-100 content-blog-sli">
                                            <h5><?php echo $value->title;?></h5>
                                        </div>
                                    </a>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/asRange.css" type="text/css">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>