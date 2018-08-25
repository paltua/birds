<!DOCTYPE html>
<html lang="en">
<head>
    <?php if(isset($head)): echo $head; endif;?>
</head>
<body>
    <div id="wrapper">
        <div class="intro-header">
        <?php if(isset($content)): echo $content; endif;?>
        </div>
    </div>
</body>
</html>