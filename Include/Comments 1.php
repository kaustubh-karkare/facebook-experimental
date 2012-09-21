<?php

function comment_namelist($self,$list,$single,$plural){
  if(!is_array($list) || count($list)==0) return "";
  $i=0; shuffle($list);
  if(in_array($self,$list)) $a[$i++]="You"; else $self=0;
  foreach($list as $user) if($user!=$self) $a[$i++]=user_fullname($user,0);
  switch(count($a)){
    case 1 : if($self) $str="$a[0] $plural"; else $str="$a[0] $single"; break;
    case 2 : $str="$a[0] & $a[1] $plural"; break;
    case 3 : $str="$a[0], $a[1] & $a[2] $plural"; break;
    default : $str="$a[0], $a[1] & ".(count($a)-2)." others $plural";
    }
  return trim($str);
  }











function comment_display($self,$id){
  return "<div id='comment-$id'>".comment_refresh($self,$id)."</div>";
  }

function comment_refresh($uid,$id){

  $dbid = "Comments/$id";
  $output="";
  if(eregi("([A-Za-z])([0-9]{1,3})",$id,$x)){
    if(strtoupper($x[1])=='A') $source = "Albums"; 
    if(strtoupper($x[1])=='P') $source = "Photos";
    if(strtoupper($x[1])=='F') $source = "News";
    if(strtoupper($x[1])=='X') $source = "Special";
    
    $creator=db_get($source,$x[2],"UID",0);
    $caption=db_get($source,$x[2],"Caption","");
    $like=db_get($source,$x[2],"Like",array());
    $dislike=db_get($source,$x[2],"Dislike",array());
    }

  if(!db_exists($dbid)) db_create($dbid,"Time,User,Status,Text,Like,Dislike");
  db_load($dbid);

  $str.="<table class='comments-container' border=0>";

  if($source!="News"){
    if($uid!=$creator && $caption!="") $str.="<br><div class='caption' style='margin-bottom:10px;'>$caption</div>";
    if($uid==$creator){
      $str.="<div class='caption' ><textarea title='Caption' onBlur=\"if(this.value!='$caption') ajax_post('Ajax.php?action=Comment-Mark','command=Caption&id=$id&cid=0&text='+(this.value),'comment-$id',0); \">$caption</textarea></div>";
      }
    }

  if($uid!=$creator && $caption=="") $str.="<br><br>";

  if($source!="Special"){
    $str.="<div class='top'>";
    if($source=="Albums" || $source=="Photos") $str.="<img src='Images/System/news-photo.png' />&nbsp; ";
    if($source=="News") $str.="<a href='?fid=$x[2]'><img src='Images/System/news-comment.png' /></a>&nbsp; ".adate(db_get("News",$x[2],"Time",0)).SEP;
    if(is_array($like)&&!in_array($uid,$like)) $str.="<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Like&id=$id&cid=0','comment-$id',0);\">Like</a>".SEP;
      else $str.="<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=UnLike&id=$id&cid=0','comment-$id',0);\">UnLike</a>".SEP;
    if(is_array($dislike)&&!in_array($uid,$dislike)) $str.="<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Dislike&id=$id&cid=0','comment-$id',0);\">Dislike</a>".SEP;
      else $str.="<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=UnDislike&id=$id&cid=0','comment-$id',0);\">UnDislike</a>".SEP;
    $str.="<a onClick=\"e=document.getElementById('comment-$id-write'); e.click(); e.select();\">Comment</a>";
    if($source=="News") $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Delete&id=$id&cid=0&refresh=disabled','newsfeed-$id',0);\">Delete</a>";
    $str.="</div>";
    }

  $str.="<tr><td class='top'><img src='Images/System/news-triangle.png' /></td></tr>";
  if(count($like)) $str.="<tr><td colspan=2><img src='Images/System/news-like.png'> <a onClick='".popup_namelist("$id-Like","People Who Like This",$like,$output1)."'>".comment_namelist($uid,$like,"likes this.","like this.")."</td></tr><tr><td class='hpad' colspan=2></td></tr>";
  if(count($dislike)) $str.="<tr><td colspan=2><img src='Images/System/news-dislike.png'> <a onClick='".popup_namelist("$id-Dislike","People Who Dislike This",$dislike,$output2)."'>".comment_namelist($uid,$dislike,"dislikes this.","dislike this.")."</td></tr><tr><td class='hpad' colspan=2></td></tr>";
  $output.="$output1 $output2 ";

  foreach(db_get_ids($dbid) as $i){
    if(db_get($dbid,$i,'Status','')!="Normal") continue;
    $x = db_get($dbid,$i,'Like',''); if(!is_array($x)) $x = array();
    $y = db_get($dbid,$i,'Dislike',''); if(!is_array($y)) $y = array();
    $z = db_get($dbid,$i,'User','');
    $str.="<tr><td class='pic'>".user_photo($z,30,1)."</td>";
    $str.="<td class='text'>".user_fullname($z)." ".db_get($dbid,$i,'Text','');
    $str.="<br><div>".adate(db_get($dbid,$i,'Time',''));
    if(!in_array($uid,$x)) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Like&id=$id&cid=$i','comment-$id',0);\">Like</a>";
      else $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=UnLike&id=$id&cid=$i','comment-$id',0);\">UnLike</a>";
    if(count($x)>0) $str.=SEP."<a onClick='".popup_namelist("$id-C$i-Like","People Who Like This",$x,$output1)."'><img src='Images/System/news-like.png' height=10> ".((count($x)==1)?"1 person":count($x)." people")."</a>";
    if(!in_array($uid,$y)) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Dislike&id=$id&cid=$i','comment-$id',0);\">Dislike</a>";
      else $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=UnDislike&id=$id&cid=$i','comment-$id',0);\">UnDislike</a>";
    if(count($y)>0) $str.=SEP."<a onClick='".popup_namelist("$id-C$i-Dislike","People Who Dislike This",$y,$output2)."'><img src='Images/System/news-dislike.png' height=10> ".((count($y)==1)?"1 person":count($y)." people")."</a>";
    if($uid==$creator||$uid==$z) $str.=SEP."<a onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Delete&id=$id&cid=$i','comment-$id',0);\">Delete</a>";
    $str.="</div></td></tr><tr><td class='hpad' colspan=2></td></tr>";
    $output.="$output1 $output2 ";
    }

  if($source=="Special" || $uid==$creator || friend_check($uid,$creator)){
    $defaulttext = "Write a comment...";
    $str.="<tr><td class='pic'>".user_photo($uid,30,1)."</td>";
    $str.="<td class='text'><textarea id='comment-$id-write' onClick=\"if(this.value=='$defaulttext'){ this.value=''; this.style.color='#000000'; (document.getElementById('newcomment_$id')).style.display='block'; }\" onBlur=\"if(this.value==''){ this.value='$defaulttext'; this.style.color='#444444'; (document.getElementById('newcomment_$id')).style.display='none'; } \" >$defaulttext</textarea>";
    $str.="<div id='newcomment_$id' class='new'><input type='button' value='Comment' onClick=\"ajax_post('Ajax.php?action=Comment-Mark','command=Write&id=$id&cid=0&text='+((document.getElementById('comment-$id-write')).value),'comment-$id',0);\" /></div></td></tr>";
    }
  else $str.="<tr><td colspan=2>Comments not allowed!</td></tr>";
  $str.="</table> $output";

  db_unload($dbid);
  return $str;
  }

















