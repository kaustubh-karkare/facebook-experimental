<?php


include("Systems.php");
db_load_all();

if(user_online()) header("Location: Construct.php?");

if($clientip!=$serverip){
  $l = fdate($time)." - $clientip - Login Page<br>";
  if(file_exists("Database/LOG.txt")) file_add("Database/LOG.txt",$l);
  else file_set("Database/LOG.txt",$l);
  }

echo html_start1();
echo html_login();
if($_SERVER['HTTP_REFERER']==""){
  $_SESSION["notice"].="<img src='Images/System/hp-redflag.png' style='float:left; margin:4px; '>Please understand that what you are seeing is not the real Facebook Login page, but a lookalike. Please do not enter your real Facebook Account Details here. No copyright infringement is intended as this is purely a learning experiment.".NL;
  $_SESSION["notice"].="<img src='Images/System/hp-warning.png' style='float:left; margin:4px; '>Due to this website's dependance on Cookie-based Sessions to save your login state, you shall be unable to proceed beyond this page unless Cookies are enabled on your web browser.".NL;
  $_SESSION["notice"].="<img src='Images/System/hp-world.png' style='float:left; margin:4px; '>Due to the lack of manpower, this website has undergone primary testing only on Opera 10. As a result, there may be some problems if you are using other browsers.".NL;
  }
echo display_notice();

db_save_all();

?>
<script>
if(BrowserDetect.browser=="Explorer"){ alert("Internet Explorer Not Supported! A solution to this problem has been found, although implementation will take a while as it creates a different set of problems that need now be resolved. Please try again using a different browser. Redirecting to Website Preview page ..."); window.location="Preview.php"; }
</script></body></html>