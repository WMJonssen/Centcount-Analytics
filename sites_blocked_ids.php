<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Manage Blocked Visitor IDs PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

function blocked_ids_operation() {

		$param = empty($_POST['param']) ? '' : SDATA($_POST['param'], 1, 64);
		$key = empty($_POST['key']) ? '' : SDATA($_POST['key'], 1, 32);
		$value = empty($_POST['value']) ? '' : SDATA($_POST['value'], 7, 2048);
		
		$v = get_visa();//Authorize for access database
		$host = get_site_info('DataCenter');
		$tz = get_site_info('TimeZone');
		
		$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
		$q = $v . 'q=set domain&type=6&sid=' . $GLOBALS['SITEID'] . '&tz=' . $tz . '&param=' . $param . '&key=' . $key . '&value=' . $value;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
		$ret = curl_exec($ch);
		curl_close($ch);
		
		if ($ret === 'OK') $ret = '';
		
		return $ret;
		
}


function blocked_ids_table_html() {
	return '
	<div id="frametable" class="frametable"></div>
	<div id="frameinput" class="frameinput">		
		<div class="framebox">
			<div class="inputbox">
				<div class="inputtitle">'. $GLOBALS['indicator']['Add Blocked VID'] .'</div>
				<div class="closebtn"><a href="javascript:showInputBox(0)" style="height:24px; float:left;"><img src="images/inputclose.png" style="width:24px; height:24px;" alt="close" title="close"/></a></div>
				<form name="blockitemform" method="POST" action="" style="width:100%; height:100%; text-align:left; float:left;">
					<div style="width:100%; height:auto; padding:15px; padding-top:0px; text-align:left; float:left;">
						<p style="margin:0px; padding:0px; font-size:13px; height:30px; line-height:30px;">'. $GLOBALS['indicator']['Blocked VID'] .' :</p>
						<textarea name="blockitem" id="blockitem" maxlength="10240"></textarea>
					</div>
					<div style="width:100%; height:auto; padding:15px; padding-top:0px; text-align:left; float:left;">
						<button name="blockitembtn" id="blockitemsubmit" type="button" class="middle" onclick="showInputBox(0);batchaddblockitem();" style="float:left;">'. $GLOBALS['indicator']['Batch Add'] .'</button></td>
					</div>
				</form>	
			</div>
		</div>
	</div> ';
}


function blocked_ids() {

	$sitename;
	$timezone;
	$ret = array();
	$sum = 1;
	
	$i = count($GLOBALS['SITES']);
	for ($row = 0; $row < $i; $row++) {
		if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
			$sitename = $GLOBALS['SITES'][$row]['SiteName'];
			$timezone = $GLOBALS['SITES'][$row]['TimeZone'];
			break;
		}
	}
	date_default_timezone_set($timezone);
	
	$i = count($GLOBALS['DOMAINS']);
	if ($i)	{
		for ($row = 0; $row < $i; $row++) {
			if ($GLOBALS['DOMAINS'][$row]['DomainType'] == 6) {
				$tmp = $GLOBALS['DOMAINS'][$row];
				$tmp['SiteName']   = $sitename;
				$tmp['SiteID']     = $GLOBALS['SITEID'];
				$tmp['UpdateTime'] = date('Y-m-d H:i:s', $tmp['UpdateTime']);
				$tmp['CreateTime'] = date('Y-m-d H:i:s', $tmp['CreateTime']);
				$ret[] = $tmp;
			}
		}
	}
	
	return json_encode($ret);
}


