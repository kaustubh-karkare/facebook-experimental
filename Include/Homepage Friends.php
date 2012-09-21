<?php

function friendindex_display($self){
  return "<div id='friendindex'>".friendindex_refresh($self)."</div>";
  }

function friendindex_refresh($self){

  $flag=1;
  $f = db_get("Friends",$self,"Requests","");
  if(!is_array($f) || $f[0]=="") $f=array();
  $str.="<table class='friendindex' border=0>";
  $str.="<h2>Friend Requests</h2>";
  $x="";
  foreach($f as $i=>$j){
    $flag=0;
    $m = friend_list_mutual($self,$j);
    if(!is_array($m) || $m[0]=="") $m=array();
    $str.="<tr>";
    $str.="<td class='pic'>".user_photo($j,50,1)."</td>";
    $str.="<td class='info1'>".user_fullname($j,1);
      $link = popup_namelist("$j-mf","List of Mutual Friends",$m,$output);
      $str.="<br><span onClick='$link'>".((count($m)==1)?"1 mutual friend":count($m)." mutual friends")."</span>";
      $str.="</td>";
    $str.="<td><input type='button' value='Confirm' class='accept' onClick=\"ajax_post('Ajax.php?action=Friend-Accept','uid=$j&refresh=friendindex','friendindex',0);\" /></td>";
    $str.="<td><input type='button' value='Ignore' class='ignore' onClick=\"ajax_post('Ajax.php?action=Friend-Ignore','uid=$j&refresh=friendindex','friendindex',0);\" /></td>";
    $str.="</tr>";
    $x .= $output;
    }

  $f = db_get_ids("Users");
  if(!is_array($f) || $f[0]=="") $f=array();
  $a = array();
  foreach($f as $i) if(db_array_chk("Friends",$i,"Requests",$self)) $a[]=$i;
  foreach($a as $j){
    $flag=0;
    $m = friend_list_mutual($self,$j);
    if(!is_array($m) || $m[0]=="") $m=array();
    $str.="<tr>";
    $str.="<td class='pic'>".user_photo($j,50,1)."</td>";
    $str.="<td class='info1'>".user_fullname($j,1);
      $link = popup_namelist("$j-mf","List of Mutual Friends",$m,$output);
      $str.="<br><span onClick='$link'>".((count($m)==1)?"1 mutual friend":count($m)." mutual friends")."</span>";
      $str.="</td>";
    $str.="<td class='info2' colspan=2>Awaiting confirmation</td>";
    $str.="</tr>";
    $x .= $output;
    }
  if($flag==1) $str.="<tr><td colspan=4 class='info2'>None</td></tr>";
  $str.="</table> $x <br>";

  // --------------------------------------------------

  $f = friend_list_normal($self);
  if(!is_array($f) || $f[0]=="") $f=array();
  $u = db_get_ids("Users");
  if(!is_array($u) || $u[0]=="") $u=array();
  $n = db_get("Friends",$self,"Requests",array());
  if(!is_array($n) || $n[0]=="") $n=array();
  $m = array();
  foreach($u as $i) if(db_array_chk("Friends",$i,"Requests",$self)) $m[]=$i;
  $a = array_diff($u,array($self),$f,$n,$m);
  shuffle($a);

  $str.="<table class='friendindex' border=0>";
  $str.="<h2>Friends Suggestions</h2>";
  $x="";
  $flag=1;

  foreach($a as $i=>$j){
    if($i==3) break;
    $flag=0;
    $m = friend_list_mutual($self,$j);
    if(!is_array($m) || $m[0]=="") $m=array();
    $str.="<tr>";
    $str.="<td class='pic'>".user_photo($j,50,1)."</td>";
    $str.="<td class='info1'>".user_fullname($j,1);
      $link = popup_namelist("$j-mf","List of Mutual Friends",$m,$output);
      $str.="<br><span onClick='$link'>".((count($m)==1)?"1 mutual friend":count($m)." mutual friends")."</span>";
      $str.="</td>";
    $str.="<td colspan=2><input type='button' value='Add as Friend' class='accept' onClick=\"ajax_post('Ajax.php?action=Friend-Request','uid=$j&refresh=friendindex','friendindex',0);\" /></td>";
    $str.="</tr>";
    $x .= $output;
    }
  if($flag==1) $str.="<tr><td colspan=4 class='info2'>None</td></tr>";
  $str.="</table> $x <br>";

  // --------------------------------------------------

  $f = friend_list_normal($self);
  if(!is_array($f) || $f[0]=="") $f=array();

  for($i=0;$i<count($f);$i++)
    for($j=0;$j<$i;$j++){
      $name1 = db_get("Users",$f[$j],"First Name","")." ".db_get("Users",$f[$j],"Last Name","");
      $name2 = db_get("Users",$f[$j+1],"First Name","")." ".db_get("Users",$f[$j+1],"Last Name","");
      if(strcasecmp($name1,$name2)>0){ $t=$f[$j]; $f[$j]=$f[$j+1]; $f[$j+1]=$t; }
      }

  $str.="<table class='friendindex' border=0>";
  $str.="<h2>Friends</h2>";
  $x="";
  $flag=1;

  foreach($f as $i=>$j){
    $flag=0;
    $m = friend_list_mutual($self,$j);
    if(!is_array($m) || $m[0]=="") $m=array();
    $str.="<tr>";
    $str.="<td class='pic'>".user_photo($j,50,1)."</td>";
    $str.="<td class='info1'>".user_fullname($j,1);
      $link = popup_namelist("$j-mf","List of Mutual Friends",$m,$output);
      $str.="<br><span onClick='$link'>".((count($m)==1)?"1 mutual friend":count($m)." mutual friends")."</span>";
      $str.="</td>";
    $str.="<td class='info3'>Last Active : ".adate(db_get("Users",$j,"Last Active",""))."</td>";
    $str.="</tr>";
    $x .= $output;
    }
  if($flag==1) $str.="<tr><td colspan=4 class='info2'>None</td></tr>";
  $str.="</table> $x <br>";

  return $str;
  }


























function friend_read($self){
  $f = db_get("Friends",$self,"Requests",array());
  if(!is_array($f) || $f[0]=="" ) $f=array();
  if(count($f)>0) foreach($f as $i){
    $m = friend_list_mutual($self,$i);
    if(!is_array($m) || $m[0]=="") $m=array();
    foreach($m as $j=>$k) $m[$j]=user_fullname($k,0);
    $str.="<tr><td class='thin'>".user_photo($i,40,1)."</td>";
    $str.="<td>".user_fullname($i,1)."<br><n title='".implode($m,'; ')."'>".((count($m)==1)?"1 mutual friend":count($m)." mutual friends")."</n>";
    $str.="<td class='thin'><input type='button' value='Confirm' class='confirm' onClick=\"ajax_post('Ajax.php?action=Friend-Accept','uid=$i&refresh=friendrequests','friend-data',0);\" /></td>";
    $str.="<td class='thin'><input type='button' value='Ignore' class='ignore' onClick=\"ajax_post('Ajax.php?action=Friend-Ignore','uid=$i&refresh=friendrequests','friend-data',0);\" /></td>";
    $str.="</tr>";
    }
  else $str.="<tr><td>No Friend Requests as of now.</td></tr>";
  return "<table>$str</table>";
  }





?>