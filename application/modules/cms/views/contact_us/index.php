<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Contact Us</h1>
        <div class="breadcramb">
            <ul>
                <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </div>
</section>

<?php $this->load->view('cms/category');?>

<section class="inner-layout">
    <div class="container">
        <div class="inner-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="bottom-map"><iframe
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3703.892490434317!2d88.227446!3d21.8230844!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a03ab0cb48f3bbb%3A0xbb4dccab9ea0ea52!2sukilerhat!5e0!3m2!1sen!2sin!4v1535229139068"
                            width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe></div>
                    <div class="contact-info">
                        <h4>Contact Info</h4>
                        <p>Official Time : 11 AM to 9 PM</p>
                        <!-- <p>Mobile : + 91-<?php echo SITEMOBILE;?></p> -->
                        <p>Mobile : +91 9907104115 </p>
                        <p>Email : parrotdipankarstore@gmail.com</p>
                        <!-- parrotdipankar@gmail.com -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="addrs-block">
                        <h4>OFFICE ADDRESS</h4>

                        <h5>PARROT DIPANKAR</h5>
                        <p>School Road, Ukiler Hat, South 24 Paraganas</p>
                        <p>West Bengal, India - 743347</p>
                    </div>
                    <div class="contact-form-block">
                        <h6>If you have any problem or need some additional info about our site, do not hesitate to
                            contact us. We will reply soon.</h6>
                        <?php if($msg != ''): echo $msg; endif;?>
                        <form class="block" action="" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="row">
                                <div class="col-md-6 multi-horizontal" data-for="name">
                                    <div class="form-group">
                                        <label class="form-control-label ">Name</label>
                                        <input class="form-control input" name="contact_us[name]" data-form-field="Name"
                                            placeholder="Your Name" required="" id="name-form4-4v" type="text">
                                    </div>
                                </div>
                                <div class="col-md-6 multi-horizontal" data-for="phone">
                                    <div class="form-group">
                                        <label class="form-control-label ">Phone No</label>
                                        <input class="form-control input" name="contact_us[mobile]"
                                            data-form-field="Phone" placeholder="Phone" required="" id="phone-form4-4v"
                                            type="number">
                                    </div>
                                </div>
                                <div class="col-md-12" data-for="email">
                                    <div class="form-group">
                                        <label class="form-control-label ">Email</label>
                                        <input class="form-control input" name="contact_us[email]"
                                            data-form-field="Email" placeholder="Email" required="" id="email-form4-4v"
                                            type="email">
                                    </div>
                                </div>
                                <div class="col-md-12" data-for="message">
                                    <div class="form-group">
                                        <label class="form-control-label ">Message</label>
                                        <textarea class="form-control input" name="contact_us[desccription]" rows="3"
                                            required="" data-form-field="Message" placeholder="Message"
                                            style="resize:none" id="message-form4-4v"></textarea>
                                    </div>
                                </div>
                                <div class="input-group-btn col-md-12">
                                    <button href="" type="submit" class="btn btn-primary btn-form display-4">SEND
                                        MESSAGE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>