<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">PD Charitable Trust</h1>
        <div class="breadcramb">
            <ul>
                <li><a href="<?php echo base_url(); ?>"><i class="lnr lnr-home"></i></a></li>
                <li>Home</li>
            </ul>
        </div>
    </div>
</section>

<section class="inner-layout">
    <div class="container">
        <div class="owl-carousel owl-theme owl-slider-pd-ch mt-4">

            <?php if (count($mainEvents) > 0) { ?>
            <?php foreach ($mainEvents as $key => $value) { ?>

            <div class="item">
                <a href="#">
                    <div class="img-wrap"><img src="<?php echo base_url(UPLOAD_EVENT_PATH . $value->image_path); ?>">
                    </div>
                    <div class="col-12 wrap-text-slider-sud pd-charit-content">
                        <h3><?php echo $value->event_title; ?></h3>
                        <p><?php echo (strlen($value->event_short_desc) > 480) ? substr($value->event_short_desc, 0, 480) . '...' : $value->event_short_desc; ?>
                        </p>
                    </div>
                </a>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="item">
                <a href="#">
                    <div class="img-wrap"><img src="./../public/theme1/images/pic.jpg"></div>
                    <div class="col-12 wrap-text-slider-sud pd-charit-content">
                        <h3>Title will come here</h3>
                        <p>Duis a orci nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi feugiat
                            ultrices elementum. Nullam nisi elit, semper nec eleifend et, auctor aliquet risus.
                            Curabitur placerat lacus et orci blandit ac lacinia sem dignissim. Nam nec odio elit.
                        </p>
                    </div>
                </a>
            </div>
            <?php } ?>
        </div>


        <div class="w-100 pt-4">
            <div class="row">
                <div class="col-12 f-unit-birds">
                    <div class="w-100 float-left text-center">
                        <h2>Others Event</h2>
                    </div>
                    <div class="owl-carousel owl-theme owl-slider-pd-ch-list w-100 float-left mt-0">
                        <?php if (count($mainEvents) > 0) { ?>
                        <?php foreach ($mainEvents as $key => $value) { ?>
                        <div class="item">
                            <div class="inner-wrap-pd-block w-100 float-left">
                                <a href="<?php echo base_url('cms/event/details/' . $value->event_title_url); ?>">
                                    <div class="image-pd-block w-100 float-left"><img
                                            src="<?php echo base_url(UPLOAD_EVENT_PATH . 'thumb/' . $value->image_path); ?>">
                                    </div>
                                    <h4 class="col-12 float-left"><?php echo $value->event_title; ?></h4>
                                    <p class="col-12 float-left">
                                        <?php echo (strlen($value->event_short_desc) > 120) ? substr($value->event_short_desc, 0, 120) . '...' : $value->event_short_desc; ?>
                                    </p>
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <div class="item">
                            <div class="inner-wrap-pd-block w-100 float-left">
                                <a href="">
                                    <div class="image-pd-block w-100 float-left"><img
                                            src="./../public/theme1/images/pic.jpg"></div>
                                    <h4 class="col-12 float-left">Title will come here</h4>
                                    <p class="col-12 float-left">Duis a orci nisi. Lorem ipsum dolor sit amet,
                                        consectetur adipiscing elit. Morbi feugiat ultrices elementum.</p>
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>


                <!-- <div class="col-md-12 wrap-details-blog">
                    <?php echo $content[0]->name_val; ?>
                </div> -->
                <div class="col-md-12 wrap-details-blog">
                    <h2 class="text-uppercase">Charity </h2>
                    <?php echo $content[0]->name_val; ?>
                </div>
            </div>
        </div>
    </div>
</section>