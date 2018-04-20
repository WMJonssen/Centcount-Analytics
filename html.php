<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free HTML Template CODE *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/19/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

//CA Analytics JS Code
define('CA_JS_CODE','');

//CA common JS Code
function JS_COMMON() {
	return '';
}


//generate header
function HTML_HEADER($flag=0, $id=0, $verify_code=0) {
	return '
<div id="header">
	<div id="logo">	
		<img class="mid" src="images/cc.png" alt="site icon"/><span class="guidespan"> Centcount Analytics</span>
	</div>

	<div id="guide">
		<ul>
			<li><a class="guidebtn" href="login.php">'.$GLOBALS['language']['guideLogin'].'</a></li>
			<li><a class="guidebtn" href="forgot.php">'.$GLOBALS['language']['PageForgotPW'].'</a></li>
			<li><a class="langbtn">'.$GLOBALS['language']['guideLanguage'].'</a>
  				<ul id="submenuLan">'.
					($flag === 0 ? '
					<li><a class="lastbtn" href="?lan=en-US">English</a></li>
					<li><a class="lastbtn" href="?lan=zh-CN">简体中文</a></li>
					<li><a class="lastbtn" href="?lan=zh-TW">繁體中文</a></li>
					':'
					<li><a class="lastbtn" href="?id=' . $id . '&vcode=' . $verify_code . '&lan=en-US">English</a></li>
					<li><a class="lastbtn" href="?id=' . $id . '&vcode=' . $verify_code . '&lan=zh-CN">简体中文</a></li>
					<li><a class="lastbtn" href="?id=' . $id . '&vcode=' . $verify_code . '&lan=zh-TW">繁體中文</a></li>').'
				</ul>
			</li>
		</ul>
	</div>
</div>';

}

//generate footer
function HTML_FOOTER() {
	return '<div id="footer">Powered by <a href="https://www.centcount.com">Centcount Analytics</a></div>';
}

//generate header meta info
function META_TEXT($keywords=NULL, $description=NULL) {
	return '
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="renderer" content="webkit|ie-comp|ie-stand"/>
<meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=yes"/>
<meta name="applicable-device" content="pc,mobile">
<meta name="author" content="WM Jonssen" />
<meta name="keywords" content=""/>
<meta name="description" content=""/>
<link href="images/cc.ico" rel="shortcut icon" type="image/x-icon" />
<link href="images/cc.ico" rel="icon" type="image/x-icon" />
<link href="images/cc_apple.png" rel="apple-touch-icon-precomposed">
';
}

//generate CSS
function CSS_TEXT() {
	return '';
}

?>