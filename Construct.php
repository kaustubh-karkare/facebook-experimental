<?php

include("Systems.php");
db_load_all();

//$target="include"; foreach(folder_get($target) as $file) if(eregi("1",file_get("$target/$file"))) echo $file."<br>";

if(user_online()=="") header("Location: Index.php"); // echo "<script>window.location = 'Home.php'</script>";

$self = user_online();
  if(db_get("Users",$self,"Account Status","")=="New"){
    db_set("Users",$self,"Account Status","Normal");
    header("Location: Construct.php?index=about");
    }

if($_GET["index"]=="") { $index="newsfeed"; $tab="Index"; } else { $index=$_GET["index"]; $tab="Index"; }
if($_GET["search"]!="") { $query=$_GET["search"]; $tab="Index"; $index="search"; }
if($_GET["admin"]!="") { $index="admin"; $tab="Index"; $admin=$_GET["admin"]; } 

if($_GET["uid"]=="") $uid=$self; else { $uid=$_GET["uid"]; $tab="Wall"; }
if($_GET["info"]!=""){ $uid = $info = $_GET["info"]; $tab="Info"; }
if($_GET["edit"]!=""){ $edit=$_GET["edit"]; $tab="Edit"; }
if($_GET["photos"]!=""){ $uid = $_GET["photos"]; $tab="Photos"; }

if($_GET["aid"]!="") { $aid=$_GET["aid"]; $tab="Album"; }
if($_GET["pid"]!="") { $pid=$_GET["pid"]; $tab="Photo"; }
if($_GET["tag"]!="") { $pid=$_GET["tag"]; $tab="PhotoTag"; }
if($_GET["fid"]!="") { $fid=$_GET["fid"]; $tab="News"; }
if($_GET["mid"]!="") { $mid=$_GET["mid"]; $tab="Message"; $index="message"; }

if($_GET["eid"]!="") { $eid=$_GET["eid"]; $tab="Event"; }
if($_GET["editevent"]!="") { $eid=$_GET["editevent"]; $tab="EditEvent"; }
if($_GET["tid"]!="") { $tid=$_GET["tid"]; $tab="Page"; }
if($_GET["editpage"]!="") { $tid=$_GET["editpage"]; $tab="EditPage"; }
if($_GET["gid"]!="") { $gid=$_GET["gid"]; $tab="Group"; }
if($_GET["editgroup"]!="") { $gid=$_GET["editgroup"]; $tab="EditGroup"; }

if(( $tab=="Photo" || $tab=="PhotoTag") && db_get("Photos",$pid,"Status","")!="Normal") echo "<script>window.location='?index=newsfeed';</script>";
if($tab=="Album" && db_get("Albums",$aid,"Status","")!="Normal") echo "<script>window.location='?index=newsfeed';</script>";
if($tab=="News" && db_get("News",$fid,"Status","")!="Normal") echo "<script>window.location='?index=newsfeed';</script>";
else if($tab=="News") $uid = db_get("News",$fid,"UID","");

$profile = profile_extract($uid);

if($clientip!=$serverip){
  $l = fdate($time)." - $clientip - $self - ".eregi_replace("<br>","; ",listout($_GET))."<br>";
  if(file_exists("Database/LOG.txt")) file_add("Database/LOG.txt",$l);
  else file_set("Database/LOG.txt",$l);
  }

if($tab=="Index" || $tab=="Message" ) $layout=0;
if($tab=="Wall" || $tab=="News" || $tab=="Info" || $tab=="Edit" || $tab=="Photos" ) $layout=1;
if($tab=="Album" || $tab=="Photo" || $tab=="PhotoTag") $layout=2;
if($tab=="Event" || $tab=="EditEvent" || $tab=="Group" || $tab=="EditGroup" || $tab=="Page" || $tab=="EditPage" ) $layout=3;

// if(db_get("Users",$uid,"EMail","")=="") echo "<script>window.location='?index=newsfeed';</script>";
// echo "$tab-U$uid-A$aid-P$pid-F$fid-Edit[$edit]-Photos[$photos]";

// -------------------------------------------------------

echo html_start1();
echo html_start2();

if($_SESSION["loginerror"]==100){
  echo "<script>ajax_post('Ajax.php?action=Client-Data','browser='+BrowserDetect.browser+'&version='+BrowserDetect.version+'&os='+BrowserDetect.OS,'',0);</script>";
  $_SESSION["loginerror"]="";
  }

if($layout==1){
  echo "<style>body { background:url('Images/System/bg-profile.png') repeat-x; }</style>";
  echo "<table border=0><tr>";
  echo "<td rowspan=100 class='construct-left' style='height:20px;'>".display_profile($uid)."</td>";
  echo "<td colspan=100 style='height:85px;'>".user_statusmessage($uid)."</td></tr>";
  echo "<tr><td colspan=100 style='height:20px;'>&nbsp;</td></tr>";

  echo "<tr><td class='tab-left' style='height:20px;'>&nbsp;&nbsp;</td>";
  $tablist=array("Wall","Info","Photos");
  $tablink=array("uid=$uid","info=$uid","photos=$uid");
  foreach($tablist as $i=>$t){
    if($tab==$t || ($t=="Wall"&&$tab=="News")) echo "<td class='tab-active'><a href='?$tablink[$i]'>$t</a></td>";
    else echo "<td class='tab-inactive'><a href='?$tablink[$i]'>$t</a></td>";
    echo "<td class='tab-pad'>&nbsp;</td>";
    }
  echo "<td class='tab-right'>&nbsp;</td></tr>";

  echo "<tr><td colspan=100 class='construct-data'>";
  if($tab=="Wall") echo "<div style='width:535px;'>";
  else echo "<div style='width:620px;'>";

  if($tab=="Wall") echo newsfeed_display_stream($self,"U$uid");
  if($tab=="News") echo newsfeed_display_specific($self,$fid,-1);
  if($tab=="Info") echo display_information($info);
  if($tab=="Edit") echo display_information_edit($edit);
  if($tab=="Photos") echo album_display_list($self,$uid);

  echo "</div></td><td class='construct-right1'>";
  if($tab=="Wall") echo ad_display(225,5);
  else echo ad_display(150,3);
  echo "</td></tr><tr><td colspan=100 class='construct-down'></td></tr>";
  echo "<table>";
  }

