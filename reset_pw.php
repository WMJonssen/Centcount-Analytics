<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Reset Password PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/
session_name('CASESSID');
session_start();
header('Content-type: text/html; charset=utf-8');

@require './config/config_security.php';
@require './config/config_common.php';
require 'language.php';
require 'html.php';

$id = empty($_GET['id']) ? 0 : (int)$_GET['id'];
$verify_code = empty($_GET['vcode']) ? 0 : (int)$_GET['vcode'];
$success = 0;
$err = '';

if ($id === 0 || $verify_code === 0) {
	die('Access Denied.');
}

if ($_POST) {

	if (empty($_POST['password'])) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrPW1'];//'<br/>Password must not be empty';
	} else if (strlen($_POST['password']) < 6) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrPW2'];//'<br/>Password must be at least 6 characters long';
	} else if (strlen($_POST['password']) > 16) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrPW3'];//'<br/>Password max-length is 16 characters long';
	}
	
	if (empty($_POST['confirm'])) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrReenter1'];//'<br/>Re-enter password must not be empty';
	} else if (strlen($_POST['confirm']) < 6) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrReenter2'];//'<br/>Re-enter password must be at least 6 characters long';
	} else if (strlen($_POST['confirm']) > 16) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrReenter3'];//'<br/>Re-enter password max-length is 16 characters long';
	} else if ($_POST['password'] != $_POST['confirm']) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrReenter4'];//'<br/>Password must be the same as Re-enter';
	}
	
	$vcode = empty($_SESSION['vcode']) ? '' : $_SESSION['vcode'];
	$_SESSION['vcode'] = mt_rand(1E6,1E9);
	
	if (empty($_POST['captcha'])) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrCaptcha1'];//'<br/>Captcha must not be empty';
	} else if (strlen($_POST['captcha']) !== 4) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrCaptcha2'];//'<br/>Captcha must be 4 characters long';
	} else if ($vcode !== strtolower($_POST['captcha']) || $vcode === '') {
		$err .= '<br/>' . $GLOBALS['language']['RegErrCaptcha3'];//'<br/>Wrong captcha';
	}
	
	if ($err === '') {
		$err = reset_pw($id, $verify_code, $_POST['password'], $success);
	}
	
	if (substr($err,0,5) === '<br/>') $err = substr($err,5);	
	
}


function reset_pw($id, $verify_code, $pw, &$success) {

	$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
	if (mysqli_connect_errno($con))
	{
		$err = 'Could not connect server. Please contact Administrator!';
		return;
	}
	
	$db_selected = mysqli_select_db($con, DB_NAME_USER);
	if (!$db_selected)
	{
		$err .= '<br/>Could not connect database. Please contact Administrator!';
		mysqli_close($con);
		return $err;
	}
	
	//check username 
	$result = mysqli_query($con, "SELECT ActivateCode,ActivateTime FROM User WHERE UserID={$id}");
	if (mysqli_num_rows($result) == 1) {
		while ($row = mysqli_fetch_assoc($result))
		{
			$acode = $row['ActivateCode'];
			$atime = $row['ActivateTime'];
			break;
		}
		mysqli_free_result($result);
	} else {
		$err .= '<br/>' . $GLOBALS['language']['ResetFailed1'];// 'Reset password failed! User is not exist. Error No: 4001';
		mysqli_close($con);
		return $err;
	}
	//check activation code expires time
	$ntime = time();
	
	if ($atime < $ntime) {
		$err .= '<br/>' . $GLOBALS['language']['ResetFailed2'];//Reset password failed! Activation-Code has been expired. Error No: 4002';
		mysqli_close($con);
		return $err;
	}
	//match activation code
	if ($verify_code != $acode) {
		$err .= '<br/>' . $GLOBALS['language']['ResetFailed3'];// 'Reset password failed! Activation-Code is not valid. Error No: 4003';
		mysqli_close($con);
		return $err;
	}
	
	
	//update User information
	$md5pw = md5($pw);
	$sql = "UPDATE User SET Password = '{$md5pw}', Activated = 1, ActivateCode = 0, ActivateTime = 0 WHERE UserID={$id}";
	if (mysqli_query($con, $sql)) {
		$err .= '<br/>' . $GLOBALS['language']['ResetSuccess1'];// 'Reset password was successfully!';
		$success = 1;
	} else {
		$err .= '<br/>' . $GLOBALS['language']['ResetFailed4'];// 'Reset password was failed! Error No: 4004';
	}
	
	mysqli_close($con);

	return $err;
}

?> 

<!DOCTYPE html>

<head>

<title>Centcount Analytics - Reset Password</title>
<?php echo META_TEXT() ?>
<?php echo CA_JS_CODE ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

</head>

<body>


<?php echo HTML_HEADER(); ?>


<div id="title">
	<h1><?php echo $GLOBALS['language']['ResetPageTitle']; ?></h1>
</div>


