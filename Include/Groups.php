<?php

function group_create($self){
  if($_POST["name"]=="") return;

  $gid = $a["GID"] = count(db_get_ids("Groups"))+1;
  $a["UID"] = $self;
  $a["Status"] = "Normal";
  $a["Name"] = text_screen($_POST["name"]);
  $a["About"] = text_screen($_POST["about"]);
  $a["Location"] = text_screen($_POST["location"]);
  db_addrow("Groups",$a);
  if($_FILES['groupimage']['error']==0){ 
    file_delete("Images/Group/$gid.jpg"); file_delete("Images/Group/$gid.png"); file_delete("Images/Group/$gid.gif");
    $type=file_upload('groupimage',"Images/Group/".$gid,'image/jpeg,image/png,image/gif',2*1024*1024);
    }

  global $redirect; $redirect = "?editgroup=$gid";
  }

function group_delete($self){
  $gid = $_POST["gid"];
  $confirm = $_POST["confirm"];
  if(db_get("Groups",$gid,"UID","")!=$self) return;
  if($confirm=="on") db_set("Groups",$gid,"Status","Deleted");
  }

function group_mark($self,$gid,$command){
  if($command=="Join") db_array_add("Groups",$gid,"Members",$self);
  if($command=="Leave") db_array_del("Groups",$gid,"Members",$self);
  }

function group_update($self){
  $gid = $_POST["gid"];
  db_set("Groups",$gid,"About",text_screen($_POST["about"]));
  db_set("Groups",$gid,"Info",text_screen($_POST["info"]));
  db_set("Groups",$gid,"Location",text_screen($_POST["location"]));

  if($_POST['deleteimage']=="on" || $_FILES['groupimage']['error']==0){
    file_delete("Images/Group/$gid.jpg"); file_delete("Images/Group/$gid.png"); file_delete("Images/Group/$gid.gif");
    }
  if($_FILES['groupimage']['error']==0){ 
    $type=file_upload('groupimage',"Images/Group/".$gid,'image/jpeg,image/png,image/gif',2*1024*1024);
    }
  }







function group_left_display($gid,$self){
  $creator = db_get("Groups",$gid,"UID","");
  $about = db_get("Groups",$gid,"About","");
  $mem = db_get("Groups",$gid,"Members",""); if(!is_array($mem)||$mem[0]=="") $mem=array();

  $friends = friend_list_normal($self); if(!is_array($friends)||$friends[0]=="") $friends=array();
  $fmem = array_intersect($friends,$mem);

  shuffle($mem); shuffle($fmem);

  $str.="<table class='page-left-container' border=0>";
  $str.="<tr><td class='t11'><img src='Image.php?gid=$gid' /></td></tr>";

  $str.="<form action='Action.php?action=Group-Mark' method='post' name='group_mark'> <input type='hidden' name='gid' value='$gid' />";
  $str.="<input type='hidden' name='command' value='' /></form>";
  if(!in_array($self,$mem)) $str.="<tr><td class='t51' onClick=\"e=document.group_mark; e.command.value='Join'; e.submit(); \">Join this Group</td></tr>";
  else $str.="<tr><td class='t51' onClick=\"e=document.group_mark; e.command.value='Leave'; e.submit(); \">Leave this Group</td></tr>";

  $str.="<form action='Action.php?action=Group-Post' method='post' name='group_post'> <input type='hidden' name='gid' value='$gid' /> </form>";
  $str.="<tr><td class='t51' onClick=\" document.group_post.submit(); \">Post Group on Profile</td></tr>";
  if($self==$creator) $str.="<tr><td class='t51' onClick=\" window.location='?editgroup=$gid'; \">Edit Group Details</td></tr>";

  $str.="<tr><td class='t21'></td></tr>";
  $str.="<tr><td class='t31'><div>$about</div></td></tr>";

  $str.="<tr><td class='t21'></td></tr>";
  $str.="<tr><td class='t41'>";
    $str.=display_friends("Friends in this Group","friend(s)","","",$fmem,3,array(),"List of Friends who are in this Group","$gid-fmem",$output1);
    $str.="</td></tr>";
  $str.="<tr><td class='t41'>";
    $str.=display_friends("People in this Group","people","","",$mem,6,array(),"List of People in this Group","$gid-mem",$output2);
    $str.="</td></tr>";
  $str.="<tr><td class='t42'></td></tr>";

  $str.="<tr><td class='t21'></td></tr>";

  $str.="</table> $output1 $output2 $output3 $output4";

  return $str;
  }






