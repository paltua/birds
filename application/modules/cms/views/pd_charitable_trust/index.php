<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">PD Charitable Trust</h1>
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

                </div>
            </div>
        </div>
    </div>
</section>