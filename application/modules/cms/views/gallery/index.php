<link rel="stylesheet" href="<?php echo base_url('public/'.THEME.'/');?>css/jquery.fancybox.css"/>
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
      <div class="section homeGetTouch" id="section2">
        <div class="gridwrap">
            <div class="two-grid secleft">
                <div class="img-grid clearfix">
                <?php if(count($gallery) > 0){?>
                  <ul>
                      <?php foreach ($gallery as $key => $value) {?>
                        <li><a href="<?php echo base_url('uploads/gallery/'.$value->g_path);?>" data-fancybox="gallery"><img src="<?php echo base_url('uploads/gallery/thumb/'.$value->g_path);?>" alt=""/></a></li>
                      <?php }?>
                  </ul>
                  <?php } ?>
                </div>
            </div>  
        </div>
      </div>
    </div>
  </div>
  </section>  
  <script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery.fancybox.js"></script>
  <script type="text/javascript">
    $('[data-fancybox="gallery"]').fancybox({
      buttons : [
        'share',
        'fullScreen',
        'close'
      ],
      thumbs : {
        autoStart : true
      }
    });

</script>

