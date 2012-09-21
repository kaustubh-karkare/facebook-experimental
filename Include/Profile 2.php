<?php

function profile_extract($uid){
  $col = db_get_cols("Profiles");
  foreach($col as $field)
    $profile[$field]=db_get("Profiles",$uid,$field,"");
  if(is_array($profile["DBA"]))
    foreach($profile["DBA"] as $i=>$j)
      if(!friend_check($uid,$j))
        unset($profile["DBA"][$i]);
  return $profile;
  }

function display_information($uid){
  global $profile;
  $self = user_online();

$str="";

  if(1){
    $str.="<table class='info-container'>";
    if($uid==$self) $str.="<tr><th class='subhead'>About Me</th><td class='subhead'><a href='?edit=basicinfo'><img src='Images/System/profile-edit.png'> Edit</a></td></tr>";
    else $str.="<tr><th class='subhead'>About Me</th><td class='subhead'></td></tr>";
    $str.="<tr><th>First Name</th><td>".db_get("Users",$uid,"First Name","")."</td></tr>";
    $str.="<tr><th>Last Name</th><td>".db_get("Users",$uid,"Last Name","")."</td></tr>";
    $str.="<tr><th>Sex</th><td>".db_get("Users",$uid,"Sex","")."</td></tr>";
    $str.="<tr><th>Birthdate</th><td>".bdate(db_get("Users",$uid,"Birthday",""))."</td></tr>";
    if($profile["Relationship Status"]!="") $str.="<tr><th>Relationship Status</th><td>".$profile["Relationship Status"]."</td></tr>";

    if(db_get("Users",$uid,"Current Location","")!="") $str.="<tr><th>Current Location</th><td>".db_get("Users",$uid,"Current Location","")."</td></tr>";
    if($profile["Hometown"]!="") $str.="<tr><th>Hometown</th><td>".$profile["Hometown"]."</td></tr>";
    if($profile["Political Views"]!="") $str.="<tr><th>Political Views</th><td>".$profile["Political Views"]."</td></tr>";
    if($profile["Religious Views"]!="") $str.="<tr><th>Religious Views</th><td>".$profile["Religious Views"]."</td></tr>";

    if($profile["Bio"]!="") $str.="<tr><th>Bio</th><td>".$profile["Bio"]."</td></tr>";
    if($profile["Favorite Quotations"]!="") $str.="<tr><th>Favorite Quotations</th><td>".$profile["Favorite Quotations"]."</td></tr>";
    $str.="</table>";
    }

  if($profile["Interests"]!=""
  || $profile["Music"]!=""
  || $profile["Books"]!=""
  || $profile["Movies"]!=""
  || $profile["Television"]!=""
  || $profile["Games"]!=""){
    $str.="<table class='info-container'>";
    if($uid==$self) $str.="<tr><th class='subhead'>Likes and Interests</th><td class='subhead'><a href='?edit=interests'><img src='Images/System/profile-edit.png'> Edit</a></td></tr>";
    else $str.="<tr><th class='subhead'>Likes and Interests</th><td class='subhead'></td></tr>";
    if($profile["Interests"]!="") $str.="<tr><th>Interests</th><td>".$profile["Interests"]."</td></tr>";
    if($profile["Music"]!="") $str.="<tr><th>Music</th><td>".$profile["Music"]."</td></tr>";
    if($profile["Books"]!="") $str.="<tr><th>Books</th><td>".$profile["Books"]."</td></tr>";
    if($profile["Movies"]!="") $str.="<tr><th>Movies</th><td>".$profile["Movies"]."</td></tr>";
    if($profile["Television"]!="") $str.="<tr><th>Television</th><td>".$profile["Television"]."</td></tr>";
    if($profile["Games"]!="") $str.="<tr><th>Games</th><td>".$profile["Games"]."</td></tr>";
    $str.="</table>";
    }


  if($profile["High School"]!=""
  || $profile["Employer"]!=""
  || $profile["University"]!=""){
    $str.="<table class='info-container'>";
    if($uid==$self) $str.="<tr><th class='subhead'>Work and Education</th><td class='subhead'><a href='?edit=education'><img src='Images/System/profile-edit.png'> Edit</a></td></tr>";
    else $str.="<tr><th class='subhead'>Work and Education</th><td class='subhead'></td></tr>";
    if($profile["High School"]!="") $str.="<tr><th>High School</th><td>".$profile["High School"]."</td></tr>";
    if($profile["University"]!="") $str.="<tr><th>University</th><td>".$profile["University"]."</td></tr>";
    if($profile["Employer"]!="") $str.="<tr><th>Employer</th><td>".$profile["Employer"]."</td></tr>";
    $str.="</table>";
    }

  if(friend_check($self,$uid)){
    $str.="<table class='info-container'>";
    if($uid==$self) $str.="<tr><th class='subhead'>Contact Information</th><td class='subhead'><a href='?edit=contact'><img src='Images/System/profile-edit.png'> Edit</a></td></tr>";
    else $str.="<tr><th class='subhead'>Contact Information</th><td class='subhead'></td></tr>";
    $str.="<tr><th>EMail Address</th><td>".db_get("Users",$uid,"EMail","NA")."</td></tr>";
    if($profile["Phone"]!="") $str.="<tr><th>Phone</th><td>".$profile["Phone"]."</td></tr>";
    if($profile["Website"]!="") $str.="<tr><th>Website</th><td><a href='".$profile["Website"]."'>".$profile["Website"]."</a></td></tr>";
    if($profile["Address"]!="") $str.="<tr><th>Address</th><td>".$profile["Address"]."</td></tr>";
    $str.="</table>";
    }

  return $str;
  }

