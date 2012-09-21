<?php


function event_create($self){
  $a["EID"] = count(db_get_ids("Events"))+1;
  $a["UID"] = $self;
  $a["Status"] = "Normal";
  $a["Name"] = text_screen($_POST["name"]);
  $a["Start Date"] = mktime(0,0,0,$_POST["smonth"],$_POST["sdate"],$_POST["syear"]);
  $a["Start Time"] = $_POST["stime"];
  $a["Location"] = text_screen($_POST["location"]);
  $a["Type"] = "Public";
  $a["Display End"] = "no";
  $a["Yes"] = array($self);

  if($a["Name"]=="") return;
  db_addrow("Events",$a);

  global $redirect; $redirect = "?editevent=".$a["EID"];
  }


function event_delete($self){
  $eid = $_POST["eid"];
  $confirm = $_POST["confirm"];
  if(db_get("Events",$eid,"UID","")!=$self) return;
  if($confirm=="on") db_set("Events",$eid,"Status","Deleted");
  }



function event_respond($self,$eid,$response){
  db_addcol("Events","No");
  db_addcol("Events","Maybe");
  if($response=='yes') db_array_add("Events",$eid,"Yes",$self);
  if($response=='no') db_array_add("Events",$eid,"No",$self);
  if($response=='maybe') db_array_add("Events",$eid,"Maybe",$self);
  if($response=='cancel'){
    db_array_del("Events",$eid,"Yes",$self);
    db_array_del("Events",$eid,"No",$self);
    db_array_del("Events",$eid,"Maybe",$self);
    }
  }



function event_update($self){
  $eid = $_POST["eid"];
  $sdate = mktime(0,0,0,$_POST["smonth"],$_POST["sdate"],$_POST["syear"]);
  $edate = mktime(0,0,0,$_POST["emonth"],$_POST["edate"],$_POST["eyear"]);
  // db_set("Events",$eid,"Name",text_screen($_POST["name"]));
  db_set("Events",$eid,"Start Date",$sdate);
  db_set("Events",$eid,"End Date",$edate);
  db_set("Events",$eid,"Start Time",$_POST["stime"]);
  db_set("Events",$eid,"End Time",$_POST["etime"]);
  db_set("Events",$eid,"Display End",$_POST["eshow"]);
  db_set("Events",$eid,"Display End",$_POST["eshow"]);
  db_set("Events",$eid,"Location",text_screen($_POST["location"]));
  db_set("Events",$eid,"Info",text_screen($_POST["info"]));
  db_set("Events",$eid,"Type",$_POST["type"]);

  if($_POST['deleteimage']=="on" || $_FILES['eventimage']['error']==0){
    file_delete("Images/Event/$eid.jpg"); file_delete("Images/Event/$eid.png"); file_delete("Images/Event/$eid.gif");
    }
  if($_FILES['eventimage']['error']==0)
    $type=file_upload('eventimage',"Images/Event/".$eid,'image/jpeg,image/png,image/gif',2*1024*1024);
  }















