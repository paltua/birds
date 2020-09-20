<?php if ( count( $list ) > 0 ) {
    // print_r( $list );
    // die;
    foreach ( $list as $key => $value ) {
        ?>
<div class='col-md-12 col-sm-6 col-xs-12 blog-s-new'>
    <div class='pd-item-box'>
        <figure>
            <span class='img-box'>
                <span class='img-inner'>
                    <?php
        $imagePath = base_url( 'public/'.THEME.'/images/cockatiel_01_img.jpg' );
        if ( $value->image_path != '' ) {
            $imagePath = base_url( UPLOAD_BLOG_PATH.'thumb/'.$value->image_path );
        }
        ?>
                    <a href="<?php echo base_url('cms/blog/details/'.$value->title_url);?>">
                        <img src="<?php echo $imagePath;?>" />
                    </a>
                </span>
            </span>
            <figcaption>
                <div class='content-item item-left'>

                    <h3>
                        <a href="<?php echo base_url('cms/blog/details/'.$value->title_url);?>"><?php echo $value->title;
        ?> </a>
                    </h3>
                    <span><?php echo date_format( date_create( $value->created_date ),  'l, jS F, Y' );
        ?></span>
                    <p><?php //echo $value->amd_short_desc;
        if ( strlen( $value->short_desc ) >= 340 ) {
            echo substr( $value->short_desc, 0, 337 ).'...';
        } else {
            echo $value->short_desc;
        }
        ?></p>

                </div>
            </figcaption>
        </figure>
    </div>
</div>
<?php }
    }
    ?>