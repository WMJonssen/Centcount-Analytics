<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Installation PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/
session_name('CASESSID');
session_start();
header('Content-type: text/html; charset=utf-8');

@require './config/config_security.php';
@require './config/config_common.php';
//require 'language.php';
require 'html.php';

$err = '';
$chk = 0;

if (isset($_GET['act']) && $_GET['act'] == 'check') $chk = 1;

if ($_POST) {

	if (empty($_POST['dbuser'])) {
		$err .= 'MySQL login username must not be empty';
	}
	
	if (empty($_POST['dbpw'])) {
		$err .= '<br/>MySQL login password must not be empty';
	}
	
	$matchStr = filter_var($_POST['username'], FILTER_VALIDATE_EMAIL);
	if (empty($_POST['username'])) {
		$err .= '<br/>Administrator username must not be empty';
	} else if ($_POST['username'] !== $matchStr) {
		$err .= '<br/>Administrator username contains illegal characters';
	}
	
	if (empty($_POST['password'])) {
		$err .= '<br/>Administrator password must not be empty';
	} else if (strlen($_POST['password']) < 6) {
		$err .= '<br/>Administrator password must be at least 6 characters long';
	}

	if (empty($_POST['confirm'])) {
		$err .= '<br/>Repeat password must not be empty';
	} else if (strlen($_POST['confirm']) < 6) {
		$err .= '<br/>Repeat password must be at least 6 characters long';
	} else if (strlen($_POST['confirm']) > 16) {
		$err .= '<br/>Repeat password max-length is 16 characters long';
	} else if (trim($_POST['password']) !== $_POST['confirm']) {
		$err .= '<br/>Passwords do not match';
	}
	
	$vcode = empty($_SESSION['vcode']) ? '' : $_SESSION['vcode'];
	$_SESSION['vcode'] = mt_rand(1E6,1E9);
	
	if (empty($_POST['captcha'])) {
		$err .= '<br/>Captcha must not be empty';
	} else if (strlen($_POST['captcha']) < 4) {
		$err .= '<br/>Captcha must be 4 characters long';
	} else if ($vcode !== strtolower($_POST['captcha'])  || $vcode === '') {
		$err .= '<br/>Wrong captcha';
	}
	
	
	if ($err === '') $err = InstallCA($_POST['dbuser'], $_POST['dbpw'], $_POST['username'], $_POST['password']);
	
	if (substr($err,0,5) == '<br/>') $err = substr($err,5);
}

