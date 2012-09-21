<?php

//   db_create("Chat","UID,Status,Tabs,Maximized,Lists");

// --------------------------------------------------------

function chat_initiate($uid){
  if($uid=="")return;
  global $time;
  db_set("Chat",$uid,"Update",$time+2);
  $text = "<table><tr><td><img src='Images/System/chat_systems.jpg' style='float:left;height:100px;'> Information requests have been sent to the server via Ajax. If the \"Contacting Server ...\" tab does not disappear to be replaced by Facebook-like Chat-boxes within a few seconds of loading this page, this could indicate one of the following problems : Server has been Overloaded or gone Offline, the link between your computer and the Server could have been broken, or the browser you are using is incompatible with the scripts that the Chat Systems relies on. If the browser is your problem, please try Opera, Google Chrome or Safari, on which the working of the Chat (and most others) Systems has been verified.</td></tr></table>";
  $link = popup_text("chat_systems","Chat Systems",$text,"","","",$output);

  $str.="<div id='chat-position' class='chat-position'>";
  $str.="<div class='initial' onClick='$link'><a>Contacting Server ...</a></div>";
  $str.="</div><script>window.setTimeout(\"ajax_post('Ajax.php?action=Chat-Command','command=0','chat-position',0);\",250);</script> $output";
  return $str;
  }

// --------------------------------------------------------

function chat_command($uid,$tab,$action,$text){
  global $time;
  $text = stripslashes($text);
  if($action==1) db_array_add("Chat",$uid,"Tabs",$tab);
  if($action==2) db_array_del("Chat",$uid,"Tabs",$tab);
  if($action==3 || $action==1) db_array_add("Chat",$uid,"Maximized",$tab);
  if($action==4 || $action==2) db_array_del("Chat",$uid,"Maximized",$tab);
  if($action==5) db_set("Chat",$uid,"Status","Online");
  if($action==6) db_set("Chat",$uid,"Status","Minimized");
  if($action==7) file_delete("Database/Chat/" .$uid. "_" .$tab. ".txt");
  if($action==8){
    if(db_get("Users",$tab,"Session","")!=""){ //file_exists("Database/Chat/" .$tab. "_" .$uid. ".txt")){
      if(!file_exists("Database/Chat/" .$tab. "_" .$uid. ".txt")) file_set("Database/Chat/" .$tab. "_" .$uid. ".txt","");
      file_add("Database/Chat/" .$uid. "_" .$tab. ".txt","<p><b title='".fdate($time)."'>Me :</b> $text</p>".NL);
      file_add("Database/Chat/" .$tab. "_" .$uid. ".txt","<p><a href='?uid=$tab' title='".fdate($time)."'>".db_get("Users",$uid,"First Name","")." :</a> $text</p>".NL);
      }
    else file_add("Database/Chat/" .$uid. "_" .$tab. ".txt","<p><n>".db_get("Users",$tab,"First Name","")." has not recieved your message as ".((db_get("Users",$tab,"Sex","")=="Male")?"he":"she")." is offline.</n></p>".NL);
    db_array_add("Chat",$tab,"Tabs",$uid);
    db_array_add("Chat",$tab,"Maximized",$uid);
    }
  if($action==9) db_set("Chat",$uid,"Status","Offline");
  }

// --------------------------------------------------------

function chat_login($uid){
  $chatfiles = folder_get("Database/Chat/");
  if(is_array($chatfiles))
    foreach($chatfiles as $chatfile){
      if(eregi($uid."_([0-9]+).txt",$chatfile)){
        file_delete("Database/Chat/$chatfile");
        }
      else if(eregi("([0-9]+)_$uid.txt",$chatfile,$result)){
        file_add("Database/Chat/".$result[0],"<p><n>".db_get("Users",$uid,"First Name","")." is now online.</n></p>".NL);
        }
      }
  }

function chat_logout($uid){

  $chatfiles = folder_get("Database/Chat/");
  if(is_array($chatfiles))
    foreach($chatfiles as $chatfile)
      if(eregi($uid."_([0-9]+).txt",$chatfile,$result)){
        file_delete("Database/Chat/$chatfile");
        file_add("Database/Chat/".$result[1]."_$uid.txt","<p><n>".db_get("Users",$uid,"First Name","")." is now offline.</n></p>".NL);
        }
  }

// --------------------------------------------------------

