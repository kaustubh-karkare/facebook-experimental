<?php

function page_create($self){
  if($_POST["name"]=="") return;

  $tid = $a["TID"] = count(db_get_ids("Pages"))+1;
  $a["UID"] = $self;
  $a["Status"] = "Normal";
  $a["Name"] = text_screen($_POST["name"]);
  $a["About"] = text_screen($_POST["about"]);
  db_addrow("Pages",$a);
  if($_FILES['pageimage']['error']==0){ 
    file_delete("Images/Page/$tid.jpg"); file_delete("Images/Page/$tid.png"); file_delete("Images/Page/$tid.gif");
    $type=file_upload('pageimage',"Images/Page/".$tid,'image/jpeg,image/png,image/gif',2*1024*1024);
    }

  global $redirect; $redirect = "?editpage=$tid";
  }

function page_delete($self){
  $tid = $_POST["tid"];
  $confirm = $_POST["confirm"];
  if(db_get("Pages",$tid,"UID","")!=$self) return;
  if($confirm=="on") db_set("Pages",$tid,"Status","Deleted");
  }

function page_mark($self,$tid,$command){
  if($command=="Like") db_array_add("Pages",$tid,"Like",$self);
  if($command=="UnLike") db_array_del("Pages",$tid,"Like",$self);
  if($command=="Dislike") db_array_add("Pages",$tid,"Dislike",$self);
  if($command=="UnDislike") db_array_del("Pages",$tid,"Dislike",$self);
  }

function page_update($self){
  $tid = $_POST["tid"];
  db_set("Pages",$tid,"About",text_screen($_POST["about"]));
  db_set("Pages",$tid,"Info",text_screen($_POST["info"]));

//echo listout($_POST).listout($_FILES);

  if($_POST['deleteimage']=="on" || $_FILES['pageimage']['error']==0){
    file_delete("Images/Page/$tid.jpg"); file_delete("Images/Page/$tid.png"); file_delete("Images/Page/$tid.gif");
    }
  if($_FILES['pageimage']['error']==0){ 
    $type=file_upload('pageimage',"Images/Page/".$tid,'image/jpeg,image/png,image/gif',2*1024*1024);
    }
  }







function page_left_display($tid,$self){
  $creator = db_get("Pages",$tid,"UID","");
  $about = db_get("Pages",$tid,"About","");
  $like = db_get("Pages",$tid,"Like",""); if(!is_array($like)||$like[0]=="") $like=array();
  $dislike = db_get("Pages",$tid,"Dislike",""); if(!is_array($dislike)||$dislike[0]=="") $dislike=array();

  $friends = friend_list_normal($self); if(!is_array($friends)||$friends[0]=="") $friends=array();
  $flike = array_intersect($friends,$like);
  $fdislike = array_intersect($friends,$dislike);

  shuffle($like); shuffle($dislike); shuffle($flike); shuffle($fdislike);

  $str.="<table class='page-left-container' border=0>";
  $str.="<tr><td class='t11'><img src='Image.php?tid=$tid' /></td></tr>";

  $str.="<form action='Action.php?action=Page-Mark' method='post' name='page_mark'> <input type='hidden' name='tid' value='$tid' />";
  $str.="<input type='hidden' name='command' value='' /></form>";
  if(in_array($self,$like)) $str.="<tr><td class='t51' onClick=\"e=document.page_mark; e.command.value='UnLike'; e.submit(); \">UnLike this Page</td></tr>";
  else $str.="<tr><td class='t51' onClick=\"e=document.page_mark; e.command.value='Like'; e.submit(); \">Like this Page</td></tr>";
  if(in_array($self,$dislike)) $str.="<tr><td class='t51' onClick=\"e=document.page_mark; e.command.value='UnDislike'; e.submit(); \">UnDislike this Page</td></tr>";
  else $str.="<tr><td class='t51' onClick=\"e=document.page_mark; e.command.value='Dislike'; e.submit(); \">Dislike this Page</td></tr>";

  $str.="<form action='Action.php?action=Page-Post' method='post' name='page_post'> <input type='hidden' name='tid' value='$tid' /> </form>";
  $str.="<tr><td class='t51' onClick=\" document.page_post.submit(); \">Post Page on Profile</td></tr>";
  if($self==$creator) $str.="<tr><td class='t51' onClick=\" window.location='?editpage=$tid'; \">Edit this Page</td></tr>";

  $str.="<tr><td class='t21'></td></tr>";
  $str.="<tr><td class='t31'><div>$about</div></td></tr>";

  $str.="<tr><td class='t21'></td></tr>";
  $str.="<tr><td class='t41'>";
    $str.=display_friends("Friends who like this","friends like this","","",$flike,3,array(),"List of Friends who like this Page","$tid-flikers",$output1);
    $str.="</td></tr>";
  $str.="<tr><td class='t41'>";
    $str.=display_friends("Friends who dislike this","friends dislike this","","",$fdislike,3,array(),"List of Friends who dislike this Page","$tid-fdislikers",$output2);
    $str.="</td></tr>";
  $str.="<tr><td class='t42'></td></tr>";

  $str.="<tr><td class='t21'></td></tr>";
  $str.="<tr><td class='t41'>";
    $str.=display_friends("People who like this","people like this","","",$like,6,array(),"List of People who like this Page","$tid-likers",$output3);
    $str.="</td></tr>";
  $str.="<tr><td class='t41'>";
    $str.=display_friends("People who dislike this","people dislike this","","",$dislike,6,array(),"List of People who dislike this Page","$tid-dislikers",$output4);
    $str.="</td></tr>";
  $str.="<tr><td class='t42'></td></tr>";

  $str.="<tr><td class='t21'></td></tr>";

  $str.="</table> $output1 $output2 $output3 $output4";

  return $str;
  }






