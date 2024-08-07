<?php
header("Content-type: text/css; charset: UTF-8");
$color = get_theme_mod('primary_color', '#ff0000');

?> 
body {
    background-color: <?php echo $color; ?>;
}