function chat_refresh($uid,$user){
  global $time;
  if(!db_array_chk("Chat",$uid,"Maximized",$user)) return "";//chat_display($uid);
  $chatfile = $uid."_".$user;
  if(file_exists("Database/Chat/$chatfile.txt")) $chatdata.= file_get("Database/Chat/$chatfile.txt");
  else file_set("Database/Chat/$chatfile.txt","");
  return $chatdata;
  }

// -----------------------------------------------------------

function chat_index($uid){
  $status =	db_get("Chat",$uid,"Status","");
  if($status==""){ $status="Offline"; db_set("Chat",$uid,"Status","Offline"); }
  $tabs =	db_get("Chat",$uid,"Tabs","");
  if(!is_array($tabs)) $tabs = array();
  $max =	db_get("Chat",$uid,"Maximized","");
  if(!is_array($max)) $max = array();

  $users = db_get_ids("Users");
  $x = 0;
  if(is_array($users)) foreach($users as $user){
    if($user==$uid) continue;
    if(db_get("Users",$user,"Session","")=="") continue;
    if($uid!=1 && $user!=1 && !friend_check($uid,$user)) continue;
    if(db_get("Chat",$user,"Status","")=="Offline") continue;
    $fullname = db_get("Users",$user,"First Name","")." ".db_get("Users",$user,"Last Name","");
    if(in_array($user,$tabs)){
      if(in_array($user,$max)) $userlist.="<p><a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=4','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Image.php?uid=$user' height=20> <span>$fullname</span></a></p>";
      else $userlist.="<p><a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=3','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Image.php?uid=$user' height=20> <span>$fullname</span></a></p>";
      }
    else $userlist.="<p><a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=1','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Image.php?uid=$user' height=20> <span>$fullname</span></a></p>";
    $x++;
    }
  $str="";
  if($status=="Online"){
    $str.="<table class='chatbox-index'>";
    $str.="<tr><td class='ci11'>Chat ($x)</td><td class='ci12'>";
    $str.="<a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=6','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Images/System/chat-minimize.png' onMouseOver='this.src=\"Images/System/chat-minimize-highlight.png\";' onMouseOut='this.src=\"Images/System/chat-minimize.png\";' >";
    $str.="</td></tr>";
    $str.="<tr><td class='ci21' colspan=2><a>Options</a>".SEP;
      $str.="<a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=9','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\">Go Offline</a></td></tr>";
    $str.="<tr><td class='ci31' colspan=2>";
    if($x) $str.="$userlist"; else $str.="<p>No friends are online.</p>";
    $str.="</td></tr>";
    $str.="<tr><td class='ci41' colspan=2 onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=6','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><a>Chat</a></td></tr>";
    $str.="</table>";
    }
  else if($status=="Minimized"){
    $str.="<table class='chatbox-index'>";
    $str.="<tr><td class='ci41' colspan=2 onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=5','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><a>Chat ($x)</a></td></tr>";
    $str.="</table>";
    }
  $str.="<input type='hidden' id='chat_tab_new' value='".implode($tabs,',')."' />";
  $str.="<input type='hidden' id='chat_max_new' value='".implode($max,',')."' />";
  return $str;
  }

// ------------------------------------------------------

function chat_check($uid,$tab,$max){
  $t1 = explode($tab);
  $t2 = db_get("Chat",$uid,"Tabs",array());
  $m1 = explode($max);
  $m2 = db_get("Chat",$uid,"Maximized",array());
  if(!is_array($t2))$t2 = array($t2);
  if(!is_array($m2))$m2 = array($m2);

  $refresh=0;
  foreach($t2 as $t){ if(!in_array($t,$t1)) $refresh=1; }
  foreach($t1 as $t){ if(!in_array($t,$t2)) $refresh=1; }
  foreach($m2 as $m){ if(!in_array($m,$m1)) $refresh=1; }
  foreach($m1 as $m){ if(!in_array($m,$m2)) $refresh=1; }
  return $refresh;
  }

// ------------------------------------------------------

