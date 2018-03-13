/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free ITABLE API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/
	
function RTTBAPI(sid, r, type, timezone, timer, host, box, box2, Lan, Extra) {
		
	this.sid = sid;
	this.q = '';
	this.type = type;
	this.tz = timezone;
	this.start = 0;
	this.end = 10;
		
	var LogData = [],
		title = getTitle(type),
		cName = title.replace(/\s+/g,''),
		Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
		APIUrl = Protocol + host + '/api/api_ca.php?',
		PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
		HTML_LOADING = "<table><tr class='tra'><td class='tdmid'><img class='mid' src='images/loading.gif'/>&nbsp;Processing...</td></tr></table>",
		HTML_NODATA_TABLE = "<div style='width:auto; float:none; font-size:36px; color:#ddd; text-align:center; line-height:198px; border:#ccc 1px solid;'>No Data</div>",
		hTimer = 0,
		that = this;

	timer = timer < 1 ? 0 : timer < 5000 ? 15000 : timer;
	
	this.q = title;	
	this.end = gC("TB_" + cName + "Rows") || 10;//mysql limit number, default is 10
	
	switch (type) {
	case 0:
		document.getElementById(box).innerHTML = "<div id='CARTLOG" + cName + "' class='ca_table'></div>";
		document.getElementById(box2).innerHTML = "<div class='ca_online_no'><p id='CARTNO" + cName + "' class='ca_online_p'>0</p><p id='CARTPEAK" + cName + "' class='ca_online_p2'>"+ Lan['Online Visitors'] + "<br/><br/>"+ Lan['Peak'] + " PV: 0&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " UV: 0&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " IP: 0</p></div>";
		break;
	case 1:
		document.getElementById(box).innerHTML = "<div id='CARTLOG" + cName + "' class='ca_table'></div>";
		break;
	case 2:
		document.getElementById(box).innerHTML = "<div class='ca_online_no'><p id='CARTNO" + cName + "' class='ca_online_p'>0</p><p id='CARTPEAK" + cName + "' class='ca_online_p2'>"+ Lan['Online Visitors'] + "<br/><br/>"+ Lan['Peak'] + " PV: 0&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " UV: 0&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " IP: 0</p></div>";
		break;
	}
	
	if (obj = document.getElementById(box + '_R')) obj.href = 'javascript:' + box + '.run()'; 
	if (obj = document.getElementById(box2 + '_R')) obj.href = 'javascript:' + box + '.run()'; 
	//add resize events
	//window.addEventListener ? window.addEventListener('resize',Resize) : document.attachEvent('onresize',Resize);
	
		
	//********************************** Public Class Function Begin **********************************	
	this.run = function() {
		this.wGet(PassportUrl, true, 0);
		if (timer > 0 && hTimer === 0) hTimer = setInterval(Update, timer);
	}


	this.callAjax = function(pass,flag){//flag =0 is ini, flag =1 is update

		if (flag == 0) document.getElementById('CARTLOG' + cName).innerHTML = HTML_LOADING;
		
		var d=[];
		for(var k in this){
			d.push(k + "=" + this[k]);
			if (k == 'end') break;
		}
		var url = APIUrl + pass + d.join("&"),
			myAjax;
		
		try {
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange=function(){
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					LogData[0] = [0,0,0];
					
					if (myAjax.responseText == ""){
						if (type == 0 || type == 2) {
							document.getElementById('CARTNO' + cName).innerHTML = "0";
							document.getElementById('CARTPEAK' + cName).innerHTML = Lan['Online Visitors'] + "<br/><br/>"+ Lan['Peak'] + " PV: 0&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " UV: 0&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " IP: 0";
						}
						if (type == 0 || type == 1) draw(LogData);
					} else {
						var tmp = eval(myAjax.responseText);
						LogData = str2object(tmp);
						if (type == 0 || type == 2) {
							document.getElementById('CARTNO' + cName).innerHTML = LogData[0][0];
							document.getElementById('CARTPEAK' + cName).innerHTML = Lan['Online Visitors'] + "<br/><br/>"+ Lan['Peak'] + " PV: " + LogData[0][3] + "&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " UV: " + LogData[0][4] + "&nbsp;&nbsp;&nbsp;"+ Lan['Peak'] + " IP: " + LogData[0][5];
						}
						if (type == 0 || type == 1) draw(LogData);
					}
					
					//if (flag == 0) {
					//	setInterval(Update, 15000);
					//}
				}
			}
			
			myAjax.open("GET", url, true);
			myAjax.send();
		} catch(e) {	//alert(e.name + ": " + e.message);
			alert('XMLHttpRequest Error');
		}

	};

	this.wGet = function(a,b,flag){// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get"),
		var c, v="";
			a += "&rnd=" + Math.random();
			
		try {
			if (window.XMLHttpRequest) {
				c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c = new ActiveXObject("Microsoft.XMLHTTP");// code for IE6, IE5
			}
			c.open("GET", a, b);
			c.send(); 
			c.onreadystatechange = function(){
				if (c.readyState == 4 && c.status == 200) {
			   	 	v = c.responseText.replace(/(^\s*)|(\s*$)/g, "");
					if (v) that.callAjax(v,flag);
				}
			}
		}
		catch (e) {
			//alert("Request failed!");
		}
	};

	//********** table class function begin *********

	this.SetRow = function (x) {
		this.end = parseInt(x);
		this.start = 0;
		sC('TB_' + cName + 'Rows', x, 30);
		this.wGet(PassportUrl, true, 0);
	};


	this.GoPage = function(x) {
		this.start = (x - 1) * this.end;
		this.wGet(PassportUrl, true, 0);
	};


	this.PreviousPage = function() {
		if ((this.start - this.end * 5) < 0) {
			this.GoPage(1);
			return;
		} else {
			this.start -= this.end * 5;
		}
		this.wGet(PassportUrl, true, 0);
	};


	this.NextPage = function() {
		if ((this.start + this.end * 5) < LogData[0][0]) {
			this.start += this.end * 5;
		} else {
			var pages = Math.ceil(LogData[0][0] / this.end);
			this.GoPage(pages);
			return;
		}
		this.wGet(PassportUrl, true, 0);
	};


	this.resize = function() {
		Resize();
	};

	//********** table class function end *********


