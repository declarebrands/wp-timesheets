/*<![CDATA[*/
function Test(rad){
 var rads=document.getElementsByName(rad.name);
 document.getElementById('depot').style.display=(rads[1].checked)?'block':'none';
}
/*]]>*/
