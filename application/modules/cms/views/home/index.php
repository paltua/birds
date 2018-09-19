<script type="text/javascript">
    $(document).raedy(function(){
        
    });

    function submitForm(){
        var form = $('#searchFormId');
        var cat_id = $("#searchCatId").val();
        form.attr('action',form.attr('action')+'/'+cat_id).trigger('submit');
        return false;
    }
    
</script>

<section id="fullpage"> 
    <div class="section homeBanner" id="section0">
        <div class="container">
            <div class="content-sec clearfix">
                <h2 class="title text-center">Chossing The Right Bird</h2>
                <h3 class="sub-title text-center">For You And Your Family</h3>

                <h4 class="text-center">Browse Categories <span>(Discover new products)</span></h4>
                <h6 class="text-center">Browse our classifieds and find best deal for you - buy, sell or exchange items</h5>
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

                <div class="category-circle carousel-5 owl-carousel owl-theme">
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
                                $imagePath = base_url('public/'.THEME.'/images/buddies_01_img.jpg');
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
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_01.jpg); background-repeat: no-repeat;"></div>
                <div class="carousel-item" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_02.jpg); background-repeat: no-repeat;"></div>
                <div class="carousel-item" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_03.jpg); background-repeat: no-repeat;"></div>
            </div>
        </div>
    </div>
    <!-- BANNER SECTION -->

    <div class="section homepdlist" id="section1">
        <div class="container">
            <div class="content-sec">
                <div class="row">
                    <div class="col-md-6">              
                        <div id="horizontalTab" class="homepdlist-tab">
                            <ul class="resp-tabs-list">
                                <li>Premium listings</li>
                                <li>Latest listings</li>
                                <li>Dipankar Choice</li>
                            </ul>
                            <div class="resp-tabs-container">
                                <div>
                                    <div class="homelist-box carousel-2 owl-carousel owl-theme">
                                        <!-- <div class="item">
                                            <figure>
                                                <div class="box-layout">
                                                    <span class="pdimg"><img src="images/list-img_01.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                                    <figcaption>
                                                        <h3><a href="javascript:void(0)">Red eye male</a></h3>
                                                        <h5>RS 300.00</h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div> -->
                                        <?php if(count($premiumProduct) > 0):
                                                foreach ($premiumProduct as $key => $value) :
                                                    $imagePath = base_url('public/'.THEME.'/images/list-img_01.jpg');
                                                    if($value->ami_path != ''){
                                                        $imagePath = base_url('uploads/animal/'.$value->ami_path);
                                                    }
                                            ?>
                                            <div class="item">
                                                <figure>
                                                    <div class="box-layout">
                                                        <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                                        <figcaption>
                                                            <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>"><?php echo $value->amd_name;?></a></h3>
                                                            <h5>RS <?php echo $value->amd_price;?></h5>
                                                        </figcaption>
                                                    </div>                              
                                                </figure>
                                            </div>
                                        <?php endforeach; endif;  ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="homelist-box carousel-2 owl-carousel owl-theme">
                                        <!-- <div class="item">
                                            <figure>
                                                <div class="box-layout">
                                                    <span class="pdimg"><img src="images/list-img_01.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                                    <figcaption>
                                                        <h3><a href="javascript:void(0)">Red eye male</a></h3>
                                                        <h5>RS 300.00</h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div> -->
                                        <?php if(count($latestProduct) > 0):
                                            foreach ($latestProduct as $key => $value) :
                                                $imagePath = base_url('public/'.THEME.'/images/list-img_01.jpg');
                                                if($value->ami_path != ''){
                                                    $imagePath = base_url('uploads/animal/'.$value->ami_path);
                                                }
                                        ?>
                                        <div class="item">
                                            <figure>
                                                <div class="box-layout">
                                                    <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                                    <figcaption>
                                                        <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>"><?php echo $value->amd_name;?></a></h3>
                                                        <h5>RS <?php echo $value->amd_price;?></h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div>
                                    <?php endforeach; endif;  ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="homelist-box carousel-2 owl-carousel owl-theme">
                                        <!-- <div class="item">
                                            <figure>
                                                <div class="box-layout">
                                                    <span class="pdimg"><img src="images/list-img_01.jpg" alt="Buddies"><a href="javascript:void(0)" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                                    <figcaption>
                                                        <h3><a href="javascript:void(0)">Red eye male</a></h3>
                                                        <h5>RS 300.00</h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div> -->
                                        <?php if(count($dipChoices) > 0):
                                            foreach ($dipChoices as $key => $value) :
                                                $imagePath = base_url('public/'.THEME.'/images/list-img_01.jpg');
                                                if($value->ami_path != ''){
                                                    $imagePath = base_url('uploads/animal/'.$value->ami_path);
                                                }
                                        ?>
                                        <div class="item">
                                            <figure>
                                                <div class="box-layout">
                                                    <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                                    <figcaption>
                                                        <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>"><?php echo $value->amd_name;?></a></h3>
                                                        <h5>RS <?php echo $value->amd_price;?></h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div>
                                    <?php endforeach; endif;  ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="video-sec">
                            <div class="video-box">
                                <iframe width="100%" height="350" src="https://www.youtube.com/embed/ICIKly4Mh4k?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0" frameborder="0" allow="encrypted-media" allowfullscreen ></iframe>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="botm-social">
                                        <ul>
                                            <li class="fb"><a href="javascript:void(0)">Facebook</a></li>
                                            <li class="twt"><a href="javascript:void(0)">Twitter</a></li>
                                            <li class="inst"><a href="javascript:void(0)">Instagram</a></li>
                                            <li class="linkd"><a href="javascript:void(0)">Linkdin</a></li>
                                            <li class="utube"><a href="javascript:void(0)">YouTube</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <!-- <a href="javascript:void(0)" class="btn subscribe">Subscribe to my channels</a> -->
                                    <script src="https://apis.google.com/js/platform.js"></script>
                                    <div class="g-ytsubscribe" data-channelid="UCMP0o_U6wmJ2Zd60N2hWpXQ" data-layout="default" data-theme="dark" data-count="default"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>      
    </div>
    

    <div class="section homeGetTouch" id="section2">
        <div class="gridwrap">
            <div class="two-grid secleft">
                <div class="img-grid clearfix">
                    <ul>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_01.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_02.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_03.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_04.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_05.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_06.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_07.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_08.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_09.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_10.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_11.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_12.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_13.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_14.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_15.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_16.jpg" alt=""/></li>
                    </ul>
                </div>
            </div>
            <div class="two-grid secright">
                <div class="content-sec">
                    <h3>Best Choices</h3>
                    <div class="box-wrap">
                        <div class="category-circle carousel-3 owl-carousel owl-theme">
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
                            <?php if(count($bestCat) > 0){
                                foreach ($bestCat as $key => $value) {
                            ?>
                            <div class="item">
                                <figure>
                                    <div class="circle-layout">
                                        <?php 
                                        $imagePath = base_url('public/'.THEME.'/images/buddies_01_img.jpg');
                                        if($value->image_name != ''){
                                            $imagePath = base_url(UPLOAD_CAT_PATH.$value->image_name);
                                        }?>
                                        <img src="<?php echo $imagePath;?>" alt="<?php echo $value->acmd_name;?>">
                                        <figcaption>
                                            <a href="javascript:void(0)" class="button"><i class="lnr lnr-plus-circle"></i></a>
                                        </figcaption>
                                    </div>                              
                                </figure>
                                <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;?></a></h3>
                            </div>
                        <?php } } ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- GET IN TOUCH SECTION -->
