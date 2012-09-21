<?php

include("Systems.php");
db_load_all();

// echo "<div style='margin:4px; padding:2px;color:red; border:1px solid red; background:#FFEEEE;'>Retrieved via Ajax ... Timestamp : $time (".fdate().")</div>";

$action = $_GET["action"];

if(!user_online()) $action=-1;

if($action=="Friend-Request"){	$uid=user_online(); friend_request($uid,$_POST["uid"],1); if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); if($_POST["refresh"]=="friendindex") echo friendindex_refresh($uid); }
if($action=="Friend-Revoke"){	$uid=user_online(); friend_request($uid,$_POST["uid"],0); if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); if($_POST["refresh"]=="friendindex") echo friendindex_refresh($uid); }
if($action=="Friend-Accept"){	$uid=user_online(); friend_accept($_POST["uid"],$uid,1);  if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); if($_POST["refresh"]=="friendindex") echo friendindex_refresh($uid); if($_POST["refresh"]=="friendrequests") echo friend_read($uid); }
if($action=="Friend-Ignore"){	$uid=user_online(); friend_ignore($_POST["uid"],$uid);    if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); if($_POST["refresh"]=="friendindex") echo friendindex_refresh($uid); if($_POST["refresh"]=="friendrequests") echo friend_read($uid); }
if($action=="Friend-Remove"){	$uid=user_online(); friend_accept($uid,$_POST["uid"],0);  if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); if($_POST["refresh"]=="friendindex") echo friendindex_refresh($uid); }

if($action=="Poke-Accept"){	$uid=user_online(); friend_poke($_POST["uid"],$uid,0); if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); }
if($action=="Poke-Perform"){	$uid=user_online(); friend_poke($uid,$_POST["uid"],1); if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); }
if($action=="Punch-Accept"){	$uid=user_online(); friend_punch($_POST["uid"],$uid,0); if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); }
if($action=="Punch-Perform"){	$uid=user_online(); friend_punch($uid,$_POST["uid"],1); if($_POST["refresh"]=="profile") echo display_profile($_POST["uid"]); if($_POST["refresh"]=="homepage") echo homepage_refresh_right($uid); }

if($action=="Edit-About"){	update_about(user_online(),$_POST["text"]); $profile=profile_extract(user_online()); echo display_profile(user_online()); }
if($action=="Status-Message-Clear"){ $uid=user_online(); db_set("Users",user_online(),"Status Message",""); echo "<h2>".user_fullname(user_online())."</h2>"; }

if($action=="Search-Basic"){	echo ajax_search($_POST["query"]); }

if($action=="Chat-Refresh"){	echo chat_refresh(user_online(),$_POST["user"]); }
if($action=="Chat-Index"){	echo chat_index(user_online()); }
if($action=="Chat-Command"){	if($_POST["command"]!=0) chat_command(user_online(),$_POST["user"],$_POST["command"],$_POST["text"]); if($_POST["command"]!=7 && $_POST["command"]!=8) echo chat_display(user_online()); }

if($action=="FriendRequest-Get"){ echo friend_read(user_online()); }
if($action=="Notifications-Get"){ echo notify_read(user_online(),0); }
if($action=="Notifications-Clear"){ notify_clear(user_online()); echo "<table><tr><td>Notifications Deleted.</td></tr></table>"; }

if($action=="Comment-Mark"){ 	$self=user_online(); comment_command($_POST["command"],$_POST["id"],$_POST["cid"],$_POST["text"],$self); if($_POST["refresh"]!="disabled") echo comment_refresh($self,$_POST["id"]); }

if($action=="Message-Names"){	echo message_ajax_target(user_online(),$_POST["id"],$_POST["query"]); }
if($action=="Message-Get"){	echo message_ajax_new(user_online()); }

if($action=="Client-Data"){	clientdata(user_online(),$_POST["browser"],$_POST["version"],$_POST["os"]); }

db_save_all();

?>