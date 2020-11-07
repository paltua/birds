<div class="col-12 col-md-6 col-lg-4 col-xl-4 float-left box-event-lists">
    <div class="w-100 float-left wrap-inner-event">
        <a href="<?php echo base_url($module . '/' . $controller . '/details/' . $row->event_title_url); ?>"
            class="w-100 float-left">
            <div class="w-100 float-left img-ev">
                <?php
                $imagePath = base_url('public/' . THEME . '/images/cockatiel_01_img.jpg');
                if ($row->image_path != '') {
                    $imagePath = base_url(UPLOAD_EVENT_PATH . 'thumb/' . $row->image_path);
                } ?>
                <img src="<?php echo $imagePath ?>" alt="<?php echo $row->event_title; ?>">
            </div>
            <div class="ev-content w-100 float-left">
                <h3>
                    <?php
                    if (strlen($row->event_title) >= 25) {
                        echo substr($row->event_title, 0, 24) . '...';
                    } else {
                        echo $row->event_title;
                    }
                    ?>
                </h3>
                <span class="w-100 float-left"><?php echo date('jS F Y', strtotime($row->event_start_date_time)); ?> -
                    <?php echo date('jS F Y', strtotime($row->event_end_date_time)); ?></span>
                <p>
                    <?php
                    if (strlen($row->event_short_desc) >= 60) {
                        echo substr($row->event_short_desc, 0, 59) . '...';
                    } else {
                        echo $row->event_short_desc;
                    }
                    ?>
                </p>
            </div>
        </a>
    </div>
</div>