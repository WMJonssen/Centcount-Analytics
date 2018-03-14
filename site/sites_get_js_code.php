<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Get CA Javascript Code PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function get_js_code_html() {
	
	$sitename;
	$server;
	
	$i = count($GLOBALS['SITES']);
	for ($row = 0; $row < $i; $row++) {
		if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
			$sitename = $GLOBALS['SITES'][$row]['SiteName'];
			$server = $GLOBALS['SITES'][$row]['DataCenter'];
			break;
		}
	}
	
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
	//(w._caq = w._caq || []).push(["_requestTimeCA",(new Date).getTime()]);
	$GLOBALS['TEXT_BODY'] = '
<div class="dTitle">'. $GLOBALS['indicator']['JS Code'] .'</div>
<table>
	
		<tr><td>'. $GLOBALS['indicator']['Site Name'] .' : '.$sitename.'</td></tr>

		<tr><td>'. $GLOBALS['indicator']['Site ID'] .' : '.$GLOBALS['SITEID'].'</td></tr>

		<tr><td><b>CA '. $GLOBALS['indicator']['JS Code'] .' : </b></td></tr>
	
		<tr><td class="mTop">
<textarea name="CAJS" id="JSCODE1" readonly onclick="copyText(this.id)" rows="14">
<script type="text/javascript">
	(function(w, d, s, a, m) {
	w._caq_rt = w._caq_rt || (new Date).getTime();
	a = d.createElement(s),
	a.async = 1;
	a.src = "//'.$server.'/ca.php?siteid='.$GLOBALS['SITEID'].'&r=" + Math.random();
	m = d.getElementsByTagName(s)[0];
	m.parentNode.insertBefore(a, m)
	})(window, document, "script");
</script>
</textarea>
		</td></tr>

		<tr><td><b>'. $GLOBALS['language']['HOW TO USE'] .' :</b></td></tr>
		<tr><td class="mTop">'. $GLOBALS['language']['JS Usage'] .'</td></tr>
			
</table>';
			
	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">
	
.errmsg{margin-bottom:15px;}

.framebody{width:calc(100% - 30px); max-width:640px; height:auto; padding:15px; padding-top:0px; margin:0; text-align:center; border:0; float:left;}
.dTitle{width:calc(100% - 30px); height:40px; line-height:40px; padding-left:15px; padding-right:15px; font-family:Verdana,"Microsoft Yahei",Arial; font-size:16px; text-align:left; background-color:#555; color:#fff; float:left;}

td{width:100%; height:auto; font-size:13px; line-height:18px; border:0px; border-collapse:collapse; border-spacing:0; margin:0; margin-top:15px; text-align:left; float:left;}
tr,tbody,table,form{width:100%; border:0px; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana;}

input, select, textarea{width:calc(100% - 13px); height:auto; padding:5px; margin:0; vertical-align:middle; border:#ccc 1px solid; ime-mode:disabled; font-family:"Microsoft Yahei",Arial,Verdana; float:left;} 
select{width:calc(100% - 1px);}
textarea{color:#555; resize:none; ime-mode:disabled;}
button{width:150px; height:30px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333;}

.mTop{margin-top:0px;}
b{font-size:14px; color:#111;}

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">

	function copyText(id) {
		if (window.clipboardData) { 
			var txt = document.getElementById(id).value; 
			window.clipboardData.clearData(); 
			window.clipboardData.setData("Text",txt);
			//alert("You have already copy the url to the clipboard. Please press Ctrl + V to paste the url into browser address field.");
		} else {
			var obj = document.getElementById(id);
			obj.select();
			//alert("You can press Ctrl + C to copy the url to the clipboard. And press Ctrl + V to paste the url into browser address field.");
		}
	}
	
	function Resize() {
		return;
	}

</script>';
	
}

?>

