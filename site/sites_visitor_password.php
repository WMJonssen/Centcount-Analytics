<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Manage Visitor Password PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function modify_visitor_password() {
	
		$err = '';
		$con;
		$visitpw;
		$visittype;
		
		if (isset($_POST['enablevisit'])) {
			$visittype = $_POST['visittype'];
		} else {
			$visittype = 0;
		}
		
		if ($visittype == 1) {
			$visitpw = $_POST['visitpw'];
			if ($visitpw == '' || strlen($visitpw) < 6) {
				$err = '<br/>Visitor Password must be at least 6 characters long';
			}
		}
		
		if ($err === '') {
			
			$con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, DB_NAME_USER, 'Visitor Password', 'modify_visitor_password');
			if (!$con) return;
			
			if ($visittype == 1) {
				$sql = "UPDATE st{$GLOBALS['SITELIST_TABLE']} SET VisitType={$visittype}, VisitorPassword='{$visitpw}' WHERE SiteID={$GLOBALS['SITEID']}";
			} else {
				$sql = "UPDATE st{$GLOBALS['SITELIST_TABLE']} SET VisitType={$visittype} WHERE SiteID={$GLOBALS['SITEID']}";
			}

			if (mysqli_query($con, $sql)) {
				$err .= '<br/>Update visitor setting successfully';
			} else {
				$err .= '<br/>Update visitor setting failed';
			}

			mysqli_close($con);
		}
		
		return $err;
		
}


function visitor_password_html() {
	
	$sitename; $createtime; $sitestaus; $textsitestatus; $sitedes; $visitpw; $visittype;
	
	$i = count($GLOBALS['SITES']);
	for ($row = 0; $row < $i; $row++) {
		if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
			$sitename = $GLOBALS['SITES'][$row]['SiteName'];
			$sitedes = $GLOBALS['SITES'][$row]['SiteDescription'];
			$sitestatus = $GLOBALS['SITES'][$row]['SiteStatus'];
			$visitpw = $GLOBALS['SITES'][$row]['VisitorPassword'];
			$visittype = $GLOBALS['SITES'][$row]['VisitType'];
			break;
		}
	}
	
	switch ($sitestatus) {
	case 0:
		$textsitestatus = $GLOBALS['indicator']['Running'];
		break;
	case 1:
		$textsitestatus = $GLOBALS['indicator']['Stopped'];
		break;
	}
	
	switch ($visittype) {
		case 0://disable visit
			$textvisittype = '
			<tr><td class="hTD"><input type="checkbox" id="check1" name="enablevisit" value="0" onclick="checkboxchange(this)"/>Enable anonymous visit</td></tr>
			<tr><td class="hTD"><input type="radio" id="radio1" name="visittype" value="1" disabled="disabled" onclick="radiochange()"/>Visit with password</td></tr>
			<tr><td class="hTD"><input type="radio" id="radio2" name="visittype" value="2" disabled="disabled" onclick="radiochange()"/>Visit without password</td></tr>';
			$textpw = '<tr><td class="mTop"><input type="text" class="dIme" name="visitpw" id="Visit Password" maxlength="16" value="'.$visitpw.'" disabled="disabled" onblur="checkValue(this.id)" onfocus="setStyle(this.id)"/></td></tr>';
			break;
		case 1://enable visit with password
			$textvisittype = '
			<tr><td class="hTD"><input type="checkbox" id="check1" name="enablevisit" value="0" checked="checked" onclick="checkboxchange(this)"/>Enable anonymous visit</td></tr>
			<tr><td class="hTD"><input type="radio" id="radio1" name="visittype" value="1" checked="checked" onclick="radiochange()"/>Visit with password</td></tr>
			<tr><td class="hTD"><input type="radio" id="radio2" name="visittype" value="2" onclick="radiochange()"/>Visit without password</td></tr>';
			$textpw = '<tr><td class="mTop"><input type="text" class="dIme" name="visitpw" id="Visit Password" maxlength="16" value="'.$visitpw.'" onblur="checkValue(this.id)" onfocus="setStyle(this.id)"/></td></tr>';
			break;
		case 2://enable visit without password
			$textvisittype = '
			<tr><td class="hTD"><input type="checkbox" id="check1" name="enablevisit" value="0" checked="checked" onclick="checkboxchange(this)"/>Enable anonymous visit</td></tr>
			<tr><td class="hTD"><input class="tools" type="radio" id="radio1" name="visittype" value="1" onclick="radiochange()"/>Visit with password</td></tr>
			<tr><td class="hTD"><input type="radio" id="radio2" name="visittype" value="2" checked="checked" onclick="radiochange()"/>Visit without password</td></tr>';
			$textpw = '<tr><td class="mTop"><input type="text" class="dIme" name="visitpw" id="Visit Password" maxlength="16" value="'.$visitpw.'" disabled="disabled" onblur="checkValue(this.id)" onfocus="setStyle(this.id)"/></td></tr>';
			break;
	}

	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
			
	$GLOBALS['TEXT_BODY'] = '
