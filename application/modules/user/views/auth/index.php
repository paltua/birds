<section class="innerbanner">
  <div class="banner-cont">
    <h1 class="title">Login/Register</h1>
    <div class="breadcramb">
      <ul>
        <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
        <li>Login/Registration</li>
      </ul>
    </div>
  </div>
</section>

<section class="inner-top-cat">
  <div class="container">
    <div class="category-circle carousel-7 owl-carousel owl-theme">
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
          <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;?></a></h3>
        </div>
        <?php } } ?>
    </div>
  </div>
</section>
<section class="inner-layout">
  <div class="container">   
    <div class="inner-content">
      <div class="row">
        <?php echo $loginHtml;?>
        <?php echo $registrationHtml;?>        
      </div>  
    </div>
  </div>
</section>
