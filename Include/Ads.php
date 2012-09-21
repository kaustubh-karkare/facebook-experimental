<?php

// db_create("Ads","BID,UID,Status,Head,Info,More,Like,Dislike");

function ad_demon(){
  return;
  $head[0]="File 666";		$info[0] = "File 666 is a Level 10 Classified Document (please note that the President of the USA only has a Level 7 clearance) compiled by the United Nations Military Command in the year 2037 about the cause of a series of anomalies in the physical laws of our Universe. These anomalies include the time slip that shifted the document from the future to the present, and the termination of the future from which it originated.";
  $head[1]="File 666.02";	$info[1] = "Very few people are privy to the existance of the true form of Neeraj Pradhan, the powerful and evil being in the entire Multiverse.";
  $head[2]="File 666.13";	$info[2] = "Neeraj Pradhan was originally born as a T-Rex. When he got bored of dinosaurs, he summoned a meteor to wipe them out and make way for humans (and took on a human form to blend in with the idiots).";
  $head[3]="File 666.21";	$info[3] = "The reason that miracles dont occur in the universe is that God himself is scared to crap of Neeraj Pradhan, and avoids anything to do with this world.";
  $head[4]="File 666.27";	$info[4] = "Whenever Neeraj Pradhan gets bored, he obliterates alternate realities. Reasons for the continued existance of our reality are still unclear.";
  $head[5]="File 666.28";	$info[5] = "All the theories that the best human astrophysicists have put forward about how the universe was created are false. The universe exists because Neeraj Pradhan wanted entertainment.";
  $head[6]="File 666.32";	$info[6] = "Neeraj Pradhan's will is indistinguishable from objective reality. Any violators are terminted via coincidental events in the natural course of reality.";
  $head[7]="Fringe";		$info[7] = "Season 03 Begins on 23 September 2010 ... highly recommended!";
  $head[8]="Deathnote";		$info[8] = "This is the best anime series ever ... must watch for all!";
  for($i=0;$i<=8;$i++){
    db_addrow("Ads",array("BID"=>($i+1),"UID"=>1,"Status"=>"Normal","Head"=>$head[$i],"Info"=>$info[$i] ));
    }
  }






function ad_create($self,$head,$info,$more){

  $defaulttext1 = "Enter Advertisement Heading";
  $defaulttext2 = "Enter the text that will be visible on the right hand side pane below the Advertisement heading.";
  $defaulttext3 = "This text will appear when a user clicks on an Advertisement in search of more Information regarding your Advertisement.";

  if($head==$defaulttext1 || $head=="" || $info==$defaulttext2 || $info=="") return;

  $a["BID"]=count(db_get_ids("Ads"))+1;
  $a["UID"]=$self;
  $a["Status"]="Waiting";
  $a["Head"]=text_screen($head);
  $a["Info"]=text_screen($info);
  $a["More"]=text_screen($more);

  db_addrow("Ads",$a);
  }




















