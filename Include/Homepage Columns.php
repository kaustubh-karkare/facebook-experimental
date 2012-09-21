<?php

function homepage_display_left($self){

  $str.="<table class='homepage-column-left' border=0>";
  $str.="<tr><td class='hpl11'>".user_photo($self,50)."</td>";
  $str.="<td class='hpl12'><span>".user_fullname($self)."</span>";
  $str.="<br><a href='?edit=basicinfo'>Edit my profile</a> </td></tr>";

  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=newsfeed'; \"><img src='Images/System/hp-newsfeed.png' /> News Feed</td></tr>";
  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=messages'; \"><img src='Images/System/hp-messages.png' /> Messages</td></tr>";
  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=events'; \"><img src='Images/System/hp-events.png' /> Events</td></tr>";
  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=photos'; \"><img src='Images/System/hp-photos.png' /> Photos</td></tr>";
  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=friends'; \"><img src='Images/System/hp-friends.png' /> Friends</td></tr>";
  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=pages'; \"><img src='Images/System/hp-pages.png' /> Pages</td></tr>";
  $str.="<tr><td class='hpl21' colspan=2 onClick=\"window.location='?index=groups'; \"><img src='Images/System/hp-groups.png' /> Groups</td></tr>";

  $str.="<tr><td colspan=2></td></tr>";
  $str.="</table>";

  return $str;
  }










function homepage_display_center($self,$index){
  global $time;

  $str.="<table class='homepage-column-center' border=0>";
  $str.="<tr><td class='hpc11'>";
  if($index=="newsfeed")	$str.="<img src='Images/System/hp-newsfeed.png'> News Feed";
  if($index=="messages")	$str.="<img src='Images/System/hp-messages.png'> Messages";
  if($index=="events")		$str.="<img src='Images/System/hp-events.png'> Events";
  if($index=="oldevents")		$str.="<img src='Images/System/hp-events.png'> Events (Past)";
  if($index=="photos")		$str.="<img src='Images/System/hp-photos.png'> Photos";
  if($index=="friends")		$str.="<img src='Images/System/hp-friends.png'> Friends";
  if($index=="pages")		$str.="<img src='Images/System/hp-pages.png'> Pages";
  if($index=="groups")		$str.="<img src='Images/System/hp-groups.png'> Groups";
  if($index=="notifications")	$str.="<img src='Images/System/hp-notify.png'> Notifications";
  if($index=="about")		$str.="<img src='Images/System/hp-help.png'> About this Website";

  if($index=="newsfeed") $str.="</td><td class='hpc12'> <a title='This page is up to date as of ".fdate($time)."' href='?index=newsfeed'>Refresh</a> </td></tr>";
  else $str.="</td><td></td></tr>";
  $str.="</table>";

  return $str;
  }















function homepage_display_right($self){
  return "<div id='homepage-refresh-right'>".homepage_refresh_right($self)."</div>";
  }

