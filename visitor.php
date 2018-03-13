<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics User Login PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/
session_name("CASESSID");
session_start();
header('Content-type: text/html; charset=utf-8');

@require './config/config_security.php';
@require './config/config_common.php';
require 'language.php';
require 'html.php';

$err = '';
$visittype = -1;
$visitpw = '';
$id = 0;
$siteid = 0;
$stb;


//******************** MAIN FUNCTION BEGIN ********************

empty($_GET['id']) ? exit : $id = (int)$_GET['id']; 
empty($_GET['siteid']) ? exit : $siteid = (int)$_GET['siteid'];

$err = get_visitor_password($visitpw, $visittype, $id, $siteid);

if ($_POST) {

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

}


switch ($visittype) {//visit type: 0->deny, 1->visit with password, 2->visit without password
case 0://access denied
	die('Access Denied.');
case 1://visit with password
	if( $err === '') {
		if ($visitpw === $_POST['password']) {
			login($id, $siteid);
		} else {
			$err .= '<br/>Wrong Password';
		}
	}
	break;
case 2://visit without password
	login($id, $siteid);
	break;
}

if (substr($err, 0, 5) == '<br/>') $err = substr($err, 5);	

//********************* MAIN FUNCTION END *********************


function login($id, $siteid) {

	$_SESSION['admin'] = $siteid === DEMO_SITE_ID ? 1 : 0;//admin type: 0->visitor, 1->demo account, 2->user account, 4->super administrator
	$r = mt_rand(1E9, 9E9);
	$_SESSION['r'] = $r;
	$_SESSION['user'] = 'Anonymous';
	$_SESSION['visa'] = md5($id . $siteid . $r . ENCODE_FACTOR);
	header('Location: manager.php?id='.$id.'&siteid='.$siteid); 
	exit;

}

function get_visitor_password(&$visitpw, &$visittype, $id, $siteid) {
	
	$err = '';
	$visittype = 0;
	$visitpw = '';
	
	$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
	if (mysqli_connect_errno($con)) {
		$err .= '<br/>Could not connect mysql host. Please contact Administrator!';
		return $err;
	}

	$db_selected = mysqli_select_db($con, DB_NAME_USER);
	if (!$db_selected) {
		mysqli_close($con);
		$err .= '<br/>Could not use database. Please contact Administrator!';
		return $err;
	}
	
	$sql = "SELECT SiteTB FROM User WHERE UserID={$id}";
	$result = mysqli_query($con, $sql);
	if ($result && mysqli_num_rows($result)) {
		while ($row = mysqli_fetch_assoc($result))
		{
			$stb = $row['SiteTB'];
		}
		$_SESSION['stb'] = $stb;
		mysqli_free_result($result);
	} else {
		mysqli_close($con);
		$err .= '<br/>User is not existed. Please try again!';
		return $err;
	}
	
	$sql = "SELECT VisitorPassword,VisitType FROM st{$stb} WHERE SiteID={$siteid}";
	$result = mysqli_query($con, $sql);
	if ($result && mysqli_num_rows($result)) {
		while ($row = mysqli_fetch_assoc($result))
		{
			$visitpw = $row['VisitorPassword'];
			$visittype = (int)$row['VisitType'];
		}
		mysqli_free_result($result);
	} else {
		$err .= '<br/>Could not get visitor information. Please contact Administrator!';
	}

	mysqli_close($con);
	
	return $err;
}

?> 

<!DOCTYPE html>

<head>

<title>Centcount Analytics - Visitor Login</title>
<?php echo META_TEXT() ?>
<?php echo CA_JS_CODE ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

</head>

<body>

<?php echo HTML_HEADER(); ?>

<div id='title'>
	<h1>Visitor Login</h1>
</div>

