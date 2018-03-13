<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Manage Blocked Pages PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

define('HTML_LOADING', '
		"<table>"+
		"<tr class=\'trhead\'>"+
		"<td class=\'tdhmid\' style=\'width:5%\' >No</td>"+
		"<td class=\'tdhlt\'  style=\'width:20%\'>'. $GLOBALS['indicator']['Blocked Pages'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Blocked Times'] .'</td>"+
		"<td class=\'tdhlt\'  style=\'width:10%\'>'. $GLOBALS['indicator']['Site Name'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Site ID'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Update Time'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Create Time'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:25%\' colspan=\'3\'>'. $GLOBALS['indicator']['Settings'] .'</td>"+
		"</tr>"+
		"<tr class=\'tra\'>"+
		"<td class=\'tdmid\' colspan=\'10\'><img class=\'mid\' src=\'images/loading.gif\'/>&nbsp;Processing...</td>"+
		"</tr>"+
		"<tr class=\'trfoot\'>"+
		"<td class=\'tdrt\' colspan=\'10\'>"+
		"</td>"+	
		"</tr>"+
		"</table>"
');

		
define('HTML_LOAD_ERROR', '
		"<table>"+
		"<tr class=\'trhead\'>"+
		"<td class=\'tdhmid\' style=\'width:5%\' >No</td>"+
		"<td class=\'tdhlt\'  style=\'width:20%\'>'. $GLOBALS['indicator']['Blocked Pages'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Blocked Times'] .'</td>"+
		"<td class=\'tdhlt\'  style=\'width:10%\'>'. $GLOBALS['indicator']['Site Name'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Site ID'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Update Time'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:10%\'>'. $GLOBALS['indicator']['Create Time'] .'</td>"+
		"<td class=\'tdhmid\' style=\'width:25%\' colspan=\'3\'>'. $GLOBALS['indicator']['Settings'] .'</td>"+
		"</tr>"+
	
		"<tr class=\'tra\'>"+
		"<td class=\'tdmid\' colspan=\'10\'>Request timed out! Please refresh</td>"+
		"</tr>"+

		"<tr class=\'trfoot\'>"+
		"<td class=\'tdrt\' colspan=\'10\'>"+
			"<form name=\'blockitemform\' method=\'POST\' action=\'\' >"+
				"<button name=\'blockitembtn\' id=\'blockitemsubmit\' type=\'submit\' class=\'middle\'>Batch Add</button>"+
				"<textarea name=\'blockitem\' id=\'blockitem\' rows=\'1\' maxlength=\'10240\' placeholder=\'required\' onBlur=\'checkValue(this.id)\' onfocus=\'setStyle(this.id)\'></textarea>"+
			"</form>"+	
		"</td>"+	
		"</tr>"+
		"</table>"
');


function blocked_pages_operation() {

		$param = empty($_POST['param']) ? '' : SDATA($_POST['param'], 1, 64);
		$key = empty($_POST['key']) ? '' : SDATA($_POST['key'], 1, 32);
		$value = empty($_POST['value']) ? '' : SDATA($_POST['value'], 7, 2048);
		
		$v = get_visa();//Authorize for access database
		$host = get_site_info('DataCenter');
		$tz = get_site_info('TimeZone');
		
		$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
		$q = $v . 'q=set domain&type=4&sid=' . $GLOBALS['SITEID'] . '&tz=' . $tz . '&param=' . $param . '&key=' . $key . '&value=' . $value;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);//不返回response头部信息 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
		$ret = curl_exec($ch);
		curl_close($ch);
		
		if ($ret === 'OK') $ret = '';
		
		return $ret;
	
}


function blocked_pages_table_html() {

	$sitename;
	$timezone;
	$sum = 0;
	$i=count($GLOBALS['SITES']);
	for ($row = 0; $row < $i; $row++) {
		if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
			$sitename = $GLOBALS['SITES'][$row]['SiteName'];
			$timezone = $GLOBALS['SITES'][$row]['TimeZone'];
			break;
		}
	}
	date_default_timezone_set($timezone);
		
	$tabletext = '
<div id="frametable" class="frametable">
	<table>
		<tr class="trhead">
			<td class="tdhmid" style="width:5%" >No</td>
			<td class="tdhlt"  style="width:20%">'. $GLOBALS['indicator']['Blocked Page'] .'</td>
			<td class="tdhmid" style="width:10%">'. $GLOBALS['indicator']['Blocked Times'] .'</td>
			<td class="tdhlt"  style="width:10%">'. $GLOBALS['indicator']['Site Name'] .'</td>
			<td class="tdhmid" style="width:10%">'. $GLOBALS['indicator']['Site ID'] .'</td>
			<td class="tdhmid" style="width:10%">'. $GLOBALS['indicator']['Update Time'] .'</td>
			<td class="tdhmid" style="width:10%">'. $GLOBALS['indicator']['Create Time'] .'</td>
			<td class="tdhmid" style="width:25%" colspan="3">'. $GLOBALS['indicator']['Settings'] .'</td>
		</tr>';
	
	$i = count($GLOBALS['DOMAINS']);
	if ($i)	{
		$n = true;
		$sum = 1;
		for ($row = 0; $row < $i; $row++) {
			
			if ($GLOBALS['DOMAINS'][$row]['DomainType'] == 4) {

				if ($n) {
					$tabletext .= '<tr class="tra">';
				} else {
					$tabletext .= '<tr class="trb">';
				}
				$tabletext .= '
				<td class="tdmid">'.$sum.'</td>
				<td class="tdlt" >'.$GLOBALS['DOMAINS'][$row]['Domain'].'</td>
				<td class="tdmid">'.$GLOBALS['DOMAINS'][$row]['BlockedTimes'].'</td>
				<td class="tdlt" >'.$sitename.'</td>
				<td class="tdmid">'.$GLOBALS['SITEID'].'</td>
				<td class="tdmid">'.date('m-d-Y H:i:s', $GLOBALS['DOMAINS'][$row]['UpdateTime']).'</td>
				<td class="tdmid">'.date('m-d-Y H:i:s', $GLOBALS['DOMAINS'][$row]['CreateTime']).'</td>
				<td class="tdmidnoborder"><a class="taba" href="javascript:modifyblockitem(\''.$GLOBALS['DOMAINS'][$row]['MD5'].'\',\''.$GLOBALS['DOMAINS'][$row]['Domain'].'\')">'. $GLOBALS['indicator']['Modify'] .'</a></td>
				<td class="tdmidnoborder"><a class="taba" href="javascript:addblockitem()">'. $GLOBALS['indicator']['Add'] .'</a></td>
				<td class="tdmidnoborder"><a class="taba" href="javascript:deleteblockitem(\''.$GLOBALS['DOMAINS'][$row]['MD5'].'\',\''.$GLOBALS['DOMAINS'][$row]['Domain'].'\')">'. $GLOBALS['indicator']['Delete'] .'</a></td>
				</tr>
				';

				$sum++;
				$n = !$n;
			}
		}
		
	}
	
	if ($sum == 1) {
		$tabletext .= '
		<tr class="tra">
			<td class="tdmid" colspan="10">No Data</td>
		</tr>
		';
	}
			
	$tabletext .= '
		<tr class="trfoot">	
			<td class="tdrt" colspan="10">
				<a class="taba" href="javascript:showInputBox(1)">'. $GLOBALS['indicator']['Batch Add'] .'</a>
			</td>
		</tr>
	</table>
</div>';
	
	$tabletext .= '
	<div id="frameinput" class="frameinput">		
		<div class="framebox">
			<div class="inputbox">
				<div class="inputtitle">'. $GLOBALS['indicator']['Add Blocked Page'] .'</div>
				<div class="closebtn"><a href="javascript:showInputBox(0)" style="height:24px; float:left;"><img src="images/inputclose.png" style="width:24px; height:24px;" alt="close" title="close"/></a></div>
				<form name="blockitemform" method="POST" action="" style="width:100%; height:100%; text-align:left; float:left;">
					<div style="width:100%; height:auto; padding:15px; padding-top:0px; text-align:left; float:left;">
						<p style="margin:0px; padding:0px; font-size:13px; height:30px; line-height:30px;">'. $GLOBALS['indicator']['Blocked Page'] .' :</p>
						<textarea name="blockitem" id="blockitem" maxlength="10240"></textarea>
					</div>
					<div style="width:100%; height:auto; padding:15px; padding-top:0px; text-align:left; float:left;">
						<button name="blockitembtn" id="blockitemsubmit" type="button" class="middle" onclick="showInputBox(0);batchaddblockitem();" style="float:left;">'. $GLOBALS['indicator']['Batch Add'] .'</button></td>
					</div>
				</form>	
			</div>
		</div>
	</div> ';
	
	return $tabletext;
	
}


