<?php

// db_create("Messages","MID,Status,UID,Subject,Between,Update,Read");
// db_create("Messages/$mid","Time,User,Text,Like,Dislike,Read");

function message_ajax_target($self,$id,$query){
  $query = explode(",",$query);
  if(!is_array($query)) $query = array($query);
  $uids = db_get_ids("Users");
  $res = array();
  $xyz = array();
  foreach($uids as $uid){
    $fname = db_get("Users",$uid,"First Name","");
    $lname = db_get("Users",$uid,"Last Name","");
    foreach($query as $q){
      $q=trim($q);
      if($q=="" || eregi("^ +$",$q)) continue;
      $q = eregi_replace(" +"," ",$q);
      $q = explode(" ",$q);
      $y=1;
      foreach($q as $x)
        if(!eregi($x,$fname) && !eregi($x, $lname) && !eregi($x,$fname." ".$lname)) $y=0;
      if($y==1) $res[]=$uid;
      }
    }
  $res = array_unique($res);
  $res = array_diff($res,array($self));
  $str="";
  foreach($res as $r) $str.="<p><input type='checkbox' style='width:13px;' id='check_mesname_$r' checked='checked' onClick=\"if(this.value=='off') this.value='on'; else this.value='off';\"> ".user_fullname($r)."</p>";
  if($str==""){
    $str.="<span id='message-$id-nosuggest-0' onClick=\"this.style.display='none'; (document.getElementById('message-$id-nosuggest-1')).style.display='block'; \">No Suggestions : Click here to view Help Text</span>";
    $str.="<span id='message-$id-nosuggest-1' style=\"display:none;\" onClick=\"this.style.display='none'; (document.getElementById('message-$id-nosuggest-0')).style.display='block'; \">Please start typing the names of the intended recipients in the field above. Once suggestions start appearing, ensure that the name of the person to whom you want to send the message to has appeared, and then move on to the next name after a comma. The message will only be sent to the people whose names have appeared here and are still ticked by you.</span>";
    }
  return $str;
  }














function popup_message($self,$id,$to,&$output){
  $text.="<table>";
  $div_mesname = message_ajax_target($self,$id,$to);
  $text.="<tr><th>To :</th><td><input class='text' value='$to' type='text' onKeyUp=\"ajax_post('Ajax.php?action=Message-Names','id=".$id."&query='+this.value,'div_mesname_".$id."',0);\" >";
  $text.="<input type='hidden' id='message-to-".$id."' name='to' value='###' /><br><div class='div_mesname' id='div_mesname_".$id."'>$div_mesname</div></td></tr>";
  $text.="<tr><th>Subject :</th><td><input type='text' class='text' name='subject'></td></tr>";
  $text.="<tr><th>Message : </th><td><textarea name='message'></textarea></td></tr>";
  $text.="</table>";
  $onclick=" between=''; for(i=1;i<=".(count(db_get_ids("Users"))+10).";i++) if(document.getElementById('check_mesname_'+i) && (document.getElementById('check_mesname_'+i)).value=='on') between+=i+','; (document.getElementById('message-to-".$id."')).value=between; ";
  $link = popup_text('message-'.$id,"Send a Message",$text,"Send","Action.php?action=Message-Send",$onclick,$output);
  return $link;
  }












function message_create($self,$between,$subject,$text){
  global $time;

  if($between==""||$text=="") return;

  $mid = count(db_get_ids("Messages"))+1;
  $between = explode(",",$between);
  if(!is_array($between) || $between[0]=="") $between = array();
  $between2 = array($self);
  foreach($between as $i) if($i!="") $between2[] = $i;
  $between2 = array_unique($between2);

  if(db_exists("Messages/$mid")){ }
  else db_create("Messages/$mid","Time,UID,Text");

  $a["MID"] = $mid;
  $a["Status"] = "Normal";
  $a["UID"] = $self;
  $a["Subject"] = text_screen($subject);
  $a["Between"] = $between2;
  $a["Update"] = $time;
  $a["Read"] = array($self);
  db_addrow("Messages",$a);

  global $time;
  $b["Time"] = $time;
  $b["UID"] = $self;
  $b["Text"] = text_screen($text);
  db_load("Messages/$mid");
  db_addrow("Messages/$mid",$b);
  db_save("Messages/$mid");

  $_SESSION["notice"].="Your message has been sent.".NL;
  }








function message_unread($self){
  foreach($_POST as $key=>$value){
    if($_POST["action"]=='delete' && eregi("^mark([0-9]{1,5})$",$key,$x) && $self==db_get("Messages",$x[1],"UID","") ) db_set("Messages",$x[1],"Status","Deleted"); 
    if($_POST["action"]=='unread' && eregi("^mark([0-9]{1,5})$",$key,$x) && in_array($self, db_get("Messages",$x[1],"Between","") ) ) db_array_del("Messages",$x[1],"Read",$self);
    }
  }