function event_left_display($eid,$self){

  if(db_get("Events",$eid,"Status","")!="Normal") return "<script>window.location='?index=events'</script>";

  $yes = db_get("Events",$eid,"Yes",array());
  $no = db_get("Events",$eid,"No",array());
  $maybe = db_get("Events",$eid,"Maybe",array());

  if(!is_array($yes)||$yes[0]=="") $yes=array(); shuffle($yes);
  if(!is_array($no)||$no[0]=="") $no=array(); shuffle($no);
  if(!is_array($maybe)||$maybe[0]=="") $maybe=array(); shuffle($maybe);

  $str.="<table border=0 class='event-left-container'>";
  $str.="<tr><td colspan=3 class='e11'><a href='?eid=$eid'><img src='Image.php?eid=$eid' width=200 /></a></td></tr>";

  $decided=1;
  if(in_array($self,$yes)) $str.="<tr><td colspan=3 class='e51'>You are attending this event.</td></tr>";
  else if(in_array($self,$no)) $str.="<tr><td colspan=3 class='e51'>You are not attending this event.</td></tr>";
  else if(in_array($self,$maybe)) $str.="<tr><td colspan=3 class='e51'>You may attend this event.</td></tr>";
  else $decided=0;
  if($decided){
    $str.="<form action='Action.php?action=Event-Respond' method='post' name='event_cancel'><input type='hidden' name='eid' value='$eid' /><input type='hidden' name='response' value='cancel' /></form>";
    $str.="<tr><td class='e51' colspan=3><a onClick=\"document.event_cancel.submit();\">[ Cancel Response To Event ]</a></td></tr>";
    }

  $str.="<tr><td colspan=3 class='e12'></td></tr>";
  $link = popup_namelist("event-yes","People Attending This Event",$yes,$output1);
  $str.="<tr><td colspan=2 class='e21'>".count($yes)." Attending</td>";
  $str.="<td class='e22'><a onClick='$link'>See All</a></td></tr>";
  $str.="</table>";
  $str.="<table border=0 class='event-left-container'>";
  foreach($yes as $i=>$u){ if($i==8) break;
    $str.="<tr><td class='e31'>".user_photo($u,30,1)."</td>";
    $str.="<td class='e32' colspan=2>".user_fullname($u,1)."</td></tr>";
    }

  $str.="</table>";
  $str.="<table border=0 class='event-left-container'>";
  $str.="<tr><td colspan=3 class='e12'></td></tr>";
  $link = popup_namelist("event-no","People Not Attending This Event",$no,$output2);
  $str.="<tr><td colspan=2 class='e21'>".count($no)." Not Attending</td>";
  $str.="<td class='e22'><a onClick='$link'>See All</a></td></tr>";
  $str.="<tr><td colspan=3 class='e41'>";
    foreach($no as $i=>$u){ if($i==10) break;
      $str.=user_photo($u,30,1);
      if($i&&$i%5==0) $str.="<br>";
      }
    $str.="</td></tr>";

  $str.="<tr><td colspan=3 class='e12'></td></tr>";
  $link = popup_namelist("event-maybe","People Unsure About Attending This Event",$maybe,$output3);
  $str.="<tr><td colspan=2 class='e21'>".count($maybe)." Unsure</td>";
  $str.="<td class='e22'><a onClick='$link'>See All</a></td></tr>";
  $str.="<tr><td colspan=3 class='e41'>";
    foreach($maybe as $i=>$u){ if($i==10) break;
      $str.=user_photo($u,30,1);
      if($i&&$i%5==0) $str.="<br>";
      }
    $str.="</td></tr>";

  $str.="</table> $output1 $output2 $output3";

  return $str;
  }





















