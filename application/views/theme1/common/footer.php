

	<div class="outer-container clearfix">
		<div class="row">
			<div class="col-lg-3 col-md-12"><p>Copyright © <?php echo date("Y");?> ParrotDipankar</p></div>
			<div class="col-lg-6 col-md-12">
				<div class="botm-links">
					<ul>
						<li><a href="<?php echo base_url('cms/about_us');?>">About US</a></li>
						<li><a href="<?php echo base_url('cms/disclaimer');?>">Disclaimer</a></li>
						<li><a href="<?php echo base_url('cms/google_privacy_policy');?>">Google Adsense Privacy Policy</a></li>
						<li><a href="<?php echo base_url('cms/contact_us');?>">Contact Us</a></li>
					</ul>
				</div>
			</div>
			<div class="col-lg-3 col-md-16">
				<?php $this->load->view(THEME.'/common/socialLink');?>
			</div>
		</div>
	</div>
