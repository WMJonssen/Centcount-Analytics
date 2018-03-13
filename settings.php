<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Modify Site Information PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function change_settings() {
	
		$err = '';
		$con;
		
		if (isset($_POST['ChangePassword'])) {

			if (empty($_POST['OldPassword'])) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW1'];//'<br/>Password must not be empty';
			} else if (strlen(trim($_POST['OldPassword'])) < 6) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW2'];//'<br/>Password must be at least 6 characters long';
			} else if (strlen(trim($_POST['OldPassword'])) > 16) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW3'];//'<br/>Password max-length is 16 characters long';
			}
			
			if (empty($_POST['NewPassword'])) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW1'];//'<br/>Password must not be empty';
			} else if (strlen(trim($_POST['NewPassword'])) < 6) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW2'];//'<br/>Password must be at least 6 characters long';
			} else if (strlen(trim($_POST['NewPassword'])) > 16) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW3'];//'<br/>Password max-length is 16 characters long';
			}
	
			if (empty($_POST['ConfirmPassword'])) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrReenter1'];//'<br/>Re-enter password must not be empty';
			} else if (strlen($_POST['ConfirmPassword']) < 6) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrReenter2'];//'<br/>Re-enter password must be at least 6 characters long';
			} else if (strlen($_POST['ConfirmPassword']) > 16) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrReenter3'];//'<br/>Re-enter password max-length is 16 characters long';
			} else if (trim($_POST['NewPassword']) != $_POST['ConfirmPassword']) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrReenter4'];//'<br/>Password must be the same as Re-enter';
			}
			
		} else if (isset($_POST['ChangeEmail'])) {
			
			if (empty($_POST['LoginPassword'])) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW1'];//'<br/>Password must not be empty';
			} else if (strlen(trim($_POST['LoginPassword'])) < 6) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW2'];//'<br/>Password must be at least 6 characters long';
			} else if (strlen(trim($_POST['LoginPassword'])) > 16) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrPW3'];//'<br/>Password max-length is 16 characters long';
			}
			
			if (empty($_POST['NewEmail'])) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrEmail1'];//'Email must not be empty';
			} else if (!filter_var($_POST['NewEmail'], FILTER_VALIDATE_EMAIL)) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrEmail2'];//'Email is not valid';
			} else if ($_POST['NewEmail'] == $GLOBALS['USER']) {
				$err .= '<br/>' . $GLOBALS['language']['RegErrEmail4'];//'New email is same as current email, Please check it again',
			}
			
		}
			
		if ($err === '') {

			$con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, DB_NAME_USER, 'Settings', 'change_settings');
			if ($con === false) return;
			
			if (isset($_POST['ChangePassword'])) {
				$oldpassword = md5(SDATA($_POST['OldPassword'], 1, 16, 0, $con));
				$newpassword = md5(SDATA($_POST['NewPassword'], 1, 16, 0, $con));
				
				$sql = "SELECT Password FROM User WHERE Username='{$GLOBALS['USER']}'";
				$result = mysqli_query($con, $sql);
				if ($result && mysqli_num_rows($result)) {			
					$row = mysqli_fetch_assoc($result);
					if ($row['Password'] !== $oldpassword) {
						$err .= '<br/>Wrong password';
					}
					mysqli_free_result($result);
				} else {
					$err .= '<br/>Check password error';
				}
				
				if ($err) {
					mysqli_close($con); 
					return $err;
				}
				
				$sql = "UPDATE User SET Password='{$newpassword}' WHERE Username='{$GLOBALS['USER']}'";
				if (mysqli_query($con, $sql)) {
					$err .= '<br/>Change password successfully';
				} else {
					$err .= '<br/>Change password failed';
				}
			} else if (isset($_POST['ChangeEmail'])) {
				$oldpassword = md5(SDATA($_POST['LoginPassword'], 1, 16, 0, $con));
				$newemail = SDATA($_POST['NewEmail'], 1, 255, 0, $con);
				
				$sql = "SELECT Password FROM User WHERE Username='{$GLOBALS['USER']}'";
				$result = mysqli_query($con, $sql);
				if ($result && mysqli_num_rows($result)) {			
					$row = mysqli_fetch_assoc($result);
					if ($row['Password'] !== $oldpassword) {
						$err .= '<br/>Wrong password';
					}
					mysqli_free_result($result);
				} else {
					$err .= '<br/>Check password error';
				}
				
				if ($err) {
					mysqli_close($con); 
					return $err;
				}
				
				$sql = "UPDATE User SET Username='{$newemail}' WHERE Username='{$GLOBALS['USER']}'";
				if (mysqli_query($con, $sql)) {
					$_SESSION['user'] = $newemail;
					$GLOBALS['USER'] = $newemail;
					$err .= '<br/>Change email successfully';
				} else {
					$err .= '<br/>Change email failed';
				}
			}
			
			mysqli_close($con);
		}
		
		return $err;
}