function InstallCA($DB_User, $DB_PW, $user, $pw) {
	
		$err = '';
		date_default_timezone_set(DEFAULT_TIME_ZONE);
		
		$con = mysqli_connect(DB_HOST_LOCAL, $DB_User, $DB_PW);
		if (mysqli_connect_errno($con)) {
			$err = '<br/>Could not connect database';
		} else {
			$err = '<br/>Connect to database successfully';
		}
		
		$db_selected = mysqli_select_db($con, 'ccdata');
		if (!$db_selected) {
			$err .=  '<br/>Database does not exist';
			if (mysqli_query($con, 'CREATE DATABASE IF NOT EXISTS ccdata DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci')) {
				$err .= '<br/>Create database successfully';
			} else {
				$err .= '<br/>Creating database failed';
				return $err;
			}
 		} else {
			$err .=  '<br/>Database exists';
		}
		
		$db_selected = mysqli_select_db($con, 'ccdata');
		if (!$db_selected) {
			$err .= '<br/>Select database failed';
			return $err;
 		}
		

		
		if (check_table($con, 'User', 'ccdata')) {
			$err .=  '<br/>User table has existed';
		} else {
			$err .=  '<br/>User table does not exist';
						
$sql = 'CREATE TABLE IF NOT EXISTS User (
Version smallint NOT NULL DEFAULT 0, 
UserID bigint NOT NULL DEFAULT 0, 
Username varchar(255) NOT NULL PRIMARY KEY, 
Password varchar(32) NOT NULL DEFAULT "", 
Authority tinyint NOT NULL DEFAULT 0,
SiteTB varchar(16) NOT NULL DEFAULT "",
TimeZone varchar(32) NOT NULL DEFAULT "", 
ActivateCode int NOT NULL DEFAULT 0, 
ActivateTime int NOT NULL DEFAULT 0, 
CreateTime int NOT NULL DEFAULT 0,
Activated tinyint NOT NULL DEFAULT 0,
Message varchar(255) NOT NULL DEFAULT "",
UNIQUE indexUID (UserID),
INDEX indexCT (CreateTime)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';

			if (mysqli_query($con, $sql)) {
				$err .=  '<br/>Create User table successfully';
			} else {
				$err .=  '<br/>Create User table failed' . mysqli_error($con);
				return $err;
			}
			
		}

		
		if (check_table($con, 'host', 'ccdata')) {
			$err .=  '<br/>Host table has existed';
		} else {
			$err .=  '<br/>Host table does not exist';
						
$sql = 'CREATE TABLE IF NOT EXISTS host (
Hostname varchar(255) NOT NULL PRIMARY KEY, 
Domain varchar(255) NOT NULL DEFAULT "", 
Enabled tinyint NOT NULL DEFAULT 1,
CreateTime int NOT NULL DEFAULT 0,
UNIQUE indexDomain (Domain)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';

			if (mysqli_query($con, $sql)) {
				$err .=  '<br/>Create host table successfully';
			} else {
				$err .=  '<br/>Create host table failed' . mysqli_error($con);
				return $err;
			}

			$now = time();
			$hostname = 'Local Host';
			$domain = $_SERVER['SERVER_NAME'];

			$sql = "INSERT INTO host(HostName, Domain, Enabled, CreateTime) VALUES('{$hostname}', '{$domain}', 1, $now)";
			if (mysqli_query($con, $sql)) {
				$err .= '<br/>Add host successfully';
			} else {
				$err .= '<br/>Add host failed';// 
				return $err;
			}
			
		}



		//GENERATE USER ID BEGIN
		$CreateTime = time();
		//$ActivateCode = mt_rand(1E8, 2E9);
		//$ActivateTime = $CreateTime + 604800;//one week(7*24*60*60 = 604800);
		$Today = date('ymd');
		$UserDate = $Today;
		$UserID = 1;
		$UID = (int)($UserDate . '0000001'); 
		$SiteTB = 1;
		//GENERATE USER ID END



		//REGISTER USER ID BEGIN
		$md5pw = md5($pw);
		$sql = "REPLACE INTO User(UserID, Username, Password, SiteTB, ActivateCode, ActivateTime, Activated, Authority, CreateTime) VALUES($UID, '{$user}', '{$md5pw}', {$SiteTB}, 0, 0, 1, 4, {$CreateTime})";
		if (mysqli_query($con, $sql)) {
			if (!check_table($con, 'st'. $SiteTB, 'ccdata')) {
				$sql = 'CREATE TABLE IF NOT EXISTS st'. $SiteTB .' (
						Version smallint NOT NULL DEFAULT 0, 
						UserID bigint NOT NULL DEFAULT 0,
						SiteID bigint NOT NULL PRIMARY KEY, 
						SiteName varchar(255) NOT NULL DEFAULT "", 
						TimeZone varchar (32) NOT NULL DEFAULT "", 
						DataCenter varchar (128) NOT NULL DEFAULT "",
						SiteDescription varchar(255) NOT NULL DEFAULT "", 
						SiteType tinyint NOT NULL DEFAULT 0, 
						SiteStatus tinyint NOT NULL DEFAULT 0, 
						VisitorPassword varchar(16) NOT NULL DEFAULT "",
						IPDatabase smallint NOT NULL DEFAULT 0, 
						CreateTime int NOT NULL DEFAULT 0,
						VisitType tinyint NOT NULL DEFAULT 0,
						INDEX indexUID (UserID),
						INDEX indexCT (CreateTime),
						INDEX indexDC (DataCenter)
						) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;';
				if (!mysqli_query($con, $sql)) {
					$err .= '<br/>Create sites list table failed. Error No: 1003';
					mysqli_close($con);
					return $err;
				}
			}
			/*
			if (autoresponse($user, $pw, $UID, $ActivateCode)) {
				$err .= '<br/>Register super administrator account successfully!<br/>Please check the verify-mail to activate account';
			} else {
				$err .= '<br/>Send verify email failed! Please try again or send error information to us. Error No: 1001';
				return $err;
			}
			*/
		} else {//Error No ' . mysqli_errno($con) . ': ' . mysqli_error($con);
			$err .= '<br/>Register super administrator account failed, Please try again or send error information to us. Error No: 1002';
			return $err;
		}
		//REGISTER USER ID END


		
		mysqli_close($con);

		$err =  'Congratulations!<br/>Install CA Successfully.<br/>' . $err . '<br/><br/><a href="https://' . $_SERVER['HTTP_HOST'] . '/login.php">Log In</a>';
		return $err;

}