function page_mid_display($tid,$self,$editmode){
  global $time;

  if(db_get("Pages",$tid,"Status","")!="Normal") return "<script>window.location='?index=pages'</script>";

  $creator = db_get("Pages",$tid,"UID","");
  $name = db_get("Pages",$tid,"Name","");
  $about = db_get("Pages",$tid,"About","");
  $info = db_get("Pages",$tid,"Info","");
  $like = db_get("Pages",$tid,"Like",""); if(!is_array($like)||$like[0]=="") $like=array();
  $dislike = db_get("Pages",$tid,"Dislike",""); if(!is_array($dislike)||$dislike[0]=="") $dislike=array();

  if(!in_array($self,$like)&&!in_array($self,$dislike)){
    $opt = "<input type='button' value='Like' onClick=\"e=document.page_mark; e.command.value='Like'; e.submit(); \">";
    $opt.= "<input type='button' value='Dislike' onClick=\"e=document.page_mark; e.command.value='Dislike'; e.submit(); \">";
    }

  $str.="<table class='page-mid-container'>";
  if($editmode && $self==$creator) $str.="<tr><td class='t11' colspan=2>$name : Page Edit Mode <a href='?tid=$tid'>(Return to Normal Mode)</a></td></tr>";
  else $str.="<tr><td class='t11' colspan=2>$name $opt</td></tr>";
  $str.="<tr><td class='t12' colspan=2>";
    $str.="<a onClick=\"window.location='?index=pages';\">Return to Pages Index</a>";
    if(in_array($self,$like)) $str.=SEP." You like this Page";
    if(in_array($self,$dislike)) $str.=SEP." You dislike this Page";
    $str.="</td></tr>";
  $str.="<tr><td class='t21' colspan=2></td></tr>";
  $str.="<tr><td class='t31'>Creator</td><td class='t32'>".user_fullname($creator,1)."</td></tr>";
  if($editmode && $self==$creator){
    $str.="<form action='Action.php?action=Page-Update' method='post' enctype='multipart/form-data' > <input type='hidden' name='tid' value='$tid' />";
    $str.="<tr><td class='t44' colspan=2></td></tr>";
    $str.="<tr><td class='t41'>About</td><td class='t42'><textarea name='about'>$about</textarea></td></tr>";
    $str.="<tr><td class='t41'>Information</td><td class='t42'><textarea name='info'>$info</textarea></td></tr>";
    $str.="<tr><td class='t41'>Page Image</td><td class='t42'><input type='file' name='pageimage' /></td></tr>";
    $str.="<tr><td class='t41'></td><td class='t43'><input type='checkbox' name='deleteimage' /> Delete Current Page Image</td></tr>";
    $str.="<tr><td class='t41'></td><td class='t42'><input type='submit' class='button' value='Update' /> <input type='button' class='button' value='Clear Changes' onClick=\"window.location='?editpage=$tid';\" /> </td></tr>";
    $str.="<tr><td class='t45' colspan=2></td></tr>";
    $str.="</form><form action='Action.php?action=Page-Delete' method='post'> <input type='hidden' name='tid' value='$tid' />";
    $str.="<tr><td class='t44' colspan=2></td></tr>";
    $str.="<tr><td class='t41'>Confirm Deletion</td><td class='t43'><input type='checkbox' name='confirm' /> Unless this checkbox is ticked, pressing the button below will have no effect.</td></tr>";
    $str.="<tr><td class='t41'></td><td class='t42'><input type='submit' class='button' value='Delete Page' /> </td></tr>";
    $str.="<tr><td class='t45' colspan=2></td></tr>";
    $str.="</table>";
    }
  else {
    if($info!="") $str.="<tr><td class='t31'>Information</td><td class='t32'>$info</td></tr>";
    $str.="<tr><td class='t21' colspan=2></td></tr>";
    $str.="<tr><td class='t33'>Wall</td><td class='t34'><a href='?tid=$tid' title='This page isup to date as of ".fdate($time)."'>Refresh</td></tr>";
    $str.="</table>";
    $str.=newsfeed_display_stream($self,"T$tid");
    }

  return $str;
  }















