<?php

function popup_droplist1($id,$uid,$source,&$output){
  $str.="<script>mouseOver_account=0; function droplist_open_account(){ var e = document.getElementById(\"droplist-$id\"); var f = document.getElementById(\"$source\"); e.style.left=getXYpos(f).x-0; e.style.top=getXYpos(f).y-30; e.style.display=\"block\"; }</script>";
  $str.="<table class='droplist-account' id='droplist-account' border=0 onMouseOver='mouseOver_account=1;' onMouseOut='mouseOver_account=0;' >";
  $str.="<tr><td class='space' onClick='mouseOver_account=0; close_drop_lists();'></td> <td class='top' onClick='mouseOver_account=0; close_drop_lists();'><a>Account</a></td></tr>";
  $str.="<tr><td class='profile' colspan=2><span style='float:left;padding:4px;'>".user_photo($uid,50)."</span> <span style='position:relative;top:15px;'>".user_fullname($uid)."</td></tr>";
  $str.="<tr><td class='hr' colspan=2><div></div></td></tr>";
//  $str.="<tr><td class='option' colspan=2 onClick='window.location=\"Action.php?action=System-Refresh\";'>Flush Database</td></tr>";
  $str.="<tr><td class='option' colspan=2 onClick='window.location=\"?index=about\";'>About this Website</td></tr>";
  $str.="<tr><td class='option' colspan=2 onClick='window.location=\"Action.php?action=Account-Logout\";'>Logout</td></tr>";
  $str.="<tr><td class='bottom' colspan=2></td></tr>";
  $str.="</table>";
  $output = $str;
  global $close_drop_lists; $close_drop_lists.= " if(mouseOver_account==0)(document.getElementById(\"droplist-$id\")).style.display=\"none\"; ";
  return "droplist_open_account(); mouseOver_account=1;";
  }

function popup_droplist2($id,$head,$option,$foot,$text,$source,&$output){
  $str = "";
  $str.="<script>mouseOver_$id=0; function droplist_open_$id(){ var e = document.getElementById(\"droplist-".$id."\"); var f = document.getElementById(\"$source\"); e.style.left=getXYpos(f).x; e.style.top=getXYpos(f).y-33; e.style.display=\"block\"; }</script>";
  $str.="<form action='$action' method='post'><table class='droplist-top' id='droplist-$id' onMouseOver='mouseOver_$id=1;' onMouseOut='mouseOver_$id=0;'>";
  $str.="<tr><td class='top'><a onClick='mouseOver_$id=0;close_drop_lists();'><img src='Images/System/top-$id-selected.png'></a></td>";
  $str.="<td class='space' onClick='mouseOver_$id=0;'><div></div></td></tr>";
  $str.="<tr><td colspan=2 class='head'> <table width=100% border=0><tr><td class='left'>$head</td><td class='right'>$option</td></tr></table> </td></tr>";
  $str.="<tr><td colspan=2 class='text'><div id='$id-data'>$text</div> <img id='whiteline' src='Images/System/top-whiteline.png' style='position:absolute;top:31;left:1;'></td></tr>";
  $str.="<tr><td colspan=2 class='foot'>$foot</td></tr>";
  $str.="</table></form>";
  $output = $str;
  global $close_drop_lists; $close_drop_lists.= " if(mouseOver_$id==0) (document.getElementById(\"droplist-".$id."\")).style.display=\"none\"; ";
  return "droplist_open_$id(); mouseOver_$id=1;";
  }

function popup_droplist3($id,$uid,$source,&$output){
  $str = "";
  $str.="<script>mouseOver_search=0; function droplist_open_search(){ var e = document.getElementById(\"droplist-$id\"); var f = document.getElementById(\"$source\"); e.style.left=getXYpos(f).x+5; e.style.top=getXYpos(f).y-5; e.style.display=\"block\"; }";
  $str.="function asr(query){ ajax_post('Ajax.php?action=Search-Basic','query='+query,'asr',0); } </script>";
  $str.="<table class='droplist-search' id='droplist-search' border=0 onMouseOver='mouseOver_search=1;' onMouseOut='mouseOver_search=0;'>";
  $str.="<tr><td class='text' style='padding:0px;'><div id='asr'></div></td></tr>";
  $str.="</table>";
  $output = $str;
  global $close_drop_lists; $close_drop_lists.=" if(mouseOver_search==0) (document.getElementById(\"droplist-search\")).style.display=\"none\"; ";
  return "droplist_open_search(); mouseOver_search=1;";
  }

function popup_droplist4($id,$text,$action,$source,&$output){
  $str = "";
  $str.="<script>mouseOver_$id=0; function droplist_open_$id(){ var e = document.getElementById(\"droplist-".$id."\"); var f = document.getElementById(\"$source\"); e.style.left=getXYpos(f).x+".(($source=="pc-editbutton-info")?"17":"38")."; e.style.top=getXYpos(f).y-21; e.style.display=\"block\"; }</script>";
  $str.="<form action='$action' method='post'><table class='droplist-container' id='droplist-$id' onMouseOver='mouseOver_$id=1;' onMouseOut='mouseOver_$id=0;'>";
  $str.="<tr><th class='top'><img src='Images/System/box-edit-highlight.png'></th><td class='head'>Edit Box</td></tr>";
  $str.="<tr><td rowspan=2 class='space' onClick='mouseOver_$id=0; close_drop_lists();'></td>";
  $str.="<td class='data'><div>$text</div></td></tr><tr><td class='foot'>";
  $str.="<input type='submit' value='OK' /> <input type='button' value='Cancel' onClick='mouseOver_$id=0; close_drop_lists();' /></td></tr></table></form>";
  $output = $str;
  global $close_drop_lists; $close_drop_lists.= " if(mouseOver_$id==0) (document.getElementById(\"droplist-".$id."\")).style.display=\"none\"; ";
  return "droplist_open_$id(); mouseOver_$id=1;";
  }


?>