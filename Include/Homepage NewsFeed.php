<?php

function newsfeed_display_stream($self,$location){

  if( $self!=1
  && eregi("^([A-Za-z])([0-9]*)$",$location,$x)
  && $x[1]=="U"
  && $x[2]!=$self && !friend_check($self,$x[2]) )
  return "<script>window.location='?info=$x[2]';</script>";

  $feed = db_get_ids("News");
  $feed = array_reverse($feed);
  $str=newsfeed_display_create($self,$location);
  foreach($feed as $i=>$fid){
    if(db_get("News",$fid,"Status","")!="Normal") continue;
    if(strcasecmp($location,0)!=0
    && strcasecmp(db_get("News",$fid,"Location",""),$location)!=0 ) continue;
    $uid = db_get("News",$fid,"UID","");
    if(!($location==0 && (friend_check($self,$uid) || $self==$uid) )) continue;
    $str.=newsfeed_display_specific($self,$fid,$location);
    }
  return $str;
  }

function newsfeed_display_create($self,$location){
  $str.="<form name='newsfeed_write' action='Action.php?action=Newsfeed-Write' method='post'>";
  $str.="<input type='hidden' name='location' value='$location' />";
  $str.="<table class='newsfeed-container' border=0>";
  $defaulttext = "Something on your mind ...";
  $str.="<tr><td class='write1' colspan=2><textarea name='data' style='color:#808080;' onClick=\"if(this.value=='$defaulttext'){ this.value=''; this.style.color='#000000'; }\" onBlur=\"if(this.value==''){ this.value='$defaulttext'; this.style.color='#808080'; }\" >$defaulttext</textarea></td></tr>";
  $str.="<tr><td class='write2'>Input Options</td>";
  $str.="<td class='write3'><input type='submit' value='Share' onClick=\"e=document.newsfeed_write.data; if(e.value=='$defaulttext') e.value=''; \"></td></tr>";
  $str.="</table></form>";
  return $str;
  }

function newsfeed_display_specific($self,$fid,$location){
  return "<div id='newsfeed-F$fid'>".newsfeed_display_refresh($self,$fid,$location)."</div>";
  }

// ----------------------------------------------------------------

