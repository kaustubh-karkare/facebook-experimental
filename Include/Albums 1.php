<?php

function album_create($uid){
  global $time;
  $aid = count(db_get_ids("Albums"))+1;
  $name = $_POST["name"];

  $flag=1;
  for($i=0;$i<5;$i++) if($_FILES["photo".($i+1)]['error']==0) $flag=0;
  if($name=="") $flag=1;
  if($flag) return;

  db_addrow("Albums",array("AID"=>$aid,"UID"=>$uid,"Name"=>$name,"Status"=>"Normal" ));
  db_set("Albums",$aid,"Photos",array());
  $cover=0;
  for($i=0;$i<5;$i++){
    if($_FILES["photo".($i+1)]['error']==0){
      $pid = photo_create($uid,$aid,"photo".($i+1));
      if($pid==0) continue;
      db_array_add("Albums",$aid,"Photos",$pid);
      if($cover==0) $cover=$pid;
      }
    }
  db_set("Albums",$aid,"Cover",$cover);
  db_set("Albums",$aid,"Updated",$time);
  }

function album_edit($uid){
  global $time;
  $aid = $_POST["aid"];

  foreach($_POST as $key=>$value){
    if(eregi("name",$key) && $value!="") db_set("Albums",$aid,"Name",$value);
    if(eregi("cover",$key)) db_set("Albums",$aid,"Cover",$value);
    if(eregi("delete([0-9]+)",$key,$pid) ){
      db_set("Photos",$pid[1],"Status","Deleted");
      db_array_del("Albums",$aid,"Photos",$pid[1]);
      }
    if(eregi("caption([0-9]+)",$key,$pid) ){
      db_set("Photos",$pid[1],"Caption",$value);
      }
    }

  foreach($_FILES as $key=>$value){
    if(eregi("photo[1-5]",$key) && $_FILES[$key]['error']==0){
      $pid = photo_create($uid,$aid,$key);
      if($pid==0) continue;
      db_array_add("Albums",$aid,"Photos",$pid);
      }
    }

  $cover = db_get("Albums",$aid,"Cover","");
  if($cover==0 || db_get("Photos",$cover,"Status","")=="Deleted"){
    $p=db_get("Albums",$aid,"Photos",array());
    if(count($p)==0) $p=0; else $p=$p[0];
    db_set("Albums",$aid,"Cover",$p);
    }

  db_set("Albums",$aid,"Updated",$time);
  }

function album_delete($uid){
  $aid = $_POST["aid"];
  if(db_get("Albums",$aid,"UID","")==$uid
  && $_POST['albumdelete']=="on" )
  db_set("Albums",$aid,"Status","Deleted");

  $photos = db_get("Albums",$aid,"Photos",array());
  if(is_array($photos)) foreach($photos as $pid)
    db_set("Photos",$pid,"Status","Deleted");
  }















