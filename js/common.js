/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free CA Common JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/20/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

if (!document.getElementsByClassName) {
	document.getElementsByClassName = function(className, element) {
		var children = (element || document).getElementsByTagName('*');
		var elements = new Array();
		for (var i = 0; i < children.length; i++) {
			var child = children[i];
			var classNames = child.className.split(' ');
			for (var j = 0; j < classNames.length; j++) {
				if (classNames[j] == className) {
					elements.push(child);
					break;
				}
			}
		}
		return elements;
	};
}

function hasClass(obj, cls) {
	return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)')); 
}

function addClass(obj, cls) {
	if (!this.hasClass(obj, cls)) obj.className += ' ' + cls; 
}

function removeClass(obj, cls) {
	if (hasClass(obj, cls)) {
		var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)'); 
		obj.className = obj.className.replace(reg, ' '); 
	}
}

function toggleClass(obj,cls) {
	hasClass(obj,cls) ? removeClass(obj, cls) : addClass(obj, cls);
} 


function hasClassByID(id, cls) {
	return document.getElementById(id).className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)')); 
}

function addClassByID(id, cls) {
	if (!hasClass(id, cls)) document.getElementById(id).className += ' ' + cls; 
}

function removeClassByID(id, cls) {
	if (hasClass(id, cls)) {
		var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)'); 
		var obj = document.getElementById(id);
		obj.className = obj.className.replace(reg, ' '); 
	}
}

function exist_id(id) {
	return (document.getElementById(id) ? true : false);
}

function display_id(id, val) {
	document.getElementById(id).style.display = val;
}

function hide_frame(o) {
	o.parentNode.parentNode.style.display = "none";
}

function hide_me(o) {
	o.style.display = "none";
}

function hide_id() {
	for (var i = 0; i < hide_id.arguments.length; i++) document.getElementById(hide_id.arguments[i]).style.display = "none";
}

function show_id() {
	for (var i = 0; i < show_id.arguments.length; i++) document.getElementById(show_id.arguments[i]).style.display = "block";
}

function set_val(id, val) {
	document.getElementById(id).value = val;
}

function get_val(id) {
	return document.getElementById(id).value;
}

function set_txt(id, txt) {
	document.getElementById(id).innerHTML = txt;
}

function get_txt(id) {
	return document.getElementById(id).innerHTML;
}

function set_style(id, key, val) {
	document.getElementById(id).style[key] = val;
}

function get_style(id, key) {
	return document.getElementById(id).style[key];
}

function set_prop(id, key, val) {
	document.getElementById(id)[key] = val;
}

function get_prop(id, key) {
	return document.getElementById(id)[key];
}

function set_focus(id) {
	document.getElementById(id).focus();
}

function set_enabled(id) {
	addClass(id, 'btnEnable');
	set_prop(id, 'disabled', '');
}

function set_disabled(id) {
	removeClass(id, 'btnEnable');
	set_prop(id, 'disabled', 'disabled');
}

function I(a) {
	return parseInt(a, 10);
}

function preload(){
	for (var i = 0; i < preload.arguments.length; i++) (new Image).src = preload.arguments[i];
}

function sC(a, b, c, d) {//set Cookie: a is name, b is value, c is expired time, d is expired type: 0 is second flag, 1 is day flag
	try {
		var f = new Date();
		d ? f.setDate(f.getDate() + c) : f.setTime(f.getTime() + c);
		document.cookie = a + '=' + encodeURI(b) + (c ? ';expires=' + f.toUTCString() : '') + ';path=/';
	} catch(z) {
		return;
	}
}

function gC(a, f) {//get Cookie, f is default value
	try {
		var b,c,d='',e;
		if (document.cookie.length > 0) {
			e = '; ' + a + '=';
			b = document.cookie.indexOf(e);
			if (b < 0) {
				e = a + '=';
				b = document.cookie.indexOf(e);
			}
			if (b > -1) {
				b = b + e.length;
				c = document.cookie.indexOf(';', b);
				if (c < 0) c = document.cookie.length;
				d = decodeURIComponent(document.cookie.substring(b, c));
			}
		}
		return d ? (typeof(f) === 'number' ? I(d) : d) : f;
	} catch(z) {
		return;
	}
}