<div id='bodyframe'>

	<div class='framebody'>
	
		<?php echo ($err != '' ? '<div class="errmsg" id="errormsg" style="display:block;">'.$err.'</div>' : '<div class="errmsg" id="errormsg"></div>'); ?>

		<form name='visitorlogin' method='POST'  action=''>

		  <table>
		  
			<tr>
				<td><input class="pwd" type="password" name="password" id="Password" maxlength="16" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" placeholder="<?php echo $GLOBALS['language']['PagePW']; ?>" onBlur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td>
			</tr>
			
			
			<tr>
				<td><input class="short" type="text" name="captcha" id="Captcha" maxlength="4" placeholder="<?php echo $GLOBALS['language']['PageCaptcha']; ?>" onBlur="checkCaptcha(this.id)" onfocus="setStyle(this.id)" />
					<div class="suggestion" styel="text-align:left;">
						<img src="validcode.php?rnd=<?php echo  mt_rand(1E7,9E8); ?>" style="width:130px; height:34px; float:left; margin-right:6px;" id="code"/>
						<a class="fresh" href="javascript:changeCode()" title="<?php echo $GLOBALS['language']['PageRefresh']; ?>"></a>
					</div>
				</td>
			</tr>
			
			<tr>
				<td><button type='submit' name='submit' id='Login'>Visitor Login</button></td>
			</tr>
			
			<tr>
				<td class="sug">
					<a style="float:left;" href='login.php'>User Login</a></a>
					<a style="float:right;" href="register.php"><?php echo $GLOBALS['language']['RegPageAgreeRegister'] ?></a>
				</td>
			</tr>
			
		  </table>

		</form>
	
	</div>

</div>

<?php echo HTML_FOOTER(); ?>


<script type='text/javascript'>
   
   function changeCode(){ 
	   document.getElementById('code').src = 'validcode.php?id='+Math.random();
   }
   
   
   function setStyle(x){
	   document.getElementById(x).style.borderColor='#39F';
   }
   
   function checkValue(x){
	   if(document.getElementById(x).value == ''){
		   document.getElementById('errormsg').innerHTML = document.getElementById(x).name + ' must not be empty';
		   document.getElementById('errormsg').style.display = 'block';
		   document.getElementById(x).style.borderColor = '#f00';
		   return false;
	   }
   }
 
   
   function checkPW(x){
	   var pw= document.getElementById(x).value;
	   if (pw.length < 6){
		   document.getElementById('errormsg').innerHTML = 'Password must be at least 6 characters long';
		   document.getElementById('errormsg').style.display = 'block';
		   document.getElementById(x).style.borderColor = '#f00';
	   }
	   else{
		   document.getElementById('errormsg').innerHTML = '';
		   document.getElementById('errormsg').style.display = 'none';
		   document.getElementById(x).style.borderColor = '#ccc';
	   }
	   
   }
   
   
   function checkCaptcha(x){
	   if (checkValue(x) == false) return;
	   var captcha= document.getElementById(x).value;
	   //
	   if (captcha.length < 4){
		   document.getElementById('errormsg').innerHTML = 'Captcha must be 4 characters long';
		   document.getElementById('errormsg').style.display = 'block';
		   document.getElementById(x).style.borderColor = '#f00';
	   }
	   else{
		   document.getElementById('errormsg').innerHTML = '';
		   document.getElementById('errormsg').style.display = 'none';
		   document.getElementById(x).style.borderColor = '#ccc';
	   } 
   }
   
   
	function selectLanguage($) {
		var $ = document.getElementById($),
		RunOnce = !1;
		
		if ($.style.display == "block") {
			$.style.display = "none"
		} else {
			$.style.display = "block";
			var a = ($.parentNode.offsetWidth - 3);
			if (($.offsetWidth - 2) < a) $.style.width = a + "px";
		}
		
		if (RunOnce == !1) {
			if (window.addEventListener) { //for firefox, chrome, safari
				window.addEventListener("click",LangCancel);
			} else { // for IE5,6,7,8
				document.attachEvent("onclick",LangCancel);
			}
			RunOnce = !0;
		}
	
		function LangCancel(e) {
			e = e || window.event;
			var a = e.srcElement || e.target;
			if (a.id != "lang") $.style.display = "none";
		}
	}
   
</script>

</body>
</html>
