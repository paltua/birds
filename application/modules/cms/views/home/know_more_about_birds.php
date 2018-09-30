<section class="innerbanner">
  <div class="banner-cont">
    <h1 class="title">Know More About Birds</h1>
    <div class="breadcramb">
      <ul>
        <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
        <li>Know More About Birds</li>
      </ul>
    </div>
  </div>
</section>

<?php $this->load->view('cms/category');?>

<section class="inner-layout">
    <div class="container">   
        <div class="inner-content">
            <div class="row">
                <div class="col-md-12">
                	<div id="verticalTab">
			            <ul class="resp-tabs-list">
			                <li>English</li>
			                <li>Bengali</li>
			                <li>Hindi</li>
			            </ul>
			            <div class="resp-tabs-container">
			                <div>
			                    <p><?php echo $set['about_bird_en'];?></p>
			                </div>
			                <div>
			                    <p><?php echo $set['about_bird_ben'];?></p>
			                </div>
			                <div>
			                    <p><?php echo $set['about_bird_hi'];?></p>
			                </div>
			            </div>
			        </div>
            	</div>
            </div>
        </div>
    </div>
</section>