function GetOffset(o, offset) { 
		var x = o.offsetLeft + offset,
			y = o.offsetTop + offset,
			w = x + o.offsetWidth,
			h = y + o.offsetHeight; 
		return {"x": x, "y": y, "w": w, "h": h};	
}

function GetOffsetA(e, p, offset) {	
		var x = e.offsetLeft + offset,
			y = e.offsetTop + offset,
			w = e.offsetWidth,
			h = p.offsetHeight - 8;

		while (e = e.offsetParent) {	
			x += e.offsetLeft;	
			y += e.offsetTop;	
		}
		y -= h;
		w += x;
		h += y;
		
		return {"x": x, "y": y, "w": w, "h": h};	
}


function DropDown($, obj) {
	
	var p = $.parentNode,
		obj = document.getElementById(obj),
		xy = GetOffset($,-3),
		status = obj.style.display,
		bW = 0;

	status == "block" ? Hide() : Show();

	function AddEvents() {
		if (window.addEventListener) { //for firefox, chrome, safari
			window.addEventListener("click",CancelDD);
			window.addEventListener("resize",ResizeDD);
		} else { //for IE5,6,7,8
			document.attachEvent("onclick",CancelDD);
			document.attachEvent("onresize",ResizeDD);
		}
	}
	
	function RemoveEvents() {
		if (window.addEventListener) { //for firefox, chrome, safari
			window.removeEventListener('click',CancelDD);
			window.removeEventListener('resize',ResizeDD);
		} else {
			document.detachEvent('onclick',CancelDD);
			document.detachEvent('onresize',ResizeDD);
		}
	}
	
	function Show() {
		p.style.width = p.offsetWidth + "px";
		p.style.height = p.offsetHeight + "px";
		obj.style.display = "block";
		$.style.left = xy.x + "px";
		$.style.top = xy.y + "px";
		$.style.position = "absolute";
		var cW = document.documentElement.clientWidth || document.body.clientWidth;
		var bW = obj.offsetWidth;
		if ((xy.x + bW) > cW) {
			$.style.width = (cW - xy.x - 6) + "px";
		} else {
			$.style.width = obj.style.width;
		}
		$.style.height = "auto";
		addClass($, "d_active");
		
		AddEvents();
	} 
	
	function Hide() {//hide
		obj.style.display = "none";
		$.style.position = "static";
		$.style.width = "auto";
		$.style.height = "auto";
		p.style.width = "auto";
		p.style.height = "auto";
		removeClass($, "d_active");
		
		RemoveEvents();
	}
	
	function CancelDD(e) {
		var B = document.body,
		xy = GetOffset($,0);
		e = e || window.event;
		if (e.pageX || e.pageY) { 
			MX = e.pageX;
			MY = e.pageY;
		} else {//code for IE5,6,7
			MX = e.clientX + B.scrollLeft - B.clientLeft;
			MY = e.clientY + B.scrollTop - B.clientTop;
		}
		//console.log("MX:" +MX + " MY:" + MY + " L:" + xy.x + " T:" + xy.y + " R:" + xy.w + " B:"+xy.h);
		if (MX <= xy.x || MX >= xy.w || MY <= xy.y || MY >= xy.h) Hide()
		
	}
	
	function ResizeDD(e) {
		var xy = obj.style.display == "none" ? GetOffset($, -3) : GetOffset(p, 0);
		$.style.left = xy.x + "px";
		$.style.top = xy.y + "px";
		var cW = document.documentElement.clientWidth || document.body.clientWidth;
		var bW = obj.offsetWidth;
		//console.log("cw:" + cW + " bw:" + bW + " xy.x:" + xy.x);
		if ((xy.x + bW) > cW) {
			$.style.width = (cW - xy.x - 6) + "px";
		} else {
			$.style.width = obj.style.width;
		}
	}
}