function CheckEnv() {
	
		$error = '';
		$err_count = 0;
		date_default_timezone_set(DEFAULT_TIME_ZONE);
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);

		//check Configurstion
		$error .= '<br>**************************************************<br>';
		$error .= 'Check Server Configuration: <br>**************************************************';
		//get cpu
		$TMP_FP = popen('cat /proc/cpuinfo| grep "processor"| wc -l', 'r');
		$tmp = fread($TMP_FP, 1024);
		pclose($TMP_FP);
		if ($tmp) {
			$error .= '<br>CPU: ' . $tmp . ' Core(s)';
		}
		//get memory
		$TMP_FP = popen('free -m | grep -E "^(Mem:)"', 'r');
		$tmp = fread($TMP_FP, 1024);
		pclose($TMP_FP);
		if ($tmp) {
			$tmp = preg_replace("/\s(?=\s)/", "\\1", $tmp);
			$tmp = explode(' ', $tmp);
			if (count($tmp) > 3) {
				$error .= '<br>Memory: ' . $tmp[1] . ' MB';
			}
		}
		//get disk size
		$TMP_FP = popen('df -BG | grep -E "^(/)"', 'r');
		$tmp = fread($TMP_FP, 1024);
		pclose($TMP_FP);
		if ($tmp) {
			$tmp = preg_replace("/\s(?=\s)/", "\\1", $tmp);
			$tmp = explode(' ', $tmp);
			$n = (int)(count($tmp) / 6);
			$total = 0;
			$used = 0;
			for ($i=0; $i<$n; $i++) {
				$t = $i * 6 + 1;
				$total += (int)$tmp[$t];
			}
			$error .= '<br>Disk: ' . $total . ' GB';
		}
		$error .= '<br>**************************************************<br><br>';

		//check version
		$error .= '<br>**************************************************<br>';
		$error .= 'Check Version: <br>**************************************************';
		$TMP_FP = popen('cat /etc/issue', 'r');
		$tmp = trim(fread($TMP_FP, 128));
		pclose($TMP_FP);
		$error .= '<br>OS: ' . PHP_OS . ' - ' . str_replace('\n \l', '', $tmp) . ' - '. (log(PHP_INT_MAX + 1, 2) + 1). 'bit';
		$error .= '<br>Server: ' . $_SERVER['SERVER_SOFTWARE'];
		//check php
		$tmp = explode('-', PHP_VERSION);
		if ((float)$tmp[0] < 5.6) {
			$error .= '<br><i>PHP: ' . $tmp[0] . ' (Require >= 5.6)</i>';
			$err_count++;
		} else {
			$error .= '<br>PHP: ' . $tmp[0];
		}
		//check php-cli version
		$TMP_FP = popen('php -v', 'r');
		$tmp = trim(fread($TMP_FP, 128));
		pclose($TMP_FP);
		if (substr($tmp, 0 ,3) != 'PHP') {
			$error .= '<br><i>PHP CLI: Disabled (Must Be Enabled)</i>';
			$err_count++;
		} else {
			$tmp = (float)substr($tmp, 4);
			if ($tmp < 5.6) {
				$error .= '<br><i>PHP CLI: ' . $tmp. ' (Require >= 5.6)</i>';
				$err_count++;
			} else {
				$error .= '<br>PHP CLI: ' . $tmp;
			}
		}
		//check mysql
		if ($con) {
			$tmp = explode('-', mysqli_get_server_info($con));
			$error .= '<br>MySQL: ' . $tmp[0];
		} else {
			$error .= '<br><i>MySQL: Missed</i>';
			$err_count++;
		}
		//check redis
		$TMP_FP = popen('redis-cli --version', 'r');
		$tmp = trim(fread($TMP_FP, 128));
		pclose($TMP_FP);
		if (substr($tmp, 0, 9) != 'redis-cli') {
			$error .= '<br><i>Redis: Missed</i>';
			$err_count++;
		} else {
			$error .= '<br>Redis: ' . substr($tmp, 9);
		}
		//check postfix
		$TMP_FP = popen('ps aux|grep postfix -c', 'r');
		$tmp = trim(fread($TMP_FP, 128));
		pclose($TMP_FP);
		if ((int)$tmp > 1) {
			$error .= '<br>Postfix: OK';
		} else {
			$error .= '<br><i>Postfix: Missed</i>';
			$err_count++;
		}
 		$error .= '<br>**************************************************<br><br>';

 		//check mysql 
		$error .= '<br>**************************************************<br>';
		$error .= 'Check MySQL Connection: <br>**************************************************';
		if (mysqli_connect_errno($con)) {
			$error .= '<br/><i>Connect MySQL Failed</i>';
			$err_count++;
 		} else {
 			$error .= '<br/>Connect MySQL Successfully';
 		}
 		$error .= '<br>**************************************************<br><br>';
		
		if ($con) {
			$error .= '<br>**************************************************<br>';
			$need = array('ONLY_FULL_GROUP_BY'=>0,'NO_AUTO_VALUE_ON_ZERO'=>0,'PIPES_AS_CONCAT'=>0,'ANSI_QUOTES'=>0);
			$sql = "SELECT @@GLOBAL.sql_mode";
			$result = mysqli_query($con, $sql);
			if ($result && mysqli_num_rows($result)) {
				$ret = mysqli_fetch_row($result);
				$error .= 'Check SQL Mode: <br>**************************************************';// $ret[0];
				$sql_mode = trim($ret[0]);
				if ($sql_mode) {
					$sql_mode = explode(',', trim($ret[0]));
					foreach ($sql_mode as $key) {
						if (array_key_exists($key, $need)) {
							$error .= '<br><i>' . $key . ' => Need To Be Removed</i>';
							$err_count++;
						} else {
							$error .= '<br>' . $key . ' => OK';
						}
					};
				} else {
					$error .= '<br>No Set MySQL Mode => OK';
				}	
				mysqli_free_result($result);
			} else {
				$error .= '<i>Check SQL Mode Failed</i>';
			}
			$error .= '<br>**************************************************<br><br>';

			$error .= '<br>**************************************************<br>';
			$need = array('character_set_client'=>0,'character_set_connection'=>0,'character_set_database'=>0,'character_set_results'=>0,'character_set_server'=>0,'character_set_system'=>0);
			$sql = "show variables like 'character%'";
			$result = mysqli_query($con, $sql);
			if ($result && mysqli_num_rows($result)) {
				$error .= 'Check MySQL Character Set: <br>**************************************************';
				while ($ret = mysqli_fetch_row($result)) {
					//$error .= '<br>', $ret[0], ' => ', $ret[1];
					if (array_key_exists($ret[0], $need)) {
						if ($ret[1] == 'utf8') {
							$error .= '<br>' . $ret[0] . ' : ' . $ret[1] . ' => OK';
						} else {
							$error .= '<br><i>' . $ret[0] . ' : ' . $ret[1] . ' => WRONG (Require "utf8")</i>';
							$err_count++;
						}
					} 
				}
				mysqli_free_result($result);
			} else {
				$error .= '<i>Check MySQL Character Set Failed</i>';
				$err_count++;
			}
			$error .= '<br>**************************************************<br><br>';

			$error .= '<br>**************************************************<br>';
			$need = array('collation_connection'=>0,'collation_database'=>0,'collation_server'=>0);
			$sql = "show variables like 'collation_%'";
	 		$result = mysqli_query($con, $sql);
			if ($result && mysqli_num_rows($result)) {
				$error .= 'Check MySQL Collation: <br>**************************************************';
				while ($ret = mysqli_fetch_row($result)) {
					//$error .= '<br>', $ret[0], ' => ', $ret[1];
					if (array_key_exists($ret[0], $need)) {
						if ($ret[1] == 'utf8_general_ci') {
							$error .= '<br>' . $ret[0] . ' : ' . $ret[1] . ' => OK';
						} else {
							$error .= '<br><i>' . $ret[0] . ' : ' . $ret[1] . ' => WRONG (Require "utf8_general_ci")</i>';
							$err_count++;
						}
					} 
				}
				mysqli_free_result($result);
			} else {
				$error .= '<i>Check MySQL Collation Failed</i>';
				$err_count++;
			}
			$error .= '<br>**************************************************<br><br>';
		}
		

		$error .= '<br>**************************************************<br>';
		$need = array('session'=>0,'curl'=>0,'mbstring'=>0,'gd'=>0,'json'=>0,'mysqli'=>0,'redis'=>0);
		
		$Extensions = get_loaded_extensions();
		if (count($Extensions)) {
			$error .= 'Check PHP Extension: <br>**************************************************';
			foreach ($Extensions as $key => $value) {
				//$error .= '<br>', $value;
				if (array_key_exists($value, $need)) $need[$value] = 1;
			};
			foreach ($need as $key => $value) {
				if ($value == 1) {
					$error .= '<br>' . $key . ' => OK';
				} else {
					$error .= '<br><i>' . $key . ' => Missed</i>';
					$err_count++;
				}
			};
		} else {
			$error .= '<i>Check PHP Extension Failed</i>';
			$err_count++;
		}	
		$error .= '<br>**************************************************<br><br>';



		$error .= '<br>**************************************************<br>';
		$need = array('popen'=>0);
		
		$error .= 'Check PHP Function: <br>**************************************************';
		foreach ($need as $key => $value) {
			$need[$key] = function_exists($key);
		};
		foreach ($need as $key => $value) {
			if ($value == 1) {
				$error .= '<br>' . $key . ' => OK';
			} else {
				$error .= '<br><i>' . $key . ' => Disabled (Must Be Enabled)</i>';
				$err_count++;
			}
		};
		$error .= '<br>**************************************************<br><br>';


		//********** connect redis begin ***********
		$error .= '<br>**************************************************<br>';
		$error .= 'Check Redis: <br>**************************************************';
		$REDIS_0 = new Redis();
		if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) {
			$error .= '<br><i>Redis 0 => ERROR</i>';
			$err_count++;
		} else {
			$error .= '<br>Redis 0 => OK';
		}
		$REDIS_1 = new Redis();
		if ($REDIS_1->CONNECT(REDIS_IP_1, REDIS_PORT_1) !== true) {
			$error .= '<br><i>Redis 1 => ERROR</i>';
			$err_count++;
		} else {
			$error .= '<br>Redis 1 => OK';
		}
		$REDIS_2 = new Redis();
		if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) {
			$error .= '<br><i>Redis 2 => ERROR</i>';
			$err_count++;
		} else {
			$error .= '<br>Redis 2 => OK';
		}
		$REDIS_3 = new Redis();
		if ($REDIS_3->CONNECT(REDIS_IP_3, REDIS_PORT_3) !== true) {
			$error .= '<br><i>Redis 3 => ERROR</i>';
			$err_count++;
		} else {
			$error .= '<br>Redis 3 => OK';
		}
		$error .= '<br>**************************************************<br><br>';
		//*********** connect redis end ************


		//checking summary 
		$error .= '<b>';
		if ($err_count == 0) {
			$error .= 'It Is Ready For Installing Centcount Analytics';
		} else if ($err_count == 1) {
			$error .= 'There Is ' . $err_count . ' Issue Need To Be Solved Before Installing Centcount Analytics';
		} else {
			$error .= 'There Are ' . $err_count . ' Issues Need To Be Solved Before Installing Centcount Analytics';
		}
		$error .= '</b><br><br>';

		//return error
		return $error;

}

