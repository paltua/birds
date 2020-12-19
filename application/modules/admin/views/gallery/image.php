<!-- DataTables CSS -->
<link href="<?php echo base_url() . $resourceNameAdmin; ?>vendor/datatables-plugins/dataTables.bootstrap.css"
    rel="stylesheet">
<!-- DataTables Responsive CSS -->
<link href="<?php echo base_url() . $resourceNameAdmin; ?>vendor/datatables-responsive/dataTables.responsive.css"
    rel="stylesheet">
<!-- DataTables JavaScript -->
<script src="<?php echo base_url() . $resourceNameAdmin; ?>vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() . $resourceNameAdmin; ?>vendor/datatables-plugins/dataTables.bootstrap.min.js">
</script>
<script src="<?php echo base_url() . $resourceNameAdmin; ?>vendor/datatables-responsive/dataTables.responsive.js">
</script>

<script type="text/javascript">
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $("#imageId").show();
            $('#blah')
                .attr('src', e.target.result)
                .width(150)
                .height(100);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    $("#imageId").hide();
    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>'] =
        '<?php echo $this->security->get_csrf_hash(); ?>';
    //alert('<?php echo $this->security->get_csrf_hash(); ?>');
    $.ajaxSetup({
        data: csfrData
    });

    $('#dataTables-example').DataTable({
        responsive: true,
        serverSide: true,
        ajax: {
            url: "<?php echo $dataTableUrl; ?>", // json datasource
            type: "post", // method  , by default get
            dataType: "json",
            error: function() { // error handling
                //$(".employee-grid-error").html("");
                $("#dataTableId").append(
                    '<tbody class="employee-grid-error"><tr><th colspan="6">No record found.</th></tr></tbody>'
                );
                $("#data-table_processing").css("display", "none");
            }
        },

        deferRender: true,
        bProcessing: true,
        iDisplayLength: 10,
        bPaginate: true,
        scroller: {
            loadingIndicator: true,
        },
        columnDefs: [{
            "targets": 'no-sort',
            "orderable": false,
        }],
        aaSorting: [],
    });

    $('#dataTables-example').on('click', '.deleteChange', function() {
        var confirmStat = confirm(
            "Are you sure to delete this <?php echo $controller; ?>?");
        if (confirmStat) {
            return true;
        } else {
            return false;
        }
    });

    $('#dataTables-example').on('click', '.statusChange', function() {
        var confirmStat = confirm(
            "Are you sure to change the status of this <?php echo $controller; ?>?");
        if (confirmStat) {
            var url = '<?php echo $statusUrl; ?>';
            var am_id = $(this).attr('name');
            var am_status = $(this).attr('value');
            $.post(url, {
                am_id: am_id
            }, function(data) {
                if (am_status == 'lock') {
                    $("#status_" + am_id).attr('value', 'unlock').removeClass('btn-warning')
                        .addClass('btn-info');
                    $("#i_status_" + am_id).removeClass('fa-lock').addClass('fa-unlock');
                    $("#span_status_" + am_id).text('Active');
                } else {
                    $("#status_" + am_id).attr('value', 'lock').removeClass('btn-info')
                        .addClass(
                            'btn-warning');
                    $("#i_status_" + am_id).removeClass('fa-unlock').addClass('fa-lock');
                    $("#span_status_" + am_id).text('Inactive');
                }
                $("#msgShow").html(data.msg);
            }, "json");
        }
    });


});
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Gallery</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div id="msgShow"></div>
    <?php if ($msg != '') : ?>
    <div class="col-lg-12">
        <?php echo $msg; ?>
    </div>
    <?php endif; ?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add Image
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <!-- Nav tabs -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>File input</label>
                                <input type="file" name="myFile" onchange="readURL(this);"
                                    accept="image/gif, image/jpeg, image/png">
                                <?php //echo form_error('filename', '<p class="text-danger">', '</p>'); 
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div id="imageId" class=" alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <img id="blah" src="#" alt="" />
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Image Gallery
            </div>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th class="no-sort">Image</th>
                            <th>Image URL</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>