function PopupMenu($, O, Style, Pos) {//$: popup position object; O: popup menu object ID; Style: 0 is left float, 1 is right float, Position:0 is block, 1 is fixed
	
	var that = $,
		$Pos = GetOffset($, 0),
		obj = document.getElementById(O),
		oPos = GetOffset(obj, 0),
		status = obj.style.visibility,
		ready = 0;

	status == "visible" ? Hide() : Show();

	function AddEvents() {
		if (window.addEventListener) { //for firefox, chrome, safari
			window.addEventListener("click",Hide);
			window.addEventListener("resize",Hide);
		} else { // for IE5,6,7,8
			document.attachEvent("onclick",Hide);
			document.attachEvent("onresize",Hide);
		}
	}
	
	function RemoveEvents() {//remove events
		if (window.addEventListener) { //attach events 
			window.removeEventListener('click',Hide);
			window.removeEventListener('resize',Hide);
		} else {
			document.detachEvent('onclick',Hide);
			document.detachEvent('onresize',Hide);
		}
	}
	
	function Show() {
		obj.style.visibility = "visible";
		obj.style.position = Pos == 1 ? "fixed" : "static";
		obj.style.left = (Style === 0 ? $Pos.x : $Pos.w - oPos.w + oPos.x) + "px";
		obj.style.top = $Pos.y + $Pos.h + "px";
		AddEvents();
	} 
	
	function Hide() {//hide	
		ready++;
		if (ready > 1) {
			obj.style.visibility = "hidden";
			obj.style.left = "0px";
			obj.style.top = "0px";
			RemoveEvents();
		}
	}
}


function selectButton($, offset) {
	var p = $.parentNode,
		e = p.getElementsByTagName("UL"),
		obj = e[0],
		xy,
		runonce = !1;
		obj.style.width = (p.offsetWidth - (offset ? offset : 11)) + "px";

	(obj.style.display=="block") ? obj.style.display="none" : obj.style.display="block";
	
	if (runonce == !1) {
		Init();
		runonce = !0;
	}
	
	function Init() {
		if (window.addEventListener) { //for firefox, chrome, safari
			window.addEventListener("click", Cancel);
		} else { // for IE5,6,7,8
			document.attachEvent("onclick", Cancel);
		}
	}
	
	function Cancel(e) {
		var B = document.body,
			xy = GetOffsetA(obj, p, 0);
		e = e || window.event;
		if (e.pageX || e.pageY) {  //for firefox, chrome, safari
			MX = e.pageX;
			MY = e.pageY;
		} else {//code for IE5,6,7
			MX = e.clientX + B.scrollLeft - B.clientLeft;
			MY = e.clientY + B.scrollTop - B.clientTop;
		}
		//console.log("MX:" +MX + " MY:" + MY + " L:" + xy.x + " T:" + xy.y + " R:" + xy.w + " B:"+xy.h);
		if (MX <= xy.x || MX >= xy.w || MY <= xy.y || MY >= xy.h) {
			obj.style.display = "none";
			if (window.addEventListener) { //attach events 
				window.removeEventListener('click', Cancel);
			} else {
				document.detachEvent('onclick', Cancel);
			}
		}
	}	
}