function autoresponse($to, $cc_pw, $cc_id, $cc_code) {
	
	$subject = 'CA Super Administrator Information';
	$v_url = 'https://' . $_SERVER['HTTP_HOST'] . '/verify.php?id=' . $cc_id . '&vcode=' . $cc_code ;
	$login_url = 'https://' . $_SERVER['HTTP_HOST'] . '/login.php';
	$message = "
	<html>
	<head>
	<title>CA Super Administrator Information</title>
	</head>
	<body>
	<div  style='font-family: Microsoft Yahei,Arial,Verdana; font-size:13px;'>
	
	<p>
	Hi,<br/><br/>
	Thank you for using Centcount Analytics. Here is your super administrator information<br/><br/>
	Click on the following link to verify your email address and activate your account<br/>
	<a href='" . $v_url . "' style='color:#39f; text-decoration:underline;'>" . $v_url . "</a><br/><br/>
	
	<u><b>Login Details:</b></u><br/><br/>
	<i>URL:</i><a href='" . $login_url . "' style='color:#39f; text-decoration:underline;'><i>" . $login_url . "</i></a><br/><br/>
	<i>Username:". $to . "</i><br/><br/>
	<i>Password:". $cc_pw . "</i><br/><br/>
	<i>User ID:". $cc_id . "</i><br/><br/><br/>
	
	Regards,<br/>
	WM Jonssen<br/>
	Centcount Analytics<br/>
	<a href='https://www.centcount.com' style='color:#39f; text-decoration:underline;'>https://www.centcount.com</a><br/><br/>
	
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
	$headers .= 'From: <'.AUTORESPONSE_MAIL.'>';

	$ret = mail($to,$subject,$message,$headers);
	
	return $ret;
	
}