function chat_display($uid){
  global $time;
  $str="";
  $refresh=" ";

  $status =	db_get("Chat",$uid,"Status","");
  if($status==""){ $status="Online"; db_set("Chat",$uid,"Status","Online"); }
  $tabs =	db_get("Chat",$uid,"Tabs","");
  if(!is_array($tabs)) $tabs = array();
  $max =	db_get("Chat",$uid,"Maximized","");
  if(!is_array($max)) $max = array();

  if($status=="Online"||$status=="Minimized"){

  $str.="<table class='chatbox-container'><tr>";

  foreach($tabs as $user){
    $fullname = db_get("Users",$user,"First Name","")." ".db_get("Users",$user,"Last Name","");
    $chatfile = $uid."_".$user;

    $str.="<td>";
    if(in_array($user,$max)){
      $str.="<table class='chatbox-user'>";
      $str.="<tr><td class='cu11'><img src='Image.php?uid=$user'><div>".user_fullname($user)."</div></td><td class='cu12'>";
      $str.="<a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=4','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Images/System/chat-minimize.png' onMouseOver='this.src=\"Images/System/chat-minimize-highlight.png\";' onMouseOut='this.src=\"Images/System/chat-minimize.png\";' >";
      $str.="<a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=2','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Images/System/chat-close-blue.png' onMouseOver='this.src=\"Images/System/chat-close-blue-highlight.png\";' onMouseOut='this.src=\"Images/System/chat-close-blue.png\";' >";
      $str.="</td></tr>";
      $str.="<tr><td colspan=2 class='cu21'></td></tr>";
      $str.="<tr><td colspan=2 class='cu31'><a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=7','',0);\">Clear Chat History</a></td></tr>";
      $str.="<tr><td colspan=2 class='cu41'><div id='$chatfile' onClick=\"ajax_post('Ajax.php?action=Chat-Refresh','user=$user','$chatfile',0);\"></div> </td></tr>";
      $str.="<tr><td colspan=2 class='cu51'><img src='Images/System/chat-input.png' /><input type='text' id='input_$chatfile' onkeypress=\"{ var key=event.keyCode || event.which; if(key==13&&this.value!=''){ ajax_post('Ajax.php?action=Chat-Command','user=$user&command=8&text='+this.value,'',0); this.value=''; } }\" /></td></tr>";
      $str.="<tr><td class='cu61'><div><a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=4','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\">$fullname</a></div></td><td class='cu62'>";
      $str.="<a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=2','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Images/System/chat-close-white.png' onMouseOver='this.src=\"Images/System/chat-close-white-highlight.png\";' onMouseOut='this.src=\"Images/System/chat-close-white.png\";' >";
      $str.="</td></tr>";
      $str.="</table>";
      $refresh.="ajax_post('Ajax.php?action=Chat-Refresh','user=$user','$chatfile',0); e = document.getElementById('$chatfile'); e.scrollTop = e.scrollHeight; ";
      }
    else {
      $str.="<table class='chatbox-user'>";
      $str.="<tr><td class='cu61'><div><a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=3','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\">$fullname</a></div></td><td class='cu62'>";
      $str.="<a onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=2','chat-position',0); ajax_post('Ajax.php?action=Chat-Command','user=$user&command=0','chat-position',0);\"><img src='Images/System/chat-close-white.png' onMouseOver='this.src=\"Images/System/chat-close-white-highlight.png\";' onMouseOut='this.src=\"Images/System/chat-close-white.png\";' >";
      $str.="</td></tr>";
      $str.="</table>";
      }
    $str.="</td>";
    }

  // $str.="<td> <table border=1><tr><td style='height:250px;'>250</td></tr></table> </td>";

  $str.="<td id='chat-index'>".chat_index($uid)."</td>";
  $refresh.="ajax_post('Ajax.php?action=Chat-Index','','chat-index',0);";
  //$refresh.=$xyz="chat_check('tab=".implode($tabs,",")."&max=".implode($max,",")."');";

  $str.="<input type='hidden' id='chat_tab_old' value='".implode($tabs,',')."' />";
  $str.="<input type='hidden' id='chat_max_old' value='".implode($max,',')."' />";
  $str.="<input style='position:fixed;top:-100px;' type='button' id='refresh_chatdata' onClick=\"$refresh window.setTimeout('(document.getElementById(\'refresh_chatdata\')).click();',5000); \" value='Refresh Data' />";
  $str.="</tr></table> $xyz";

  } // Chat Online

  else if($status=="Offline"){
    $str.="<table class='chatbox-index'>";
    $str.="<tr><td class='ci41' colspan=2 onClick=\"ajax_post('Ajax.php?action=Chat-Command','user=$user&command=5','chat-position',0); \"><a>Chat (Offline)</a></td></tr>";
    $str.="</table>";
    }

  return $str;
  }

// --------------------------------------------------------------


?>