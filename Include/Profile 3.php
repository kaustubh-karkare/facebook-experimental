<?php

function update_about($uid,$text){
  db_load("Profiles");
  db_set("Profiles",$uid,"About",text_screen($text));
  db_save("Profiles");
  }

function profile_edit1(){
  $uid = user_online();
  foreach($_POST as $key=>$value){
    if(!in_array($key, array("First_Name","Last_Name","Current_Location","Sex","dob-date","dob-month","dob-year","oldpass0","newpass1","newpass2") ))
      db_set("Profiles",$uid,eregi_replace("_"," ",$key),text_screen($value));
    else {
      if($key=="newpass1" || $key=="newpass2") continue;
      if($key=="oldpass0"){
        if($value==db_get("Users",$uid,"Password","") && $_POST["newpass1"]==$_POST["newpass2"] && $_POST["newpass1"]!=""){ db_set("Users",$uid,"Password",$_POST["newpass1"]); $_SESSION["notice"].="Your password has been changed.".NL; }
        else { $_SESSION["notice"].="Your password has not been changed.".NL;
               if($value==db_get("Users",$uid,"Password","")) $_SESSION["notice"].="Current Passsword does not match records!".NL;
               else if($_POST["newpass1"]=="") $_SESSION["notice"].="Password fields cannot be left empty!".NL;
               else if($_POST["newpass1"]==$_POST["newpass2"]) $_SESSION["notice"].="Mismatch in New Password Fields!".NL;
          }
        }
      else if($key == "First_Name" || $key=="Last_Name")
        db_set("Users",$uid,eregi_replace("_"," ",$key),eregi_replace("[^A-Za-z0-9 ]","",$value));
      else db_set("Users",$uid,eregi_replace("_"," ",$key),text_screen($value));
      }
    } // foreach
  if(!empty($_POST["dob-year"])){
    $value = mktime(0,0,0,$_POST["dob-month"],$_POST["dob-date"],$_POST["dob-year"]);
    db_set("Users",$uid,"Birthday",$value);
    }
  } // fn


function profile_edit2($action){
  $uid = user_online();
  file_delete("Images/Profile/$uid.jpg");
  file_delete("Images/Profile/$uid.png");
  file_delete("Images/Profile/$uid.gif");
  if($action) file_upload("profile-picture","Images/Profile/$uid","image/jpeg,image/png,image/gif",2*1024*1024);
  }

function profile_edit3(){
  $uid = user_online();
  $list1=array();
  $list2=array();
  $list3=array();
  foreach($_POST as $key=>$value){
    if(eregi("^DBA-",$key)){ $list1[]=eregi_replace("_"," ",eregi_replace("DBA-","",$key)); }
    if(eregi("^DBU-",$key)){ $list2[]=eregi_replace("_"," ",eregi_replace("DBU-","",$key)); }
    if(eregi("^DBP-",$key)){ $list3[]=eregi_replace("_"," ",eregi_replace("DBP-","",$key)); }
    }

  db_set("Profiles",user_online(),"DBF",$_POST["DBF-X"]);
  if($_POST["type"]==1) db_set("Profiles",$uid,"DBA",$list1);
  if($_POST["type"]==2) db_set("Profiles",$uid,"DBU",$list2);
  if($_POST["type"]==2) db_set("Profiles",$uid,"DBP",$list3);
  }

?>