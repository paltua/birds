<?php $this->load->view('theme1/common/disc');
?>
<button class='navbtn'><em></em><em></em><em></em></button>
<div class='outer-container'>
    <div class='headerleft'>
        <a href='<?php echo base_url(); ?>' class='logo'>
            <img src="<?php echo base_url('public/' . THEME . '/'); ?>images/site-logo.png" alt='ParrotDipankar' />
        </a>
    </div>
    <div class='headerright'>
        <div class='inline-elmnt'>
            <nav>
                <ul>
                    <li><a href='<?php echo base_url(); ?>'>Home</a></li>
                    <li><a href="<?php echo base_url('cms/about-us'); ?>">About US</a></li>
                    <li><a href="<?php echo base_url('cms/blog'); ?>">Blog</a></li>
                    <li><a href="<?php echo base_url('cms/event'); ?>">Event</a></li>
                    <li><a href="<?php echo base_url('cms/contact-us'); ?>">Contact Us</a></li>
                </ul>
            </nav>
        </div>
        <div class='inline-elmnt'>
            <a href="<?php echo base_url('cms/pd-charitable-trust'); ?>" class='btn pub-list pubnew'>Charity</a>
        </div>
        <?php if ($this->session->userdata('user_id') <= 0) {
        ?>
        <div class='inline-elmnt login'>
            <a href="<?php echo base_url('user/auth/login'); ?>" class='logbtn after-log nolog'>Login</a>
        </div>
        <?php }
        ?>
        <?php if ($this->session->userdata('user_id') > 0) {
        ?>
        <div class='inline-elmnt logout'>
            <a href='javascript:void(0)' class='logbtn after-log'></a>
            <ul class='user-drop'>
                <li class='user-details'><img
                        src="<?php echo base_url('public/' . THEME . '/'); ?>images/usernav.png"><?php echo $this->session->userdata('name');
                                                                                                                        ?>
                </li>
                <!-- <li><a href = ''>My Listing</a></li> -->
                <?php // echo base_url( 'user/animal/listing' );
                    ?>
                <li><a href="<?php echo base_url('user/profile/details'); ?>">My Profile</a></li>
                <li><a href="<?php echo base_url('user/auth/logout'); ?>">Log Out</a></li>
            </ul>
        </div>
        <?php }
        ?>
    </div>
</div>