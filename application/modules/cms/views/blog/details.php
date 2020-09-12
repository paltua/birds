<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Bolg</h1>
        <div class="breadcramb">
            <ul>
                <li><a href="<?php echo base_url();?>"><i class="lnr lnr-home"></i></a></li>
                <li>Home</li>
            </ul>
        </div>
    </div>
</section>



<section class="inner-layout">
    <div class="container">
        <?php if(count($images) > 0){?>
        <div class="w-100 wrap-blog-image">
            <img src="<?php echo base_url(UPLOAD_BLOG_PATH.$images[0]->image_path);?>"
                alt="<?php echo $images[0]->title;?>">
        </div>
        <?php } ?>

        <div class="w-100 wrap-details-blog">
            <h1><?php echo $details[0]->title;?></h1>
            <span><?php echo date(' jS F Y',strtotime($details[0]->created_date));?></span>
            <?php echo $details[0]->long_desc;?>
        </div>

        <?php if(count($list) > 0) {?>
        <div class="w-100 wrap-blog-slider">
            <h4>See other Blogs</h4>
            <div class="w-100 blog-slider-owl-sud">
                <div class="owl-carousel owl-theme owl-slider-blog-sud">
                    <?php foreach ($list as $key => $value) {?>
                    <div class="item">
                        <a class="w-100 blog-inner-wap-slider"
                            href="<?php echo base_url('cms/blog/details/'.$value->title_url);?>">
                            <div class="w-100 img-blog-sli">
                                <img src="<?php echo $value->image_path != '' ?base_url(UPLOAD_BLOG_PATH.$value->image_path):base_url('public/theme1/images/sectionbanner01_01.jpg');?>"
                                    alt="<?php echo $value->title;?>"></div>
                            <div class="w-100 content-blog-sli">
                                <h5><?php echo $value->title;?></h5>
                            </div>
                        </a>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
        <?php }?>

        <div class="w-100 wrap-comment-section">
            <div class="comments">
                <h2>Leave a comment below!</h2>
                <div class="comments-form">
                    <textarea name="comments" id="comments" placeholder="Enter Your Comment" required
                        class="w-100"></textarea>
                    <input id="postButton" type="button" value="Submit" class="btn btn-submit-blog">
                </div>

                <div class="comments-list" id="commentsListId">

                </div>
            </div>
        </div>

    </div>
</section>

<script type="text/javascript">
$(document).ready(function() {
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });
    generateCommentsList();
    $("#postButton").on('click', function() {
        addComments();
    });
    $('#comments').keypress(function(e) {
        var key = e.which;
        if (key == 13) // the enter key code
        {
            $("#postButton").click();
            return true;
        }
    });
    $('.sellersPhone').click(function() {
        if ($(this).attr('href') == '#') {
            $(this).text($(this).attr('data-phone'));
            $(this).attr('title', 'Click to Call');
            $(this).attr('href', 'tel:' + $(this).attr('data-phone'));
            return false;
        }
    });
});

function addComments() {
    var comments = $("#comments").val();
    var blog_revision_id = '<?php echo $details[0]->blog_revision_id;?>';
    var url = '<?php echo base_url("user/comment/blog_add"); ?>';
    var id = '<?php echo $this->session->userdata("user_id");?>';
    if (id <= 0) {
        var conStatus = confirm('Please login to give your valuable comments.');
        if (conStatus) {
            window.location.href = "<?php echo base_url('user/auth/login');?>";
        }
    } else {
        if (comments != '') {
            $.post(url, {
                comments: comments,
                blog_revision_id: blog_revision_id
            }, function(data) {
                if (data.html == 'success') {
                    $("#comments").val('');
                    generateCommentsList();
                }
            }, 'json');
        } else {
            alert('Please enter the text in the comments box.');
        }
    }
}

function generateCommentsList() {
    var blog_id = '<?php echo $details[0]->blog_id;?>';
    var url = '<?php echo base_url("cms/blog/getComments"); ?>';
    $.post(url, {
        blog_id: blog_id
    }, function(data) {
        if (data.status == 'success') {
            $('#commentsListId').html(data.html);
        }
    }, 'json');
}
</script>