function event_mid_display($eid,$self){
  global $time;

  if(db_get("Events",$eid,"Status","")!="Normal") return "<script>window.location='?index=events';</script>";

  $uid=db_get("Events",$eid,"UID","");
  $name=db_get("Events",$eid,"Name","");
  $type=db_get("Events",$eid,"Type","");
  $sdate=db_get("Events",$eid,"Start Date","");
  $edate=db_get("Events",$eid,"End Date","");
  $stime=db_get("Events",$eid,"Start Time","");
  $etime=db_get("Events",$eid,"End Time","");
  $loca=db_get("Events",$eid,"Location","");
  $info=db_get("Events",$eid,"Info","");
  $end = db_get("Events",$eid,"Display End","");

  $yes = db_get("Events",$eid,"Yes",array());
  $no = db_get("Events",$eid,"No",array());
  $maybe = db_get("Events",$eid,"Maybe",array());

  if(!is_array($yes)||$yes[0]=="") $yes=array();
  if(!is_array($no)||$no[0]=="") $no=array();
  if(!is_array($maybe)||$maybe[0]=="") $maybe=array();

  if($uid=="") return "<script>window.location='?index=events';</script>";

  $response=-1;
  if(!in_array($self,$yes)&&!in_array($self,$no)&&!in_array($self,$maybe)){
    $options1 ="<form name='event_response' action='Action.php?action=Event-Respond' method='post' >";
    $options1.="<input type='hidden' name='eid' value='$eid' /><input type='hidden' name='response' value='none' /> </form>";
    $options2.="<input type='button' value=\"I'm Attending\" onClick=\"var e = document.event_response; e.response.value='yes'; e.submit(); \">";
    $options2.="<input type='button' value=\"No\" onClick=\"var e = document.event_response; e.response.value='no'; e.submit(); \">";
    $options2.="<input type='button' value=\"Maybe\" onClick=\"var e = document.event_response; e.response.value='maybe'; e.submit(); \">";
    }
  else {
    if(in_array($self,$yes)) $response = "You are attending this event";
    if(in_array($self,$no)) $response = "You are not attending this event";
    if(in_array($self,$maybe)) $response = "You may attend this event";
    }

  $str.="<table class='event-mid-container' border=0>";
  $str.="<tr><td colspan=2 class='e11'>$name $options2 $options1</td></tr>";
  $str.="<tr><td colspan=2 class='e12'>";
    if($response!=-1) 
    $str.="<a onClick=\"window.location='?index=events';\">Return to Events Index</a>".SEP;
    $str.=$response.SEP;
    $str.="<a onClick=\"document.event_share.submit();\">Post On Profile</a>";
    if($uid==$self) $str.=SEP."<a href='?editevent=$eid'>Edit Event Details</a>";
    $str.="<form name='event_share' action='Action.php?action=Event-Post' method='post'><input type='hidden' name='eid' value='$eid' /></form>";
    $str.="</td></tr>";

  $str.="<tr><td class='e31'>Date / Time</td><td class='e32'>";
    $str.=jdate($sdate).", $stime";
    if($end=='yes') $str.=" to ".jdate($edate).", $etime";
    $str.="</td></tr>";
  if($loca!="") $str.="<tr><td class='e31'>Location</td><td class='e32'>$loca</td></tr>";
  $str.="<tr><td class='e31'>Created By</td><td class='e32'>".user_fullname($uid,1)."</td></tr>";
  if($info!="") $str.="<tr><td class='e31'>More Info</td><td class='e32'>$info</td></tr>";
  $str.="<tr><td colspan=2 class='e41'></td></tr>";
  $str.="<tr><td class='e51'>Wall</td><td class='e52'><a title='This page is up to date as of ".fdate($time)."' href=''>Refresh</a></td></tr>";
  $str.="</table>";
  $str.=newsfeed_display_stream($self,"E$eid");

  return $str;
  }