<div class="dTitle">'. $GLOBALS['indicator']['Visitor Password'] .'</div>
<form name="modifysiteinfoform" method="POST" action="" >
	<table>
		<tr><td>'. $GLOBALS['indicator']['Site Name'] .' : '.$sitename.'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Site ID'] .' : '.$GLOBALS['SITEID'].'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Site Status'] .' : '.$textsitestatus.'</td></tr>

		'.$textvisittype.'
					
		<tr><td>'. $GLOBALS['indicator']['Visitor Password'] .' : </td></tr>
		'.$textpw.'
		
		<tr><td>'. $GLOBALS['indicator']['Visit Url'] .' : </td></tr>
		<tr><td class="mTop"><textarea name="VisitUrl" id="Visit Url" rows="4" maxlength="1024" readonly onclick="copyText(this.id)">http://' . $_SERVER['HTTP_HOST'] . '/visitor.php?id='.$GLOBALS['USERID'].'&siteid='.$GLOBALS['SITEID'].'</textarea></td></tr>
				
		<tr><td style="text-align:venter;"><button name="modifyvisitorpw" value="modifyvisitorpw" id="modifyvisitorpwsubmit" type="submit" class="middle">'. $GLOBALS['indicator']['Apply'] .'</button></td></tr>		
	</table>
</form>';
			
	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">
	
.errmsg{margin-bottom:15px;}

.framebody{width:calc(100% - 30px); max-width:640px; height:auto; padding:15px; padding-top:0px; margin:0; text-align:center; border:0; float:left;}
.dTitle{width:calc(100% - 30px); height:40px; line-height:40px; padding-left:15px; padding-right:15px; font-family:Verdana,"Microsoft Yahei",Arial; font-size:16px; text-align:left; background-color:#555; color:#fff; float:left;}

td{width:100%; height:auto; font-size:13px; line-height:18px; border:0px; border-collapse:collapse; border-spacing:0; margin:0; margin-top:15px; text-align:left; float:left;}
tr,tbody,table,form{width:100%; border:0px; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana;}

input, select, textarea{width:calc(100% - 13px); height:auto; padding:5px; margin:0; vertical-align:middle; border:#ccc 1px solid; font-family:"Microsoft Yahei",Arial,Verdana; float:left;} 
select{width:calc(100% - 1px);}
textarea{color:#555; resize:none; ime-mode:disabled;}
button{width:150px; height:30px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333;}
#check1, #radio1, #radio2{width:30px; height:15px; padding:0;}

.hTD{height:13px; line-height:15px; padding:0;}
.dIme{ime-mode:disabled;}
.mTop{margin-top:0px;}
.mTop15{margin-top:15px;}
.mTop30{margin-top:30px;}
b{font-size:14px; color:#111;}

.ctext{width:315px; height:26px; text-align:center;}
.ccheckbox{width:auto; height:auto; margin-top:7px; border:0px;} 
.cradio{width:auto; height:auto; margin-top:7px; margin-left:30px; border:0px;} 

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">
	
	function setStyle(x) {
		document.getElementById(x).style.borderColor="#39F";
	}
	
	function checkValue(x) {
		if (document.getElementById(x).value == "") {
			document.getElementById("errormsg").innerHTML = document.getElementById(x).id + " must not be empty";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
			return false;
		} else {
			document.getElementById("errormsg").style.display = "";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	function checkboxchange(x) {
		if (x.checked) {
			document.getElementById("radio1").disabled=false;
			document.getElementById("radio2").disabled=false;
			document.getElementById("radio1").checked=true;
			document.getElementById("Visit Password").disabled=false;
		} else {
			document.getElementById("radio1").disabled=true;
			document.getElementById("radio2").disabled=true;
			document.getElementById("radio1").checked=false;
				document.getElementById("radio2").checked=false;
			document.getElementById("Visit Password").disabled=true;
		}
	}
	
	function radiochange() {
		if (document.getElementById("radio1").checked) {
			document.getElementById("Visit Password").disabled=false;
		} else {
			document.getElementById("Visit Password").disabled=true;
		}
	}

	function copyText(id) {		
		if (window.clipboardData) { 
			var txt=document.getElementById(id).value; 
			window.clipboardData.clearData(); 
			window.clipboardData.setData("Text",txt);
			alert("You have already copy the url to the clipboard. Please press Ctrl + V to paste the url into browser address field.");
		} else {
			var obj=document.getElementById(id);
			obj.select();
			alert("You can press Ctrl + C to copy the url to the clipboard. And press Ctrl + V to paste the url into browser address field.");
		}
	} 
	
	function Resize() {
		return;
	}

</script>';
	
}

?>

