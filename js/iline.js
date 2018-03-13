/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free ILINE API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function ILINE(sid, r, from, to, period, type, timezone, timer, host, box, H, Lan) {
		
		this.sid = sid;
		this.q = 'line';
		this.type = type;
		this.period = period;//0 as day, 1 as week, 2 as month, 3 as year, 4 as date range
		this.from = from;//start date//20151201;//
		this.to = to;//start date//20151201;//
		this.tz = timezone;
		
		if (!H) H = 225;

		var LineData = [],
			lineChartData = [],
			MaxValue = 0,
			StepValue = 1,
			obj,
			iLine,
			hTimer = 0,
			fm = 0,//0 as normal, 1 as precent(%), 2 as seconds
			title = getTitle(type),
			cName = title.replace(/\s+/g,''),
			Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
			APIUrl = Protocol + host + '/api/api_ca.php?',
			PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
			HTML_NODATA = "<tr class='tra'><td class='tdmid'><p style='font-size:36px; color:#ddd; text-align:center; line-height:198px;'>No Data</p></td></tr>",
			that = this;
		
		//add tools
		var obj;
		if (obj = document.getElementById(box + '_R')) obj.href = 'javascript:' + box + '.run()'; 
	
	//********************************** Public Class Function Begin **********************************	
	this.run = function() {
		document.getElementById(box).innerHTML = 
		"<div id='" + cName + "' class='ca_line'>"+
			"<div id='" + cName + "Title' class='line_title'></div>"+
			"<div style='height:" + H + "px; width:auto; margin:10px;'>"+
				"<canvas id='" + cName + "Line' style='height:100%; width:100%;'></canvas>"+
			"</div>"+
		"</div>";
		//run
		this.wGet(PassportUrl); 

	};

	this.callAjax = function(pass) {
		
		var d = [];
		for (var k in this) {
			d.push(k + '=' + this[k]);
			if (k == 'tz') break;
		}
		var url = APIUrl + pass + d.join('&'),
			myAjax;
		try {
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject('Microsoft.XMLHTTP'); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					LineData = [];
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v !== '') {
						LineData = eval(v);
					} else {
						document.getElementById(cName).innerHTML = HTML_NODATA;
						return;
					}
					draw();
				}
			}
			
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch (e) {	//alert(e.name + ': ' + e.message);
			alert('XMLHttpRequest Error - iLine');
		}

	};

	this.wGet = function(a) {// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method('Post' or 'Get'),
		var c, v = '';
		a += '&rnd=' + Math.random();
		try {
			if (window.XMLHttpRequest) {
				c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c = new ActiveXObject('Microsoft.XMLHTTP');// code for IE6, IE5
			}
			c.open('GET', a, true);
			c.send(); 
			c.onreadystatechange = function() {
				if (c.readyState == 4 && c.status == 200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) that.callAjax(v);
				}
			}
		} catch (e) {
			alert('wGet Request Failed - iLine');
		}
	};

	this.resize = function() {
		if (LineData.length > 0) draw();
	};
	//********************************** Public Class Function End **********************************	

	// draw line
	function draw() {
		var strfm = '';
		if (fm == 1 || fm == 3) {
			strfm = '%';
		} else if (fm == 2) {
			strfm = 's';
		} else {
			strfm = '';
		}
		lineChartData = iniData(LineData);
		
		if (fm === 1 || fm === 3) {
			StepValue = 25;
		} else {
			if (MaxValue < 40) {
				StepValue = Math.ceil(MaxValue / 4);
				if (StepValue < 1) StepValue = 1;
			} else if (MaxValue < 100) {
				StepValue = Math.ceil(MaxValue / 20) * 5;
			} else if (MaxValue < 1000) {
				StepValue = Math.ceil(MaxValue / 40) * 10;
			} else if (MaxValue < 10000) {
				StepValue = Math.ceil(MaxValue / 400) * 100;
			} else if (MaxValue < 100000) {
				StepValue = Math.ceil(MaxValue / 4000) * 1000;
			} else {
				StepValue = Math.ceil(MaxValue / 40000) * 10000;
			}
		}
		if (iLine) iLine.destroy();
		obj = document.getElementById(cName + 'Line').getContext('2d');
		iLine = new Chart(obj).Line(lineChartData, {
			multiTooltipTemplate: '<%=datasetLabel%>: <%= value %>' + strfm,
			tooltipTemplate: '<%if (label) {%><%=label%> - <%=datasetLabel%>: <%}%><%= value %>' + strfm,
			bezierCurve: false,
			animationSteps: 40,
			scaleBeginAtZero: true,
			scaleOverride: true,
			scaleSteps: (4 > MaxValue ? MaxValue : 4),
			scaleStepWidth: StepValue,
			scaleStartValue: 0,
			datasetStrokeWidth: 1,
			datasetFill: false,
			responsive: true,
			maintainAspectRatio: false
		});
		
	}


	function iniData(Data) {//a means data array length, b means datasets length
		
			MaxValue = 0;
			var a = Data.length - 1, // a is data length,
				b = Data[0].length, // b is datasets length
				iniColorArr = [['rgba(255,150,50,0.2)','rgba(255,150,50,1)'],['rgba(50,150,255,0.2)','rgba(50,150,255,1)'],['rgba(255,50,50,0.2)','rgba(255,50,50,1)'],['rgba(50,200,150,0.2)','rgba(50,200,150,1)'],['rgba(150,50,200,0.2)','rgba(150,50,200,1)'],["rgba(150,150,150,0.2)","rgba(150,150,150,1)"]],
				iniLabArr = iniArray(a, 0, 0, Data),
				iniDatasetArr = iniDatasets(a,b,Data),
				iniDateArr = iniArray(a, 1, 1, Data);
			
			document.getElementById(cName + 'Title').innerHTML = iniTitle(b,Data);
			
			return {
				labels : iniLabArr,
				datasets : iniDatasetArr,
				dates : iniDateArr
			};
			//
			function iniArray(a, b, c, Data) {//a is data length,b is datasets length, c is flag: 0 as label, 1 as data
				var ret = [],
					d;
				for (var i = 1; i <= a; i++) {
					switch (c) {
					case 0://get date
						d = Data[i][0];
						break;
					case 1://get sunday
						d = Data[i][2] === '0' ? Data[i][0] : '';
						break;
					case 2://get data
						d = parseInt(Data[i][1][b]);
						if (fm == 1) {
							d = d / 1E2;
						} else if (fm == 2) {
							d = d / 1E3;
						}
						if (d > MaxValue) MaxValue = d;
						break;
					}
					ret.push(d);
				}
				return ret;
			}
			//
			function iniDatasets(a, b, Data) {
				var ret = [];
				if (b > 6) b = 6;//max array length is 6
				b--;
				for (var i = b; i > -1; i--) {
					ret.push({
					label: Lan[Data[0][i]],
					fillColor: iniColorArr[i][0],
					strokeColor: iniColorArr[i][1],
					pointColor: iniColorArr[i][1],
					pointStrokeColor: '#fff',
					pointHighlightFill: '#fff',
					pointHighlightStroke: iniColorArr[i][1],
					data: iniArray(a,i,2,Data)
					});
				}
				return ret;
			}
			//
			function iniTitle(a, Data) {
				var ret = '';
				if (a > 6) a = 6;//max array length is 5
				for (var i = 0; i < a; i++) {
					ret += '<span style ="color:' + iniColorArr[i][1] + '; padding-right:10px;">&ndash; ' + Lan[Data[0][i]] + '</span>';
				}
				return ret;
			}
	}//end iniData
	
	function getTitle(type) {
		switch (type) {
		case 0:// Visit Status Overview
			fm = 0;
			return 'Visit Overview';
		case 1:// Exit Status Overview
			fm = 1;
			return 'Exit Status Overview';
		case 2:// Browsing Status Overview
			fm = 3;
			return 'Browsing Status Overview';
		case 3:// Duration Status Overview
			fm = 2;
			return 'Duration Status Overview';
		case 11:// Visits over time
			fm = 0;
			return 'Visits Over Time';
		case 12:// UV over time
			fm = 0;
			return 'UV Over Time';
		case 13:// PV over time
			fm = 0;
			return 'PV Rate Over Time';
		case 14:// UPV over time
			fm = 0;
			return 'UPV Over Time';
		case 15:// RV over time
			fm = 0;
			return 'RV Over Time';
		case 16:// Bounce Rate over time
			fm = 1;
			return 'Bounce Rate Over Time';
		case 17:// Exit Rate over time
			fm = 1;
			return 'Exit Rate Over Time';
		case 18:// Avg Max Read X over time
			fm = 3;
			return 'Avg Max-Read On X-Axis Over Time';
		case 19:// Avg Max Read Y over time
			fm = 3;
			return 'Avg Max-Read On Y-Axis Over Time';
		case 20:// Avg Ready over time
			fm = 2;
			return 'Avg Ready Over Time';
		case 21:// Avg Laod over time
			fm = 2;
			return 'Avg Load Over Time';
		case 22:// Avg Online Rate over time
			fm = 2;
			return 'Avg Online Over Time';
		}
	}


}//end API












