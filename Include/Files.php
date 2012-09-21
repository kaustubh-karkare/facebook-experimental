<?php

function file_get($target){
  if(!file_exists($target)) return -1;
  $f = fopen($target,"r");
  $data="";
  while(!feof($f))$data.=fgets($f);
  return $data;
  }
function file_set($target,$data){
  $f = fopen($target,"w");
  fputs($f,$data);
  return 0;
  }
function file_add($target,$data){
  if(!file_exists($target)) return -1;
  $f = fopen($target,"a");
  fputs($f,$data);
  return 0;
  }
function file_delete($target){
  if(!file_exists($target)) return -1;
  unlink($target); return 0;
  }
function file_rename($target,$new){
  if(!file_exists($target)) return -1;
  rename($target,$new); return 0;
  }
function folder_get($target,$type=0){
  if(!is_dir($target)) return -1;
  $f=opendir($target);
  $list=array();
  while($e=readdir($f)){
    if($e=="."||$e=="..") continue;
    if( ($type==0 || $type==2) && file_exists("$target/$e")) $list[]=$e;
    if( ($type==1 || $type==2) && is_dir("$target/$e")) $list[]=$e;
    }
  closedir($f);
  return $list;
  }

function file_upload($file,$targetid,$allowedtypes,$allowedsize){

  $fileempty = empty($_FILES[$file]);
  $fileerror = $_FILES[$file]['error'];

  $filename = basename($_FILES[$file]['name']);
  $filetype = $_FILES[$file]["type"];
  $filesize = $_FILES[$file]["size"];
  $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
  $filetmpname = $_FILES[$file]['tmp_name'];

  if(strtolower($extension)=="jpeg") $extension="jpg";

  if( (!$fileempty) && ($fileerror == 0) )
    if( in_array($filetype,explode(",",$allowedtypes)) )
      if($filesize<$allowedsize)
        if(!file_exists("$targetid.$extension"))
          if(move_uploaded_file($filetmpname,"$targetid.$extension")) return $extension;
          else $str = "File Upload Error : Could not move file from temporary location!".NL;
        else $str = "File Upload Error : A file with the same name already exists!".NL;
      else $str = "File Upload Error : Filesize exceeds limits!".NL;
    else $str = "File Upload Error : Filetype not allowed!".NL;
  else $str = "File Upload Error : File-Data Empty / File-Error Non-Zero!".NL;

  $_SESSION["notice"].=$str;
  return -4;
  }




function file_imagedim($pid){
  $e=-1;
  if(file_exists("Images/Photos/$pid.jpg")){ $a=getimagesize("Images/Photos/$pid.jpg"); $e="jpg"; }
  if(file_exists("Images/Photos/$pid.gif")){ $a=getimagesize("Images/Photos/$pid.gif"); $e="gif"; }
  if(file_exists("Images/Photos/$pid.png")){ $a=getimagesize("Images/Photos/$pid.png"); $e="png"; }
  if($e!=-1) return array($e,$a[0],$a[1]);
  }

?>