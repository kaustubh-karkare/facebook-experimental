<?php

include("Systems.php");
db_load_all();

$action = $_GET["action"];
$redirect = -1;

if($action=="Account-Login") action_Login();
if($action=="Account-Create") action_CreateAccount();
if($action=="System-Refresh") system_refresh();

if(user_online()==0) $action=-1;

if($action=="Account-Logout") action_Logout();

// if($action=="News-Create") news_create($_POST["location"],user_online(),$_POST["text"]);

if($action=="Search-Perform") search_perform($_POST["query"]); 

if($action=="Profile-EditDisplay") profile_edit3();
if($action=="Profile-Edit") profile_edit1();
if($action=="Profile-Picture-Upload") profile_edit2(1);
if($action=="Profile-Picture-Delete") profile_edit2(0);

if($action=="Photo-Rotate"){ photo_rotate($_POST["pid"],$_POST["deg"]); }

if($action=="Newsfeed-Write"){ newsfeed_text_add(user_online(),$_POST["location"],$_POST["data"]); }

if($action=="Album-Create"){ album_create(user_online()); }
if($action=="Album-Edit"){ album_edit(user_online()); }
if($action=="Album-Delete"){ album_delete(user_online()); }
if($action=="Album-Post"){ newsfeed_share(user_online(),"Album",$_POST["aid"]); }

if($action=="Photo-Cover"){ photo_cover(user_online()); }
if($action=="Photo-Delete"){ photo_delete(user_online()); }
if($action=="Photo-Profile"){ photo_profilepicture(user_online()); }
if($action=="Photo-Post"){ newsfeed_share(user_online(),"Photo",$_POST["pid"]); }

if($action=="Image-Tag"){ photo_tag(user_online()); }
if($action=="Image-Untag"){ photo_untag(user_online()); }

if($action=="Notifications-Clear"){ notify_clear(user_online()); }

if($action=="Ad-Create"){ ad_create(user_online(),$_POST["head2"],$_POST["info"],$_POST["more"]); }
if($action=="Ad-Update"){ db_set("Ads",$_POST["bid"],$_POST["command"],$_POST["text"]); }

if($action=="Event-Create"){ event_create(user_online()); }
if($action=="Event-Delete"){ event_delete(user_online()); }
if($action=="Event-Respond"){ event_respond(user_online(),$_POST["eid"],$_POST["response"]); }
if($action=="Event-Update"){ event_update(user_online()); }
if($action=="Event-Post"){ newsfeed_share(user_online(),"Event",$_POST["eid"]); }

if($action=="Page-Create"){ page_create(user_online()); }
if($action=="Page-Delete"){ page_delete(user_online()); }
if($action=="Page-Mark"){ page_mark(user_online(),$_POST["tid"],$_POST["command"]); }
if($action=="Page-Update"){ page_update(user_online()); }
if($action=="Page-Post"){ newsfeed_share(user_online(),"Page",$_POST["tid"]); }

if($action=="Group-Create"){ group_create(user_online()); }
if($action=="Group-Delete"){ group_delete(user_online()); }
if($action=="Group-Mark"){ group_mark(user_online(),$_POST["gid"],$_POST["command"]); }
if($action=="Group-Update"){ group_update(user_online()); }
if($action=="Group-Post"){ newsfeed_share(user_online(),"Group",$_POST["gid"]); }

if($action=="Message-Send"){ message_create(user_online(),$_POST["to"],$_POST["subject"],$_POST["message"]); }
if($action=="Message-Reply"){ message_reply(user_online(),$_POST["mid"],$_POST["text"]); }
if($action=="Message-Delete"){ message_delete(user_online(),$_POST["mid"]); }
if($action=="Message-Action"){ message_unread(user_online()); }

db_save_all();

if($_GET["action"]!="")
  header("Location: ".$_SERVER['HTTP_REFERER']);
if($_SERVER['HTTP_REFERER']=="") header("Location: Index.php");
if($action=="Account-Logout") header("Location: Index.php");
if($redirect!=-1){
  $ref = $_SERVER['HTTP_REFERER'];
  eregi("^([^\?]*)\??([^\?]*)$",$ref,$ref);
  header("Location: ".$ref[1].$redirect);
  }

if($action=="Account-Login") header("Location: Construct.php");
echo "<a href='".$_SERVER['HTTP_REFERER']."'>".$_SERVER['HTTP_REFERER']."</a>";

?>