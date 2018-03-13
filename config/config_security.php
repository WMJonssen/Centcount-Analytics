<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free SECURITY CONFIG  *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


//force ssl
define('FORCE_SSL', true);
//check ssl
define('IS_HTTPS', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 1 || $_SERVER['HTTPS'] === 'on') ? true : false);
//define security transfer protocol
define('PROTOCOL', IS_HTTPS ? 'https://' : 'http://');
//define API transfer protocol
define('CURL_PROTOCOL', 'https://');

//check protocol
FORCE_SSL && (IS_HTTPS || die(header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])));


@require 'config_mail.php';//load Mail Config


?>