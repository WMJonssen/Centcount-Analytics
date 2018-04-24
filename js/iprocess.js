/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics IPROCESS INFORMATION JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/24/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function IPROCESS(uid, r, timer, host, box, Lan, Extra) {

	this.uid = uid;
	this.q = 'process info';
	
	//window.Charts = [];
		
	var AjaxData = [],
		ErrorData = [],
		Charts = [],
		PIDs = 0,
		PIDArr = [],
		Enabled = 1,
		hTimer = 0,
		Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
		APIUrl = Protocol + host + '/command.php?',
		PassportUrl = Protocol + document.location.host + '/passport.php?l=4&uid=' + uid + '&r=' + r,
		RowTotal = 0,
		RowStart = 0,
		RowSet = 20,
		PageTotal = 0,
		PageCurrent = 0,
		that = this;
	
	timer = timer < 1 ? 0 : timer < 1000 ? 1000 : timer;

	//********************************** Public Class Function Begin **********************************	
	
	this.create = function(x) {
		try {
			this.wGet(PassportUrl, true, 0); 
			if (timer > 0 && hTimer === 0) hTimer = setInterval(RefreshChart, timer);
		} catch(z) {
			console.log('iProcess create error!' + z.name + ': ' + z.message);
		}
	};
	
	
	this.callAjax = function(visa, flag) {//flag = 0 is ini, flag = 1 is update
		try {
			var d = [];
			for (var k in this) {
				d.push(k + "=" + this[k]);
				if (k == 'q') break;
			}
			var url = APIUrl + visa + d.join('&'), 
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
						return;
					}
					
					if (flag) {//update
						UpdateChart(AjaxData);
					} else {
						CreateChart(AjaxData);
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {	//console.log(e.name + ': ' + e.message);
			console.log('XMLHttpRequest Error - iProcess');
		}
	};
	
	
	this.wGet = function(a, b, flag) {// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get"), return Visa string
		try {
			var c, v = '';
			a += '&rnd=' + Math.random();

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
			console.log('wGet Request Failed - iProcess');
		}
	};
	
	this.pSet = function(pid, opt) {// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get"), return Visa string
		try {
			if (opt >= 90 && confirm('Are you sure?') == false) return;

			var c, 
				v = '',
				url = PassportUrl + '&rnd=' + Math.random();
		
			if (window.XMLHttpRequest) {
				c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c = new ActiveXObject('Microsoft.XMLHTTP');// code for IE6, IE5
			}
			c.open('GET', url, true);
			c.send(); 
			c.onreadystatechange = function() {
				if (c.readyState == 4 && c.status == 200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) that.setProcess(v,pid,opt);
				}
			}
		} catch (e) {
			console.log('pSet Request Failed - iProcess');
		}
	};
	
	
	this.setProcess = function(visa, pid, opt) {//flag = 0 is ini, flag = 1 is update
		try {
			showInputBox(2, '');
			var url = APIUrl + visa + 'uid=' + uid + '&q=set process&pid=' + pid + '&opt=' + opt, 
				myAjax;
			
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '')
					if (v) {
						Enabled = -1;
						showInputBox(1, v);
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {//console.log(e.name + ': ' + e.message);
			console.log('XMLHttpRequest Error - iProcess');
		}
	};

	this.gErr = function(opt, start) {// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get"), return Visa string
		try {

			if (opt > 100 && confirm("Are you sure to delete error log?") == false) return;

			var c, 
				v = '',
				url = PassportUrl + '&rnd=' + Math.random();
		
			if (window.XMLHttpRequest) {
				c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c = new ActiveXObject('Microsoft.XMLHTTP');// code for IE6, IE5
			}
			c.open('GET', url, true);
			c.send(); 
			c.onreadystatechange = function() {
				if (c.readyState == 4 && c.status == 200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) {
						switch (start) {
						case 0://first page
							RowStart = 0;
							break;
						case 1://previous page
							RowStart -= RowSet;
							if (RowStart < 0) RowStart = 0;
							break;
						case 2://next page
							RowStart += RowSet;
							break;
						case 3://last page
							RowStart = -1;
							break;
						}
						that.getError(v, opt, RowStart);
					}
				}
			}
		} catch (e) {
			console.log('gErr Request Failed - iProcess');
		}
	};

	this.getError = function(visa, opt, start) {//flag = 0 is ini, flag = 1 is update
		try {
			showInputBox(2, '');
			var url = APIUrl + visa + 'uid=' + uid + '&q=error&opt=' + opt + '&start=' + start, 
				myAjax;
			
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					showInputBox(0);
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '')
					if (v !== '') {
						if (opt > 100) {
							showInputBox(1, v);
						} else {
							ErrorData = eval(v);
							var LEN = ErrorData.length;

							if (LEN > 1) {
								RowTotal = ErrorData[LEN-1][0];
								RowStart = ErrorData[LEN-1][1];
								PageTotal = Math.ceil(RowTotal / RowSet);
								PageCurrent = Math.ceil(RowStart / RowSet) + 1;
								var ErrMsg = '', ErrBtn = '';
								for (var i=0; i<(LEN-1); i++) {
									ErrMsg += (i + 1 + RowStart) + ') ' + unescape(ErrorData[i]) + '<br><br>';
								}

								//add button
								ErrBtn += (RowStart > 0 ? '<a class="errA" alt="first page" title="first page" href="javascript:'+box+'.gErr('+opt+', 0)">«</a>' : '<span class="errSpan">«</span>');
								ErrBtn += (RowStart > 0 ? '<a class="errA" alt="next page" title="next page" href="javascript:'+box+'.gErr('+opt+', 1)">‹</a>' : '<span class="errSpan">‹</span>');
								ErrBtn += '<a class="errA" style="width:auto; padding-left:5px; padding-right:5px; font-size:14px; line-height:24px;" alt="set page" title="set page" href="javascript:'+box+'.gErr('+opt+', 0)">' + PageCurrent + '/' + PageTotal + '</a>';
								ErrBtn += (RowTotal > (RowStart + RowSet) ? '<a class="errA" alt="previous page" title="previous page" href="javascript:'+box+'.gErr('+opt+', 2)">›</a>' : '<span class="errSpan">›</span>');
								ErrBtn += (RowTotal > (RowStart + RowSet) ? '<a class="errA" alt="last page" title="last page" href="javascript:'+box+'.gErr('+opt+', 3)">»</a>' : '<span class="errSpan">»</span>');
								ErrBtn += '<a class="errA" alt="delete error log" title="delete error log" href="javascript:'+box+'.gErr('+(100 + opt)+', 0)">x</a>';
								
								//popup msgbox
								showInputBox(3, ErrMsg, ErrBtn);
							}
						}
					} else {
						return;
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {//console.log(e.name + ': ' + e.message);
			console.log('XMLHttpRequest Error - iProcess');
		}
	};
	
	//********************************** Public Class Function End **********************************
	
	
	
	//*********************************** Common Fuction Begin **********************************
	
	function CreateChart(x) {
		try {
			var len = x.length + 0;
			if (len < 1) return;
			PIDs = len - 3;
			//PushProcess();
			Charts = [];
			//build chart
			BuildSummary();
			if (len > 3) for (var i = 1; i < (len-2); i++) BuildPie(x[i], i);
			//BuildTop20();
			BuildPerformance();
			BuildOptions();
			//draw data
			//UpdateTop20();
			UpdatePerformance();
			if (len > 2) for (var i = 0; i < (len-2); i++) DrawPie(x[i], i);
		} catch(z) {
			console.log('iProcess create charts error!' + z.name + ': ' + z.message);
		}
	}
	
	function UpdateChart(x) {
		try {
			var len = x.length + 0;
			if (len < 1) return;
			CheckProcess(len - 3);
			//update data
			UpdateOptions();
			//UpdateTop20();
			UpdatePerformance();
			if (len > 2) for (var i = 0; i < (len-2); i++) UpdatePie(x[i], i);
		} catch(z) {
			console.log('iProcess update charts error!' + z.name + ': ' + z.message);
		}
	}
	
	function RefreshChart() {
		if (document.hidden) return;
		that.wGet(PassportUrl,true,1); 
	}
	
	function CheckProcess(l) {
		try {
			if (l != PIDs) {
				CreateChart(AjaxData);
				return;
			}
		} catch(z) {
			CreateChart(AjaxData);
		}
	}
	
	function PushProcess() {
		try {
			PIDArr = [];
			var len = AjaxData.length - 3;
			if (len > 0) for (var i = 1; i <= len; i++) {
				PIDArr[AjaxData[i][0]] = 1;
			}
		} catch(z) {
			console.log('iProcess push process error!' + z.name + ': ' + z.message);
		}
	}
	
	//************************************ Common Fuction End ************************************
	
	//************************************* Modular Pie Begin *************************************
	
	function DropdownList(opt,x,len,id,title) {
		var textHtml = "<div class='selectbtn'>";
		textHtml += "<ul><li><a id='" + id + "' class='select' onclick='selectButton(this, 2)'>" + title + x + "</a><ul>";
		for (var i = 1; i < len; i++) {
			textHtml += "<li><a onclick='" + box + ".pSet("+i+","+opt+")'>"+i+"</a></li>";
		}
		textHtml += "</ul></li></ul></div>";
		return textHtml;
	}
	
	function BuildSummary() {
		try {
			document.getElementById(box).innerHTML =
			"<div class='ctrl-frame'>"+
				"<p>SUMMARY</p>"+
				"<div class='canvas-box'>"+
					"<div id='T_0' class='canvas-tips'>0</div>"+
					"<canvas id='P_0' width='200px' height='200px'></canvas>"+
				"</div>"+
				"<div id='D_0' class='detail'>"+
					"PID: <br/>Peak: 100<br/>Total Processed: 0<br/>Avg Consume: 0 ms"+
				"</div>"+
			"</div>"
		} catch(z) {
			console.log('iProcess build summary error!' + z.name + ': ' + z.message);
		}
	}
/*
	function BuildTop20() {
		try {
			document.getElementById(box).innerHTML +=
			"<div class='ctrl-frame'>"+
				"<p>TOP 20 SITES</p>"+
				"<div class='canvas-box'>"+
					"<div id='TopList' class='top-box'></div>"+
				"</div>"+
			"</div>"
		} catch(z) {
			console.log('iProcess build top 20 list error!' + z.name + ': ' + z.message);
		}
	}

	function UpdateTop20() {
		try {
			var len = AjaxData.length - 2;
			if (len > 0) {
				var html = '';
				//'<a class="lnk" href="manager.php?id=' + k.substr(0, 13) + '&siteid='+ k +' target="_blank">' + k +'</a>'
				for (var k in AjaxData[len]) html += ('<a class="lnk" href="manager.php?id=' + k.substr(0, 13) + '&siteid='+ k +'" target="_blank">' + k +'</a>: ' + AjaxData[len][k] + '<br/>');
				if (html) document.getElementById('TopList').innerHTML = html + '';
			}
		} catch(z) {
			console.log('iProcess update top 20 error!' + z.name + ': ' + z.message);
		}
	}
*/
	function BuildPerformance() {
		try {
			document.getElementById(box).innerHTML +=
			"<div class='ctrl-frame'>"+
				"<p>PERFORMANCE</p>"+
				"<div class='canvas-box'>"+
					"<div id='PerformanceList' class='top-box'></div>"+
				"</div>"+
			"</div>"
		} catch(z) {
			console.log('iProcess build performance list error!' + z.name + ': ' + z.message);
		}
	}

	function UpdatePerformance() {
		try {
			var len = AjaxData.length - 1;
			if (len > 0) {
				var html = '',
					n = AjaxData[len].length - 5;
				//js performance
				html += ('JS: ' + parseInt(AjaxData[len][1]/AjaxData[len][0]) / 1000 + ' ms (' + AjaxData[len][0] + ')<br/>');
				//request performance
				for (var i=2; i<n; i+=2) {
					html += ((i/2 - 1) + ': ' + parseInt(AjaxData[len][i+1]/AjaxData[len][i]) / 1000 + ' ms (' + AjaxData[len][i] + ')<br/>');
				}
				//missed CA, VA, VC, VID, IND RECORD COUNT
				html += ('<br/>Total Memory: ' + parseInt(AjaxData[len][n]) + ' MB');
				html += ('<br/>Free Memory: ' + parseInt(AjaxData[len][n+2]) + ' MB');
				html += ('<br/>Buffer/Cache: ' + parseInt(AjaxData[len][n] - AjaxData[len][n+1] - AjaxData[len][n+2]) + ' MB');
				html += ('<br/>Total Disk: ' + parseInt(AjaxData[len][n+3]) + ' GB');
				html += ('<br/>Free Disk: ' + parseInt(AjaxData[len][n+3] - AjaxData[len][n+4]) + ' GB');

				if (html) document.getElementById('PerformanceList').innerHTML = html;
			}
		} catch(z) {
			console.log('iProcess update performance error!' + z.name + ': ' + z.message);
		}
	}
	
	function BuildOptions() {
		try {
			Enabled = AjaxData[0][8];
			document.getElementById(box).innerHTML +=
			"<div class='ctrl-frame'>"+
				"<p>OPTIONS</p>"+
				"<div class='canvas-box'>"+
					"<div id='opt' class='option-box'>"+
						DropdownList(4,AjaxData[0][6],129,'MaxP','Max Processes: ')+
						DropdownList(5,AjaxData[0][7],129,'MinP','Min Processes: ')+
						"<a href='javascript:"+box+".pSet(0, 90)' class='wbtn'>CLEAN ALL LOG</a>" +
						"<a href='javascript:"+box+".pSet(0, 91)' class='wbtn'>CLEAN SETTING CACHE</a>" +
						"<a href='javascript:"+box+".pSet(0, 99)' class='wbtn'>CLEAN TODAY DATA</a>" +
						(AjaxData[0][8] ? "<span>START NEW PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 13)' class='wbtn'>START NEW PROCESS</a>")+
						"<a href='javascript:"+box+".pSet(0, " + (AjaxData[0][8] ? 15 : 14) + ")' "+(AjaxData[0][8] ? "class='redbtn'>ENABLE PROCESS" : "class='wbtn'>DISABLE PROCESS") + "</a>" + 
						"<a href='javascript:"+box+".pSet(0, " + (AjaxData[0][10] ? 21 : 20) + ")' "+(AjaxData[0][10] ? "class='redbtn'>ENABLE STORAGE" : "class='wbtn'>DISABLE STORAGE") + "</a>" + 
						//(AjaxData[0][8] ? "<span>PAUSE ALL PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 12)' class='wbtn'>PAUSE ALL PROCESS</a>")+
						//(AjaxData[0][8] ? "<span>RESUME ALL PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 11)' class='wbtn'>RESUME ALL PROCESS</a>")+
						//(AjaxData[0][8] ? "<span>STOP ALL PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 10)' class='wbtn'>STOP ALL PROCESS</a>")+
						"<a href='javascript:"+box+".pSet(0, " + (AjaxData[0][8] && AjaxData[0][10] ? 31 : 30) + ")' "+(AjaxData[0][8] && AjaxData[0][10] ? "class='redbtn'>ENABLE THIS HOST" : "class='wbtn'>DISABLE THIS HOST") + "</a>" + 
					"</div>"+
				"</div>"+
			"</div>"
		} catch(z) {
			console.log('iProcess build options error!' + z.name + ': ' + z.message);
		}
	}
	
	function UpdateOptions() {
		try {
			if (Enabled != AjaxData[0][8]) {
				document.getElementById('opt').innerHTML = 
					DropdownList(4,AjaxData[0][6],129,'MaxP','Max Processes: ')+
					DropdownList(5,AjaxData[0][7],129,'MinP','Min Processes: ')+
					"<a href='javascript:"+box+".pSet(0, 90)' class='wbtn'>CLEAN ALL LOG</a>" +
					"<a href='javascript:"+box+".pSet(0, 91)' class='wbtn'>CLEAN SETTING CACHE</a>" +
					"<a href='javascript:"+box+".pSet(0, 99)' class='wbtn'>CLEAN TODAY DATA</a>" +
					(AjaxData[0][8] ? "<span>START NEW PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 13)' class='wbtn'>START NEW PROCESS</a>")+
					"<a href='javascript:"+box+".pSet(0, " + (AjaxData[0][8] ? 15 : 14) + ")' "+(AjaxData[0][8] ? "class='redbtn'>ENABLE PROCESS" : "class='wbtn'>DISABLE PROCESS") + "</a>" + 
					"<a href='javascript:"+box+".pSet(0, " + (AjaxData[0][10] ? 21 : 20) + ")' "+(AjaxData[0][10] ? "class='redbtn'>ENABLE STORAGE" : "class='wbtn'>DISABLE STORAGE") + "</a>" + 
					//(AjaxData[0][8] ? "<span>PAUSE ALL PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 12)' class='wbtn'>PAUSE ALL PROCESS</a>")+
					//(AjaxData[0][8] ? "<span>RESUME ALL PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 11)' class='wbtn'>RESUME ALL PROCESS</a>")+
					//(AjaxData[0][8] ? "<span>STOP ALL PROCESS</span>" : "<a href='javascript:"+box+".pSet(0, 10)' class='wbtn'>STOP ALL PROCESS</a>")+
					"<a href='javascript:"+box+".pSet(0, " + (AjaxData[0][8] && AjaxData[0][10] ? 31 : 30) + ")' "+(AjaxData[0][8] && AjaxData[0][10] ? "class='redbtn'>ENABLE THIS HOST" : "class='wbtn'>DISABLE THIS HOST") + "</a>"

				Enabled = AjaxData[0][8];
			}
		} catch(z) {
			console.log('iProcess update options error!' + z.name + ': ' + z.message);
		}
	}
	
	function BuildPie(x, No) {
		try {
			document.getElementById(box).innerHTML +=
			"<div class='ctrl-frame'>"+
				"<p>PROCESS: "+No+"</p>"+
				"<div class='canvas-box'>"+
					"<div id='T_"+No+"' class='canvas-tips'>0</div>"+
					"<canvas id='P_"+No+"' width='200px' height='200px'></canvas>"+
				"</div>"+
				"<div id='D_"+No+"' class='detail'>"+
					"PID: <br/>Peak: 100<br/>Processed: 0<br/>Avg Consume: 0 ms"+
				"</div>"+
				"<div id='C_"+No+"' class='ctrl-box'>"+
					"<a href='javascript:"+box+".pSet(\""+x[0]+"\", 1)' class='btn'>RUN</a>"+
					"<a href='javascript:"+box+".pSet(\""+x[0]+"\", 0)' class='btn'>STOP</a>"+
					"<a href='javascript:"+box+".pSet(\""+x[0]+"\", 2)' class='lbtn'>PAUSE</a>"+
				"</div>"+
			"</div>"
			
		} catch(z) {
			console.log('iProcess build error!' + z.name + ': ' + z.message);
		}
	}
/*	
//Process Status & Control Code: 0->to terminate process, 1->process is running, 2->process paused, 10->stop all process, 11->resume all process, 12->pause all process, 13->start new process
//Process Structure[1]: 0->$PID, 1->$PROCESS_STATUS, 2->$PROCESS_START_TIME, 3->$PROCESS_LAST_RESPONSE, 4->$PROCESS_COUNT, 5->$PROCESS_CONSUME, 6->$PROCESS_CURRENT, 7->$PROCESS_PEAK
//Process Structure[0]: 0->$Requests, 1->$TotalProcessed, 2->$TotalConsume, 3->$FatalErrors, 4->$Reserved, 5->$ExecuteFailures, 6->$PROCESS_MAX, 7->$PROCESS_MIN, 8->$PROCESS_LIMIT	
*/
	function DrawPie(x, No) {
		try {
			var canvas = document.getElementById('P_' + No);	
				
			if (No == 0) {
				
				var len =  AjaxData.length - 2;
				if (len < 1) return;
				var	AvgC=0,CurP=0,PeakP=0,FreeP=0,TotalP=0,pColor=0,MemU=0;
				if (len > 1) for (var i = 1; i < len; i++) {
					CurP += parseInt(AjaxData[i][6]);
					if (AjaxData[i][1] == 1) pColor = 1;
					//MemU += parseInt(AjaxData[i][10]);
				}
				pColor = pColor ? "#46BFBD" : "#aaa";
				AvgC =  parseInt(AjaxData[0][1]) ? parseFloat((AjaxData[0][2] / (AjaxData[0][1] * 1000)).toFixed(2)) : 0,
				PeakP = AvgC ? Math.ceil(1000 / AvgC) : 100;
				FreeP = PeakP > CurP ? PeakP - CurP : 100;
				//MemU = MemU < 1048576 ? (MemU / 1024).toFixed(2) + ' KB' : (MemU / 1048576).toFixed(2) + ' MB';

				document.getElementById('T_' + No).innerHTML = CurP;//tips
				document.getElementById('D_' + No).innerHTML = 'Request Queues: ' + AjaxData[0][0] + '<br/>Total Processed: ' + AjaxData[0][1] + '<br/>Avg Consume: ' + AvgC + ' ms<br/>Kernel: ' + AjaxData[0][9] + '<br/>Storage: ' + AjaxData[0][11] + '<br/>Fatal Errors: ' + (AjaxData[0][3] > 0 ? '<a href="javascript:'+box+'.gErr(2, 0)">' + AjaxData[0][3] + '</a>' : AjaxData[0][3]) + '<br/>Execute Failures: ' + (AjaxData[0][5] > 0 ? '<a href="javascript:'+box+'.gErr(1, 0)">' + AjaxData[0][5] + '</a>' : AjaxData[0][5]) + '<br/>Bad Requests: ' + (AjaxData[0][4] > 0 ? '<a href="javascript:'+box+'.gErr(3, 0)">' + AjaxData[0][4] + '</a>' : AjaxData[0][4]);//details  '<br/>Kernel Memory: ' + MemU +
				
			} else {
				
				var pColor = x[1] == 1 ? "#46BFBD" : "#aaa";
				var AvgC = parseInt(x[4]) ? (x[5] / (x[4] * 1000)) : 0;
				if (AvgC) AvgC = (AvgC > 10) ? parseFloat(AvgC.toFixed(1)) : parseFloat(AvgC.toFixed(2));
				var AvgP = AvgC ? Math.ceil(1000 / AvgC) : 100,
					CurP = parseInt(x[6]),
					FreeP = AvgP > CurP ? AvgP - CurP : 100,
					MemU = parseInt(x[10]);
					MemU = MemU < 1048576 ? (MemU / 1024).toFixed(2) + ' KB' : (MemU / 1048576).toFixed(2) + ' MB';
					
				document.getElementById('T_' + No).innerHTML = CurP;//tips
				document.getElementById('D_' + No).innerHTML = "Status: " + (x[1] == 1 ? 'Running' : 'Paused') + '<br/>Memory: ' + MemU + '<br/>Processed: ' + x[4] + '<br/>Peak: ' + x[7] + '&ensp;&ensp;Avg: ' + AvgP + '<br/>Consume: [Avg, Max, Min]<br/>' + AvgC + '&ensp;&ensp;' + (x[8] > 1E4 ? (x[8] * 0.001).toFixed(1) : (x[8] * 0.001).toFixed(2)) + '&ensp;&ensp;' + (x[9] > 1E4 ? (x[9] * 0.001).toFixed(1) : (x[9] * 0.001).toFixed(2)) + ' ms';//details
				
			}
			
	
			var Data = [
					{
						value: CurP,
						color: "#F7464A",
						highlight: "#FF5A5E",
						label: "Current"
					},
					{
						value: FreeP,
						color: pColor,
						highlight: "#5AD3D1",
						label: "Free"
					}];
			//Chart.defaults.global.responsive = true;
			if (Charts[No]) Charts[No].destroy();
			Charts[No] = new Chart(canvas.getContext('2d')).Pie(Data, {
				animateRotate : true,
				animationSteps : 10,
				animationEasing : "easeInQuad",
				animateScale : false,
				segmentStrokeWidth : 0,
				percentageInnerCutout : 75,
				showTooltips: false,
				responsive : true
			});
			
		} catch(z) {
			console.log('iProcess draw pie error!' + z.name + ': ' + z.message);
		}
			
	}
	
	
	function UpdatePie(x, No) {
		try {
			if (Charts.length == 0) return;
			
			if (No == 0) {
				
				var len =  AjaxData.length - 2;
				if (len < 1) return;
				var	AvgC=0,CurP=0,PeakP=0,FreeP=0,pColor=0,MemU=0;
				if (len > 1) for (var i = 1; i < len; i++) {
					CurP += parseInt(AjaxData[i][6]);
					if (AjaxData[i][1] == 1) pColor = 1;
					//MemU += parseInt(AjaxData[i][10]);
				}
				pColor = pColor ? "#46BFBD" : "#aaa";
				AvgC =  parseInt(AjaxData[0][1]) ? parseFloat((AjaxData[0][2] / (AjaxData[0][1] * 1000)).toFixed(2)) : 0,
				PeakP = AvgC ? Math.ceil(1000 / AvgC) : 100;
				FreeP = PeakP > CurP ? PeakP - CurP : 100;
				//MemU = MemU < 1048576 ? (MemU / 1024).toFixed(2) + ' KB' : (MemU / 1048576).toFixed(2) + ' MB';
				
				document.getElementById('T_' + No).innerHTML = CurP;//tips
				document.getElementById('D_' + No).innerHTML = 'Request Queues: ' + AjaxData[0][0] + '<br/>Total Processed: ' + AjaxData[0][1] + '<br/>Avg Consume: ' + AvgC + ' ms<br/>Kernel: ' + AjaxData[0][9] + '<br/>Storage: ' + AjaxData[0][11] + '<br/>Fatal Errors: ' + (AjaxData[0][3] > 0 ? '<a href="javascript:'+box+'.gErr(2, 0)">' + AjaxData[0][3] + '</a>' : AjaxData[0][3]) + '<br/>Execute Failures: ' + (AjaxData[0][5] > 0 ? '<a href="javascript:'+box+'.gErr(1, 0)">' + AjaxData[0][5] + '</a>' : AjaxData[0][5]) + '<br/>Bad Requests: ' + (AjaxData[0][4] > 0 ? '<a href="javascript:'+box+'.gErr(3, 0)">' + AjaxData[0][4] + '</a>' : AjaxData[0][4]);//details '<br/>Kernel Memory: ' + MemU + 
				
			} else {
				
				var pColor = x[1] == 1 ? "#46BFBD" : "#aaa";
				var AvgC = parseInt(x[4]) ? (x[5] / (x[4] * 1000)) : 0;
				if (AvgC) AvgC = (AvgC > 10) ? parseFloat(AvgC.toFixed(1)) : parseFloat(AvgC.toFixed(2));
				var AvgP = AvgC ? Math.ceil(1000 / AvgC) : 100,
					CurP = parseInt(x[6]),
					FreeP = AvgP > CurP ? AvgP - CurP : 100,
					MemU = parseInt(x[10]);
					MemU = MemU < 1048576 ? (MemU / 1024).toFixed(2) + ' KB' : (MemU / 1048576).toFixed(2) + ' MB';
					
				document.getElementById('T_' + No).innerHTML = CurP;//tips
				document.getElementById('D_' + No).innerHTML = "Status: " + (x[1] == 1 ? 'Running' : 'Paused') + '<br/>Memory: ' + MemU + '<br/>Processed: ' + x[4] + '<br/>Peak: ' + x[7] + '&ensp;&ensp;Avg: ' + AvgP + '<br/>Consume: [Avg, Max, Min]<br/>' + AvgC + '&ensp;&ensp;' + (x[8] > 1E4 ? (x[8] * 0.001).toFixed(1) : (x[8] * 0.001).toFixed(2)) + '&ensp;&ensp;' + (x[9] > 1E4 ? (x[9] * 0.001).toFixed(1) : (x[9] * 0.001).toFixed(2)) + ' ms';//details
				
			}
			
			Charts[No].segments[0]['value'] = CurP;//current value
			Charts[No].segments[1]['value'] = FreeP;//peak value
			Charts[No].segments[1]['color'] = pColor;//peak color
			Charts[No].segments[1]['fillColor'] = pColor;//peak color
			
			Charts[No].update();

		} catch(z) {
			console.log('iProcess update pie error!' + z.name + ': ' + z.message);
		}
	
	}
	
	//************************************* Modular Pie End *************************************

		
}//End IPROCESS Function












function ISUMMARY(uid, r, timer, host, hostname, box, Lan, Extra) {

	this.uid = uid;
	this.q = 'process sum';
		
	var AjaxData = [],
		Charts,
		Enabled = 1,
		hTimer = 0,
		Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
		APIUrl = Protocol + host + '/command.php?',
		PassportUrl = Protocol + document.location.host + '/passport.php?l=4&uid=' + uid + '&r=' + r,
		that = this;
	
	timer = timer < 1 ? 0 : timer < 1000 ? 1000 : timer;

	//********************************** Public Class Function Begin **********************************	
	
	this.create = function(x) {
		try {
			BuildSummary();
			this.wGet(PassportUrl, true, 0); 
			if (timer > 0 && hTimer === 0) hTimer = setInterval(RefreshChart, timer);
		} catch(z) {
			console.log('iSummary create error!' + z.name + ': ' + z.message);
		}
	};
	
	
	this.callAjax = function(pass, flag) {//flag = 0 is ini, flag = 1 is update
		try {
			var d = [];
			for (var k in this) {
				d.push(k + "=" + this[k]);
				if (k == 'q') break;
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
						return;
					}
					
					if (flag) {//update
						UpdateChart(AjaxData);
					} else {
						CreateChart(AjaxData);
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {	//console.log(e.name + ': ' + e.message);
			console.log('XMLHttpRequest Error - iSummary');
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
			console.log('wGet Request Failed - iSummary');
		}
	};
	
	this.pSet = function(pid, opt) {// a:url, b:method(true[asynchronous] or false[Synchronize]), x:method("Post" or "Get")
		var c, 
			v = '',
			url = PassportUrl + '&rnd=' + Math.random();
		try {
			if (window.XMLHttpRequest) {
				c = new XMLHttpRequest();// code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				c = new ActiveXObject('Microsoft.XMLHTTP');// code for IE6, IE5
			}
			c.open('GET', url, true);
			c.send(); 
			c.onreadystatechange = function() {
				if (c.readyState == 4 && c.status == 200) {
					v = c.responseText.replace(/(^\s*)|(\s*$)/g, '');
					if (v) that.setProcess(v,pid,opt);
				}
			}
		} catch (e) {
			console.log('wGet Request Failed - iSummary');
		}
	};
	
	
	this.setProcess = function(pass, pid, opt) {//flag = 0 is ini, flag = 1 is update
		try {
			showInputBox(2, '');
			
			var url = APIUrl + pass + 'uid=' + uid + '&q=set process&pid=' + pid + '&opt=' + opt, 
				myAjax;
			
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '')
					if (v) {
						switch (opt) {
						case 4://set max process
							var ret = v.substr(0,1);
							showInputBox(1, v.substr(1));
							if (ret === '1') document.getElementById('MaxP').innerHTML = 'Max Processes: ' + pid;
							break;
						case 5:// set min process
							var ret = v.substr(0,1);
							showInputBox(1, v.substr(1));
							if (ret === '1') document.getElementById('MinP').innerHTML = 'Min Processes: ' + pid;
							break;
						default:
							showInputBox(1, v);
							break;
						}
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {//console.log(e.name + ': ' + e.message);
			console.log('XMLHttpRequest Error - iSummary');
		}
	};
	
	//********************************** Public Class Function End **********************************
	
	
	
	//*********************************** Common Fuction Begin **********************************

	function CreateChart(x) {
		try {
			var len = x.length + 0;
			if (len < 1) return;
			DrawPie(x[0]);
		} catch(z) {
			console.log('iSummary create charts error!' + z.name + ': ' + z.message);
		}
	}
	
	function UpdateChart(x) {
		try {
			var len = x.length + 0;
			if (len < 1) return;
			UpdatePie(x[0]);
		} catch(z) {
			console.log('iSummary update charts error!' + z.name + ': ' + z.message);
		}
	}
	
	function RefreshChart() {
		if (document.hidden) return;
		that.wGet(PassportUrl,true,1); 
	}
	
	
	//************************************ Common Fuction End ************************************
	
	//************************************* Modular Pie Begin *************************************
	
	function BuildSummary() {
		try {
			var newNode = document.createElement("div"),
				Url = document.location.href;

			newNode.innerHTML =
			"<div class='ctrl-frame'>"+
				"<p>" + hostname + "</p>"+
				"<div class='canvas-box'>"+
					"<div id='T_" + host + "' class='canvas-tips'>0</div>"+
					"<canvas id='P_" + host + "' width='200px' height='200px'></canvas>"+
				"</div>"+
				"<div id='D_" + host + "' class='detail'>"+
					"PID: <br/>Peak: 100<br/>Total Processed: 0<br/>Avg Consume: 0 ms"+
				"</div>"+
				"<div id='C_" + host + "' class='ctrl-box'>"+
					"<a href='javascript:" + box + "[" + Extra + "].pSet(0, 31)' class='btn'>ENABLE</a>"+
					"<a href='javascript:" + box + "[" + Extra + "].pSet(0, 30)' class='btn'>DISABLE</a>"+
					"<a href='" + Url.substr(0, (Url.indexOf('&action=') + 8)) + "Host Status&param=" + host + "' class='lbtn'>MORE</a>"+
				"</div>"+
			"</div>"

			document.getElementById(box).appendChild(newNode);
		} catch(z) {
			console.log('iSummary build summary error!' + z.name + ': ' + z.message);
		}
	}
	
/*	
//Process Status & Control Code: 0->to terminate process, 1->process is running, 2->process paused, 10->stop all process, 11->resume all process, 12->pause all process, 13->start new process
//Process Structure[1]: 0->$PID, 1->$PROCESS_STATUS, 2->$PROCESS_START_TIME, 3->$PROCESS_LAST_RESPONSE, 4->$PROCESS_COUNT, 5->$PROCESS_CONSUME, 6->$PROCESS_CURRENT, 7->$PROCESS_PEAK
//Process Structure[0]: 0->$Requests, 1->$TotalProcessed, 2->$TotalConsume, 3->$FatalErrors, 4->$Reserved, 5->$ExecuteFailures, 6->$PROCESS_MAX, 7->$PROCESS_MIN, 8->$PROCESS_LIMIT	
*/
	function DrawPie(x) {
		try {
			var canvas = document.getElementById('P_' + host);	
				
			var len =  AjaxData.length;
			if (len < 1) return;
			var	AvgC=0,CurP=0,PeakP=0,FreeP=0,TotalP=0,pColor=0,MemU=0;
			if (len > 1) for (var i = 1; i < len; i++) {
				CurP += parseInt(AjaxData[i][6]);
				if (AjaxData[i][1] == 1) pColor = 1;
				MemU += parseInt(AjaxData[i][10]);
			}
			pColor = pColor ? "#46BFBD" : "#aaa";
			AvgC =  parseInt(AjaxData[0][1]) ? parseFloat((AjaxData[0][2] / (AjaxData[0][1] * 1000)).toFixed(2)) : 0,
			PeakP = AvgC ? Math.ceil(1000 / AvgC) : 100;
			FreeP = PeakP > CurP ? PeakP - CurP : 100;
			//MemU = MemU < 1048576 ? (MemU / 1024).toFixed(2) + ' KB' : (MemU / 1048576).toFixed(2) + ' MB';

			document.getElementById('T_' + host).innerHTML = CurP;//tips
			document.getElementById('D_' + host).innerHTML = 'Request Queues: ' + AjaxData[0][0] + '<br/>Total Processed: ' + AjaxData[0][1] + '<br/>Avg Consume: ' + AvgC + ' ms<br/>Kernel: ' + AjaxData[0][9] + '<br/>Storage: ' + AjaxData[0][11] + '<br/>Fatal Errors: ' + AjaxData[0][3] + '<br/>Execute Failures: ' + AjaxData[0][5] + '<br/>Bad Requests: ' + AjaxData[0][4];//details

	
			var Data = [
					{
						value: CurP,
						color: "#F7464A",
						highlight: "#FF5A5E",
						label: "Current"
					},
					{
						value: FreeP,
						color: pColor,
						highlight: "#5AD3D1",
						label: "Free"
					}];
			//Chart.defaults.global.responsive = true;
			if (Charts) Charts.destroy();
			Charts = new Chart(canvas.getContext('2d')).Pie(Data, {
				animateRotate : true,
				animationSteps : 10,
				animationEasing : "easeInQuad",
				animateScale : false,
				segmentStrokeWidth : 0,
				percentageInnerCutout : 75,
				showTooltips: false,
				responsive : true
			});
			
		} catch(z) {
			console.log('iSummary draw pie error!' + z.name + ': ' + z.message);
		}
			
	}
	
	
	function UpdatePie(x) {
		try {
			
			if (Charts.length == 0) return;
				
			var len =  AjaxData.length;
			if (len < 1) return;
			var	AvgC=0,CurP=0,PeakP=0,FreeP=0,pColor=0,MemU=0;
			if (len > 1) for (var i = 1; i < len; i++) {
				CurP += parseInt(AjaxData[i][6]);
				if (AjaxData[i][1] == 1) pColor = 1;
				MemU += parseInt(AjaxData[i][10]);
			}
			pColor = pColor ? "#46BFBD" : "#aaa";
			AvgC =  parseInt(AjaxData[0][1]) ? parseFloat((AjaxData[0][2] / (AjaxData[0][1] * 1000)).toFixed(2)) : 0,
			PeakP = AvgC ? Math.ceil(1000 / AvgC) : 100;
			FreeP = PeakP > CurP ? PeakP - CurP : 100;
			//MemU = MemU < 1048576 ? (MemU / 1024).toFixed(2) + ' KB' : (MemU / 1048576).toFixed(2) + ' MB';
				
			document.getElementById('T_' + host).innerHTML = CurP;//tips
			document.getElementById('D_' + host).innerHTML = 'Request Queues: ' + AjaxData[0][0] + '<br/>Total Processed: ' + AjaxData[0][1] + '<br/>Avg Consume: ' + AvgC + ' ms<br/>Kernel: ' + AjaxData[0][9] + '<br/>Storage: ' + AjaxData[0][11] + '<br/>Fatal Errors: ' + AjaxData[0][3] + '<br/>Execute Failures: ' + AjaxData[0][5] + '<br/>Bad Requests: ' + AjaxData[0][4];//details

	
			Charts.segments[0]['value'] = CurP;//current value
			Charts.segments[1]['value'] = FreeP;//peak value
			Charts.segments[1]['color'] = pColor;//peak color
			Charts.segments[1]['fillColor'] = pColor;//peak color
			
			Charts.update();

		} catch(z) {
			console.log('iSummary update pie error!' + z.name + ': ' + z.message);
		}
	
	}
	
	//************************************* Modular Pie End *************************************

		
}//End ISUMMARY Function