function check_table($con, $tb, $db) {
	
		$ret = false;
		
		$sql = "SHOW TABLES FROM {$db}";
		$result = mysqli_query($con, $sql);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				if ($row[0] == $tb) {
					$ret = true;
					break;
				}
			}
			mysqli_free_result($result);
		}

		return $ret;

}
	
?> 


<!DOCTYPE html>

<head>

<title>Centcount Analytics - Install CA</title>
<?php echo META_TEXT(); ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

<style type="text/css">
p{padding:15px;}
b{font-weight:bold;}
i{color:red;}
</style>

</head>

<body>

<div id="title">
	<h1>Install CA</h1>
</div>

<div id="bodyframe">

	<div class="framebody">

		<?php
			if ($err != '') {
				echo '<div class="errmsg dShow" id="errormsg">'. $err .'</div>';
			} else {
				echo '<div class="errmsg" id="errormsg"></div>';
			}
		?>

		<form name="register" method="POST"  action="">

			<table>

				<tr><td><input class="email" type="text" name="dbuser" id="MySQL Login Username" maxlength="255" value="<?php echo isset($_POST['dbuser']) ? $_POST['dbuser'] : ''; ?>" placeholder="Type MySQL administrator username here" onblur="checkUser(this.id)" onfocus="setStyle(this.id)"/></td></tr>
				
				<tr><td><input class="pwd" type="password" name="dbpw" id="MySQL Login Password" maxlength="16" placeholder="Type MySQL administrator password here" onBlur="checkPW(this.id)" autocomplete="new-password" onfocus="setStyle(this.id)"/></td></tr>

				<tr><td><input class="email" type="text" name="username" id="Administrator Username" maxlength="255" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" placeholder="Type administrator login email here" onblur="checkEmail(this.id)" onfocus="setStyle(this.id)"/></td></tr>
				
				<tr><td><input class="pwd" type="password" name="password" id="Administrator Password" maxlength="16" placeholder="Type administrator login password here" onBlur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td></tr>
				
				<tr><td><input class="pwd" type="password" name="confirm" id="Confirm Password" maxlength="16" placeholder="Repeat administrator login password here" onBlur="matchPW('Administrator Password', this.id)" onfocus="setStyle(this.id)"/></td></tr>
				
				<tr><td><input class="short" type="text" name="captcha" id="Captcha" maxlength="4" placeholder="Captcha" onBlur="checkCaptcha(this.id)" onfocus="setStyle(this.id)" />
						<div class="suggestion" styel="text-align:left;">
							<img src="validcode.php?rnd=<?php echo mt_rand(1E7,9E8); ?>" class="vcode" id="code"/>
							<a class="fresh" href="javascript:changeCode()" title="Refresh"></a>
						</div>
				</td></tr>
				
				<tr><td><button id="init" type="submit" name="submit">Install CA</button></td></tr>
			
			</table>

		</form>
		
	</div>

