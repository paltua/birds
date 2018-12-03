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
        <div class="container">
            <div class="content-sec clearfix">
                <h2 class="title text-center">Choosing The Right Bird</h2>
                <h3 class="sub-title text-center">For You And Your Family</h3>

                <!-- <h4 class="text-center">Browse Categories <span>(Discover new products)</span></h4> -->
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
            <div class="content-sec">
                <div class="row">
                    <div class="col-md-12">              
                        <div id="horizontalTab" class="homepdlist-tab">
                            <div class="common wr-fuol">
                            <ul class="resp-tabs-list pull-left">
                                <li>Pet's Listings</li>
                                <!-- <li>Foods & Accessories Listings</li> -->
                                <li>Dipankar's Choice</li>
                            </ul>
                            
                            </div>
                            <div class="resp-tabs-container ">
                                <div class="momar">
                                    <div class="homelist-box carousel-2 owl-carousel owl-theme">
                                        
                                        <?php if(count($latestProduct) > 0):
                                            foreach ($latestProduct as $key => $value) :
                                                $imagePath = base_url('public/'.THEME.'/images/no-image.jpg');
                                                if($value->ami_path != ''){
                                                    $imagePath = base_url(UPLOAD_PROD_PATH.'thumb/'.$value->ami_path);
                                                }
                                        ?>
                                        <div class="item">
                                            <figure>
                                                <div class="box-layout img-icons">
                                                    <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="fa fa-link"></i></a><a href="<?php echo $imagePath;?>" data-fancybox="gallery" class="detailsbtn fa-img-link"><i class="fa fa-image"></i></a></span>
                                                    <figcaption>
                                                        <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>">
                                                        <?php if(strlen($value->amd_name) <= 13){
                                                                echo $value->amd_name;
                                                            }else{
                                                                echo substr($value->amd_name,0,11).'..';
                                                            };?></a></h3>
                                                        <h5>RS <?php echo $value->amd_price;?></h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div>
                                    <?php endforeach; endif;  ?>
                                    </div>
                                    <a href="<?php echo base_url('user/product/search/0/pet');?>" class="btn btn-danger pull-right deskview for-top-but">View All</a>

                                    <a href="<?php echo base_url('user/product/search/0/pet');?>" class="btn btn-danger pull-right mobview mar-bot">View All</a>
                                    
                                </div>
                                
                                
                                <div class="">
                                    <div class="homelist-box carousel-2 owl-carousel owl-theme">
                                        
                                        <?php if(count($dipChoices) > 0):
                                            foreach ($dipChoices as $key => $value) :
                                                $imagePath = base_url('public/'.THEME.'/images/no-image.jpg');
                                                if($value->ami_path != ''){
                                                    $imagePath = base_url(UPLOAD_PROD_PATH.'thumb/'.$value->ami_path);
                                                }
                                        ?>
                                        <div class="item">
                                            <figure>
                                                <div class="box-layout img-icons">
                                                    <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies">
                                                        <a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="fa fa-link"></i></a><a href="<?php echo $imagePath;?>" data-fancybox="gallery" class="detailsbtn fa-img-link"><i class="fa fa-image"></i></a>
                                                    </span>
                                                    <figcaption>
                                                        <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>">
                                                        
                                                        <?php if(strlen($value->amd_name) <= 13){
                                                            echo $value->amd_name;
                                                        }else{
                                                            echo substr($value->amd_name,0,11).'..';
                                                        };?>
                                                        </a></h3>
                                                        <h5>RS <?php echo $value->amd_price;?></h5>
                                                    </figcaption>
                                                </div>                              
                                            </figure>
                                        </div>
                                    <?php endforeach; endif;  ?>
                                    </div>

                                    <a href="<?php echo base_url('user/product/search/0/dip');?>" class="btn btn-danger pull-right deskview for-top-but">View All</a>

                                    <a href="<?php echo base_url('user/product/search/0/dip');?>" class="btn btn-danger pull-right mobview">View All</a>
                                    <!-- <a href="<?php echo base_url('user/product/search/');?>" class="btn btn-danger">View All</a> -->
                                </div>
                            </div>
                        </div>
                        
                    </div>     
                    
                    

<div class="col-md-12 food-acc-sec">              
                        <div id="horizontalTab" class="homepdlist-tab">
                        <div class="common wr-fuol">
                            <h4 class="fd-title pull-left">Foods & Accessories Listings</h4>
                            <a href="<?php echo base_url('user/product/search/0/food');?>" class="btn btn-danger pull-right deskview">View All</a>
                                                    </div>
                            <div class="resp-tabs-container">
                               
                                <div> 
                                    <div class="homelist-box carousel-2 owl-carousel owl-theme">
                                        
                                        <?php if(count($premiumProduct) > 0):
                                                foreach ($premiumProduct as $key => $value) :
                                                    $imagePath = base_url('public/'.THEME.'/images/no-image.jpg');
                                                    if($value->ami_path != ''){
                                                        $imagePath = base_url(UPLOAD_PROD_PATH.'thumb/'.$value->ami_path);
                                                    }
                                            ?>
                                            <div class="item">
                                                <figure>
                                                    <div class="box-layout img-icons">
                                                        <span class="pdimg"><img src="<?php echo $imagePath;?>" alt="Buddies"><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>" class="detailsbtn"><i class="fa fa-link"></i></a><a href="<?php echo $imagePath;?>" data-fancybox="gallery" class="detailsbtn fa-img-link"><i class="fa fa-image"></i></a></span>
                                                        <figcaption>
                                                            <h3><a href="<?php echo base_url('user/product/details/'.$value->am_id);?>">
                                                            
                                                            <?php if(strlen($value->amd_name) <= 13){
                                                                echo $value->amd_name;
                                                            }else{
                                                                echo substr($value->amd_name,0,11).'..';
                                                            };?>
                                                            </a></h3>
                                                            <h5>RS <?php echo $value->amd_price;?></h5>
                                                        </figcaption>
                                                    </div>                              
                                                </figure>
                                            </div>
                                        <?php endforeach; endif;  ?>
                                    </div>
                                    <a href="<?php echo base_url('user/product/search/0/food');?>" class="btn btn-danger pull-right mobview">View All</a>
                                    
                                </div>
                                
                               
                            </div>
                        </div>
                        
                    </div>





                </div>
            </div>
        </div>      
    </div>
    

    <div class="section homeGetTouch overflow-sec" id="section2">
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
                    <div class="video-sec">
                        <div class="video-box">
                            <iframe width="100%" height="200" src="<?php echo $set['you_tube_link'];?>?rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=0&amp;disablekb=0" frameborder="0" allow="encrypted-media" allowfullscreen ></iframe>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="botm-social">
                                    <ul>
                                        <?php $this->load->view(THEME.'/common/socialLink');?>
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
    <!-- GET IN TOUCH SECTION -->
</section>








