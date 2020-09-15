<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">About Us</h1>
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
            <div class="row">
                <div class="col-md-12 wrap-details-blog">
                    <?php echo $content[0]->name_val;?>
                </div>
            </div>
            <div class="bottom-imglist">
                <div class="row">
                    <?php if(count($about_us_user) > 0){
                            foreach ($about_us_user as $key => $value) {
                                if($value->img == ''){
                                    $image = base_url('public/'.THEME.'/images/no-image.jpg');
                                }else{
                                    $image = base_url(UPLOAD_ABOUT_US_USER.'thumb/'.$value->img);
                                }
                                
                        ?>
                    <div class="col-6 col-md-3 col-lg-3 col-xl-3">
                        <img src="<?php echo $image;?>" alt="">
                        <h6><?php echo  $value->name;?></h6>
                    </div>
                    <?php } } ?>
                </div>
            </div>
        </div>
    </div>
</section>