function page_right_display($self,$tid){
  $p = db_get_ids("Pages");
  shuffle($p);

  $k=0;
  $str.="<table class='page-right-container' border=0>";
  $output="";
  foreach($p as $i){
    if($i==$self) continue;
    if(db_get("Pages",$tid,"Status","")!="Normal") continue;
    if($k==3) break; $k++;
    if($k==1) $str.="<tr><th><a href='?index=pages'>Pages</a></th></tr>";
    $name = db_get("Pages",$tid,"Name","");
    $like = db_get("Pages",$tid,"Like",""); if(!is_array($like)||$like[0]=="") $like=array();
    $dislike = db_get("Pages",$tid,"Dislike",""); if(!is_array($dislike)||$dislike[0]=="") $dislike=array();
    $str.="<tr><td><span><img src='Image.php?tid=$i' ></span> <p><a href='?tid=$i' class='name'>$name</a></p><br>";
    if(count($like)>0){
      $o=""; $link = popup_namelist("tid-$tid-right-like","People who like this Page",$like,$o); $output.=$o;
      $str.="<p><a onClick='$link'><img src='Images/System/news-like.png'> ".((count($like)==1)?"1 person likes this.":count($like)." people like this.")."</a></p>";
      }
    if(count($dislike)>0){
      $o=""; $link = popup_namelist("tid-$tid-right-dislike","People who dislike this Page",$dislike,$o); $output.=$o;
      $str.="<p><a onClick='$link'><img src='Images/System/news-dislike.png'> ".((count($dislike)==1)?"1 person dislikes this.":count($dislike)." people dislike this.")."</a></p>";
      }
    $str.="</td></tr>";
    }
  $str.="</table>".ad_display(225,5-$k);
  return $str;
  }