function event_edit_display($eid,$self){

  $uid=db_get("Events",$eid,"UID","");
  $name=db_get("Events",$eid,"Name","");
  $type=db_get("Events",$eid,"Type","");
  $sdate0=(int)db_get("Events",$eid,"Start Date","");
  $edate0=(int)db_get("Events",$eid,"End Date","");
  $end = db_get("Events",$eid,"Display End","");
  $stime=db_get("Events",$eid,"Start Time","");
  $etime=db_get("Events",$eid,"End Time","");
  $location=db_get("Events",$eid,"Location","");
  $info=db_get("Events",$eid,"Info","");

  $sdate=date("j",$sdate0); $smonth=date("n",$sdate0); $syear=date("Y",$sdate0);
  $edate=date("j",$edate0); $emonth=date("n",$edate0); $eyear=date("Y",$edate0);

  if($uid!=$self) return "<script>window.location='?index=events';</script>";

  $str.="<table class='event-edit-container' border=0>";
  $str.="<h2>$name : Event Edit Mode <a href='?eid=$eid'>(Return to Normal Mode)</a></h2>";

  $str.="<form action='Action.php?action=Event-Update' method='post' enctype='multipart/form-data' > ";
  $str.="<input type='hidden' name='eid' value='$eid' />";

  $str.="<tr><td class='e21'>Event Name</td><td class='e22'><input type='text' name='name' value=\"$name\" disabled='disabled' /></td></tr>";
  $str.="<tr><td class='e21'>Event Image</td><td class='e22'><input type='file' name='eventimage' /></td></tr>";
  $str.="<tr><td class='e21'></td><td class='e23'><input type='checkbox' name='deleteimage' /> Delete Current Event Image</td></tr>";

  $str.="<tr><td class='e21'>Start Date / Time</td><td class='e22'>";
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    $str.="<select name='syear'>"; for($i=2005;$i<=2025;$i++) if($syear!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $str.="<select name='smonth'>"; for($i=1;$i<=12;$i++) if($smonth!=$i) $str.="<option value='$i'>".$months[$i-1]."</option>"; else $str.="<option value='$i' selected='selected'>".$months[$i-1]."</option>"; $str.="</select>";
    $str.="<select name='sdate'>"; for($i=1;$i<=31;$i++) if($sdate!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $str.="<select name='stime'>";
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($stime=="$j:00 am") $str.="<option selected='selected'>$j:00 am</option>"; else $str.="<option>$j:00 am</option>"; if($stime=="$j:30 am") $str.="<option selected='selected'>$j:30 am</option>"; else $str.="<option>$j:30 am</option>"; }
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($stime=="$j:00 pm") $str.="<option selected='selected'>$j:00 pm</option>"; else $str.="<option>$j:00 pm</option>"; if($stime=="$j:30 pm") $str.="<option selected='selected'>$j:30 pm</option>"; else $str.="<option>$j:30 pm</option>"; }
    $str.="</select>";
    $str.="</td></tr>";

  $str.="<tr><td class='e21'>Display End Date / Time</td><td class='e22'>";
    $str.="<select style='width:100%;' name='eshow'>";
    $str.="<option ".(($end=="yes")?"selected='selected'":"")." value='yes'>Yes</option>";
    $str.="<option ".(($end=="no")?"selected='selected'":"")." value='no'>No</option>";
    $str.="</select>";
    $str.="</td></tr>";

  $str.="<tr><td class='e21'>End Date / Time</td><td class='e22'>";
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    $str.="<select name='eyear'>"; for($i=2005;$i<=2025;$i++) if($eyear!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $str.="<select name='emonth'>"; for($i=1;$i<=12;$i++) if($emonth!=$i) $str.="<option value='$i'>".$months[$i-1]."</option>"; else $str.="<option value='$i' selected='selected'>".$months[$i-1]."</option>"; $str.="</select>";
    $str.="<select name='edate'>"; for($i=1;$i<=31;$i++) if($edate!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $str.="<select name='etime'>";
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($etime=="$j:00 am") $str.="<option selected='selected'>$j:00 am</option>"; else $str.="<option>$j:00 am</option>"; if($etime=="$j:30 am") $str.="<option selected='selected'>$j:30 am</option>"; else $str.="<option>$j:30 am</option>"; }
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($etime=="$j:00 pm") $str.="<option selected='selected'>$j:00 pm</option>"; else $str.="<option>$j:00 pm</option>"; if($etime=="$j:30 pm") $str.="<option selected='selected'>$j:30 pm</option>"; else $str.="<option>$j:30 pm</option>"; }
    $str.="</select>";
    $str.="</td></tr>";

  $str.="<tr><td class='e21'>Event Location</td><td class='e22'><input type='text' name='location' value=\"$location\" /></td></tr>";
  $str.="<tr><td class='e21'>More Information</td><td class='e22'><textarea name='info'>$info</textarea></td></tr>";

  $str.="<tr><td class='e21'>Event Type</td><td class='e22'>";
    $str.="<select name='type'>";
    $str.="<option ".(($type=="Public")?"selected='selected'":"")." value='Public'>Public Event (Anybody can View and Attend)</option>";
    $str.="<option ".(($type=="Private")?"selected='selected'":"")." value='Private'>Private Event (Only friends can View and Attend)</option>";
    $str.="</select>";
    $str.="</td></tr>";

  $str.="<tr><td class='e21'></td><td class='e22' colspan=2>";
    $str.="In case of the Event Image field, leaving it blank will not cause the current Event Image to be deleted. In all other case, the values of the fields as they were at the moment of clicking the Update button will be considered as updated values, whether or not any change was intended.";
    $str.="</td></tr>";

  $str.="<tr><td class='e21'></td><td class='e22' colspan=2>";
    $str.="<input class='button' type='submit' value=\"Update\" />";
    $str.="<input class='button' type='button' value=\"Clear Changes\" onClick=\"window.location='?editevent=$eid';\" />";
    $str.="</td></tr>";

  $str.="</form></table>";


  $str.="<table class='event-edit-container' border=0>";
  $str.="<form action='Action.php?action=Event-Delete' method='post'> <input type='hidden' name='eid' value='$eid' />";
  $str.="<tr><td class='e21'>Confirm Deletion</td><td class='e23'><input type='checkbox' name='confirm' /> Unless this checkbox is ticked, pressing the button below will have no effect.</td><tr>";
  $str.="<tr><td class='e21'></td><td class='e22' colspan=2><input class='button' type='submit' value=\"Delete Event\" /></td></tr>";
  $str.="</form></table>";

  return $str;
  }
