/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free DAY TREND LINE API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 07/29/2017 *
* copyright 2015 ~ 2017 WM Jonssen
*/

function IDTLINE(sid, r, from, to, period, type, timezone, timer, host, box, H, Lan) {
		
		this.sid = sid;
		this.q = 'daytrend';
		this.type = type;
		//this.period = period;//0 as day, 1 as week, 2 as month, 3 as year, 4 as date range
		this.from = from;//start date//20151201;//
		this.to = to;//start date//20151201;//
		this.tz = timezone;
		
		if (!H) H = 225;

		var LineData = [],
			lineChartData = [],
			obj,
			iLine,
			Mins = 0,
			Hours = 0,
			hTimer = 0,
			title = getTitle(type),
			cName = title.replace(/\s+/g,''),
			Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
			APIUrl = Protocol + host + '/api/api_ca.php?',
			PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
			HTML_NODATA = "<tr class='tra'><td class='tdmid'><p style='font-size:36px; color:#ddd; text-align:center; line-height:198px;'>No Data</p></td></tr>",
			that = this;
		
		timer = timer < 1 ? 0 : timer < 6E4 ? 6E4 : timer;
		
		//add tools
		var obj;
		if (obj = document.getElementById(box + '_R')) obj.href = 'javascript:' + box + '.run()';
		
		//add resize events
		window.addEventListener ? window.addEventListener('resize',Resize) : document.attachEvent('onresize',Resize);
		
		
	//********************************** Public Class Function Begin **********************************	
	this.run = function() {
		document.getElementById(box).innerHTML = 
		"<div id='" + cName + "' class='ca_line'>"+
			"<div id='" + cName + "Title' class='line_title'></div>"+
			"<div style='height:" + H + "px; width:auto; margin:10px;'>"+
				"<canvas id='" + cName + "DTLine' style='height:100%; width:100%'></canvas>"+
			"</div>"+
		"</div>";
		//run
		this.wGet(PassportUrl ,true, 0);
		if (timer > 0 && hTimer === 0) hTimer = setInterval(Update, timer);
	};

	this.callAjax = function(pass,flag){//flag = 0 is ini, flag = 1 is update
		var d = [];
		for (var k in this) {
			d.push(k + "=" + this[k]);
			if (k == 'tz') break;
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
					LineData = [];
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v !== ''){
						LineData = eval(v);
					} else {
						document.getElementById(cName).innerHTML = HTML_NODATA;
						return;
					}
					if (flag) {
						updateLine();
					} else {
						draw();
					}
				}
			}
			
			myAjax.open("GET", url, true);
			myAjax.send();
		} catch (e) {	//alert(e.name + ": " + e.message);
			alert('XMLHttpRequest Error - DTLine Chart');
		}

	};

	this.wGet = function(a,b,flag){// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get"),
		var c,v="";
		a += "&rnd=" + Math.random();
		try{
			if (window.XMLHttpRequest) {
				c=new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c=new ActiveXObject("Microsoft.XMLHTTP");// code for IE6, IE5
			}
			c.open("GET",a,b);
			c.send(); 
			c.onreadystatechange=function(){
				if (c.readyState==4 && c.status==200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) that.callAjax(v,flag);
				}
			}
		} catch (e) {
			alert("wGet Request Failed - DTLine Chart");
		}
	};

	this.resize = function() {
		try {
			if (LineData.length > 0) Resize();
		} catch (z) {
			return;
		}
	};

	//*********************************** Public Class Function End ***********************************	


	//draw table
	function draw(){
		lineChartData = iniData(LineData);
		if (iLine) iLine.destroy();
		obj = document.getElementById(cName + "DTLine").getContext("2d");
		iLine = new Chart(obj).Line(lineChartData, {
			multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>",
			tooltipTemplate: "<%if (label){%><%=label%> - <%=datasetLabel%>: <%}%><%= value %>",
			bezierCurve: false,
			animationSteps: 10,
			pointDot: false,//display value point
			scaleBeginAtZero: true,
			scaleOverride: false,
			scaleSteps: 4,
			scaleStepWidth: 1,
			scaleStartValue: 0,
			datasetStrokeWidth : 1,
			datasetFill : true,
			responsive: true,
			maintainAspectRatio: false
		});
	}

	function iniData(Data){//a -> data array length, b -> chart datasets length

			var w = document.getElementById(cName + "DTLine").offsetWidth;

			if (w > 1580) { //1440 
				Mins = 1;
			} else if (w >860) { //720
				Mins = 2;
			} else { //288
				Mins = 5;
			}

			Hours = w < 400 ? 2 : 1;
				
			//console.log(Mins,Hours,w);
			var a = Data.length - 1, // a is data length,
			b = Data[1].length - 1, // b is datasets length  rgba(151,187,205,0.2)
			iniColorArr = [[""],["rgba(151,187,205,0.2)","rgba(151,187,205,1)"],["rgba(50,150,255,0.2)","rgba(50,150,255,1)"],["rgba(255,50,50,0.2)","rgba(255,50,50,1)"],["rgba(50,200,150,0.2)","rgba(50,200,150,1)"],["rgba(150,50,200,0.2)","rgba(150,50,200,1)"],["rgba(150,150,150,0.2)","rgba(150,150,150,1)"]],
			iniLabArr = getTimeArray(Mins,0),
			iniDatasetArr = iniDatasets(a,b,Data),
			iniDateArr = getTimeArray(Mins,Hours);
			//console.log(iniLabArr);
			//console.log(iniDateArr);
			
			document.getElementById(cName + 'Title').innerHTML = iniTitle(b,Data);
			
			return {
			labels : iniLabArr,
			datasets : iniDatasetArr,
			dates : iniDateArr
			};
			//
			function iniDatasets(a,b,Data) {
				var ret = [];
				if (b > 6) b = 6;//max array length is 6
				for (var i = b; i > 0; i--) {
					ret.push({
					label: Lan[Data[0][i-1]],
					fillColor: iniColorArr[i][0],
					strokeColor: iniColorArr[i][1],
					pointColor: iniColorArr[i][1],
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: iniColorArr[i][1],
					data: iniArray(a,i,Data)});
				}
				return ret;
			}
			//
			function iniTitle(a, Data) {
				var ret = '';
				if (a > 6) a = 6;//max array length is 6
				for (var i = 1; i <= a; i++) {
					ret += '<span style ="color:' + iniColorArr[i][1] + '; padding-right:10px;">&ndash; ' + Lan[Data[0][i-1]] + '</span>';
				}
				return ret;
			}

	}//end iniData
	
	function iniArray(a, b, Data) {//a -> data array length, b -> chart datasets series, Data -> data array
		var ret = [],
			d,
			n = 0,
			iniDataArr = getTimeArray(Mins,-1),
			rn = getDataByTime(Mins,Data[0][6]);

		for (var i = 1; i <= a; i++) {
			d = getDataByTime(Mins,Data[i][0]);
			if (iniDataArr[d] < parseInt(Data[i][b])) iniDataArr[d] = parseInt(Data[i][b]);
		}

		for (key in iniDataArr) {
			if (n < iniDataArr[key]) n = iniDataArr[key];
			ret.push(n);
			if (key == rn) break;
		}
		//console.log(ret);
		return ret;
	}
	
	/*
	function updateLine2(Data){

		var a = Data.length - 1, // a is data length,
			b = Data[1].length - 1, // b is datasets length
			ret = [];
		for (var n = b,i = 0; n > 0; n--) {
			ret = iniArray(a,b,Data);
			for (i = 0; i < a; i++){
				iLine.datasets[n-1].points[i].value = ret[i];
			}
		}
		iLine.update();
		//console.log(iLine);
	}
	*/
	
	function updateLine(){

		lineChartData = iniData(LineData);
		if (iLine) iLine.destroy();
		obj = document.getElementById(cName + "DTLine").getContext("2d");
		iLine = new Chart(obj).Line(lineChartData, {
			multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>",
			tooltipTemplate: "<%if (label){%><%=label%> - <%=datasetLabel%>: <%}%><%= value %>",
			animation: false,
			bezierCurve: false,
			pointDot: false,//display value point
			scaleBeginAtZero: true,
			scaleOverride: false,
			scaleSteps: 4,
			scaleStepWidth: 1,
			scaleStartValue: 0,
			datasetStrokeWidth : 1,
			datasetFill: true,
			responsive: true,
			maintainAspectRatio: false
		});
		//console.log(iLine);
	}
	
	
	function Update(){
		if (document.hidden) return;
		that.wGet(PassportUrl,true,1); 
	}

