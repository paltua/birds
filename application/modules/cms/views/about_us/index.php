<section class="innerbanner">
  <div class="banner-cont">
    <h1 class="title">About Us</h1>
    <div class="breadcramb">
      <ul>
        <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
        <li>About Us</li>
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
<!--       <h3>www.parrotdipankar.com is only about the EXOTIC BIRDS and other pets species which are LEGAL IN INDIA, AS PER INDIAN WIELD LIFE ACT.</h3> -->
      <p>There are very few people who can continue with their childhood passion or dream!  My affection with this wonderful creation of god has started long back when I was in the age of 10.</p>
      <p>I have devoted time, send money to continue it. Sometime I had to sacrifice new shirt in the festive season to buy new cage for my love.</p>
      <p>My journey of farming was started with local prison and Budgies, as and when I could manage some fund from parent or from CCTV business, I have added different more spices in my farm. Once I brought 2 African Grey Parrot. Those two lovely couple found my farm very homely and gave us 25 chicks. It made me crazier for birds. I started collecting more birds like Sunconure , Severe Macaw etc. I started selling those chicks, and earned more money. I used that money to increase my collection and enhance the farm capacity and atmosphere. This way my farm is very very colorful now.</p>
      <p>With the support from my spouse, parents, younger brother, other family member and friends, now I am able to live with my passion.</p>
      <!-- <p>All the information on this website is published in good faith and for general information purpose only. www.parrotdipankar.com does not make any warranties about the completeness, reliability and accuracy of this information. Any action you take upon the information you find on this website (www.parrotdipankar.com), is strictly at your own risk. www.parrotdipankar.com will not be liable for any losses and/or damages in connection with the use of our website.I take no responsibility for, and will not be liable for, the web site being or the link provided in the contain temporarily unavailable due to technical issues beyond my control.</p> -->
    </div>
  </div>
</section>
