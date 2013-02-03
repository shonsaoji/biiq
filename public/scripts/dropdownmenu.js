var TimeOut         = 300;
var currentLayer    = null;
var currentitem     = null;
var currentLayerNum = 0;
var noClose         = 0;
var closeTimer      = null;

function mopen(n) {
  var l  = document.getElementById("menu"+n);
  var mm = document.getElementById("mmenu"+n);
	
  if(l) {
	if(l.style.visibility!='visible'){
		greyoutnew(true);
	}
    l.style.visibility='visible';
    if(currentLayer && (currentLayerNum != n))
	currentLayer.style.visibility='hidden';
    currentLayer = l;
    currentitem = mm;
    currentLayerNum = n;			
  } else if(currentLayer) {
    currentLayer.style.visibility='hidden';
    currentLayerNum = 0;
    currentitem = null;
    currentLayer = null;

 	}


}

function mclosetime() {
  closeTimer = window.setTimeout(mclose, TimeOut);

}

function mcancelclosetime() {
  if(closeTimer) {
    window.clearTimeout(closeTimer);
    closeTimer = null;
  }
}

function mclose() {
  if(currentLayer && noClose!=1)   {
	greyoutnew(false);
    currentLayer.style.visibility='hidden';
    currentLayerNum = 0;
    currentLayer = null;
    currentitem = null;
  } else {
    noClose = 0;
  }
  currentLayer = null;
  currentitem = null;
}

document.onclick = mclose; 

/**********************************************************************************************************************************/
/* STARTS : Function for gray box display for popup box                                                                           */
/**********************************************************************************************************************************/
var current_div_poped

function addEvent(obj ,evt, fnc) {
    if (obj.addEventListener)
        obj.addEventListener(evt,fnc,false);
    else if (obj.attachEvent)
        obj.attachEvent('on'+evt,fnc);
    else
        return false;
    return true;
}

function removeEvent(obj ,evt, fnc) {
     if (obj.removeEventListener)
         obj.removeEventListener(evt,fnc,false);
    else if (obj.detachEvent)
         obj.detachEvent('on'+evt,fnc);
    else
         return false;
    return true;
}

function appendElement(node,tag,id,htm) {
    var ne = document.createElement(tag);
    if(id) ne.id = id;
    if(htm) ne.innerHTML = htm;
    node.appendChild(ne);
}


function showPopup(popDiv) {

	if( popDiv	==	'signUp' ) {
		greyoutnew(true);
		var l  = document.getElementById("signUp");
		l.style.visibility='visible';
		current_div_poped='signUp';
	}
	if( popDiv	==	'sugarCRM' ) {
		greyoutnew(true);
		var l  = document.getElementById("sugarCRM");
		l.style.display='inline';
		current_div_poped='sugarCRM';
	}
}

var count;
count =0;
function showPopup_menu(popDiv) {

	if(popDiv	==	'menu1' && count==1 && current_div_poped	 ==	'menu1' ){
		close_all();
		return;
	}
	if(popDiv	==	'menu2' && count==1 && current_div_poped	 ==	'menu2' ){
		close_all();
		return;
	}
	if(popDiv !=	current_div_poped && count==1 ){
		close_all();
	}
	if( popDiv	==	'menu1' && count==0 && current_div_poped !=	'menu1') {
		greyoutnew(true);
		document.getElementById('mmenu1').style.zIndex = "3";
		var l  = document.getElementById("menu1");
		l.style.visibility='visible';
		current_div_poped='menu1';
		count=1;
	}
	if( popDiv	==	'menu2' && count==0 && current_div_poped !=	'menu2') {
		greyoutnew(true);
		document.getElementById('mmenu1').style.zIndex = "";
		document.getElementById('mmenu2').style.zIndex = "3";
		var l  = document.getElementById("menu2");
		l.style.visibility='visible';
		current_div_poped='menu2';
		count=1;
	}
}

