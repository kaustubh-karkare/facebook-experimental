<?php

function photoindex_display($self){
  return "<div id='photoindex'>".photoindex_refresh($self)."</div>";
  }

function photoindex_refresh($self){
  $str="<style>
table.photoindex { width:525px; margin:10px; background:#EEEEEE; border:1px solid #BBBBBB; }
table.photoindex h2 { color:#222222; font-size:14px; font-weight:bold; font-family:'Tahoma'; margin-bottom:2px; padding-bottom:4px; }
table.photoindex td { vertical-align:middle; text-align:center; width:100px; }
table.photoindex td { font-family:'Tahoma'; font-size:12px; padding:4px; }
table.photoindex td img { width:100px; border:1px solid #CCCCCC; background:#FFFFFF; padding:4px; margin:4px;
	-o-transition: all 0.5s; -moz-transition: all 0.5s;-webkit-transition: all 0.5s; }
table.photoindex td img:hover { width:174px; }
table.photoindex td input { width:350px; font-family:'Tahoma'; font-size:12px; padding:2px; }
table.photoindex td input.submit { background:#627AAC; color:#FFFFFF; font-size:14px; font-weight:bold; font-family:'Tahoma'; border:1px solid #29447E; padding:4px; }
</style>";

  // --------------------------------------------------------------------

  $str.="<form action='Action.php?action=Album-Create' method='post' enctype='multipart/form-data'>";
  $str.="<table class='photoindex' border=0>";
  $str.="<h2>Create New Album</h2>";
  $str.="<tr><td colspan=2>&nbsp;</td></tr>";
    $str.="<tr><td>Enter Album Name</td><td><input type='text' name='name' /></td></tr>";
    for($i=0;$i<5;$i++)
      $str.="<tr><td>Select Photo ".($i+1)."</td><td><input type='file' name='photo".($i+1)."' /></td></tr>";
    $str.="<tr><td></td><td>More photos can be added once the Album has been created.<br>A 2MB size restriction has been imposed on the files to be uploaded.</td></tr>";
    $str.="<tr><td></td><td>&nbsp;</td></tr>";
    $str.="<tr><td></td><td><input type='submit' class='submit' value='Create New Album'></td></tr>";
    $str.="<tr><td></td><td>&nbsp;</td></tr> </table></form> <br>";

  // --------------------------------------------------------------------

  $a = db_get_ids("Albums"); $q = array();
  if(!is_array($a) || $a[0]=="") $a=array();
  foreach($a as $i){
    if(db_get("Albums",$i,"Status","")!="Normal") continue;
    if(db_get("Albums",$i,"UID","")==$self) $q[]=$i;
    }
  shuffle($q);

  if(count($q)>0) $str.="<table class='photoindex' border=0>";
  if(count($q)>0) $str.="<h2>Albums uploaded by you</h2>";
  if(count($q)>0) for($i=0;$i<count($q) && $i<15;$i++){
    if($i==0) $str.="<tr>";
    $cover = db_get("Albums",$q[$i],"Cover","");
    $str.="<td><a href='?aid=$q[$i]' ><img src='Image.php?pid=$cover' ";
    $str.="title=\"".eregi_replace("\"","'", user_fullname( db_get("Albums",$q[$i],"UID","") ,0) );
    $caption=eregi_replace("\"","'", db_get("Albums",$q[$i],"Caption","") );
    $str.=(($caption=="")?"":" : $caption")."\" /></a></td>";
    if($i&&$i%3==2) $str.="</tr><tr>";
    }
  if(count($q)>0) while($i%3!=0){ $str.="<td></td>"; $i++; }
  if(count($q)>0) $str.="</tr></table> <br>";

  // ---------------------------------------------------------------------

  $p = db_get_ids("Photos");
  if(!is_array($p) || $p[0]=="") $p=array();
  $q = array();
  foreach($p as $i){
    if(db_get("Photos",$i,"Status","")!="Normal") continue;
    if( friend_check($self, db_get("Photos",$i,"UID","") )==0 ) continue;
    $q[] = $i;
    }
  shuffle($q);

  if(count($q)>0) $str.="<table class='photoindex' border=0>";
  if(count($q)>0) $str.="<h2>Photos uploaded by friends</h2>";
  if(count($q)>0) for($i=0;$i<count($q) && $i<15;$i++){
    if($i==0) $str.="<tr>";
    $str.="<td><a href='?pid=$q[$i]' ><img src='Image.php?pid=$q[$i]' ";
    $str.="title=\"".eregi_replace("\"","'", user_fullname( db_get("Photos",$q[$i],"UID","") ,0) );
    $caption=eregi_replace("\"","'", db_get("Photos",$q[$i],"Caption","") );
    $str.=(($caption=="")?"":" : $caption")."\" /></a></td>";
    if($i&&$i%3==2) $str.="</tr><tr>";
    }
  if(count($q)>0) while($i%3!=2){ $str.="<td></td>"; $i++; }
  if(count($q)>0) $str.="</tr></table>";

  return $str;
  }

?>