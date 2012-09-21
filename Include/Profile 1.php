<?php

function friend_list_normal($uid){
  $people = db_get_ids("Friends");
  if(is_array($people))
  foreach($people as $person)
    if(db_array_chk("Friends",$person,"Accepted",$uid))
      $list[] = $person;
  $temp = db_get("Friends",$uid,"Accepted","");
  if(is_array($temp))
  foreach($temp as $t) $list[]=$t;
  if(!is_array($list)) $list = array();
  return $list;
  }

function friend_list_mutual($uid1,$uid2){
  $list1 = friend_list_normal($uid1);
  $list2 = friend_list_normal($uid2);
  if(is_array($list1)&&is_array($list2)){
    $intersect = array_intersect($list1,$list2);
    foreach($intersect as $value) $temp[]=$value;
    return $temp;
    }
  else return array();
  }

function display_friends($heading,$subhead,$edit,$droplist,$list,$limit,$always,$popupheading,$popupid,&$output){
  if($limit=="") $limit=3;
  $link = popup_namelist("Friends-$popupid",$popupheading,$list,$output);
  $str.="<table class='fullwidth' border=0><tr>";
  if($edit!="")$str.="<th colspan=2>$heading</th><th class='edit'><a onClick='$edit'><img src='Images/System/box-edit.png' onMouseOver='this.src=\"Images/System/box-edit-highlight.png\"' onMouseOut='this.src=\"Images/System/box-edit.png\"' /></a><div id='pc-editbutton-friends'>$droplist</div></th></tr>";
  else $str.="<th colspan=3>$heading</th></tr>";
  $str.="<tr><td class='left' colspan=2><a onClick='$link'>".count($list)." $subhead</a></td>";
  $str.="<td class='right'><a onClick='$link'>View All</a></td></tr><tr>";

  if(is_array($always)){
    foreach($list as $i=>$j)
    if(in_array($j,$always)) unset($list[$i]);
    }
  else $always = array();
  if(!is_array($list)) $list=array();
  shuffle($list);
  $list = array_merge($always,$list);

  for($i=0;$i<count($list)&&$i<$limit;$i++){
    $str.="<td>".user_photo($list[$i],50)."<br>";
    $str.=user_fullname($list[$i])."</td>";
    if($i%3==2)$str.="</tr><tr>";
    }
  if($i%3<2){ while($i%3<2){ $str.="<td></td>"; $i++; } $str.="</tr><tr>"; }
  if(count($list)==0) $str.="<td class='text' colspan=3><p>None</p></td>";
  $str.="</tr><tr><td colspan=3 class='bottom'></td></tr></table>";
  return $str;
  }


function display_pc_about($uid,$self){
  global $profile;
  $default = $profile["About"];
  if($uid!=$self) return "<div>$default</div>";
  $str.="<script>";
  $str.="function about_".$uid."_show(){ (document.getElementById('about-$uid-show')).style.display='none'; (document.getElementById('about-$uid-edit')).style.display='block'; (document.getElementById('about-$uid-text')).select(); }";
  $str.="function about_".$uid."_edit(){ text = (document.getElementById('about-$uid-text')).value; ajax_post('Ajax.php?action=Edit-About','text='+(text),'profile-$uid',0); }";
  $str.="</script>";
  $str.="<div id='about-$uid-show' onClick='about_".$uid."_show();'>".(($default=="")?"Write something about yourself!":$default)."</div>";
  $str.="<div id='about-$uid-edit' style='padding:0; display:none;'><textarea id='about-$uid-text' onBlur='about_".$uid."_edit();' class='fullwidth'>$default</textarea></div>";
  return $str;
  }

function display_pc_info($uid,$action,$droplist){
  global $profile;
  $str.="<table class='fullwidth'>";
  $str.="<tr><th>Information</th><th class='edit'>";
  if($action!="") $str.="<a onClick='$action'><img src='Images/System/box-edit.png' onMouseOver='this.src=\"Images/System/box-edit-highlight.png\"' onMouseOut='this.src=\"Images/System/box-edit.png\"' /></a><div id='pc-editbutton-info'>$droplist</div>";
  $str.="</th></tr><tr><td class='text' colspan=2>";

  if(!is_array($profile["DBU"])) $profile["DBU"]=array();
  if(!is_array($profile["DBP"])) $profile["DBP"]=array();

  $str.="<p><h>Sex:</h><br>".db_get("Users",$uid,"Sex","")."</p>";
  foreach(array_merge($profile["DBU"],$profile["DBP"]) as $field){
    $value=db_get("Users",$uid,$field,"");
    if($value=="") $value = $profile[$field];
    if($field=="Birthday"||$field=="Last Active") $value=bdate($value);
    if($value!="") $str.="<p><h>$field:</h><br>".$value."</p>";
    }

  $str.="</td></tr><tr><td colspan=3 class='bottom'></td></tr></table>";
  return $str;
  }