function event_right_display($eid,$self){
  global $time;

  $e = db_get_ids("Events");
  if(!is_array($e)||$e[0]=="") $e=array();
  shuffle($e);

  $str.="<table class='event-right-container' border=0>";
  $str.="<tr><th><a href='?index=events'>Events</a></th></tr>";
  $k=0;
  foreach($e as $j){
    if($j==$eid) continue;
    $t = db_get("Events",$j,"Start Time","");
    eregi("([0-9]{1,2}):([0-9]{1,2}) ([apm]{2})",$t,$t);
    if($t[3]=="am") $t = $t[1]*60*60+$t[2]*60; else $t = 12*60*60+$t[1]*60*60+$t[2]*60; 

    if(db_get("Events",$j,"Start Date","")+$t<$time) continue;

    if($k==3) break; $k++; 
    $str.="<tr><td class='e2'></td></tr>";
    $str.="<tr><td class='e1'><a href='?eid=$j'><img src='Image.php?eid=$j'></a>";
    $str.="<p><a href='?eid=$j'><b>".db_get("Events",$j,"Name","")."</b></a>";
    $str.="<p>Date/Time : ";
      $str.=jdate(db_get("Events",$j,"Start Date","")).", ".db_get("Events",$j,"Start Time","");
      if(db_get("Events",$j,"Display End","no")=="yes") $str.=" to ".jdate(db_get("Events",$j,"End Date","")).", ".db_get("Events",$j,"End Time","");
      $str.="</p>";
    $l = db_get("Events",$j,"Location","");
    if($l!="") $str.="<p>Location : $l</p>";
    $str.="<p>Creator : ".user_fullname(db_get("Events",$j,"UID",""),1)."</p>";
    $str.="</td></tr>";
    }
  $str.="</table>";
  $str.=ad_display(225,5-$k);

  return $str;
  }






















