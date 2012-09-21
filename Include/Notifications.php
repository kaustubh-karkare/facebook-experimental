<?php

function notify_write($uid,$u1,$u2,$u3,$u4,$type,$link,$time){
  $dbid = "Notifications/$uid";
  if(!db_exists($dbid)) db_create($dbid,"Time,U1,U2,U3,U4,Type,Link,Read");
  db_load($dbid);
  $t = $time; $x = db_get_ids($dbid); while(in_array($t,$x)) $t++;
  db_addrow($dbid,array("Time"=>$t,"U1"=>$u1,"U2"=>$u2,"U3"=>$u3,"U4"=>$u4,"Type"=>$type,"Link"=>$link,"Read"=>0));
  db_save($dbid);
  db_unload($dbid);
  }





function notify_read($user,$t){
  $dbid = "Notifications/$user";
  if(!db_exists($dbid)) db_create($dbid,"Time,U1,U2,U3,U4,Type,Link,Read");
  db_load($dbid);
  $data=db_get_ids($dbid);
  $str="";
  $latest=array();
  $older=array();
  if(is_array($data)) foreach($data as $row){
    if(count($data)>100){ db_delrow($dbid,$row); continue; }
    $type = db_get($dbid,$row,"Type",-1);
    $u1 = db_get($dbid,$row,"U1",-1);
    $u2 = db_get($dbid,$row,"U2",-1);
    $u3 = db_get($dbid,$row,"U3",-1);
    $u4 = db_get($dbid,$row,"U4",-1);
    $link = db_get($dbid,$row,"Link",-1);
    $read = db_get($dbid,$row,"Read",0);
    $time = db_get($dbid,$row,"Time",0);
    $flag=1;

    $per0=user_fullname($user,0);	
    $per1=user_fullname($u1,0);
    $per2=user_fullname($u2,0);
    $per3=user_fullname($u3,0);
    $per4=user_fullname($u4,0);

    if($per2==$per0) $per2="your";
    else { if($per1==$per2) $per2=user_hisher($u1); else $per2.="'s"; }
    if($per3==$per0) $per3="your";
    else { if($per1==$per3) $per3=user_hisher($u1); else $per3.="'s"; }
    if($per4==$per0) $per4="your";
    else { if($per1==$per4) $per4=user_hisher($u1); else $per4.="'s"; }

    if($u2==1){ $special = "this website's About Page."; }

         if($per2==$per3 && $type=="News-Comment")	$message = $per1." commented on ".$per2." status.";
    else if($per2==$per3 && $type=="News-Like")		$message = $per1." likes ".$per2." status.";
    else if($per2==$per3 && $type=="News-Dislike")	$message = $per1." dislikes ".$per2." status.";
    else if($per2==$per3 && $type=="Comment-Like")	$message = $per1." likes ".$per4." comment on ".$per2." status.";
    else if($per2==$per3 && $type=="Comment-Dislike")	$message = $per1." likes ".$per4." comment on ".$per2." status.";
    else if($per2!=$per3 &&$type=="News-Comment")	$message = $per1." commented on ".$per2." post on ".$per3." wall.";
    else if($per2!=$per3 &&$type=="Comment-Like")	$message = $per1." likes ".$per4." comment on ".$per2." post on ".$per3." wall.";
    else if($per2!=$per3 &&$type=="Comment-Dislike")	$message = $per1." dislikes ".$per4." comment on ".$per2." post on ".$per3." wall.";
    else if($per2!=$per3 &&$type=="News-Like")		$message = $per1." likes ".$per3." post on ".$per2." wall.";
    else if($per2!=$per3 &&$type=="News-Dislike")	$message = $per1." dislikes ".$per2." post on ".$per3." wall.";
    else if($type=="Wall-Post")				$message = $per1." has written something on your wall.";
    else if($type=="Photos-Comment")			$message = $per1." commented on ".$per2." photo.";
    else if($type=="Photos-Like")			$message = $per1." likes ".$per2." photo.";
    else if($type=="Photos-Dislike")			$message = $per1." dislikes ".$per2." photo.";
    else if($type=="Albums-Comment")			$message = $per1." commented on ".$per2." album.";
    else if($type=="Albums-Like")			$message = $per1." likes ".$per2." album.";
    else if($type=="Albums-Dislike")			$message = $per1." dislikes ".$per2." album.";
    else if($type=="Image-Tag")				$message = $per1." tagged a photo of you.";
    else if($type=="Special-Comment")			$message = $per1." commented on ".$special;
    else { $message = "<p>".$type." : Unknown Type ($u1:$u2:$u3:$u4:$link)</p>"; $flag=0; }

    if($flag){
      if($t==0) $message = "<tr><td>". ((!$read)?"<td class='new1'>":"<td>") ."<a class='link' href='$link'>".user_photo($u1,40,0)."</a></td>". ((!$read)?"<td class='new2'>":"<td>")."<a class='link' href='$link'>".$message."</a><br><n>".adate($time)."</n></td></tr>";
      if($t==1) $message = "<tr><td class='pic'><a href='$link'>".user_photo($u1,40,0)."</a></td><td><a href='$link'>$message</a><br><n>".adate($time)."</n></td></tr>";
      }
    if($t==0){  if($read==0){ $latest[] = $message; db_set($dbid,$row,"Read",1); } else $older[]=$message;  }
    if($t==1) $older[]=$message;
    }
  db_save($dbid);
  db_unload($dbid);
  $latest = array_reverse($latest); 
  $older = array_reverse($older); 
		
  if(count($latest)>0){
    $str=implode($latest,"");
    for($i=0;$i<5-count($latest);$i++)$str.=$older[$i];
    }
  else if($t==0) { for($i=0;$i<5;$i++) $str.=$older[$i]; }
  else if($t==1) { $str.=implode($older); }

  if($t==0){
    if($str=="") $str="<tr><td colspan=2>No notifications as of now.</td></tr>";
    return "<table>".$str."</table>";
    }
  else {
    if($str=="") $str="<tr><td colspan=2>No notifications as of now.</td></tr>";
    $str2.="<table class='notify-container' border=0>";
    $str2.="<tr><th colspan=2><a href='Action.php?action=Notifications-Clear'>Clear Notifications</a></th>".$str."</table>";
    return $str2;
    }
  }

function notify_clear($uid){
  db_delete("Notifications/$uid");
  }


















function display_notice(){
  if($_SESSION["notice"]=="") return;
  $n = explode(NL,$_SESSION["notice"]);
  $_SESSION["notice"]="";
  if(is_array($n)) foreach($n as $i) if($i!="") $notice.="<tr><td>$i</td></tr>";
  $link = popup_text("actionstatus","Server","<table>$notice</table>","","","",$output);
  return $output."<script>".$link."</script>";
  }

?>