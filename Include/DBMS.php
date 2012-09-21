<?php

$database = array();

function screen($text,$type){
  $text = eregi_replace("#","#",$text);
  $l = strlen($text); $code="";
  if($type>0)
  for($i=0;$i<$l;$i++){
    $t = dechex(ord($text[$i]));
    $code.=(hexdec($t)<16)?"0".$t:$t;
    }
  if($type<0)
  for($i=0;$i<$l;$i+=2){
    $t = hexdec($text[$i].$text[$i+1]);
    $code.=chr($t);
    }
  return $code;
  }

function db_create($name,$fields){
  global $database;
  if(file_exists($name)) return -1;
  $fields=explode(",",$fields);
  foreach($fields as $i=>$j)
    if($i!=count($fields)-1) $data.=screen($j,1)."~";
    else $data.=screen($j,1);
  file_set("Database/$name.txt",$data);
  return 0;
  }

function db_load($name){
  global $database;
  if(!db_exists($name)) return -1;
  $data=file_get("Database/$name.txt");
  $data=explode(NL,$data);
  $database[$name]["Fields"] = explode("~",$data[0]);
  foreach($database[$name]["Fields"] as $fi=>$fj) $database[$name]["Fields"][$fi]=screen($fj,-1);
  $fields = $database[$name]["Fields"];
  $data2 = array();
  // data looped by i,j,k,l, data2 looped by m,n
  $m=0;
  foreach($data as $i=>$j){
    if(!$i) continue;
    $data[$i]=explode("|",$j);
    $n=0;
    foreach($data[$i] as $k=>$l){
      if(eregi("{",$l)){
        if($l=="{}"){ $data2[$m][ $fields[$n] ] = array(); continue; }
        $data2[$m][ $fields[$n] ] = explode(":",eregi_replace("[{}]","",$l));
        foreach($data2[$m][ $fields[$n] ] as $ti=>$tj) $data2[$m][ $fields[$n] ][$ti] = screen($tj,-1);
        }
      else $data2[$m][ $fields[$n] ] = screen($l,-1);
      $n++;
      }
    $m++;
    }
  $database[$name]["Data"]=$data2;
  return 0;
  }

function db_unload($name){
  global $database;
  unset($database[$name]);
  }

function db_save($name){
  global $database;
  if(!is_array($database[$name])) return -1;

  $fields = $database[$name]["Fields"];
  $temp=array();
  foreach($database[$name]["Fields"] as $i) $temp[]=screen($i,1);
  $data=implode($temp,"~");
  
  if(is_array( $database[$name]["Data"] ))
  foreach($database[$name]["Data"] as $ri=>$rj){
    $temp=array();
    if($rj[ $fields[0] ]=="") continue;
    foreach($fields as $fi=>$fj){
      $cell = $database[$name]["Data"][$ri][$fj];
      if(is_array($cell)){
        $temp2=array();
        foreach($cell as $k) $temp2[]=screen($k,1);
        $temp[]="{".implode($temp2,":")."}";
        }
      else { $temp[]=screen($cell,1); }
      }
    $data.=NL.implode($temp,"|");
    }
  if($data=="") return -3;
  file_set("Database/$name.txt",$data);
  return 0;
  }

function db_delete($name){
  return file_delete("Database/$name.txt");
  }

function db_exists($name){
  if(file_exists("Database/$name.txt")) return 1;
  else return 0;
  }

function db_addcol($name,$field){
  global $database;
  $set=0;
  foreach($database[$name]["Fields"] as $f) if($f==$field) $set=1;
  if(!$set) $database[$name]["Fields"][]=$field;
  }

function db_delcol($name,$field){
  global $database;
  $set=-1;
  foreach($database[$name]["Fields"] as $fi=>$fj) if($fj==$field) $set=$fi;
  if($set>-1) unset($database[$name]["Fields"][$set]);
  }

function db_addrow($name,$rowdata){ /// rowdata must be associative
  global $database;
  $id = $database[$name]["Fields"][0];
  if(is_array( $database[$name]["Data"] ))
  foreach($database[$name]["Data"] as $row)
    if($row[$id]==$rowdata[$id]) return -1;
  $database[$name]["Data"][] = $rowdata;
  return 0;
  }

function db_delrow($name,$row){
  global $database;
  $set=-1;
  $f = $database[$name]["Fields"][0];
  foreach($database[$name]["Data"] as $di=>$dj) if($dj[$f]==$row) $set = $di;
  if($set>-1) unset($database[$name]["Data"][$set]);
  }

