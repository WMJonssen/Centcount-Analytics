<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Verify Page PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/
header('Content-type: text/html; charset=utf-8');

@require './config/config_security.php';
@require './config/config_common.php';
require 'language.php';
require 'html.php';

//******************** MAIN FUNCTION BEGIN ******************** 
	
$id = empty($_GET['id']) ? exit : (int)$_GET['id'];
$verify_code = empty($_GET['vcode']) ? exit : (int)$_GET['vcode'];
	
$success = false;
	
$err = verify($id, $verify_code, $success);
if (substr($err, 0, 5) === '<br/>') $err = substr($err, 5);	

//********************* MAIN FUNCTION END ********************* 

function verify($id, $verify_code, &$success) {

		$err = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
			$err = 'Could not connect mysql host. Please contact Administrator!';
			return $err;
 		}
		
		$db_selected = mysqli_select_db($con, DB_NAME_USER);
		if (!$db_selected) {
			$err .= '<br/>Could not use database. Please contact Administrator!';
			mysqli_close($con);
			return $err;
  		}
		
		//check username 
		$result = mysqli_query($con, "SELECT * FROM User WHERE UserID={$id}");
		if (mysqli_num_rows($result) == 1) {
			while ($row = mysqli_fetch_assoc($result))
			{
				$acode = (int)$row['ActivateCode'];
				$atime = (int)$row['ActivateTime'];
				$actived = (int)$row['Activated'];
				break;
			}
			mysqli_free_result($result);
		} else {
			$err .= '<br/>Verify failed! User is not exist';
			mysqli_close($con);
			return $err;
		}
			
		if ($actived > 0) {
			$err .= '<br/>This account is already activated. Please login!';
			$success = true;
			mysqli_close($con);
			return $err;
		}

		//date_default_timezone_set('PRC');
		$ntime = time();
		
		if ($atime < $ntime) {
			$err .= '<br/>Verify failed. Activation-Code has been expired';
			mysqli_close($con);
			return $err;
		}
		
		if ($verify_code !== $acode) {
			$err .= '<br/>Verify failed. Activation-Code is not valid';
			mysqli_close($con);
			return $err;
		}
		
		//update User information
		$sql = "UPDATE User SET Activated=1,ActivateCode=0,ActivateTime=0 WHERE UserID={$id}";
		if (mysqli_query($con, $sql)) {
			$err .= '<br/>Verify successfully!';
			$success = true;
		} else {
			$err .= '<br/>Verify failed! Please try again';
		}
		
 		mysqli_close($con);
	
		return $err;
		
}

?> 

<!DOCTYPE html>

<head>

<title>Centcount Analytics - Verify User</title>
<?php echo META_TEXT() ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

</head>

<body>


<?php echo HTML_HEADER(1, $id, $verify_code); ?>


<div id="title">
	<h1>Verify User</h1>
</div>


<div id="bodyframe">

	<div class="framebody">

		<div class="successmsg" id="errormsg"><?php echo $err; ?></div>
		
		<?php if ($success) echo '<a class="successbtn" id="login" href="login.php">Login</a>'; ?>
		
	</div>

</div>


<?php echo HTML_FOOTER(); ?>


<script type="text/javascript">
	
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