<div id="bodyframe">

	<div class="framebody">
	
		<?php echo ($err != '' && $success == 0 ? '<div class="errmsg" id="errormsg" style="display:block;">'.$err.'</div>' : '<div class="errmsg" id="errormsg"></div>'); ?>
	
		<?php if ($success > 0) echo '<div class="successmsg" id="successmsg">'. $err .'</div>'; ?>
	
		<?php if ($success > 0) echo '<p></p><div class="successmsg" id="login"><a href="login.php">' .$GLOBALS['language']['PageLogIn']. '</a></div>'; ?>
		

		<div class="frameform" <?php if ($success) echo 'style="display:none;"'; ?>>

			<form name="resetpw" method="POST" action="">

			  <table>

				<tr>
					<td><input class="pwd" type="password" name="password" id="Password" maxlength="16" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" placeholder="<?php echo $GLOBALS['language']['RegTipMinlength']; ?>" onBlur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td>
				</tr>
				
				<tr id="pwdstrong" style="display:none; width:100%;">
					<td style="height:30px; text-align:left;">
						<?php echo $GLOBALS['language']['RegPageStrength']; ?>: 
						<div id="pwdstrong_img" class="strength"></div>
					</td>
				</tr>
			
				<tr>
					<td><input class="pwd" type="password" name="confirm" id="Confirm" maxlength="16" value="<?php echo isset($_POST['confirm']) ? $_POST['confirm'] : ''; ?>" placeholder="<?php echo $GLOBALS['language']['RegTipReenter']; ?>" onBlur="matchPW('Password', this.id)" onfocus="setStyle(this.id)"/></td>
				</tr>
			
				<tr>
					<td><input class="short" type="text" name="captcha" id="Captcha" maxlength="4" placeholder="<?php echo $GLOBALS['language']['PageCaptcha']; ?>" onBlur="checkCaptcha(this.id)" onfocus="setStyle(this.id)" />
						<div class="suggestion" styel="text-align:left;">
							<img src="validcode.php?rnd=<?php echo mt_rand(1E7,9E8); ?>" style="width:130px; height:34px; float:left; margin-right:6px;" id="code"/>
							<a class="fresh" href="javascript:changeCode()" title="<?php echo $GLOBALS['language']['PageRefresh']; ?>"></a>
						</div>
					</td>
				</tr>
				
				<tr>
					<td><button id="reg" type="submit" name="submit"><?php echo $GLOBALS['language']['PageReset']; ?></button></td>
				</tr>
				
				<tr>
					<td class="sug">
						<a style="float:left;" href="login.php"><?php echo $GLOBALS['language']['PageLogIn']; ?></a>
					</td>
				</tr>
			
			  </table>

			</form>
			
		</div>
	
	</div>

</div>


<?php echo HTML_FOOTER(); ?>

<?php echo JS_COMMON(); ?>

<script type="text/javascript">
	
	function changeCode() { 
		document.getElementById("code").src = "validcode.php?id=" + Math.random();
	}
	
	
	function setStyle(x) {
		document.getElementById(x).style.borderColor = "#39F";
	}
	
	function checkValue(x) {
		if (document.getElementById(x).value == "") {
			document.getElementById("errormsg").innerHTML = x + "<?php echo $GLOBALS['language']['RegErrCommonTip']; ?>";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
			return false;
		}
	} 
	
	function checkPW(x) {
		var pw = document.getElementById(x).value;
		var strongPW = 0;
		var reg = /[~!@#$%^&*()_+|}{":?><]/;
		var reg2 = /[a-zA-Z]/;
		var reg3 = /[0-9]/;
		
		if (pw.length < 6) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrPW2']; ?>";//"Password must be at least 6 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else if (pw.length > 16) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrPW3']; ?>";//"Password must be at least 6 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
		if (reg.test(pw)) strongPW++;
		if (reg2.test(pw)) strongPW++;
		if (reg3.test(pw)) strongPW++;

		if (strongPW > 0) {
			switch (strongPW) {
			case 1:
				document.getElementById("pwdstrong_img").style.backgroundPosition = "5px -39px";
				break;
			case 2:
				document.getElementById("pwdstrong_img").style.backgroundPosition = "5px -55px";
				break;
			case 3:
				document.getElementById("pwdstrong_img").style.backgroundPosition = "5px -71px";
				break;
			}
			document.getElementById("pwdstrong").style.display = "";
		} else {
			document.getElementById("pwdstrong").style.display = "none"; 
		}
		
	}
	
	function matchPW(x,y) {
		var pw = document.getElementById(x).value;
		var cpw = document.getElementById(y).value;
		if (pw != cpw) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrReenter4']; ?>";//"Password must be the same as re-enter";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(y).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(y).style.borderColor = "#ccc";
		} 
	} 
	
	
	function checkCaptcha(x) {
		if (checkValue(x) == false) return;
		var captcha = document.getElementById(x).value;

		if (captcha.length != 4) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrCaptcha2']; ?>";//"Captcha must be 4 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		} 
	}


function preload(){
	for (var i = 0; i < preload.arguments.length; i++) (new Image).src = preload.arguments[i];
}

preload("images/cc_images.png")
	
</script>

</body>
</html>