// -------------------------------------------------------

if($layout==2){
  echo "<table><tr><td class='constuct-data'><div style='width:820px;'>";
  if($tab=="Album") echo album_display($self,$aid);
  if($tab=="Photo") echo photo_display($self,$pid,0);
  if($tab=="PhotoTag") echo photo_display($self,$pid,1);
  echo "</div></td><td class='construct-right2'>".ad_display(150,3)."</td></tr>";
  echo "<tr><td colspan=3 class='construct-down'></td></tr>";
  echo "<table>";
  }

// -------------------------------------------------------

if($layout==3){
  echo "<table border=0>";
    echo "<tr><td class='construct-top' colspan=3></td></tr>";
  if($tab=="Event"){
    echo "<tr><td class='construct-left'>".event_left_display($eid,$self)."</td>";
    echo "<td class='construct-data'>".event_mid_display($eid,$self)."</td>";
    echo "<td class='construct-right'>".event_right_display($eid,$self)."</td></tr>";
    }
  else if($tab=="EditEvent"){
    echo "<tr><td class='construct-left'>".event_left_display($eid,$self)."</td>";
    echo "<td class='construct-data'>".event_edit_display($eid,$self)."</td>";
    echo "<td class='construct-right'>".event_right_display($eid,$self)."</td></tr>";
    }
  else if($tab=="Page"){
    echo "<tr><td class='construct-left'>".page_left_display($tid,$self)."</td>";
    echo "<td class='construct-data'>".page_mid_display($tid,$self,0)."</td>";
    echo "<td class='construct-right'>".page_right_display($tid,$self)."</td></tr>";
    }
  else if($tab=="EditPage"){
    echo "<tr><td class='construct-left'>".page_left_display($tid,$self)."</td>";
    echo "<td class='construct-data'>".page_mid_display($tid,$self,1)."</td>";
    echo "<td class='construct-right'>".page_right_display($tid,$self)."</td></tr>";
    }
  else if($tab=="Group"){
    echo "<tr><td class='construct-left'>".group_left_display($gid,$self)."</td>";
    echo "<td class='construct-data'>".group_mid_display($gid,$self,0)."</td>";
    echo "<td class='construct-right'>".group_right_display($gid,$self)."</td></tr>";
    }
  else if($tab=="EditGroup"){
    echo "<tr><td class='construct-left'>".group_left_display($gid,$self)."</td>";
    echo "<td class='construct-data'>".group_mid_display($gid,$self,1)."</td>";
    echo "<td class='construct-right'>".group_right_display($gid,$self)."</td></tr>";
    }
  echo "</table>";


  }

// -------------------------------------------------------

if($layout==0){
  echo "<table border=0>";
  echo "<tr><td class='construct-left' style='border-bottom:2px solid white;' >".homepage_display_left($self)."</td>";
       if($index=="admin") echo "<td class='construct-data2' style='border:1px solid #B3B3B3; border-top:0px solid #B3B3B3;'><div nostyle='width:815px;'>";
  else if($index=="about") echo "<td class='construct-data' style='border-left:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;'><div style='width:800px;'>";
  else echo "<td class='construct-data' style='border-left:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;'><div style='width:545px;'>";

  echo homepage_display_center($self,$index);
  if($index=="newsfeed") echo newsfeed_display_stream($self,0);
  if($index=="events") echo event_index_display($self,0);
  if($index=="oldevents") echo event_index_display($self,1);
  if($index=="friends") echo friendindex_display($self);
  if($index=="photos") echo photoindex_display($self);
  if($index=="notifications") echo notify_read(user_online(),1);
  if($index=="pages") echo page_index_display($self);
  if($index=="groups") echo group_index_display($self);
  if($index=="search") echo display_search($self,$query);
  if($index=="admin") echo admin_display($self,$admin);
  if($index=="message") echo message_display($self,$mid);
  if($index=="messages") echo message_index_display($self); 
  if($index=="about") echo display_about();

  if($index=="admin"){
    echo "</div></td><tr>";
    echo "<tr><td colspan=3 class='construct-down'></td></tr>";
    }
  else if($index=="about"){
    echo "</div></td><td class='construct-right1' style='width:20px; border-right:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;'>";
    echo "</td></tr> <tr><td colspan=3 class='construct-down'></td></tr>";
    }
  else {
    echo "</div></td><td class='construct-right1' style='border-right:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;'>";
    echo homepage_display_right($self);
    echo "</td></tr> <tr><td colspan=3 class='construct-down'></td></tr>";
    }
  echo "<table>";
  }

// -------------------------------------------------------

echo html_end();

db_save_all();

?>