function display_pc_photos($uid,$list,$popupheading,$popupid,&$output){

  $str.="<table class='pc-photos' border=0><tr><th colspan=2>Photos</th></tr>";
  $str.="<tr><td class='left'>";
  $str.="<a href='?photos=$uid'>Photo Albums</a></td>";
  $str.="<td class='right'><a></a></td></tr>";

  $a = array();
  if(is_array( $albums=db_get_ids("Albums") ))
    foreach($albums as $aid){
      if( db_get("Albums",$aid,"UID","")!=$uid ) continue;
      if( db_get("Albums",$aid,"Status","") != "Normal" ) continue;
      $p = db_get("Albums",$aid,"Photos",""); if($p[0]=="") $p=array();
      if(count($p)==0)continue; 
      $a[] = $aid;
      }
  shuffle($a);

  if(count($a)) foreach($a as $i=>$aid){ if($i==2) continue;
    $str.="<tr><td class='pic'><a href='?aid=$aid'><img width=75 src='Image.php?pid=".db_get("Albums",$aid,"Cover","")."'></a></td>";
    $str.="<td class='info'><a href='?aid=$aid'>".db_get("Albums",$aid,"Name","")."</a><br>";
    $str.="Updated : ".adate(db_get("Albums",$aid,"Updated",""))."</td></tr>";
    }
  else $str.="<tr><td class='text' colspan=3>".db_get("Users",$uid,"First Name",$uid)." has not uploaded any photos.</td></tr>";

  $str.="<tr><td colspan=3 class='finalbottom'></td></tr>";
  $str.="</table>";
  return $str;
  }


// ---------------------------------------------------------