function comment_command($command,$id,$cid,$text,$uid){
  global $time;
  $dbid = "Comments/$id";

  $text=stripslashes($text);
  $text=eregi_replace("\""," ",$text); // 34
  $text=eregi_replace("\'"," ",$text); // 39
  $text=eregi_replace("<","&#60;",$text);
  $text=eregi_replace(">","&#62;",$text);
  $text=eregi_replace(NL," ",$text);

  if(eregi("([A-Za-z])([0-9]{1,5})",$id,$x)){
    if(strtoupper($x[1])=='A') $source = "Albums"; 
    if(strtoupper($x[1])=='P') $source = "Photos";
    if(strtoupper($x[1])=='F') $source = "News";
    if(strtoupper($x[1])=='X') $source = "Special";
    }

  db_load($dbid);
  if($cid!=0){
    if($command=="Like") db_array_add($dbid,$cid,"Like",$uid);
    if($command=="UnLike") db_array_del($dbid,$cid,"Like",$uid);
    if($command=="Dislike") db_array_add($dbid,$cid,"Dislike",$uid);
    if($command=="UnDislike") db_array_del($dbid,$cid,"Dislike",$uid);
    if($command=="Delete") db_set($dbid,$cid,"Status","Deleted");
    }
  else {
    if($command=="Like") db_array_add($source,$x[2],"Like",$uid);
    if($command=="UnLike") db_array_del($source,$x[2],"Like",$uid);
    if($command=="Dislike") db_array_add($source,$x[2],"Dislike",$uid);
    if($command=="UnDislike") db_array_del($source,$x[2],"Dislike",$uid);
    if($command=="Caption") db_set($source,$x[2],"Caption",$text);

    if($command=="Write") db_addrow($dbid,array("Time"=>$time,"User"=>$uid,"Text"=>$text,"Status"=>"Normal")); // New Comment
    if($command=="Delete" && $source=="News") db_set("News",$x[2],"Status","Deleted");
    }
  db_save($dbid);

  // -----------------------------------------------------

  if($command!="Delete" && $command!="Caption"){
    $a=array();
    $u2=$a[]=db_get($source,$x[2],"UID","");
    $u3=$a[]=db_get($source,$x[2],"Location","");
    if($source=="Special"){ $u2 = $x[2]; $u3 = 0; }
    $u4=db_get($dbid,$cid,"User","");
    $c = db_get_ids($dbid);
    if(is_array($c)) foreach($c as $i){
      $a[] = db_get($dbid,$i,"User","");
      $l = db_get($dbid,$i,"Like","");
      if(is_array($l)) foreach($l as $j) $a[] = $j;
      $d = db_get($dbid,$i,"Dislike","");
      if(is_array($d)) foreach($d as $j) $a[] = $j;
      if($source=="Photos") $t = db_get("Photos",$x[2],"Tags","");
      if(is_array($t)) foreach($t as $j) if(eregi("^[0-9]+,[0-9]+~([0-9]+)$",$j,$y)) $a[]=$y[1];
      }
    $a = array_unique($a);
    if(is_array($a)) foreach($a as $u)
      if($u!="" && $u!=$uid){
        $s=-1;
        if($cid==0 && $command=="Write") $s = $source."-Comment";
        if($cid==0 && $command=="Like") $s = $source."-Like";
        if($cid==0 && $command=="Dislike") $s = $source."-Dislike";
        if($cid!=0 && $command=="Like") $s = "Comment-Like";
        if($cid!=0 && $command=="Dislike") $s = "Comment-Dislike";
        $link = "?".strtolower($x[1])."id=".$x[2];
        if($source=="Special" && $s!="Special-Comment") break;
        else $link="?index=about";
        if($s!=-1) notify_write($u,$uid,$u2,$u3,$u4,$s,$link,$time);
        }
    }

  // -----------------------------------------------------

  db_unload($dbid);
  }


?>