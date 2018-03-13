/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free CA DatePick JS Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

function DatePick(obj) {

	var o = document.getElementById(obj),
		p = o.parentNode,
		e = o.getElementsByTagName("DIV"),
		t = e[0],// title
		b = e[1];// body

	this.o = o;
	this.p = p;
	this.t = e[0];
	this.b = e[1];
	this.xy = GetOffset(this.o, -3);
	this.runonce = !1;
	
	if (this.runonce == !1) {
		Init();
		this.runonce = !0;
	}

	function Init() {
		if (window.addEventListener) { //for firefox, chrome, safari
			window.addEventListener("click", Cancel);
			window.addEventListener("resize", Resize);
		} else { // for IE5,6,7,8
			document.attachEvent("onclick", Cancel);
			document.attachEvent("onresize", Resize);
		}
	}
	
	function Hide() {//hide
		b.style.display  = "none";
		t.style.width    = "auto";
		o.style.position = "static";
		o.style.width    = "auto";
		o.style.height   = "32px";
		p.style.width    = "auto";
		p.style.height   = "auto";
		removeClass(o, "d_active");
	}
	
	function Cancel(e) {
		var B = document.body,
			xy = GetOffset(o,0);

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
	
	function Resize(e) {
		var xy = b.style.display == "block" ? GetOffset(p, 0) : GetOffset(o, -3);
		o.style.left = xy.x + "px";
		o.style.top  = xy.y + "px";
	}
}

	DatePick.prototype.Status = function() {
		var status = this.b.style.display;
		if (status == "block") {
			this.Hide();
		} else {
			this.Show();
		}
	}

	DatePick.prototype.Show = function() {
		this.xy = GetOffset(this.o, -3);
		this.p.style.width = this.p.offsetWidth + "px";
		this.p.style.height = this.p.offsetHeight + "px";
		this.b.style.display = "block";
		this.o.style.left = this.xy.x + "px";
		this.o.style.top = this.xy.y + "px";
		this.o.style.position = "absolute";
		this.o.style.width = this.b.style.width;
		this.o.style.height = "auto";
		this.t.style.width = "auto";
		addClass(this.o, "d_active");
	} 
	
	DatePick.prototype.Hide = function() {//hide
		this.b.style.display = "none";
		this.t.style.width = "auto";
		this.o.style.position = "static";
		this.o.style.width = "auto";
		this.o.style.height = "32px";
		this.p.style.width = "auto";
		this.p.style.height = "auto";
		removeClass(this.o, "d_active");
	}








function Calendar(o, from, to, period, current, lan) {

	this.o = o;
	this.Period = period;
	this.TY = parseInt(to.substr(0,4));//to year
	this.TM = parseInt(to.substr(4,2));
	this.TD = parseInt(to.substr(6,2));
	this.FY = parseInt(from.substr(0,4));//from year
	this.FM = parseInt(from.substr(4,2));
	this.FD = parseInt(from.substr(6,2));
	this.CY = current ? parseInt(current.substr(0,4)) : this.TY;//current year
	this.CM = current ? parseInt(current.substr(4,2)) : this.TM;
	this.CD = current ? parseInt(current.substr(6,2)) : this.TD;
	this.SY = this.CY;//select year
	this.SM = this.CM;
	this.SD = this.CD;
	this.EY = this.CY;//exchange year
	this.EM = this.CM;
	this.ED = this.CD;
	
	this.MD = this.MaxDays(this.SY,this.SM);//max days of month
	this.WD = this.FirstWeekDay(this.SY,this.SM);//first day in week;
	
	this.Lan = lan;
}

	Calendar.prototype.MaxDays = function(year, month) {
		month--;
		var enddate = ["31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31"];
		if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)) {
			enddate[1] = "29";
		}
		return enddate[month];
	}
	
	Calendar.prototype.FirstWeekDay = function(year, month) {
		var strdate = year + "/" + month + "/01",
		dt = new Date(strdate);
		return dt.getDay();
	}
	
	Calendar.prototype.Draw = function() {
		var html_text = "";
		
		//year
		for (var i=this.FY; i<=this.TY; i++) {
			html_text += "<li><a class='guidebtn' onclick='" + this.o + "_date.SetYear(" + i + ")'>" + i + "</a></li>";
		}
		document.getElementById("year_" + this.o).innerHTML = this.CY;
		document.getElementById("yearlist_" + this.o).innerHTML = html_text;
		
		//month
		html_text = "";
		for (var i=1; i<=12; i++) {
			html_text += "<li><a class='guidebtn' onclick='" + this.o + "_date.SetMonth(" + i + ")'>" + i + "</a></li>";
		}
		document.getElementById("month_" + this.o).innerHTML = this.CM;
		document.getElementById("monthlist_" + this.o).innerHTML = html_text;
		
		//day
		html_text ="";
		this.SY = this.CY;//select year
		this.SM = this.CM;
		this.SD = this.CD;
		this.EY = this.CY;
		this.EM = this.CM;
		this.ED = this.CD;
		this.Refresh();
	}
	
	Calendar.prototype.SetYear = function(x) {
		this.SY = x;
		document.getElementById("year_" + this.o).innerHTML = this.SY;
		this.Refresh();
	}


	Calendar.prototype.SetMonth = function(x) {
		this.SM = x;
		document.getElementById("month_" + this.o).innerHTML = this.SM;
		this.Refresh();
	}
	
	Calendar.prototype.SetDay = function(x) {
		if (this.SY == this.EY && this.SM == this.EM) removeClass(document.getElementById(this.o + this.ED), "focus");
		this.SD = x;
		this.EY = this.SY;
		this.EM = this.SM;
		this.ED = this.SD;
		this.MD = this.MaxDays(this.SY,this.SM);//max days of month
		this.WD = this.FirstWeekDay(this.SY,this.SM);//first day in week;
		
		addClass(document.getElementById(this.o + x),"focus");
		
		CA_SELECT_DATE[this.o] = this.SY + "" + ((this.SM < 10) ? ("0" + this.SM) : this.SM) + "" + ((this.SD < 10) ? ("0" + this.SD) : this.SD);
	}

	Calendar.prototype.SetDate = function(x, period) {
		x += '';
		this.SD = parseInt(x.substr(6,2));
		this.SM = parseInt(x.substr(4,2));
		this.SY = parseInt(x.substr(0,4));
		this.CY = this.SY;
		this.CM = this.SM;
		this.CD = this.SD;
		this.Period = period;
		
		this.RefreshDate();
	}

	Calendar.prototype.Refresh = function() {		

		html_text = "";
		this.WD = this.FirstWeekDay(this.SY,this.SM);//first day in week;
		this.MD = this.MaxDays(this.SY,this.SM);//max days of month
		for (var i=0;i<this.WD;i++) {
			html_text += "<span></span>";
		}

		for (var i=1; i<=this.MD; i++) {
			if (i == this.ED && this.SY == this.EY && this.SM == this.EM) {
				html_text += "<a id='" + this.o + i + "' class='focus' onclick='" + this.o + "_date.SetDay(" + i + ");'>" + i + "</a>";
			} else if ((this.SY >= this.TY && this.SM > this.TM) || (this.SY <= this.FY && this.SM < this.FM)) {
				html_text += "<span>" + i + "</span>";
			} else if (i > this.TD && this.SY >= this.TY && this.SM >= this.TM) {
				html_text += "<span>" + i + "</span>";
			} else if (i < this.FD && this.SY <= this.FY && this.SM <= this.FM) {
				html_text += "<span>" + i + "</span>";
			} else {
				html_text += "<a id='" + this.o + i + "' onclick='" + this.o + "_date.SetDay(" + i + ");'>" + i + "</a>";
			}
		}

		CA_SELECT_DATE[this.o] = this.CY + "" + ((this.CM < 10) ? ("0" + this.CM) : this.CM) + "" + ((this.CD < 10) ? ("0" + this.CD) : this.CD);
		document.getElementById("day_" + this.o).innerHTML = html_text;
		
		this.RefreshDate();
	}

	Calendar.prototype.LastMonth = function() {
		if (this.SM > 1) {
			this.SM--;
		} else if (this.SY > this.FY) {
			this.SM = 12;
			this.SY--;
		} else {
			return;
		}
		
		document.getElementById("year_" + this.o).innerHTML = this.SY;
		document.getElementById("month_" + this.o).innerHTML = this.SM;
		this.Refresh();
	}

	Calendar.prototype.NextMonth = function() {
	
		if (this.SM < 12) {
			this.SM++;
		} else if (this.SY < this.TY) {
			this.SM = 1;
			this.SY++;
		} else {
			return;
		}
		
		document.getElementById("year_" + this.o).innerHTML = this.SY;
		document.getElementById("month_" + this.o).innerHTML = this.SM;
		this.Refresh();
	}

	Calendar.prototype.RefreshDate = function() {
	
		document.getElementById("period_to").innerHTML = "";
		
		switch (this.Period) {
		case 0: //today
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.Lan["Today"];
			break;
		case 1: //yesterday
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.Lan["Yesterday"];
			break;
		case 2: //last 7 days
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.Lan["Past 7 Days"];
			break;
		case 3:  //last 30 days
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.Lan["Past 30 Days"];
			break;
		case 4: //last week
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.Lan["Last Week"];
			break;
		case 5: //last month
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.Lan["Last Month"];
			break;
		case 6: //specified day
			if (this.o == "from") document.getElementById("period_" + this.o).innerHTML = this.CY + "-" + ((this.CM < 10) ? ("0" + this.CM) : this.CM) + "-" + ((this.CD < 10) ? ("0" + this.CD) : this.CD);
			break;
		case 7: //range
			if (this.o == "from") {
				document.getElementById("period_" + this.o).innerHTML = this.Lan["From"] + " " + this.CY + "-" + ((this.CM < 10) ? ("0" + this.CM) : this.CM) + "-" + ((this.CD < 10) ? ("0" + this.CD) : this.CD);
			} else {
				document.getElementById("period_" + this.o).innerHTML = " " + this.Lan["To"] + " " + this.CY + "-" + ((this.CM < 10) ? ("0" + this.CM) : this.CM) + "-" + ((this.CD < 10) ? ("0" + this.CD) : this.CD);
			}
			break;
		}
	}








	