//*************************** Modular Realtime Visitor Log Begin ****************************
	function draw(x){

		var b = parseInt(document.getElementById('CARTLOG' + cName).offsetWidth);
		if (b >= 1200) {
		  a = 1;
		} else if (b >= 800) {
		  a = 2;
		} else if (b >= 600) {
		  a = 3;
		} else {
		  a = 4;
		}
		
		switch (a) {
		default:
		case 1://max width
			document.getElementById('CARTLOG' + cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:6%' >"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:23%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhlt'  style='width:10%'>"+ Lan['Source'] +"</td>"+
			"<td class='tdhlt'  style='width:5%' >"+ Lan['Max Read'] +"</td>"+
			"<td class='tdhmid' style='width:5%' >"+ Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:5%' >"+ Lan['Delay'] +"</td>"+
			"<td class='tdhmid' style='width:5%' >"+ Lan['DOM Ready'] +"</td>"+
			"<td class='tdhmid' style='width:5%' >"+ Lan['Load'] +"</td>"+
			"<td class='tdhmid' style='width:5%' >"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid' style='width:9%' >"+ Lan['VID'] +"</td>"+
			"<td class='tdhmid' style='width:8%' >"+ Lan['IP'] +"</td>"+
			"<td class='tdhmid pR' style='width:14%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 2://middle width
			document.getElementById('CARTLOG' + cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:10%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:25%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhlt'  style='width:10%'>"+ Lan['Source'] +"</td>"+
			"<td class='tdhlt'  style='width:10%'>"+ Lan['Max Read'] +"</td>"+
			"<td class='tdhmid' style='width:5%' >"+ Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid' style='width:15%'>"+ Lan['IP'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 3://small width
			document.getElementById('CARTLOG' + cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:15%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:45%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:15%'>"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 4://min width
			document.getElementById('CARTLOG' + cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:20%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:45%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid' style='width:20%'>"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		}

	}//end draw function

	function genLogHtml(y, x){
		try {
			var cols;
			switch (y) {
			case 1:
				cols = 12;
				break;
			case 2:
				cols = 8;
				break;
			case 3:
				cols = 5;
				break;
			case 4:
				cols = 4;
				break;
			}

			var i = x[0][2];
			if (typeof(x[0]) === "undefined" || i < 1) return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
			
			var n=true,TB_TEXT="",LOCATION="",CITY="",PAGE="",TITLE="",FROM="",PG="",RF="",KW="";
				
			switch (y) {
			default:
			case 1://max width		
				if(i) {
					
					for (var row = 1; row <= i ; row++) {
						LOCATION = x[row]['l1'].replace(/'/g,"&apos;") || '';
						if (x[row]['l2']) LOCATION += "-" + (x[row]['l2'].replace(/'/g,"&apos;"));
						if (x[row]['l3']) LOCATION += "-" + (x[row]['l3'].replace(/'/g,"&apos;"));
						PAGE = (x[row]['pg']);
						TITLE = (x[row]['dt']);
						RF = (x[row]['rf']);
						KW = UTF82Native(x[row]['kw']);
			
						if (KW){
							FROM = RF + "(" + KW + ")";//SE
						} else {
							if (RF) {
								FROM = RF;//REFERRER
							} else {
								FROM = "Direct Entry"
							}
						}
						var t_delay = parseInt(x[row]['ds']),
						t_ready = parseInt(x[row]['rs']),
						t_load = parseInt(x[row]['ls']),
						t_online = parseInt(x[row]['ols']);	
			
						if (n) {
							TB_TEXT +="<tr class='tra'>";
						} else {
							TB_TEXT +="<tr class='trb'>";
						}
						
						TB_TEXT +=
			"<td class='tdltnoborder'>"+FormatDate(x[row]['rn']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'>"+
				"<a class='lnk' target='_blank' href='"+PAGE+"'>"+TITLE+"<br/>"+PAGE+"</a>"+
			"</td>"+
			"<td class='tdltnoborder'>"+FROM+"</td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+x[row]['mnry']+";"+x[row]['mxry']+"<br/>"+
				"X: "+x[row]['mnrx']+";"+x[row]['mxrx']+
			"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['pv']+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_delay,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_ready,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_load,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_online,1)+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['vid']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['ip']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+LOCATION+"'>"+LOCATION+"</td></tr>";
			
						n = !n;
						
					}//end for
					return TB_TEXT;
				}
				break;
				
			case 2:
				if(i) {
					
					for (var row = 1; row <= i ; row++) {
						LOCATION = x[row]['l1'].replace(/'/g,"&apos;") || '';
						if (x[row]['l2']) LOCATION += "-" + (x[row]['l2'].replace(/'/g,"&apos;"));
						if (x[row]['l3']) LOCATION += "-" + (x[row]['l3'].replace(/'/g,"&apos;"));
						PAGE = (x[row]['pg']);
						TITLE = (x[row]['dt']);
						RF = (x[row]['rf']);
						KW = UTF82Native(x[row]['kw']);
			
						if (KW){
							FROM = RF + "(" + KW + ")";//SE
						} else {
							if (RF) {
								FROM = RF;//REFERRER
							} else {
								FROM = "Direct Entry"
							}
						}
						var t_online = parseInt(x[row]['ols']);	
			
						if (n) {
							TB_TEXT +="<tr class='tra'>";
						} else {
							TB_TEXT +="<tr class='trb'>";
						}
						
						TB_TEXT +=
			"<td class='tdltnoborder'>"+FormatDate(x[row]['rn']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'>"+
				"<a class='lnk' target='_blank' href='"+PAGE+"'>"+TITLE+"<br/>"+PAGE+"</a>"+
			"</td>"+
			"<td class='tdltnoborder'>"+FROM+"</td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+x[row]['mnry']+";"+x[row]['mxry']+"<br/>"+
				"X: "+x[row]['mnrx']+";"+x[row]['mxrx']+
			"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['pv']+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_online,1)+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['ip']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+LOCATION+"'>"+LOCATION+"</td></tr>";
			
						n = !n;
						
					}//end for
					return TB_TEXT;
				}
				break;
				
			case 3:
				if(i) {
					
					for (var row = 1; row <= i ; row++) {
						LOCATION = x[row]['l1'].replace(/'/g,"&apos;") || '';
						if (x[row]['l2']) LOCATION += "-" + (x[row]['l2'].replace(/'/g,"&apos;"));
						if (x[row]['l3']) LOCATION += "-" + (x[row]['l3'].replace(/'/g,"&apos;"));
						CITY = x[row]['l3'].replace(/'/g,"&apos;") || x[row]['l2'].replace(/'/g,"&apos;") || x[row]['l1'].replace(/'/g,"&apos;");
						PAGE = (x[row]['pg']);
						TITLE = (x[row]['dt']);
						RF = (x[row]['rf']);
						KW = UTF82Native(x[row]['kw']);
			
						if (KW){
							FROM = RF + "(" + KW + ")";//SE
						} else {
							if (RF) {
								FROM = RF;//REFERRER
							} else {
								FROM = "Direct Entry"
							}
						}
						var t_online = parseInt(x[row]['ols']);	
			
						if (n) {
							TB_TEXT +="<tr class='tra'>";
						} else {
							TB_TEXT +="<tr class='trb'>";
						}
						
						TB_TEXT +=
			"<td class='tdltnoborder'>"+FormatDate(x[row]['rn']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'>"+
				"<a class='lnk' target='_blank' href='"+PAGE+"'>"+TITLE+"<br/>"+FROM+"</a>"+
			"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['pv']+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_online,1)+"</td>"+
			"<td class='tdmidnoborder pR' title='"+LOCATION+"'>"+CITY+"</td></tr>";
			
						n = !n;
						
					}//end for
					return TB_TEXT;
				}
				break;
				
			case 4:
				if(i) {
					
					for (var row = 1; row <= i ; row++) {
						LOCATION = x[row]['l1'].replace(/'/g,"&apos;") || '';
						if (x[row]['l2']) LOCATION += "-" + (x[row]['l2'].replace(/'/g,"&apos;"));
						if (x[row]['l3']) LOCATION += "-" + (x[row]['l3'].replace(/'/g,"&apos;"));
						CITY = x[row]['l4'] || 'unknown';
						PAGE = (x[row]['pg']);
						TITLE = (x[row]['dt']);
						RF = (x[row]['rf']);
						KW = UTF82Native(x[row]['kw']);
			
						if (KW){
							FROM = RF + "(" + KW + ")";//SE
						} else {
							if (RF) {
								FROM = RF;//REFERRER
							} else {
								FROM = "Direct Entry"
							}
						}
						var t_online = parseInt(x[row]['ols']);	
			
						if (n) {
							TB_TEXT +="<tr class='tra'>";
						} else {
							TB_TEXT +="<tr class='trb'>";
						}
						
						TB_TEXT +=
			"<td class='tdltnoborder'>"+FormatDate(x[row]['rn']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'>"+
				"<a class='lnk' target='_blank' href='"+PAGE+"'>"+TITLE+"<br/>"+FROM+"</a>"+
			"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(t_online,1)+"</td>"+
			"<td class='tdmidnoborder pR' title='"+LOCATION+"'>"+CITY+"</td></tr>";
			
						n = !n;
						
					}//end for
					return TB_TEXT;
				}
				break;
			}
		} catch(z) {
			//console.log(z);
			return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";	
		}		
	}


	function pagesHtml(x){
		if (x[0] == undefined) return '';
		//
		var pages = Math.ceil(x[0][0] / that.end),
		cPage = (x[0][1] / that.end) + 1,
		start = 1,//start PAGE
		end = pages,//end PAGE
		limit = 5,//limited PAGE
		textHtml ="<div class='tbfootbar'>";
		
		if (pages > limit) {
			start = (cPage - Math.floor(limit / 2));
			if (start < 1) start = 1;
			end = start + limit - 1;
			if (end > pages){
				end = pages;
				start = pages - limit + 1;
			}
		}
		
		if (pages > limit){
			textHtml += "<a class='pagebut' onclick='" + box + ".GoPage(1)' title='First Page'>&laquo;</a>";
			textHtml += "<a class='pagebut' onclick='" + box + ".LastPage()' title='Previous Page'>&lsaquo;</a>";
		}
		
		for (var i=start; i<=end; i++){
			if (i == cPage){
				textHtml += "<span class='page_focus'>" + i + "</span>";
			} else {
				textHtml += "<a class='page' onclick='" + box + ".GoPage(" + i + ")'>" + i + "</a>";
			}
		}
		
		if (pages > limit){
			textHtml += "<a class='pagebut' onclick='" + box + ".NextPage()' title='Next Page'>&rsaquo;</a>";
			textHtml += "<a class='pagebut' onclick='" + box + ".GoPage(" + pages + ")' title='Last Page'>&raquo;</a>";
		}


		textHtml += "<ul><li><a class='select' onclick='selectButton(this)'>" + that.end + "</a><ul id='" + box + "rows'>";
		switch (parseInt(that.end)){
		default:
		case 10:
			textHtml += "<li><span>10</span></li><li><a onclick='" + box + ".SetRow(20)'>20</a><li><a onclick='" + box + ".SetRow(50)'>50</a></li><li><a onclick='" + box + ".SetRow(100)'>100</a></li><li><a onclick='" + box + ".SetRow(200)'>200</a></li></ul>";
			break;
		case 20:
			textHtml += "<li><a onclick='" + box + ".SetRow(10)'>10</a><li><span>20</span></li><li><a onclick='" + box + ".SetRow(50)'>50</a></li><li><a onclick='" + box + ".SetRow(100)'>100</a></li><li><a onclick='" + box + ".SetRow(200)'>200</a></li></ul>";
			break;
		case 50:
			textHtml += "<li><a onclick='" + box + ".SetRow(10)'>10</a><li><a onclick='" + box + ".SetRow(20)'>20</a></li><li><span>50</span></li><li><a onclick='" + box + ".SetRow(100)'>100</a></li><li><a onclick='" + box + ".SetRow(200)'>200</a></li></ul>";
			break;
		case 100:
			textHtml += "<li><a onclick='" + box + ".SetRow(10)'>10</a><li><a onclick='" + box + ".SetRow(20)'>20</a></li><li><a onclick='" + box + ".SetRow(50)'>50</a></li><li><span>100</span></li><li><a onclick='" + box + ".SetRow(200)'>200</a></li></ul>";
			break;
		case 200:
			textHtml += "<li><a onclick='" + box + ".SetRow(10)'>10</a><li><a onclick='" + box + ".SetRow(20)'>20</a></li><li><a onclick='" + box + ".SetRow(50)'>50</a></li><li><a onclick='" + box + ".SetRow(100)'>100</a></li><li><span>200</span></li></ul>";
			break;
		}
		textHtml += "</li></ul></div>";

		return textHtml;
	}


	function Resize(){
		draw(LogData);
	}


	function Update(){
		if (document.hidden) return;
		that.wGet(PassportUrl, true, 1); 
	}
		
	function getTitle(type) {
			switch (type) {
			case 0:
				return 'online';//online visitor log & number
			case 1:
				return 'online log';//online visitor log
			case 2:
				return 'online no';//online visitor number
			}
			return '';
	}
//**************************** Modular Realtime Visitor Log End *****************************


//*********************************** Common Fuction Begin **********************************

	function UTF82Native(code) {
		try {
			var a = decodeURIComponent(code);
			return (unescape(a.replace(/&#x/g, "%u").replace(/;/g, "")));
		} catch(z) {
			return code;
		}
	}
	
	function FormatDate(ptime, fmt) {	
		var weekday=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	
		var pdate = new Date();
		var offsetLocal = pdate.getTimezoneOffset() * 60000;//本地时间与GMT时间的时间偏移差
		pdate.setTime(ptime + offsetLocal + 3600000 * Extra);//设置时区与GMT的时间偏移
		var o = {
			"M+": pdate.getMonth() + 1,//月份 
			"d+": pdate.getDate(),//日 
			"h+": pdate.getHours(),//小时 
			"m+": pdate.getMinutes(),//分 
			"s+": pdate.getSeconds(),//秒 
			"q+": Math.floor((pdate.getMonth() + 3) / 3),//季度 
			"S": pdate.getMilliseconds(), //毫秒 
			"w+": weekday[pdate.getDay()]//day of week
		};
		if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (pdate.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o) if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	}
	
	function FormatTime(ptime,op){
		var fmt="";
		switch (op) {
		case 0:
			if (ptime >= 3600000) {
				fmt="hh:mm:ss";
			} else if (ptime >= 60000) {
				fmt="mm:ss";
			} else {
				fmt="s.S";
			}
			break;
		case 1:	//hours
			if (ptime >= 3600000) {
				fmt="HH:mm:ss";
			} else {
				fmt="mm:ss";
			}
			break;
		case 2:	//minutes
			fmt="M:ss";
			break;
		case 3: //seconds
			fmt="S";
			break;
		}
		
		var pdate = new Date();
		pdate.setTime(ptime);
		var o = {
			"h+": pdate.getHours(),//小时 
			"H+": Math.floor(ptime/3600000),//小时 
			"m+": pdate.getMinutes(),//分 
			"M+": Math.floor(ptime/60000),//
			"s+": pdate.getSeconds(),//秒
			"C+": Math.floor(ptime/1000), //
			"S": (ptime/1000) //1000毫秒格式，无前导零
		};
		if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (pdate.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o) if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	}

	function gC(a) {//get Cookie, a: cookie name
		var a,b,c,d;
		if (document.cookie.length > 0) {
			a += "=";
			b = document.cookie.indexOf(a);
			if (b != -1) {
				b = b + a.length;
				c = document.cookie.indexOf(";", b);
				if (c == -1) c = document.cookie.length;
				d = unescape(document.cookie.substring(b, c));
			}
		}
		return d ? d : '';
	}
	
	function str2object(x) {
		var a,
			b,
			c,
			l = x.length,
			ret = [];
			ret.push(x[0]);

		for (var n = 1; n < l; n++) {
			a = x[n];
			if (a) {
				a = a.split('&');
				b = a.length;
				ret[n] = {'stat':0, 'rn':0, 'vid':0, 'ip':0, 'ipdb':0, 'pg':'', 'rf':'', 'kw':'', 'pv':0, 'dt':'', 'ds':0, 'rs':0, 'ls':0, 'ols':0, 'mnrx':0, 'mnry':0, 'mxrx':0, 'mxry':0};
				for (var i = 0; i < b; i++) {
					c = a[i];
					if (c) {
						c = c.split('=');
						ret[n][c[0]] = UTF82Native(c[1]);
					}
				}
			}
		}
		return ret;
	}

	function sC(a, b, c) {//set Cookie,a: cookie name, b: cookie value, c: cookie expriod
		var d = new Date();
		d.setDate(d.getDate() + c);
		document.cookie = a + "=" + escape(b) + ((c == null) ? "":";expires=" + d.toUTCString());
	}



}//end API function