function blocked_ids_html() {
		

	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
			
	$GLOBALS['TEXT_BODY'] = blocked_ids_table_html();
			
	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">

.framebody{width:auto; min-width:330px; height:auto; float:none; margin:15px; margin-top:0px; text-align:left; overflow-x:auto; overflow-y:hidden;}
.errmsg{margin-bottom:10px;}
.frametable{width:100%; height:auto; text-align:center;}
table{width:100%; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana; table-layout:fixed;}
td{border:#ccc 1px solid; font-size:12px; line-height:18px; color:#555; padding-left:10px; padding-right:10px; overflow:hidden;}

.trhead{width:100%; height:45px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#e7e7e7; font-family:"Microsoft Yahei",Arial,Verdana;}
.trfoot{width:100%; height:45px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#e7e7e7; font-family:"Microsoft Yahei",Arial,Verdana;}
.tra{width:100%; height:45px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#fff; font-family:"Microsoft Yahei",Arial,Verdana;}
.trb{width:100%; height:45px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#f7f7f7; font-family:"Microsoft Yahei",Arial,Verdana;}
.tra:hover,.trb:hover{border-left:#39F 2px solid; background-color:#C6E2FF;}

.tdhlt{border:0px; text-align:left; font-size:13px; color:#000;}
.tdhrt{border:0px; text-align:right; font-size:13px; color:#000;}
.tdhmid{border:0px; text-align:center; font-size:13px; color:#000;}

.tdlt{text-align:left;}
.tdltnoborder{text-align:left; border:0px;}
.tdrt{text-align:right;}
.tdrtnoborder{text-align:right; border:0px;}
.tdmid{text-align:center;}
.tdmidnoborder{text-align:center; border:0px;}

p{white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
.plast{color:#999;}

button{width:120px; height:28px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333; float:right; margin-right:5px;}
textarea{width:-moz-calc(100% - 40px); width:-webkit-calc(100% - 40px); width: calc(100% - 40px); height:225px; line-height:20px; font-family:"Microsoft Yahei",Arial,Verdana; font-size:12px; color:#333; border:#ccc 1px solid; padding-left:5px; margin-right:10px; ime-mode:disabled; resize:none; float:left;}
img.mid{vertical-align:middle;}

.frameinput{width:100%; height:100%; display:none; position:fixed; top:0; left:0; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background:rgba(255,255,255,0.5); float:left; z-index:999999999;}
.framebox{width:auto; max-width:540px; min-width:360px; height:100%; min-height:362px; margin:auto; background:transparent; float:none; overflow:hidden;}
.inputbox{width:auto; height:360px; margin:auto; background:#eee; color:#555; float:none; border:#bbb 1px solid; border-radius:5px; position:relative; top:50%; margin-top:-180px;}
.closebtn{width:auto; height:auto; position:absolute; right:0px; top:0px; font-size:14px; color:#fff; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; background-color:transparent; margin:10px;}
.inputtitle{width:100%; height:auto; font-size:14px; color:#333; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background-color:transparent; float:left; margin-top:10px; margin-bottom:10px;}

@media only screen and (max-width: 960px) {
td{padding-left:5px; padding-right:5px;}
}

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">

	var SETTINGS = '. blocked_ids() .';
	var TB_WIDTH = I(document.getElementById("frametable").offsetWidth);
	var TB_RESIZE = (TB_WIDTH > 960 ? 0 : 1);


	function showInputBox(x) {
		document.getElementById("frameinput").style.display = x ? "block" : "none";
	}

	function modifyblockitem(x, y) {
		var str = prompt("Modify Blocked VID", y);
		//str = str.toLowerCase();
		if (str == y) {
			alert("No changes");
		} else if (str) {
			var param = "param=modify&key=" + x + "&value=" + str;
			CA_POST(param);
		}
	}
	
	function deleteblockitem(x,y) {
		if (confirm("Are you sure to delete the blocked VID: " + y + "?")) {
			var param = "param=delete&key=" + x + "&value=" + y;
			CA_POST(param);
		}
	}

	function batchaddblockitem() {
		var str = document.getElementById("blockitem").value ;
		if (str == "") {
			alert("No blocked VID added");
		} else if (str) {
			//str=str.toLowerCase();
			var param = "param=batch&value=" + str;
			CA_POST(param);
		}
	}


	function Resize() {
		TB_WIDTH = I(document.getElementById("frametable").offsetWidth);
		var tmp = (TB_WIDTH > 960 ? 0 : 1);
		if (tmp != TB_RESIZE) {
			blocked_ids_table_html(TB_WIDTH);
			TB_RESIZE = tmp;
		}
	}


	function get_table_header(w) {
		var html = 
			"<tr class=\"trhead\">" +
				(w > 960 ? "<td class=\"tdhmid\" style=\"width:5%\" >No</td>" : "") +
				"<td class=\"tdhlt\"  style=\"width:20%\">"+ LAN["Blocked VID"] +"</td>" +
				"<td class=\"tdhmid\" style=\"width:10%\">"+ LAN["Blocked Times"] +"</td>" +
				(w > 960 ?
				"<td class=\"tdhlt\"  style=\"width:15%\">"+ LAN["Site Name"] +"</td>" +
				"<td class=\"tdhmid\" style=\"width:10%\">"+ LAN["Site ID"] +"</td>" +
				"<td class=\"tdhmid\" style=\"width:10%\">"+ LAN["Update Time"] +"</td>" +
				"<td class=\"tdhmid\" style=\"width:10%\">"+ LAN["Create Time"] +"</td>" : "") +
				"<td class=\"tdhmid\" style=\"width:20%\" colspan=\"2\">"+ LAN["Settings"] +"</td>" +
			"</tr>";

		return html;
	}


	function blocked_ids_table_html(w) {

		var sum = 1;
		var cols = (w > 960 ? 9 : 4);
		var tabletext = "<table>" + get_table_header(w);
		
		var i = SETTINGS.length;
		if (i) {
			var n = true;
			for (var row = 0; row < i; row++) {
				tabletext += 
					(n ? "<tr class=\"tra\">" : "<tr class=\"trb\">") +
					(w > 960 ?"<td class=\"tdmid\">"+ sum +"</td>" : "") +
					"<td class=\"tdlt\" >"+ SETTINGS[row]["Domain"] +"</td>" +
					"<td class=\"tdmid\">"+ SETTINGS[row]["BlockedTimes"] +"</td>" +
					(w > 960 ?
					"<td class=\"tdlt\" >"+ SETTINGS[row]["SiteName"] +"</td>" +
					"<td class=\"tdmid\">"+ SETTINGS[row]["SiteID"] +"</td>" +
					"<td class=\"tdmid\">"+ SETTINGS[row]["UpdateTime"] +"</td>" +
					"<td class=\"tdmid\">"+ SETTINGS[row]["CreateTime"] +"</td>" : "") +
					"<td class=\"tdmidnoborder\" ><a class=\"taba\" href=\"javascript:modifyblockitem(SETTINGS[" + row + "][\'MD5\'], SETTINGS[" + row + "][\'Domain\'])\">"+ LAN["Modify"] +"</a></td>" +
					"<td class=\"tdmidnoborder\" ><a class=\"taba\" href=\"javascript:deleteblockitem(SETTINGS[" + row + "][\'MD5\'], SETTINGS[" + row + "][\'Domain\'])\">"+ LAN["Delete"] +"</a></td>" +
					"</tr>";

				sum++;
				n = !n;
			}
		} else {
			tabletext += "<tr class=\"tra\"><td class=\"tdmid\" colspan=\"" + cols + "\">No Data</td></tr>";
		}
				
		tabletext += 
			"<tr class=\"trfoot\">" +	
				"<td class=\"tdrtnoborder\" colspan=\"" + cols + "\">" +
					"<a class=\"taba\" href=\"javascript:showInputBox(1)\">"+ LAN["Batch Add"] +"</a>" +
				"</td>" +
			"</tr>" +
			"</table>";
		
		document.getElementById("frametable").innerHTML = tabletext;
	}


	function status_html(status, w) {
		var cols = (w > 960 ? 9 : 4);
		return 
		"<table>" +
			get_table_header(w) +
			"<tr class=\'tra\'>" +
			"<td class=\'tdmid\' colspan=\'" + cols + "\'>" + (status ? "Request timed out! Please refresh" : "<img class=\'mid\' src=\'images/loading.gif\'/>&nbsp;Processing...") + "</td>" +
			"</tr>" +
			"<tr class=\'trfoot\'>" +
			"<td class=\'tdrtnoborder\' colspan=\'" + cols + "\'>" +
			"</td>" +	
			"</tr>" +
		"</table>";
	}
	
	
	function CA_POST(param) {
		
		document.getElementById("frametable").innerHTML = status_html(0);
		
		var ca_post,
			url = window.location.href;
			
		try {
			if (window.XMLHttpRequest) {
				ca_post = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				ca_post = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			ca_post.onreadystatechange = function() {
				if (ca_post.readyState == 4 && ca_post.status == 200) {
					var v = ca_post.responseText.replace(/(^\s*)|(\s*$)/g, ""),
						success = I(v.substr(0, 1)),
						msg = v.substr(1);

					if (success == 1) {
						SETTINGS = eval(msg);
						blocked_ids_table_html(TB_WIDTH);
					} else {
						document.getElementById("frametable").innerHTML = msg;	
					}
				}
			}
			
			ca_post.open("POST", url, true);
			ca_post.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
			ca_post.send(param);
		} catch(e) {//alert(e.name + ": " + e.message);
			document.getElementById("frametable").innerHTML = status_html(1);	
		}
	
	}
	
	preload("images/loading.gif","images/inputclose.png")
	blocked_ids_table_html(TB_WIDTH);
 
</script>';
		
}

?>