</section>










<section id="fullpage"> 
    <div class="section homeBanner" id="section0">  
        <!-- <div class="slide" id="slide1">
            <iframe width="1920" height="1080" src="https://www.youtube.com/embed/ICIKly4Mh4k?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div> -->
        <div class="slide" id="slide2" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_01.jpg); background-repeat: no-repeat;">
            <div class="banner-container">
                <div class="bannertxt"><h2>Chossing The Right Bird</h2><h3>For You And Your Family</h3></div>
            </div>
        </div>
        <div class="slide" id="slide3" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_02.jpg); background-repeat: no-repeat;">
            <div class="banner-container">
                <div class="bannertxt"><h2>Chossing The Right Bird</h2><h3>For You And Your Family</h3></div>
            </div>
        </div>
        <div class="slide" id="slide4" style="background-image: url(<?php echo base_url('public/'.THEME.'/');?>images/sectionbanner01_03.jpg); background-repeat: no-repeat;">
            <div class="banner-container">
                <div class="bannertxt"><h2>Chossing The Right Bird</h2><h3>For You And Your Family</h3></div>
            </div>
        </div>
    </div>
    <!-- BANNER SECTION -->

     
    <div class="section homecategory" id="section1">
        <div class="container">
            <div class="content-sec clearfix">
                <h2 class="title text-center">Browse Categories</h2>
                <!-- <h3 class="title text-center">Discover new products</h3>-->
                <h5 class="subtitle text-center">Browse our classifieds and find best deal for you - buy, sell or exchange items</h5>  
                <form method="post" action="<?php echo base_url('user/product/search');?>">
                <div class="search-layout">
                    
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="country_id">
                        <input type="hidden" name="state_id">
                        <input type="hidden" name="city_id">
                        <input type="hidden" name="price">
                    <div class="search-group">
                        <input type="text" name="keyWord" placeholder="What are you looking for?">

                        <input type="submit" name="" value="Search">
                    </div>
                
                </div>
                </form>

                <div class="category-circle carousel-5 owl-carousel owl-theme">
                    <?php if(count($category) > 0){
                        foreach ($category as $key => $value) {
                    ?>
                    <div class="item">
                        <figure>
                            <div class="circle-layout">
                                <?php 
                                $imagePath = base_url('public/'.THEME.'/images/buddies_01_img.jpg');
                                if($value->image_name != ''){
                                    $imagePath = base_url(UPLOAD_CAT_PATH.$value->image_name);
                                }?>
                                <img src="<?php echo $imagePath;?>" alt="<?php echo $value->acmd_name;?>">
                                <figcaption>
                                    <button><i class="lnr lnr-plus-circle"></i></button>
                                </figcaption>
                            </div>                              
                        </figure>
                        <h3><a href="<?php echo base_url('user/product/search/'.$value->acm_id);?>"><?php echo $value->acmd_name;?></a></h3>
                    </div>
                <?php } } ?>
                </div>
            </div>
        </div>
    </div>


    <!-- CATEGORY SECTION -->

    
    <!-- WAHT WE DO SECTION -->

    <div class="section homepdlist" id="section3">
        <div class="container">
            <div class="content-sec">

                <div id="horizontalTab" class="homepdlist-tab">
                    <ul class="resp-tabs-list">
                        <li>Latest listings</li>
                        <li>Premium listings</li>
                    </ul>
                    <div class="resp-tabs-container">
                        <div>
                            <div class="homelist-box carousel-4 owl-carousel owl-theme">
                                <?php if(count($latestProduct) > 0):
                                    foreach ($latestProduct as $key => $value) :
                                        $imagePath = base_url('public/'.THEME.'/images/list-img_01.jpg');
                                        if($value->ami_path != ''){
                                            $imagePath = base_url('uploads/animal/'.$value->ami_path);
                                        }
                                ?>
                                <div class="item">
                                    <figure>
                                        <div class="box-layout">
                                            <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                            <figcaption>
                                                <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>"><?php echo $value->amd_name;?></a></h3>
                                                <h5>RS <?php echo $value->amd_price;?></h5>
                                            </figcaption>
                                        </div>                              
                                    </figure>
                                </div>
                            <?php endforeach; endif;  ?>
                                
                            </div>
                        </div>
                        <div>
                            <div class="homelist-box carousel-4 owl-carousel owl-theme">
                                <?php if(count($premiumProduct) > 0):
                                    foreach ($premiumProduct as $key => $value) :
                                        $imagePath = base_url('public/'.THEME.'/images/list-img_01.jpg');
                                        if($value->ami_path != ''){
                                            $imagePath = base_url('uploads/animal/'.$value->ami_path);
                                        }
                                ?>
                                <div class="item">
                                    <figure>
                                        <div class="box-layout">
                                            <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="lnr lnr-plus-circle"></i></a></span>
                                            <figcaption>
                                                <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>"><?php echo $value->amd_name;?></a></h3>
                                                <h5>RS <?php echo $value->amd_price;?></h5>
                                            </figcaption>
                                        </div>                              
                                    </figure>
                                </div>
                            <?php endforeach; endif;  ?>
                            </div>                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- HOMELIST SECTION -->
    

    <div class="section homeGetTouch" id="section7">
        <div class="gridwrap">
            <div class="two-grid secleft">
                <div class="img-grid clearfix">
                    <ul>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_01.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_02.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_03.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_04.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_05.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_06.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_11.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_08.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_09.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_10.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_11.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_12.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_13.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_14.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_15.jpg" alt=""/></li>
                        <li><img src="<?php echo base_url('public/'.THEME.'/');?>images/ft-img-gallery_16.jpg" alt=""/></li>
                    </ul>
                </div>
            </div>
            <div class="two-grid secright">
                <div class="content-sec">
                    <div class="box-wrap">
                        <div class="botm-logo"><a href="javascript:void(0)"><img src="<?php echo base_url('public/'.THEME.'/');?>images/site-white-logo.png" alt="Logo"/></a></div>
                        <div class="botm-links">
                            <ul>
                                <li><a href="<?php echo base_url('cms/disclaimer');?>">Disclaimer</a></li>
                                <li><a href="<?php echo base_url('cms/google_privacy_policy');?>">Privacy Policy</a></li>
                                <li><a href="<?php echo base_url('cms/contact_us');?>">Contact</a></li>
                            </ul>
                        </div>
                        <div class="botm-social">
                            <ul>
                                <li class="fb"><a href="javascript:void(0)">Facebook</a></li>
                                <li class="twt"><a href="javascript:void(0)">Twitter</a></li>
                                <li class="inst"><a href="javascript:void(0)">Instagram</a></li>
                                <li class="linkd"><a href="javascript:void(0)">Linkdin</a></li>
                                <li class="utube"><a href="javascript:void(0)">YouTube</a></li>
                            </ul>
                        </div>                  
                        <div class="botm-copyright">
                            <h4>Copyright © 2018 ParrotDipankar</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- GET IN TOUCH SECTION -->
</section>