function message_reply($self,$mid,$text){
  global $time;
  if($text=="") return;

  $b = db_get("Messages",$mid,"Between",array());
  $status = db_get("Messages",$mid,"Status","");
  if(!is_array($b) || $b[0]=="") $b=array();

  if(!in_array($self,$b) || $status!="Normal") return;

  $a["Time"] = $time;
  $a["UID"] = $self;
  $a["Text"] = text_screen($text);

  db_load("Messages/$mid");
  db_addrow("Messages/$mid",$a);
  db_save("Messages/$mid");

  db_set("Messages",$mid,"Read",array($self));
  db_set("Messages",$mid,"Update",$time);
  }














function message_delete($self,$mid){
  $uid = db_get("Messages",$mid,"UID","");
  if($uid==$self) db_set("Messages",$mid,"Status","Deleted");
  else $_SESSION["notice"]="You cannot delete this Message.".NL;
  }













function message_ajax_new($self){
  $m = db_get_ids("Messages");
  if(!is_array($m) || $m[0]=="") $m=array();

  $str="<table>";
  $old = array();
  $new = array();
  foreach($m as $i){
    if(db_get("Messages",$i,"Status","")!="Normal") continue;
    $b = db_get("Messages",$i,"Between","");
    if(!is_array($b) || $b[0]=="") $b=array();
    if(!in_array($self,$b)) continue;
    $subject = db_get("Messages",$i,"Subject","");
    if($subject=="") $subject="(no subject)";

    $read = db_get("Messages",$i,"Read",array());
    if(!is_array($read) || $read[0]=="") $read=array();
    if(in_array($self,$read)) $read=1; else $read=0;

    db_load("Messages/$i");
    $j = db_get_ids("Messages/$i");
    if(!is_array($j) || $j[0]=="") $j=array(); else $j = array_reverse($j);
    foreach($j as $k){
      $uid = db_get("Messages/$i",$k,"UID","");
      $text = db_get("Messages/$i",$k,"Text","");
      break;
      }
    db_unload("Messages/$i");

    $str2="";
    $str2.="<tr><td class='".((!$read)?"new1":"old1")."' onClick=\"window.location='?mid=$i';\" >";
    $str2.=user_photo($uid,40,0)."</td>";
    $str2.="<td ".((!$read)?"class='new2'":"")." onClick=\"window.location='?mid=$i';\" >";
    // $str2.="Subject : $subject <br>";
    $str2.="<a>".user_fullname($uid,0)."</a> <span class='type1'>$text</span>";
    $str2.="<br><span class='type2' >Between ".comment_namelist($self,$b,"","").".</span>";
    $str2.="</a></td></tr>";

    if($read) $old[] = $str2;
    else $new[] = $str2;
    $none=0;
    }

  $j=0;
  if(count($new)){ $str.=implode($new); $j++; }
  for($i=0;$i-$j<5;$i++) $str.=$old[$i];

  if(count($old)==0&&count($new)==0)
    $str.="<tr><td colspan=2>You have recieved no messages.</td></tr>";
  $str.="</table>";

  return $str;
  }













function message_display($self,$mid){

  $status = db_get("Messages",$mid,"Status","");
  $between = db_get("Messages",$mid,"Between","");
  if(!is_array($between) || $between[0]=="") $between=array();

  if($status!="Normal" || !in_array($self,$between)) $str.="<script>window.location='?index=messages';</script>";

  $read = db_get("Messages",$mid,"Read","");
  if(!is_array($read) || $read[0]=="") $read=array();
  db_array_add("Messages",$mid,"Read",$self);

  $subject = db_get("Messages",$mid,"Subject","");
  if($subject=="") $subject="(no subject)";


  $between = db_get("Messages",$mid,"Between","");
  if(!is_array($between) || $between[0]=="") $between=array();
  $between = array_diff($between,array($self));
  shuffle($between);
  $last = $between[count($between)-1];
  if(count($between)>2){ 
    $between = array_diff($between,array($last));
    foreach($between as $i=>$j) $between[$i]=user_fullname($j,1);
    $between = implode($between,", ");
    $between = "<span>Between <a href='?uid=$self'>You</a>, $between and ".user_fullname($last,1).".</span>";
    }
  else $between = "<span>Between <a href='?uid=$self'>You</a> and ".user_fullname($last,1).".</span>";

  $str.="<table class='message-container' border=0>";
  $str.="<tr><th colspan=3><img src='Images/System/hp-messages.png' > $subject</th></tr>";
  $str.="<tr><td class='options' colspan=3>";
    $str.="<input type='button' value='Back To Messages' onClick=\"window.location='?index=messages';\" /> ";
    // $str.="<input type='button' value='Mark as Unread' /> ";
    $str.="<input type='button' value='Delete' onClick='document.messagedelete.submit();' /> ";
    $str.="</td></tr>";
  $str.="<form name='messagedelete' action='Action.php?action=Message-Delete' method='post'> <input type='hidden' name='mid' value='$mid' /> </form>";
  $str.="<tr><td class='between' colspan=3>$between</td></tr>";

  db_load("Messages/$mid");
  $m = db_get_ids("Messages/$mid"); if(!is_array($m)||$m[0]=="") $m=array();
  foreach($m as $i){
    $str.="<tr><td class='pic'>".user_photo(db_get("Messages/$mid",$i,"UID",""),40,1)."</td>";
    $str.="<td class='text' colspan=2>".user_fullname(db_get("Messages/$mid",$i,"UID",""),1)." ";
    $str.="<span>".adate(db_get("Messages/$mid",$i,"Time",""))."</span><br>";
    $str.=db_get("Messages/$mid",$i,"Text","")."</td></tr>";
    }
  db_unload("Messages/$mid");

  $str.="<form action='Action.php?action=Message-Reply' method='post' >";
  $str.="<input type='hidden' name='mid' value='$mid' />";
  $str.="<tr><td class='write'>Reply:</td><td class='write2' colspan=2><textarea name='text'></textarea></td></tr>";
  $str.="<tr><td class='write'>Attach:</td><td class='write2'><a>Input Options</a></td>";
  $str.="<td class='write'><input type='submit' value='Reply' /></td></tr>";

  $str.="</table>";

  return $str;
  }





