function homepage_refresh_right($self){
  global $time;

  $str.="<table class='homepage-column-right' border=0>";

  // -----------------------------------------------------------------------

  $u1 = db_get_ids("Users");
  if(!is_array($u1)||$u1[0]=="") $u1=array();
  $bday="";
  foreach($u1 as $i){
    if(!friend_check($self,$i)) continue;
    if(date("m d",(int)db_get("Users",$i,"Birthday",""))==date("m d",(int)$time)) $bday.="<p><img src='Images/System/news-gift.png' /> ".user_fullname($i,1)."</p>";
    }
  if($bday!=""){
  $str.="<tr><td class='hpr11'>Birthdays Today</td></tr>";
  $str.="<tr><td class='hpr13'></td></tr>";
  $str.="<tr><td class='hpr12'>$bday</td></tr>";
  $str.="<tr><td class='hpr14'></td></tr>";
  }

  // -----------------------------------------------------------------------

  $e1 = db_get_ids("Events");
  if(!is_array($e1)||$e1[0]=="") $e1=array();
  $e2 = array(); // sort
  $e3 = array(); // future/past
  $e4 = array(); 
  $c=0;
  foreach($e1 as $i){ $c++;
    if( db_get("Events",$i,"Status","")!="Normal" ) continue;
    $creator = db_get("Events",$i,"UID","");
    if( $self!=$creator && !friend_check($self,$creator) && db_get("Events",$i,"Type","")!="Public" ) continue;
    $j = db_get("Events",$i,"Start Time","");
    eregi("([0-9]{1,2}):([0-9]{1,2}) ([apm]{2})",$j,$j);
    if($j[3]=="am") $j = $j[1]*60*60+$j[2]*60; else $j = 12*60*60+$j[1]*60*60+$j[2]*60; 
    $e2[] = array("EID"=>$i,"Date"=>db_get("Events",$i,"Start Date",""),"Time"=>$j );
    }
  for($i=0;$i<$c;$i++) for($j=0;$j<$c-$i-1;$j++){ $swap=0;
    if($e2[$j]["Date"]>$e2[$j+1]["Date"]) $swap=1;
    if($e2[$j]["Date"]==$e2[$j+1]["Date"] && $e2[$j]["Time"]>$e2[$j+1]["Time"]) $swap=1;
    if($swap){ $t = $e2[$j]; $e2[$j]=$e2[$j+1]; $e2[$j+1]=$t; }
    }
  foreach($e2 as $i=>$j) if($j["EID"]!=""){
    if($j["Date"]+$j["Time"]>=$time){ $e3[$i]=$j["EID"]; $e4[$i]=$j["Date"]+$j["Time"]; }
    }
  array_multisort($e4,$e3);
  if($old==1) $e3 = array_reverse($e3);

  $upcoming="";
  foreach($e3 as $k=>$i){
    if($k==5) break;
    if($time>$e4[$k] || $e4[$k]>$time+60*60*24*15) continue;
    $t1 = jdate(db_get("Events",$i,"Start Date",""));
    if($t1==jdate($time)) $t1="Today";
    $fromtotime = "Created by ".user_fullname(db_get("Events",$i,"UID",""),0)." ; ".jdate(db_get("Events",$i,"Start Date","")).", ".db_get("Events",$i,"Start Time","");
    if(db_get("Events",$i,"Display End","")=="yes") $fromtotime.=" to ".jdate(db_get("Events",$i,"End Date","")).", ".db_get("Events",$i,"End Time","");
    $upcoming.="<p><a href='?eid=$i' title='$fromtotime'>".db_get("Events",$i,"Name","")." ($t1)</a></p>";
    }
  if($upcoming!=""){
  $str.="<tr><td class='hpr11'><a href='?index=events'>Upcoming Events</a></td></tr>";
  $str.="<tr><td class='hpr13'></td></tr>";
  $str.="<tr><td class='hpr12'>$upcoming</td></tr>";
  $str.="<tr><td class='hpr14'></td></tr>";
  }


  // -----------------------------------------------------------------------

  $str.="<tr><td class='hpr11'><a href='?index=events'>Events</a></td></tr>";
  $str.="<tr><td class='hpr13'></td></tr>";
  $str.="<tr><td class='hpr12'>";
  $str.="<form action='Action.php?action=Event-Create' method='post' name='event_create'>";

  $defaulttext1 = "What are you planning?";
  $str.="<input title=\"$defaulttext1\" name='name' type='text' value='$defaulttext1' onClick=\"if(this.value=='$defaulttext1') this.value=''; (document.getElementById('event-expand')).style.height='85px'; this.style.color='#000000'; \" onBlur=\"if(this.value=='') this.value='$defaulttext1'; \" style='color:#444444'; />";
  if(1){

    $stime = date("i",$time)*60+date("s",$time);
    if(30*60>$stime) $t = $time+(30*60-$stime); 
    else $t = $time+(60*60-$stime); 

    $str.="<div id='event-expand'>";
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    $str.="<select name='syear'>"; for($i=2005;$i<=2025;$i++) if(date("Y",$t)!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $str.="<select name='smonth'>"; for($i=1;$i<=12;$i++) if(date("m",$t)!=$i) $str.="<option value='$i'>".$months[$i-1]."</option>"; else $str.="<option value='$i' selected='selected'>".$months[$i-1]."</option>"; $str.="</select>";
    $str.="<select name='sdate'>"; for($i=1;$i<=31;$i++) if(date("d",$t)!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";

    $stime = date("g:i a",$t);
    $str.="<select name='stime'>";
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($stime=="$j:00 am") $str.="<option selected='selected'>$j:00 am</option>"; else $str.="<option>$j:00 am</option>"; if($stime=="$j:30 am") $str.="<option selected='selected'>$j:30 am</option>"; else $str.="<option>$j:30 am</option>"; }
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($stime=="$j:00 pm") $str.="<option selected='selected'>$j:00 pm</option>"; else $str.="<option>$j:00 pm</option>"; if($stime=="$j:30 pm") $str.="<option selected='selected'>$j:30 pm</option>"; else $str.="<option>$j:30 pm</option>"; }
    $str.="</select>";

    $defaulttext2 = "Where?";
    $str.="<input title=\"$defaulttext\" name='location' type='text' value='$defaulttext2' onClick=\"if(this.value=='$defaulttext2') this.value='';\" onBlur=\"if(this.value=='') this.value='$defaulttext2'; \" />";
    //$defaulttext3 = "Who`s invited?";
    //$str.="<input title=\"$defaulttext\" name='invite' type='text' value='$defaulttext3' onClick=\"if(this.value=='$defaulttext3') this.value='';\" onBlur=\"if(this.value=='') this.value='$defaulttext3'; \" />";

    $str.="<input type='submit' class='submit' value='Create Event' onClick=\"e=document.event_create; if(e.name.value=='$defaulttext1')e.name.value=''; if(e.location.value=='$defaulttext2') e.location.value=''; if(e.invite.value=='$defaulttext3') e.invite.value=''; \" />";
    for($n=0;$n<27;$n++) $str.="&nbsp;";
    $str.="<a onClick=\"(document.getElementById('event-expand')).style.height='2px'; document.event_create.name.style.color='#444444'; \">Cancel</a>";
    $str.="</div>";
    }

  $str.="</form> </td></tr>";
  $str.="<tr><td class='hpr14'></td></tr>";

  // -----------------------------------------------------------------------

  $str.="<tr><td class='hpr11'><a href='?index=pages'>Pages</a></td></tr>";
  $str.="<tr><td class='hpr13'></td></tr>";
  $str.="<tr><td class='hpr12'>";
  $str.="<form action='Action.php?action=Page-Create' method='post' name='page_create' enctype='multipart/form-data' >";

  $defaulttext4 = "Enter Page Name";
  $str.="<input title=\"$defaulttext4\" name='name' type='text' value='$defaulttext4' onClick=\"if(this.value=='$defaulttext4') this.value=''; (document.getElementById('page-expand')).style.height='103px'; this.style.color='#000000'; \" onBlur=\"if(this.value=='') this.value='$defaulttext4'; \" style='color:#444444'; />";
  if(1){
    $str.="<div id='page-expand'>";
    $defaulttext5 = "Select image for Page ...";
    $str.="Image : <input title='$defaulttext5' name='pageimage' type='file' style='width:200px;' />";
    $defaulttext6 = "Write something about your Page ...";
    $str.="<textarea name='about' onClick=\"if(this.value=='$defaulttext6') this.value='';\" onBlur=\"if(this.value=='') this.value='$defaulttext6'; \" >$defaulttext6</textarea>";

    $str.="<input type='submit' class='submit' value='Create Page' onClick=\"e=document.page_create; if(e.name.value=='$defaulttext4') e.name.value=''; if(e.about.value=='$defaulttext6') e.about.value=''; \" />";
    for($n=0;$n<27;$n++) $str.="&nbsp;";
    $str.="<a onClick=\"(document.getElementById('page-expand')).style.height='2px'; document.page_create.name.style.color='#444444'; \">Cancel</a>";

    $str.="</div>";
    }

  $str.="</form> </td></tr>";
  $str.="<tr><td class='hpr14'></td></tr>";

  // -----------------------------------------------------------------------

  $str.="<tr><td class='hpr11'><a href='?index=groups'>Groups</a></td></tr>";
  $str.="<tr><td class='hpr13'></td></tr>";
  $str.="<tr><td class='hpr12'>";
  $str.="<form action='Action.php?action=Group-Create' method='post' name='group_create' enctype='multipart/form-data' >";

  $defaulttext7 = "Enter Group Name";
  $str.="<input title=\"$defaulttext7\" name='name' type='text' value='$defaulttext7' onClick=\"if(this.value=='$defaulttext7') this.value=''; (document.getElementById('group-expand')).style.height='128px'; this.style.color='#000000'; \" onBlur=\"if(this.value=='') this.value='$defaulttext7'; \" style='color:#444444'; />";
  if(1){
    $str.="<div id='group-expand'>";
    $defaulttext8 = "Select image for Group ...";
    $str.="Image : <input title='$defaulttext8' name='groupimage' type='file' style='width:200px;' />";
    $defaulttext9 = "Write something about your Group ...";
    $str.="<textarea name='about' onClick=\"if(this.value=='$defaulttext9') this.value='';\" onBlur=\"if(this.value=='') this.value='$defaulttext9'; \" >$defaulttext9</textarea>";
    $defaulttext10 = "Where is your Group located?";
    $str.="<input name='location' type='text' value='$defaulttext10' onClick=\"if(this.value=='$defaulttext10') this.value='';\" onBlur=\"if(this.value=='') this.value='$defaulttext10'; \" />";

    $str.="<input type='submit' class='submit' value='Create Group' onClick=\"e=document.group_create; if(e.name.value=='$defaulttext7') e.name.value=''; if(e.about.value=='$defaulttext9') e.about.value=''; if(e.location.value=='$defaulttext10') e.location.value=''; \" />";
    for($n=0;$n<27;$n++) $str.="&nbsp;";
    $str.="<a onClick=\"(document.getElementById('group-expand')).style.height='2px'; document.group_create.name.style.color='#444444'; \">Cancel</a>";

    $str.="</div>";
    }

  $str.="</form> </td></tr>";
  $str.="<tr><td class='hpr14'></td></tr>";

  // -----------------------------------------------------------------------

  $u = db_get_ids("Users"); $p=array();
  foreach($u as $i=>$j)
    if(friend_check($j,$self) || friend_check_request($self,$j) || friend_check_request($j,$self) || $self==$j) unset($u[$i]);
    else {
      $mf = friend_list_mutual($j,$self);
      if(is_array($mf)) $mf = count($mf); else $mf = 0;
      if($mf==0) $mf = "No mutual friends";
      if($mf==1) $mf = "1 mutual friend";
      if($mf>1)  $mf = "$mf mutual friends";

      $q = "<tr><td><a href='?uid=$j'><img src='Image.php?uid=$j' width=50 class='pic' /></a></td>";
      $q.= "<td class='info'>".user_fullname($j)."<br>$mf<br>";
      $q.= "<span><img src='Images/System/edit-relations.png'><a onClick=\"ajax_post('Ajax.php?action=Friend-Request','uid=$j&refresh=homepage','homepage-refresh-right',0);\">Add as friend</span></td></tr>";
      $p[]="$q";
      }
  shuffle($p);
  $p = $p[0].$p[1];

  if($p!=""){
    $str.="<tr><td class='hpr11'>People you may know</td></tr>";
    $str.="<tr><td class='hpr13'></td></tr>";
    $str.="<tr><td class='hpr12'> <table border=0>$p</table> </td></tr>";
    $str.="<tr><td class='hpr14'></td></tr>";
    }

  // -----------------------------------------------------------------------

  $r1 = db_get("Friends",$self,"Requests",0);
  if(is_array($r1) && $r1[0]!="" && count($r1)>0){ $r1="<img src='Images/System/edit-relations.png' /> "; if(count($r1)>1) $r1.=count($r1)." friend requests"; else $r1.="1 friend request"; } else $r1=-1;

  if($r1!=-1) $str.="<tr><td class='hpr11'>Requests</td></tr>";
  if($r1!=-1) $str.="<tr><td class='hpr13'></td></tr>";
  if($r1!=-1) $str.="<tr><td class='hpr12'><a href='?index=friends'>$r1</a></td></tr>";
  if($r1!=-1) $str.="<tr><td class='hpr14'></td></tr>";

  // -----------------------------------------------------------------------

  $p = db_get("Friends",$self,"Pokes",array());
  if(is_array($p) && $p[0]!="" && count($p)>0){
    $str.="<tr><td class='hpr11'>Pokes</td></tr>";
    $str.="<tr><td class='hpr13'></td></tr>";
    foreach($p as $i){
      $str.="<tr><td class='hpr12'>".user_fullname($i).SEP." <a onClick=\"ajax_post('Ajax.php?action=Poke-Accept','uid=$i&refresh=homepage','homepage-refresh-right',0);\">Accept</a> ";
      $str.=SEP." <a onClick=\"ajax_post('Ajax.php?action=Poke-Accept','uid=$i&refresh=homepage','homepage-refresh-right',0); ajax_post('Ajax.php?action=Poke-Perform','uid=$i&refresh=homepage','homepage-refresh-right',0);\">Return</a> </td></tr>";
      }
    $str.="<tr><td class='hpr14'></td></tr>";
    }

  // -----------------------------------------------------------------------

  $p = db_get("Friends",$self,"Punches",array());
  if(is_array($p) && $p[0]!="" && count($p)>0){
    $str.="<tr><td class='hpr11'>Punches</td></tr>";
    $str.="<tr><td class='hpr13'></td></tr>";
    foreach($p as $i){
      $str.="<tr><td class='hpr12'>".user_fullname($i).SEP." <a onClick=\"ajax_post('Ajax.php?action=Punch-Accept','uid=$i&refresh=homepage','homepage-refresh-right',0);\">Accept</a> ";
      $str.=SEP." <a onClick=\"ajax_post('Ajax.php?action=Punch-Accept','uid=$i&refresh=homepage','homepage-refresh-right',0); ajax_post('Ajax.php?action=Punch-Perform','uid=$i&refresh=homepage','homepage-refresh-right',0);\">Return</a> </td></tr>";
      }
    $str.="<tr><td class='hpr14'></td></tr>";
    }

  // -----------------------------------------------------------------------
 
/*
  $str.="<tr><td class='hpr11'>Notice</td></tr>";
  $str.="<tr><td class='hpr13'></td></tr>";
  $str.="<tr><td class='hpr12'>Testing ...</td></tr>";
  $str.="<tr><td class='hpr14'></td></tr>";
*/

 // -----------------------------------------------------------------------

  $str.="<tr><td></td></tr></table>";

  return $str;
  }

?>