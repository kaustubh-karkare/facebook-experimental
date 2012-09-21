<?php

/*
The following is the Field List of the Users Database
UID,Status,
*/

function user_next(){
  return db_num_rows("Users")+1;
  }

function user_add($assocarray){
  $uid = user_next();
  $a["UID"]=$uid;
  foreach($assocarray as $i=>$j) $a[$i]=$j;
  db_addrow("Users",$a);
  db_addrow("Friends",array("UID"=>$uid));
  db_addrow("Profiles",array("UID"=>$uid));
  }

function user_del($uid){
  db_set("Users",$uid,"Status","Deleted");
  }

function user_fullname($uid,$a=1){
  if($a)$fn ="<a href='?uid=$uid'>";
  $fn.=db_get("Users",$uid,"First Name","")." ";
  $fn.=db_get("Users",$uid,"Last Name","");
  if($a)$fn.="</a>";
  return $fn;
  }

function user_photo($uid,$width,$a=1){
  if($a)$p ="<a href='?uid=$uid' title=\"".eregi_replace('"','',user_fullname($uid,0))."\"> ";
  $p.="<img class='photo' src='Image.php?uid=$uid' width='$width' />";
  if($a)$p.="</a>";
  return $p;
  }

function user_online(){
  global $session;
  $a = db_search("Users","UID","Session",$session);
  return ($a!=-1)?$a:"";
  }

function user_hisher($uid){
  if(db_get("Users",$uid,"Sex","Male")=="Male") return "his";
  else return "her";
  }

function user_statusmessage($uid){
  $sm = db_get("Users",$uid,"Status Message","");
  $str = "<div id='statusmessage' style=\" margin-top:20px;\"><h2>".user_fullname($uid)." $sm";
  if($uid==user_online()&&$sm!="") $str.=" <a onClick=\"ajax_post('Ajax.php?action=Status-Message-Clear','','statusmessage',0);\" style='font-size:10px;font-weight:normal;color:blue;'>(clear)</a>";
  $str.="</h2></div>";
  return $str;
  }

function user_authenticate(){
  global $time;
  $self = user_online();
  $uids = db_get_ids("Users");
  if(is_array($uids)) foreach($uids as $uid){
    $lastactive = db_get("Users",$uid,"Last Active",0);
    if($lastactive+300>$time) { if($uid==$self) db_set("Users",$uid,"Last Active",$time); }
    else { db_set("Users",$uid,"Session",""); db_set("Users",$uid,"IP Address",""); }
    if(db_get("Chat",$uid,"Status","")=="") db_set("Chat",$uid,"Status","Online");
    }
  }

function data_authenticate(){
  $list1 = db_get_ids("Users");
  $list2 = db_get_ids("Friends");
  $list3 = db_get_ids("Profiles");
  $list4 = db_get_ids("Chat");
  $list12 = array_diff($list1,$list2);
  if(is_array($list12)) foreach($list12 as $user) db_addrow("Friends",array("UID"=>$user));
  $list13 = array_diff($list1,$list3);
  if(is_array($list13)) foreach($list13 as $user) db_addrow("Profiles",array("UID"=>$user));
  $list14 = array_diff($list1,$list4);
  if(is_array($list14)) foreach($list14 as $user) db_addrow("Chat",array("UID"=>$user));
  }


?>