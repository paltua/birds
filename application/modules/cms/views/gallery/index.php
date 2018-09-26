<section class="innerbanner">
  <div class="banner-cont">
    <h1 class="title">Gallery</h1>
    <div class="breadcramb">
      <ul>
        <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
        <li>Gallery</li>
      </ul>
    </div>
  </div>
</section>

<?php $this->load->view('cms/category');?>

<section class="inner-layout">
  <div class="container">   
    <div class="inner-content">
      <div class="gridwrap">
        <div class="two-grid secleft">
            <div class="img-grid clearfix">
                <?php if(count($gallery) > 0){?>
                <ul>
                    <?php foreach ($gallery as $key => $value) {?>
                    <li><img src="<?php echo base_url('uploads/gallery/thumb/'.$value->g_path);?>" alt=""/></li>
                    <?php }?>
                </ul>
                <?php } ?>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>