function event_index_display($self,$old){
  global $time;

  $e1 = db_get_ids("Events");
  if(!is_array($e1)||$e1[0]=="") $e1=array();
  $e2 = array(); // sort
  $e3 = array(); // future/past
  $e4 = array(); 
  $c=0;
  foreach($e1 as $i){ $c++;
    if( db_get("Events",$i,"Status","")!="Normal" ) continue;
    $creator = db_get("Events",$i,"UID","");
    if( $self!=$creator && !friend_check($self,$creator) && db_get("Events",$i,"Type","")!="Public" ) continue;
    $j = db_get("Events",$i,"Start Time","");
    eregi("([0-9]{1,2}):([0-9]{1,2}) ([apm]{2})",$j,$j);
    if($j[3]=="am") $j = $j[1]*60*60+$j[2]*60; else $j = 12*60*60+$j[1]*60*60+$j[2]*60; 
    $e2[] = array("EID"=>$i,"Date"=>db_get("Events",$i,"Start Date",""),"Time"=>$j );
    }
  for($i=0;$i<$c;$i++) for($j=0;$j<$c-$i-1;$j++){ $swap=0;
    if($e2[$j]["Date"]>$e2[$j+1]["Date"]) $swap=1;
    if($e2[$j]["Date"]==$e2[$j+1]["Date"] && $e2[$j]["Time"]>$e2[$j+1]["Time"]) $swap=1;
    if($swap){ $t = $e2[$j]; $e2[$j]=$e2[$j+1]; $e2[$j+1]=$t; }
    }
  foreach($e2 as $i=>$j) if($j["EID"]!=""){
    if($old==0 && $j["Date"]+$j["Time"]>=$time){ $e3[$i]=$j["EID"]; $e4[$i]=$j["Date"]+$j["Time"]; }
    if($old==1 && $j["Date"]+$j["Time"]<$time){ $e3[$i]=$j["EID"]; $e4[$i]=$j["Date"]+$j["Time"]; }
    }

  array_multisort($e4,$e3);
  if($old==1) $e3 = array_reverse($e3);

  $str.="<table class='event-index-container' border=0>";
  if($old==0) $str.="<tr><td class='e11' colspan=2> <div onClick=\"window.location='?index=oldevents';\" style='cursor:pointer;color:blue;'>View Past Events</div> </td></tr>";
  else $str.="<tr><td class='e11' colspan=2> <div onClick=\"window.location='?index=events';\" style='cursor:pointer;color:blue;'>View Future Events</div> </td></tr>";
  $none=1;
  foreach($e3 as $i){
    $creator = db_get("Events",$i,"UID","");
    $str.="<tr><td class='e21'><a href='?eid=$i'><img width=100 src='Image.php?eid=$i' /></a></td>";
    $str.="<td class='e22'><p><h><a href='?eid=$i'>".db_get("Events",$i,"Name","")."</a></h></p><br>";
    $str.="<p>Creator : ".user_fullname($creator,1)."</p>";
    if(db_get("Events",$i,"Display End","no")=="yes"){
      $str.="<p>Start Date/Time : ".jdate(db_get("Events",$i,"Start Date","")).", ".db_get("Events",$i,"Start Time","")."</p>";
      $str.="<p>End Date/Time : ".jdate(db_get("Events",$i,"End Date","")).", ".db_get("Events",$i,"End Time","")."</p>";
      }
    else $str.="<p>Date/Time : ".jdate(db_get("Events",$i,"Start Date","")).", ".db_get("Events",$i,"Start Time","")."</p>";
    $l = db_get("Events",$i,"Location","");
    if($l!="") $str.="<p>Location : $l</p>";
    $str.="</td></tr><tr><td colspan=2 class='e31'></td></tr>";
    $none=0;
    }
  if($none && $old==0) $str.="<tr><td class='e11' colspan=2><div>There are no upcoming events.</div></td></tr>";
  if($none && $old==1) $str.="<tr><td class='e11' colspan=2><div>There are no past events.</div></td></tr>";

  if($old==0){
  $str.="<form action='Action.php?action=Event-Create' method='post' name='event_create'>";
  $str.="<tr><td colspan=2 class='e51'>Create New Event</td></tr>";
  $str.="";
  $str.="<tr><td class='e52'>Event Name</td><td class='e53'><input name='name' type='text' /></td></tr>";
    $stime = date("i",$time)*60+date("s",$time);
    if(30*60>$stime) $t = $time+(30*60-$stime); 
    else $t = $time+(60*60-$stime); 
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
  $str.="<tr><td class='e52'>Start Time</td><td class='e53'>";
    $str.="<select name='syear'>"; for($i=2005;$i<=2025;$i++) if(date("Y",$t)!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $str.="<select name='smonth'>"; for($i=1;$i<=12;$i++) if(date("m",$t)!=$i) $str.="<option value='$i'>".$months[$i-1]."</option>"; else $str.="<option value='$i' selected='selected'>".$months[$i-1]."</option>"; $str.="</select>";
    $str.="<select name='sdate'>"; for($i=1;$i<=31;$i++) if(date("d",$t)!=$i) $str.="<option>$i</option>"; else $str.="<option selected='selected'>$i</option>"; $str.="</select>";
    $stime = date("g:i a",$t);
    $str.="<select name='stime'>";
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($stime=="$j:00 am") $str.="<option selected='selected'>$j:00 am</option>"; else $str.="<option>$j:00 am</option>"; if($stime=="$j:30 am") $str.="<option selected='selected'>$j:30 am</option>"; else $str.="<option>$j:30 am</option>"; }
    for($i=0;$i<12;$i++){ if($i==0) $j="12"; else $j=$i; if($stime=="$j:00 pm") $str.="<option selected='selected'>$j:00 pm</option>"; else $str.="<option>$j:00 pm</option>"; if($stime=="$j:30 pm") $str.="<option selected='selected'>$j:30 pm</option>"; else $str.="<option>$j:30 pm</option>"; }
    $str.="</select>";
  $str.="</td></tr>";
  $str.="<tr><td class='e52'>Event Location</td><td class='e53'><input name='location' type='text' /></td></tr>";
  $str.="<tr><td class='e52'></td><td class='e53'> <input type='submit' class='button' value='Create Event'> </td></tr>";
  $str.="<tr><td colspan=2 class='e54'></td></tr>";
  $str.="</form>";
  }

  $str.="</table>";

  return $str;
  }





?>