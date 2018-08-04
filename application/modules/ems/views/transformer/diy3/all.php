
<style type="text/css">
    #chartContainer {
      width: 100%;
      height: 500px;
    }
    .amcharts-export-menu-top-right {
      top: 10px;
      right: 0;
    }
</style>
<script type="text/javascript">

    var csfrData = {};
    csfrData['<?php echo $this->security->get_csrf_token_name(); ?>']
                     = '<?php echo $this->security->get_csrf_hash(); ?>';
    $.ajaxSetup({
      data: csfrData
    });
    var chartData;
    var transName;
    $("#expectedLoss").hide();
    $("#chartContainer").hide();
    $("#alertMinMax").hide();
    $(document).ready(function(){
        $("#go").click(function(){
            var transId = $("#transId").val();
            if(transId == '' || transId <= 0){
                showErrorMsg(1);
                return false;
            }
            var loading = $("#loading").val();
            if(loading == ''){
                showErrorMsg(2);
                return false;
            }
            showErrorMsg(0);
            $("#myDiv").show();
            $.post( "<?php echo base_url();?>ems/transformer/getAjaxData/"+transId+"/"+loading, function( data ) {
                showErrorMsg(0);
                $("#myDiv").hide();
                transName = data.transName;
                if(data.errStatus){
                    $("#expectedLoss").hide();
                    $("#chartContainer").hide();
                    $("#alertMsg").text(data.errMsg);
                    $("#alertMsg").addClass('alert alert-danger');
                }else{
                    $("#expectedLoss").show().text('Expected Loss : '+data.expectedLoss);
                    $("#chartContainer").show();
                    chartData = data.newChartData;
                    //alert(chartData);
                    getChart();
                }
            },'json');

        });

        $("#transId").change(function(){
            var transId = $(this).val();
            $.post( "<?php echo base_url();?>ems/transformer/getAjaxMinMaxLoading/"+transId, function( data ) {
                $("#loading").val('');
                $("#loading").attr("min",data.min);
                $("#loading").attr("max",data.max);
                $("#alertMinMax").html(data.info);
                $("#alertMinMax").show();
                $("#expectedLoss").hide();
                $("#chartContainer").hide();
            },'json');
        });
    });

    function showErrorMsg(type){
        var msg = '';
        if(type == 1){
            msg = 'Please select Transformer.';
        }else if (type == 2) {
            msg = 'Please enter Loading value.';
        }
        $("#alertMsg").text(msg);
        if(msg != ''){
            $("#alertMsg").addClass('alert alert-danger');
        }else{
            $("#alertMsg").removeClass('alert alert-danger');
        }
        
    }
</script>

<div class="row">
    <form name="frmReport" action="" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="col-lg-4">
            <label>Transformer : <span class="red">*</span></label>
            <select class="form-control" name="transId" id="transId">
                <option value=" ">Select One</option>
                <?php if(count($transDetails) > 0){?>
                    <?php foreach($transDetails as $val){ 
                        $selected = '';
                        if($selectedTransId == $val->id){ 
                            $selected = 'selected = "selected"';
                        }
                    ?>
                    <option value="<?php echo $val->id;?>" <?php echo $selected;?>><?php echo $val->trans_name;?></option>
                    <?php }?>
                <?php }?>    
            </select>
        </div>
        <div class="col-lg-4">
            <label>Loading : <span class="red">*</span></label>
            <input class="form-control" name="loading" id="loading" value="<?php echo $selectedLoading;?>" type="number" step="0.01" min="0" max="0" required>
        </div>
        <div class="col-lg-2">
            <input type="button" id="go" name="go" class="btn btn-primary" value="Go">
        </div>
    </form>
    
</div>
<br>
<div id="alertMinMax" class="alert alert-info">
    
</div>

<div class="panel panel-default">
    <div class="panel-heading"><!-- Transformer Loading Vs Loss Chart style="float: right;" --> 
        <span class="lbl_hd" id="expectedLoss" ></span>
    </div>
    <div class="panel-body">
        <div id="myDiv" style='display: none; width: 100%;text-align: center;'>    
            <img src="<?php echo base_url();?>resource/logo/loader.gif">
        </div>
        <div id="alertMsg" role="alert" class=""></div>
        <div id="chartContainer"></div>
    </div>
</div>


<script type="text/javascript">

function getChart(){
    var chart = AmCharts.makeChart("chartContainer", {
        "type": "serial",
        "theme": "light",
        "marginRight": 80,
        "dataProvider": chartData,
        "valueAxes": [{
            "position": "left",
            "axisAlpha": 0.1,
            "axisThickness" : 2,
            "title": "Loss",
        }],
        "startDuration": 1,
        "graphs": [{
            "id":"g1",
            "balloonText": "Loading : [[category]]<br><b><span style='font-size:14px;'> Loss : [[value]]</span></b>",
            "bullet": "round",
            "bulletSize": 8,
            "lineColor": "#67b7dc",
            "lineThickness": 2,
            /*"type": "smoothedLine",*/
            "valueField": "loss"
        },{
            "valueAxis": "g1",
            "balloonText": "<b>Expected Loss : [[value]]</b>",
            "fillColorsField": "color",
            "lineColor": "#3acaad",
            "fillAlphas": 1,
            "lineAlpha": 2,
            "valueField": "demand",
            "bullet": "diamond",
            "bulletSize": 20,
            "type": "line",
          }],
        /*"chartScrollbar": {
            "graph":"g1",
            "gridAlpha":0,
            "color":"#888888",
            "scrollbarHeight":55,
            "backgroundAlpha":0,
            "selectedBackgroundAlpha":0.1,
            "selectedBackgroundColor":"#888888",
            "graphFillAlpha":0,
            "autoGridCount":true,
            "selectedGraphFillAlpha":0,
            "graphLineAlpha":0.2,
            "graphLineColor":"#c2c2c2",
            "selectedGraphLineColor":"#888888",
            "selectedGraphLineAlpha":1

        },*/
        "chartCursor": {
            "categoryBalloonEnabled": true,
            /*"cursorAlpha": 0,*/
            "valueLineEnabled":true,
            "valueLineBalloonEnabled":true,
            /*"valueLineAlpha":0.5,
            "fullWidth":true*/
        },
        "categoryField": "loading",
        "categoryAxis": {
            "minPeriod": "YYYY",
            "parseDates": false,
            "minorGridAlpha": 0.1,
            "minorGridEnabled": true,
            "title" : "Loading"
        },
        "export": {
            "enabled": true
        },
        "titles": [{
            "text": "Transformer #"+transName
          }, {
            "text": "Data set of Loading Vs Loss",
            "bold": false
          }],
    });

    chart.addListener("rendered", zoomChart);
    if(chart.zoomChart){
        chart.zoomChart();
    }
}

function zoomChart(){
    chart.zoomToIndexes(Math.round(chart.dataProvider.length * 0.4), Math.round(chart.dataProvider.length * 0.55));
}
  
</script>


  



