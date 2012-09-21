<?php

function popup_namelist($id,$heading,$uid,&$output){
  $str = "";
  $str.="<table class='popup-container' id='popup-".$id."'>";
  $str.="<tr><td class='sides'></td><td class='mid'>";

  $str.="<table class='border'><tr><td>"; // border box start

  $str.="<table class='popup-box'>";
  $str.="<tr><td class='head'>".$heading."</td></tr>";
  $str.="<tr><td class='data'><div class='con'><table>";
  if(is_array($uid)&&count($uid)>0)
  for($i=0;$i<count($uid);$i++){
    $str.="<tr><td>".user_photo($uid[$i],50)."</td>";
    $str.="<td class='fullwidth'>".user_fullname($uid[$i]);
    $str.="</td></tr>";
    }
  else $str.="<tr><td>None</td></tr>";
  $str.="</table></div></td></tr>";
  $str.="<tr><td class='foot'><input type='button' value='Close' class='close' onClick='(document.getElementById(\"popup-".$id."\")).style.display=\"none\";'></td></tr>";
  $str.="</table>";

  $str.="</td></tr></table>"; // border box end

  $str.="</td><td class='sides'></td></tr></table>";

  $output = $str;
  return "(document.getElementById(\"popup-".$id."\")).style.display=\"block\";";
  }



function popup_text($id,$heading,$text,$button,$action,$onclick,&$output){
  $str = "";
  $str.="<table class='popup-container' id='popup-".$id."'>";
  if($id=="create-ads") $str.="<form action='$action' method='post' name='create_ads'>";
  else $str.="<form action='$action' method='post'>";
  $str.="<tr><td class='sides'></td><td>";

  $str.="<table class='border'><tr><td>"; // border box start

  $str.="<table class='popup-box'>";
  $str.="<tr><td class='head'>".$heading."</td></tr>";
  $str.="<tr><td class='data'><div class='con'>";

  $str.=$text;

  $str.="</div></td></tr>";
  $str.="<tr><td class='foot'>";
  if($action!="") $str.="<input type='submit' value='$button' class='close' onClick=\"(document.getElementById('popup-".$id."')).style.display='none'; $onclick \"> ";
  $str.="<input type='button' value='Close' class='close' onClick='(document.getElementById(\"popup-".$id."\")).style.display=\"none\";'></td></tr>";
  $str.="</table>";

  $str.="</td></tr></table>"; // border box end

  $str.="</td><td class='sides'></td></tr></form></table>";

  $output = $str;
  return "(document.getElementById(\"popup-".$id."\")).style.display=\"block\";";
  }

?>