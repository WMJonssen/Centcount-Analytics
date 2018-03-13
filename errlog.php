<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Error Log PHP Code *
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


if ($_GET) {

	$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
	if (mysqli_connect_errno($con)) exit;
		
	$db_selected = mysqli_select_db($con, DB_NAME_ERROR);
	if (!$db_selected) {
		if (!mysqli_query($con, 'CREATE DATABASE IF NOT EXISTS '.DB_NAME_ERROR.' DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci')) {
			mysqli_close($con);
			exit;
		}

		$db_selected = mysqli_select_db($con, DB_NAME_ERROR);
		if (!$db_selected) {
			mysqli_close($con);
			exit;
		}
	}
		
		
	if (!check_table_exist($con, 'jserror')) {
		$sql = 'CREATE TABLE IF NOT EXISTS jserror (
pKey int AUTO_INCREMENT NOT NULL PRIMARY KEY, 
SiteID bigint NOT NULL DEFAULT 0, 
VID bigint NOT NULL DEFAULT 0, 
RecordNo bigint NOT NULL DEFAULT 0,
ErrorName varchar(128) NOT NULL DEFAULT "", 
ErrorMsg varchar(1024) NOT NULL DEFAULT "",
ErrorPosition varchar(128) NOT NULL DEFAULT "",  
Referrer varchar(1024) NOT NULL DEFAULT "", 
Page varchar(1024) NOT NULL DEFAULT "", 
Agent varchar(512) NOT NULL DEFAULT "",
Extra varchar(255) NOT NULL DEFAULT "",
IP varchar(15) NOT NULL DEFAULT "",
RecordTime varchar(20) NOT NULL DEFAULT ""
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';

		if (!mysqli_query($con, $sql)) {
			mysqli_close($con);
			exit;
		}	
	}
	
	$err = 0;
	
	isset($_GET['siteid']) ? $siteid = (int)$_GET['siteid'] : $err++;
	isset($_GET['vid']) ? $vid = (int)$_GET['vid'] : 0;
	isset($_GET['rn']) ? $rn = (int)$_GET['rn'] : $err++;
	isset($_GET['name']) ? $name = SDATA($con,$_GET['name'],1,128) : $err++;
	isset($_GET['msg']) ? $msg = SDATA($con,$_GET['msg'],1,1024) : $err++;
	isset($_GET['pos']) ? $pos = SDATA($con,$_GET['pos'],1,128) : $err++;
	isset($_GET['rf']) ? $rf = SDATA($con,$_GET['rf'],1,1024) : $err++;
	isset($_GET['page']) ? $page = SDATA($con,$_GET['page'],1,1024) : $err++;
	isset($_GET['agent']) ? $agent = SDATA($con,$_GET['agent'],1,512) : $err++;
	isset($_GET['ex']) ? $ex = SDATA($con,$_GET['ex'],1,255) : $err++;
	$ip = get_ip();
	
	if ($err) exit;
	
	date_default_timezone_set('PRC');
	$now = date('Y-m-d H:i:s', time());
	
	$sql = "INSERT INTO jserror (SiteID,VID,RecordNo,ErrorName,ErrorMsg,ErrorPosition,Referrer,Page,Agent,Extra,IP,RecordTime) VALUES($siteid, $vid, $rn,'{$name}','{$msg}','{$pos}','{$rf}','{$page}','{$agent}','{$ex}','{$ip}','{$now}')";
	mysqli_query($con, $sql);
	mysqli_close($con);

	autoresponse($siteid,$rn,$vid,$name,$msg,$pos,$rf,$page,$agent,$ex,$ip,$now);

}

function autoresponse($siteid,$rn,$vid,$name,$msg,$pos,$rf,$page,$agent,$ex,$ip,$now){

	$subject = "Notification of CA JS Error";

	$message = "
	<html>
	<head>
	<title>CA JS Error</title>
	</head>
	<body>
	<div style='font-family: Microsoft Yahei,Arial,Verdana; font-size:13px;'>
	
	<p>
	Hi,<br/><br/>
	You received a notification message from JS CA Error Log<br/><br/><br/>
	
	<u><b>Details:</b></u><br/><br/>
	<i>Site ID: ".$siteid. "</i><br/><br/>
	<i>VID: ".$vid. "</i><br/><br/>
	<i>Record No: ".$rn. "</i><br/><br/>
	<i>Error Name: ". $name . "</i><br/><br/>
	<i>Error Msg: ". $msg . "</i><br/><br/>
	<i>Error Position: ". $pos . "</i><br/><br/>
	<i>Referrer: ". $rf . "</i><br/><br/>
	<i>Page: ". $page . "</i><br/><br/>
	<i>Agent: ". $agent . "</i><br/><br/>
	<i>Extra: ". $ex . "</i><br/><br/>
	<i>IP: ". $ip . "</i><br/><br/>
	<i>Remote IP: ". $_SERVER['REMOTE_ADDR'] . "</i><br/><br/>
	<i>Log Time: ". $now ."</i><br/><br/><br/><br/>
	
	Regards,<br/>
	Centcount Team<br/><br/>
	
	This is an auto-response mail. Please do not reply.<br/>
	
	</p>
	
	</div>
	</body>
	</html>
	";
	
	$headers =  "MIME-Version: 1.0\r\n".
				"Content-type: text/html; charset=utf-8\r\n".
				"From: <js-error@centcount.com>";
	
	return mail(ADMIN_MAIL, $subject, $message, $headers);
}

function get_ip() {
		foreach (array('REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER)) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if ((bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return $ip;
				}
			}
		}
		return '';
}

function check_table($con, $tb, $db) {
	$ret = false;
	
	$sql = 'SHOW TABLES FROM ' . $db;
	$result = mysqli_query($con, $sql);
	if ($result && mysqli_num_rows($result)) {
		while ($row = mysqli_fetch_row($result)) {
			if ($row[0] == $tb){
				$ret = true;
				break;
			}
		}
		mysqli_free_result($result);
	}
	return $ret;
}

function check_table_exist($con, $cTB) {

		$result = mysqli_query($con, "SHOW TABLES LIKE '{$cTB}'");
		if ($result && mysqli_num_rows($result)) {
			mysqli_free_result($result);
			return true;
		}
		return false;

}

function SDATA($con, $val, $opt, $maxL, $minL=0) {
	switch ($opt) {
	case 1://string with sanitize & check length, use sanitized data
		$val = rawurldecode($val);
		$encoding = mb_detect_encoding($val, 'UTF-8, GB18030', true);
		switch ($encoding) {
		case 'UTF-8':
			break;
		case 'GB18030':
			$val = mb_convert_encoding($val, 'UTF-8', 'GB18030');
			break;
		default:
			$val = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
			break;
		}
		if (mb_detect_encoding($val, 'UTF-8', true) === false) return ''; 
		
		$val = filter_var($val, FILTER_SANITIZE_STRING);
		$val = mysqli_real_escape_string($con, $val);
		if (mb_strlen($val,'UTF-8') > $maxL) {
			return mb_substr($val, 0, $maxL, 'UTF-8');
		} else {
			return $val;
		}
	case 2://format int data and check length
		$tmp = (int)$val;
		return ($tmp > $maxL || $tmp < $minL) ? $def : $tmp;
	}

	return NULL;
}

?> 