function ad_adminindex(){
  $b = db_get_ids("Ads");
  if(!is_array($b) || $b[0]=="" ) $b=array();
  $b = array_reverse($b);

$str.="<style>
table.ad-adminindex { width:795px; margin:10px; }
table.ad-adminindex * { font-family:'Tahoma'; font-size:12px; padding:2px; }
table.ad-adminindex input { width:100%; }
table.ad-adminindex select { width:100%; }
table.ad-adminindex textarea { width:100%; height:30px; }
table.ad-adminindex textarea:focus { height:100px; }

table.ad-adminindex td.Normal { background:#DDFFDD; } table.ad-adminindex td.Normal * { background:#DDFFDD; }
table.ad-adminindex td.Waiting { background:#DDDDFF; } table.ad-adminindex td.Waiting * { background:DDDDFF; }
table.ad-adminindex td.Rejected { background:#FFDDDD; } table.ad-adminindex td.Rejected * { background:#FFDDDD; }
</style>";

  $str.="<table class='album-head-container' border=0><th>Admin Mode : Advertisements</th></table>";

  $str.="<form name='ad_admin' action='Action.php?action=Ad-Update' method='post'>";
  $str.="<input type='hidden' name='bid' value='' />";
  $str.="<input type='hidden' name='command' value='' />";
  $str.="<input type='hidden' name='text' value='' />";
  $str.="</form> <table class='ad-adminindex' border=1>";

// BID,UID,Status,Head,Info,More,Like,Dislike")

  $str.="<tr><th rowspan=3>Ad ID</th><th>Status</th><th>Heading</th><th>Like</th><th>Dislike</th></tr>";
  $str.="<tr><th colspan=4>About</th></tr><tr><th colspan=4>Information</th></tr>";
  foreach($b as $bid){
    // $str.="<tr><td colspan=5></td></tr>";
    $status = db_get("Ads",$bid,"Status","");
    $uid = db_get("Ads",$bid,"UID","");
    $str.="<tr><td rowspan=3 class='$status'>$bid</td>";
    $str.="<td class='$status'>".user_fullname($uid)."</td>";
    $str.="<td class='$status'><input type='text' value='".db_get("Ads",$bid,"Head","")."' onChange=\"e=document.ad_admin; e.bid.value='$bid'; e.command.value='Head'; e.text.value=this.value; e.submit(); \" /></td>";
    $str.="<td class='$status'><select onChange=\"e=document.ad_admin; e.bid.value='$bid'; e.command.value='Status'; e.text.value=this.value; e.submit(); \">";
      $x = array("Normal","Waiting","Rejected");
      foreach($x as $i){ if(db_get("Ads",$bid,"Status","")==$i) $str.="<option selected='selected'>$i</option>"; else $str.="<option>$i</option>"; }
      $str.="</select></td>";
    $str.="<td class='$status'><select>";
      $like = db_get("Ads",$bid,"Like",""); if(!is_array($like) || $like[0]=="") $like = array();
      $str.="<option>".((count($like)==1)?"1 Person":count($like)." People")."</option>";
      foreach($like as $i) $str.="<option>".user_fullname($i)."</option>";
      $str.="</select></td>";
    $str.="<td class='$status'><select>";
      $dislike = db_get("Ads",$bid,"Dislike",""); if(!is_array($dislike) || $dislike[0]=="") $dislike = array();
      $str.="<option>".((count($dislike)==1)?"1 Person":count($dislike)." People")."</option>";
      foreach($dislike as $i) $str.="<option>".user_fullname($i)."</option>";
      $str.="</select></td>";
    $str.="</tr>";
    $str.="<tr><td colspan=5 class='$status'><textarea onChange=\"e=document.ad_admin; e.bid.value='$bid'; e.command.value='Info'; e.text.value=this.value; e.submit(); \">".db_get("Ads",$bid,"Info","")."</textarea></td></tr>";
    $str.="<tr><td colspan=5 class='$status'><textarea onChange=\"e=document.ad_admin; e.bid.value='$bid'; e.command.value='More'; e.text.value=this.value; e.submit(); \">".db_get("Ads",$bid,"More","")."</textarea></td></tr>";
    }
  $str.="</table>";

  return $str;
  }


















function ad_display($width,$number){
  $a = db_get_ids("Ads");
  if(!is_array($a)||$a[0]=="") $a = array();
  shuffle($a);

  $str.="<table class='ad-container' style='width:".$width."px;'>";
  $str.="<tr><tr><th>Advertisements</th></tr>";
  $k=0; $output="";
  foreach($a as $i){
    if(db_get("Ads",$i,"Status","")!="Normal") continue;
    if($k==$number) break; $k++;
    $str.="<tr><td><a>".db_get("Ads",$i,"Head","")."</a>";
    $str.="<br>".db_get("Ads",$i,"Info","");
    $str.="</td></tr>";
    }

  $text = "<table>";
    $text.="<tr><td colspan=2>If either the 'Heading' or 'About' fields are empty, your advertisement shall not be submitted. However, the 'More Information' field can be left blank.</td></tr>";
    $defaulttext1 = "Enter Advertisement Heading";
    $text.="<tr><th>Heading</th><td><textarea name='head2' style='color:#808080;' onClick=\"if(this.value=='$defaulttext1'){ this.value=''; this.style.color='black'; } \" onBlur=\"if(this.value==''){ this.value='$defaulttext1'; this.style.color='#808080'; } \" >$defaulttext1</textarea></td></tr>";
    $defaulttext2 = "Enter the text that will be visible on the right hand side pane below the Advertisement heading.";
    $text.="<tr><th>About</th><td><textarea name='info' style='color:#808080;' onClick=\"if(this.value=='$defaulttext2'){ this.value='';  this.style.color='black'; } \" onBlur=\"if(this.value==''){ this.value='$defaulttext2'; this.style.color='#808080'; } \" >$defaulttext2</textarea></td></tr>";
    $defaulttext3 = "This text will appear when a user clicks on an Advertisement in search of more Information regarding your Advertisement.";
    $text.="<tr><th>More Information</th><td><textarea name='more' style='color:#808080;' onClick=\"if(this.value=='$defaulttext3'){ this.value='';  this.style.color='black'; } \" onBlur=\"if(this.value==''){ this.value='$defaulttext3'; this.style.color='#808080'; } \" >$defaulttext3</textarea></td></tr>";
    $text.="<tr><td colspan=2>Please note that your Advertisement is subject to moderation and possible modification, and may not start appearing until after a while, assuming that it is approved.</td></tr>";
    $text.="</table>";
  $onclick=" e=document.create_ads; if(e.head.value='$defaulttext1') e.head.value=''; if(e.about.value='$defaulttext2') e.about.value=''; if(e.info.value='$defaulttext3') e.info.value=''; ";

  $link = popup_text("create-ads","Create New Advertisements",$text,"Submit","Action.php?action=Ad-Create",$onclick,$o); $output.=$o;
  $str.="<tr><td class='bottom'><a onClick='$link'>Create your own Advertisement</a></td></tr>";
  $str.="</table> $output";
  return $str;
  }


?>