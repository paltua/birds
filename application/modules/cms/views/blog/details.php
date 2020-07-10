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

        <div class="w-100 wrap-blog-slider">
            <h4>See other blogs</h4>
            <div class="w-100 blog-slider-owl-sud">
                <div class="owl-carousel owl-theme owl-slider-blog-sud">
                    <div class="item">
                        <a class="w-100 blog-inner-wap-slider" href="#">
                            <div class="w-100 img-blog-sli"><img
                                    src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg"
                                    alt="Image"></div>
                            <div class="w-100 content-blog-sli">
                                <h5>Title of the blog</h5>
                            </div>
                        </a>
                    </div>
                    <div class="item">
                        <a class="w-100 blog-inner-wap-slider" href="#">
                            <div class="w-100 img-blog-sli"><img
                                    src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg"
                                    alt="Image"></div>
                            <div class="w-100 content-blog-sli">
                                <h5>Title of the blog</h5>
                            </div>
                        </a>
                    </div>
                    <div class="item">
                        <a class="w-100 blog-inner-wap-slider" href="#">
                            <div class="w-100 img-blog-sli"><img
                                    src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg"
                                    alt="Image"></div>
                            <div class="w-100 content-blog-sli">
                                <h5>Title of the blog</h5>
                            </div>
                        </a>
                    </div>
                    <div class="item">
                        <a class="w-100 blog-inner-wap-slider" href="#">
                            <div class="w-100 img-blog-sli"><img
                                    src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg"
                                    alt="Image"></div>
                            <div class="w-100 content-blog-sli">
                                <h5>Title of the blog</h5>
                            </div>
                        </a>
                    </div>
                    <div class="item">
                        <a class="w-100 blog-inner-wap-slider" href="#">
                            <div class="w-100 img-blog-sli"><img
                                    src="http://localhost/birds/public/theme1/images/sectionbanner01_01.jpg"
                                    alt="Image"></div>
                            <div class="w-100 content-blog-sli">
                                <h5>Title of the blog</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 wrap-comment-section">
            <div class="comments">
                <h2>Leave a comment below!</h2>
                <div class="comments-form">
                    <textarea placeholder="Comment" required class="w-100"></textarea>
                    <input type="submit" value="Submit" class="btn btn-submit-blog">
                </div>

                <div class="comments-list">
                    <div class="comments comments-list-inner w-100">
                        <h4>R. Das</h4>
                        <p>Duis a orci nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi feugiat
                            ultrices elementum. Nullam nisi elit</p>
                        <p class="comment-time">14.01.2020</p>
                    </div>
                    <div class="comments comments-list-inner w-100">
                        <h4>R. Das</h4>
                        <p>Duis a orci nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi feugiat
                            ultrices elementum. Nullam nisi elit</p>
                        <p class="comment-time">14.01.2020</p>
                    </div>
                    <div class="comments comments-list-inner w-100">
                        <h4>R. Das</h4>
                        <p>Duis a orci nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi feugiat
                            ultrices elementum. Nullam nisi elit</p>
                        <p class="comment-time">14.01.2020</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>