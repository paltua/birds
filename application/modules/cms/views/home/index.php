<script type="text/javascript">
    $(document).raedy(function(){
        
    });

    function submitForm(){
        var form = $('#searchFormId');
        var cat_id = $("#searchCatId").val();
        if(cat_id != '' && cat_id > 0){
            cat_id = '/'+cat_id;
        }
        form.attr('action',form.attr('action')+cat_id).trigger('submit');
        return false;
    }
    
</script>

<section id="fullpage"> 
    <div class="section homeBanner" id="section0">

    <div class="w-100 slider-top-sud">
        <div class="container">
            <div class="w-100 slider-top-inner">
                 <div class="owl-carousel owl-theme owl-slider-top-sud">
                    <div class="item"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_03.jpg" alt="">
                <div class="col-12 col-md-12 col-lg-5 col-xl-5 wrap-text-slider-sud">
                    <h3>Title will come here</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec luctus felis id dolor dignissim vel vulputate eros feugiat. Mauris accumsan aliquam ultrices.</p>
                    <a href="#">View Details</a>
                </div>
                </div>
                     <div class="item"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_03.jpg" alt="">
                    <div class="col-12 col-md-12 col-lg-5 col-xl-5 wrap-text-slider-sud">
                    <h3>Title will come here</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec luctus felis id dolor dignissim vel vulputate eros feugiat. Mauris accumsan aliquam ultrices.</p>
                    <a href="#">View Details</a>
                </div></div>
                      <div class="item"><img src="http://localhost/birds/public/theme1/images/sectionbanner01_03.jpg" alt="">
                    <div class="col-12 col-md-12 col-lg-5 col-xl-5 wrap-text-slider-sud">
                    <h3>Title will come here</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec luctus felis id dolor dignissim vel vulputate eros feugiat. Mauris accumsan aliquam ultrices.</p>
                    <a href="#">View Details</a>
                </div></div>
                 </div>
            </div>
        </div>
    </div>
        
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_01.jpg); background-repeat: no-repeat;"></div>
                <div class="carousel-item" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_02.jpg); background-repeat: no-repeat;"></div>
                <div class="carousel-item" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_03.jpg); background-repeat: no-repeat;"></div>
            </div>
        </div>
    </div>
    <!-- BANNER SECTION -->

    <div class="section homepdlist overflow-sec" id="section1">
        <div class="container">
            <div class="col-sm-12 col-md-12 col-lg-7 float-left">
            <div class="content-sec clearfix sud-text">
                <h2 class="title text-center mt-4">Know more about Birds</h2>
                <!-- <h3 class="sub-title text-center">For You And Your Family</h3> -->

                <!-- <h4 class="text-center">Browse Categories <span>(Discover new products)</span></h4> -->
                <!-- <h6 class="text-center">Browse our classifieds and find best deal for you - buy, sell or exchange items</h5> -->
                <div class="search-layout">
                    <form method="post" action="<?php echo base_url('user/product/search');?>" id="searchFormId">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="country_id">
                    <input type="hidden" name="state_id">
                    <input type="hidden" name="city_id">
                    <input type="hidden" name="price">
                    <div class="search-group">
                        <input type="text" name="keyWord" placeholder="What are you looking for?">
                        <div class="dropdown show">                       
                          <select name="cat_id" id="searchCatId" id="searchCatId">
                            <option value="">All categories</option>
                            <?php if(count($category) > 0){
                                foreach ($category as $key => $value) {
                            ?>
                            <option value="<?php echo $value->acm_id;?>"><?php echo $value->acmd_name;?></option>
                            <?php } } ?>
                            
                          </select>
                        </div>
                        <input type="submit" onclick="return submitForm();" name="" id="searchButtunId" value="Search">
                    </div>
                </form>
                </div>

                <div class="category-circle carousel-5 owl-carousel owl-theme mt-5 owl-new-sud">
                    <!-- <div class="item">
                        <figure>
                            <div class="circle-layout">
                                <img src="images/buddies_01_img.jpg" alt="Buddies">
                                <figcaption>
                                    <a href="javascript:void(0)" class="button"><i class="lnr lnr-plus-circle"></i></a>
                                </figcaption>
                            </div>                              
                        </figure>
                        <h3><a href="javascript:void(0)">Buddies</a></h3>
                    </div> -->
                    <?php if(count($category) > 0){
                        foreach ($category as $key => $value) {
                    ?>
                    <div class="item">
                        <figure>
                            <div class="circle-layout">
                                <?php 
                                $imagePath = base_url('public/'.THEME.'/images/no-image.jpg');
                                if($value->image_name != ''){
                                    $imagePath = base_url(UPLOAD_CAT_PATH.$value->image_name);
                                }?>
                                <img src="<?php echo $imagePath;?>" alt="<?php echo $value->acmd_name;?>">
                                <figcaption>
                                    <a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>" class="button"><i class="lnr lnr-plus-circle"></i></a>
                                </figcaption>
                            </div>                              
                        </figure>
                        <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;?></a></h3>
                    </div>
                <?php } } ?>
                </div>
            </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-5 float-left">
            <div class="video-sec sud-video-sec">
                        <div class="video-box">
                            <iframe width="100%" height="325px" src="<?php echo $set['you_tube_link'];?>?rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=0&amp;disablekb=0" frameborder="0" allow="encrypted-media" allowfullscreen ></iframe>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="botm-social">
                                    <ul>
                                        <?php $this->load->view(THEME.'/common/socialLink');?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 pl-0 data-subs-sud">
                                <!-- <a href="javascript:void(0)" class="btn subscribe">Subscribe to my channels</a> -->
                                <script src="https://apis.google.com/js/platform.js"></script>
                                <div class="g-ytsubscribe" data-channelid="UCMP0o_U6wmJ2Zd60N2hWpXQ" data-layout="default" data-theme="dark" data-count="default"></div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>     
    </div>
    

    <!-- <div class="section homeGetTouch overflow-sec" id="section2">
        <div class="gridwrap gal-full">
        <div class="common wr-gal two-grid secleft full-wr">
        <div class="img-grid clearfix">
                    <?php if(count($gallery) > 0){?>
                    <ul>
                        <?php foreach ($gallery as $key => $value) {?>
                        <li><a href="<?php echo base_url('uploads/gallery/'.$value->g_path);?>" data-fancybox="gallery"><img src="<?php echo base_url('uploads/gallery/thumb/'.$value->g_path);?>" alt=""/></a></li>
                        <?php }?>
                    </ul>
                    <div class="cmmon wrapbutton">
                     <a href="<?php echo base_url('cms/gallery');?>" class="btn pub-list-btn pull-right">View All</a>
                     </div>
                    <?php } ?>
                    
                </div>
        </div>
            <div class="two-grid secleft sstop">
                
                <div class="botm-carsl bst-ch">
                            <h3 class="title">Best Choices</h3>
                            <div class="box-wrap">
                            <?php if(count($bestCat) > 0){
                                foreach ($bestCat as $key => $value) {
                            ?>    
                                <div class="category-circle">
                                    <div class="circle-item">
                                        <figure>
                                            <div class="circle-layout">
                                                <?php 
                                                $imagePath = base_url('public/'.THEME.'/images/no-image.jpg');
                                                if($value->image_name != ''){
                                                    $imagePath = base_url(UPLOAD_CAT_PATH.$value->image_name);
                                                }?>
                                                <img src="<?php echo $imagePath;?>" alt="<?php echo $value->acmd_name;?>">
                                                <figcaption>
                                                    <a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>" class="button"><i class="lnr lnr-plus-circle"></i></a>
                                                </figcaption>
                                            </div>                              
                                        </figure>
                                        <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;?></a></h3>
                                    </div>
                                </div>
                            <?php } } ?>    
                            </div>
                        </div>
                    
            </div>
            <div class="two-grid secright sstop" >
                <div class="content-sec">
                    

                    
                </div>
            </div>
        </div>
    </div> -->
    <!-- GET IN TOUCH SECTION -->
</section>