function newsfeed_display_refresh($self,$fid,$loc1){
  $uid = db_get("News",$fid,"UID","");
  $data = db_get("News",$fid,"Data","");
  $time = db_get("News",$fid,"Time","");
  $loc2 = db_get("News",$fid,"Location","");
  $loc3 = eregi_replace("[A-Za-z]","",$loc2);
  $loc4 = eregi_replace("[0-9]","",$loc2);
  $type = db_get("News",$fid,"Type","");

  if(db_get("News",$fid,"Status","")!="Normal") return "";
  if($loc4=="E" && db_get("Events",$loc3,"Status","")!="Normal") return "";

  $str.="<table class='newsfeed-container' border=0>";

//  $str.="$type, $loc1, $loc2, $loc3, $loc4, $uid<br>";

  if($type=="Wall" && ( $loc4=="U" || strcasecmp($loc1,$loc2)==0) ){
    $str.="<tr><td rowspan=2 class='pic'>".user_photo($uid,50,1)."</td>";
    if( ($loc1==-1 && $uid!=$loc3) || (strcasecmp($loc1,0)==0 && strcasecmp($loc3,$uid)!=0) ) $str.="<td class='text1'>".user_fullname($uid,1)." <img src='Images/System/news-fromto.png' /> ".user_fullname($loc3,1)." $data</td></tr>";
    else $str.="<td class='text1'>".user_fullname($uid,1)." $data</td></tr>";
    $str.="<tr><td class='cmnt'>".comment_display($self,"F$fid")."</td></tr>";
    }
  else if($type=="Wall"){
    if($loc4=="E"){
      $str.="<tr><td rowspan=3 class='pic'><a href='?eid=$loc3'><img src='Image.php?eid=$loc3' style='width:50px;border:1px solid black;' /></a></td>";
      $str.="<td class='text2' colspan=2><a href='?eid=$loc3'>".db_get("Events",$loc3,"Name","")."</a> (Event)</td></tr>";
      }
    if($loc4=="T"){
      $str.="<tr><td rowspan=3 class='pic'><a href='?tid=$loc3'><img src='Image.php?tid=$loc3' style='width:50px;border:1px solid black;' /></a></td>";
      $str.="<td class='text2' colspan=2><a href='?tid=$loc3'>".db_get("Pages",$loc3,"Name","")."</a> (Page)</td></tr>";
      }
    if($loc4=="G"){
      $str.="<tr><td rowspan=3 class='pic'><a href='?tid=$loc3'><img src='Image.php?gid=$loc3' style='width:50px;border:1px solid black;' /></a></td>";
      $str.="<td class='text2' colspan=2><a href='?gid=$loc3'>".db_get("Groups",$loc3,"Name","")."</a> (Group)</td></tr>";
      }
    $str.="<tr><td class='det1'>".user_photo($uid,35,1)."</td><td class='det2'>".user_fullname($uid,1)." $data</td></tr>";
    $str.="<tr><td class='cmnt' colspan=2>".comment_display($self,"F$fid")."</td></tr>";
    }


  if($type=="Album"){
    $aid=$data;
    $cover = db_get("Albums",$aid,"Cover","");
    $caption = db_get("Albums",$aid,"Caption","");
    $creator = db_get("Albums",$aid,"UID","");
    $p = db_get("Albums",$aid,"Photos",""); if(is_array($p)) $p = count($p); else $p=0;
    $l = db_get("Albums",$aid,"Like",array()); if(!is_array($l) || $l[0]=="") $l=array(); if(count($l)>0) $l2=((count($l)==1)?"1 person":count($l)." people"); else $l2=-1; 
    $d = db_get("Albums",$aid,"Dislike",array()); if(!is_array($d) || $d[0]=="") $d=array(); if(count($d)>0) $d2=((count($d)==1)?"1 person":count($d)." people"); else $d2=-1;
    if($p==0 || db_get("Albums",$aid,"Status","")!="Normal" ) return "";

    $str.="<tr><td rowspan=2 class='pic'>".user_photo($uid,50,1)."</td>";
    $str.="<td class='text1'>".user_fullname($uid,1)." (Album)</td></tr>";
    $str.="<tr><td class='photo'>";
    $str.="<table border=0><tr><td style='width:50px;'><div> <a href='?aid=$aid'><img src='Image.php?pid=$cover' width=80 /></a> </div></td><td>";

    $str.="<span class='bold'><a href='?aid=$aid'>".db_get("Albums",$aid,"Name","")."</a></span>";
    if($caption!="") $str.=" <span>$caption</span>";
    $str.="<br><br><span>Photos:</span> $p";
    $str.="<br><span>By:</span> ".user_fullname($creator);

    $str.="</td></tr><tr><td colspan=2><br>";

    $str.="<a href='?fid=$fid'><img src='Images/System/news-photo.png'></a> ".adate($time);
    if($l2!=-1){
      $link=popup_namelist("$loc2-albumlike","People who like this Album",$l,$output1);
      $str.=SEP."<span><img src='Images/System/news-like.png'> <a onClick='$link'>$l2</a></span>";
      }
    if($d2!=-1){
      $link=popup_namelist("$loc2-albumdislike","People who dislike this Album",$d,$output2);
      $str.=SEP."<span><img src='Images/System/news-dislike.png'> <a onClick='$link'>$d2</a></span>";
      }
    if($self==$loc3 || $creator==$self) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Delete&id=F$fid&cid=0&refresh=disabled','newsfeed-F$fid',0);\">Delete post</a>";

    $str.="</td></tr></table> </td></tr>";
    }

  if($type=="Photo"){
    $pid=$data;
    $aid = db_get("Photos",$pid,"AID","");
    $caption = db_get("Photos",$pid,"Caption","");
    $creator = db_get("Photos",$pid,"UID","");

    $l = db_get("Photos",$pid,"Like",array()); if(!is_array($l) || $l[0]=="") $l=array(); if(count($l)>0) $l2=((count($l)==1)?"1 person":count($l)." people"); else $l2=-1; 
    $d = db_get("Photos",$pid,"Dislike",array()); if(!is_array($d) || $d[0]=="") $d=array(); if(count($d)>0) $d2=((count($d)==1)?"1 person":count($d)." people"); else $d2=-1;
    if( db_get("Photos",$pid,"Status","")!="Normal" ) return "";

    $str.="<tr><td rowspan=2 class='pic'>".user_photo($uid,50,1)."</td>";
    $str.="<td class='text1'>".user_fullname($uid,1)." (Photo)</td></tr>";
    $str.="<tr><td class='photo'>";
    $str.="<table border=0><tr><td style='width:50px;'><div> <a href='?pid=$pid'><img src='Image.php?pid=$pid' width=80 /></a> </div></td><td>";

    if($caption!="") $str.="<span>$caption</span><br><br>";
    $str.="<span>Album:</span> <a href='?aid=$aid'>".db_get("Albums",$aid,"Name","")."</a>";
    $str.="<br><span>By:</span> ".user_fullname($creator)."<br>";

    $str.="</td></tr><tr><td colspan=2><br>";

    $str.="<a href='?fid=$fid'><img src='Images/System/news-photo.png'></a> ".adate($time);
    if($l2!=-1){
      $link=popup_namelist("$loc2-photolike","People who like this Photo",$l,$output3);
      $str.=SEP."<span><img src='Images/System/news-like.png'> <a onClick='$link'>$l2</a></span>";
      }
    if($d2!=-1){
      $link=popup_namelist("$loc2-photodislike","People who dislike this Photo",$d,$output4);
      $str.=SEP."<span><img src='Images/System/news-dislike.png'> <a onClick='$link'>$d2</a></span>";
      }
    if($self==$loc3 || $creator==$self) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Delete&id=F$fid&cid=0&refresh=disabled','newsfeed-F$fid',0);\">Delete post</a>";

    $str.="</td></tr></table> </td></tr>";
    }

  if($type=="Event"){
    $eid=$data;
    $creator = db_get("Events",$eid,"UID","");

    $yes = db_get("Events",$eid,"Yes",array()); if(!is_array($yes)||$yes[0]=="") $yes=array(); 
    $no = db_get("Events",$eid,"No",array()); if(!is_array($no)||$no[0]=="") $no=array();
    $maybe = db_get("Events",$eid,"Maybe",array()); if(!is_array($maybe)||$maybe[0]=="") $maybe=array();

    $str.="<tr><td rowspan=2 class='pic'>".user_photo($uid,50,1)."</td>";
    $str.="<td class='text1'>".user_fullname($uid,1)." (Event)</td></tr>";
    $str.="<tr><td class='photo'>";
    $str.="<table border=0><tr><td style='width:50px;'><div> <a href='?eid=$eid'><img src='Image.php?eid=$eid' width=80 /></a> </div></td><td>";

    $str.="<a href='?eid=$eid'><b>".db_get("Events",$eid,"Name","")."</b></a><br><br>";
    $str.="Date/Time : ";
      $str.=jdate(db_get("Events",$eid,"Start Date","")).", ".db_get("Events",$eid,"Start Time","");
      if(db_get("Events",$eid,"Display End","")=="yes")
      $str.=" to ".jdate(db_get("Events",$eid,"End Date","")).", ".db_get("Events",$eid,"End Time","");
      $str.="<br>";
    $l = db_get("Events",$eid,"Location","");
    if($l!="") $str.="Location : $l<br>";
    $str.="Creator : ".user_fullname(db_get("Events",$eid,"UID",""),1)."<br>";

    $str.="</td></tr><tr><td colspan=2><br>";

    $str.="<a href='?fid=$fid'><img src='Images/System/news-event.png'></a> ".adate($time);
    $link.=popup_namelist("$loc2-eventyes","People who are Attending this Event",$yes,$output5);
    if(count($yes)>0) $str.=SEP."<a onClick='$link'>".((count($yes)==1)?"1 Person":count($yes)." People")." Attending</a>";
    if($self==$loc3 || $creator==$self) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Delete&id=F$fid&cid=0&refresh=disabled','newsfeed-F$fid',0);\">Delete post</a>";

    $str.="</td></tr></table> </td></tr>";

    }

  if($type=="Page"){
    $tid=$data;
    $creator = db_get("Page",$tid,"UID","");

    $l = db_get("Pages",$tid,"Like",array()); if(!is_array($l) || $l[0]=="") $l=array(); if(count($l)>0) $l2=((count($l)==1)?"1 person":count($l)." people"); else $l2=-1; 
    $d = db_get("Pages",$tid,"Dislike",array()); if(!is_array($d) || $d[0]=="") $d=array(); if(count($d)>0) $d2=((count($d)==1)?"1 person":count($d)." people"); else $d2=-1;
    if( db_get("Pages",$tid,"Status","")!="Normal" ) return "";

    $str.="<tr><td rowspan=2 class='pic'>".user_photo($uid,50,1)."</td>";
    $str.="<td class='text1'>".user_fullname($uid,1)." (Page)</td></tr>";
    $str.="<tr><td class='photo'>";
    $str.="<table border=0><tr><td style='width:50px;'><div> <a href='?tid=$tid'><img src='Image.php?tid=$tid' width=80 /></a> </div></td><td>";

    $str.="<a href='?tid=$tid'><b>".db_get("Pages",$tid,"Name","")."</b></a><br><br>";
    $str.="About : ".db_get("Pages",$tid,"Name","")."<br>";

    $str.="</td></tr><tr><td colspan=2><br>";

    $str.="<a href='?fid=$fid'><img src='Images/System/news-page.png'></a> ".adate($time);
    if(count($l)>0){
      $link.=popup_namelist("$loc2-page-like","People who are like this Page",$l,$output7);
      $str.=SEP."<a onClick='$link'><span><img src='Images/System/news-like.png'></span> ".((count($l)==1)?"1 person":count($l)." people")."</a>";
      }
    if(count($d)>0){
      $link.=popup_namelist("$loc2-page-dislike","People who are dislike this Page",$d,$output8);
      $str.=SEP."<a onClick='$link'><span><img src='Images/System/news-dislike.png'></span> ".((count($d)==1)?"1 person":count($d)." people")."</a>";
      }
    if($self==$loc3 || $creator==$self) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Delete&id=F$fid&cid=0&refresh=disabled','newsfeed-F$fid',0);\">Delete post</a>";

    $str.="</td></tr></table> </td></tr>";

    }



  $str.="</table> $output1 $output2 $output3 $output4 $output5 $output7 $output8";

  return $str;
  }

function newsfeed_text_add($self,$location,$data){
  global $time;
  if($data=="") return;
  $data = text_screen($data);
  if(strcmp($location,"0")==0) $location="U$self";

  $x["FID"]=count(db_get_ids("News"))+1;
  $x["UID"]=$self;
  $x["Status"]="Normal";
  $x["Location"]=$location;
  $x["Time"]=$time;
  $x["Data"]=$data;
  $x["Type"]="Wall";
  db_addrow("News",$x);

  if($x["Type"]=="Wall")
    if($location=="U$self") db_set("Users",$self,"Status Message",$data);
    else notify_write($location,$self,"","","","Wall-Post","?fid=".$x["FID"],$time);
  }

function newsfeed_share($self,$type,$id){
  global $time;
  $x["FID"]=count(db_get_ids("News"))+1;
  $x["UID"]=$self;
  $x["Status"]="Normal";
  $x["Location"]="U$self";
  $x["Type"]=$type;
  $x["Time"]=$time;
  $x["Data"]=$id;
  db_addrow("News",$x);
  }

?>