</div>


<div id="CheckEnv" class="frameagree" <?php echo ($err != '' ? '' : 'style="display:block;"') ?>>
	
	<div class="agreebtn">
		Check Server Environment
	</div>
			
	<div class="agreebox">
		<p>
			<?php echo CheckEnv(); ?>
		</p>
	</div>
			
	<div class="agreebtn">
		<a href="install.php?act=check" style="margin-right:100px;">Check Again</a>
		<a href="javascript:hide_me('CheckEnv');">Next Step</a>
	</div>

</div> 


<div id="agreement" class="frameagree" <?php echo ($chk || $err != '' ? '' : 'style="display:block;"') ?>>
	
	<div class="agreebtn">
		Centcount Analytics Agreement
	</div>
			
	<div class="agreebox">
<p>
<b>Please read this agreement carefully before installing and using our software.</b><br/><br/>
Copyright &copy; 2015-2018, WM Jonssen.<br/>
All Rights Reserved.<br/><br/>

This software license agreement (hereinafter the “Agreement”) is a legal agreement between the user (hereinafter “You” or the “User”) and Author/Owner of Centcount Analytics (hereinafter “Author”) for the software products (hereinafter the “Software”) and related services (hereinafter the “Service”) that accompanies this Agreement, as may be updated or replaced by feature enhancements, software updates or maintenance releases and any services that may be provided by Author under this Agreement. You are not allowed to download, install or use the Software or to use Services unless you accept all the terms and conditions of this Agreement. Your download, installation and use of the Software shall be regarded as your acceptance of the Agreement and your agreement to be bound by all the terms and conditions of this Agreement.<br/><br/>

