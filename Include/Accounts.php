<?php

function display_Login(){
  if(user_online()){
    global $session;
    $uid = user_online();
    $str.="<form method='post' action='Action.php?action=Account-Logout'>";
    $str.="<table>";
    $str.="<tr><th colspan=2>Account Details</th></tr>";
    $str.="<tr><td>Name</td><td><a href='Construct.php?uid=$uid'>".user_fullname($uid,0)."</a></td></tr>";
    $str.="<tr><td></td><td><input type='submit' value='Log Out'></td></tr>";
    $str.="</table>";
    }
  else {
    $str.="<form method='post' action='Action.php?action=Account-Login'>";
    $str.="<table class='Login'>";
    $str.="<tr><th colspan=2>Login Box</th></tr>";
    $str.="<tr><td>Email Address</td><td><input name='emailadd' value=''></td></tr>";
    $str.="<tr><td>Password</td><td><input name='password' type='password' value=''></td></tr>";
    $str.="<tr><td></td><td><input type='submit' value='Log In'></td></tr>";
    $str.="</table></form>";
    }
  return $str;
  }

function action_Login(){
  global $session,$time;
  $emailadd = $_POST["emailadd"];
  $password = $_POST["password"];
  $error=0;
  if($emailadd==""){ $_SESSION["loginerror"].= "Login Error : EMail Address Field Field Empty!"; return; }
  if($password==""){ $_SESSION["loginerror"].= "Login Error : Password Field Empty!"; return; }
  if($error) return;
  $uid = db_search("Users","UID","EMail",$emailadd);
  $ip = $_SERVER['REMOTE_ADDR']; $time = time()+5.5*60*60;
  if(db_get("Users",$uid,"Password",-1)!=$password){ $_SESSION["loginerror"].= "Login Error : Incorrect Username/Password!"; return; }
  // if($uid==user_online()){ $_SESSION["loginerror"].= "Login Error : Multiple Login Not Allowed!"; return;  }
  if(db_get("Users",$uid,"Password","Normal")=="Blocked"){ $_SESSION["loginerror"].= "Login Error : Account Blocked!"; return; }
  db_set("Users",$uid,"Session",$session);
  db_set("Users",$uid,"Last Active",$time);
  db_set("Users",$uid,"IP Address",$_SERVER["REMOTE_ADDR"]);
  db_array_add("Users",$uid,"IP Addresses Used",$_SERVER["REMOTE_ADDR"]);
  chat_login($uid);
  $_SESSION["loginerror"] = 100;
  }

function action_Logout(){
  global $time;
  $ip = $_SERVER['REMOTE_ADDR'];
  $uid = user_online();
  db_set("Users",$uid,"Last Active",$time);
  db_set("Users",$uid,"Session","");
  db_set("Users",$uid,"IP Address","");
  session_regenerate_id();
  chat_logout($uid);
  }

// -------------------------------------------------------------------------

function action_CreateAccount(){
  $error=0;
  if($_POST["FirstName"]==""){ $str.="First Name Empty!".NL; $error=1; }
  if($_POST["LastName"]==""){ $str.="Last Name Empty!".NL; $error=1;; }
  if($_POST["EMail"]==""){ $str.="EMail Empty!".NL; $error=1; }
  if($_POST["Sex"]!="Male" && $_POST["Sex"]!="Female"){ $str.="Sex Not Selected!".NL; $error=1; }
  if($_POST["dob-month"]==0){ $str.="Birth Month Not Selected!".NL; $error=1; }
  if($_POST["dob-date"]==0){ $str.="Birth Date Not Selected!".NL; $error=1; }
  if($_POST["dob-year"]==0){ $str.="Birth Year Not Selected!".NL; $error=1; }
  if($_POST["Password1"]==""){ $str.="Password 1 Empty!".NL; $error=1; }
  if($_POST["Password2"]==""){ $str.="Password 2 Empty!".NL; $error=1; }
  if($_POST["Password1"]!=$_POST["Password2"] ){ $str.="Password Mismatch!".NL; $error=1; }

  global $database;
  if(db_search("Users","UID","EMail",$_POST["EMail"])!=-1){ echo "EMail Address already Registered!".NL; $error=1; }

  if($error){ $_SESSION["notice"].="Error : Could Not Create Account!".NL.$str; return; }

  $user["UID"] = user_next();
  $user["Account Status"] = "New";
  $user["First Name"] = $_POST["FirstName"];
  $user["Last Name"] = $_POST["LastName"];
  $user["Sex"] = $_POST["Sex"];
  $user["Birthday"] = mktime(0,0,0,$_POST["dob-month"],$_POST["dob-date"],$_POST["dob-year"]);
  $user["EMail"] = $_POST["EMail"];
  $user["Password"] = $_POST["Password1"];
  $user["Access"] = 1;
  $user["Points"] = 100;
  $user["Last Active"] = time()+5.5*60*60;
  $user["Session"] = 0;
  user_add($user);
  $_SESSION["notice"].= "Account Created : Please login using the details you entered below.".NL;
  }

