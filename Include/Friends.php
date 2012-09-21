<?php


function friend_check_punch($from,$to){
  return db_array_chk("Friends",$to,"Punches",$from);
  }

function friend_punch($from,$to,$action){
  if($from==$to) return;
  if(!friend_check_punch($to,$from)){
    if($action==1) db_array_add("Friends",$to,"Punches",$from);
    else db_array_del("Friends",$to,"Punches",$from);
    }
  }

function friend_check_poke($from,$to){
  return db_array_chk("Friends",$to,"Pokes",$from);
  }

function friend_poke($from,$to,$action){
  if($from==$to) return;
  if(!friend_check_poke($to,$from)){
    if($action==1) db_array_add("Friends",$to,"Pokes",$from);
    else db_array_del("Friends",$to,"Pokes",$from);
    }
  }

function friend_check_request($from,$to){
  return db_array_chk("Friends",$to,"Requests",$from);
  }

function friend_request($from,$to,$action){
  if(!friend_check($to,$from)){
    if($action==1) db_array_add("Friends",$to,"Requests",$from);
    else db_array_del("Friends",$to,"Requests",$from);
    }
  }

function friend_check($uid1,$uid2){
  if($uid1==$uid2) return 1;
  if(db_array_chk("Friends",$uid1,"Accepted",$uid2)==1
  || db_array_chk("Friends",$uid2,"Accepted",$uid1)==1) return 1;
  else return 0;
  }

function friend_accept($from,$to,$action){
  if($action==1){
    db_array_add("Friends",$to,"Accepted",$from);
    db_array_del("Friends",$to,"Requests",$from);
    }
  else {
    db_array_del("Friends",$to,"Accepted",$from);
    db_array_del("Friends",$from,"Accepted",$to);
    }
  }

function friend_ignore($from,$to){
  db_array_del("Friends",$to,"Requests",$from);
  }

?>