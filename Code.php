<?php
include("Systems.php");
$d=folder_get("."); foreach($d as $f) if(eregi(".php$",$f)) echo text_screen(file_get($f));
$d=folder_get("Include/"); foreach($d as $f) if(eregi(".php$",$f)) echo text_screen(file_get("Include/".$f));
?>
<style> * { font-family:'Courier New'; font-size:11px; } </style>