//*********************************** Common Fuction Begin **********************************
	function Resize() {
		updateLine();
	}
	
	function getTimeArray(x,y){ //x分钟间隔（1,2,5），y=0获取分钟周期时间字符数组，y=1获取一小时周期时间字符数组，y=2获取二小时周期时间字符数组
		var t=0,
			h,
			m,
			Arr = [],
			tmp,
			n = 1440 / x,
			p = y * 60;
	
		for (var i = 0; i <= n; i++) { 
			t = i * x;
			if (y > 0) {
				if (t % p === 0) {
					h = Math.floor(t / p) * y;
					Arr.push((h < 10 ? '0' + h : h) + ':00');
				} else {
					Arr.push('');
				}
			} else if (y === 0) {
				h = Math.floor(t / 60);
				m = t % 60;
				Arr.push((h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m));
			} else if (y === -1) {
				h = Math.floor(t / 60);
				m = t % 60;
				tmp =(h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
				Arr[tmp] = 0;
			}
	   	}
	   	//console.log(Arr);
		return Arr;
	}
	
	function getDataByTime(x,y){ //x分钟间隔(1,2,5)
		var t, i, h, m;
	
			i = parseInt(y.substr(0,2)) * 60 + parseInt(y.substr(3,2));
			t = Math.floor(i / x + 1) * x;
	
			h = Math.floor(t / 60);
			m = t % 60;
	
		return (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
	}
	
	function getTitle(type) {
		switch (type) {
		case 18:// DAY TREND
			return 'Day Trend';
		}
	}


}//end API function









/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free REAL TIME LINE API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 07/29/2017 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

function IRTLINE(sid, r, from, to, period, type, timezone, timer, host, box, H, Lan) {
		
		this.sid = sid;
		this.q = "rtline";
		this.type = type;
		this.tz = timezone;
		
		if (!H) H = 225;

		var LineData = [],
			lineChartData = [],
			MaxValue = 0,
			StepValue = 1,
			obj,
			iLine,
			hTimer = 0,
			fm = 0,//0 as normal, 1 as precent(%), 2 as seconds
			title = 'Realtime Visitor',
			cName = title.replace(/\s+/g,''),
			Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
			APIUrl = Protocol + host + '/api/api_ca.php?',
			PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
			HTML_NODATA = "<tr class='tra'><td class='tdmid'><p style='font-size:36px; color:#ddd; text-align:center; line-height:198px;'>No Data</p></td></tr>",
			that = this;
		
		timer = timer < 1 ? 0 : timer < 5000 ? 15000 : timer;
		
		//add tools
		var obj;
		if (obj = document.getElementById(box + '_R')) obj.href = 'javascript:' + box + '.run()'; 
	
	//********************************** Public Class Function Begin **********************************	
	this.run = function() {
		document.getElementById(box).innerHTML = 
		"<div id='" + cName + "' class='ca_line'>"+
			"<div id='" + cName + "Title' class='line_title'></div>"+
			"<div style='height:" + H + "px; width:auto; margin:10px;'>"+
				"<canvas id='" + cName + "RTLine' style='height:100%; width:100%'></canvas>"+
			"</div>"+
		"</div>";
		//run
		this.wGet(PassportUrl,true,0);
		if (timer > 0 && hTimer === 0) hTimer = setInterval(Update, timer);
	};
			
	this.callAjax = function(pass,flag){//flag = 0 is ini, flag = 1 is update
		var d=[];
		for(var k in this){
			d.push(k + "=" + this[k]);
			if (k == 'tz') break;
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
					LineData = [];
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v !== ''){
						LineData = eval(v);
					} else {
						document.getElementById(cName).innerHTML = HTML_NODATA;
						return;
					}
					if (flag) {
						updateLine(LineData);
					} else {
						draw();
					}
				}
			}
			
			myAjax.open("GET", url, true);
			myAjax.send();
		} catch (e) {	//alert(e.name + ": " + e.message);
			alert('XMLHttpRequest Error - RTLine Chart');
		}
	};

	this.wGet = function(a,b,flag){// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get"),
		var c,v="";
		a += "&rnd=" + Math.random();
		try{
			if (window.XMLHttpRequest) {
				c=new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c=new ActiveXObject("Microsoft.XMLHTTP");// code for IE6, IE5
			}
			c.open("GET",a,b);
			c.send(); 
			c.onreadystatechange=function(){
				if (c.readyState == 4 && c.status == 200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) that.callAjax(v,flag);
				}
			}
		} catch (e) {
			console.log("wGet Request Failed - RTLine Chart");
		}
	};

	this.resize = function() {
		try {
			if (LineData.length > 0) draw();
		} catch (z) {
			return;
		}
	};

	//*********************************** Public Class Function End ***********************************	

	// draw table
	function draw(){
		lineChartData = iniData(LineData);
		if (iLine) iLine.destroy();
		obj = document.getElementById(cName + "RTLine").getContext("2d");
		iLine = new Chart(obj).Line(lineChartData, {
			multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>",
			tooltipTemplate: "<%if (label){%><%=label%> - <%=datasetLabel%>: <%}%><%= value %>",
			bezierCurve : false,
			scaleBeginAtZero: true,
			scaleOverride: false,
			scaleSteps: 4,
			scaleStepWidth: 1,
			scaleStartValue: 0,
			datasetStrokeWidth : 1,
			animationSteps : 60,
			datasetFill : false,
			responsive: true,
			maintainAspectRatio: false
		});
	}


	function iniData(Data){//a means data array length, b means datasets length
		
			var a = 15;//Data.length - 1, // a is data length,
			b = Data[1].length - 1, // b is datasets length
			iniLabArr = iniArray(a, 0, 0, Data),
			iniColorArr = [["rgba(255,150,50,0.2)","rgba(255,150,50,1)"],["rgba(50,150,255,0.2)","rgba(50,150,255,1)"],["rgba(255,50,50,0.2)","rgba(255,50,50,1)"],["rgba(50,200,150,0.2)","rgba(50,200,150,1)"],["rgba(150,50,200,0.2)","rgba(150,50,200,1)"],["rgba(150,150,150,0.2)","rgba(150,150,150,1)"]],
			iniDatasetArr = iniDatasets(a,b,Data);
			
			document.getElementById(cName + 'Title').innerHTML = iniTitle(b,Data);
			
			return {
			labels : iniLabArr,
			datasets : iniDatasetArr
			};
			//
			function iniArray(a, b, c, Data) {//a is data length,b is datasets langth, c is flag: 0 as label, 1 as data
				var ret = [],
				d;
				for (var i = 1; i <= a; i++) {//a = 15
					d = (c == 0) ? Data[i][0] : Data[i][b + 1];
					ret.push(d);
				}
				return ret;
			}
			//
			function iniDatasets(a,b,Data) {
				var ret = [];
				if (b > 6) b = 6;//max array length is 6
				b--;
				for (var i = b; i > -1; i--) {
					ret.push({
					label: Lan[Data[0][i]],
					fillColor : iniColorArr[i][0],
					strokeColor : iniColorArr[i][1],
					pointColor : iniColorArr[i][1],
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : iniColorArr[i][1],
					data : iniArray(a,i,1,Data)});
				}
				return ret;
			}
			//
			function iniTitle(a, Data) {
				var ret = '';
				if (a > 6) a = 6;//max array length is 6
				for (var i = 0; i < a; i++) {
					ret += '<span style ="color:' + iniColorArr[i][1] + '; padding-right:10px;">&ndash; ' + Lan[Data[0][i]] + '</span>';
				}
				return ret;
			}
	}//end iniData
	
	function updateLine(Data){

		//iLine.options.animation = false;
		iLine.options.animationSteps = 20;
		var a = 15;//Data.length - 1, // a is data length,
		b = Data[1].length - 1; // b is datasets length
		for (var i = 0; i < a; i++){
			iLine.updateLabel(i,Data[i+1][0]);
			iLine.datasets[0].points[i].label = Data[i+1][0];
			for (var n = b; n > 0; n--) {
				iLine.datasets[n-1].points[i].value = Data[i+1][b-n+1];
			}
		}
		iLine.update();
	}
	
	
	function Update(){
		if (document.hidden) return;
		that.wGet(PassportUrl,true,1);
	}

}//end API function