function db_display($name){
  global $database;
    $str.="<table border=1 class='db-display'><tr>";
    $str.="<th colspan=100>Custom Database : $name Table</th></tr><tr>";
    if(is_array($database[$name]["Fields"]))
    foreach($database[$name]["Fields"] as $fieldname) $str.="<th>".$fieldname."</th>";
    $str.="</tr>";
    $fields = db_get_cols($name);
    if(is_array( $database[$name]["Data"] ))
    foreach($database[$name]["Data"] as $rows){
      $str.="<tr>";
      if(is_array($fields)) foreach($fields as $field){
        $cell = $rows[$field];
        $str.="<td>";
        if(!is_array($cell)){ if(eregi("^[0-9]{8,10}$",$cell))$str.="$cell [".fdate($cell)."]"; else $str.=$cell; }
        else $str.="[".implode($cell,"] [")."]";
        $str.="</td>";
        }
      $str.="</tr>";
      }
    else $str.="<td colspan=100>No Data in Table!</td></tr>";
    $str.="</table>";
  return $str;
  }

function db_get($name,$id,$field,$default){
  global $database;
  $f = $database[$name]["Fields"][0];
  if(is_array( $database[$name]["Data"] ))
  foreach($database[$name]["Data"] as $row)
    if($id==$row[$f]){ if($row[$field]=="") return $default; else return $row[$field]; }
  return $default;
  }

function db_set($name,$id,$field,$value){
  global $database;
  $f = $database[$name]["Fields"][0];
  if(is_array($database[$name]["Data"]))
  foreach($database[$name]["Data"] as $i=>$row)
    if($id==$row[$f]){ $database[$name]["Data"][$i][$field]=$value; return 0; }
  return -1;
  }

function db_chk($name,$id,$field,$value){
  global $database;
  $f = $database[$name]["Fields"][0];
  if(is_array( $database[$name]["Data"] ))
  foreach($database[$name]["Data"] as $i=>$row)
    if($id==$row[$f]){ $data=$row[$field]; }
  if(!is_array($data) && $data==$value) return 1;
  else if( is_array($data) && in_array($value,$data) ) return 1;
  else return 0;
  }

function db_num_rows($name){
  global $database;
  $c = count($database[$name]["Data"]);
  return $c;
  }

function db_get_rows($name){
  global $database;
  return $database[$name]["Data"];
  }

function db_get_ids($name){
  global $database;
  $f = $database[$name]["Fields"][0];
  if(is_array($database[$name]["Data"]))
  foreach($database[$name]["Data"] as $row) $list[] = $row[$f];
  if(!is_array($list))$list=array();
  return $list;
  }

function db_set_rows($name,$data){
  global $database;
  $database[$name]["Data"]=$data;
  return 0;
  }

function db_get_cols($name){
  global $database;
  return $database[$name]["Fields"];
  }

function db_get_list($prefix){
  $list = folder_get("Database/".$prefix,0);
  if(is_array($list))
  foreach($list as $i=>$item) $list[$i]=eregi_replace(".txt$","",$item);
  if(!is_array($list))$list=array();
  return $list;
  }

function db_search($name,$id,$field,$value){
  global $database;
  if(is_array($database[$name]["Data"]))
  foreach($database[$name]["Data"] as $row){
    if($row[$field]==$value){ return $row[$id]; }
    }
  return -1;
  }  

function db_array_chk($name,$row,$field,$value){
  global $database;
  $data = db_get($name,$row,$field,array());
  if(!is_array($data)) return 0;
  return (in_array($value,$data))?1:0;
  }

function db_array_add($name,$row,$field,$value){
  global $database;
  $data = db_get($name,$row,$field,"");
  if(is_array($data)) $data[] = $value;
  else if($value=="") $data = array();
  else $data = array($value);
  $data = array_unique($data);
  db_set($name,$row,$field,$data);
  return 0;
  }

function db_array_del($name,$row,$field,$value){
  global $database;
  $data = db_get($name,$row,$field,"");
  if(is_array($data))
  $data = array_diff($data,array($value));
  if(count($data)==0)$data="";
  db_set($name,$row,$field,$data);
  return 0;
  }

function db_extract($name,$fields){
  global $database;
  $data = array();
  $fields = explode(",",$fields);

  foreach(db_get_ids($name) as $row)
    foreach($fields as $field){
      $value = db_get($name,$row,$field,"");
      if($value=="") continue;
      if(!is_array($value)) $data[]=$value;
      else foreach($value as $x) $data[]=$x;
      }

  $data = array_unique($data);
  return $data;
  }

?>