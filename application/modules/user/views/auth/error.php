<div class="col-md-6 register-block">
	<h2 class="title">Register <span>an account for free</span></h2>
	<form class="block" action="" method="post">
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="text-danger">Due to technical glitches, registration can’t be done now. Sorry for the inconvenience. Please try after some time.</div>
	</form>
</div>