This Agreement, between the User and Author, is an agreement regarding the User's downloading, installation, use and copying of the Software, and the use of Services provided by Author. This Agreement stipulates the rights and duties of You and Author regarding the licensed use and related services. "The User" or "You" refers to a person (who is 18 years of age or older, any parent or guardian of a person under the age of 18 may accept this Agreement on behalf of a user) or entity who obtains the Software authorization license and/or the Software products by means of the Software license and the account registration as provided by Author.<br/><br/>

This Agreement may be updated by Author at any time and without prior notice to the User. After the updated Agreement is issued, it shall replace the original Agreement effectively. Users can read the updated Agreement by logging in the Software or checking on the Website at any time. After Author has updated the clauses of Agreement, the User should stop using software and services provided by Author if he/she does not agree with the clauses amended, otherwise it will be deemed that the user has accepted the Agreement as amended.<br/><br/>

Except those explicitly stated in the Agreement, the Agreement does not stipulate the terms of service for other services offered by Author which the User visits when using the “Software”. Those services are usually regulated by other separate terms of service and the User should be familiar with and confirm his/her acceptance of those terms of service when using the relevant services. If there are conflicts between separate terms of service and this Agreement, the separate terms of service will govern. By using those services, the User shall be regarded as having accepted the relevant terms of service.<br/><br/>

<b>1. Grant of License</b><br/>
Subject to the terms and conditions of this Agreement, Author grants to you a limited, non-exclusive, worldwide license to install, download and use a single instance of the Software on a single website server through a single installation for non-commercial purposes for free.<br/><br/>

You can modify the source code (if being provided) or interface of the Software if your modification is strictly under the Agreement and to beautify your website.<br/><br/>

When using the Software, the User shall comply with the relevant national laws and policies, protect the national interests, safeguard the national security and comply with this Agreement. The User, but not Author, shall be fully responsible for all liabilities resulting from the illegal use of the Software or breach of the Agreement. If the User's misbehavior causes loss to Author, Author is entitled to demand the User to provide compensation, cease provision of the service immediately and keep relevant records. Moreover, if the User violates laws or the Agreement and correlative clauses of services stipulated in other services of Author by using the Software, Author has the right to take these measures including, but not limited to, interruption of use license, stopping of services, restriction of use, legal investigation, etc. considering the nature of the User’s behaviors and without a prior notice to the User.<br/><br/>

You have the entire property of all the members’ data, information and articles in your website which is powered by the Software; that is to say, you must assume solely all the relevant liabilities concerns to the contents in your website.<br/><br/>

You may use the Software for commercial means after purchase of the commercial license. Moreover, according to the license you purchase you will get technical support from Author in specified term and manner. Commercial users are prior to submitting ideas and opinions to Author, but without any guarantee of acceptance.<br/><br/>

You can download application(s) for your website from Centcount Analytics official website after you have paid appropriate fee to the author/owner of the application(s).<br/><br/>

<b>2. License Restrictions</b><br/>
You cannot use the Software for commercial or profit purposes (include but are not limited to company websites, operating websites or other for-profit websites) unless you have been licensed to. To purchase the license, please visit <a href="https://www.centcount.com">https://www.centcount.com</a> or email to wm.jonssen@gmail.com for more information.<br/><br/>

You may not rent, sublicense, assign, lease, loan, resell, distribute, publish or network the Software or related materials or create derivative works based upon the Software or any part thereof.<br/><br/>

You may not use the Software to engage in or allow others to engage in any illegal activity. You may not use the Software to engage in any activity that will violate the rights of third parties, including, without limitation, copyrights, trademarks, publicity rights, privacy rights, other proprietary rights, or rights against defamation of third parties.<br/><br/>

You cannot remove or modify the copyright information and relevant links under any circumstances, such as <a href="https://www.centcount.com">https://www.centcount.com</a>, in the foot of web pages without the prior written consent of Author.<br/><br/>

You cannot reverse engineer, decompile or disassemble, copy, modify, link, reproduce, publish or develop derivative products of the applications that you download from Centcount Analytics official website without the prior written permission from the application author/owner.<br/><br/>

