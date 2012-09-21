<?php

function photo_create($uid,$aid,$photofile){
  $pid = count(db_get_ids("Photos"))+1;
  $type=file_upload($photofile,"Images/Photos/".$pid,'image/jpeg,image/png,image/gif',2*1024*1024);
  if($type==-4) return 0;
  else db_addrow("Photos",array("PID"=>$pid,"AID"=>$aid,"UID"=>$uid,"Status"=>"Normal","Type"=>$type));
  return $pid;
  }

function photo_delete($self){
  $pid = $_POST["pid"];
  $aid = db_get("Photos",$pid,"AID","");
  $uid = db_get("Photos",$pid,"UID","");
  if($self!=$uid) return;
  db_set("Photos",$pid,"Status","Deleted");
  db_array_del("Albums",$aid,"Photos",$pid);
  if(db_get("Albums",$aid,"Cover","")==$pid){
    $p = db_get("Albums",$aid,"Photos",array());
    if(count($p)==0) $p=0; else $p=$p[0];
    db_set("Albums",$aid,"Cover",$p);
    }
  }

function photo_cover($self){
  $pid = $_POST["pid"];
  $aid = db_get("Photos",$pid,"AID","");
  $uid = db_get("Photos",$pid,"UID","");
  if($self!=$uid) return;
  db_set("Albums",$aid,"Cover",$pid);
  }

function photo_profilepicture($self){
  $pid = $_POST["pid"];
  $uid = db_get("Photos",$pid,"UID","");
  $type = db_get("Photos",$pid,"Type","");
  if($uid!=$self) return;
  if(file_exists("Images/Photos/$pid.$type"))
    copy("Images/Photos/$pid.$type","Images/Profile/$uid.$type");
  }


// ------------------------------------------------------------------------

