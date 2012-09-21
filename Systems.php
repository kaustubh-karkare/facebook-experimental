<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

/*
Error 0 : Success
Error -1 : File/Data doesnt/already exist
Error -2 : Not loggd in
Error -3 : Memory Overload
Error -4 : File Upload Error
*/

define(NL,"
");
define(SEP," | ");//" · ");

session_start();
$session = $_COOKIE["PHPSESSID"];
$popupstream="";

$inc = "Include";
$d=opendir("$inc/");
while($e=readdir()){
  if(is_dir("$inc/$e"))continue;
  if(!eregi(".php",$e))continue;
  include("$inc/$e");
  }
closedir($d);

$time = time()+5.5*60*60;

$serverip = $_SERVER['SERVER_ADDR'];
$clientip = $_SERVER['REMOTE_ADDR'];
$close_drop_lists="";

//db_create("Events","EID,UID,Status,Name,Start Date,End Date,Start Time,End Time,Location,Info,Type,Display End,Yes,No,Maybe");
//db_create("Pages","TID,UID,Status,Name,About,Info,Like,Dislike");
//db_create("Groups","GID,UID,Status,Name,Location,About,Info,Members");
//db_create("Ads","BID,UID,Status,Head,Info,More,Like,Dislike");

// system_refresh();

function db_load_all(){
  db_load("Users");
  db_load("Friends");
  db_load("Profiles");
  db_load("Chat");
  db_load("Albums");
  db_load("Photos");
  db_load("News");
  db_load("Events");
  db_load("Pages");
  db_load("Groups");
  db_load("Ads");
  db_load("Messages");
  user_authenticate();
  data_authenticate();
  }

function db_save_all(){
  db_save("Users");
  db_save("Friends");
  db_save("Profiles");
  db_save("Chat");
  db_save("Albums");
  db_save("Photos");
  db_save("News");
  db_save("Events");
  db_save("Pages");
  db_save("Groups");
  db_save("Ads");
  db_save("Messages");
  }

?>