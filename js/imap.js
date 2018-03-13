/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free IMAP API JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function MAPAPI(sid, r, from, to, period, type, timezone, key, timer, cType, host, box, roaming, W, H, LanCode, Lan, Extra) {

	
	this.sid = sid;
	this.q = '';
	this.type = type;//CityMD5 name
	this.key = key;//1->visits, 2->UV, 3->Bounce Rate, 4->Avg Time On Website
	this.period = period;//0 as day, 1 as week, 2 as month, 3 as year, 4 as date range
	this.from = from;//start date//20151201;//
	this.to = to;//end date
	this.tz = timezone;
	this.start = 0;
	this.end = 1E5;
	this.sortorder = 0;//0 as DESC 降序, 1 as ASC 升序

	
	
	var AjaxData = [],
		iChart,
		option = {},
		hTimer = 0,
		cName = box + '-GEO',
		Titles = getTitle(key),
		title = Titles[0],
		Protocol = ('https:' == document.location.protocol) ? 'https://' : 'http://',
		APIUrl = Protocol + host + '/api/api_ca.php?',
		PassportUrl = Protocol + document.location.host + '/passport.php?l=1&sid=' + sid + '&r=' + r,
		//HTML_NODATA_MAP = "<div style='width:auto; float:none; font-size:36px; color:#ddd; text-align:center; line-height:270px;'>No Data</div>",
		that = this;
	
	timer = timer < 1 ? 0 : timer < 5000 ? 15000 : timer;

	var obj = document.getElementById(box);
	if (obj) {
		W = parseInt(obj.parentNode.offsetWidth - 15); 
		H = parseInt(W * 0.55); 
	}
	
	//add tools
	if (obj = document.getElementById(box + '_R')) obj.href = 'javascript:' + box + '.run(9)';

	if (obj = document.getElementById(box + '_MAP')) obj.href = 'javascript:' + box + '.run(0)'; 
	if (obj = document.getElementById(box + '_LOC')) obj.href = 'javascript:' + box + '.run(1)'; 
	if (obj = document.getElementById(box + '_RTM')) obj.href = 'javascript:' + box + '.run(2)'; 


	//********************************** Public Class Function Begin ********************************** 
	this.run = function(a) {//switch cType
		try {
			(a === 9) ?  a = cType : cType = a;
			type = a === 0 ? 12 : 14;
			this.type = type;

			document.getElementById(box).innerHTML =
				"<div class='mapbox'>"+
					"<div id='" + cName + "' style='width:" + W + "px; height:" + H + "px;'></div>"+
				"</div>";

			
			// 图表实例化------------------
			//define chart object
			iChart = echarts.init(document.getElementById(cName));
			// 过渡---------------------
			iChart.showLoading({text: 'Loading...'}); //loading话术
			
			//run
			this.wGet(PassportUrl, true, 0); 

			if (timer > 0 && hTimer === 0) hTimer = setInterval(Update, timer);
			
			//add resize events
			//window.addEventListener ? window.addEventListener('resize',Resize) : document.attachEvent('onresize', Resize);
		} catch(z) {
			alert('run error!' + z.name + ': ' + z.message);
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

	this.callAjax = function(pass, flag) {//flag = 0 is ini, flag = 1 is update
		try {
			switch(cType) {
			case 0://0->map by loaction's name, 
				this.q = 'map';
				break;
			case 1://1->map by location's Latitude and longitude, 
				this.q = 'geo map';
				break;
			case 2://2->map by realtime
				this.q = 'realtime map';
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
					iChart.hideLoading();
					AjaxData = [];
					var v = myAjax.responseText.replace(/(^\s*)|(\s*$)/g, '')
					if (v !== '') {
						AjaxData = eval(v);
					} else {
						//document.getElementById(box).innerHTML = HTML_NODATA_MAP;
						return;
					}
					if (flag) {//update
						drawMap(cType, AjaxData);
					} else {
						drawMap(cType, AjaxData);
						Resize();
					}
				}
			}
			myAjax.open('GET', url, true);
			myAjax.send();
		} catch(e) {	//alert(e.name + ': ' + e.message);
			alert('XMLHttpRequest Error - ChartAPI - Tpye: ' + cType + ' Error: ' + e.name + ' - ' + e.message);
		}
	};

	this.resize = function() {
		Resize();
	};



	

	function drawMap(a, x) {//a is map type, a->0 Visits, a->1 Realtime Visitors, a->2 Location

		var rData = [];
		var rMax = getData(a,x,rData);
		if (rMax < 10) rMax = 10;
//console.log(rData);

		// 图表使用-------------------
		option = {
			color: ['#5D78AA'],
			backgroundColor: '#fdfdfd',
			tooltip: {
				trigger: 'item',
				formatter: function (params) {
					var value = 0,
						p = params.value + '';
					if (p !== 'NaN') {
						p = (p).split(',');
						value = p.length > 1 ? p[2] : p[0];
					}
					return (x[0][2] === 1 && a === 0 ? nameMapCN[params.name] : params.name) + '<br/>' + params.seriesName + ' : ' + value;
					//return params.name + '<br/>' + params.seriesName + ' : ' + value;
				}
				//formatter: '{b}<br/>{a}: {c}'
			},
			title: {
				text: Titles[0] + ': ' + x[0][1],
				textStyle: {
					color: '#888',
					fontSize: 12,
					fontStyle: 'normal',
					fontWeight: 'normal',
					fontFamily: 'Microsoft Yahei'//'Arial'
				},
				left: 10,
				top: 10
			},
			visualMap: {},
			geo: {
				map: 'world',
				roam: (roaming === 0 ? false : roaming === 1 ? true : roaming === 2 ? 'scale' : 'move'),
				aspectScale: 0.75,
				left: (W > 600 ? 20 : 10),
				right: (W > 600 ? 20 : 10),
				//nameMap: (x[0][2] == 1 ? nameMapCN : {}),
				label: {
					emphasis: {show: false}
				},
				itemStyle: {
					normal: {
						areaColor: '#eee',
						borderColor: '#ccc',
						borderWidth: 1
					},
					emphasis: {
						areaColor: (a === 0 ? '#ddb926' : '#eee'),
						borderColor: '#ccc',
						borderWidth: 1
					}
				}
			},//*/
			series: []
		};


		option.visualMap = {
				itemWidth:  (H < 300 ? 10 : H < 360 ? 12 : H < 400 ? 15 : 20),
				itemHeight: (H < 300 ? 50 : H < 360 ? 75 : H < 400 ? 100 : 150),
				show: (a === 2 ? false : true),
				hoverable: true,
				realtime: false,
				calculable: true,
				left: (H < 300 ? 5 : H < 360 ? 7 : 10),
				bottom: 5,
				min: 0,
				max: rMax,
				inRange: {color: (a === 2 ? ['#5D78AA'] : ['#9FB1D2', '#5D78AA'])}
			};

		switch (a) {
		case 0:// show all visit's location on map by location's name
		case 1:// show all visit's location on map by location's Latitude and longitude	   
			option.series[0] = a === 0 ? 
			{
				name: Titles[1],
				type: 'map',
				geoIndex: 0,
				data: rData//[{name: 'China', value: 5}]
			} : {
				name: Titles[1],
				type: 'scatter',
				coordinateSystem: 'geo',
				geoIndex: 0,
				symbolSize: function (val) {
					var zoom = 10,
						init = 5;
					if (W > 600) {
						zoom = rMax > 50 ? 30 : rMax > 20 ? 20 : 10;
						init = 6;
					}
					return (val[2] / rMax) * zoom + init;
				},
				label: {
					normal: {show: false},
					emphasis: {show: false}
				},
				itemStyle: {
					normal: {color: '#5D78AA'},
					emphasis: {color: '#ddb926'}
				},
				data: rData//
				//data: [{name: '无锡', value: [120.29, 31.59, 15]},{name: 'shanghai', value: [100.29, 31.59, 10]}]
			};
			break;
		case 2:// realtime visitors on map
			option.series[0] = {
				name: Titles[1],
				type: 'effectScatter',
				showLegendSymbol: false,
				coordinateSystem: 'geo',
				geoIndex: 0,
				symbolSize: function (val) {
					var zoom = 10,
						init = 5;
					if (W > 600) {
						zoom = rMax > 50 ? 30 : rMax > 20 ? 20 : 10;
						init = 6;
					}
					return (val[2] / rMax) * zoom + init;
				},
				showEffectOn: 'render',
				rippleEffect: {
					brushType: 'fill',
					scale: 3
				},
				hoverAnimation: true,
				label: {
					normal: {show: false}
				},
				itemStyle: {
					normal: {color: '#5D78AA'},
					emphasis: {color: '#ddb926'}
				},
				//zlevel: 1,
				data: rData//data: [{name: 'China', value: [116.46, 39.92, 100]}],
			};
			break;
		}
//console.log(option);
		iChart.setOption(option);
	}

	function Update() {
		if (document.hidden) return;
		that.wGet(PassportUrl,true,1); 
	}

	function getTitle(type) {
		switch (type) {
		case 1:
			return [Lan['Visits'], Lan['Visits']];
		case 2:
			return ['Unique Visitors', 'Unique Visitors'];
		case 3:
			return ['Bounce Rate', 'Bounce Rate'];
		case 4:
			return ['Avg Time On Website', 'Seconds'];
		}
		return '';
	}

	function Resize() {
		var obj = document.getElementById(box);
		if (obj) {
			W = parseInt(obj.parentNode.offsetWidth - 15); 
			H = parseInt(W * 0.55); 
			obj = document.getElementById(cName);
			obj.style.width = W + 'px';
			obj.style.height = H + 'px';
			if (cType === 0 && typeof(option.visualMap) !== 'undefined') {
				option.visualMap.itemWidth =  (H < 300 ? 10 : H < 360 ? 12 : H < 400 ? 15 : 20);
				option.visualMap.itemHeight = (H < 300 ? 50 : H < 360 ? 75 : H < 400 ? 100 : 150);
				option.visualMap.left =	   (H < 300 ? 5 : H < 360 ? 7 : 10),
				iChart.setOption(option);
			}
			
			iChart.resize();
		}
	}

	function getData(a, x, ret) {
		var max = 1, tmp='';
		switch (a) {
		case 0:// show all visit's country on map by country's name
			for (var i=1; i<x.length; i++) {
				//if (x[i][0] == '中国') x[i][0]= 'China';
				tmp = (x[0][2] == 1 || x[0][2] == 3) ? nameMapEN[x[i][0]] : x[i][0];
				x[i][1] = parseInt(x[i][1]);
				if (x[i][1] === 0) continue;
				if (x[i][1] > max) max = x[i][1];
				ret.push({name: tmp, value: x[i][1]});
			}
			break;
		case 1:// show all visit's location on map by location's Latitude and longitude
		case 2://
			var geo = [];
			for (var i=1; i<x.length; i++) {
				//if (x[i][0] == '中国') x[i][0]= 'China';
				//tmp = (x[0][2] === 1 && a === 0) ? nameMapEN[x[i][0]] : x[i][0];
				x[i][1] = parseInt(x[i][1]);
				if (x[i][1] === 0) continue;
				if (x[i][1] > max) max = x[i][1];
				geo = x[i][2].split(',');
				ret.push({name: x[i][0], value: [parseFloat(geo[0]), parseFloat(geo[1]), x[i][1]]});
			}
			break;
		}
		return max;
	}

	var nameMapCN = {
		'Afghanistan':'阿富汗',
		'Angola':'安哥拉',
		'Albania':'阿尔巴尼亚',
		'United Arab Emirates':'阿联酋',
		'Argentina':'阿根廷',
		'Armenia':'亚美尼亚',
		'French Southern and Antarctic Lands':'法属南半球和南极领地',
		'Australia':'澳大利亚',
		'Austria':'奥地利',
		'Azerbaijan':'阿塞拜疆',
		'Burundi':'布隆迪',
		'Belgium':'比利时',
		'Benin':'贝宁',
		'Burkina Faso':'布基纳法索',
		'Bangladesh':'孟加拉国',
		'Bulgaria':'保加利亚',
		'The Bahamas':'巴哈马',
		'Bosnia and Herzegovina':'波斯尼亚和黑塞哥维那',
		'Belarus':'白俄罗斯',
		'Belize':'伯利兹',
		'Bermuda':'百慕大',
		'Bolivia':'玻利维亚',
		'Brazil':'巴西',
		'Brunei':'文莱',
		'Bhutan':'不丹',
		'Botswana':'博茨瓦纳',
		'Central African Republic':'中非共和国',
		'Canada':'加拿大',
		'Switzerland':'瑞士',
		'Chile':'智利',
		'China':'中国',
		'Ivory Coast':'象牙海岸',
		'Cameroon':'喀麦隆',
		'Democratic Republic of the Congo':'刚果民主共和国',
		'Republic of the Congo':'刚果共和国',
		'Colombia':'哥伦比亚',
		'Costa Rica':'哥斯达黎加',
		'Cuba':'古巴',
		'Northern Cyprus':'北塞浦路斯',
		'Cyprus':'塞浦路斯',
		'Czech Republic':'捷克共和国',
		'Germany':'德国',
		'Djibouti':'吉布提',
		'Denmark':'丹麦',
		'Dominican Republic':'多明尼加共和国',
		'Algeria':'阿尔及利亚',
		'Ecuador':'厄瓜多尔',
		'Egypt':'埃及',
		'Eritrea':'厄立特里亚',
		'Spain':'西班牙',
		'Estonia':'爱沙尼亚',
		'Ethiopia':'埃塞俄比亚',
		'Finland':'芬兰',
		'Fiji':'斐',
		'Falkland Islands':'福克兰群岛',
		'France':'法国',
		'Gabon':'加蓬',
		'United Kingdom':'英国',
		'Georgia':'格鲁吉亚',
		'Ghana':'加纳',
		'Guinea':'几内亚',
		'Gambia':'冈比亚',
		'Guinea Bissau':'几内亚比绍',
		'Equatorial Guinea':'赤道几内亚',
		'Greece':'希腊',
		'Greenland':'格陵兰',
		'Guatemala':'危地马拉',
		'French Guiana':'法属圭亚那',
		'Guyana':'圭亚那',
		'Honduras':'洪都拉斯',
		'Croatia':'克罗地亚',
		'Haiti':'海地',
		'Hungary':'匈牙利',
		'Indonesia':'印尼',
		'India':'印度',
		'Ireland':'爱尔兰',
		'Iran':'伊朗',
		'Iraq':'伊拉克',
		'Iceland':'冰岛',
		'Israel':'以色列',
		'Italy':'意大利',
		'Jamaica':'牙买加',
		'Jordan':'约旦',
		'Japan':'日本',
		'Kazakhstan':'哈萨克斯坦',
		'Kenya':'肯尼亚',
		'Kyrgyzstan':'吉尔吉斯斯坦',
		'Cambodia':'柬埔寨',
		'South Korea':'韩国',
		'Kosovo':'科索沃',
		'Kuwait':'科威特',
		'Laos':'老挝',
		'Lebanon':'黎巴嫩',
		'Liberia':'利比里亚',
		'Libya':'利比亚',
		'Sri Lanka':'斯里兰卡',
		'Lesotho':'莱索托',
		'Lithuania':'立陶宛',
		'Luxembourg':'卢森堡',
		'Latvia':'拉脱维亚',
		'Morocco':'摩洛哥',
		'Moldova':'摩尔多瓦',
		'Madagascar':'马达加斯加',
		'Mexico':'墨西哥',
		'Macedonia':'马其顿',
		'Mali':'马里',
		'Myanmar':'缅甸',
		'Montenegro':'黑山',
		'Mongolia':'蒙古',
		'Mozambique':'莫桑比克',
		'Mauritania':'毛里塔尼亚',
		'Malawi':'马拉维',
		'Malaysia':'马来西亚',
		'Namibia':'纳米比亚',
		'New Caledonia':'新喀里多尼亚',
		'Niger':'尼日尔',
		'Nigeria':'尼日利亚',
		'Nicaragua':'尼加拉瓜',
		'Netherlands':'荷兰',
		'Norway':'挪威',
		'Nepal':'尼泊尔',
		'New Zealand':'新西兰',
		'Oman':'阿曼',
		'Pakistan':'巴基斯坦',
		'Panama':'巴拿马',
		'Peru':'秘鲁',
		'Philippines':'菲律宾',
		'Papua New Guinea':'巴布亚新几内亚',
		'Poland':'波兰',
		'Puerto Rico':'波多黎各',
		'North Korea':'北朝鲜',
		'Portugal':'葡萄牙',
		'Paraguay':'巴拉圭',
		'Qatar':'卡塔尔',
		'Romania':'罗马尼亚',
		'Russia':'俄罗斯',
		'Rwanda':'卢旺达',
		'Western Sahara':'西撒哈拉',
		'Saudi Arabia':'沙特阿拉伯',
		'Sudan':'苏丹',
		'South Sudan':'南苏丹',
		'Senegal':'塞内加尔',
		'Solomon Islands':'所罗门群岛',
		'Sierra Leone':'塞拉利昂',
		'El Salvador':'萨尔瓦多',
		'Somaliland':'索马里兰',
		'Somalia':'索马里',
		'Republic of Serbia':'塞尔维亚共和国',
		'Suriname':'苏里南',
		'Slovakia':'斯洛伐克',
		'Slovenia':'斯洛文尼亚',
		'Sweden':'瑞典',
		'Swaziland':'斯威士兰',
		'Syria':'叙利亚',
		'Chad':'乍得',
		'Togo':'多哥',
		'Thailand':'泰国',
		'Tajikistan':'塔吉克斯坦',
		'Turkmenistan':'土库曼斯坦',
		'East Timor':'东帝汶',
		'Trinidad and Tobago':'特里尼达和多巴哥',
		'Tunisia':'突尼斯',
		'Turkey':'土耳其',
		'United Republic of Tanzania':'坦桑尼亚联合共和国',
		'Uganda':'乌干达',
		'Ukraine':'乌克兰',
		'Uruguay':'乌拉圭',
		'United States':'美国',
		'Uzbekistan':'乌兹别克斯坦',
		'Venezuela':'委内瑞拉',
		'Vietnam':'越南',
		'Vanuatu':'瓦努阿图',
		'West Bank':'西岸',
		'Yemen':'也门',
		'South Africa':'南非',
		'Zambia':'赞比亚',
		'Zimbabwe':'津巴布韦'
	};


	var nameMapEN = {
		'阿富汗':'Afghanistan',
		'安哥拉':'Angola',
		'阿尔巴尼亚':'Albania',
		'阿联酋':'United Arab Emirates',
		'阿根廷':'Argentina',
		'亚美尼亚':'Armenia',
		'法属南半球和南极领地':'French Southern and Antarctic Lands',
		'澳大利亚':'Australia',
		'奥地利':'Austria',
		'阿塞拜疆':'Azerbaijan',
		'布隆迪':'Burundi',
		'比利时':'Belgium',
		'贝宁':'Benin',
		'布基纳法索':'Burkina Faso',
		'孟加拉国':'Bangladesh',
		'保加利亚':'Bulgaria',
		'巴哈马':'The Bahamas',
		'波斯尼亚和黑塞哥维那':'Bosnia and Herzegovina',
		'白俄罗斯':'Belarus',
		'伯利兹':'Belize',
		'百慕大':'Bermuda',
		'玻利维亚':'Bolivia',
		'巴西':'Brazil',
		'文莱':'Brunei',
		'不丹':'Bhutan',
		'博茨瓦纳':'Botswana',
		'中非共和国':'Central African Republic',
		'加拿大':'Canada',
		'瑞士':'Switzerland',
		'智利':'Chile',
		'中国':'China',
		'象牙海岸':'Ivory Coast',
		'喀麦隆':'Cameroon',
		'刚果民主共和国':'Democratic Republic of the Congo',
		'刚果共和国':'Republic of the Congo',
		'哥伦比亚':'Colombia',
		'哥斯达黎加':'Costa Rica',
		'古巴':'Cuba',
		'北塞浦路斯':'Northern Cyprus',
		'塞浦路斯':'Cyprus',
		'捷克共和国':'Czech Republic',
		'德国':'Germany',
		'吉布提':'Djibouti',
		'丹麦':'Denmark',
		'多明尼加共和国':'Dominican Republic',
		'阿尔及利亚':'Algeria',
		'厄瓜多尔':'Ecuador',
		'埃及':'Egypt',
		'厄立特里亚':'Eritrea',
		'西班牙':'Spain',
		'爱沙尼亚':'Estonia',
		'埃塞俄比亚':'Ethiopia',
		'芬兰':'Finland',
		'斐':'Fiji',
		'福克兰群岛':'Falkland Islands',
		'法国':'France',
		'加蓬':'Gabon',
		'英国':'United Kingdom',
		'格鲁吉亚':'Georgia',
		'加纳':'Ghana',
		'几内亚':'Guinea',
		'冈比亚':'Gambia',
		'几内亚比绍':'Guinea Bissau',
		'赤道几内亚':'Equatorial Guinea',
		'希腊':'Greece',
		'格陵兰':'Greenland',
		'危地马拉':'Guatemala',
		'法属圭亚那':'French Guiana',
		'圭亚那':'Guyana',
		'洪都拉斯':'Honduras',
		'克罗地亚':'Croatia',
		'海地':'Haiti',
		'匈牙利':'Hungary',
		'印尼':'Indonesia',
		'印度':'India',
		'爱尔兰':'Ireland',
		'伊朗':'Iran',
		'伊拉克':'Iraq',
		'冰岛':'Iceland',
		'以色列':'Israel',
		'意大利':'Italy',
		'牙买加':'Jamaica',
		'约旦':'Jordan',
		'日本':'Japan',
		'哈萨克斯坦':'Kazakhstan',
		'肯尼亚':'Kenya',
		'吉尔吉斯斯坦':'Kyrgyzstan',
		'柬埔寨':'Cambodia',
		'韩国':'South Korea',
		'科索沃':'Kosovo',
		'科威特':'Kuwait',
		'老挝':'Laos',
		'黎巴嫩':'Lebanon',
		'利比里亚':'Liberia',
		'利比亚':'Libya',
		'斯里兰卡':'Sri Lanka',
		'莱索托':'Lesotho',
		'立陶宛':'Lithuania',
		'卢森堡':'Luxembourg',
		'拉脱维亚':'Latvia',
		'摩洛哥':'Morocco',
		'摩尔多瓦':'Moldova',
		'马达加斯加':'Madagascar',
		'墨西哥':'Mexico',
		'马其顿':'Macedonia',
		'马里':'Mali',
		'缅甸':'Myanmar',
		'黑山':'Montenegro',
		'蒙古':'Mongolia',
		'莫桑比克':'Mozambique',
		'毛里塔尼亚':'Mauritania',
		'马拉维':'Malawi',
		'马来西亚':'Malaysia',
		'纳米比亚':'Namibia',
		'新喀里多尼亚':'New Caledonia',
		'尼日尔':'Niger',
		'尼日利亚':'Nigeria',
		'尼加拉瓜':'Nicaragua',
		'荷兰':'Netherlands',
		'挪威':'Norway',
		'尼泊尔':'Nepal',
		'新西兰':'New Zealand',
		'阿曼':'Oman',
		'巴基斯坦':'Pakistan',
		'巴拿马':'Panama',
		'秘鲁':'Peru',
		'菲律宾':'Philippines',
		'巴布亚新几内亚':'Papua New Guinea',
		'波兰':'Poland',
		'波多黎各':'Puerto Rico',
		'北朝鲜':'North Korea',
		'葡萄牙':'Portugal',
		'巴拉圭':'Paraguay',
		'卡塔尔':'Qatar',
		'罗马尼亚':'Romania',
		'俄罗斯':'Russia',
		'卢旺达':'Rwanda',
		'西撒哈拉':'Western Sahara',
		'沙特阿拉伯':'Saudi Arabia',
		'苏丹':'Sudan',
		'南苏丹':'South Sudan',
		'塞内加尔':'Senegal',
		'所罗门群岛':'Solomon Islands',
		'塞拉利昂':'Sierra Leone',
		'萨尔瓦多':'El Salvador',
		'索马里兰':'Somaliland',
		'索马里':'Somalia',
		'塞尔维亚共和国':'Republic of Serbia',
		'苏里南':'Suriname',
		'斯洛伐克':'Slovakia',
		'斯洛文尼亚':'Slovenia',
		'瑞典':'Sweden',
		'斯威士兰':'Swaziland',
		'叙利亚':'Syria',
		'乍得':'Chad',
		'多哥':'Togo',
		'泰国':'Thailand',
		'塔吉克斯坦':'Tajikistan',
		'土库曼斯坦':'Turkmenistan',
		'东帝汶':'East Timor',
		'特里尼达和多巴哥':'Trinidad and Tobago',
		'突尼斯':'Tunisia',
		'土耳其':'Turkey',
		'坦桑尼亚联合共和国':'United Republic of Tanzania',
		'乌干达':'Uganda',
		'乌克兰':'Ukraine',
		'乌拉圭':'Uruguay',
		'美国':'United States',
		'乌兹别克斯坦':'Uzbekistan',
		'委内瑞拉':'Venezuela',
		'越南':'Vietnam',
		'瓦努阿图':'Vanuatu',
		'西岸':'West Bank',
		'也门':'Yemen',
		'南非':'South Africa',
		'赞比亚':'Zambia',
		'津巴布韦':'Zimbabwe'
	};

}//END MAPAPI

/*
// 增加些数据------------------
option.legend.data.push('win');
option.series.push({
		name: 'win',							// 系列名称
		type: 'line',						 // 图表类型，折线图line、散点图scatter、柱状图bar、饼图pie、雷达图radar
		data: [112, 23, 45, 56, 233, 343, 454, 89, 343, 123, 45, 123]
});
iChart.setOption(option);



// 图表清空-------------------
iChart.clear();

// 图表释放-------------------
iChart.dispose();
*/