function display_profile($uid){
  global $profile;
  $self = user_online();

  $str.="<div id='profile-$uid'><table class='profile-container'>";
  if($uid==$self) $str.="<tr><td class='pc01'><a href='?edit=profilepic'><img src='Image.php?uid=$uid' class='photo'></a></td></tr>";
  else $str.="<tr><td class='pc01'><img src='Image.php?uid=$uid' class='photo'></td></tr>";

  $name = db_get("Users",$uid,"First Name","// Error //");
  $fullname = "$name ".db_get("Users",$uid,"Last Name","");
  $f12 = friend_check($self,$uid);
  $r12 = friend_check_request($self,$uid);
  $r21 = friend_check_request($uid,$self);
  $p12 = friend_check_poke($self,$uid);
  $p21 = friend_check_poke($uid,$self);
  $h12 = friend_check_punch($self,$uid);
  $h21 = friend_check_punch($uid,$self);

  if($self!=$uid){
    $str.="<tr><td class='pc02' onClick=\"window.location='?photos=$uid';\"><a>View Photos of $name</a></td></tr>";
    $str.="<tr><td class='pc02'><a onClick='".popup_message($self,'profile-'.$uid,$fullname,$output7)."'>Send $name a Message</a></td></tr>";
    if(!$f12 && !$r12 && !$r21)	$str.="<tr><td class='pc02' onClick=\"ajax_post('Ajax.php?action=Friend-Request','uid=$uid&refresh=profile','profile-$uid',0);\"><a>Send $name a Friend Request</a></td></tr>";
    if(!$f12 && $r12)		$str.="<tr><td class='pc02'>Awaiting Friend Request Response</td></tr>";
    if(!$f12 && $r21)		$str.="<tr><td class='pc02' onClick=\"ajax_post('Ajax.php?action=Friend-Accept','uid=$uid&refresh=profile','profile-$uid',0);\"><a>Accept Friend Request</a></td></tr>";
    }
  else {
    $str.="<tr><td class='pc02' onClick=\"window.location='?photos=$uid';\"><a>View Photos of You</a></td></tr>";
    $str.="<tr><td class='pc02' onClick=\"window.location='?edit=basicinfo';\"><a>Edit My Profile</a></td></tr>";
    }

  if($f12 && $self!=$uid){
    if(!$p12 && !$p21)	$str.="<tr><td class='pc02'><a href='#' onClick=\"ajax_post('Ajax.php?action=Poke-Perform','uid=$uid&refresh=profile','profile-$uid',0);\">Poke $name</a></td></tr>";
    if($p12)		$str.="<tr><td class='pc02'>Awaiting Poke Response</td></tr>";
    if($p21)		$str.="<tr><td class='pc02'><a href='#' onClick=\"ajax_post('Ajax.php?action=Poke-Accept','uid=$uid&refresh=profile','profile-$uid',0);\">Respond to Poke</a></td></tr>";

    if(!$h12 && !$h21)	$str.="<tr><td class='pc02'><a href='#' onClick=\"ajax_post('Ajax.php?action=Punch-Perform','uid=$uid&refresh=profile','profile-$uid',0);\">Punch $name</a></td></tr>";
    if($h12)		$str.="<tr><td class='pc02'>Awaiting Punch Response</td></tr>";
    if($h21)		$str.="<tr><td class='pc02'><a href='#' onClick=\"ajax_post('Ajax.php?action=Punch-Accept','uid=$uid&refresh=profile','profile-$uid',0);\">Respond to Punch</a></td></tr>";
    }

  $str.="<tr><td class='pc03'>".display_pc_about($uid,$self)."</td></tr>";

  // Edit Popups Begin -----------------------------------------

  if($uid==$self){
    $temp ="<input type='hidden' name='type' value='1'>";
    $temp.="<p>Select number of Friends To Be Displayed : ";
    $temp.="<select name='DBF-X'>";
    for($n=3;$n<=12;$n+=3) $temp.="<option ". (($profile["DBF"]==$n)?"selected='selected'":"") .">$n</option>";
    $temp.="</select></p>";
    $temp.="<p>Always Display :</p>";
    foreach(friend_list_normal($uid) as $person){
      $temp.="<p><input type='checkbox' name='DBA-$person' ";
      if(is_array($profile["DBA"]) && in_array($person,$profile["DBA"])) $temp.="checked='checked' ";
      $temp.="/> ".user_fullname($person)."</p>";
      }
    //$edit1 = popup_text("$uid-edit-friends","Friends Display : Edit",$temp,"Action.php?action=Profile-EditDisplay",$output4);
    $edit1 = popup_droplist4($uid."_edit_friends",$temp,"Action.php?action=Profile-EditDisplay",'pc-editbutton-friends',$output4);

    $temp ="<input type='hidden' name='type' value='2'>";
    $temp.="<p>Show:</p>";
    $x = array("EMail","Birthday","Current Location");
    $y = array("Hometown","Relationship Status","Political Views","Religious Views","High School","University","Website");
    $temp.="<p><input type='checkbox' disabled='disabled' checked='checked'> Sex</p>";
    foreach($x as $z) $temp.="<p><input type='checkbox' name='DBU-$z' ".((is_array($profile["DBU"]) && in_array($z,$profile["DBU"]))?"checked='checked'":"")." /> $z</p>";
    foreach($y as $z) $temp.="<p><input type='checkbox' name='DBP-$z' ".((is_array($profile["DBP"]) && in_array($z,$profile["DBP"]))?"checked='checked'":"")." /> $z</p>";
    $edit2 = popup_droplist4($uid."_info_friends",$temp,"Action.php?action=Profile-EditDisplay",'pc-editbutton-info',$output5);
    }
  else { $edit1=""; $edit2=""; }

  // Edit Popups End -----------------------------------------

  $str.="<tr><td class='pc04'>".display_pc_info($uid,$edit2,"")."</td></tr>";
  if($self!=$uid) $str.="<tr><td class='pc04'>".display_friends("Mutual Friends","mutual friends","","",friend_list_mutual($uid,$self),3,0,"List of Your's & ".db_get("Users",$uid,"First Name","Error")."'s Mutual Friends","$uid-mutual-friends",$output1)."</td></tr>";
  $str.="<tr><td class='pc04'>".display_friends("Friends","friends",$edit1,"",friend_list_normal($uid),$profile["DBF"],$profile["DBA"],"List of ".db_get("Users",$uid,"First Name","Error")."'s Friends","$uid-normal-friends",$output2)."</td></tr>";
  $str.="<tr><td class='pc04'>".display_pc_photos($uid,"","List of ".db_get("Users",$uid,"First Name","Error")."'s Albums","$uid-albums",$output3)."</td></tr>";

  if($self!=$uid){
    $text = "I apologize for the inconvienience, but frankly speaking, I dont give a crap as to whether or not you've been offended in any way by anything here. So shut up and get lost ...";
    $link = popup_text("report-person","Report This Person","<table><tr><td><img src='Images/System/fuckoff.jpg' height=190 /></td><td>$text</td></table>","","","",$output6);
    $str.="<tr><td class='pc05'></td></tr>";
    $str.="<tr><td class='pc02'><a onClick='$link'>Report this Person</a></td></tr>";
    if($r12) $str.="<tr><td class='pc02'><a onClick=\"ajax_post('Ajax.php?action=Friend-Revoke','uid=$uid&refresh=profile','profile-$uid',0);\">Revoke Friend Request</a></td></tr>";
    if($f12) $str.="<tr><td class='pc02'><a onClick=\"ajax_post('Ajax.php?action=Friend-Remove','uid=$uid&refresh=profile','profile-$uid',0);\">Remove from Friends</a></td></tr>";
    }

  $str.="<tr><td class='pc05'></td></tr>";
  $str.="</table>$output1 $output2 $output3 $output4 $output5 $output6 $output7</div>";
  return $str;
  }

// pc-editbutton-friends pc-editbutton-info
// javascript: alert(getXYpos((document.getElementById('pc-editbutton-edit'))).y);

?>