function photo_display($uid,$pid,$tagmode=0){

  $aid = db_get("Photos",$pid,"AID","");
  $creator=db_get("Albums",$aid,"UID","");

  if(db_get("Photos",$pid,"Status","")!="Normal") return "<script>window.location='?uid=$uid';</script>";

    $p = db_get("Albums",$aid,"Photos",array());
    foreach($p as $i=>$j) if($pid==$j) $m=$i+1;
    $n = count($p);
    $prev = ($m==1)?$p[$n-1]:$p[$m-2];
    $next = ($m==$n)?$p[0]:$p[$m];

  $str.="<table class='photo-container' border=0>";
  $str.="<h2>".user_fullname($creator,0)."'s Photos - ".db_get("Albums",$aid,"Name","")."</h2>";
  $str.="<tr><td class='topleft' colspan=2 >Photo $m of $n ".SEP;
  $str.="<a href='?aid=$aid'>Back to Album</a>".SEP;
  $str.="<a href='?photos=$creator'>".db_get("Users",$creator,"First Name","")."'s Photos</a>".SEP;
  $str.="<a href='?uid=$creator'>".db_get("Users",$creator,"First Name","")."'s Profile</a>";
  $str.="</td><td class='topright' colspan=2 style='text-align:right;'>";
  $str.="<a href='?pid=$prev'>Previous</a>".SEP."<a href='?pid=$next'>Next</a>";
  $str.="</td></tr>";

  // -------------------------------------------------

  if($tagmode==1){
    $str.="<tr><td colspan=4 class='tagdone'><div> <table><tr><td class='left'>Click on people's faces in the photo to tag them.</td>";
    $str.="<td class='right'><input type='button' value='Done Tagging' onClick=\"window.location='?pid=$pid';\" /></td></tr></table> </div></td></tr>";
    }

  $box=""; $untag=""; $ppl=array();
  if(1){
    $t = db_get("Photos",$pid,"Tags",array());
    if(!is_array($t) || $t[0]=="") $t=array();
    $mm = array();
    foreach($t as $c=>$i){
      if(eregi("^([0-9]+),([0-9]+):(.*)$",$i,$x)){ $title=$x[3]; $tt=1; }
      else if(eregi("^([0-9]+),([0-9]+)~([0-9]+)$",$i,$x)){ $title=user_fullname($x[3],0); $tt=2; }
      else continue;
      $mm[] = " tb = document.getElementById('tagbox-$c'); if( x>".($x[1]-50)." && x<".($x[1]+50)." && y>".($x[2]-50)." && y<".($x[2]+50)." ){ tb.style.display='block'; tb.style.left=$x[1]-50+xy.x; tb.style.top=$x[2]-50+xy.y; } else { tb.style.display='none'; }";
      $box.="<div id='tagbox-$c' class='tagbox'> <div class='up'></div> <div class='down'>$title</div> </div>";

      if($tt==2) $p = "<a onMouseOver=\" tb = document.getElementById('tagbox-$c'); tb.style.display='block'; tb.style.left=$x[1]-50+xy.x; tb.style.top=$x[2]-50+xy.y; \" onMouseOut=\" tb = document.getElementById('tagbox-$c'); tb.style.display='none'; \" href='?uid=$x[3]' >$title</a>";
      else $p = "<a onMouseOver=\" tb = document.getElementById('tagbox-$c'); tb.style.display='block'; tb.style.left=$x[1]-50+xy.x; tb.style.top=$x[2]-50+xy.y; \" onMouseOut=\" tb = document.getElementById('tagbox-$c'); tb.style.display='none'; \" >$title</a>";
           if($tt==2 && ($x[3]==$uid || $uid==$creator) ) $p.=" (<a href='?photos=$x[3]'>photos</a> | <a onClick=\"document.forms['imageuntag$c'].submit();\">remove tag</a>)";
      else if($tt==2 && ($x[3]!=$uid && $uid==$creator) ) $p.=" (<a href='?photos=$x[3]'>photos</a>)";
      else if($tt==1 && $uid==$creator) $p.=" (<a onClick=\"document.forms['imageuntag$c'].submit();\">remove tag</a>)";
      $ppl[] = $p;
      $untag.= "<form action='Action.php?action=Image-Untag' method='post' name='imageuntag$c'> <input type='hidden' name='pid' value='$pid' />";
      $untag.= "<input type='hidden' name='tag' value=\"$i\" /> </form>";
      }
    $str.="<script>function mousemove(e,f){ xy = getXYpos(f); var x=e.pageX-xy.x; var y=e.pageY-xy.y; ".implode($mm)." }</script>";
    }

  // -------------------------------------------------

  if($tagmode==0) $str.="<tr><td colspan=4 class='pic2'>".$box."<a href='?pid=$next'><img width=700 src='Image.php?pid=$pid' id='imagetag' onmousemove='mousemove(event,this);' /></a></td></tr>";
  if($tagmode==1){
 
    $str.="<tr><td colspan=4 class='pic2'>";
    $str.="<div id='pointer_div' onclick='if(mouseOverTaglist==0) mouseclick(event); else mouseOverTaglist=1; '> ";

    $str.="<div id='square' class='square' onMouseOver='mouseOverTaglist=1;' onMouseOut='mouseOverTaglist=0;'>";
      $str.="<div></div> </div>";
    $str.="<script>mouseOverTaglist=0;</script>";
    $str.="<div id='taglist' class='taglist' onMouseOver='mouseOverTaglist=1;' onMouseOut='mouseOverTaglist=0;' >";
      $str.="<form name='pointform' action='Action.php?action=Image-Tag' method='post'> <table border=0>";
      $str.="<tr><td class='up'>Tag : <input type='hidden' name='pid' value='$pid'>";
      $str.="<input type='text' name='xpos' value='0' disabled='true' title='X-Coordinate' /> ";
      $str.="<input type='text' name='ypos' value='0' disabled='true' title='Y-Coordinate' />";
      $str.="</td></tr> <tr><td class='mid'><div>";
      $str.="<input type='radio' name='uid' value='0' checked='checked' /> <input type='text' name='name' /> <br />";
      $str.="<input type='radio' name='uid' value='$uid' /> ".user_fullname($uid,0)." (Me) <br />";
      $f = friend_list_normal($uid); if(!is_array($f)||$f[0]=="") $f=array(); $f=array_unique($f);
      foreach($f as $u) $str.="<input type='radio' name='uid' value='$u' /> ".user_fullname($u,0)." <br />";
      $str.="</div></td></tr> <tr><td class='bottom'>";
      $str.="<input type='submit' class='submit' value='Tag' onClick=\" document.pointform.xpos.disabled=false; document.pointform.ypos.disabled=false; \" />";
      $str.="<input type='button' class='cancel' value='Cancel' onClick=\" (document.getElementById('square')).style.display='none'; (document.getElementById('taglist')).style.display='none'; mouseOverTaglist=1; \" />";
      $str.="</td></tr>";

      $str.="</table> </form> </div>";

    $str.="<img id='tagimage' src='Image.php?pid=$pid' width=700 style='cursor:crosshair;' /></div> </td></tr>";

    }

  // ---------------------------------------------------------------------

  if(1){

    $str.="<tr><td rowspan=100>";
    if(count($ppl)>0) $str.=$untag."<div class='tagnames'>People in this photo : ".implode($ppl,'; ')."</div>";
    $str.=comment_display($uid,"P$pid")."</td> <td rowspan=100 class='vspace'></td> <td></td> <td rowspan=100 class='vspace'></td> <tr>";
      if($uid==$creator) $str.="<tr><td class='opt1'>From your album:<br><a href='?aid=$aid'>".db_get("Albums",$aid,"Name","")."</a></td></tr>";
      else $str.="<tr><td class='opt1'>From the album:<br><a href='?aid=$aid'>".db_get("Albums",$aid,"Name","")."</a> by ".user_fullname($creator)."</td></tr>";

    if($uid==$creator){
      $str.="<tr><td class='opt1'>";
      $str.="<img src='Images/System/photo-rotate-left.gif'  onMouseOver=\"this.src='Images/System/photo-rotate-left-highlight.gif';\"  onMouseOut=\"this.src='Images/System/photo-rotate-left.gif';\"  onClick='rotate1();' >";
      $str.="<img src='Images/System/photo-rotate-right.png' onMouseOver=\"this.src='Images/System/photo-rotate-right-highlight.png';\" onMouseOut=\"this.src='Images/System/photo-rotate-right.png';\" onClick='rotate2();' >";
      $str.="</td></tr>";
      $str.="<form name='photo-rotate' action='Action.php?action=Photo-Rotate' method='post'>";
        $str.="<input type='hidden' name='pid' value='$pid' />";
        $str.="<input type='hidden' name='deg' value='' />";
        $str.="</form> <script>";
        $str.="function rotate1(){ e=document.forms['photo-rotate']; e.deg.value=90;  e.submit(); }";
        $str.="function rotate2(){ e=document.forms['photo-rotate']; e.deg.value=-90; e.submit(); }";
        $str.="</script>";
      }
    else $str.="<tr><td>&nbsp;</td></tr>";

    if($tagmode==0) $str.="<tr><td class='opt2' onClick=\"window.location='?tag=$pid';\"><a>Tag this Photo</a></td></tr>";
    else $str.="<tr><td class='opt2' onClick=\"window.location='?pid=$pid';\"><a>Done Tagging</a></td></tr>";

    $str.="<form action='Action.php?action=Photo-Post' method='post' name='post-pid-$pid'><input type='hidden' name='pid' value='$pid' /></form>";
    $str.="<tr><td class='opt2' onClick=\"(document.forms['post-pid-$pid']).submit();\"><a>Post Photo to Profile</a></td></tr>";
    if($uid==$creator){
      $str.="<tr><td class='opt2' onClick=\"(document.forms['cover-pid-$pid']).submit();\"><a>Make Album Cover</a></td></tr>";
      $str.="<form action='Action.php?action=Photo-Cover' method='post' name='cover-pid-$pid'><input type='hidden' name='pid' value='$pid' /></form>";

      $str.="<tr><td class='opt2' onClick=\"(document.forms['dp-pid-$pid']).submit();\"><a>Make Profile Picture</a></td></tr>";
      $str.="<form action='Action.php?action=Photo-Profile' method='post' name='dp-pid-$pid'><input type='hidden' name='pid' value='$pid' /></form>";

      $str.="<tr><td class='opt2' onClick=\"(document.forms['delete-pid-$pid']).submit();\"><a>Delete This Photo</a></td></tr>";
      $str.="<form action='Action.php?action=Photo-Delete' method='post' name='delete-pid-$pid'><input type='hidden' name='pid' value='$pid' /></form>";
      }

    }

  $str.="<tr><td></td></tr></table>";
  return $str;
  }

// ------------------------------------

function photo_rotate($pid,$deg){

  $imgfile = "Images/Photos/$pid";

  if(file_exists("$imgfile.jpg")){
    $source = imagecreatefromjpeg("$imgfile.jpg") ;
    $rotate = imagerotate($source, $deg, 0) ;
    imagejpeg($rotate,"$imgfile.jpg");
    }

  }

// ---------------------------------------------------------

function photo_tag($self){
  $pid = $_POST["pid"];
  $uid = $_POST["uid"];
  $name = text_screen($_POST["name"]);
  $xpos = $_POST["xpos"];
  $ypos = $_POST["ypos"];
  if($uid==0) $str="$xpos,$ypos:$name";
  else {
    $str="$xpos,$ypos~$uid";
    global $time;
    notify_write($uid,$self,0,0,0,"Image-Tag","?pid=$pid",$time);
    }
  db_array_add("Photos",$pid,"Tags",$str);
  }

function photo_untag($self){
  $pid = $_POST["pid"];
  $tag = text_screen($_POST["tag"]);
  db_array_del("Photos",$pid,"Tags",$tag);
  }

?>