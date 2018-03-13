<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Relogin PHP Code *
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
require 'language.php';
require 'html.php';

//CLEAN SESSION COOKIE
if (isset($_COOKIE['CASESSID'])) {
	setcookie('CASESSID', '', time() - 86400, '/');
}

//DESTROY SESSION.
session_destroy();


global $tip;
$tip = isset($_GET['tip']) ? filter_var($_GET['tip'], FILTER_SANITIZE_STRING) : 'No Tip';


?>

<!DOCTYPE html>

<head>

<title>Centcount Analytics - Session Expired Error</title>
<?php echo META_TEXT() ?>
<?php echo CA_JS_CODE ?>
<link href="css/common.css" rel="stylesheet" type="text/css"/>

</head>

<body>


<?php echo HTML_HEADER(); ?>


<div id="title">
	<h1><?php echo $GLOBALS['language']['PageRelogin'] ?></h1>
</div>


<div id="bodyframe">

	<div class="framebody">

		<div class="errmsg" id="errormsg"><?php echo $tip ?></div>
		<div class="bBox sP15">
			<a class="btnB" href="login.php"><?php echo $GLOBALS['language']['PageRelogin'] ?></a>
		</div>

	</div>

</div>


<?php echo HTML_FOOTER(); ?>


<?php echo JS_COMMON(); ?>


</body>

</html>