function message_index_display($self){

  $m = db_get_ids("Messages");
  if(!is_array($m) || $m[0]=="") $m=array();
  $n=array();
  foreach($m as $i) $n[] = db_get("Messages",$i,"Update","");
  array_multisort($n,$m);
  $output=""; $none=1;

  $str.="<form name='message_index' action='Action.php?action=Message-Action' method='post'>";
  $str.="<input type='hidden' name='action' value='' />";
  $str.="<table class='message-container' border=0>";
  $str.="<tr><td class='options' colspan=4>";
    $str.="<input type='button' value='Send New Message' onClick='".popup_message($uid,'mid','',$o)."' /> ";
    $str.="<input type='submit' value='Mark as Unread' onClick=\"document.message_index.action.value='unread'; \" /> ";
    $str.="<input type='submit' value='Delete' onClick=\"document.message_index.action.value='delete'; \" /> ";
    $str.="</td></tr><tr><td colspan=4></td></tr>";
    $output.=$o;

  $m = array_reverse($m);

  foreach($m as $i){

    $subject = db_get("Messages",$i,"Subject","");
    if($subject=="") $subject="(no subject)";
    $update = db_get("Messages",$i,"Update","");
    $status = db_get("Messages",$i,"Status","");
    $between = db_get("Messages",$i,"Between","");
    if(!is_array($between) || $between[0]=="") $between=array();
    $read = db_get("Messages",$i,"Read","");
    if(!is_array($read) || $read[0]=="") $read=array();
    if(!in_array($self,$between) || $status!="Normal") continue;

    db_load("Messages/$i");
    $uid = db_get("Messages/$i",$update,"UID","");
    $text=db_get("Messages/$i",$update,"Text","");
    db_unload("Messages/$i");

    if(!is_array($between) || $between[0]=="") $between=array();
    $between = array_diff($between,array($self));
    shuffle($between);
    $last = $between[count($between)-1];
    if(count($between)>5){
      $b = $between;
      foreach($b as $k=>$j) $b[$k]=user_fullname($j,0);
      $between = "<span>Between You, $b[0], $b[1], $b[2] and more ...</span>";
      }
    else if(count($between)>2){
      foreach($b as $k=>$j) $b[$k]=user_fullname($j,0);
      $b = implode($b,", "); if($b!="") $b=", $b";
      $between = "<span>Between You$b and ".user_fullname($last,0).".</span>";
      }
    else $between = "<span>Between You and ".user_fullname($last,0).".</span>";

    if(!in_array($self,$read)){
      $str.="<tr><td class='cb-unread'><input type='checkbox' name='mark$i' /></td>";
      $str.="<td class='pic-unread' onClick=\"window.location='?mid=$i';\">".user_photo($uid,50,0)."</td>";
      $str.="<td class='text-unread' onClick=\"window.location='?mid=$i';\"><div>".user_fullname($uid,0)."</div>$between<br>".adate($update)."</td>";
      $str.="<td class='text-unread' onClick=\"window.location='?mid=$i';\">";
      $str.="<div>$subject</div><span>$text</span></td></tr>";
      }
    else {
      $str.="<tr><td class='cb'><input type='checkbox' name='mark$i' /></td>";
      $str.="<td class='pic' onClick=\"window.location='?mid=$i';\">".user_photo($uid,50,0)."</td>";
      $str.="<td class='text' onClick=\"window.location='?mid=$i';\"><div>".user_fullname($uid,0)."</div>$between<br>".adate($update)."</td>";
      $str.="<td class='text' onClick=\"window.location='?mid=$i';\">";
      $str.="<div>$subject</div><span>$text</span></td></tr>";
      }
    
    $output.=$o; $none=0;

    }
  if($none) $str.="<tr><td colspan=3>You have no Messages.</td></tr>";
  $str.="</table></form> $output";

  return $str;
  }

// g9: 2,3,6
// g14: 21,22
// salt

?>