function SetDay(cancel) {
	var x, y;
	if (!cancel) {
		x = CA_SELECT_DATE["from"];
		y = CA_SELECT_DATE["to"];
		if (x > y) {
			VisitorsAPI.from = y;
			VisitorsAPI.to = x;
			VisitorsAPI.period = GetPeriod(y, x);
		} else {
			VisitorsAPI.from = x;
			VisitorsAPI.to = y;
			VisitorsAPI.period = GetPeriod(x, y);
		}
		sC("CA_SelectDay", VisitorsAPI.from + "" + VisitorsAPI.to, 1, 1);
		if (typeof Refresh !== "undefined") Refresh();
	}

	from_date.SetDate(VisitorsAPI.from + "", VisitorsAPI.period);
	to_date.SetDate(VisitorsAPI.to + "", VisitorsAPI.period);

	document.getElementById("period_to").innerHTML = "";
	switch (VisitorsAPI.period) {
	case 0: //today
		document.getElementById("period_from").innerHTML = LAN["Today"];
		break;
	case 1: //yesterday
		document.getElementById("period_from").innerHTML = LAN["Yesterday"];
		break;
	case 2: //past 7 days
		document.getElementById("period_from").innerHTML = LAN["Past 7 Days"];
		break;
	case 3:  //past 30 days
		document.getElementById("period_from").innerHTML = LAN["Past 30 Days"];
		break;
	case 4: //past week
		document.getElementById("period_from").innerHTML = LAN["Last Week"];
		break;
	case 5: //past month
		document.getElementById("period_from").innerHTML = LAN["Last Month"];
		break;
	case 6: //specified day
		x = VisitorsAPI.from + "";
		document.getElementById("period_from").innerHTML = x.substr(0, 4) + "-" + x.substr(4, 2) + "-" + x.substr(6, 2);
		break;
	case 7: //range
		x = VisitorsAPI.from + "";
		y = VisitorsAPI.to + "";
		document.getElementById("period_from").innerHTML = LAN["From"] + " " + x.substr(0, 4) + "-" + x.substr(4, 2) + "-" + x.substr(6, 2);
		document.getElementById("period_to").innerHTML = " " + LAN["To"] + " " + y.substr(0, 4) + "-" + y.substr(4, 2) + "-" + y.substr(6, 2);
		break;
	}
}


function GetPeriod(from, to) {
	var PERIODS = GenPeriods();
	
	if (from == to && from == PERIODS[0][0]) {
		return 0;
	} else if (from == to && from == PERIODS[1][0]) {
		return 1;
	} else if (from == PERIODS[2][0] && to == PERIODS[2][1]) {
		return 2;
	} else if (from == PERIODS[3][0] && to == PERIODS[3][1]) {
		return 3;
	} else if (from == PERIODS[4][0] && to == PERIODS[4][1]) {
		return 4;
	} else if (from == PERIODS[5][0] && to == PERIODS[5][1]) {
		return 5;
	} else if (from == to) {
		return 6;
	} else {
		return 7;
	}
}


function SetPeriod(Period) {
	var PERIODS = GenPeriods();
	
	VisitorsAPI.period = Period;
	VisitorsAPI.from = PERIODS[Period][0];
	VisitorsAPI.to   = PERIODS[Period][1];
	
	from_date.SetDate(VisitorsAPI.from, VisitorsAPI.period);
	to_date.SetDate(VisitorsAPI.to, VisitorsAPI.period);
	
	from_dp.Status();
	
	sC("CA_SelectDay", VisitorsAPI.from + "" + VisitorsAPI.to, 1, 1);
	if (typeof Refresh !== "undefined") Refresh();
}


function GenPeriods() {
	var today = I(PERIOD_TODAY);//(new Date).getTime();
	var w = I(FormatDate(today, 'W'));
	var n = I(FormatDate(today, 'N'));
	var y = I(FormatDate(today, 'yyyy'));

	n--;
	if (n < 0) {
		y--;
		n = 11
	}
	m = n + 1;
	if (m < 10) m = '0' + m;
	
	var enddate = ["31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31"];
	if (((y % 4 == 0) && (y % 100 != 0)) || (y % 400 == 0)) enddate[1] = "29";

	var d = enddate[n];
	var PERIODS = [[], [], [], [], [], []];
	var fmt = 'yyyynndd';
	
	PERIODS[0][0] = FormatDate(today, fmt);
	PERIODS[0][1] = PERIODS[0][0];
	PERIODS[1][0] = FormatDate(today - 864E5, fmt);
	PERIODS[1][1] = PERIODS[1][0];
	PERIODS[2][0] = FormatDate(today - 864E5 * 7, fmt);
	PERIODS[2][1] = FormatDate(today - 864E5, fmt);
	PERIODS[3][0] = FormatDate(today - 864E5 * 30, fmt);
	PERIODS[3][1] = FormatDate(today - 864E5, fmt);
	PERIODS[4][0] = FormatDate(today - 864E5 * (w + 7), fmt);
	PERIODS[4][1] = FormatDate(today - 864E5 * (w + 1), fmt);
	PERIODS[5][0] = y + '' + m + '01';
	PERIODS[5][1] = y + '' + m + d;

	return PERIODS;
}


