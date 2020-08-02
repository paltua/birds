<!DOCTYPE html>
<html lang="en">

<head>
    <?php if(isset($head)): echo $head; endif;?>
    <?php if($google_add != ''): echo $google_add; endif;?>
</head>

<body>
    <div id="wrapper">
        <div class="intro-header">
            <?php if(isset($content)): echo $content; endif;?>
        </div>
    </div>
    <div id="myModal" class="modal fade myModalLg" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
</body>

</html>