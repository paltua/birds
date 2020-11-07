<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Programmes Details</h1>
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
                                <?php foreach ($details as $key => $value) { ?>
                                <div class="w-100 float-left img-ev item">
                                    <img src="<?php echo base_url(UPLOAD_PROG_PATH . $value->prog_img_name); ?>"
                                        alt="<?php echo $details[0]->program_title; ?>">
                                </div>
                                <?php } ?>
                            </div>

                            <div class="ev-content w-100 float-left">
                                <h3><?php echo $details[0]->program_title; ?></h3>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->program_short_desc; ?>
                                </div>
                                <h4>Objective</h4>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->program_objectives; ?>
                                </div>
                                <h4>About</h4>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->program_about; ?>
                                </div>
                                <h4>Description</h4>
                                <div class="w-100 wrap-details-blog">
                                    <?php echo  $details[0]->program_desc; ?>
                                </div>
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
                            <a href="<?php echo base_url($module . '/event/details/' . $value->event_title_url); ?>"
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
                            <a href="<?php echo base_url($module . '/event/details/' . $value->event_title_url); ?>"
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