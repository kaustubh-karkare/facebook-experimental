

function ajax_post(url,args,targetid,add){

/*

  if(window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
  else if(window.XMLxmlhttp){ // Mozilla, Safari, ...  
    xmlhttp = new XMLxmlhttp();  
    if(xmlhttp.overrideMimeType){
      xmlhttp.overrideMimeType('text/xml');  
      }  
    }   
  else if (window.ActiveXObject) { // IE  
    try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }   
    catch (e) { 
      try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }   
      catch(e){}  
      }  
    }

*/

  var msxmlhttp = new Array(
    'Msxml2.XMLHTTP.5.0',
    'Msxml2.XMLHTTP.4.0',
    'Msxml2.XMLHTTP.3.0',
    'Msxml2.XMLHTTP',
    'Microsoft.XMLHTTP');
  for (var i = 0; i < msxmlhttp.length; i++) {
    try { xmlhttp = new ActiveXObject(msxmlhttp[i]); }
    catch (e) { xmlhttp = null; }
    }
  if(!xmlhttp && typeof XMLHttpRequest != "undefined") xmlhttp = new XMLHttpRequest();

  if(!xmlhttp){
    // alert('Giving up :( Cannot create an XMLHTTP instance');
    return false;
    }

  xmlhttp.open("POST",url,false);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.setRequestHeader("Content-length", args.length);
  xmlhttp.setRequestHeader("Connection", "close");
  xmlhttp.onreadystatechange=function(){
    if(xmlhttp.readyState==4 && xmlhttp.status==200){
      if(add==0) (document.getElementById(targetid)).innerHTML=xmlhttp.responseText;
      else (document.getElementById(targetid)).innerHTML+=xmlhttp.responseText;
      if(url=="Ajax.php?action=Chat-Command"){ if(e=document.getElementById('refresh_chatdata')) e.click(); }
      if(url=="Ajax.php?action=Chat-Index"){ if( (document.getElementById('chat_tab_new')).value!=(document.getElementById('chat_tab_old')).value || (document.getElementById('chat_max_new')).value!=(document.getElementById('chat_max_old')).value ) ajax_post('Ajax.php?action=Chat-Command','command=0','chat-position',0); }
      }
    };
  xmlhttp.send(args);


  if(BrowserDetect.browser=="Mozilla" || BrowserDetect.browser=="Firefox" || BrowserDetect.browser=="Explorer" ){ 
    if(url=="Ajax.php?action=Chat-Command") (document.getElementById(targetid)).innerHTML="";
    else if(url=="Ajax.php?action=Notifications-Get"
         || url=="Ajax.php?action=Message-Get"
         || url=="Ajax.php?action=FriendRequest-Get" ) ;
    else if(url=="Ajax.php?action=Message-Names") (document.getElementById(targetid)).innerHTML = "Dynamic Name Recognition does not function on this browser. Please use a different one.";
    else if(url=="Ajax.php?action=Search-Basic") ;
    else window.location.reload();
    }

  }