<b>3. Disclaimer of Warranties and Limitations of Liabilities</b><br/>
The software and the accompanying files are provided “as-is”, and to the maximum extent permitted by applicable law, Author disclaims all other warranties, express or implied, by statute or otherwise, regarding the software and any related materials, including their fitness for a particular purpose, their quality, their merchantability, or any related services or content is secure, or is free from bugs, viruses, errors, or other program limitations nor does it warrant access to the internet or to any other services through the software.<br/><br/>

To the maximum extent permitted by applicable law, Author will not be liable for any indirect, special, incidental, or consequential damages(including damages for loss of business, loss of profits, or the like), whatever based on breach of contract, tort(including negligence), product liability or otherwise, even Author has been advised of the possibility of such damages. Author’ total liability to you for actual damages for any cause whatsoever will be limited to the purchase price amount paid by you for the software.<br/><br/>

Author is not liable for the content of any message in the websites powered by the Software.<br/><br/>

Author does not guarantee the legality, safety, integrity, authenticity and quality of the applications which uploaded into Centcount Analytics official website by any third party. You agree to judge on yourself and take fully responsibilities to the action of download from Centcount Analytics official website. But in any case, Author may stop the Store’s service and take appropriate actions, including but not limited to uninstall the associated applications, suspend the Store’s service in whole or in part, keep the relevant records to the relevant authorities, and Author will not undertake any direct, indirect or consequential liability to the actions thereof.<br/><br/>

<b>4. Termination</b><br/>
The License is effective until terminated. You may terminate the License at any time by uninstalling the Software and destroying all copies of the Software in any media. This Agreement may be terminated by Author immediately and without notice if you fail to comply with any term or condition of the License or this Agreement. Upon such termination, you must immediately cease using the Software, and assume relevant liabilities.<br/><br/>

Author reserves the right to change or add to the terms of this Agreement at any time (including but are not limited to Internet-based Solutions, pricing, technical support options, and other product-related policies), and to change, discontinue or impose conditions on any feature or aspect of the Software, or any Internet-based Solutions provided to you or made available to you in the official websites. Such changes will be effective upon notification by any means reasonable to give you actual or constructive notice including by posting such terms on the discuz.net website, or another website designated by Author. Your continued use of the Software will indicate your agreement to any such change.<br/><br/>

<b>5. Controlling Law</b><br/>
The Agreement is governed by and construed in accordance with the laws of P.R China. You hereby consent to the exclusive jurisdiction and venue in the HuiShan District court of the City of Wuxi.<br/>
</p>

<p style="text-align:right;">
Author/Owner: WM Jonssen<br/>
02-14-2018
</p>

	</div>
			
	<div class="agreebtn">
		<a href="javascript:hide_me('agreement');">Accept</a>
	</div>

</div> 


<?php echo HTML_FOOTER(); ?>


<script type="text/javascript">

	function hide_me(id) {
		document.getElementById(id).style.display = "none";
	}
	
	function changeCode(){ 
		document.getElementById("code").src = "validcode.php?id="+Math.random();
	}
	
	
	function setStyle(x){
		document.getElementById(x).style.borderColor="#39F";
	}
	
	function checkValue(x){
		if(document.getElementById(x).value == ""){
			document.getElementById("errormsg").innerHTML = x + " must not be empty";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
			return false;
		}
	}
	
	function checkUser(x){
		var username= document.getElementById(x).value;		//alert(username);
		if (username == ""){
			document.getElementById("errormsg").innerHTML = x + " must not be empty";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	} 
	
	function checkPW(x){
		var pw= document.getElementById(x).value;
		if (pw.length < 6){
			document.getElementById("errormsg").innerHTML = x + " must be at least 6 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
		
	}

	function matchPW(x,y) {
		var pw = document.getElementById(x).value;
		var cpw = document.getElementById(y).value;
		if (pw != cpw) {
			document.getElementById("errormsg").innerHTML = "Passwords do not match";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(y).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(y).style.borderColor = "#ccc";
		} 
	}

	function checkEmail(x) {
		if (checkValue(x) == false) return;
		var email = document.getElementById(x).value;

		var reg  = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
		if (reg.test(email) == false) {
			document.getElementById("errormsg").innerHTML = "Email is not valid";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	} 

	function checkCaptcha(x){
		if (checkValue(x) == false) return;
		var captcha= document.getElementById(x).value;
		//
		if (captcha.length < 4){
			document.getElementById("errormsg").innerHTML = "Captcha must be 4 characters long";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		} 
	}
	
	
</script></body>
</html>
