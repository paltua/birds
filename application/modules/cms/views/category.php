<section class='inner-top-cat'>
    <div class='container'>
        <div class='category-circle carousel-7 owl-carousel owl-theme'>
            <?php if ( count( $category ) > 0 ) {
    foreach ( $category as $key => $value ) {
        ?>
            <?php if ( $this->uri->segment( 4 ) == $value->acm_id ) {
            ?>
            <script>
            var startPositionOwlCarSeven = '<?php echo $key+1;?>';
            </script>
            <?php }
            ?>
            <div class='item <?php if ( $this->uri->segment( 4 ) == $value->acm_id ) { ?> active <?php }?>'>
                <figure>
                    <div class='circle-layout'>
                        <?php
            $imagePath = base_url( 'public/'.THEME.'/images/no-image.jpg' );
            if ( $value->image_name != '' ) {
                $imagePath = base_url( UPLOAD_CAT_PATH.$value->image_name );
            }
            ?>
                        <img src="<?php echo $imagePath;?>" alt="<?php echo $value->acmd_name;?>">
                        <figcaption>
                            <!-- <button><i class = 'lnr lnr-plus-circle'></i></button> -->
                            <a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>" class='button'><i
                                    class='lnr lnr-plus-circle'></i></a>
                        </figcaption>
                    </div>
                </figure>
                <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;
            ?></a>
                </h3>
            </div>
            <?php }
        }
        ?>
        </div>
    </div>
</section>