function page_index_display($self){
  $p = db_get_ids("Pages");
  if(!is_array($p)||$p[0]=="") $p=array();
  $x = array();
  foreach($p as $i){
    if(db_get("Pages",$i,"Status","")!="Normal") continue;
    // $like = db_get("Pages",$i,"Like",""); if(!is_array($like)||$like[0]=="") $like=array();
    // $dislike = db_get("Pages",$i,"Dislike",""); if(!is_array($dislike)||$dislike[0]=="") $dislike=array();
    $x[] = $i;
    }
  shuffle($x);

  $str="<style>
table.page-index-container { width:525px; margin:10px; }
table.page-index-container * { font-family:'Tahoma'; font-size:12px; }
table.page-index-container td.t31 { width:80px; padding-bottom:10px; }
table.page-index-container td.t31 img { width:80px; border:1px solid black; margin:4px; }
table.page-index-container td.t32 { padding:4px; }
table.page-index-container td.t32 a { font-weight:bold; font-size:14px;  }
table.page-index-container td.t33 { padding:4px; vertical-align:top; padding-bottom:10px; }
table.page-index-container td.t33 img { height:10px; }
table.page-index-container td.t41 { height:20px; border-top:1px solid #D8DFEA; }
table.page-index-container td.t51 { border:1px solid #CCCCCC; border-bottom:0px solid #CCCCCC; background:#EEEEEE; font-size:16px; font-weight:bold; text-align:center; padding:4px; }
table.page-index-container td.t52 { border-left:1px solid #CCCCCC; background:#EEEEEE; font-size:14px; font-weight:bold; color:#444444; padding:4px; }
table.page-index-container td.t53 { border-right:1px solid #CCCCCC; background:#EEEEEE; padding:4px; }
table.page-index-container td.t53 input { width:100%; padding:2px; }
table.page-index-container td.t53 textarea { width:100%; height:40px; padding:2px; }
table.page-index-container td.t53 input.button { width:160px; background:#627AAC; color:#FFFFFF; font-size:14px; font-weight:bold; font-family:'Tahoma'; border:1px solid #29447E; padding:4px; margin:2px; }
table.page-index-container td.t54 { border-top:1px solid #CCCCCC; }
</style>";

  $str.="<table class='page-index-container' border=0>";
  $k=0; $output="";
  foreach($x as $i){ if($k==10) break; $k++;
    $like = db_get("Pages",$i,"Like",""); if(!is_array($like)||$like[0]=="") $like=array();
    $dislike = db_get("Pages",$i,"Dislike",""); if(!is_array($dislike)||$dislike[0]=="") $dislike=array();
    $str.="<tr><td class='t31' rowspan=2><a href='?tid=$i'><img src='Image.php?tid=$i' /></a></td>";
    $str.="<td class='t32'><a href='?tid=$i'>".db_get("Pages",$i,"Name","")."</a></td></tr>";
    $str.="<tr><td class='t33'>";
    if(count($like)>0){
      $o=""; $link = popup_namelist("tid-$tid-index-like","People who like this Page",$like,$o); $output.=$o;
      $str.="<p><a onClick='$link'><img src='Images/System/news-like.png'> ".((count($like)==1)?"1 person likes this.":count($like)." people like this.")."</a></p>";
      }
    if(count($dislike)>0){
      $o=""; $link = popup_namelist("tid-$tid-index-dislike","People who dislike this Page",$dislike,$o); $output.=$o;
      $str.="<p><a onClick='$link'><img src='Images/System/news-dislike.png'> ".((count($dislike)==1)?"1 person dislikes this.":count($dislike)." people dislike this.")."</a></p>";
      }
    $str.="</td></tr><tr><td colspan=2 class='t41'></td></tr>";
    }

  $str.="<form action='Action.php?action=Page-Create' method='post' enctype='multipart/form-data'>";
  $str.="<tr><td class='t51' colspan=2> Create your own Page</td></tr>";
  $str.="<tr><td class='t52'>Name</td><td class='t53'><input type='text' name='name' /></td></tr>";
  $str.="<tr><td class='t52'>Image</td><td class='t53'><input type='file' name='pageimage' /></td></tr>";
  $str.="<tr><td class='t52'>About</td><td class='t53'><textarea name='about'></textarea></td></tr>";
  $str.="<tr><td class='t52'></td><td class='t53'><input type='submit' class='button' value='Create Page'></td></tr>";
  $str.="<tr><td class='t54' colspan=2></td></tr>";
  $str.="</form>";

  $str.="</table> $output";

  return $str;
  }





?>