function hidePopup(popDiv) {

	if( popDiv	==	'menu1' ) {
		document.getElementById('mmenu1').style.zIndex = "0";
		var l  = document.getElementById("menu1");
		l.style.visibility='hidden';
		count=0
	}
	if( popDiv	==	'menu2' ) {
		document.getElementById('mmenu2').style.zIndex = "0";
		var l  = document.getElementById("menu2");
		l.style.visibility='hidden';
		count=0
	}
	if( popDiv	==	'signUp' ) {
		var l  = document.getElementById("signUp");
		l.style.visibility='hidden';
	}
	if( popDiv	==	'sugarCRM' ) {
		var l  = document.getElementById("sugarCRM");
		l.style.display='none';
	}

    greyoutnew(false);
	current_div_poped='';
}

function close_all() {
	hidePopup(current_div_poped);
}

function greyoutnew(d,z) {

    var obj = document.getElementById('greyout');
    if(!obj) {
        appendElement(document.body,'div','greyout');
        obj = document.getElementById('greyout');
        obj.style.position = 'absolute';
        obj.style.top = '0px';
        obj.style.left = '0px';
        obj.style.cursor = 'not-allowed';
        obj.style.background = '#111';
        obj.style.opacity = '.5';
        obj.style.filter = 'alpha(opacity=50)';
        obj.style.zIndex = '2'; // Added by muhtu
		if (obj.addEventListener){
		obj.addEventListener('click', close_all, false); 
		} else if (obj.attachEvent){
		obj.attachEvent('onclick', close_all);
		}

		//obj.addEventListener('click',function (e) {  close_all();},true);

    }
    if(d) {
        if(!z){ z - 50 }
        //obj.style.zIndex = z;
        obj.style.height = Math.max(document.body.scrollHeight,document.body.clientHeight)+'px';
        obj.style.width  = Math.max(document.body.scrollWidth,document.body.clientWidth)+'px';
        obj.style.display = 'block';
        addEvent(window,'resize',greyoutResize);
    }
    else {
        obj.style.display = 'none';   
        removeEvent(window,'resize',greyoutResize);
    }
}
 
function greyoutResize() {
    var obj = document.getElementById('greyout');
    obj.style.height = document.body.clientHeight+'px';
    obj.style.width  = document.body.clientWidth+'px';
    obj.style.height = Math.max(document.body.scrollHeight,document.body.clientHeight)+'px';
    obj.style.width  = Math.max(document.body.scrollWidth,document.body.clientWidth)+'px';
}
 
/**********************************************************************************************************************************/
/* ENDS  : Function for gray box display for popup box                                                                            */
/**********************************************************************************************************************************/

function show_demo(popDiv,id) {
	document.getElementById("swap1").src='images/Q1.jpg';
	document.getElementById("swap2").src='images/Q2.jpg';
	document.getElementById("swap3").src='images/Q3.jpg';
	document.getElementById("swap4").src='images/Q4.jpg';
	if( popDiv	==	'yes' ) {
		document.getElementById("swap"+id).src='images/Q'+id+'-n.jpg';
		var y  = document.getElementById("yes");
		y.style.display='inline';
		var n  = document.getElementById("no");
		n.style.display='none';
	}
	if( popDiv	==	'no' ) {
		document.getElementById("swap"+id).src='images/Q'+id+'-y.jpg';
		var n  = document.getElementById("no");
		n.style.display='inline';
		var y  = document.getElementById("yes");
		y.style.display='none';
	}
}

function showPopup_3box(divBox,t) {
	document.getElementById("userselection").style.display='inline';
	current_div_poped='userselection';
	document.getElementById("userselection").innerHTML='<img src="images/'+divBox+'.gif"  border="0">';
	document.getElementById("userselection").style.top=t+'px';
}

function showPopup_3box_hide() {
	var l  = document.getElementById("userselection");
	l.style.display='none';
	document.getElementById("userselection").innerHTML='';
}