function blocked_pages_html() {

	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
			
	$GLOBALS['TEXT_BODY'] = blocked_pages_table_html($GLOBALS['SITES'], $GLOBALS['SITEID'], $GLOBALS['DOMAINS']);
			
	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">

.framebody{width:auto; min-width:330px; height:auto; float:none; margin:15px; margin-top:0px; text-align:left; overflow-x:auto; overflow-y:hidden;}
.errmsg{margin-bottom:10px;}
.frametable{width:100%; height:auto; text-align:center;}
table{width:100%; min-width:960px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana; table-layout:fixed;}
td{border:#ccc 1px solid; font-size:12px; color:#555; padding-left:10px; padding-right:10px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}

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
.tdmid{text-align:center;}
.tdmidnoborder{text-align:center; border:0px;}

.plast{color:#999;}

button{width:120px; height:28px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333; float:right; margin-right:5px;}
textarea{width:-moz-calc(100% - 40px); width:-webkit-calc(100% - 40px); width: calc(100% - 40px); height:225px; line-height:20px; font-family:"Microsoft Yahei",Arial,Verdana; font-size:12px; color:#333; border:#ccc 1px solid; padding-left:5px; margin-right:10px; ime-mode:disabled; resize:none; float:left;}
img.mid{vertical-align:middle;}

.frameinput{width:100%; height:100%; display:none; position:fixed; top:0; left:0; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background:rgba(255,255,255,0.5); float:left; z-index:999999999;}
.framebox{width:auto; max-width:540px; min-width:360px; height:100%; min-height:362px; margin:auto; background:transparent; float:none; overflow:hidden;}
.inputbox{width:auto; height:360px; margin:auto; background:#eee; color:#555; float:none; border:#bbb 1px solid; border-radius:5px; position:relative; top:50%; margin-top:-180px;}
.closebtn{width:auto; height:auto; position:absolute; right:0px; top:0px; font-size:14px; color:#fff; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; background-color:transparent; margin:10px;}
.inputtitle{width:100%; height:auto; font-size:14px; color:#333; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background-color:transparent; float:left; margin-top:10px; margin-bottom:10px;}

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">

	function showInputBox(x) {
		if (x) {
			document.getElementById("frameinput").style.display = "block";
		} else {
			document.getElementById("frameinput").style.display = "none";
		}
	}

	function modifyblockitem(x,y) {
		var str=prompt("Modify Blocked Page",y);
		//str=str.toLowerCase();
		if (str == y) {
			alert("No changes");
		} else if (str) {
			str = escape(str); 
			var param = "param=modify&key=" + x + "&value=" + str;
			callAjax(param);
		}
	}
	
	function deleteblockitem(x,y) {
		if (confirm("Are you sure to delete the blocked page: " + y + "?")) {
			var param = "param=delete&key=" + x + "&value=" + y;
			callAjax(param);
		}
	}
	
	function addblockitem() {
		var def="Please input new page url here";
		var str=prompt("Add Blocked Page",def);
		if (str == def) {
			alert("No blocked page added");
		} else if (str) {
			str = escape(str); 
			//str=str.toLowerCase();
			var param = "param=add&value=" + str;
			callAjax(param);
		}
	}
	
	function batchaddblockitem() {
		var str=document.getElementById("blockitem").value ;
		if (str == "") {
			alert("No blocked page added");
		} else if (str) {
			str = escape(str); 
			//str=str.toLowerCase();
			var param = "param=batch&value=" + str;
			callAjax(param);
		}
	}

	
	function Resize() {
		return;
	}
	
	function callAjax(param) {
		
		document.getElementById("frametable").innerHTML = ' . HTML_LOADING . ';
		
		var myAjax,
			url = window.location.href;
			
		try {
			if (window.XMLHttpRequest) {
				myAjax = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			} else {
				myAjax = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			}
			
			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					document.getElementById("frametable").innerHTML = myAjax.responseText;
				}
			}
			
			myAjax.open("POST", url, true);
			myAjax.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
			myAjax.send(param);
		} catch(e) {//alert(e.name + ": " + e.message);
			document.getElementById("frametable").innerHTML = ' . HTML_LOAD_ERROR . ';
		}
	
	}
	
	var images = new Array()
	function preload() {
		for (i = 0; i < preload.arguments.length; i++) {
			images[i] = new Image();
			images[i].src = preload.arguments[i];
		}
	}
	preload("images/loading.gif","images/inputclose.png")
 
</script>';
		
}

?>