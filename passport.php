<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Passport PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

if (isset($_GET['l'])) {

	header('Content-type: text/html; charset=utf-8');
	@require './config/config_common.php';

	session_name('CASESSID');
	session_start();

	$level = (int)$_GET['l'];

	switch ($level) {
	case 1://Normal	Data Access Permission

		isset($_SESSION['r']) ? $r = (int)$_SESSION['r'] : exit;
		isset($_GET['sid']) ? $sid = (int)$_GET['sid'] : exit;
		isset($_GET['r']) ? $v = (int)$_GET['r'] : exit;
		
		if ($v !== $r || $v === 0) exit;
		
		$t = time() + 30;
		
		$pass = md5($sid . $t . ENCODE_FACTOR);
		echo 't=' . $t . '&v=' . $pass . '&';

		exit;

	case 2://CA Heatmap Access Permission

		$ip = '';
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER)) {
				foreach (explode(',', $_SERVER[$key]) as $val) {
					if ((bool) filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
						$ip = $val;
						goto skip;
					}
				}
			}
		}
		skip:

		$ip === '' AND exit;

		isset($_SESSION['r']) ? $r = (int)$_SESSION['r'] : exit;
		isset($_GET['sid']) ? $sid = (int)$_GET['sid'] : exit;
		isset($_GET['r']) ? $v = (int)$_GET['r'] : exit;
		
		if ($v !== $r || $v === 0) exit;
		
		$t = time() + 86400;
		
		$pass = md5($sid . $t . ENCODE_FACTOR);
		echo 'cahm_visa=' . $pass . $t;

		exit;

	case 4://Administrator Access Permission
	
		if (isset($_SESSION['admin'])) {
			if ($_SESSION['admin'] != 4 && $_SESSION['admin'] != 1) exit;
		} else {
			exit;
		}
		
		isset($_SESSION['r']) ? $r = (int)$_SESSION['r'] : exit;
		isset($_GET['uid']) ? $uid = (int)$_GET['uid'] : exit;
		isset($_GET['r']) ? $v = (int)$_GET['r'] : exit;
		
		if ($v !== $r || $v === 0) exit;
		
		$t = time() + 30;
		
		$pass = md5($uid . $t . ENCODE_FACTOR . '4');
		echo 't=' . $t . '&v=' . $pass . '&';

		exit;
	}

}

exit;

?>  
