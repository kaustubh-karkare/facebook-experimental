<?php

function listout($array){
  if(!is_array($array)) $array = array($array);
  foreach($array as $i=>$j){
    $str.="$i=>";
    if(!is_array($j)) $str.=$j;
    else { $t = array(); foreach($j as $k) $t[]=$k; $str.=implode($t,","); }
    $str.="<br>";
    }
  return $str;
  }

function mysql_display($table){
  $result = mysql_query("SELECT * FROM $table");
  $str.="<table border=1 width=100%>";
  $count = mysql_num_rows($result);
  $str.="<tr><th colspan=100>MySQL Database : $table Table</th></tr>";
  if($count==0) $str.="<tr><th colspan=100>Table Empty</th></tr>";
  for($i=0;$i<$count;$i++){
    if($i==0){
      $str.="<tr>";
      $row = mysql_fetch_assoc($result);
      foreach($row as $j=>$k) $str.="<th>$j</th>";
      $str.="</tr>";
      }
    $str.="<tr>";
    if($i)$row = mysql_fetch_assoc($result);
    foreach($row as $j) $str.="<td>$j</td>";
    $str.="</tr>";
    }
  $str.="</table>";
  return $str;
  }

function system_refresh(){

  db_delete("Users");
  db_create("Users","UID,Status,First Name,Last Name,Sex,Birthday,EMail,Password,Status Message,Current Location,Last Active,IP Address,IP Addresses Used,Browser,Browsers Used,OS,OSs Used,Session,");
  db_load("Users");
  user_add(array("Sex"=>"Male","First Name"=>"Kaustubh","Last Name"=>"Karkare","EMail"=>"kaustubh.karkare@gmail.com","Password"=>"pass","Current Location"=>"Ranchi, India"));
  db_save("Users");

  db_delete("Profiles");
  db_create("Profiles","UID,About,Relationship Status,Political Views,Religious Views,Bio,Favorite Quotations,Interests,Music,Books,Movies,Television,Games,High School,University,Employer,Phone,Website,Address,Hometown,Current Location,DBU,DBP,DBF,DBA"); // DB UserInfo,ProfileInfo,NoOfFriend,NoOFMutual,AlwaysDisplay
  db_load("Profiles");
  db_addrow("Profiles",array("UID"=>1,"Interests"=>"Computer Programming, Compputer Gaming, Reading Books, Writing Stories","Hometown"=>"Nagpur, Maharashtra, India","University"=>"Birla Institute Of Technology, Mesra","Website"=>"http://192.168.154.51/","DBU"=>array("Birthday","EMail"),"DBP"=>array("University"),"DBA"=>array() ));
  db_save("Profiles");

  db_delete("Friends"); db_create("Friends","UID,Accepted,Requests,Pokes,Punches"); db_load("Friends"); db_save("Friends");
  db_delete("Chat"); db_create("Chat","UID,Status,Tabs,Maximized,Lists"); db_load("Chat"); db_addrow("Chat",array("UID"=>1)); db_save("Chat");
  db_delete("News"); db_create("News","FID,UID,Status,Type,Location,Time,Data,Like,Dislike"); db_load("News"); db_save("News");
  db_delete("Messages"); db_create("Messages","MID,Status,UID,Subject,Between,Update,Read"); db_load("Messages"); db_save("Messages");
  db_delete("Albums"); db_create("Albums","AID,UID,Status,Name,Cover,Photos,Updated,Published,Sharers,Caption,Like,Dislike"); db_load("Albums"); db_save("Albums");
  db_delete("Photos"); db_create("Photos","PID,AID,UID,Status,Type,Published,Sharers,Caption,Like,Dislike,Tags"); db_load("Photos"); db_save("Photos");
  
  db_delete("Events"); db_create("Events","EID,UID,Status,Name,Start Date,End Date,Start Time,End Time,Location,Info,Type,Display End,Yes,No,Maybe"); db_load("Events"); db_save("Events");
  db_delete("Pages"); db_create("Pages","TID,UID,Status,Name,About,Info,Like,Dislike"); db_load("Pages"); db_save("Pages");
  db_delete("Groups"); db_create("Groups","GID,UID,Status,Name,Location,About,Info,Members"); db_load("Pages"); db_save("Pages");
  db_delete("Ads"); db_create("Ads","BID,UID,Status,Head,Info,More,Like,Dislike"); db_load("Ads"); db_save("Ads");

  foreach(folder_get("Database/Comments/") as $file) if($file!="Index.php") file_delete("Database/Comments/$file");
  foreach(folder_get("Database/Chat/") as $file) if($file!="Index.php") file_delete("Database/Chat/$file");
  foreach(folder_get("Database/Messages/") as $file) if($file!="Index.php") file_delete("Database/Messages/$file");
  foreach(folder_get("Database/News/") as $file) if($file!="Index.php") file_delete("Database/News/$file");
  foreach(folder_get("Database/Notifications/") as $file) if($file!="Index.php") file_delete("Database/Notifications/$file");
  
  foreach(folder_get("Images/Photos/") as $file) file_delete("Images/Photos/$file");
  foreach(folder_get("Images/Page/") as $file) file_delete("Images/Page/$file");
  foreach(folder_get("Images/Event/") as $file) file_delete("Images/Event/$file");
  foreach(folder_get("Images/Profile/") as $file) file_delete("Images/Profile/$file");
  foreach(folder_get("Images/Group/") as $file) file_delete("Images/Group/$file");
  
  
  }

function fdate($t=-1){
  global $time;
  if($t==-1)$t=$time;
  return date("d F Y, l, H:i:s",(int)$t);
  }

function bdate($t){
  return date("d F Y",(int)$t);
  }

function adate($t){
  global $time;
  $dif = $time-(int)$t;
  if($dif<20) $str="A few seconds ago";
  else if($dif<60) $str="Less than a minute ago";
  else if($dif<60*60){ $str="About ".round($dif/60)." minute(s) ago"; }
  else if($dif<48*60*60 && strcmp(date("d",$t),date("d",$time)) ) $str="Yesterday at ".date("g:i A",$t);
  else if($dif<24*60*60) $str="About ".round($dif/(24*60))." hours ago";
  else return "<span>".date("d F Y, g:i A",(int)$t)."</span>";
  // else return date("d F Y, l, g:i A",(int)$t);
  return "<span title='".fdate($t)."'>$str</span>";
  }

function jdate($t){
  return date("d F Y",(int)$t);
  }

function text_screen($str){
  $str=stripslashes($str);
  $str=eregi_replace('"', "&#34;",$str); // 34
  $str=eregi_replace("'", "&#39;",$str); // 39
  $str=eregi_replace("<", "&#60;",$str);
  $str=eregi_replace(">", "&#62;",$str);
  $str=eregi_replace(NL,"<br>",$str);
  return $str;
  }

?>