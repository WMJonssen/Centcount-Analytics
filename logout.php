<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics User Logout PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

//DEFINE SESSION NAME
define('CA_SESSION_NAME','CASESSID');

//START SESSION.
session_name(CA_SESSION_NAME);
session_start();

//CLEAN SESSION COOKIE
if (isset($_COOKIE[CA_SESSION_NAME])) {
	setcookie(CA_SESSION_NAME, '', time() - 86400, '/');
}

//DESTROY SESSION.
session_destroy();

//GOTO LOGIN PAGE
header('Location: login.php');

?>