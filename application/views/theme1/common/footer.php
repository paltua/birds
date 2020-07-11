<div class="outer-container clearfix">
    <div class="row">
        <div class="col-lg-3 col-md-12">
            <p>Copyright ©<?php echo date("Y");?> ParrotDipankar</p>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="botm-links">
                <ul>
                    <li><a href="<?php echo base_url('cms/disclaimer');?>">Disclaimer</a></li>
                    <li><a href="<?php echo base_url('cms/google-privacy-policy');?>">Google Adsense Privacy Policy</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-3 col-md-16">
            <ul class="ft-social">
                <?php $this->load->view(THEME.'/common/socialLink');?>
            </ul>
        </div>
    </div>
</div>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
(adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-3375071677065247",
    enable_page_level_ads: true
});
</script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
    integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<script src="<?php echo base_url('public/'.THEME.'/');?>js/jquery-asRange.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/prefixfree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/'.THEME.'/');?>js/zoom-slideshow.js"></script>

<script>
$(".toggle-password").click(function() {

    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});
</script>