function display_CreateAccount(){
  $str.="<form method='post' action='Action.php?action=Account-Create'>";
  //$str ="<form method='post' onSubmit=\"alert('Unfortunately, the registeration system is currently disabled, given that the website is currently operating in a passive mode with minimal features (for exhibition purposes only). Please use the test account, the login details of which are available by default in the relevant input fields.');\">";
  $str.="<table class='CreateAccountBox' border=0>";
  $str.="<tr><td class='ca11' colspan=2>Sign Up</td></tr>";
  $str.="<tr><td class='ca12' colspan=2>It's free, and always will be.</td></tr>";
  $str.="<tr><td class='ca21'>First Name : </td><td class='ca22'><input class='fullwidth' name='FirstName' /></td></tr>";
  $str.="<tr><td class='ca21'>Last Name : </td><td class='ca22'><input class='fullwidth' name='LastName' /></td></tr>";
  $str.="<tr><td class='ca21'>Your Email : </td><td class='ca22'><input class='fullwidth' name='EMail' /></td></tr>";
  $str.="<tr><td class='ca21'>Password : </td><td class='ca22'><input class='fullwidth' name='Password1' type='password' /></td></tr>";
  $str.="<tr><td class='ca21'>Retype Password : </td><td class='ca22'><input class='fullwidth' name='Password2' type='password' /></td></tr>";
  $str.="<tr><td class='ca21'>I am : </td><td class='ca22'><select class='fullwidth' name='Sex'><option>Select Sex: </option><option>Male</option><option>Female</option></td></tr>";
  $str.="<tr><td class='ca21'>Birthday : </td><td class='ca22'>";
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    $str.=" <select name='dob-month'><option value=0>Month:</option>"; foreach($months as $i=>$j) $str.="<option value=".($i+1).">$j</option>"; $str.="</select>";
    $str.=" <select name='dob-date'><option value=0>Date:</option>"; for($i=1;$i<=31;$i++) $str.="<option>$i</option>"; $str.="</select>";
    $str.=" <select name='dob-year'><option value=0>Year:</option>"; for($i=1980;$i<=2010;$i++) $str.="<option>$i</option>"; $str.="</select>";
  $str.="</td></tr>";
  $str.="<tr><td></td><td class='ca31'><input type='submit' value='Sign Up'></td></tr>";
  $str.="<tr><td class='ca41' colspan=2></td></tr>";
  $str.="<tr><td class='ca42' colspan=2></td></tr>";
  $str.="<tr><td class='ca43' colspan=2><a href='Preview.php'>Click here if you wish to check this site out<br>without having to make an Account.</a></td></tr>";
  $output="";
  $str.="</table></form> $output";
  return $str;
  }

function clientdata($self,$browser,$version,$os){
  db_set("Users",$self,"Browser","$browser $version");
  db_array_add("Users",$self,"Browsers Used","$browser $version");
  db_set("Users",$self,"OS",$os);
  db_array_add("Users",$self,"OSs Used",$os);
  }

?>