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
        var url = "<?php echo base_url($module . '/' . $controller . '/getAjaxData'); ?>";
        $.post(url, formData, function(result) {
            $("#productListDivId").append(result.html);
            $("#startPage").val(result.startPage);
            if (result.loaderStatus == 'hide') {
                $("#loadMoreId").hide();
            }
        }, 'json');
    });
});
</script>
<section class="innerbanner">
    <div class="banner-cont">
        <h1 class="title">Events</h1>
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
                <form class="row" method="post" id="searchForm">
                    <input type="hidden" name="startPage" id="startPage" value="<?php echo $limit['start']; ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>">
                </form>
                <div class="col-12 col-md-12 col-lg-9 col-xl-9 float-left event-left p-0">
                    <div id="productListDivId">
                        <?php foreach ($list as $key => $viewData) {
                            $inData['module'] = $module;
                            $inData['controller'] = $controller;
                            $inData['row'] = $viewData;
                        ?>
                        <?php $this->load->view($module . '/' . $controller . '/single', $inData); ?>
                        <?php } ?>
                    </div>

                    <?php if ($listCount > $limit['perPage']) { ?>
                    <div class="buttonLoadMoreClass">
                        <a href="javascript:void(0);" id="loadMoreId" class="btn btn-primary float-right">Load More</a>
                    </div>
                    <?php } ?>
                </div>

                <div class="col-12 col-md-12 col-lg-3 col-xl-3 float-left event-right">
                    <?php if (count($program) > 0) { ?>
                    <div class="w-100 float-left recent-events">
                        <h4>Programmes</h4>
                        <div class="w-100 float-left event-seps">
                            <?php foreach ($program as $key => $value) { ?>
                            <a href="<?php echo base_url($module . '/programme/details/' . $value->pro_title_url); ?>"
                                class="w-100 float-left"><?php echo $value->program_title; ?><i
                                    class="fas fa-angle-right"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="w-100 float-left recent-events">
                        <?php if (count($completed) > 0) { ?>
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