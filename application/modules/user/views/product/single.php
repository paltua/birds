<?php if(count($list) > 0){
		foreach ($list as $key => $value) {
	?>
	<div class="col-md-12 col-sm-6 col-xs-12">
		<div class="pd-item-box">
			<figure>
				<span class="img-box">
					<span class="img-inner">
						<?php 
			              $imagePath = base_url('public/'.THEME.'/images/cockatiel_01_img.jpg');
			              if($value->ami_path != ''){
			                $imagePath = base_url(UPLOAD_PROD_PATH.'thumb/'.$value->ami_path);
			              }?>
			              <a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" >
						<img src="<?php echo $imagePath;?>"/>
					</a>
					</span>
				</span>
				<figcaption>
					<div class="content-item item-left">
						<div>
							<h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>"><?php echo $value->amd_name;?></a></h3>
							<h4><?php echo $value->amd_short_desc;?></h4>
							<h5><span class="location"><?php echo showLocation($value->country_name, $value->state_name, $value->city_name);?></span>
								<span class="publshby">Published by <b><?php echo ($value->am_user_type == 'admin')?'Company': $value->user_name;?></b></span></h5>
						</div>
					</div>
					<div class="content-item item-right">
						<div>
							<h2>Rs. <?php echo $value->amd_price;?></h2>
							<a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="btn btn-danger">View</a>
							<h6><?php echo $value->acmd_name;?></h6>
							<span class="publsh-date">published 
								<span><?php echo getViewDate($value->am_created_date);?></span>
							</span>
							<span class="viewed">viewed 
								<span><?php echo $value->am_viewed_count;?>+</span>
							</span>
						</div>
					</div>
				</figcaption>
			</figure>	
		</div>
	</div>
	<?php }} ?>