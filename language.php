<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Language PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved. *
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

$LAN = init_language();

function init_language() {

		$lan = '';
		$lanfile = '';
	
		if (isset($_GET['lan'])) {
			$lan = $_GET['lan'];
		} else {
			if (isset($_SESSION['lan'])) {
				$lan = $_SESSION['lan'];
			} else {
				$lan = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en-US';
			}
		}
		$_SESSION['lan'] = $lan;
	
		$country = strtoupper(substr($lan, 3, 5));
		$lanuage = strtolower(substr($lan, 0, 2));
	
		switch ($lanuage) {
		default:
		case 'en':
			$lanfile = 'en-US.php';
			break;
		case 'zh':
			switch ($country) {
			default:
			case 'CN':
			case 'SG':
				$lanfile = 'zh-CN.php';
				break;
				
			case 'TW':
			case 'HK':
				$lanfile = 'zh-TW.php';
				break;
			}
			break;
		case 'de':
			$lanfile = 'de-DE.php';
			break;
		case 'ja':
			$lanfile = 'ja-JP.php';
			break;
		}
		
		require './language/lan-'.$lanfile;
		return $lanfile;

}

?>