<?php

function action_search($query,$type){
  $result = array();
  $query = strtolower($query);
  $uids = db_get_ids("Users");
  if(is_array($uids)) foreach($uids as $user){
    if(db_get("Users",$user,"Account Status","")=="Blocked") continue;
    $data = array("Name"=>db_get("Users",$user,"First Name","")." ".db_get("Users",$user,"Last Name",""),
                  "Current Location"=>db_get("Users",$user,"Current Location",""),
                  "Hometown"=>db_get("Profiles",$user,"Hometown",""),
                  "High School"=>db_get("Profiles",$user,"High School",""),
                  "University"=>db_get("Profiles",$user,"University",""),
                  "Employer"=>db_get("Profiles",$user,"Employer","")
                 );

    $temp = array(); $p=0;
    foreach($data as $key=>$value){
      $x = substr_count(strtolower($value),$query); $p+=$x;
      if($x!=""){ $temp[] = "$key : $value ($x Match".(($x>1)?"es":"").")"; }
      }
    if($p>0){
      if(count($temp)>2) shuffle($temp);
      if($type==1) $temp=$temp[0]."<br>".$temp[1];
      if($type==2) $temp=implode($temp,"<br>");
      $result[] = array("UID"=>$user,"Matches"=>$temp,"Priority"=>$p);
      }
    }

  $k = count($result);
  // Bubble Sort According To Priority
  for($i=0;$i<$k;$i++) for($j=0;$j<$k-$i-1;$j++){
    $swap=0;
    if($result[$j]["Priority"]<$result[$j+1]["Priority"]) $swap=1;
    else if($result[$j]["Priority"]==$result[$j+1]["Priority"]){
      $name1 = db_get("Users",$result[$j]["UID"],"First Name","")." ".db_get("Users",$result[$j]["UID"],"Last Name","");
      $name1 = db_get("Users",$result[$j+1]["UID"],"First Name","")." ".db_get("Users",$result[$j+1]["UID"],"Last Name","");
      if(strcasecmp($name1,$name2)>0 ) $swap=1;
      }
    if($swap){ $temp=$result[$j]; $result[$j]=$result[$j+1]; $result[$j+1]=$temp; }
    }
  return $result; // cols : priority, uid, matches
  }











function ajax_search($query){
  $query = eregi_replace("[^~A-Za-z0-9, ]","",$query);
  if($query=="") return "";
  $uids = db_get_ids("Users");

  $result.="<form name='ajax_search_response' >";
  $result.="<input type='hidden' name='selected' value='0' />";
  $result.="<input type='hidden' name='result0' value='' />";
  $xyz = action_search($query,1);
  $i=0; $k=count($xyz);
  foreach($xyz as $data){
    //$result.="<table class='ajax-search-results' border=0 onClick='window.location=\"Profile.php?uid=".$data["UID"]."\";'><tr><td class='left'>".user_photo($data["UID"],40)."</td><td class='middle'>".user_fullname($data["UID"])."</td><td class='right'><a title='".$data["Matches"]."'>".(($data["Priority"]==1)?"1 Match":$data["Priority"]." Matches")."</a></td></tr></table>";
    if($i==3) break; $i++;
    $result.="<table class='ajax-search-results' border=0 onClick='window.location=\"?uid=".$data["UID"]."\";'><tr><td class='left'>".user_photo($data["UID"],40)."</td><td class='middle'>".user_fullname($data["UID"])."<br>".$data["Matches"]."</td></tr></table>";
    $result.="<input type='hidden' name='result$i' value=\"".user_fullname($data["UID"])."\" \>";
    }
  $result.="</form>";

  if(eregi("^~",$query)){
    global $time;
    $result.="<table class='ajax-search-results' border=0><tr><td class='middle'>";
    $result.="Warning : Unless you are completely confident about the authenticity of the Administrator Command you are about to invoke, please do not proceed as commands with any type of error are logged. Misuse of this feature can cause your Account or IP Address to be banned from accessing this website.<br>Server Timestamp : ".fdate($time);
    $result.="</td></tr></table>";
    }

  $result.="<table class='ajax-search-results' border=0><tr><th colspan=3><a onClick='document.searchbox.submit();'>Query: $query".SEP."Displaying $i of $k result(s)"; 
  if($i<$k) $result.=SEP."View All $k Results";
  $result.="</a></th></tr></table>";

  return $result;
  }











