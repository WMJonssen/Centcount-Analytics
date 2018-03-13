<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analyticsb Free User Login PHP Code *
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


$success = false;
$err = '';

if ($_POST) {

	if (empty($_POST['email'])) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrEmail1'];//'Email must not be empty';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrEmail2'];//'Email is not valid';
	}

	if (empty($_POST['password'])) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrPW1'];//'<br/>Password must not be empty';
	} else if (strlen($_POST['password']) < 6) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrPW2'];//'<br/>Password must be at least 6 characters long';
	} else if (strlen($_POST['password']) > 16) {
		$err .= '<br/>' . $GLOBALS['language']['RegErrPW3'];//'<br/>Password password max-length is 16 characters long';
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
		$err = login($_POST['email'], $_POST['password'], $success);
	}

	if (substr($err, 0, 5) == '<br/>') $err = substr($err, 5);	
	
}
	


function login($user, $pw, &$success) {

	$err = '';
	$apw = ''; 
	$activated = 0;
	$admin = -1; //0->visitor, 1->demo account, 2->user account, 4->super admin
	$id = 0; 
	$stb = ''; 

	$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
	if (mysqli_connect_errno($con)) {
		$err = 'Could not connect database server. Please contact Administrator!';//die('Could not connect: ' . mysqli_error($con));
		return $err;
 		}
	
	$user = mysqli_real_escape_string($con, $user);
	$pw = mysqli_real_escape_string($con, $pw);
	
	$db_selected = mysqli_select_db($con, DB_NAME_USER);
	if (!$db_selected) {
		$err .= '<br/>Could not use database. Please contact Administrator!';
		mysqli_close($con);
		return $err;
	}
	
	//check username  
	$result = mysqli_query($con, "SELECT * FROM User WHERE Username='{$user}'"); 
	if ($result && mysqli_num_rows($result) == 1) { 
		$row = mysqli_fetch_assoc($result);
		if (count($row) > 0) {
			$apw = $row['Password']; 
			$activated = (int)$row['Activated'];
			$admin = (int)$row['Authority']; //0->visitor, 1->demo account, 2->user account, 4->super admin
			$id = (int)$row['UserID']; 
			$stb = $row['SiteTB']; 
		} 
		mysqli_free_result($result); 
		mysqli_close($con); 
	} else { 
		$err .= '<br/>' . $GLOBALS['language']['LoginFailed1'];//"<br/>Login failed! Email is not exist"; 
		mysqli_close($con); 
		return $err; 
	}


	if ($activated === 1) {
		if ($apw === md5($pw)) {
			$_SESSION['admin'] = $admin;//0->visitor, 1->demo account, 2->user account, 4->super admin
			$r = mt_rand(1E9, 9E9);
			$_SESSION['r'] = $r;
			$_SESSION['user'] = $user;
			$_SESSION['visa'] = $admin === 4 ? md5($r . ENCODE_FACTOR) : md5($id . $r . ENCODE_FACTOR);
			$_SESSION['stb'] = $stb;
			header('Location: manager.php?id='.$id); 
			exit;
		} else {
			$err .= '<br/>' . $GLOBALS['language']['LoginFailed3'];//"<br/>Wrong password";
		}
	} else {
		$err .= '<br/>' . $GLOBALS['language']['LoginFailed2'];//"<br/>This account is not activated. Please activate first!";
	}

	return $err;

}

?> 

<!DOCTYPE html>

<head>

<title><?php echo $GLOBALS['language']['CA']; ?> - <?php echo $GLOBALS['language']['LoginPageTitle']; ?></title>
<?php echo META_TEXT() ?>
<?php echo CA_JS_CODE ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

</head>

<body>


<?php echo HTML_HEADER(); ?>


<div id="title">
	<h1><?php echo $GLOBALS['language']['LoginPageTitle']; ?></h1>
</div>


<div id="bodyframe">

	<div class="framebody">
	
		<?php echo ($err != '' ? '<div class="errmsg" id="errormsg" style="display:block;">'.$err.'</div>' : '<div class="errmsg" id="errormsg"></div>'); ?>

		<form name="login" method="POST" action="">
			
			<table>
		  
				<tr><td><input class="email" type="text"  name="email" id="Email" maxlength="255" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" placeholder="<?php echo $GLOBALS['language']['RegTipRequired']; ?>" onblur="checkEmail(this.id)" onfocus="setStyle(this.id)"/></td></tr>

				<tr><td><input class="pwd" type="password" name="password" id="Password" maxlength="16" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" placeholder="<?php echo $GLOBALS['language']['RegTipMinlength']; ?>" onBlur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td></tr>

				<tr><td><input class="short" type="text" name="captcha" id="Captcha" maxlength="4" placeholder="<?php echo $GLOBALS['language']['PageCaptcha']; ?>" onBlur="checkCaptcha(this.id)" onfocus="setStyle(this.id)" />
						<div class="suggestion">
							<img src="validcode.php?rnd=<?php echo mt_rand(1E7,9E8); ?>" class="vcode" id="code"/>
							<a class="fresh" href="javascript:changeCode()" title="<?php echo $GLOBALS['language']['PageRefresh']; ?>"></a>
						</div>
				</td></tr>

				<tr><td><button id="login" type="submit" name="submit"><?php echo $GLOBALS['language']['PageLogIn']; ?></button></td></tr>
				
				<tr><td class="sug">
						<a style="float:left;" href="forgot.php"><?php echo $GLOBALS['language']['PageForgotPW']; ?></a>
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
	
	function checkEmail(x){
		var email = document.getElementById(x).value;
		if (email == "") {
			document.getElementById(x).style.borderColor = "#ccc";
			return;
		}
		
		var reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
		if (reg.test(email) == false) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrEmail2']; ?>";//"email is not valid";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	} 
	
	function checkPW(x) {
		var pw = document.getElementById(x).value;
		if (pw == "") {
			document.getElementById(x).style.borderColor = "#ccc";
			return;
		}
		
		if (pw.length < 6) {
			document.getElementById("errormsg").innerHTML = "<?php echo $GLOBALS['language']['RegErrPW2']; ?>";//"Password must be at least 6 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
		
	}
	
	
	function checkCaptcha(x) {
		var captcha = document.getElementById(x).value;
		if (captcha == "") {
			document.getElementById(x).style.borderColor = "#ccc";
			return;
		}

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
	
	
</script>

</body>
</html>
