<script type="text/javascript">
$(document).ready(function() {
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });
    $("#loadMoreId").click(function() {
        var formData = $("#searchForm").serialize();
        var url = "<?php echo base_url('cms/blog/getAjaxData');?>";
        $.post(url, formData, function(result) {
            console.log(result);
            $("#productListDivId").append(result.html);
            $("#startPage").val(result.startPage);
            if (result.loaderStatus == 'hide') {
                $("#loadMoreDivId").hide();
            }
        }, 'json');
    });
});
</script>



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
                            <div class="pd-list-tab">
                                <div class="resp-tabs-container">
                                    <div>
                                        <form class="row" method="post" id="searchForm">
                                            <input type="hidden"
                                                name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                                value="<?php echo $this->security->get_csrf_hash(); ?>">
                                            <input type="hidden" name="startPage" id="startPage"
                                                value="<?php echo $limit['start'];?>">
                                        </form>
                                        <div class="row" id="productListDivId">
                                            <?php 
											$viewData['list'] = $list;
											$this->load->view('cms/blog/list_row', $viewData);
											?>
                                        </div>
                                        <?php if($prodListCount > $perPage){?>
                                        <div class="w-100 text-center mt-4" id="loadMoreDivId">
                                            <a href="javascript:void(0);" id="loadMoreId"
                                                class="btn btn-primary d-inline-block buttonLoadMoreClass">
                                                Load More </a>
                                        </div>
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
</section>