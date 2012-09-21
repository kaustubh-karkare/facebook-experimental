/*

function convert(text){
  code = "";
  for(i=0;i<text.length;i++){
    if(text.charCodeAt(i)<16)code+="0";
    code+=d2h(text.charCodeAt(i));
    }
  return code;
  }

*/

function getXYpos(elem) {
   if (!elem) { return {"x":0,"y":0}; }
   var xy={"x":elem.offsetLeft,"y":elem.offsetTop}
   var par=getXYpos(elem.offsetParent);
   for (var key in par) {
      xy[key]+=par[key];
   }
   return xy;
}

function mouseclick(event){
  var e = document.getElementById('tagimage'); e = getXYpos(e);
  x = event.offsetX?(event.offsetX):event.pageX-document.getElementById('pointer_div').offsetLeft;
  y = event.offsetY?(event.offsetY):event.pageY-document.getElementById('pointer_div').offsetTop;
  var s = document.getElementById('square'); s.style.display='block';
  s.style.left = e.x+x-50;   s.style.top = e.y+y-50;
  var t = document.getElementById('taglist'); t.style.display='block';
  t.style.left = e.x+x+60;   t.style.top = e.y+y-50;
  document.pointform.xpos.value = x;
  document.pointform.ypos.value = y;
  }