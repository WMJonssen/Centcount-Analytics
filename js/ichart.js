/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free ICHART API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function CHARTAPI(sid, r, from, to, period, type, timezone, key, timer, cType, host, box, sortorder, W, H, LanCode, Lan, Extra) {
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
	
	if (!W) W = 320;
	if (!H) H = 200;
	if (W < 320) W = 320;
	H = (H > (W - 120)) ? W - 120 : H; 
	var CW = (H + 120) > W ? W - 120 : H;
	var PIE_MAX = parseInt(CW / 20);
	if (PIE_MAX > 10) PIE_MAX = 10;
	var halfCW = parseInt(CW / 2);
	
	var AjaxData = [],
		ChartData = [],
		iChart,
		helpers = Chart.helpers,
		hTimer = 0,
		title = getTitle(type),
		cName = title.replace(/\s+/g,''),
		Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
		APIUrl = Protocol + host + '/api/api_ca.php?',
		PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
		HTML_LOADING = "<table><tr class='tra'><td class='tdmid'><img class='mid' src='images/loading.gif'/>&nbsp;Processing...</td></tr></table>",
		HTML_NODATA_TABLE = "<div style='width:auto; float:none; font-size:36px; color:#ddd; text-align:center; line-height:198px; border-top:#ccc 1px solid; border-bottom:#ccc 1px solid;'>No Data</div>",
		HTML_NODATA_PIE = "<div style='width:auto; float:none; font-size:36px; color:#ddd; text-align:center; line-height:128px; padding-top:35px;'>No Data</div>",
		HTML_NODATA_BAR = "<div style='width:auto; float:none; font-size:36px; color:#ddd; text-align:center; line-height:198px;'>No Data</div>",
		that = this;
	

	timer = timer < 1 ? 0 : timer < 6E4 ? 6E4 : timer;

	
	//add tools
	var obj;
	if (obj = document.getElementById(box + '_R')) obj.href = 'javascript:' + box + '.run(9)';
	switch (cType) {
	case 4://heatmap
	case 8://overview
		if (obj = document.getElementById(box + '_TB'))  obj.href = 'javascript:' + box + '.run('+cType+', 0)'; 
		if (obj = document.getElementById(box + '_ALL')) obj.href = 'javascript:' + box + '.run('+cType+', 1)'; 
		break;
	default://common
		if (obj = document.getElementById(box + '_TB'))  obj.href = 'javascript:' + box + '.run(0, 0)'; 
		if (obj = document.getElementById(box + '_ALL')) obj.href = 'javascript:' + box + '.run(0, 1)'; 
		break;
	}
	if (obj = document.getElementById(box + '_PIE')) obj.href = 'javascript:' + box + '.run(1)'; 
	if (obj = document.getElementById(box + '_BAR')) obj.href = 'javascript:' + box + '.run(2)'; 
	//add resize events
	//window.addEventListener ? window.addEventListener('resize', Resize) : window.attachEvent('onresize', Resize);

	//********************************** Public Class Function Begin **********************************	
	this.run = function(a, b) {//switch cType
		try {
			if (a !== 9) AllData = b ? b : 0;
			(a === 9) ? a = cType : cType = a;

			switch (a) {
			case 0://table for common
			case 4://table for heatmap
			case 8://table for overview
				this.end = gC("TB_" + cName + "Rows") || 10;//mysql limit number, default is 10
				document.getElementById(box).innerHTML = "<div id='" + cName + "' class='ca_table'></div>";
				break;
			case 1://pie
				this.end = PIE_MAX;
				document.getElementById(box).innerHTML =
				"<div id='" + cName + "' class='canvasbox'>"+
					"<div id='" + cName + "Title' class='pie_title'></div>"+
					"<div style='width:" + CW + "px; height:" + CW + "px; float:left; margin:0px; margin-right:30px; margin-left:-moz-calc(50% - " + halfCW + "px); margin-left:-webkit-calc(50% - " + halfCW + "px); margin-left: calc(50% - " + halfCW + "px);' >"+
						"<canvas id='CAPLUGIN-" + cName + "' style='width:" + CW + "px; height:" + CW + "px;'></canvas>"+
					"</div>"+
					"<div id='CAPLUGINlegendHolder-" + cName +"' class='pie-legend_holder'></div>"+
				"</div>";
				break;
			case 2://bar
				this.end = gC("TB_" + cName + "Rows") || 10;
				if (this.end > 20) this.end = 20;
				if (H < 320) H = 320;
				document.getElementById(box).innerHTML = 
				"<div id='" + cName + "' class='ca_bar'>"+
					"<div id='" + cName + "Title' class='bar_title'></div>"+
					"<div style='height:" + H + "px; width:auto; margin:10px; margin-left:5px; margin-bottom: 5px;'>"+
						"<canvas id='CAPLUGIN-" + cName +"' style='height:" + H + "px; width:100%;'></canvas>"+
					"</div>"+
				"</div>";
				break;
			}
			//run
			this.wGet(PassportUrl, true, 0); 
			if (timer > 0 && hTimer === 0) hTimer = setInterval(Update, timer);
		} catch(z) {
			alert('switch chart error!' + z.name + ': ' + z.message);
		}
	};

	this.callAjax = function(pass, flag) {//flag = 0 is ini, flag = 1 is update
		try {
			if (cType !== 1 && cType !== 2) document.getElementById(cName).innerHTML = HTML_LOADING;
		
			switch (cType) {
			case 0:// entry page, exit page, bounce page, all page, overview
				this.q = 'table';
				break;
			case 1:
				this.q = 'pie';
				break;
			case 2:
				this.q = 'bar';
				break;
			case 4://heatmap
				this.q = 'clicks';
				break;
			case 8://overview
				this.q = 'overview';
				break;
			}
			
			var d = [];
			for (var k in this) {
				d.push(k + "=" + this[k]);
				if (k == 'sortorder') break;
			}
			var url = APIUrl + pass + d.join('&'), 
				myAjax;
			
		
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					AjaxData = [];
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '')
					if (v !== '') {
						AjaxData = eval(v);
					} else {
						switch (cType) {
						case 0://entry page, exit page, bounce page, entry page, all page
						case 4://heatmap
						case 8://overview
							document.getElementById(cName).innerHTML = HTML_NODATA_TABLE;
							break;
						case 1:
							document.getElementById(cName).innerHTML = HTML_NODATA_PIE;
							break;
						case 2:
							document.getElementById(cName).innerHTML = HTML_NODATA_BAR;
							break;
						}
						return;
					}
					if (flag) {//update
						switch (cType) {
						case 0:
						case 8://overview
							drawTable_AP(AjaxData);
							break;
						case 1:
							updatePie(AjaxData);
							break;
						case 2:
							updateBar(AjaxData);
							break;
						case 4://heatmap
							drawTable_HM(AjaxData);
							break;
						}
					} else {
						switch (cType) {
						case 0:
						case 8://overview
							drawTable_AP(AjaxData);
							break;
						case 1:
							drawPie(AjaxData);
							break;
						case 2:
							drawBar(AjaxData);
							break;
						case 4://heatmap
							drawTable_HM(AjaxData);
							break;
						}
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {
			alert('XMLHttpRequest Error - ChartAPI - Tpye: ' + cType + ' Error: ' + e.name + ' - ' + e.message);
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
			alert('wGet Request Failed - iPlugin');
		}
	};


	//********** table class function begin *********
	this.SetSort = function (ikey, isortorder) {
		this.key = ikey;
		key = ikey;
		this.sortorder = isortorder;//0 as DESC 降序, 1 as ASC 升序
		this.wGet(PassportUrl, true, 0); 
	};

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
		if ((this.start + this.end * 5) < AjaxData[0][0]) {
			this.start += this.end * 5;
		} else {
			var pages = Math.ceil(AjaxData[0][0] / this.end);
			this.GoPage(pages);
			return;
		}
		this.wGet(PassportUrl,true,0);
	};

	this.resize = function() {
		Resize();
	};
	//********** table class function end *********
	//********************************** Public Class Function End **********************************


	//************************************* Modular Table Begin *************************************
	function genSortHtml(name,key,sortorder) {
		try {
			if (key == that.key) {//if key is current key then check sortorder, if sortorder is asc then set sortorder = desc
				if (sortorder == 0) {
					return "<a class='sort-default' onclick='" + box + ".SetSort(" + key + ",1)'>" + Lan[name] + "</a><a class='sort-asc' onclick='" + box + ".SetSort(" + key + ",1)'></a>";
				} else {
					return "<a class='sort-default' onclick='" + box + ".SetSort(" + key + ",0)'>" + Lan[name] + "</a><a class='sort-desc' onclick='" + box + ".SetSort(" + key + ",0)'></a>";
				}
			} else {
				return "<a class='sort-default' onclick='" + box + ".SetSort(" + key + ",0)'>" + Lan[name] + "</a>";
			}
		} catch(z) {
			return "";
		}
	}


	function drawTable_AP(x) {//x as Data
		
		if (typeof x[0] === "undefined") {
			document.getElementById(cName).innerHTML = HTML_NODATA_TABLE;
			return;
		}

		var a = 1;
		var b = parseInt(document.getElementById(cName).offsetWidth);
		if (AllData == 0) {
			if (b >= 1800) {
				a = 1;
			} else if (b >= 1200) {
				a = 2;
			} else if (b >= 600) {
				a = 3;
			} else if (b >= 480) {
				a = 4;
			} else {
				a = 5;
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
			"<td class='tdhlt'  style='width:17%' title='Sort by " + title + "'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Unique Page Views'>" + genSortHtml('UPV', 4, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Unique Visitors'>" + genSortHtml('UV', 3, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by New Visitors'>" + genSortHtml('NV', 21, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Returning Visitors'>" + genSortHtml('RV', 22, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Returning Visits'>" + genSortHtml('RVS', 23, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Valid Clicks'>" + genSortHtml('Valid Clicks', 14, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Direct Entries'>" + genSortHtml('Direct Entries', 19, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by SE Entries'>" + genSortHtml('SE Entries', 17, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Backlink Entries'>" + genSortHtml('Backlink Entries', 18, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Bounces'>" + genSortHtml('Bounces', 16, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Exits'>" + genSortHtml('Exits', 13, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Exit Rate'>" + genSortHtml('Exit Rate', 6, that.sortorder) + "</td>"+
			"<td class='tdhlt'  style='width:5%' title='Sort by Avg Max Read'>" + genSortHtml('Avg Max Read', 8, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg Delay'>" + genSortHtml('Avg Delay', 20, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg DOM Ready'>" + genSortHtml('Avg DOM Ready', 9, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg Load'>" + genSortHtml('Avg Load', 10, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:5%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_AP(a,x)+
			"</table></div>"+
			(cType !== 8 ? "<div class='pagination'><td>"+ pagesHtml(x) +"</td></div>" : "");
			break;
		case 2://max width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:20%' title='Sort by " + title + "'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Unique Page Views'>" + genSortHtml('UPV', 4, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Unique Visitors'>" + genSortHtml('UV', 3, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by New Visitors'>" + genSortHtml('NV', 21, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Returning Visitors'>" + genSortHtml('RV', 22, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Bounces'>" + genSortHtml('Bounces', 16, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Exits'>" + genSortHtml('Exits', 13, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Exit Rate'>" + genSortHtml('Exit Rate', 6, that.sortorder) + "</td>"+
			"<td class='tdhlt'  style='width:5%' title='Sort by Avg Max Read'>" + genSortHtml('Avg Max Read', 8, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg Delay'>" + genSortHtml('Avg Delay', 20, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg DOM Ready'>" + genSortHtml('Avg DOM Ready', 9, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg Load'>" + genSortHtml('Avg Load', 10, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:5%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_AP(a,x)+
			"</table></div>"+
			(cType !== 8 ? "<div class='pagination'><td>"+ pagesHtml(x) +"</td></div>" : "");
			break;
		case 3://middle width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:30%' title='Sort by " + title + "'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:9%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:9%' title='Sort by Unique Page Views'>" + genSortHtml('UPV', 3, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:9%' title='Sort by Unique Visitors'>" + genSortHtml('UV', 4, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:9%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:12%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhlt'  style='width:12%' title='Sort by Avg Max Read'>" + genSortHtml('Avg Max Read', 8, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:10%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_AP(a,x)+
			"</table></div>"+
			(cType !== 8 ? "<div class='pagination'><td>"+ pagesHtml(x) +"</td></div>" : "");
			break;
		case 4://small width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:30%' title='Sort by " + title + "'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:15%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:15%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:20%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:20%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_AP(a,x)+
			"</table></div>"+
			(cType !== 8 ? "<div class='pagination'><td>"+ pagesHtml(x) +"</td></div>" : "");
			break;
		case 5://min width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:40%' title='Sort by " + title + "'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:20%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:20%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:20%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_AP(a,x)+
			"</table></div>"+
			(cType !== 8 ? "<div class='pagination'><td>"+ pagesHtml(x) +"</td></div>" : "");
			break;
		}

	}


	function drawTable_HM(x) {//heatmap
		if (typeof x[0] === "undefined") {
			document.getElementById(cName).innerHTML = HTML_NODATA_TABLE;
			return;
		}

		var a = 1;
		var b = parseInt(document.getElementById(cName).offsetWidth);
		if (AllData == 0) {
			if (b >= 1600) {
				a = 1;
			} else if (b >= 800) {
				a = 2;
			} else if (b >= 600) {
				a = 3;
			} else if (b >= 480) {
				a = 4;
			} else {
				a = 5;
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
			"<td class='tdhlt'  style='width:13%' title='Sort by " + title +"'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:4%' title='View'>" + Lan['View'] + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Valid Clicks'>" + genSortHtml('Valid Clicks', 14, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Unique Page Views'>" + genSortHtml('UPV', 4, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Unique Visitors'>" + genSortHtml('UV', 3, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by New Visitors'>" + genSortHtml('NV', 21, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Returning Visitors'>" + genSortHtml('RV', 22, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:3%' title='Sort by Returning Visits'>" + genSortHtml('RVS', 23, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Direct Entries'>" + genSortHtml('Direct Entries', 19, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by SE Entries'>" + genSortHtml('SE Entries', 17, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Backlink Entries'>" + genSortHtml('Backlink Entries', 18, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Bounces'>" + genSortHtml('Bounces', 16, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Exits'>" + genSortHtml('Exits', 13, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:4%' title='Sort by Exit Rate'>" + genSortHtml('Exit Rate', 6, that.sortorder) + "</td>"+
			"<td class='tdhlt'  style='width:5%' title='Sort by Avg Max Read'>" + genSortHtml('Avg Max Read', 8, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg Delay'>" + genSortHtml('Avg Delay', 20, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg DOM Ready'>" + genSortHtml('Avg DOM Ready', 9, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:5%' title='Sort by Avg Load'>" + genSortHtml('Avg Load', 10, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:5%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_HM(a,x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 2://middle width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:30%' title='Sort by " + title +"'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:7%' title='View'>" + Lan['View'] + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Valid Clicks'>" + genSortHtml('Valid Clicks', 14, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Unique Visitors'>" + genSortHtml('UV', 3, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:7%' title='Sort by Exit Rate'>" + genSortHtml('Exit Rate', 6, that.sortorder) + "</td>"+
			"<td class='tdhlt'  style='width:7%' title='Sort by Avg Max Read'>" + genSortHtml('Avg Max Read', 8, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:7%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_HM(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 3://small width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:30%' title='Sort by " + title +"'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:10%' title='View'>" + Lan['View'] + "</td>"+
			"<td class='tdhmid' style='width:9%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:9%' title='Sort by Valid Clicks'>" + genSortHtml('Valid Clicks', 14, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:10%' title='Sort by Sessions'>" + genSortHtml('Sessions', 1, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:10%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:12%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:10%' title='Sort by Avg Online Per Page View'>" + genSortHtml('Avg Online', 11, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_HM(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 4://tiny width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:30%' title='Sort by " + title +"'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:20%' title='View'>" + Lan['View'] + "</td>"+
			"<td class='tdhmid' style='width:15%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid' style='width:15%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:20%' title='Sort by Bounce Rate'>" + genSortHtml('Bounce Rate', 5, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_HM(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		case 5://min width
			document.getElementById(cName).innerHTML = 
			"<div class='tbdiv'><table>"+
			"<tr class='trhead'>"+
			"<td class='tdhlt'  style='width:40%' title='Sort by " + title +"'>" + Lan[title] + "</td>"+
			"<td class='tdhmid' style='width:20%' title='View'>" + Lan['View'] + "</td>"+
			"<td class='tdhmid' style='width:20%' title='Sort by Clicks'>" + genSortHtml('Clicks', 15, that.sortorder) + "</td>"+
			"<td class='tdhmid pR' style='width:20%' title='Sort by Page Views'>" + genSortHtml('PV', 2, that.sortorder) + "</td>"+
			"</tr>"+
			genTableHtml_HM(a, x)+
			"</table></div>"+
			(typeof x[0] === 'undefined' ? "" : "<div class='pagination'><td>" + pagesHtml(x) + "</td></div>");
			break;
		}
	}

	


	function genTableHtml_AP(y, x) {//action All Pages
		try {
			var cols;
			switch (y) {
			case 1:
				cols = 22;
				break;
			case 2:
				cols = 17;
				break;
			case 3:
				cols = 8;
				break;
			case 4:
				cols = 5;
				break;
			case 5:
				cols = 4;
				break;
			}
			
			var i = x[0][2];
			switch (y) {
			default:
			case 1://max width
				if (i) {
					i++;
					var tbtext = '', tmp;
					for (var row = 1; row <= i ; row++) {
						if (typeof x[row] == 'undefined') continue;

						tbtext +="<tr class='trb'>";
						tmp = UTF82Native(x[row]['Detail']);
						if ({'19':1,'20':1}[type] || 0) tmp = GetLanCode(tmp);

						tbtext +=
				"<td class='tdltnoborder' title='"+ tmp +"'>"+ tmp +"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['PV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['UPV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['UV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['NV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['RV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['RVS'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Clicks'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['ValidClicks'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Visits'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['DREntry'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['SEEntry'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['RFEntry'])+"</td>"+
				"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? parseInt(x[row]['Bounces']) : 'Null')+"</td>"+
				"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Exits'])+"</td>"+
				"<td class='tdmidnoborder'>"+(x[row]['ExitRate'] * 0.01).toFixed(2)+"%</td>"+
				"<td class='tdltnoborder'>"+
					"Y: "+parseInt(x[row]['AvgMRY'])+"%<br/>"+
					"X: "+parseInt(x[row]['AvgMRX'])+"%"+ 
				"</td>"+
				"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgDelay'],3)+"</td>"+
				"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgReady'],3)+"</td>"+
				"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgLoad'],3)+"</td>"+
				"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
				
					}
					return tbtext;
				}
				break;
			case 2://max width
				if (i) {
					i++;
					var tbtext = '', tmp;
					for (var row = 1; row <= i ; row++) {
						if (typeof x[row] == 'undefined') continue;

						tbtext +="<tr class='trb'>";
						tmp = UTF82Native(x[row]['Detail']);
						if ({'19':1,'20':1}[type] || 0) tmp = GetLanCode(tmp);

						tbtext +=
				"<td class='tdltnoborder' title='"+ tmp +"'>"+ tmp +"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['PV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['UPV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['UV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['NV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['RV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Clicks'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Visits'])+"</td>"+
				"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? parseInt(x[row]['Bounces']) : 'Null')+"</td>"+
				"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Exits'])+"</td>"+
				"<td class='tdmidnoborder'>"+(x[row]['ExitRate'] * 0.01).toFixed(2)+"%</td>"+
				"<td class='tdltnoborder'>"+
					"Y: "+parseInt(x[row]['AvgMRY'])+"%<br/>"+
					"X: "+parseInt(x[row]['AvgMRX'])+"%"+ 
				"</td>"+
				"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgDelay'],3)+"</td>"+
				"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgReady'],3)+"</td>"+
				"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgLoad'],3)+"</td>"+
				"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
				
					}
					return tbtext;
				}
				break;
			case 3://middle width
				if (i) {
					i++;
					var tbtext = '', pg, tmp;
					for (var row = 1; row <= i ; row++) {
						if (typeof x[row] == 'undefined') continue;
				
						tbtext +="<tr class='trb'>";
						tmp = UTF82Native(x[row]['Detail']);
						pg = tmp;
						if ({'1':1,'17':1,'18':1,'25':1}[type] || 0) {
							tmp = tmp.replace(/\w+:\/\/[^\/]+/,'');//tmp.replace(/\w+:\/\/.*\//,'/'); remove domain
						} else if ({'19':1,'20':1}[type] || 0) {
							tmp = GetLanCode(tmp);
						} 
						
						tbtext +=
				"<td class='tdltnoborder' title='"+ pg +"'>"+ tmp +"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['PV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['UPV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['UV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Visits'])+"</td>"+
				"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
				"<td class='tdltnoborder'>"+
					"Y: "+parseInt(x[row]['AvgMRY'])+"%<br/>"+
					"X: "+parseInt(x[row]['AvgMRX'])+"%"+ 
				"</td>"+
				"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
				
					}
					return tbtext;
				}
				break;
			case 4://small width
				if (i) {
					i++;
					var tbtext='',pg,tmp;
					for (var row = 1; row <= i ; row++) {
						if (typeof x[row] == 'undefined') continue;
				
						tbtext +="<tr class='trb'>";
						tmp = UTF82Native(x[row]['Detail']);
						pg = tmp;
						if ({'1':1,'17':1,'18':1,'25':1}[type] || 0) {
							tmp = tmp.replace(/\w+:\/\/[^\/]+/,'');//tmp.replace(/\w+:\/\/.*\//,'/');remove domain
						} else if ({'19':1,'20':1}[type] || 0) {
							tmp = GetLanCode(tmp);
						} 
						
						tbtext +=
				"<td class='tdltnoborder' title='"+ pg +"'>"+ tmp +"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['PV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Visits'])+"</td>"+
				"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
				"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
				
					}
					return tbtext;
				}
				break;
			case 5://min width
				if (i) {
					i++;
					var tbtext='',pg,tmp;
					for (var row = 1; row <= i ; row++) {
						if (typeof x[row] == 'undefined') continue;

						tbtext +="<tr class='trb'>";
						tmp = UTF82Native(x[row]['Detail']);
						pg = tmp;
						if ({'1':1,'17':1,'18':1,'25':1}[type] || 0) {
							tmp = tmp.replace(/\w+:\/\/[^\/]+/,'');//tmp.replace(/\w+:\/\/.*\//,'/');//remove domain
						} else if ({'19':1,'20':1}[type] || 0) {
							tmp = GetLanCode(tmp);
						} 
						
						tbtext +=
				"<td class='tdltnoborder' title='"+ pg +"'>"+ tmp +"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['PV'])+"</td>"+
				"<td class='tdmidnoborder'>"+parseInt(x[row]['Visits'])+"</td>"+
				"<td class='tdmidnoborder pR'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td></tr>";
				
					}
					return tbtext;
				}
				break;
			}
		} catch(z) {
			console.log('genTableHtml_AP Error. ' + z.name + ': ' + z.message);
			return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
		}
	}





	function genTableHtml_HM(y, x){
		try {
			var cols;
			switch (y) {
			case 1:
				cols = 23;
				break;
			case 2:
				cols = 11;
				break;
			case 3:
				cols = 8;
				break;
			case 4:
				cols = 5;
				break;
			case 5:
				cols = 4;
				break;
			}

			var i = x[0][2];
			switch (y) {
			default:
			case 1://max width
				if(i) {
					i++;
					var tbtext='',pg,hmpg;
					for (var row = 1; row <= i ; row++) {

						tbtext +="<tr class='trb'>";
						pg = UTF82Native(x[row]['Detail']);
						tmp = pg.replace(/\w+:\/\/[^\/]+/,'');//remove domain
						hmpg = pg + (pg.indexOf('?') > -1 ? '&' + Extra : '?' + Extra);
						
						tbtext +=
			"<td class='tdltnoborder' title='"+ pg +"'>" + (row == 1 ? tmp : "<a class='lnk' href='"+ hmpg +"' target='_blank'>"+ tmp +"</a>") + "</td>"+
			"<td class='tdmidnoborder'>" + (row == 1 ? "" : "<a class='lnk' href='"+ hmpg +"' target='_blank' title='view heatmap of this page' alt='view heatmap of this page'><img src='images/heatmap.png' /></a>") + "</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Clicks']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['ValidClicks']+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['PV'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['UPV'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['UV'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['NV'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['RV'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['RVS'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['Visits'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['DREntry'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['SEEntry'])+"</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['RFEntry'])+"</td>"+
			"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? parseInt(x[row]['Bounces']) : 'Null')+"</td>"+
			"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
			"<td class='tdmidnoborder'>"+parseInt(x[row]['Exits'])+"</td>"+
			"<td class='tdmidnoborder'>"+(x[row]['ExitRate'] * 0.01).toFixed(2)+"%</td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+parseInt(x[row]['AvgMRY'])+"%<br/>"+
				"X: "+parseInt(x[row]['AvgMRX'])+"%"+ 
			"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgDelay'],3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgReady'],3)+"</td>"+
			"<td class='tdmidnoborder'>"+FormatTime(x[row]['AvgLoad'],3)+"</td>"+
			"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
			
					}
					return tbtext;
				}
				break;
			case 2://middle width
				if(i) {
					i++;
					var tbtext='',pg,hmpg;
					for (var row = 1; row <= i ; row++) {
			
						tbtext +="<tr class='trb'>";
						pg = UTF82Native(x[row]['Detail']);
						hmpg = pg + (pg.indexOf('?') > -1 ? '&' + Extra : '?' + Extra);
		
						tbtext +=
			"<td class='tdltnoborder' title='"+ pg +"'>" + (row == 1 ? tmp : "<a class='lnk' href='"+ hmpg +"' target='_blank'>"+ tmp +"</a>") + "</td>"+
			"<td class='tdmidnoborder'>" + (row == 1 ? "" : "<a class='lnk' href='"+ hmpg +"' target='_blank' title='view heatmap of this page' alt='view heatmap of this page'><img src='images/heatmap.png' /></a>") + "</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Clicks']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['ValidClicks']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Visits']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['UV']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['PV']+"</td>"+
			"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
			"<td class='tdmidnoborder'>"+(x[row]['ExitRate'] * 0.01).toFixed(2)+"%</td>"+
			"<td class='tdltnoborder'>"+
				"Y: "+parseInt(x[row]['AvgMRY'])+"<br/>"+
				"X: "+parseInt(x[row]['AvgMRX'])+
			"</td>"+
			"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
			
					}
					return tbtext;
				}
				break;
			case 3://small width
				if(i) {
					i++;
					var tbtext='',pg,hmpg,tmp;
					for (var row = 1; row <= i ; row++) {
			
						tbtext +="<tr class='trb'>";
						pg = UTF82Native(x[row]['Detail']);
						tmp = pg.replace(/\w+:\/\/[^\/]+/,'');//remove domain
						hmpg = pg + (pg.indexOf('?') > -1 ? '&' + Extra : '?' + Extra);
						
						tbtext +=
			"<td class='tdltnoborder' title='"+ pg +"'>" + (row == 1 ? tmp : "<a class='lnk' href='"+ hmpg +"' target='_blank'>"+ tmp +"</a>") + "</td>"+
			"<td class='tdmidnoborder'>" + (row == 1 ? "" : "<a class='lnk' href='"+ hmpg +"' target='_blank' title='view heatmap of this page' alt='view heatmap of this page'><img src='images/heatmap.png' /></a>") + "</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Clicks']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['ValidClicks']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Visits']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['PV']+"</td>"+
			"<td class='tdmidnoborder'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td>"+
			"<td class='tdmidnoborder pR'>"+FormatTime(x[row]['AvgOnline'],1)+"</td></tr>";
			
					}
					return tbtext;
				}
				break;
			case 4://tiny width
				if(i) {
					i++;
					var tbtext='',tmp;
					for (var row = 1; row <= i ; row++) {
			
						tbtext +="<tr class='trb'>";
						pg = UTF82Native(x[row]['Detail']);
						tmp = pg.replace(/\w+:\/\/[^\/]+/,'');//remove domain
						hmpg = pg + (pg.indexOf('?') > -1 ? '&' + Extra : '?' + Extra);
						
						tbtext +=
			"<td class='tdltnoborder' title='"+ pg +"'>" + (row == 1 ? tmp : "<a class='lnk' href='"+ hmpg +"' target='_blank'>"+ tmp +"</a>") + "</td>"+
			"<td class='tdmidnoborder'>" + (row == 1 ? "" : "<a class='lnk' href='"+ hmpg +"' target='_blank' title='view heatmap of this page' alt='view heatmap of this page'><img src='images/heatmap.png' /></a>") + "</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Clicks']+"</td>"+
			"<td class='tdmidnoborder'>"+x[row]['PV']+"</td>"+
			"<td class='tdmidnoborder pR'>"+(parseInt(x[row]['Visits']) ? (x[row]['BounceRate'] * 0.01).toFixed(2)+'%' : 'Null') + "</td></tr>";
			
					}
					return tbtext;
				}
				break;
			case 5://min width
				if(i) {
					i++;
					var tbtext='',tmp;
					for (var row = 1; row <= i ; row++) {
			
						tbtext +="<tr class='trb'>";
						pg = UTF82Native(x[row]['Detail']);
						tmp = pg.replace(/\w+:\/\/[^\/]+/,'');//remove domain
						hmpg = pg + (pg.indexOf('?') > -1 ? '&' + Extra : '?' + Extra);
						
						tbtext +=
			"<td class='tdltnoborder' title='"+ pg +"'>" + (row == 1 ? tmp : "<a class='lnk' href='"+ hmpg +"' target='_blank'>"+ tmp +"</a>") + "</td>"+
			"<td class='tdmidnoborder'>" + (row == 1 ? "" : "<a class='lnk' href='"+ hmpg +"' target='_blank' title='view heatmap of this page' alt='view heatmap of this page'><img src='images/heatmap.png' /></a>") + "</td>"+
			"<td class='tdmidnoborder'>"+x[row]['Clicks']+"</td>"+
			"<td class='tdmidnoborder pR'>"+x[row]['PV']+"</td></tr>";
			
					}
					return tbtext;
				}
				break;
			}
		} catch(z) {
			return "<tr class='tra'><td class='tdmid' colspan='" + cols + "'>No Data</td></tr>";
		}
	}

	function pagesHtml(x) {
		
		if (typeof x[0] === 'undefined') return '';
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
			textHtml += "<a class='pagebut' onclick='" + box + ".PreviousPage()' title='Previous 5 Pages'>&lsaquo;</a>";
		}
		
		for (var i=start; i<=end; i++) {
			if (i == cPage) {
				textHtml += "<span class='page_focus'>" + i + "</span>";
			} else {
				textHtml += "<a class='page' onclick='" + box + ".GoPage(" + i + ")'>" + i + "</a>";
			}
		}
		
		if (pages > limit) {
			textHtml += "<a class='pagebut' onclick='" + box + ".NextPage()' title='Next 5 Pages'>&rsaquo;</a>";
			textHtml += "<a class='pagebut' onclick='" + box + ".GoPage(" + pages + ")' title='Last Page'>&raquo;</a>";
			
		}


		//rows select begin
		textHtml += "<ul><li><a class='select' onclick='selectButton(this)'>" + that.end + "</a><ul id='" + cName + "rows'>";
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
	//************************************* Modular Table End *************************************

		
	//************************************* Modular Pie Begin *************************************
	function drawPie(x) {

			var canvas = document.getElementById('CAPLUGIN-' + cName),
				colours = ['','#5B90BF','#d08770','#ebcb8b','#a3be8c','#96b5b4','#8fa1b3','#b48ead','#ab7967','#bf616a','#cc3346','','','','','','','','','','',''];

			var Data = [],
			n = x[0][0],
			m = x[0][2];
			if (n > m) {
				var tmp = 0, t=1;
				for (t = 1; t < m; t++) {
					tmp += parseInt(x[t][0]);
					if (t > 10) {
						colours[t] = gColour(colours[t-10], 40);
					}
				}
				if (t > 10) colours[m] = gColour(colours[m-10], 40);
				
				x[m][0] = x[0][4] - tmp;
				x[m][1] = 'Other';
			}
			
			n = m + 1;
			for (var i = 1; i < n; i++) {
				Data.push({
					value: x[i][0],
					color: colours[i],
					highlight: gColour(colours[i], 10),
					label: formatData(x[i][1],x[i][0],x[0][4],0,1) //tmp
				});
			}
			//Chart.defaults.global.responsive = true;
			if (iChart) iChart.destroy();
			iChart = new Chart(canvas.getContext('2d')).Pie(Data, {
				tooltipTemplate: "<%if (label) {%><%=label%><%}%>", //"<%if (label) {%><%=label%>: <%}%><%= value %>", 
				segmentStrokeWidth: 1,
				animationSteps: 40,
				animationEasing: 'easeInQuad',
				responsive: true,
				animation: true, 
				customTooltips: function(tooltip) {showTip(tooltip)}
			});
			//
			document.getElementById(cName + 'Title').innerHTML = '<span style ="color: #777; padding-right:10px;">&ndash; ' + Lan[x[0][3]] + '</span>';
			// 
			var legendHolder = document.getElementById('CAPLUGINlegendHolder-' + cName);
			legendHolder.innerHTML = iChart.generateLegend();
			// Include a html legend template after the module pie itself
			helpers.each(legendHolder.firstChild.childNodes, function(legendNode, index) {
				helpers.addEvent(legendNode, 'mouseover', function() {
					var activeSegment = iChart.segments[index];
					activeSegment.save();
					activeSegment.fillColor = activeSegment.highlightColor;
					iChart.showTooltip([activeSegment]);
					activeSegment.restore();
				});
			});
			helpers.addEvent(legendHolder.firstChild, 'mouseout', function() {
				iChart.draw();
				showTip(false);
			});
			//resize pie
			Resize();
			
	}


	function showTip(tooltip) {

			// Tooltip Element
			var tooltipEl = document.getElementById('chartjs-tooltip');
			if (!tooltipEl) tooltipEl = AppendEl('DIV','chartjs-tooltip','','');

			// Hide if no tooltip
			if (!tooltip) {
				tooltipEl.style.opacity = 0;
				return;
			}

			// Set caret Position
			removeClass(tooltipEl,'above below');
			addClass(tooltipEl,tooltip.yAlign);

			// Set Text
			tooltipEl.innerHTML = tooltip.text;

			// Find Y Location on page
			var top;
			if (tooltip.yAlign == 'above') {
				top = tooltip.y - tooltip.caretHeight - tooltip.caretPadding;
			} else {
				top = tooltip.y + tooltip.caretHeight + tooltip.caretPadding;
			}

			// Display, position, and set styles for font
			tooltipEl.style.left = tooltip.chart.canvas.offsetLeft + tooltip.x + "px";
			tooltipEl.style.top = tooltip.chart.canvas.offsetTop + top + "px";
			tooltipEl.style.fontFamily = tooltip.fontFamily;
			tooltipEl.style.fontSize = tooltip.fontSize;
			tooltipEl.style.fontStyle = tooltip.fontStyle;
			tooltipEl.style.opacity = 1;
	}


	function updatePie(x) {

			//iChart.clear()
			var n = x[0][0],
			t = 0,
			m = x[0][2];
			if (n > m) {
				var tmp = 0;
				for (t = 1; t < m; t++) {
					tmp += parseInt(x[t][0]);
					if (t > 10) {
						colours[t] = gColour(colours[t-10], 40);
					}
				}
				if (t > 10) colours[m] = gColour(colours[m-10], 40);
				
				if (key > 4 && key < 13) {
					
				} else {
					x[m][0] = x[0][4] - tmp;
					x[m][1] = 'Other';
				}
			}

			n = m + 1;
			for (var i = 1; i < n; i++) {
				t = i - 1;
				iChart.segments[t]['value'] = x[i][0];
				iChart.segments[t]['label'] = formatData(x[i][1],x[i][0],x[0][4],0,1); //tmp;
			}
			iChart.update();
			document.getElementById(cName + 'Title').innerHTML = '<span style ="color: #777; padding-right:10px;">&ndash; ' + Lan[x[0][3]] + '</span>';
			//
			var legendHolder = document.getElementById('CAPLUGINlegendHolder-' + cName);
			legendHolder.innerHTML = iChart.generateLegend();
			helpers.each(legendHolder.firstChild.childNodes, function(legendNode, index) {
				helpers.addEvent(legendNode, 'mouseover', function() {
					var activeSegment = iChart.segments[index];
					activeSegment.save();
					activeSegment.fillColor = activeSegment.highlightColor;
					iChart.showTooltip([activeSegment]);
					activeSegment.restore();
				});
			});
			helpers.addEvent(legendHolder.firstChild, 'mouseout', function() {
				iChart.draw();
			});

	}
		
		
	function gColour(col, amt) {
		try {
			var usePound = false;

			if (col[0] == '#') {
				col = col.slice(1);
				usePound = true;
			}

			var num = parseInt(col,16);

			var r = (num >> 16) + amt;

			if (r > 255) r = 255;
			else if (r < 0) r = 0;

			var b = ((num >> 8) & 0x00FF) + amt;

			if (b > 255) b = 255;
			else if (b < 0) b = 0;

			var g = (num & 0x0000FF) + amt;

			if (g > 255) g = 255;
			else if (g < 0) g = 0;

			return (usePound ? '#' : '') + (g | (b << 8) | (r << 16)).toString(16);
		} catch(z) {
			alert('gColour error!');
		}
	}
	//************************************* Modular Pie End *************************************


	//************************************ Modular Bar Begin ************************************
		function drawBar(x) {

			ChartData = iniBarData(x);
			if (iChart) iChart.destroy();
			var canvas = document.getElementById('CAPLUGIN-' + cName);
			iChart =  new Chart(canvas.getContext('2d')).Bar(ChartData, {
				tooltipTemplate: "<%if (datasetLabel) {%><%=datasetLabel%><%}%>",
				animationSteps: 40,
				tooltipYPadding: 7,
				tooltipXPadding: 7,
				tooltipCornerRadius: 4,
				maintainAspectRatio: false,
				responsive: true
			});
			setLable(x);
			//console.log(iChart.datasets);
				
		}
		
		
		function updateBar(x) {
			var n = x[0][2],a=[],b=[];
			for (var i = 1, t; i <= n; i++) {
				t = i - 1;
				if (typeof(iChart.datasets[0].bars[t]) === 'undefined') {
					if (key === 5 || key === 6) {
						a.push(x[i][0] * 0.01);
					} else {
						a.push(x[i][0]);
					}
					b.push(formatData(x[i][1],0,0,12,0))
				} else {
					if (key === 5 || key === 6) {
						iChart.datasets[0].bars[t].value = x[i][0] * 0.01;
					} else {
						iChart.datasets[0].bars[t].value = x[i][0];
					}
					iChart.datasets[0].bars[t].label = formatData(x[i][1],0,0,12,0);
				}
			}
			if (a.length) {
				iChart.addData(a,b);
			} else {
				iChart.update();
			}
			setLable(x);
		}
		
		
		function setLable(Data) {
			var a = Data[0][2];
			for (var i = 1, t; i <= a; i++) {
		   		t = i - 1;
				if (key === 5 || key === 6) {
					iChart.datasets[0].bars[t].datasetLabel = Data[i][1] + ': ' + Data[i][0] * 0.01 + '% (' + (Data[i][0] / Data[0][4] * 1E2).toFixed(2) + '%)'; // formatData(Data[i][1],Data[i][0],Data[0][4],0,1);
				} else {
					iChart.datasets[0].bars[t].datasetLabel = Data[i][1] + ': ' + Data[i][0] + ' (' + (Data[i][0] / Data[0][4] * 1E2).toFixed(2) + '%)'; // formatData(Data[i][1],Data[i][0],Data[0][4],0,1);
				}
			}
		}
		

		function iniBarData(Data) {//a means data array length, b means datasets length
			
				var a = Data.length - 1, // a is data length,
				b = Data[0].length, // b is datasets length
				iniLabArr = iniArray(0, Data),
				iniColorArr = ["rgba(151,187,205,","rgba(220,220,220,"],
				iniDatasetArr = iniDatasets(Data);
				
				document.getElementById(cName + 'Title').innerHTML = iniTitle(Data);
				
				return {
					labels : iniLabArr,
					datasets : iniDatasetArr
				};
				
				function iniArray(c, Data) {//c is flag: 0 as label, 1 as data
					var ret = [],
					a = Data[0][2],
					d;
					for (var i = 1; i <= a; i++) {
						d = (c == 0) ? formatData(Data[i][1],0,0,12,0) : (key === 5 || key === 6) ? Data[i][0] * 0.01 : Data[i][0];
						ret.push(d);
					}
					return ret;
				}
				
				function iniDatasets(Data) {
					var ret = [];

					ret.push({
					datasetLabel : 'test',
					fillColor : iniColorArr[0] + "0.5)",
					strokeColor : iniColorArr[0] + "0.8)",
					highlightFill : iniColorArr[0] + "0.75)",
					highlightStroke : iniColorArr[0] + "1)",
					data : iniArray(1,Data)});

					return ret;
				}
				
				function iniTitle(Data) {
					return '<span style ="color:' + iniColorArr[0] + '; padding-right:10px;">&ndash; ' + Lan[Data[0][3]] + '</span>';
				}
		}//end iniData
	//************************************* Modular Bar End *************************************


	//*********************************** Common Fuction Begin **********************************
	function Resize() {
		/*
		var TSW = screen.width + 0;
		var TFW = parseInt(document.getElementById(box).style.width);
		if (TSW == SW && Math.abs(TFW-FW) < 100) {
			return;
		} else {
			SW = TSW;
			FW = TFW;
		}
		*/

		switch (cType) {
		case 0://table
		case 3://full data table
		case 8://overview
			drawTable_AP(AjaxData);
			break;
		case 1://pie
			var obj = document.getElementById('chartjs-tooltip');
			if (obj) {
				obj.style.opacity = 0;
				obj.style.top = '0px'; 
				obj.style.left = '0px'; 
			}
			
			var a = parseInt(document.getElementById(cName).offsetWidth),
			b = document.getElementById('CAPLUGINlegendHolder-' + cName);
			if (!b) return;
			if (a < 480) {
				b.style.width = '100%';
				b.style.float = 'left'; 
				b.style.marginTop = '10px';
			} else {
				b.style.width = 'auto';
				b.style.float = 'none'; 
				b.style.marginTop = '0px';
			}
			break;
		case 2://bar
			drawBar(AjaxData);
			break;
		case 4://table for heatmap
			drawTable_HM(AjaxData);
			break;
		}
	}


	function Update() {
		if (document.hidden) return;
		that.wGet(PassportUrl,true,1); 
	}


	function AppendEl(el,id,istyle,cls) {
		try {
			var b = document.getElementsByTagName('BODY')[0],
				e = document.createElement(el);
			if (id) e.setAttribute('id', id);
			if (istyle) e.setAttribute('style', istyle);
			if (cls) e.setAttribute('class', cls);
			b.appendChild(e);
			return e;
		} catch(z) {
			alert('AppendEl failed');
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


	function UTF82Native(code) {
		try {
			var a = decodeURIComponent(code);
			return unescape(a.replace(/&#x/g, '%u').replace(/;/g, ''));
		} catch(z) {
			return code;
		}
	}


	function formatData(a,b,c,d,e) { //a as lable text, b as value, c as total value, d as limit string length, e as turn to precent format
		try {
				var tmp = UTF82Native(a);
				if ({'1':1,'17':1,'18':1,'25':1}[type] || 0) {//remove domain & short url
					tmp = tmp.replace(/\w+:\/\/[^\/]+/,'');
				} else if ({'19':1,'20':1}[type] || 0) {//language & country code
					tmp = GetLanCode(tmp);
				}
				if (d > 0 && tmp.length > d) tmp = tmp.substring(0,d) + '...';//bar lable text
				
				var tmp2 = b;
				if ({'5':1,'6':1}[key] || 0) {//exit rate & bounce rate 
					tmp2 =  (b >= 10000) ? '100%' : (b * 0.01).toFixed(2) + '%';
				} else if ({'9':1,'10':1}[key] || 0) {//avg dom ready & avg load  
					tmp2 = FormatTime(b,3);
				} else if ({'11':1}[key] || 0) {//avg online  
					tmp2 = FormatTime(b,1);
				}
				if (e) tmp += ': ' + tmp2 + ' (' + (b / c * 1E2).toFixed(2) + '%)';//pie lable text

				return tmp;
		} catch(z) {
			return '';
		}
	}

	function FormatDate(ptime, fmt) {
		
		var weekday=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

		var pdate = new Date();
		pdate.setTime(ptime);
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
		case 1:
			return 'Pages';
		case 2:
			return 'Internal Links';
		case 3:
			return 'Direct Entry';
		case 4:
			return 'Search Engine Details';
		case 5:
			return 'Websites';
		case 6:
			return 'Keywords';
		case 7:
			return 'Browsers';
		case 8:
			return 'Operating Systems';
		case 9:
			return 'Browser Engines';
		case 10:
			return 'Devices';
		case 11:
			return 'Resolutions';
		case 12:
			return 'Country';	
		case 13:
			return 'Region';
		case 14:
			return 'City';
		case 15:
			return 'Search Engines';
		case 16:
			return 'Backlinks';
		case 17:
			return 'Days';
		case 19:
			return 'Language Code';
		case 20:
			return 'Browser Language';
		case 21:
			return 'Server Time';
		case 22:
			return 'Local Time';
		case 23:
			return 'Page Duration';
		case 24:
			return 'Session Duration';
		case 25:
			return 'Robot Crawled Pages';
		case 26:
			return 'Spiders';
		case 27:
			return 'Domains';
		case 28:
			return 'Browser Version';
		case 29:
			return 'OS Version';
		case 30:
			return 'Sources';
		case 31:
			return 'Mediums';
		case 32:
			return 'Terms';
		case 33:
			return 'Contents';
		case 34:
			return 'Campaigns';
		case 35:
			return 'Spider IP';
		case 36:
			return 'Spider IP Block';
		case 37:
			return 'Viewed Pages';
		case 60:
			return 'Overview';
		case 63:
			return 'Entry Pages';
		case 64:
			return 'Bounce Pages';
		case 65:
			return 'Exit Pages';
		case 66:
			return 'Page Urls';
		case 67:
			return 'All Referrers';
		case 68:
			return 'All Pages';
		}
		return '';
	}

	function GetLanCode(x) {
		var lCode = [], splitChar = '';
		splitChar = (x.indexOf('-') > -1) ? '-' : (x.indexOf('_') > -1) ? '_' : '';
		splitChar ? lCode = x.split(splitChar) : lCode[0] = x;
		var lan = lanA[lCode[0]] || lCode[0],
			reg = '',
			cty = '',
			ret = lan;

		if (lCode.length === 1) return lan;
		if (lCode[1].length < 3 || parseInt(lCode[1]) > 0) {
			if (lCode[2]) reg = regA[lCode[2]] || lCode[2];
			if (lCode[1]) cty = ctyA[lCode[1]] || lCode[1];
		} else {
			if (lCode[1]) reg = regA[lCode[1]] || lCode[1];
			if (lCode[2]) cty = ctyA[lCode[2]] || lCode[2];
		}
		if (reg) ret = ret + ' ' + reg;
		if (cty) ret = ret + ' - ' + cty;
		return (ret ? ret : x);
	}

	var lanA, 
		ctyA, 
		regA,
		LanguageCode = LanCode.substr(0,2);
		
	if (LanguageCode === 'zh') {
		lanA = {
			'' : 'Unknown',
			'Other' : '其它',
			'af' : '南非荷兰语',
			'ar' : '阿拉伯语',
			'az' : '阿泽里语',
			'be' : '白俄罗斯语',
			'bg' : '保加利亚语',
			'bn' : '孟加拉语',
			'bs' : '波斯尼亚语',
			'ca' : '加泰罗尼亚语',
			'cs' : '捷克语',
			'cy' : '威尔士语',
			'da' : '丹麦语',
			'de' : '德语',
			'div' : '马尔代夫语',
			'el' : '希腊语',
			'en' : '英语',
			'es' : '西班牙语',
			'et' : '爱沙尼亚语',
			'eu' : '巴斯克语',
			'fa' : '波斯语',
			'fi' : '芬兰语',
			'fil' : '菲律宾语',
			'fo' : '法罗语',
			'fr' : '法语',
			'gl' : '加利西亚语',
			'gu' : '古吉拉特语',
			'he' : '希伯来语',
			'hi' : '印地语',
			'hr' : '克罗地亚语',
			'hu' : '匈牙利语',
			'hy' : '亚美尼亚语',
			'id' : '印度尼西亚语',
			'is' : '冰岛语',
			'it' : '意大利语',
			'ja' : '日语',
			'ka' : '格鲁吉亚语',
			'kk' : '哈萨克语',
			'km' : '高棉语',
			'kn' : '卡纳达语',
			'ko' : '朝鲜语',
			'kok' : '贡根语',
			'ky' : '吉尔吉斯语',
			'lt' : '立陶宛语',
			'lv' : '拉脱维亚语',
			'mk' : '马其顿语',
			'ml' : '马拉雅拉姆语',
			'mn' : '蒙古语',
			'mr' : '马拉地语',
			'ms' : '马来语',
			'nb' : '挪威语（博克马尔）',
			'ne' : '尼泊尔语',
			'nl' : '荷兰语',
			'nn' : '挪威语（尼诺斯克）',
			'no' : '挪威语',
			'pa' : '旁遮普语',
			'pl' : '波兰语',
			'pt' : '葡萄牙语',
			'ro' : '罗马尼亚语',
			'ru' : '俄语',
			'sa' : '梵语',
			'si' : '僧伽罗语',
			'sk' : '斯洛伐克语',
			'sl' : '斯洛文尼亚语',
			'sq' : '阿尔巴尼亚语',
			'sr' : '塞尔维亚语',
			'sv' : '瑞典语',
			'sw' : '斯瓦希里语',
			'syr' : '叙利亚语',
			'ta' : '泰米尔语',
			'te' : '泰卢固语',
			'th' : '泰语',
			'tl' : '塔加路语', 
			'tr' : '土耳其语',
			'tt' : '鞑靼语',
			'uk' : '乌克兰语',
			'ur' : '乌尔都语',
			'uz' : '乌兹别克语',
			'vi' : '越南语',
			'zh' : '中文'
		};

		ctyA = {
			'029' : '加勒比地区',
			'419' : '拉丁美洲和加勒比地区',
			'ad' : '安道尔共和国',
			'ae' : '阿拉伯联合酋长国',
			'af' : '阿富汗',
			'ag' : '安提瓜和巴布达',
			'ai' : '安圭拉岛',
			'al' : '阿尔巴尼亚',
			'am' : '亚美尼亚',
			'ao' : '安哥拉',
			'ar' : '阿根廷',
			'at' : '奥地利',
			'au' : '澳大利亚',
			'az' : '阿塞拜疆',
			'ba' : '波斯尼亚和赫塞哥维纳',
			'bb' : '巴巴多斯',
			'bd' : '孟加拉国',
			'be' : '比利时',
			'bf' : '布基纳法索',
			'bg' : '保加利亚',
			'bh' : '巴林',
			'bi' : '布隆迪',
			'bj' : '贝宁',
			'bm' : '百慕大群岛',
			'bn' : '文莱',
			'bo' : '玻利维亚',
			'br' : '巴西',
			'bs' : '巴哈马',
			'bw' : '博茨瓦纳',
			'by' : '白俄罗斯',
			'bz' : '伯利兹',
			'ca' : '加拿大',
			'cb' : '加勒比',
			'cf' : '中非共和国',
			'cg' : '刚果',
			'ch' : '瑞士',
			'ci' : '科特迪瓦',
			'ck' : '库克群岛',
			'cl' : '智利',
			'cm' : '喀麦隆',
			'cn' : '中国',
			'co' : '哥伦比亚',
			'cr' : '哥斯达黎加',
			'cs' : '塞尔维亚和黑山共和国',
			'cu' : '古巴',
			'cy' : '塞浦路斯',
			'cz' : '捷克',
			'de' : '德国',
			'dj' : '吉布提',
			'dk' : '丹麦',
			'do' : '多米尼加共和国',
			'dz' : '阿尔及利亚',
			'ec' : '厄瓜多尔',
			'ee' : '爱沙尼亚',
			'eg' : '埃及',
			'es' : '西班牙',
			'et' : '埃塞俄比亚',
			'fi' : '芬兰',
			'fj' : '斐济',
			'fo' : '法罗群岛',
			'fr' : '法国',
			'ga' : '加蓬',
			'gb' : '英国',
			'gd' : '格林纳达',
			'ge' : '格鲁吉亚',
			'gf' : '法属圭亚那',
			'gh' : '加纳',
			'gi' : '直布罗陀',
			'gm' : '冈比亚',
			'gn' : '几内亚',
			'gr' : '希腊',
			'gt' : '危地马拉',
			'gu' : '关岛',
			'gy' : '圭亚那',
			'hk' : '香港',
			'hn' : '洪都拉斯',
			'hr' : '克罗地亚',
			'ht' : '海地',
			'hu' : '匈牙利',
			'id' : '印度尼西亚',
			'ie' : '爱尔兰',
			'il' : '以色列',
			'in' : '印度',
			'iq' : '伊拉克',
			'ir' : '伊朗',
			'is' : '冰岛',
			'it' : '意大利',
			'jm' : '牙买加',
			'jo' : '约旦',
			'jp' : '日本',
			'ke' : '肯尼亚',
			'kg' : '吉尔吉斯坦',
			'kh' : '柬埔寨',
			'kp' : '朝鲜',
			'kr' : '韩国',
			'kw' : '科威特',
			'kz' : '哈萨克斯坦',
			'la' : '老挝',
			'lb' : '黎巴嫩',
			'lc' : '圣卢西亚',
			'li' : '列支敦士登',
			'lk' : '斯里兰卡',
			'lr' : '利比里亚',
			'ls' : '莱索托',
			'lt' : '立陶宛',
			'lu' : '卢森堡',
			'lv' : '拉脱维亚',
			'ly' : '利比亚',
			'ma' : '摩洛哥',
			'mc' : '摩纳哥',
			'md' : '摩尔多瓦',
			'mg' : '马达加斯加',
			'mk' : '马其顿',
			'ml' : '马里',
			'mm' : '缅甸',
			'mn' : '蒙古',
			'mo' : '澳门',
			'ms' : '蒙特塞拉特岛',
			'mt' : '马耳他',
			'mu' : '毛里求斯',
			'mv' : '马尔代夫',
			'mw' : '马拉维',
			'mx' : '墨西哥',
			'my' : '马来西亚',
			'mz' : '莫桑比克',
			'na' : '纳米比亚',
			'ne' : '尼日尔',
			'ng' : '尼日利亚',
			'ni' : '尼加拉瓜',
			'nl' : '荷兰',
			'no' : '挪威',
			'np' : '尼泊尔',
			'nr' : '瑙鲁',
			'nz' : '新西兰',
			'om' : '阿曼',
			'pa' : '巴拿马',
			'pe' : '秘鲁',
			'pf' : '法属玻利尼西亚',
			'pg' : '巴布亚新几内亚',
			'ph' : '菲律宾',
			'pk' : '巴基斯坦',
			'pl' : '波兰',
			'pr' : '波多黎各',
			'pt' : '葡萄牙',
			'py' : '巴拉圭',
			'qa' : '卡塔尔',
			'ro' : '罗马尼亚',
			'rs' : '塞尔维亚',
			'ru' : '俄罗斯',
			'sa' : '沙特阿拉伯',
			'sb' : '所罗门群岛',
			'sc' : '塞舌尔',
			'sd' : '苏丹',
			'se' : '瑞典',
			'sg' : '新加坡',
			'si' : '斯洛文尼亚',
			'sk' : '斯洛伐克',
			'sl' : '塞拉利昂',
			'sm' : '圣马力诺',
			'sn' : '塞内加尔',
			'so' : '索马里',
			'sp' : '塞尔维亚',
			'sr' : '苏里南',
			'st' : '圣多美和普林西比',
			'sv' : '萨尔瓦多',
			'sy' : '叙利亚',
			'sz' : '斯威士兰',
			'td' : '乍得',
			'tg' : '多哥',
			'th' : '泰国',
			'tj' : '塔吉克斯坦',
			'tm' : '土库曼斯坦',
			'tn' : '突尼斯',
			'to' : '汤加',
			'tr' : '土耳其',
			'tt' : '特立尼达和多巴哥',
			'tw' : '台湾',
			'tz' : '坦桑尼亚',
			'ua' : '乌克兰',
			'ug' : '乌干达',
			'us' : '美国',
			'uy' : '乌拉圭',
			'uz' : '乌兹别克斯坦',
			'vc' : '圣文森特岛',
			've' : '委内瑞拉',
			'vn' : '越南',
			'xl' : '拉丁美洲',
			'ye' : '也门',
			'yu' : '南斯拉夫',
			'za' : '南非',
			'zm' : '赞比亚',
			'zr' : '扎伊尔',
			'zw' : '津巴布韦'
		};

		regA = {
			'chs' : '（简体）',
			'cht' : '（繁体）',
			'cyrl' : '（西里尔语）',
			'hans' : '（简体）',
			'hant' : '（繁体）',
			'latn' : '（拉丁语）'
		}

	} else {
			
		lanA = {
			'' : 'Unknown',
			'Other' : 'Other',
			'af' : 'Afrikaans',
			'ar' : 'Arabic',
			'az' : 'Azeri',
			'be' : 'Belarus',
			'bg' : 'Bulgarian',
			'bn' : 'Bangla',
			'bs' : 'Bosnian',
			'ca' : 'Catalan',
			'cs' : 'Czech',
			'cy' : 'Welsh',
			'da' : 'Danish',
			'de' : 'German',
			'div' : 'Maldivian',
			'el' : 'Greek',
			'en' : 'English',
			'es' : 'Spanish',
			'et' : 'Estonian',
			'eu' : 'Basque',
			'fa' : 'Persian',
			'fi' : 'Finnish',
			'fil' : 'Filipino',
			'fo' : 'Faeroese',
			'fr' : 'French',
			'gl' : 'Galician',
			'gu' : 'Gujarati',
			'he' : 'Hebrew',
			'hi' : 'Hindi',
			'hr' : 'Croatian',
			'hu' : 'Hungarian',
			'hy' : 'Armenian',
			'id' : 'Indonesian',
			'is' : 'Icelandic',
			'it' : 'Italian',
			'ja' : 'Japanese',
			'ka' : 'Georgian',
			'kk' : 'Kazakh',
			'km' : 'Khmer',
			'kn' : 'Kannada',
			'ko' : 'Korean',
			'kok' : 'Konkan',
			'ky' : 'Kyrgyz',
			'lt' : 'Lithuanian',
			'lv' : 'Latvian',
			'mk' : 'Macedonian',
			'ml' : 'Malayalam',
			'mn' : 'Mongolian',
			'mr' : 'Marathi',
			'ms' : 'Malay',
			'ne' : 'Nepali',
			'nl' : 'Dutch',
			'no' : 'Norwegian',
			'nb' : 'Norwegian (Bock Mar)',
			'nn' : 'Norwegian (Nino Trask)',
			'pa' : 'Punjabi',
			'pl' : 'Polish',
			'pt' : 'Portuguese',
			'ro' : 'Romanian',
			'ru' : 'Russian',
			'sa' : 'Sanskrit',
			'si' : 'Sinhala',
			'sk' : 'Slovakian',
			'sl' : 'Slovenian',
			'sq' : 'Albanian',
			'sr' : 'Serbian',
			'sv' : 'Swedish',
			'sw' : 'Swahili',
			'syr' : 'Syrian',
			'ta' : 'Tamil',
			'te' : 'Telugu',
			'th' : 'Thai',
			'tl' : 'Tagalog', 
			'tr' : 'Turkish',
			'tt' : 'Tatar',
			'uk' : 'Ukrainian',
			'ur' : 'Urdu',
			'uz' : 'Uzbek',
			'vi' : 'Vietnamese',
			'zh' : 'Chinese'
		};

		ctyA = {
			'029' : 'Caribbean',
			'419' : 'Latin America and the Caribbean',
			'ad' : 'Andorra',
			'ae' : 'Arabia United Arab Emirates',
			'ae' : 'United Arab Emirates',
			'af' : 'Afghanistan',
			'ag' : 'Antigua and Barbuda',
			'ai' : 'Anguilla',
			'al' : 'Albania',
			'am' : 'Armenia',
			'ao' : 'Angola',
			'ar' : 'Argentina',
			'at' : 'Austria',
			'au' : 'Australia',
			'az' : 'Azerbaijan',
			'ba' : 'Bosnia and Herzegovina',
			'bb' : 'Barbados',
			'bd' : 'Bangladesh',
			'be' : 'Belgium',
			'bf' : 'Burkina-faso',
			'bg' : 'Bulgaria',
			'bh' : 'Bahrain',
			'bi' : 'Burundi',
			'bj' : 'Benin',
			'bm' : 'Bermuda Is.',
			'bn' : 'Brunei',
			'bo' : 'Bolivia',
			'br' : 'Brazil',
			'bs' : 'Bahamas',
			'bw' : 'Botswana',
			'by' : 'Belarus',
			'bz' : 'Belize',
			'ca' : 'Canada',
			'cb' : 'Caribbean',
			'cf' : 'Central African Republic',
			'cg' : 'Congo',
			'ch' : 'Switzerland',
			'ci' : 'Ivory Coast',
			'ck' : 'Cook Is.',
			'cl' : 'Chile',
			'cm' : 'Cameroon',
			'cn' : 'China',
			'co' : 'Colombia',
			'cr' : 'Costa Rica',
			'cs' : 'Serbia and Montenegro',
			'cu' : 'Cuba',
			'cy' : 'Cyprus',
			'cz' : 'Czech Republic',
			'de' : 'Germany',
			'dj' : 'Djibouti',
			'dk' : 'Denmark',
			'do' : 'Dominica',
			'dz' : 'Algeria',
			'ec' : 'Ecuador',
			'ee' : 'Estonia',
			'eg' : 'Egypt',
			'es' : 'Spain',
			'et' : 'Ethiopia',
			'fi' : 'Finland',
			'fj' : 'Fiji',
			'fo' : 'Faroe Islands',
			'fr' : 'France',
			'ga' : 'Gabon',
			'gb' : 'United Kingdom',
			'gd' : 'Grenada',
			'ge' : 'Georgia',
			'gf' : 'French Guiana',
			'gh' : 'Ghana',
			'gi' : 'Gibraltar',
			'gm' : 'Gambia',
			'gn' : 'Guinea',
			'gr' : 'Greece',
			'gt' : 'Guatemala',
			'gu' : 'Guam',
			'gy' : 'Guyana',
			'hk' : 'Hongkong',
			'hn' : 'Honduras',
			'hr' : 'Croatia',
			'ht' : 'Haiti',
			'hu' : 'Hungary',
			'id' : 'Indonesia',
			'ie' : 'Ireland',
			'il' : 'Israel',
			'in' : 'India',
			'iq' : 'Iraq',
			'ir' : 'Iran',
			'is' : 'Iceland',
			'it' : 'Italy',
			'jm' : 'Jamaica',
			'jo' : 'Jordan',
			'jp' : 'Japan',
			'ke' : 'Kenya',
			'kg' : 'Kyrgyzstan',
			'kh' : 'Kampuchea (Cambodia)',
			'kp' : 'North Korea',
			'kr' : 'South Korea',
			'kw' : 'Kuwait',
			'kz' : 'Kazakhstan',
			'la' : 'Laos',
			'lb' : 'Lebanon',
			'lc' : 'Saint Lueia',
			'li' : 'Liechtenstein',
			'lk' : 'Sri Lanka',
			'lr' : 'Liberia',
			'ls' : 'Lesotho',
			'lt' : 'Lithuania',
			'lu' : 'Luxembourg',
			'lv' : 'Latvia',
			'ly' : 'Libya',
			'ma' : 'Morocco',
			'mc' : 'Monaco',
			'md' : 'Moldova',
			'mg' : 'Madagascar',
			'mk' : 'FYROM',
			'ml' : 'Mali',
			'mm' : 'Burma',
			'mn' : 'Mongolia',
			'mo' : 'Macao',
			'ms' : 'Montserrat Is',
			'mt' : 'Malta',
			'mu' : 'Mauritius',
			'mv' : 'Maldives',
			'mw' : 'Malawi',
			'mx' : 'Mexico',
			'my' : 'Malaysia',
			'mz' : 'Mozambique',
			'na' : 'Namibia',
			'ne' : 'Niger',
			'ng' : 'Nigeria',
			'ni' : 'Nicaragua',
			'nl' : 'Netherlands',
			'no' : 'Norway',
			'np' : 'Nepal',
			'nr' : 'Nauru',
			'nz' : 'New Zealand',
			'om' : 'Oman',
			'pa' : 'Panama',
			'pe' : 'Peru',
			'pf' : 'French Polynesia',
			'pg' : 'Papua New Cuinea',
			'ph' : 'Philippines',
			'pk' : 'Pakistan',
			'pl' : 'Poland',
			'pr' : 'Puerto Rico',
			'pt' : 'Portugal',
			'py' : 'Paraguay',
			'qa' : 'Qatar',
			'ro' : 'Romania',
			'rs' : 'Serbia',
			'ru' : 'Russia',
			'sa' : 'Saudi Arabia',
			'sb' : 'Solomon Is',
			'sc' : 'Seychelles',
			'sd' : 'Sudan',
			'se' : 'Sweden',
			'sg' : 'Singapore',
			'si' : 'Slovenia',
			'sk' : 'Slovakia',
			'sl' : 'Sierra Leone',
			'sm' : 'San Marino',
			'sn' : 'Senegal',
			'so' : 'Somali',
			'sp' : 'Serbia',
			'sr' : 'Suriname',
			'st' : 'Sao Tome and Principe',
			'sv' : 'EI Salvador',
			'sy' : 'Syria',
			'sz' : 'Swaziland',
			'td' : 'Chad',
			'tg' : 'Togo',
			'th' : 'Thailand',
			'tj' : 'Tajikstan',
			'tm' : 'Turkmenistan',
			'tn' : 'Tunisia',
			'to' : 'Tonga',
			'tr' : 'Turkey',
			'tt' : 'Trinidad and Tobago',
			'tw' : 'Taiwan',
			'tz' : 'Tanzania',
			'ua' : 'Ukraine',
			'ug' : 'Uganda',
			'us' : 'United States',
			'uy' : 'Uruguay',
			'uz' : 'Uzbekistan',
			'vc' : 'Saint Vincent',
			've' : 'Venezuela',
			'vn' : 'Vietnam',
			'xl' : 'Latin America',
			'ye' : 'Yemen',
			'yu' : 'Yugoslavia',
			'za' : 'South Africa',
			'zm' : 'Zambia',
			'zr' : 'Zaire',
			'zw' : 'Zimbabwe'
		};

		regA= {
			'chs' : '(Simplified)',
			'cht' : '(traditional)',
			'cyrl' : '(Cyril)',
			'hans' : '(Simplified)',
			'hant' : '(traditional)',
			'latn' : '(Latin)'
		};	
	}

//************************************ Common Fuction End ************************************
	
}//End CHARTAPI Function