function group_mid_display($gid,$self,$editmode){
  global $time;

  if(db_get("Groups",$gid,"Status","")!="Normal") return "<script>window.location='?index=groups'</script>";

  $creator = db_get("Groups",$gid,"UID","");
  $name = db_get("Groups",$gid,"Name","");
  $about = db_get("Groups",$gid,"About","");
  $info = db_get("Groups",$gid,"Info","");
  $location = db_get("Groups",$gid,"Location","");
  $mem = db_get("Groups",$gid,"Members",""); if(!is_array($mem)||$mem[0]=="") $mem=array();

  if(!in_array($self,$mem)){
    $opt.= "<input type='button' value='Join this Group' onClick=\"e=document.group_mark; e.command.value='Join'; e.submit(); \">";
    }

  $str.="<table class='page-mid-container'>";
  if($editmode && $self==$creator) $str.="<tr><td class='t11' colspan=2>$name : Group Edit Mode <a href='?gid=$gid'>(Return to Normal Mode)</a></td></tr>";
  else $str.="<tr><td class='t11' colspan=2>$name $opt</td></tr>";
  $str.="<tr><td class='t12' colspan=2>";
    $str.="<a onClick=\"window.location='?index=groups';\">Return to Group Index</a>";
    if(in_array($self,$mem)) $str.=SEP." You are a member of this Group";
    $str.="</td></tr>";
  $str.="<tr><td class='t21' colspan=2></td></tr>";
  $str.="<tr><td class='t31'>Creator</td><td class='t32'>".user_fullname($creator,1)."</td></tr>";
  if($editmode && $self==$creator){
    $str.="<form action='Action.php?action=Group-Update' method='post' enctype='multipart/form-data' > <input type='hidden' name='gid' value='$gid' />";
    $str.="<tr><td class='t44' colspan=2></td></tr>";
    $str.="<tr><td class='t41'>Location</td><td class='t42'><textarea name='location'>$location</textarea></td></tr>";
    $str.="<tr><td class='t41'>About</td><td class='t42'><textarea name='about'>$about</textarea></td></tr>";
    $str.="<tr><td class='t41'>Information</td><td class='t42'><textarea name='info'>$info</textarea></td></tr>";
    $str.="<tr><td class='t41'>Group Image</td><td class='t42'><input type='file' name='groupimage' /></td></tr>";
    $str.="<tr><td class='t41'></td><td class='t43'><input type='checkbox' name='deleteimage' /> Delete Current Group Image</td></tr>";
    $str.="<tr><td class='t41'></td><td class='t42'><input type='submit' class='button' value='Update' /> <input type='button' class='button' value='Clear Changes' onClick=\"window.location='?editgroup=$gid';\" /> </td></tr>";
    $str.="<tr><td class='t45' colspan=2></td></tr>";
    $str.="</form><form action='Action.php?action=Group-Delete' method='post'> <input type='hidden' name='gid' value='$gid' />";
    $str.="<tr><td class='t44' colspan=2></td></tr>";
    $str.="<tr><td class='t41'>Confirm Deletion</td><td class='t43'><input type='checkbox' name='confirm' /> Unless this checkbox is ticked, pressing the button below will have no effect.</td></tr>";
    $str.="<tr><td class='t41'></td><td class='t42'><input type='submit' class='button' value='Delete Group' /> </td></tr>";
    $str.="<tr><td class='t45' colspan=2></td></tr>";
    $str.="</table>";
    }
  else {
    if($location!="") $str.="<tr><td class='t31'>Location</td><td class='t32'>$location</td></tr>";
    if($info!="") $str.="<tr><td class='t31'>Information</td><td class='t32'>$info</td></tr>";
    $str.="<tr><td class='t21' colspan=2></td></tr>";
    $str.="<tr><td class='t33'>Wall</td><td class='t34'><a href='?gid=$gid' title='This page is up to date as of ".fdate($time)."'>Refresh</td></tr>";
    $str.="</table>";
    $str.=newsfeed_display_stream($self,"G$gid");
    }

  return $str;
  }















