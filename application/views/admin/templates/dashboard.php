<!DOCTYPE html>
<html lang="en">
<head>
    <?php if(isset($head)): echo $head; endif;?>
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <?php if(isset($menu)): echo $menu; endif;?>
        </nav>
        <div id="page-wrapper">
        <?php if(isset($content)): echo $content; endif;?>
        </div>
    </div>
</body>
</html>


