<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Forgot Password PHP Code *
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


$success = 0;
$err = '';


if ($_POST) {

	if (empty($_POST['email'])) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrEmail1'];//'Email must not be empty';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrEmail2'];//'Email is not valid';
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
		$err = forgot($_POST['email'], $success);
	}
	
	if (substr($err, 0, 5) === '<br/>') $err = substr($err, 5);		 
}


function forgot($user, &$success) {
	$err = '';

	$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
	if (mysqli_connect_errno($con)) {
		$err = 'Could not connect mysql host. Please contact Administrator!';
		return $err;
	}
	
	$user = mysqli_real_escape_string($con, $user);
	
	$db_selected = mysqli_select_db($con, DB_NAME_USER);
	if (!$db_selected) {
		$err = '<br/>Could not use database. Please contact Administrator!';
		mysqli_close($con);
		return $err;
	}
	
	//check useremail
	$result = mysqli_query($con, "SELECT * FROM User WHERE Username='{$user}'");
	if (mysqli_num_rows($result) == 1) {
		while ($row = mysqli_fetch_assoc($result))
		{
			$apw = $row['Password'];
			$actived = $row['Activated'];
			$id = $row['UserID'];
			break;
		}
		mysqli_free_result($result);
	} else {
		$err = '<br/>' . $GLOBALS['language']['ForgotFailed1'];//Email is not exist. Error No: 3001
		mysqli_close($con);
		return $err;
	}

	$ActivateCode = mt_rand(1E8,2E9);
	$CreateTime = time();
	$ActivateTime = $CreateTime + 604800;//7*24*60*60;
		
	//reset password
	$sql = "UPDATE User SET ActivateCode=$ActivateCode, ActivateTime=$ActivateTime WHERE Username='{$user}'";
	if (mysqli_query($con, $sql)) {
		if (autoresponse($user, $id, $ActivateCode)) {
			$err .= '<br/>' . $GLOBALS['language']['ForgotSuccess1'];//Request was successfully! Please check the verify-mail to reset new password within 7 days
			$success = 1;
		} else {
			$err .= '<br/>' . $GLOBALS['language']['ForgotFailed2'];//Send verify email failed! Please try again or send error information to us. Error No: 3002
		}	
	} else {
		$err .= '<br/>' . $GLOBALS['language']['ForgotFailed3'];//Request was failed, Error No: 3003
	}
	
	mysqli_close($con);
	return $err;
	
}

function autoresponse($to, $cc_id, $cc_code) {
	
	$subject = 'Customer Password-Reset Request';
	$v_url = 'http://' . $_SERVER['HTTP_HOST'] . '/reset_pw.php?id=' . $cc_id . '&vcode=' . $cc_code . '&rnd=' . mt_rand(1E6,1E9);
	$message = "
	<html>
	<head>
	<title>Customer Password-Reset Request</title>
	</head>
	<body>
	<div  style='font-family: Microsoft Yahei,Arial,Verdana; font-size:13px;'>
	
	<p>
	Hi,<br/><br/>
	You're receiving this e-mail because you requested a password reset for your user account at Centcount<br/><br/>
	Click on the following link to reset a new password<br/>
	<a href='" . $v_url . "' style='color:#39f; text-decoration:underline;'>" . $v_url . "</a><br/><br/><br/><br/>
	
	Regards,<br/>
	Centcount Team<br/><br/>
	
	This is an auto-response mail. Please do not reply.<br/>
	
	</p>
	
	</div>
	</body>
	</html>
	";

	// 当发送 HTML 电子邮件时，请始终设置 content-type
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";

	// 更多报头
	$headers .= 'From: <' . AUTORESPONSE_MAIL . '>';

	$ret = mail($to, $subject, $message, $headers);
	return $ret;
	
}

?> 

<!DOCTYPE html>

<head>

<title>Centcount Analytics - Forgot Password</title>
<?php echo META_TEXT() ?>
<?php echo CA_JS_CODE ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

</head>

<body>


<?php echo HTML_HEADER(); ?>


<div id="title">
	<h1><?php echo $GLOBALS['language']['ForgotPageTitle']; ?></h1>
</div>


<div id="bodyframe">

	<div class="framebody">

		<?php echo ($err != '' ? '<div class="errmsg" id="errormsg" style="display:block;">'.$err.'</div>' : '<div class="errmsg" id="errormsg"></div>'); ?>

		<form name="forgotpw" method="POST" action="">

			<table>

				<tr><td>
					<input class="email" type="text"  name="email" id="Email" maxlength="255" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" placeholder="<?php echo $GLOBALS['language']['RegTipRequired']; ?>" onblur="checkEmail(this.id)" onfocus="setStyle(this.id)"/>
				</td></tr>
				
				<tr><td>
					<input class="short" type="text" name="captcha" id="Captcha" maxlength="4" placeholder="<?php echo $GLOBALS['language']['PageCaptcha']; ?>" onBlur="checkCaptcha(this.id)" onfocus="setStyle(this.id)" />
					<div class="suggestion">
						<img src="validcode.php?rnd=<?php echo mt_rand(1E7,9E8); ?>" class="vcode" id="code"/>
						<a class="fresh" href="javascript:changeCode()" title="<?php echo $GLOBALS['language']['PageRefresh']; ?>"></a>
					</div>
				</td></tr>
				
				<tr><td><button id="Submit" type="submit" name="submit"><?php echo $GLOBALS['language']['PageOK']; ?></button></td></tr>
				
				<tr><td class="sug">
					<a style="float:left;" href="login.php"><?php echo $GLOBALS['language']['PageRecallPW']; ?></a>
				</td></tr>
			
			</table>

		</form>
		
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
	
	function checkEmail(x) {
		if (checkValue(x) == false) return;
		var email = document.getElementById(x).value;
		
		var reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
		if (reg.test(email) == false) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrEmail2']; ?>";//"Email is not valid";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	} 
	
	function checkCaptcha(x) {
		if (checkValue(x) == false) return;
		var captcha= document.getElementById(x).value;

		if (captcha.length != 4) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrCaptcha2']; ?>";//"Captcha must be 4 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		}
		else{
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		} 
	}
	
</script>


</body>
</html>
