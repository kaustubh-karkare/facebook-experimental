<?php

function html_start1(){
  // $str.= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml'><title>Facebook Experimental</title>";
  $str = "<html><head><title>Facebook Experimental</title>";
  $files = folder_get("Scripts",0); foreach($files as $file) $str.= "<script src='Scripts/$file' type='text/javascript'></script>";
  $files = folder_get("Styles",0); foreach($files as $file) $str.= "<link rel='stylesheet' type='text/css' href='Styles/$file' />";
  $str.= "<link rel='shortcut icon' href='Images/System/logo-f.png' />";
  $str.= "</head>";
  return $str;
  }












function html_start2(){
  $uid = user_online();

  $link[1] = popup_droplist1('account',user_online(),'droplist-account-position',$output1);
  $na = " Feature Non-Functional on IE, Mozilla & Seamonkey.";
  $link[2] = popup_droplist2('friend','Friend Requests','<a></a>','<a href=\'?index=friends\'>View all Friends</a>','Loading Friend Requests ... '.$na,'droplist-friend-position',$output2)." ajax_post(\"Ajax.php?action=FriendRequest-Get\",\"\",\"friend-data\",0);";
  $link[3] = popup_droplist2('message','Messages',"<a onClick='".popup_message($uid,'top','',$output6)."'>Send A Message</a>","<a href='?index=messages'>View all Messages</a>",'Loading Messages ... '.$na,'droplist-message-position',$output3)." ajax_post(\"Ajax.php?action=Message-Get\",\"\",\"message-data\",0); ";
  $link[4] = popup_droplist2('notice','Notifications',"<a onClick='ajax_post(\"Ajax.php?action=Notifications-Clear\",\"\",\"notice-data\",0);'>Clear Notifications</a>",'<a href=\'?index=notifications\'>View All Notifications</a>','Loading Notifications ... '.$na,'droplist-notice-position',$output4)." ajax_post(\"Ajax.php?action=Notifications-Get\",\"\",\"notice-data\",0);";
  $link[5] = popup_droplist3('search',user_online(),'droplist-search-position',$output5);

  $str = "<body onClick='close_drop_lists();'>";
  $str.= "<table style='width:100%;' class='main-body' border=0>";
  $str.= "<tr><td class='mb01'></td><td class='mb01'>";

  // ----------------

  $str.= "<table class='lightblue' border=0><tr><td class='left1'>";
  $str.= "<a href='Construct.php'><img src='Images/System/top-logo.png'></a></td>";
  foreach(array("friend","message","notice") as $i=>$value)
    $str.= "<td class='left2'><a onClick='".$link[$i+2]."'><img src=\"Images/System/top-$value.png\" onMouseOver=\"this.src='Images/System/top-$value-highlight.png';\" onMouseOut=\"this.src='Images/System/top-$value.png';\" /></a></td> ";
  $str.= "<td class='left3'></td>";

  $str.= "<td class='center'> <form name='searchbox' action='Action.php?action=Search-Perform' method='post'>";
  $str.= "<input class='text' type='text' name='query' onClick='asr(this.value);".$link[5]."' onBlur='close_drop_lists();' onKeyUp='asr(this.value);' />";
  $str.= "<input class='image' type='image' src='Images/System/top-search.png' />";
  $str.= "</form> </td>";

  $str.= "<td class='right' onClick='window.location=\"Construct.php\"'><a>Home</a></td>";
  $str.= "<td class='right' onClick='window.location=\"Construct.php?uid=$uid\"'><a>Profile</a></td>";
  $str.= "<td class='right' onClick='".$link[1]."'><a>Account</a></td>";
  $str.= "</tr><tr><td></td>";
  $str.= "<td><div id='droplist-friend-position'></div></td>";
  $str.= "<td><div id='droplist-message-position'></div></td>";
  $str.= "<td><div id='droplist-notice-position'></div></td>";
  $str.= "<td></td>";
  $str.= "<td><div id='droplist-search-position'></div></td>";
  $str.= "<td></td>";
  $str.= "<td class='rightdown'><div id='droplist-account-position'></div></td></tr></table>";
  $str.= " $output1 $output2 $output3 $output4 $output5 $output6";

  $str.= "<td class='mb01'></td></tr>";
  //$str.= "<tr><td colspan=3 class='mb02'></td></tr>";
  $str.= "<tr><td class='mb03'></td><td class='mb04'>";

  return $str;
  }













