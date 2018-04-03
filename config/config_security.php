<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free SECURITY CONFIG  *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/02/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

/************* Security Config Start *************/
//force ssl
define('FORCE_SSL', true);//If you don't have SSL Certificate, please set this const to "false".
//check ssl
define('IS_HTTPS', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 1 || $_SERVER['HTTPS'] === 'on') ? true : false);
//define security transfer protocol
define('PROTOCOL', IS_HTTPS ? 'https://' : 'http://');
//define API transfer protocol
define('CURL_PROTOCOL', 'https://');//If you don't have SSL Certificate, please set this const to "http://".
/************** Security Config End **************/

//check protocol
FORCE_SSL && (IS_HTTPS || die(header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])));


@require 'config_mail.php';//load Mail Config


?>