function group_right_display($self,$gid){
  $g = db_get_ids("Groups");
  shuffle($g);

  $k=0;
  $str.="<table class='page-right-container' border=0>";
  $output="";
  foreach($g as $i){
    if($i==$self) continue;
    if(db_get("Groups",$gid,"Status","")!="Normal") continue;
    if($k==3) break; $k++;
    if($k==1) $str.="<tr><th><a href='?index=groups'>Groups</a></th></tr>";
    $name = db_get("Groups",$gid,"Name","");
    $mem = db_get("Groups",$gid,"Members",""); if(!is_array($mem)||$mem[0]=="") $mem=array();
    $str.="<tr><td><span><img src='Image.php?gid=$i' ></span> <p><a href='?gid=$i' class='name'>$name</a></p><br>";
    if(count($mem)>0){
      $o=""; $link = popup_namelist("gid-$gid-right-members","People who are part of this Group",$mem,$o); $output.=$o;
      $str.="<p><a onClick='$link'> ".((count($mem)==1)?"1 member.":count($mem)." members.")."</a></p>";
      }
    $str.="</td></tr>";
    }
  $str.="</table>".ad_display(225,5-$k);
  return $str;
  }























function group_index_display($self){
  $g = db_get_ids("Groups");
  if(!is_array($g)||$g[0]=="") $g=array();
  $x = array();
  foreach($g as $i){
    if(db_get("Groups",$i,"Status","")!="Normal") continue;
    $x[] = $i;
    }
  shuffle($x);

  $str.="<table class='page-index-container' border=0>";
  $k=0; $output="";
  foreach($x as $i){ if($k==10) break; $k++;
    $mem = db_get("Groups",$i,"Members",""); if(!is_array($mem)||$mem[0]=="") $mem=array();
    $str.="<tr><td class='t31' rowspan=2><a href='?gid=$i'><img src='Image.php?gid=$i' /></a></td>";
    $str.="<td class='t32'><a href='?gid=$i'>".db_get("Groups",$i,"Name","")."</a></td></tr>";
    $str.="<tr><td class='t33'>";
    if(count($mem)>0){
      $o=""; $link = popup_namelist("gid-$gid-index-members","People who are part of this Group",$mem,$o); $output.=$o;
      $str.="<p><a onClick='$link'> ".((count($mem)==1)?"1 member.":count($mem)." members.")."</a></p>";
      }
    $str.="</td></tr><tr><td colspan=2 class='t41'></td></tr>";
    }

  $str.="<form action='Action.php?action=Group-Create' method='post' enctype='multipart/form-data'>";
  $str.="<tr><td class='t51' colspan=2> Create your own Group</td></tr>";
  $str.="<tr><td class='t52'>Name</td><td class='t53'><input type='text' name='name' /></td></tr>";
  $str.="<tr><td class='t52'>Image</td><td class='t53'><input type='file' name='groupimage' /></td></tr>";
  $str.="<tr><td class='t52'>About</td><td class='t53'><textarea name='about'></textarea></td></tr>";
  $str.="<tr><td class='t52'>Location</td><td class='t53'><input type='text' name='location' /></td></tr>";
  $str.="<tr><td class='t52'></td><td class='t53'><input type='submit' class='button' value='Create Group'></td></tr>";
  $str.="<tr><td class='t54' colspan=2></td></tr>";
  $str.="</form>";

  $str.="</table> $output";

  return $str;
  }





?>