<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Modify Site Information PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/14/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

/*********** Load Other Config Start **********/
@require_once './ca_version.php';//load CA Version File
/********* Load Other Config End ********/

function settings_about_ca_html() {
			
	$GLOBALS['TEXT_BODY'] = '
<div class="dTitle mTop15">'. $GLOBALS['indicator']['About CA'] .'</div>
<p>
	<b>'. $GLOBALS['language']['CA'] .' Free Edition</b><br>
	Code Name: Centurion<br>
	Version: '.CA_VERSION.'<br>
	Update: ' .CA_UPDATE. '<br>
	License: Free License (For Personal Non-Commercial User Only)<br>
	Copyright © 2015 - '.date('Y').' <a href="https://www.centcount.com/about.php" target="_blank">WM Jonssen</a>. All rights reserved.<br>
	Centcount Analytics is dual-licensed under the Free License and Commercial License.
</p>
<p id="updateResult" class="mTop"></p>


<div class="dTitle mTop30">'. $GLOBALS['indicator']['Thanks'] .'</div>
<p>
	<b>IP Address Library Provider:</b><br>
	<a href="https://www.ip2location.com" target="_blank">IP2Location</a><br>
	<a href="https://www.maxmind.com" target="_blank">GeoIP</a><br>
</p>';

	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">
	
.errmsg{margin-top:15px;}

.framebody{width:calc(100% - 30px); max-width:640px; height:auto; padding:15px; padding-top:0px; margin:0; text-align:center; border:0; float:left;}
.dTitle{width:calc(100% - 30px); height:40px; line-height:40px; padding-left:15px; padding-right:15px; font-family:Verdana,"Microsoft Yahei",Arial; font-size:16px; text-align:left; background-color:#555; color:#fff; float:left;}
button{width:150px; height:30px; display:none; margin-top:0px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333;}

.mTop{margin-top:0px;}
.mTop15{margin-top:15px;}
.mTop30{margin-top:30px;}
b{color:#111;}
p{width:100%; height:auto; line-height:28px; font-size:14px; text-align:left; margin-top:15px; float:left;}

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">
	
	function Resize() {
		return;
	}

	CheckUpdate();
	function CheckUpdate() {
		try {

			var APIUrl = "https://www.centcount.com/update_check.php?l=0", 
				myAjax = new XMLHttpRequest(); 

			myAjax.onreadystatechange = function() {
				if (myAjax.readyState == 4 && myAjax.status == 200) {
					var msg = myAjax.responseText;

					if (msg != "" && msg != "'.CA_VERSION.'") {
						set_txt("updateResult", "A new Centcount Analytics update is now available. New Update Version: " + msg + "<br><a href=\"https://www.centcount.com/license_price.php\" target=\"_blank\">Update Now</a>");
						show_id("updateCheck");
					} else if (msg != "" && msg == "'.CA_VERSION.'") {
						set_txt("updateResult", "Centcount Analytics is up to date");
					} else {
						set_txt("updateResult", "Update check failed. Please check later.");
					} 
				} else if (myAjax.readyState == 4 && myAjax.status == 404) {
					set_txt("updateResult", "Update check failed. Please check again.");
				}
			}
			
			myAjax.open("GET", APIUrl, true);
			myAjax.send();

		} catch(z) {
			alert("Update Check Error: " + z.name + " - " + z.message);
		}
	}

</script>';
		
}

?>