function display_information_edit($subtab){
  $uid=user_online();
  if($subtab=="") $subtab="basicinfo";

  $str.="<table class='edit-container' border=0>";
  $str.="<tr><th class='top'></th><td rowspan=100>";

  $str.="<form action='Action.php?action=Profile-Edit' method='post'>";

  if($subtab=="basicinfo"){
  $str.="<h2>Edit Profile : Basic Information</h2>";
  $str.="<div id='edit-basicinfo'><table>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='left'>First Name:</td><td class='mid'><input type='text' name='First Name' value='".db_get("Users",$uid,"First Name","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>Last Name:</td><td class='mid'><input type='text' name='Last Name' value='".db_get("Users",$uid,"Last Name","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>Birthday:</td><td class='mid'>";
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    $birthday = db_get("Users",$uid,"Birthday","");
    if($birthday=="")$birthday=0;
    $str.=" <select name='dob-month'>"; foreach($months as $i=>$j) if(date("n",$birthday)==$i+1) $str.="<option value=".($i+1)." selected='selected'>$j</option>"; else $str.="<option value=".($i+1).">$j</option>"; $str.="</select>";
    $str.=" <select name='dob-date'>"; for($i=1;$i<=31;$i++) if(date("d",$birthday)==$i) $str.="<option selected='selected'>$i</option>"; else $str.="<option>$i</option>"; $str.="</select>";
    $str.=" <select name='dob-year'>"; for($i=1980;$i<=2010;$i++) if(date("Y",$birthday)==$i) $str.="<option selected='selected'>$i</option>"; else $str.="<option>$i</option>"; $str.="</select>";
    $str.="</td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>Sex:</td><td class='mid'><select name='Sex'>";
    if(db_get("Users",$uid,"Sex","")=="Female") $str.="<option>Male</option><option selected='selected'>Female</option>";
    else $str.="<option selected='selected'>Male</option><option>Female</option>";
    $str.="</select></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Current Location:</td><td class='mid'><input type='text' name='Current Location' value='".db_get("Users",$uid,"Current Location","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>Hometown:</td><td class='mid'><input type='text' name='Hometown' value='".db_get("Profiles",$uid,"Hometown","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Relationship Status:</td><td class='mid'><input type='text' name='Relationship Status' value='".db_get("Profiles",$uid,"Relationship Status","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Political Views:</td><td class='mid'><input type='text' name='Political Views' value='".db_get("Profiles",$uid,"Political Views","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>Religious Views:</td><td class='mid'><input type='text' name='Religious Views' value='".db_get("Profiles",$uid,"Religious Views","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Bio:</td><td class='mid'><textarea name='Bio' />".db_get("Profiles",$uid,"Bio","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Favorite Quotations:</td><td class='mid'><textarea name='Favorite Quotations' />".db_get("Profiles",$uid,"Favorite Quotations","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="</table></div>";
  }

  if($subtab=="interests"){
  $str.="<h2>Edit Profile : Likes and Interests</h2>";
  $str.="<div id='edit-interests'><table>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='left'>Interests:</td><td class='mid'><textarea name='Interests' />".db_get("Profiles",$uid,"Interests","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Music:</td><td class='mid'><textarea name='Music' />".db_get("Profiles",$uid,"Music","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Books:</td><td class='mid'><textarea name='Books' />".db_get("Profiles",$uid,"Books","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Movies:</td><td class='mid'><textarea name='Movies' />".db_get("Profiles",$uid,"Movies","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Television:</td><td class='mid'><textarea name='Television' />".db_get("Profiles",$uid,"Television","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Games:</td><td class='mid'><textarea name='Games' />".db_get("Profiles",$uid,"Games","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="</table></div>";
  }

  if($subtab=="education"){
  $str.="<h2>Edit Profile : Education and Work</h2>";
  $str.="<div id='edit-education'><table>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='left'>High School:</td><td class='mid'><textarea name='High School' />".db_get("Profiles",$uid,"High School","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>College/University:</td><td class='mid'><textarea name='University' />".db_get("Profiles",$uid,"University","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Employer:</td><td class='mid'><textarea name='Employer' />".db_get("Profiles",$uid,"Employer","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="</table></div>";
  }

  if($subtab=="contact"){
  $str.="<h2>Edit Profile : Contact Information</h2>";
  $str.="<div id='edit-contact'><table>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='left'>EMail Address:</td><td class='mid'>".db_get("Users",$uid,"EMail","")."</td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Website:</td><td class='mid'><input type='text' name='Website' value='".db_get("Profiles",$uid,"Website","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Phone Number:</td><td class='mid'><input type='text' name='Phone' value='".db_get("Profiles",$uid,"Phone","")."' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3><hr></td></tr>";
  $str.="<tr><td class='left'>Address:</td><td class='mid'><textarea name='Address' />".db_get("Profiles",$uid,"Address","")."</textarea></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="</table></div>";
  }

  if($subtab=="profilepic"){
  $str.="<h2>Edit Profile : Profile Picture</h2>";
  $str.="<div id='edit-profilepic'><table>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='left2'><br><img src='Image.php?uid=$uid' />";
  if(file_exists("Images/Profile/$uid.jpg")||file_exists("Images/Profile/$uid.png")||file_exists("Images/Profile/$uid.gif")){
    $str.="<br><a onClick=\"document.delete_profile_pic.submit();\">Delete Current Profile Picture</a>";
    $str.="</form><form action='Action.php?action=Profile-Picture-Delete' method='post' name='delete_profile_pic'></form><form>";
    }
  else $str.="<br>No Profile Picture Uploaded<br>Displaying Default Profile Picture";
  $str.="</td><td class='mid2'>Your current Profile Picture is as display on the left. To upload a new Profile Picture, select an image file on your computer (2MB max):<br /><br /></form>";
  $str.="<form action='Action.php?action=Profile-Picture-Upload' method='post' enctype='multipart/form-data' name='upload-profile-pic'><input type='file' name='profile-picture' onChange='(document.forms[\"upload-profile-pic\"]).submit();' /></form><form></td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="</table></div>";
  }

  if($subtab=="password"){
  $str.="<h2>Edit Profile : Change Password</h2>";
  $str.="<div id='edit-contact'><table>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='left'>Current Password:</td><td class='mid'><input type='password' name='oldpass0' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>New Password:</td><td class='mid'><input type='password' name='newpass1' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='left'>Retype New Password:</td><td class='mid'><input type='password' name='newpass2' /></td><td class='right'>&nbsp;</td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="<tr><td class='hr' colspan=3></td></tr>";
  $str.="</table></div>";
  }

  if($subtab!="profilepic") $str.="<input type=button value='Clear Changes' onClick='window.location.reload()' class='submit'>";
  if($subtab!="profilepic") $str.="<input type=submit value='Save Changes' class='submit' />";
  $str.="</form>";

  $str.="</td></tr>";
  $str.="<tr><th".(($subtab=="basicinfo")?" class='active'  ":"")." onClick='window.location=\"?edit=basicinfo\";' ><img src='Images/System/edit-basicinfo.png' > Basic Information</th></tr>";
  $str.="<tr><th".(($subtab=="interests")?" class='active'  ":"")." onClick='window.location=\"?edit=interests\";' ><img src='Images/System/edit-interests.png' > Likes and Interests</th></tr>";
  $str.="<tr><th".(($subtab=="education")?" class='active'  ":"")." onClick='window.location=\"?edit=education\";' ><img src='Images/System/edit-education.png' > Education and Work</th></tr>";
  $str.="<tr><th".(($subtab=="contact")?" class='active'   ":"")."  onClick='window.location=\"?edit=contact\";'   ><img src='Images/System/edit-contact.png'   > Contact Information</th></tr>";
  $str.="<tr><th".(($subtab=="profilepic")?" class='active' ":"")." onClick='window.location=\"?edit=profilepic\";'><img src='Images/System/edit-profilepic.png'> Profile Picture</th></tr>";
  $str.="<tr><th".(($subtab=="password")?" class='active' ":"")." onClick='window.location=\"?edit=password\";'><img src='Images/System/edit-password.png'> Change Password</th></tr>";
  $str.="<tr><th class='bottom'></th></tr>";
  $str.="</table>";

  return $str;
  }

?>