function html_end(){
  global $close_drop_lists, $popupstream;
  $uid = user_online();
  $str = "</td><td class='mb03'></td></tr>";
  $str.= "<tr><td class='mb03'></td><td class='mb04'>";
  $str.= html_credits();
  $str.= "</td><td class='mb03'></td></tr>";
  $str.= "<script>function close_drop_lists(){ $close_drop_lists }</script>";
  $str.= chat_initiate($uid);
  $str.= "</table> $popupstream ".display_notice();
  $str.= "</body></html>";

  return $str;
  }













function html_credits(){
  $str = "<table class='credits' border=0><tr><td class='left'>This website has been designed to look and function as close to the real <a href='http://www.facebook.com'>Facebook</a> as possible. Please note that no copyright infringement is intended as this is purely a learning experiment. Further, please understand that the creator of this website has access to all information stored on the server for obvious reasons, and hence please do not upload/write anything on this website that you wish to keep absolutely confidential.</td>";
  $str.= "<td class='right'>Created by Kaustubh Karkare<br>ECE 2K9 Batch, Birla Institute of Technology, Mesra</td></tr></table>";
  return $str;
  }











function html_login(){
  $str ="<body style=\"background: url('Image.php?sys=login-bg') 0 80 repeat-x;\" >";
  $str.="<table class='login-page' border=0>";
  $str.="<form method='post' action='Action.php?action=Account-Login'>";
  $str.="<tr><td rowspan=3 class='sides-top'></td>";
    $str.="<td class='l11' rowspan=3><div><img src='Images/System/login-logo3.png'></div></td>";
    $str.="<td class='l12'><div><a>Email</a></div></td>";
    $str.="<td class='l12'><div><a>Password</a></div></td>";
    $str.="<td class='l12'></td>";
    $str.="<td rowspan=3 class='sides-top'></td>";
    $str.="</tr>";
  $str.="<tr>";
    $str.="<td class='l22'><input type='text' name='emailadd' value='' id='emailadd' /></td><script>(document.getElementById('emailadd')).select();</script>";
    $str.="<td class='l22'><input type='password' name='password' value='' /></td>";
    //$str.="<td class='l22'><input type='text' name='emailadd' value='mikhail.romanov@kgb.gov.ru' id='emailadd' /></td><script>(document.getElementById('emailadd')).select();</script>";
    //$str.="<td class='l22'><input type='password' name='password' value='motherrussia' /></td>";
    $str.="<td class='l23'><input type='submit' value='Login' /></td>";
    $str.="</tr>";
  $str.="<tr>";
    if($_SESSION['loginerror']=="") $_SESSION['loginerror']="Please remember that this is not the Real Facebook !!! ";
    $str.="<td class='l32' colspan=3>".$_SESSION['loginerror']."</td>"; $_SESSION['loginerror']="";
    //$str.="<td class='l32'><div><input type='checkbox' name='nologout'> <a>Keep me logged in</a></div></td>";
    //$str.="<td class='l32'><div><a>Forgot your password?</a></div></td>";
  $str.="</form>";
  $str.="<tr><td rowspan=100 class='sides'></td>";
    $str.="<td class='l41'>Facebook helps you connect and share<br>with the people in your life.<br><br><img src='Images/System/login-world.png' /></td>";
    $str.="<td class='l42' colspan=3><div>".display_CreateAccount()."</div></td>";
    $str.="<td rowspan=100 class='sides'></td>";
    $str.="</tr>";
  $str.="<style>table.credits { width:1000px; height:100px; border-top:0px solid #B3B3B3; }</style>";
  $str.="<tr><td colspan=4>".html_credits()."</td></tr>";
  $str.="</table>";
  return $str;
  }






function html_accessdenied(){
  $str ="<html><head><title>Supernova Reignited : Access Denied!</title></head><body><br><br>";
  $str.="<center><span style='font-family:Verdana;font-size:16px;'>Facebook Experimental : Access Denied!</span></center><br>";
  $str.="<center><span style='font-family:Verdana;font-size:10px;'>You do not have the permissions to directly access the contents of this folder!</span></center>";
  $str.="</body></html>";
  return $str;
  }












function display_about(){
  $str ="<style>
table.about-container { width:800px; margin:10px; }
table.about-container td.ha1 { padding:10px; }
table.about-container td.ha1 h2 { font-weight:bold; color:black; margin-top:10px; }
table.about-container td.ha1 p { text-indent:50px; margin-top:10px; text-align:justify; }
</style>";
  $str.="<table class='about-container'>";
  $str.="<tr><td class='ha1'>";
  $str.=file_get("Include/About.txt");
  $str.=comment_display(user_online(),"X1");
  $str.="</td></tr>";
  $str.="</table>";
  return $str;
  }

?>