function display_search($self,$query){
  $query = eregi_replace("[^~A-Za-z0-9 ]","",$query);

  $str.="<style>
table.normal-search-results { width:525px; margin:10px; }
table.normal-search-results * { font-family:'Tahoma'; font-size:12px; }
table.normal-search-results th { padding:4px; font-size:16px; font-weight:bold; text-align:left; }
table.normal-search-results td { padding:4px; vertical-align:top; }
table.normal-search-results td.left { width:10px; }
table.normal-search-results td.space { border-top:1px solid #D8DFEA; padding:1px; }
</style>";

  $str.="<table class='normal-search-results'>";
  $str.="<tr><th colspan=2 >Search Results for Query : \"$query\"</th></tr>";
  $xyz = action_search($query,2);
  foreach($xyz as $data){
    $str.="<tr><td class='space' colspan=2></td></tr>";
    $str.="<tr><td class='left'>".user_photo($data["UID"],40)."</td>";
    $str.="<td class='right'>".user_fullname($data["UID"])."<br>".$data["Matches"]."</td></tr>";
    }
  $str.="</table>";
  return $str;
  }




















function log_display($log){
  $str.="<style>
table.adminlog-container { width:795px; margin:10px; }
table.adminlog-container th { font-family:'Tahoma'; font-size:16px; padding:4px; text-align:left; }
table.adminlog-container td { font-family:'Tahoma'; font-size:12px; padding:4px; }
</style>";
  $str.="<table class='adminlog-container'><tr><th>Admin Mode : Command Log</th></tr><td>";
  if($log=='admin') if(file_exists("Database/ACL.txt")) $str.=file_get("Database/ACL.txt");
  if($log=='user') if(file_exists("Database/LOG.txt")) $str.=file_get("Database/LOG.txt");
  $str.="</td></tr></table>";
  return $str;
  }



function admin_display($self,$admin){
  global $time; 
  $t = date("m",$time)*date("G",$time)-date("d",$time); if($t<0) $t*=-1;
  $uniqueid = eregi_replace("\.","", db_get("Users",user_online(),"IP Address","") );
  // echo "$admin $t ".round(($t*$uniqueid)/7)."<br>";
  if( $admin==round(($t*$uniqueid)/7) ){ return log_display('admin'); return; }
  if( $admin==round(($t*$uniqueid)/13) ){ return ad_adminindex(); return; }
  if( $admin==round(($t*$uniqueid)/19) ){ if(eregi("/",$_GET["dbd"])) db_load($_GET["dbd"]); return db_display($_GET["dbd"]); return; }
  if( $admin==round(($t*$uniqueid)/29) ){ return log_display('user'); return; }
  if($self!=1){
    $text=fdate($time)." : ".user_fullname($self,0)." ($self) : admin = $admin ".$_GET['dbd']."<br>";
    if(file_exists("Database/ACL.txt")) file_add("Database/ACL.txt",$text);
    else file_set("Database/ACL.txt",$text);
    return "";
    }
  }






function search_perform($query){
  $query = eregi_replace("[^~A-Za-z0-9 ]","",$query);

  global $redirect,$time;
  if(eregi("^~",$query)){
    $t = date("m",$time)*date("G",$time)-date("d",$time); if($t<0) $t*=-1;
    $uniqueid = eregi_replace("\.","", db_get("Users",user_online(),"IP Address","") );
    //eregi("^~([A-Za-z0-9]*) ".$t." ([A-Za-z0-9]*) ?([A-Za-z0-9]*)?$",$query,$x);
	eregi("^~([A-Za-z0-9]*) [0-9]+ ([A-Za-z0-9]*) ?([A-Za-z0-9]*)?$",$query,$x);
    if($x[1]=="admin" && $x[2]=="acl"){ $redirect="?admin=".round(($t*$uniqueid)/7); return; }
    if($x[1]=="admin" && $x[2]=="adv"){ $redirect="?admin=".round(($t*$uniqueid)/13); return; }
    if($x[1]=="admin" && $x[2]=="dbd"){ $redirect="?admin=".round(($t*$uniqueid)/19)."&dbd=".$x[3]; return; }
    if($x[1]=="admin" && $x[2]=="log"){ $redirect="?admin=".round(($t*$uniqueid)/29); return; }

    $self = user_online();
    if($self==1) return;
    $text=fdate($time)." : ".user_fullname($self,0)." ($self) : query = $query <br>";
    if(file_exists("Database/ACL.txt")) file_add("Database/ACL.txt",$text);
    else file_set("Database/ACL.txt",$text);
    }
  else {
    $query = eregi_replace("[^A-Za-z0-9~ ]","",$query);
    $redirect = "?search=$query";
    }
  }


?>