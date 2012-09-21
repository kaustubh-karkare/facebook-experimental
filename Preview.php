<?php include("Systems.php"); ?>

<html><head><title>Facebook Experimental Preview</title><style>
body { font-family:'Tahoma'; font-size:16px; margin:0px; padding:0px; }
a { color:blue; text-decoration:none; }
img { cursor:pointer; width:200px; margin:2px; border:2px solid black; -o-transition: all 1s; -moz-transition: all 1s;-webkit-transition: all 1s; }
</style>
<script>
function expand(s){
  if( (document.getElementById('small'+s)).style.width=="200px"
  || (document.getElementById('small'+s)).style.width=="" ){
    for(i=1;i<=25;i++) if(s!=i) { img=document.getElementById('small'+i); img.style.width=0; img.style.border="0px solid black"; }
    (document.getElementById('small'+s)).style.width=1000;
    }
  else for(i=1;i<=25;i++){ img=document.getElementById('small'+i); img.style.width=200; img.style.border="2px solid black"; }
  }
</script></head><body>
<?php

echo "<center><br>Website Preview | Click on the screenshots to expand/contract them | Return to <a href='Index.php'>Facebook Experimental</a>. <br><br>";
for($i=1;$i<=25;$i++){
  echo "<img title='Click to Expand/Contract Image' id='small$i' src='Images/Preview/$i.png' onClick=\"expand($i);\" >";
  if($i%5==0) echo "<br>";
  }

if($clientip!=$serverip){
  $l = fdate($time)." - $clientip - Preview<br>";
  if(file_exists("Database/LOG.txt")) file_add("Database/LOG.txt",$l);
  else file_set("Database/LOG.txt",$l);
  }

?>
</body></html>