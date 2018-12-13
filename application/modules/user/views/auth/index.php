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


<section class="inner-layout">
  <div class="container">   
    <div class="inner-content">
      <div class="row">
        <?php echo $loginHtml;?>
        <?php echo $registrationHtml;?>   
        <?php //$this->load->view('user/auth/error');?>     
      </div>  
    </div>
  </div>
</section>