function album_display($uid,$aid){

  $albumname = db_get("Albums",$aid,"Name","");
  $photos = db_get("Albums",$aid,"Photos",array());
  $creator = db_get("Albums",$aid,"UID","");
  $updated = db_get("Albums",$aid,"Updated","");
  $cover = db_get("Albums",$aid,"Cover","");
  if(!is_array($photos) || $photos[0]=="") $photos=array();

  if(db_get("Albums",$aid,"Status","")!="Normal") return "<script>window.location='?index=newsfeed';</script>";

  $str.="<table class='album-container'>";

  $str.="<h2>".user_fullname($creator,0)."'s Albums - $albumname</h2>";
  $str.="<p> <a href='?photos=$creator'>".db_get("Users",$creator,"First Name","")."'s Photos</a>".SEP;
  $str.="<a href='?uid=$creator'>".db_get("Users",$creator,"First Name","")."'s Profile</a> </p>";

  if(is_array($photos))
  foreach($photos as $i=>$pid){
    if($i==0) $str.="<tr>";
    else if($i%4==0) $str.="</tr><tr>";
    $str.="<td><a href='?pid=$pid'><div><img src='Image.php?pid=$pid'></div></td>";
    }
  while(($i+1)%4){ $str.="<td></td>"; $i++; }
  $str.="</tr></table>";

  if($uid==$creator){
    $m = count($photos);

    $str.="<div id='album-edit1' style='display:none;'>";
    $str.="<table class='album-edit-container' border=0>";
    $str.="<form action='Action.php?action=Album-Edit' method='post' enctype='multipart/form-data'>";
    $str.="<input type='hidden' name='aid' value='$aid' />";
    $str.="<tr><th colspan=4>Edit Album</th></tr>";
    $str.="<tr><td>Album Name</td><td colspan=3 class='mid'><input type='text' class='text' name='name' value='$albumname' /></td></tr>";
    for($i=0;$i<$m;$i++){
      $str.="<tr><td rowspan=2 class='left'>Photo ".($i+1)."</td>";
      $str.="<td class='mid' rowspan=2> <img width=80 src='Image.php?pid=".$photos[$i]."' /> </td>";
      if($photos[$i]!=$cover) $str.="<td class='opt1'> <input type='radio' name='cover' value='".$photos[$i]."' /> Album Cover </td>";
        else $str.="<td class='opt1'> <input type='radio' name='cover' value='".$photos[$i]."' checked='checked' /> Album Cover </td>";
      $str.="<td rowspan=2 class='opt3'><textarea title='Caption' name='caption".$photos[$i]."'>".db_get("Photos",$photos[$i],"Caption","")."</textarea></td></tr>";
      $str.="<tr><td class='opt2'> <input type='checkbox' name='delete".$photos[$i]."' /> Delete Photo </td></tr>";
      }
    $str.="<tr><td colspan=4>&nbsp;</td></tr>";
    $str.="<tr><td></td><td class='mid' colspan=3><input type='submit' class='submit' value='Update Album'>  <input type='button' value='Cancel' class='cancel' onClick='albumedit0();'></td></tr> </form>";
    $str.="<tr><td colspan=3>&nbsp;</td></tr>";
    $str.="</form> </table> </div>";

    $str.="<div id='album-edit2' style='display:none;'>";
    $str.="<table class='album-edit-container' border=0>";
    $str.="<form action='Action.php?action=Album-Edit' method='post' enctype='multipart/form-data'>";
    $str.="<input type='hidden' name='aid' value='$aid' />";
    $str.="<tr><th colspan=3>Upload Photos</th></tr>";
    for($j=0;$j<5;$j++)
      $str.="<tr><td class='left'>Select Photo ".($j+1)."</td><td class='mid' colspan=2><input type='file' class='file' name='photo".($j+1)."' /></td></tr>";
    $str.="<tr><td colspan=3>&nbsp;</td></tr>";
    $str.="<tr><td></td><td class='mid' colspan=2><input type='submit' class='submit' value='Update Album'>  <input type='button' value='Cancel' class='cancel' onClick='albumedit0();'></td></tr> </form>";
    $str.="<tr><td colspan=3>&nbsp;</td></tr>";
    $str.="</form> </table> </div>";

    $str.="<div id='album-edit3' style='display:none;'>";
    $str.="<table class='album-edit-container' border=0>";
    $str.="<form action='Action.php?action=Album-Delete' method='post' enctype='multipart/form-data'>";
    $str.="<input type='hidden' name='aid' value='$aid' />";
    $str.="<tr><th colspan=3>Delete Album</th></tr>";
    $str.="<tr><td class='left'>Confirm Album<br>Deletion Operation</td><td class='mid' colspan=2> <input type='checkbox' name='albumdelete' /> Unless this checkbox is ticked, pressing the button below will have no effect.</td></tr>";
    $str.="<tr><td colspan=4>&nbsp;</td></tr>";
    $str.="<tr><td></td><td class='mid' colspan=2><input type='submit' class='submit' value='Delete Album'> <input type='button' value='Cancel' class='cancel' onClick='albumedit0();'> </td> </tr>";
    $str.="<tr><td colspan=4>&nbsp;</td></tr>";
    $str.="</form> </table> </div>";

    }

  $str.="<table class='album-info'><tr> <td rowspan=1000 class='vspace'></td> <td></td> <td rowspan=1000 class='vspace'></td>";
  $str.="<td rowspan=1000>".comment_display($uid,"A$aid")."</td></tr>";

  $ppl=array();
  foreach($photos as $p){
    $tags = db_get("Photos",$p,"Tags",array());
    if(!is_array($tags) || $tags[0]=="") $tags=array();
    foreach($tags as $i)
      if(eregi("^([0-9]+),([0-9]+)~([0-9]+)$",$i,$x)) $ppl[]=$x[3];
    }
  $ppl = array_unique($ppl); shuffle($ppl);
  if(count($ppl)>0){
    $str.="<tr><td class='opt1'>In this Album<br><br>";
    foreach($ppl as $i=>$p) $str.=user_photo($p,30).(($i%3==2)?"<br>":"");
    $str.="</td></tr>";
    }

    $str.="<tr><td class='opt1'>Updated<br>".adate($updated)."</td></tr>";
    $str.="<form action='Action.php?action=Album-Post' method='post' name='post-aid-$aid'><input type='hidden' name='aid' value='$aid' /></form>";
    $str.="<tr><td class='opt2' onClick=\"document.forms['post-aid-$aid'].submit();\"><a>Post Album to Profile</a></td></tr>";
    if($uid==$creator){
      $str.="<script>";
      $str.="function albumedit0(){ document.getElementById('album-edit1').style.display='none'; document.getElementById('album-edit2').style.display='none'; document.getElementById('album-edit3').style.display='none'; }";
      $str.="function albumedit1(){ document.getElementById('album-edit2').style.display='none'; document.getElementById('album-edit3').style.display='none'; var e = document.getElementById('album-edit1'); if(e.style.display=='none') e.style.display='block'; else e.style.display='none'; }";
      $str.="function albumedit2(){ document.getElementById('album-edit1').style.display='none'; document.getElementById('album-edit3').style.display='none'; var e = document.getElementById('album-edit2'); if(e.style.display=='none') e.style.display='block'; else e.style.display='none'; }";
      $str.="function albumedit3(){ document.getElementById('album-edit1').style.display='none'; document.getElementById('album-edit2').style.display='none'; var e = document.getElementById('album-edit3'); if(e.style.display=='none') e.style.display='block'; else e.style.display='none'; }";
      $str.="</script>";
      $str.="<tr><td class='opt2' onClick='albumedit1();'><a>Edit Album</a></td></tr>";
      $str.="<tr><td class='opt2' onClick='albumedit2();'><a>Upload More Photos</a></td></tr>";
      $str.="<tr><td class='opt2' onClick='albumedit3();'><a>Delete Album</a></td></tr>";
      }
  $str.="<tr><td></td></tr> </table>";

  if($uid==$creator){
    $str.="";
    }

  return $str;
  }