function FormatDate(ptime, fmt) {	
	var weekday = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	var pdate = new Date();
	pdate.setTime(ptime);

	var o = {
		"n+": pdate.getMonth() + 1, //月份 
		"N": pdate.getMonth(), //月份 
		"d+": pdate.getDate(), //日 
		"h+": pdate.getHours(), //小时 
		"m+": pdate.getMinutes(), //分 
		"s+": pdate.getSeconds(), //秒 
		"q+": Math.floor((pdate.getMonth() + 3) / 3), //季度 
		"S" : pdate.getMilliseconds(), //毫秒 
		"W" : pdate.getDay(), //day of week
		"w" : weekday[pdate.getDay()] //weekday
	};

	if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (pdate.getFullYear() + "").substr(4 - RegExp.$1.length));
	for (var k in o) if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	
	return fmt;
}


function pGet(a) {// a:url, b:element ID
	var c, d;
	a += "&rnd=" + Math.random();
	try {
		if (window.XMLHttpRequest) {
			c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
		} else {
			c = new ActiveXObject("Microsoft.XMLHTTP");// code for IE6, IE5
		}
		c.open("GET", a, true);
		c.send(); 
		c.onreadystatechange = function() {
			if (c.readyState == 4 && c.status == 200) {
				IncludeJS(c.responseText); //return c.responseText.replace(/(^\s*)|(\s*$)/g, "");
			}
		}
	} catch (e) {
		console.log("Function [pGet] - Request Failed!");
	}
}


function IncludeJS(x) {
	try {
		if (x) {
			var b = document.getElementsByTagName("HEAD").item(0),
			c = document.createElement("script");
			c.type = "text/javascript";
			c.defer = true;
			c.text = x;
			b.appendChild(c);
		}
	} catch (e) {
		alert("Include JS Failed!");
	}
}


function RefreshPage() {
	if (typeof Refresh !== "undefined") {
		Refresh();
	} else {
		window.location.reload();
	}
}







function Visitor_API(sid, date) {
	this.sid = sid;
	this.period = 1;//0 as today, 1 as yesterday, 2 as past 7 days, 3 as past 30 days, 4 as past week, 5 as past month, 6 as range
	this.from = date;//start date//20151201;//
	this.to = date;//end date//20160701;//
	this.start = 0;//mysql limit start
	this.end = 20;//mysql limit number, default is 20
	this.sortorder = 0;//0 as DESC 降序, 1 as ASC 升序
}


if (exist_id("period")) {
	
	var CA_SELECT_DATE = {'FROM':PERIOD_YESTERDAY, 'TO':PERIOD_YESTERDAY};
	var VisitorsAPI = new Visitor_API(SID, PERIOD_YESTERDAY);
	var sDay = gC("CA_SelectDay");

	if (sDay && sDay.length == 16) {
		var x = sDay.substr(0, 8);
		var y = sDay.substr(8, 8);
		if (x > y) {
			VisitorsAPI.from = y;
			VisitorsAPI.to = x;
		} else {
			VisitorsAPI.from = x;
			VisitorsAPI.to = y;
		}
		VisitorsAPI.period = GetPeriod(VisitorsAPI.from, VisitorsAPI.to);
	} else {
		sC("CA_SelectDay", VisitorsAPI.from + "" + VisitorsAPI.to, 1, 1);
	}

	var from_dp = new DatePick("period");
 
	var from_date = new Calendar("from", period_from, period_to, VisitorsAPI.period, VisitorsAPI.from, LAN);
	from_date.Draw();

	var to_date = new Calendar("to", period_from, period_to, VisitorsAPI.period, VisitorsAPI.to, LAN);
	to_date.Draw();

	preload("images/loading.gif", "images/dp.png");
}








