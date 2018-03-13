/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free ILOG API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved. *
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

	
function LOGAPI(sid, r, from, to, period, type, timezone, key, timer, cType, host, box, sortorder, W, H, Lan, Extra) {
		
	this.sid = sid;
	this.q = '';
	this.type = type;
	this.key = key;
	this.period = period;//0 as day, 1 as week, 2 as month, 3 as year, 4 as date range
	this.from = from;//start date//20151201;//
	this.to = to;//end date
	this.tz = timezone;
	this.start = 0;
	this.end = 10;
	this.sortorder = sortorder;//0 as DESC 降序, 1 as ASC 升序
	

	var AllData = 0;//0 => not show all data, 1 => show all data

	var LogData = [],
		title = getTitle(type),
		cName = title.replace(/\s+/g,''),
		Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
		APIUrl = Protocol + host + '/api/api_ca.php?',
		PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
		HTML_LOADING = "<table><tr class='tra'><td class='tdmid'><img class='mid' src='images/loading.gif'/>&nbsp;Processing...</td></tr></table>",
		HTML_NODATA_TABLE = "<div style='width:auto; float:none; font-size:36px; color:#ddd; text-align:center; line-height:198px; border-top:#ccc 1px solid; border-bottom:#ccc 1px solid;'>No Data</div>",
		that = this;
		
	this.end = gC("TB_" + cName + "Rows") || 10;//mysql limit number, default is 10
		
	var VisitStatusA = {
		"0":"unknown",
		"1":"Http Request",
		"2":"DOM Ready",
		"3":"Page Loaded Event",
		"4":"Hibernate Event",
		"5":"Unload Event",
		"6":"Heart-Beat Event",
		"7":"Visitor Surfing",
		"8":"Click Event",
		"9":"Wake Up Event"
	};


	//add tools
	var obj;
	if (obj = document.getElementById(box + '_R'))   obj.href = 'javascript:' + box + '.run(9)';
	if (obj = document.getElementById(box + '_TB'))  obj.href = 'javascript:' + box + '.run(0)'; 
	if (obj = document.getElementById(box + '_ALL')) obj.href = 'javascript:' + box + '.run(1)'; 

	//add event
	//window.addEventListener ? window.addEventListener("resize", Resize) : document.attachEvent("onresize", Resize);
	
	//********************************** Public Class Function Begin **********************************	
	this.run = function(a) {
		switch (a) {
		case 0:
		case 1:
			AllData = a
			break;
		}

		this.end = gC("TB_" + cName + "Rows") || 10;//mysql limit number, default is 10
		document.getElementById(box).innerHTML = "<div id='" + cName + "' class='ca_table'></div>";
		
		//run
		this.wGet(PassportUrl, true, 0);
	}

	this.callAjax = function(pass, flag) {//flag = 0 is ini, flag = 1 is update
		document.getElementById(cName).innerHTML = HTML_LOADING;
		
		switch (cType) {
		case 0:
			this.q = 'visitor log';
			break;
		case 1:
			this.q = 'rv log';
			break;
		case 2:
			this.q = 'robot log';
			break;
		}
		
		var d=[];
		for(var k in this) {
			d.push(k + "=" + this[k]);
			if (k == 'sortorder') break;
		}
		var url = APIUrl + pass + d.join('&'), 
			myAjax;
			
		try {
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange=function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					LogData = [];
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '')
					if (v !== '') {
						LogData = eval(v);
					} else {
						document.getElementById(cName).innerHTML = HTML_NODATA_TABLE;
						return;
					}
					
					switch (cType) {
					case 0://visitor log
					case 1://returning visitor log
						drawTable_V(LogData);
						break;
					case 2://robot log
						drawTable_RB(LogData);
						break;
					}
				}
			}
			
			myAjax.open("GET", url, true);
			myAjax.send();
		} catch(e) {	//alert(e.name + ": " + e.message);
			alert('XMLHttpRequest Error - iLog');
		}
	};

	this.wGet = function(a, b, flag) {// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get")
		var c, v = '';
		a += '&rnd=' + Math.random();
		try {
			if (window.XMLHttpRequest) {
				c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c = new ActiveXObject('Microsoft.XMLHTTP');// code for IE6, IE5
			}
			c.open('GET', a, b);
			c.send(); 
			c.onreadystatechange = function() {
				if (c.readyState == 4 && c.status == 200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) that.callAjax(v,flag);
				}
			}
		} catch (e) {
			alert('wGet Request Failed - iLog');
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
		this.wGet(PassportUrl,true,0);
	};

	this.LogDetail = function(n) {
		var x = document.getElementById("expand" + n);
		if (hasClass(x, "collapse")) {//hidden detail
			document.getElementById("detail" + n).style.display = "none";
			removeClass(x, "collapse");
		} else {//show detail
			document.getElementById("detail" + n).style.display = "";
			addClass(x, "collapse");
		}
	};

	this.resize = function() {
		Resize();
	}

	//********** table class function end *********
	//********************************** Public Class Function End **********************************


	//************************************* Modular Table Begin *************************************

	// draw table
	function drawTable_V(x) {
		if (typeof(x[0]) === "undefined") {
			document.getElementById(cName).innerHTML = HTML_NODATA_TABLE;
			return;
		}
		
		var a = 1;
		var b = parseInt(document.getElementById(cName).offsetWidth);
		if (AllData == 0) {
			if (b >= 1200) {
				a = 1;
			} else if (b >= 800) {
				a = 2;
			} else if (b >= 600) {
				a = 3;
			} else {
				a = 4;
			}	
		} else {
			var w = b >= 1600 ? '100%' : '1600px'; 
		}
		

		switch (a) {
		default:
		case 1://max width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table style='width:" + (AllData ? w : '100%') + "'>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:15%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:25%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhlt'  style='width:5%'>" + Lan['Max Read'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Delay'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['DOM Ready'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Load'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Online'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['VID'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Visitor IP'] +"</td>"+
			"<td class='tdhmid pR' style='width:10%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_V(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 2://middle width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:15%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:30%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhlt'  style='width:10%'>"+ Lan['Max Read'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid' style='width:15%'>"+ Lan['Visitor IP'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_V(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 3://small width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:15%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:45%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:15%'>"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_V(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 4://min width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:25%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:40%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid' style='width:20%'>"+ Lan['Online'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_V(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		}
		
	}//end drawTable

	// draw table
	function drawTable_RB(x) {

		if (typeof(x[0]) === "undefined") {
			document.getElementById(cName).innerHTML = HTML_NODATA_TABLE;
			return;
		}
		
		var a = 1;
		var b = parseInt(document.getElementById(cName).offsetWidth);
		if (AllData == 0) {
			if (b >= 1200) {
				a = 1;
			} else if (b >= 800) {
				a = 2;
			} else if (b >= 600) {
				a = 3;
			} else {
				a = 4;
			}	
		} else {
			var w = b >= 1600 ? '100%' : '1600px'; 
		}


		switch (a) {
		default:
		case 1://max width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table style='width:" + (AllData ? w : '100%') + "'>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:15%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:25%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhlt'  style='width:5%'>" + Lan['Max Read'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Delay'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['DOM Ready'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Load'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Online'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Robot'] +"</td>"+
			"<td class='tdhmid' style='width:10%'>"+ Lan['Robot IP'] +"</td>"+
			"<td class='tdhmid pR' style='width:10%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_RB(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 2://middle width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:15%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:35%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid' style='width:5%'>" + Lan['Views'] +"</td>"+
			"<td class='tdhmid' style='width:15%'>"+ Lan['Robot'] +"</td>"+
			"<td class='tdhmid' style='width:15%'>"+ Lan['Robot IP'] +"</td>"+
			"<td class='tdhmid pR' style='width:15%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_RB(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 3://small width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:20%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:40%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid' style='width:20%'>"+ Lan['Robot'] +"</td>"+
			"<td class='tdhmid pR' style='width:20%'>"+ Lan['Location'] +"</td>"+
			"</tr>"+
			genLogHtml_RB(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 4://min width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:25%'>"+ Lan['Time'] +"</td>"+
			"<td class='tdhlt'  style='width:40%'>"+ Lan['Page Url'] +"</td>"+
			"<td class='tdhmid pR' style='width:35%'>"+ Lan['Robot'] +"</td>"+
			"</tr>"+
			genLogHtml_RB(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		}

	}//end drawTable_RB
		








	function genLogHtml_V(y, x) {//VISITOR LOG
		try {
			var cols;
			switch (y) {
			case 1:
				cols = 11;
				break;
			case 2:
				cols = 7;
				break;
			case 3:
				cols = 5;
				break;
			case 4:
				cols = 4;
				break;
			}
			
			if (typeof(x[0]) === "undefined") return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
			var i = x[0][2];
			
			var TB_TEXT="",LOCATION="",CITY="",STATUS="",PAGE="",FROM="",PG="",RF="",SE="",KW="";

			switch (y) {
			default:
			case 1://max width
				if(i) {
			
					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));

						if (x[row][0][0]['PageViews'] == 1) {
							RF = x[row][0][0]['Referrer'];
							PAGE = x[row][0][0]['Page'];
						} else {
							RF = x[row][1][0]['Referrer'];
							PAGE = x[row][1][0]['Page'];
						}

						switch (x[row][0][0]['FromType']) {
						case "4"://SE
							FROM = "<a class='lnk' target='_blank' href='" + RF + "'>" + x[row][0][0]['FromKey'] + "(" + x[row][0][0]['FromVal'] + ")</a>";
							break;
						case "5"://backlink
							FROM = "<a class='lnk' target='_blank' href='" + x[row][0][0]['FromVal'] + "'>" + x[row][0][0]['FromVal'] + "</a>";
							break;
						default:
							FROM = "Direct Entry"
							break;
						}

						var t = x[row][1].length,
						ave_delay = 0,
						ave_ready = 0,
						ave_load = 0,
						sum_online = 0,
						delay_times = 0,
						ready_times = 0,
						load_times = 0,
						tmp = 0;
						for (var a=0; a<t; a++) {
							tmp = parseInt(x[row][1][a]['DelaySecond']);
							if (tmp) delay_times++;
							ave_delay += tmp;
							tmp = parseInt(x[row][1][a]['ReadySecond']);
							if (tmp) ready_times++;
							ave_ready += tmp;
							tmp = parseInt(x[row][1][a]['LoadSecond']);
							if (tmp) load_times++;
							ave_load += tmp;
							sum_online += parseInt(x[row][1][a]['OnlineSecond']);
						}
						ave_delay = (delay_times ? Math.round(ave_delay / delay_times) : 0);
						ave_ready = (ready_times ? Math.round(ave_ready / ready_times) : 0);
						ave_load = (load_times ? Math.round(ave_load / load_times) : 0);
			
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"w, yyyy-MM-dd hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PAGE+"</a><br/>From: "+FROM+"</td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+x[row][0][0]['MaxReadY']+";"+x[row][0][0]['MinReadY']+"<br/>"+
				"X: "+x[row][0][0]['MaxReadX']+";"+x[row][0][0]['MinReadX']+
			"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(ave_delay,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(ave_ready,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(ave_load,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(sum_online,1)+"</td>"+
			"<td class='tdmidnoborder'><a class='lnk' href='javascript:" + box + ".LogDetail("+row+")'>"+t+"</a></td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['VID']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['IP']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+LOCATION+"</td></tr>";
			
			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
						
						//visitor track list begin
						for (var subrow=0; subrow<t; subrow++) {
							if (typeof(x[row][1][subrow]) === 'undefined') continue;
							LOCATION = (x[row][1][subrow]['Country'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['Region'].replace(/'/g,"&apos;") && x[row][1][subrow]['Region'].replace(/'/g,"&apos;") != x[row][1][subrow]['Country'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['Region'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['City'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['City'].replace(/'/g,"&apos;"));
							PAGE = x[row][1][subrow]['Page'];
							RF = x[row][1][subrow]['Referrer'];
							
							STATUS = '';
							//Entry Code: 1=>Refresh, 2=>Internal, 3=>Direct, 4=>SE, 5=>Backlink, 127=>Back/Forward,
							switch (parseInt(x[row][1][subrow]['EntryCode'])) {
							case 1://1 as refresh
								STATUS = 'Refresh';
								break;
							case 2://2 as internal 
								STATUS = 'Internal Entry';
								break;
							default:
							case 3://3 as direct, 
								STATUS = 'Direct Entry';
								break;
							case 4://4 as search engine, 
								STATUS = 'Search Engine';
								break;	
							case 5://5 as backlink, 
								STATUS = 'Backlink';
								break;
							case 127://127 as Back/Forward
								STATUS = 'Back/Forward';
								break;	
							}

							
							//Exit Code: 1=>Bounce, 2=>Exit, 3=>Close, 4=>Navigate
							switch (parseInt(x[row][1][subrow]['ExitCode'])) {
							case 1://1 as bounce page, 
								STATUS += '<br>Bounce';
								break;
							case 2://2 as exit page, 
								STATUS += '<br>Exit';
								break;
							case 3://3 as close,
								STATUS += '<br>Close';
								break;
							case 4://4 as navigate,
								STATUS += '<br>Navigate';
								break;
							}
							
						
			
			TB_TEXT +="<tr class='trd'>"+
			"<td class='tdltnoborder' style='width:15%'>"+(subrow+1)+".&nbsp;"+FormatDate(x[row][1][subrow]['RecordNo']/1000,"yyyy-MM-dd hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' style='width:20%' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PAGE+"</a></td>"+
			"<td class='tdltnoborder' style='width:5%'>"+
				"Y: "+x[row][1][subrow]['MaxReadY']+";"+x[row][1][subrow]['MinReadY']+"<br/>"+
				"X: "+x[row][1][subrow]['MaxReadX']+";"+x[row][1][subrow]['MinReadX']+
			"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+FormatTime(x[row][1][subrow]['DelaySecond'],3)+"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+FormatTime(x[row][1][subrow]['ReadySecond'],3)+"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+FormatTime(x[row][1][subrow]['LoadSecond'],3)+"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+FormatTime(x[row][1][subrow]['OnlineSecond'],1)+"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+x[row][1][subrow]['Visits'] + '-' + x[row][1][subrow]['PageViews']+"</td>"+
			"<td class='tdmidnoborder' style='width:10%'>"+STATUS+"</td>"+
			"<td class='tdmidnoborder' style='width:10%'>"+x[row][1][subrow]['IP']+"</td>"+
			"<td class='tdmidnoborder pR' style='width:10%' title='"+ LOCATION +"'>"+LOCATION+"</td></tr>";				
							
						}
						//visitor track list end
			
			
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], t) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
					}//end main for
					return TB_TEXT;
				}
				break;
			case 2://middle width		
				if(i) {
					
					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));
						
						if (x[row][0][0]['PageViews'] == 1) {
							RF = x[row][0][0]['Referrer'];
							PAGE = x[row][0][0]['Page'];
						} else {
							RF = x[row][1][0]['Referrer'];
							PAGE = x[row][1][0]['Page'];
						}

						switch (x[row][0][0]['FromType']) {
						case "4"://SE
							FROM = "<a class='lnk' target='_blank' href='" + RF + "'>" + x[row][0][0]['FromKey'] + "(" + x[row][0][0]['FromVal'] + ")</a>";
							break;
						case "5"://backlink
							FROM = "<a class='lnk' target='_blank' href='" + x[row][0][0]['FromVal'] + "'>" + x[row][0][0]['FromVal'] + "</a>";
							break;
						default:
							FROM = "Direct Entry"
							break;
						}

						var t = x[row][1].length,
						sum_online = 0;
						for (var a=0; a<t; a++) {
							sum_online += parseInt(x[row][1][a]['OnlineSecond']);
						}
						
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"MM-dd hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PAGE+"</a><br/>From: "+FROM+"</td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+x[row][0][0]['MaxReadY']+";"+x[row][0][0]['MinReadY']+"<br/>"+
				"X: "+x[row][0][0]['MaxReadX']+";"+x[row][0][0]['MinReadX']+
			"</td>"+
			"<td class='tdmidnoborder'><a class='lnk' href='javascript:" + box + ".LogDetail("+row+")'>"+t+"</a></td>"+
			"<td class='tdmidnoborder'>"+(x[row][0][0]['IsFakeData'] === "1" ? Lan['Yes'] : Lan['No'])+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(sum_online,1)+"</td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['IP']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+LOCATION+"</td></tr>";
			
			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			
						//visitor track list begin
						for (var subrow=0; subrow<t; subrow++) {
							if (typeof(x[row][1][subrow]) === 'undefined') continue;
							LOCATION = (x[row][1][subrow]['Country'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['Region'].replace(/'/g,"&apos;") && x[row][1][subrow]['Region'].replace(/'/g,"&apos;") != x[row][1][subrow]['Country'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['Region'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['City'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['City'].replace(/'/g,"&apos;"));
							PAGE = x[row][1][subrow]['Page'];
							RF = x[row][1][subrow]['Referrer'];
						
			
			TB_TEXT +="<tr class='trd'>"+
			"<td class='tdltnoborder' style='width:15%'>"+(subrow+1)+".&nbsp;"+FormatDate(x[row][1][subrow]['RecordNo']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' style='width:28%' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PAGE+"</a></td>"+
			"<td class='tdltnoborder' style='width:10%'>"+
				"Y: "+x[row][1][subrow]['MaxReadY']+";"+x[row][1][subrow]['MinReadY']+"<br/>"+
				"X: "+x[row][1][subrow]['MaxReadX']+";"+x[row][1][subrow]['MinReadX']+
			"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+x[row][1][subrow]['Visits'] + '-' + x[row][1][subrow]['PageViews']+"</td>"+
			"<td class='tdmidnoborder' style='width:5%'>"+(x[row][1][subrow]['IsFakeData'] === "1" ? Lan['Yes'] : Lan['No'])+"</td>"+
			"<td class='tdmidnoborder' style='width:7%'>"+FormatTime(x[row][1][subrow]['OnlineSecond'],1)+"</td>"+
			"<td class='tdmidnoborder' style='width:15%'>"+x[row][1][subrow]['IP']+"</td>"+
			"<td class='tdmidnoborder pR' style='width:15%' title='"+ LOCATION +"'>"+LOCATION+"</td></tr>";				
							
						}
						//visitor track list end
						
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], t) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
						}
					return TB_TEXT;
				}
				break;
			case 3://small width
				if(i) {
			
					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));
						CITY = x[row][0][0]['City'].replace(/'/g,"&apos;") || x[row][0][0]['Region'].replace(/'/g,"&apos;") || x[row][0][0]['Country'].replace(/'/g,"&apos;");
						
						if (x[row][0][0]['PageViews'] == 1) {
							RF = x[row][0][0]['Referrer'];
							PAGE = x[row][0][0]['Page'];
						} else {
							RF = x[row][1][0]['Referrer'];
							PAGE = x[row][1][0]['Page'];
						}
						//PG = PAGE.replace(/\w+:\/\/.*\//,'/');//remove domain
						PG = PAGE.replace(/\w+:\/\/[^\/]+/,'');//remove domain

						switch (x[row][0][0]['FromType']) {
						case "4"://SE
							FROM = "<a class='lnk' target='_blank' href='" + RF + "'>" + x[row][0][0]['FromKey'] + "</a>";//RF;
							break;
						case "5"://backlink
							FROM = "<a class='lnk' target='_blank' href='" + x[row][0][0]['FromVal'] + "'>" + x[row][0][0]['FromKey'] + "</a>";//RF;
							break;
						default:
							FROM = "Direct Entry"
							break;
						}

						var t = x[row][1].length,
						sum_online = 0;
						for (var a=0; a<t; a++) {
							sum_online += parseInt(x[row][1][a]['OnlineSecond']);
						}
						
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PG+"</a><br/>"+FROM+"</td>"+
			"<td class='tdmidnoborder'><a class='lnk' href='javascript:" + box + ".LogDetail("+row+")'>"+t+"</a></td>"+
			"<td class='tdmidnoborder'>"+FormatTime(sum_online,1)+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+CITY+"</td></tr>";
			
			//detail information begin		
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			
						//visitor track list begin
						for (var subrow=0; subrow<t; subrow++) {
							if (typeof(x[row][1][subrow]) === 'undefined') continue;
							LOCATION = (x[row][1][subrow]['Country'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['Region'].replace(/'/g,"&apos;") && x[row][1][subrow]['Region'].replace(/'/g,"&apos;") != x[row][1][subrow]['Country'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['Region'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['City'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['City'].replace(/'/g,"&apos;"));
							CITY = (x[row][1][subrow]['City'].replace(/'/g,"&apos;") || x[row][1][subrow]['Region'].replace(/'/g,"&apos;") || x[row][1][subrow]['Country'].replace(/'/g,"&apos;"));
							PAGE = x[row][1][subrow]['Page'];
							//PG = PAGE.replace(/\w+:\/\/.*\//,'/');//remove domain
							PG = PAGE.replace(/\w+:\/\/[^\/]+/,'');//remove domain
							RF = x[row][1][subrow]['Referrer'];
						
			
			TB_TEXT +="<tr class='trd'>"+
			"<td class='tdltnoborder' style='width:15%'>"+(subrow+1)+".&nbsp;"+FormatDate(x[row][1][subrow]['RecordNo']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' style='width:45%' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PG+"</a></td>"+
			"<td class='tdmidnoborder' style='width:10%'>"+x[row][1][subrow]['Visits'] + '-' + x[row][1][subrow]['PageViews']+"</td>"+
			"<td class='tdmidnoborder' style='width:15%'>"+FormatTime(x[row][1][subrow]['OnlineSecond'],1)+"</td>"+
			"<td class='tdmidnoborder pR' style='width:15%' title='"+ LOCATION +"'>"+CITY+"</td></tr>";				
							
						}
						//visitor track list end
						
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], t) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
						}
					return TB_TEXT;
				}
				break;	
			case 4://min width		
				if(i) {
			
					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));
						CITY = x[row][0][0]['CountryISO'] || x[row][0][0]['City'].replace(/'/g,"&apos;") || x[row][0][0]['Region'].replace(/'/g,"&apos;") || x[row][0][0]['Country'].replace(/'/g,"&apos;");
						
						if (x[row][0][0]['PageViews'] == 1) {
							RF = x[row][0][0]['Referrer'];
							PAGE = x[row][0][0]['Page'];
						} else {
							RF = x[row][1][0]['Referrer'];
							PAGE = x[row][1][0]['Page'];
						}
						//PG = PAGE.replace(/\w+:\/\/.*\//,'/');//remove domain
						PG = PAGE.replace(/\w+:\/\/[^\/]+/,'');//remove domain

						switch (x[row][0][0]['FromType']) {
						case "4"://SE
							FROM = "<a class='lnk' target='_blank' href='" + RF + "'>" + x[row][0][0]['FromKey'] + "</a>";//RF;
							break;
						case "5"://backlink
							FROM = "<a class='lnk' target='_blank' href='" + x[row][0][0]['FromVal'] + "'>" + x[row][0][0]['FromKey'] + "</a>";//RF;
							break;
						default:
							FROM = "Direct Entry"
							break;
						}

						var t = x[row][1].length,
						sum_online = 0;
						for (var a=0; a<t; a++) {
							sum_online += parseInt(x[row][1][a]['OnlineSecond']);
						}
						
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"hh:mm")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PG+"</a><br/>"+FROM+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(sum_online,1)+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+CITY+"</td></tr>";
			
			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			
						//visitor track list begin
						for (var subrow=0; subrow<t; subrow++) {
							if (typeof(x[row][1][subrow]) === 'undefined') continue;
							LOCATION = (x[row][1][subrow]['Country'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['Region'].replace(/'/g,"&apos;") && x[row][1][subrow]['Region'].replace(/'/g,"&apos;") != x[row][1][subrow]['Country'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['Region'].replace(/'/g,"&apos;"));
							if (x[row][1][subrow]['City'].replace(/'/g,"&apos;")) LOCATION += "-" + (x[row][1][subrow]['City'].replace(/'/g,"&apos;"));
							CITY = x[row][1][subrow]['CountryISO'];
							PAGE = x[row][1][subrow]['Page'];
							//PG = PAGE.replace(/\w+:\/\/.*\//,'/');//remove domain
							PG = PAGE.replace(/\w+:\/\/[^\/]+/,'');//remove domain
							RF = x[row][1][subrow]['Referrer'];
			
			TB_TEXT +="<tr class='trd'>"+
			"<td class='tdltnoborder' style='width:25%'>"+(subrow+1)+".&nbsp;"+FormatDate(x[row][1][subrow]['RecordNo']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' style='width:40%' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PG+"</a></td>"+
			"<td class='tdmidnoborder' style='width:20%'>"+FormatTime(x[row][1][subrow]['OnlineSecond'],1)+"</td>"+
			"<td class='tdmidnoborder pR' style='width:15%' title='"+ LOCATION +"'>"+CITY+"</td></tr>";				
							
						}
						//visitor track list end
						
						
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], t) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
						}
					return TB_TEXT;
				}
				break;
			}
		} catch(z) {
			console.log('genLogHtml_V exception error!');
			console.log(z);
			return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
		}		
	}











	function genLogHtml_RB(y,x){//ROBOT LOG
		try {
			var cols;
			switch (y) {
			case 1:
				cols = 11;
				break;
			case 2:
				cols = 6;
				break;
			case 3:
				cols = 4;
				break;
			case 4:
				cols = 3;
				break;
			}
			
			if (typeof(x[0]) === "undefined") return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
			var i = x[0][2];

			var TB_TEXT="",LOCATION="",CITY="",STATUS="",PAGE="",FROM="",PG="",RF="",SE="",KW="";

			switch (y) {
			default:
			case 1://max width		
				if(i) {
					
					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));
						PAGE = x[row][0][0]['Page'];
						RF = x[row][1][0]['Referrer'];
						
						var t = x[row][1].length,
						ave_delay = 0,
						ave_ready = 0,
						ave_load = 0,
						sum_online = 0,
						delay_times = 0,
						ready_times = 0,
						load_times = 0,
						tmp = 0;
						for (var a=0; a<t; a++) {
							tmp = parseInt(x[row][1][a]['DelaySecond']);
							if (tmp) delay_times++;
							ave_delay += tmp;
							tmp = parseInt(x[row][1][a]['ReadySecond']);
							if (tmp) ready_times++;
							ave_ready += tmp;
							tmp = parseInt(x[row][1][a]['LoadSecond']);
							if (tmp) load_times++;
							ave_load += tmp;
							sum_online += parseInt(x[row][1][a]['OnlineSecond']);
						}
						ave_delay = (delay_times ? Math.round(ave_delay / delay_times) : 0);
						ave_ready = (ready_times ? Math.round(ave_ready / ready_times) : 0);
						ave_load = (load_times ? Math.round(ave_load / load_times) : 0);
			
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"w, yyyy-MM-dd hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PAGE+"</a></td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+x[row][0][0]['MaxReadY']+";"+x[row][0][0]['MinReadY']+"<br/>"+
				"X: "+x[row][0][0]['MaxReadX']+";"+x[row][0][0]['MinReadX']+
			"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(ave_delay,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(ave_ready,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(ave_load,3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(sum_online,1)+"</td>"+
			"<td class='tdmidnoborder'><a class='lnk' href='javascript:" + box + ".LogDetail("+row+")'>"+t+"</a></td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['Spider']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['IP']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+LOCATION+"</td></tr>";
			
			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], x[row][0][0]['PageViews']) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end

					}
					return TB_TEXT;
				}
				break;
			case 2://middle width		
				if(i) {

					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));
						PAGE = x[row][0][0]['Page'];
						RF = x[row][1][0]['Referrer'];

						var t = x[row][1].length;
						
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"MM-dd hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PAGE+"</a></td>"+
			"<td class='tdmidnoborder'><a class='lnk' href='javascript:" + box + ".LogDetail("+row+")'>"+t+"</a></td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['Spider']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['IP']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+LOCATION+"</td></tr>";
			
			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], x[row][0][0]['PageViews']) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
						}
					return TB_TEXT;
				}
				break;
			case 3://small width
				if(i) {

					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['Country'].replace(/'/g,"&apos;");
						if (x[row][0][0]['Region']) LOCATION += "-" + (x[row][0][0]['Region'].replace(/'/g,"&apos;"));
						if (x[row][0][0]['City']) LOCATION += "-" + (x[row][0][0]['City'].replace(/'/g,"&apos;"));
						CITY = x[row][0][0]['City'].replace(/'/g,"&apos;") || x[row][0][0]['Region'].replace(/'/g,"&apos;") || x[row][0][0]['Country'].replace(/'/g,"&apos;");
						PAGE = x[row][0][0]['Page'];
						//PG = PAGE.replace(/\w+:\/\/.*\//,'/');//remove domain
						PG = PAGE.replace(/\w+:\/\/[^\/]+/,'');//remove domain
						RF = x[row][1][0]['Referrer'];

						var t = x[row][1].length;
						
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"hh:mm:ss")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PG+"</a></td>"+
			"<td class='tdmidnoborder'>"+x[row][0][0]['Spider']+"</td>"+
			"<td class='tdmidnoborder pR' title='"+ LOCATION +"'>"+CITY+"</td></tr>";

			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], x[row][0][0]['PageViews']) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
						}
					return TB_TEXT;
				}
				break;	
			case 4://min width		
				if(i) {

					for (var row = 1; row <= i ; row++) {
						if (typeof(x[row]) === "undefined") continue;
						LOCATION = x[row][0][0]['City'].replace(/'/g,"&apos;") || x[row][0][0]['Region'].replace(/'/g,"&apos;") || x[row][0][0]['Country'].replace(/'/g,"&apos;");
						PAGE = x[row][0][0]['Page'];
						//PG = PAGE.replace(/\w+:\/\/.*\//,'/');//remove domain
						PG = PAGE.replace(/\w+:\/\/[^\/]+/,'');//remove domain
						RF = x[row][1][0]['Referrer'];

						var t = x[row][1].length;
						
			TB_TEXT +="<tr class='trb'>"+
			"<td class='tdltnoborder'><div class='expand' id='expand"+row+"' onclick='" + box + ".LogDetail("+row+")'></div>"+FormatDate(x[row][0][0]['RecordNo']/1000,"hh:mm")+"</td>"+
			"<td class='tdltnoborder' title='Entry Page: "+PAGE+"\nReferrer: "+RF+"'><a class='lnk' target='_blank' href='"+PAGE+"'>"+PG+"</a></td>"+
			"<td class='tdmidnoborder pR'>"+x[row][0][0]['Spider']+"</td></tr>";

			//detail information begin
			TB_TEXT +="<tr class='trc' id='detail"+row+"' style='display:none'><td colspan='" + cols + "' style='padding:0px'><table class='subtb'>";
			TB_TEXT +="<tr class='tre'>"+
			"<td class='subtd' colspan='" + cols + "'>"+ detailHtml(x[row][0][0], x[row][0][0]['PageViews']) +"</td>"+
			"</tr>";
			
			TB_TEXT +="</td></table></tr>";
			//detail information end
						
						}
					return TB_TEXT;
				}
				break;
			}
		} catch(z) {
			console.log('genLogHtml_RB exception error!');
			console.log(z);
			return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
		}
	}


	function detailHtml(x, t) {
		try {
			var htmlText = 
				"<div class='tdfootdiv'>Device: " + x['Device'] + "<br/>Model: " + x['Brand'] + (x['Model'] === "" ? "Unknown" : " - " + x['Model']) + "<br/>Resolution: " + x['ScreenWidth'] + "x" + x['ScreenHeight'] + " " + (x['TouchScreen'] === "1" ? "Touchscreen" : "") + "<br/>Color Depth: " + x['ColorDepth'] + "<br/>CPU: " + (x['CPU'] ? x['CPU'] : "Unknown")+ "</div>"+
				"<div class='tdfootdiv'>OS: " + x['OS'] + " " + x['OSCodename'] + "<br/>Bowser: " + x['BrowserName'] + " " + (x['BrowserName']=="Other" ? "" : x['BrowserVersion']) + "<br/>Bowser Engine: " + x['BrowserCore'] + " " + (x['BrowserCore']=="Other" ? "" : x['BrowserCoreVersion']) + "<br/>Language: " + x['Language'] + "<br/>Cookie Enabled: " + (x['CookieEnabled']? "Yes" : "No") + "</div>"+
				"<div class='tdfootdiv'>Browser Width: " + x['ClientWidth'] + "<br/>Browser Height: " + x['ClientHeight'] + "<br/>Page Width: " + x['ScrollWidth'] + "<br/>Page Height: " + x['ScrollHeight'] + "</div>"+
				"<div class='tdfootdiv'>First Visit Time: " + FormatDate((x['VID'] < 15E14 ? x['LastVisitTime'] : x['VID'])/1000,"yyyy-MM-dd hh:mm:ss") + "<br/>Last Visit Time: " + FormatDate(x['LastVisitTime']/1000,"yyyy-MM-dd hh:mm:ss") + "<br/>Total Views: " + x['TotalPageViews'] + " ; Today Views: " + t + "<br/>Total Visits: " + x['TotalVisits'] + " ; Today Visits: " + x['Visits'] + "</div>"+
				"<div class='tdfootdiv'>First Request: " + VisitStatusA[x['StatusCode']] + "<br/>Last Request: " + VisitStatusA[x['LastStatus']] + "<br/>Total Requests: " + x['Step'] + "<br/>Page Action: " + x['PageAction'] + "</div>"+
				"<div class='tdfootdiv2'>UserAgent: " + x['UserAgent'] + "<br/>Platform: " + x['Platform'] + "<br/>Plugin: " + x['Plugin'] + "</div>";
			return htmlText;
		} catch(z) {
			console.log(z);
			return '';
		}
	}


	function pagesHtml(x) {
		
		if (x[0] == undefined) return '';
		//
		var pages = Math.ceil(x[0][0] / that.end),
		cPage = (x[0][1] / that.end) + 1,
		start = 1,//start page
		end = pages,//end page
		limit = 5,//limited page
		textHtml = "<div class='tbfootbar'>";
		
		if (pages > limit) {
			start = (cPage - Math.floor(limit / 2));
			if (start < 1) start = 1;
			end = start + limit - 1;
			if (end > pages) {
				end = pages;
				start = pages - limit + 1;
			}
		}
		
		if (pages > limit) {
			textHtml += "<a class='pagebut' onclick='" + box + ".GoPage(1)' title='First Page'>&laquo;</a>";
			textHtml += "<a class='pagebut' onclick='" + box + ".PreviousPage()' title='Previous Page'>&lsaquo;</a>";
		}
		
		for (var i=start; i<=end; i++) {
			if (i == cPage) {
				textHtml += "<span class='page_focus'>" + i + "</span>";
			} else {
				textHtml += "<a class='page' onclick='" + box + ".GoPage(" + i + ")'>" + i + "</a>";
			}
		}
		
		if (pages > limit) {
			textHtml += "<a class='pagebut' onclick='" + box + ".NextPage()' title='Next Page'>&rsaquo;</a>";
			textHtml += "<a class='pagebut' onclick='" + box + ".GoPage(" + pages + ")' title='Last Page'>&raquo;</a>";
		}

		textHtml += "<ul><li><a class='select' onclick='selectButton(this)'>" + that.end + "</a><ul id='" + box + "rows'>";
		switch (parseInt(that.end)) {
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

	function Resize() {
		switch (cType) {
		case 0://visitor log
		case 1://returning visitor log
			drawTable_V(LogData);
			break;
		case 2://robot log
			drawTable_RB(LogData);
			break;
		}
	}

	function UTF82Native(code) {
		try {
			var a = decodeURIComponent(code);
			return unescape(a.replace(/&#x/g, "%u").replace(/;/g, ""));
		} catch(z) {
			return code;
		}
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

	function sC(a, b, c) {//set Cookie,a: cookie name, b: cookie value, c: cookie expriod
			var d = new Date();
			d.setDate(d.getDate() + c);
			document.cookie = a + "=" + escape(b) + ((c == null) ? "":";expires=" + d.toUTCString());
	}

	function FormatDate(ptime, fmt) {
		
		var weekday=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

		var pdate = new Date();
		var offsetLocal = pdate.getTimezoneOffset() * 60000;//本地时间与GMT时间的时间偏移差
		pdate.setTime(ptime + offsetLocal + 3600000 * Extra);//设置时区与GMT的时间偏移
		var o = {
			"M+": pdate.getMonth() + 1, //月份 
			"d+": pdate.getDate(), //日 
			"h+": pdate.getHours(), //小时 
			"m+": pdate.getMinutes(), //分 
			"s+": pdate.getSeconds(), //秒 
			"q+": Math.floor((pdate.getMonth() + 3) / 3), //季度 
			"S": pdate.getMilliseconds(), //毫秒 
			"w+": weekday[pdate.getDay()] //day of week
			
		};
		if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (pdate.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o) if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;

	}

	function FormatTime(ptime,op) {
		var fmt = "";
		switch (op) {
		case 0:
			if (ptime >= 3600000) {
				fmt = "hh:mm:ss";
			} else if (ptime >= 60000) {
				fmt = "mm:ss";
			} else {
				fmt = "s.S";
			}
			break;
		case 1:	//hours
			if (ptime >= 3600000) {
				fmt = "HH:mm:ss";
			} else {
				fmt = "mm:ss";
			}
			break;
		case 2:	//minutes
			fmt = "M:ss";
			break;
		case 3: //seconds
			fmt = "S";
			break;
		}
		
		var pdate = new Date();
		pdate.setTime(ptime);
		var o = {
			"h+": pdate.getHours(), //24小时格式，无前导零
			"H+": Math.floor(ptime/3600000), //转化为总小时数
			"m+": pdate.getMinutes(), //60分钟格式，无前导零
			"M+": Math.floor(ptime/60000), //转化为总分钟数
			"s+": pdate.getSeconds(), //60秒格式，无前导零
			"C+": Math.floor(ptime/1000), //转化为总秒数
			"S": parseInt(ptime)/1000 //1000毫秒格式，无前导零
		};
		if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (pdate.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o) if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	}

	function hasClass(obj, cls) {  
		return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));  
	}  
	  
	function addClass(obj, cls) {  
		if (!this.hasClass(obj, cls)) obj.className += " " + cls;  
	}  
	  
	function removeClass(obj, cls) {  
		if (hasClass(obj, cls)) {
			var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');  
			obj.className = obj.className.replace(reg, ' ');  
		}  
	}

	function getTitle(type) {
		switch (type) {
		case 0:
			return 'Visitor';
		case 1:
			return 'Returning Visitor';
		case 2:
			return 'Robot';
		}
		return '';
	}


}//end API function