// ------------------------------------------------------------------------

function album_display_list($self,$uid){

  // if(!friend_check($self,$uid)) return "<script>window.location='?info=$uid';</script>";

  $p = array();
  if(is_array( $photos=db_get_ids("Photos") ))
    foreach($photos as $pid)
      if( db_get("Photos",$pid,"Status","")=="Normal"
      && is_array( $tags=db_get("Photos",$pid,"Tags",array()) ))
        foreach($tags as $t)
          if( eregi("^[0-9]+,[0-9]+~([0-9]+)$",$t,$x) ){
            if($x[1]==$uid) $p[]=$pid; }
  $p = array_reverse($p);

  if(count($p)){
    if($self!=$uid) $str.="<table class='album-head-container' border=0><th>Photos Of ".db_get("Users",$uid,"First Name","// Error //")."</th></table>";
    else $str.="<table class='album-head-container' border=0><th>Photos Of You</th></table>";
    $str.="<table class='album-list-container' border=0>";
    foreach($p as $i=>$pid){
      if($i==8) break;
      if($i>0 && $i%4==0) $str.="</tr><tr>";
      $str.="<td class='pic2'><a href='?pid=$pid'><img src='Image.php?pid=$pid' /></a></td>";
      }
    while($i%4){ $str.="<td class='pic2'>1</td>"; $i++; }
    $str.="</tr></table>";
    }

  $a = array();
  if(is_array( $albums=db_get_ids("Albums") ))
    foreach($albums as $aid)
      if( db_get("Albums",$aid,"UID","")==$uid){
        if( db_get("Albums",$aid,"Photos",array(""))==array("") && $self!=$uid) continue;
        if( db_get("Albums",$aid,"Status","")=="Deleted" ) continue;
        $a[] = $aid;
        }

  if($self!=$uid) $str.="<table class='album-head-container' border=0><th>".db_get("Users",$uid,"First Name","// Error //")."'s Albums</th></table>";
  else $str.="<table class='album-head-container' border=0><th>Your Albums</th></table>";
  $str.="<table class='album-list-container' border=0>";
  if(count($a)) foreach($a as $aid){
    $like = db_get("Albums",$aid,"Like",array());
    $dislike = db_get("Albums",$aid,"Dislike",array());
    $str.="<tr><td class='pic'><a href='?aid=$aid'><img src='Image.php?pid=".db_get("Albums",$aid,"Cover","")."' /></a></td><td class='info'>";
    $str.="<p><a href='?aid=$aid'>".db_get("Albums",$aid,"Name","")."</a> by ".user_fullname( db_get("Albums",$aid,"UID","") )."</p>";
    $str.="<p>Updated : ".adate( db_get("Albums",$aid,"Updated","") )."</p>";
    if(count($like)>0) $str.="<p><img src='Images/System/news-like.png'> ".( (count($like)>1)?count($like)." people like this.":"1 person likes this." )."</p>";
    if(count($dislike)>0) $str.="<p><img src='Images/System/news-dislike.png'> ".( (count($dislike)>1)?count($dislike)." people dislike this.":"1 person dislikes this." )."</p>";
    $str.="</td></tr>";
    }
  else {
    if($self!=$uid) $str.="<tr><td class='info' colspan=2><p>".db_get("Users",$uid,"First Name",$uid)." has not uploaded any photos.</p></td></tr>";
    else $str.="<tr><td class='info' colspan=2><p>You have not uploaded any photos.</p></td></tr>";
    }
  $str.="</table>";

  if($self==$uid){
    $str.="<table class='album-head-container' border=0><th>Create New Album</th></table>";
    $str.="<form action='Action.php?action=Album-Create' method='post' enctype='multipart/form-data'>";
    $str.="<table class='album-make-container' border=0>";
    $str.="<tr><th colspan=2>Create New Album</th></tr>";
    $str.="<tr><td>Enter Album Name</td><td><input type='text' name='name' /></td></tr>";
    for($i=0;$i<5;$i++)
      $str.="<tr><td>Select Photo ".($i+1)."</td><td><input type='file' name='photo".($i+1)."' /></td></tr>";
    $str.="<tr><td></td><td>More photos can be added once the Album has been created.<br>A 2MB size restriction has been imposed on the files to be uploaded.</td></tr>";
    $str.="<tr><td></td><td>&nbsp;</td></tr>";
    $str.="<tr><td></td><td><input type='submit' class='submit' value='Create New Album'></td></tr>";
    $str.="<tr><td></td><td>&nbsp;</td></tr> </table></form>";
    }

  return $str;
  }

?>