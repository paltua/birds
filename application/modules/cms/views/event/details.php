<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Events Details</h1>
        <div class="breadcramb">
            <ul>
                <li><a href="<?php echo base_url(); ?>"><i class="lnr lnr-home"></i></a></li>
                <li>Home</li>
            </ul>
        </div>
    </div>
</section>

<section class="inner-layout d-block mt-4 mb-4">
    <div class="container">
        <div class="row">
            <div class="w-100 float-left ev-list-wrap">

                <div class="col-12 col-md-12 col-lg-9 col-xl-9 float-left event-left p-0">

                    <div class="w-100 float-left ev-details">
                        <div class="w-100 float-left wrap-inner-event enent-details-wrap et-det-slider">
                            <div class="owl-carousel owl-theme owl-slider-single">
                                <?php foreach ($images as $key => $value) { ?>
                                <div class="w-100 float-left img-ev item">
                                    <img src="<?php echo base_url(UPLOAD_EVENT_PATH . $value->ei_image_name); ?>"
                                        alt="<?php echo $details[0]->event_title; ?>">
                                </div>
                                <?php } ?>
                            </div>

                            <div class="ev-content w-100 float-left">
                                <h3><?php echo $details[0]->event_title; ?></h3>
                                <span class="w-100 float-left">
                                    <img class="d-inline-block"
                                        src="<?php echo base_url('public/' . THEME . '/images/calendar-date.png'); ?>">
                                    <?php echo date('jS F Y', strtotime($details[0]->event_start_date_time)); ?>
                                    -
                                    <?php echo date('jS F Y', strtotime($details[0]->event_end_date_time)); ?></span>
                                <span class="w-100 float-left">
                                    <img class="d-inline-block"
                                        src="<?php echo base_url('public/' . THEME . '/images/location.png'); ?>">
                                    <?php echo $details[0]->location; ?></span>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->event_short_desc; ?>
                                </div>
                                <h4>Objective</h4>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->event_objectives; ?>
                                </div>
                                <h4>About</h4>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->event_about; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lists-ev-details w-100 float-left">
                        <h2>Programmes</h2>
                        <div class="w-100 blog-slider-owl-sud">
                            <div class="owl-carousel owl-theme owl-slider-event-details-sud">
                                <?php if (count($program) > 0) { ?>
                                <?php foreach ($program as $key => $value) { ?>
                                <div class="item">
                                    <div class="w-100 float-left box-event-lists">
                                        <div class="w-100 float-left wrap-inner-event">
                                            <a href="<?php echo base_url($module . '/programme/details/' . $value->pro_title_url); ?>"
                                                class="w-100 float-left">
                                                <div class="w-100 float-left img-ev">
                                                    <img src="<?php echo base_url(UPLOAD_PROG_PATH . 'thumb/' . $value->prog_img_name); ?>"
                                                        alt="">
                                                </div>
                                                <div class="ev-content w-100 float-left">
                                                    <h3><?php echo $value->program_title; ?></h3>
                                                    <!-- <span class="w-100 float-left">25th June 2020</span> -->
                                                    <p>
                                                        <?php
                                                                if (strlen($value->program_short_desc) >= 25) {
                                                                    echo substr($value->program_short_desc, 0, 24) . '...';
                                                                } else {
                                                                    echo $value->program_short_desc;
                                                                }
                                                                ?></p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-12 col-lg-3 col-xl-3 float-left event-right">
                    <?php if (count($upcoming) > 0) { ?>
                    <div class="w-100 float-left recent-events">
                        <h4>Upcoming Events</h4>
                        <div class="w-100 float-left event-seps">
                            <?php foreach ($upcoming as $key => $value) { ?>
                            <a href="<?php echo base_url($module . '/' . $controller . '/details/' . $value->event_title_url); ?>"
                                class="w-100 float-left"><?php echo $value->event_title; ?> <i
                                    class="fas fa-angle-right"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (count($completed) > 0) { ?>
                    <div class="w-100 float-left recent-events">
                        <h4>Completed Events</h4>
                        <div class="w-100 float-left event-seps">
                            <?php foreach ($completed as $key => $value) { ?>
                            <a href="<?php echo base_url($module . '/' . $controller . '/details/' . $value->event_title_url); ?>"
                                class="w-100 float-left"><?php echo $value->event_title; ?><i
                                    class="fas fa-angle-right"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>

                </div>

            </div>


        </div>
    </div>
</section>