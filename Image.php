<?php

if(!empty($_GET["uid"])){
  $uid = $_GET["uid"];
  if(file_exists("Images/Profile/$uid.jpg")){ header("Content-type: image/jpeg"); $img=file_get_contents("Images/Profile/$uid.jpg"); echo $img;; }
  else if(file_exists("Images/Profile/$uid.png")){ header("Content-type: image/png"); $img=file_get_contents("Images/Profile/$uid.png"); echo $img;; }
  else if(file_exists("Images/Profile/$uid.gif")){ header("Content-type: image/gif"); $img=file_get_contents("Images/Profile/$uid.gif"); echo $img;; }
  else { $image=1;
    include("Systems.php"); db_load("Users");
    header("Content-type: image/jpeg");
    if(db_get("Users",$uid,"Sex","")=="Male") $img=file_get_contents("Images/System/default-male-".($uid%3+1).".jpg");
    if(db_get("Users",$uid,"Sex","")=="Female") $img=file_get_contents("Images/System/default-female-".($uid%3+1).".jpg");
    echo $img;
    }
  }

else if(!empty($_GET["eid"])){
  $eid=$_GET["eid"];
       if(file_exists("Images/Event/$eid.jpg")){ header("Content-type: image/jpeg"); $img=file_get_contents("Images/Event/$eid.jpg"); echo $img;; }
  else if(file_exists("Images/Event/$eid.png")){ header("Content-type: image/png"); $img=file_get_contents("Images/Event/$eid.png"); echo $img;; }
  else if(file_exists("Images/Event/$eid.gif")){ header("Content-type: image/gif"); $img=file_get_contents("Images/Event/$eid.gif"); echo $img;; }
  else { header("Content-type: image/png"); $img=file_get_contents("Images/System/default-event.png"); echo $img;; }
  }

else if(!empty($_GET["tid"])){
  $tid=$_GET["tid"];
       if(file_exists("Images/Page/$tid.jpg")){ header("Content-type: image/jpeg"); $img=file_get_contents("Images/Page/$tid.jpg"); echo $img;; }
  else if(file_exists("Images/Page/$tid.png")){ header("Content-type: image/png"); $img=file_get_contents("Images/Page/$tid.png"); echo $img;; }
  else if(file_exists("Images/Page/$tid.gif")){ header("Content-type: image/gif"); $img=file_get_contents("Images/Page/$tid.gif"); echo $img;; }
  else { header("Content-type: image/gif"); $img=file_get_contents("Images/System/default-page.gif"); echo $img;; }
  }

else if(!empty($_GET["pid"])){
  $pid = $_GET["pid"];
       if(file_exists("Images/Photos/$pid.jpg")){ header("Content-type: image/jpeg"); $img=file_get_contents("Images/Photos/$pid.jpg"); echo $img;; }
  else if(file_exists("Images/Photos/$pid.png")){ header("Content-type: image/png"); $img=file_get_contents("Images/Photos/$pid.png"); echo $img;; }
  else if(file_exists("Images/Photos/$pid.gif")){ header("Content-type: image/gif"); $img=file_get_contents("Images/Photos/$pid.gif"); echo $img;; }
  else { header("Content-type: image/jpeg"); $img=file_get_contents("Images/System/image-na.jpg"); echo $img;; }
  }

else if(!empty($_GET["preview"])){
  $preview = $_GET["preview"];
  if(file_exists("Images/Preview/$preview.png")){ header("Content-type: image/png"); $img=file_get_contents("Images/Preview/$preview.png"); echo $img;; }
  else { header("Content-type: image/jpeg"); $img=file_get_contents("Images/System/image-na.jpg"); echo $img;; }
  }

else { $sys = $_GET["sys"];
       if(file_exists("Images/System/$sys.jpg")){ header("Content-type: image/jpeg"); $img=file_get_contents("Images/System/$sys.jpg"); echo $img;; }
  else if(file_exists("Images/System/$sys.png")){ header("Content-type: image/png"); $img=file_get_contents("Images/System/$sys.png"); echo $img;; }
  else if(file_exists("Images/System/$sys.gif")){ header("Content-type: image/gif"); $img=file_get_contents("Images/System/$sys.gif"); echo $img;; }
  else { header("Content-type: image/jpeg"); $img=file_get_contents("Images/System/image-na.jpg"); echo $img;; }
  }

?>