function selectSite(x) {		
	window.location = "manager.php?id=" + UID + "&siteid=" + x + "&menu=" + MENU + "&action=" + ACT;
}

function selectHost(x) {		
	window.location = "manager.php?id=" + UID + "&siteid=" + SID + "&menu=" + MENU + "&action=" + ACT + "&param=" + x;
}

function selectLanguage($) {
	var $ = document.getElementById($),
	RunOnce = !1;
	
	if ($.style.display == "block") {
		$.style.display = "none"
	} else {
		$.style.display = "block";
		var a = ($.parentNode.offsetWidth - 3);
		if (($.offsetWidth - 2) < a) $.style.width = a + "px";
	}
	
	if (RunOnce == !1) {
		if (window.addEventListener) { //for firefox, chrome, safari
			window.addEventListener("click",LangCancel);
		} else { // for IE5,6,7,8
			document.attachEvent("onclick",LangCancel);
		}
		RunOnce = !0;
	}

	function LangCancel(e) {
		e = e || window.event;
		var a = e.srcElement || e.target;
		if (a.id != "lang") $.style.display = "none";
	}
}





//side menu functions
var RBOX = 0;

window.onresize = function() {
	side_menu_resize();
}

window.onload = function() {
	side_menu_resize(1);
	document.getElementById("rightbox").addEventListener("touchstart", function(){switch_menu(1)}, false);
}

function menu_click(o) {
	var b = o.nextSibling;
	var status = b.nextSibling.style.display;
	o.className = status === "block" ? "menu_hidden" : "menu_shown";
	b.nextSibling.style.display = status === "block" ? "none" : "block";
}
/*
function menu_click2(o) {
	var obj = o.nextSibling.nextSibling;
	obj.style.display = obj.style.display === "block" ? "none" : "block";
}
*/
function side_menu_resize(x) {
	try {
		var bcw = document.body.clientWidth || document.documentElement.clientWidth,//browser client width by js
			bch = document.body.clientHeight || document.documentElement.clientHeight,//browser client height by js
			objL = document.getElementById("leftbox"),
			objR = document.getElementById("rightbox"),
			leftboxW = parseInt(objL.offsetWidth),
			rightboxW = parseInt(objR.offsetWidth);

		if (bcw > 1200) {
			if (objL.style.left === '' || objL.style.left === '0px') objR.style.width = "calc(100% - 211px)";//(bcw - leftboxW) + "px";
		} else {
			objR.style.width = "100%";
			if (x === 1) {
				objL.style.left = (-leftboxW) + "px";
				document.getElementById("hidebtn").className = "div_side_menu_show_btn";
			}
		}
		
		document.getElementById("side_menu").style.height = (bch - 142) + "px";
		if (RBOX != rightboxW) {
			RBOX = rightboxW;
			Resize();
		}
	} catch (z) {
		console.log("Side Menu Resize Error :" + z.name + z.message);
	}	
}

function switch_menu(x) {
	try {
		var objL = document.getElementById("leftbox"),
			objR = document.getElementById("rightbox"),
			objH = document.getElementById("hidebtn"),
			leftboxW = parseInt(objL.offsetWidth),
			bcw = document.documentElement.clientWidth || document.body.clientWidth;//browser client width by js;
			
		if (x === 1) {
			if (objL.style.left === '' || objL.style.left === '0px') {
				objL.style.left = (-leftboxW) + "px";
				objR.style.width = "100%";
				objH.className = "div_side_menu_show_btn";
			}
		} else if (objL.style.left === '' || objL.style.left === '0px') {
			objL.style.left = (-leftboxW) + "px";
			objR.style.width = "100%";
			objH.className = "div_side_menu_show_btn";
		} else {
			if (bcw > 1200) objR.style.width = (bcw - leftboxW) + "px";
			objL.style.left = "0px";
			objH.className = "div_side_menu_hide_btn";
		}

		if (bcw > 1200) Resize();
	} catch (z) {
		console.log("Switch Menu Error :" + z.name + z.message);
	}
}

