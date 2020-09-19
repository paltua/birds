<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Blog</h1>
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
        <div class="inner-content">
            <div class="product-listing-layout">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 ">
                        <div class="product-item">
                            <div id="horizontalTab" class="pd-list-tab">
                                <div class="resp-tabs-container">
                                    <div>
                                        <div class="row" id="productListDivId">
                                            <?php 
											$viewData['list'] = [];
											$this->load->view('cms/blog/list_row', $viewData);
											?>
                                        </div>
                                        <div class="buttonLoadMoreClass">
                                            <?php if($prodListCount > $limit['perPage']){?>
                                            <a href="javascript:void(0);" id="loadMoreId"
                                                class="btn btn-primary float-right">Load More</a>
                                            <?php } ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>