function settings_html() {

	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_SETTINGS;
			
	$GLOBALS['TEXT_BODY'] = '
<div class="dTitle mTop15">'. $GLOBALS['indicator']['Change Password'] .'</div>
<form name="ChangePasswordForm" autocomplete="off" method="POST" action="">
	<table>
		<tr><td>'. $GLOBALS['indicator']['Current Password'] .' : </td></tr>
		<tr><td class="mTop"><input type="password" class="dIme" name="OldPassword" id="Old Password" maxlength="16" value="" placeholder="type your current password here" onblur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td></tr>
		
		<tr><td>'. $GLOBALS['indicator']['New Password'] .' : </td></tr>
		<tr><td class="mTop"><input type="password" class="dIme" name="NewPassword" id="New Password" maxlength="16" value="" placeholder="type your new password here" onblur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td></tr>
		
		<tr><td>'. $GLOBALS['indicator']['Confirm Password'] .' : </td></tr>
		<tr><td class="mTop"><input type="password" class="dIme" name="ConfirmPassword" id="Confirm Password" maxlength="16" value="" placeholder="confirm your new password" onblur="matchPW(\'New Password\', this.id)" onfocus="setStyle(this.id)"/></td></tr>
				
		<tr><td><button name="ChangePassword" value="ChangePassword" id="Change Password" type="submit" class="middle">'. $GLOBALS['indicator']['Change Password'] .'</button></td></tr>
	</table>
</form>


<div class="dTitle mTop30">'. $GLOBALS['indicator']['Change Email'] .'</div>
<form name="ChangeEmailForm" autocomplete="off" method="POST" action="">
	<table>
		<tr><td>'. $GLOBALS['indicator']['Current Email'] .' : <b>' . $GLOBALS['USER'] . '</b></td></tr>
		
		<tr><td>'. $GLOBALS['indicator']['New Email'] .' : </td></tr>
		<tr><td class="mTop"><input type="text" class="dIme" name="NewEmail" id="New Email" maxlength="255" value="" placeholder="type your new email here" onblur="checkEmail(this.id, \'' . $GLOBALS['USER'] . '\')" onfocus="setStyle(this.id)"/></td></tr>
		
		<tr><td>'. $GLOBALS['indicator']['Login Password'] .' : </td></tr>
		<tr><td class="mTop"><input type="password" class="dIme" name="LoginPassword" id="Login Password" maxlength="16" value="" placeholder="type your login password here" autocomplete="new-password" onblur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td></tr>
				
		<tr><td><button name="ChangeEmail" value="ChangeEmail" id="Change Email" type="submit" class="middle">'. $GLOBALS['indicator']['Change Email'] .'</button></td></tr>
	</table>
</form>';

	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">
	
.errmsg{margin-top:15px;}

.framebody{width:calc(100% - 30px); max-width:640px; height:auto; padding:15px; padding-top:0px; margin:0; text-align:center; border:0; float:left;}
.dTitle{width:calc(100% - 30px); height:40px; line-height:40px; padding-left:15px; padding-right:15px; font-family:Verdana,"Microsoft Yahei",Arial; font-size:16px; text-align:left; background-color:#555; color:#fff; float:left;}

td{width:100%; height:auto; font-size:13px; line-height:18px; border:0px; border-collapse:collapse; border-spacing:0; margin:0; margin-top:15px; text-align:left; float:left;}
tr,tbody,table,form{width:100%; border:0px; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana;}

input, select, textarea{width:calc(100% - 13px); height:auto; padding:5px; margin:0; vertical-align:middle; border:#ccc 1px solid; font-family:"Microsoft Yahei",Arial,Verdana; float:left;} 
select{width:calc(100% - 1px);}
textarea{color:#555; resize:none; ime-mode:disabled;}
button{width:150px; height:30px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333;}

.dIme{ime-mode:disabled;}
.mTop{margin-top:0px;}
.mTop15{margin-top:15px;}
.mTop30{margin-top:30px;}
b{font-size:14px; color:#111;}

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">
	
	function setStyle(x) {
		document.getElementById(x).style.borderColor="#39F";
	}
	
	function checkValue(x) {
		if (document.getElementById(x).value == "") {
			document.getElementById("errormsg").innerHTML = x + "' . $GLOBALS['language']['RegErrCommonTip'] . '";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
			return false;
		} else {
			document.getElementById("errormsg").style.display = "";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	function checkEmail(x,y) {
		var email = document.getElementById(x).value;
		if (email == "") {
			document.getElementById(x).style.borderColor = "#ccc";
			return;
		}
		
		var reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
		if (reg.test(email) == false) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrEmail2'] . '";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
		
		if (email == y) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrEmail4'] . '";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		}
	}
	
	function checkPW(x) {
		var pw = document.getElementById(x).value;
		if (pw == "") {
			document.getElementById(x).style.borderColor = "#ccc";
			return;
		}

		if (pw.length < 6) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrPW2'] . '";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else if (pw.length > 16) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrPW3'] . '",
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	function matchPW(x,y) {
		var cpw = document.getElementById(y).value;
		if (cpw == "") {
			document.getElementById(y).style.borderColor = "#ccc";
			return;
		}
		
		var pw = document.getElementById(x).value;
		if (pw !== cpw) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrReenter4'] . '";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(y).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(y).style.borderColor = "#ccc";
		} 
	}
	
	function Resize() {
		return;
	}

</script>';
		
}

?>