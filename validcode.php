<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Validate Code Generation PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

session_name('CASESSID');
session_start();
$charset = 'AaBbCDdEFfGgHhJKkMmNPpRrSsTtUVvWwXxYyZz2345679'; 
$code = ''; 
$codelen = 4; 
$_width = 135; 
$_height = 34; 
$_img; 
$font = dirname(__FILE__) . '/elephant.ttf'; 
$fontsize = 14; 
$fontcolor; 
$_len = strlen($charset) - 1;
for ($i = 0; $i < $codelen; $i++) {
    $code .= $charset[mt_rand(0, $_len)];
}
$_SESSION['vcode'] = strtolower($code);
$_img = imagecreatetruecolor($_width, $_height) or die('Cannot Initialize new GD image stream');
$color = imagecolorallocate($_img, mt_rand(160, 255), mt_rand(160, 255), mt_rand(160, 255));
imagefilledrectangle($_img, 0, $_height, $_width, 0, $color);
for ($i = 0; $i < 6; $i++) {
    $color = imagecolorallocate($_img, mt_rand(0, 150) , mt_rand(0, 150) , mt_rand(0, 150));
    imageline($_img, mt_rand(0, $_width) , mt_rand(0, $_height) , mt_rand(0, $_width) , mt_rand(0, $_height) , $color);
}
for ($i = 0; $i < 100; $i++) {
    $color = imagecolorallocate($_img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
    imagestring($_img, mt_rand(1, 5), mt_rand(0, $_width), mt_rand(0, $_height), '*', $color);
}
$_x = $_width / $codelen;
for ($i = 0; $i < $codelen; $i++) {
    $fontcolor = imagecolorallocate($_img, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
    imagettftext($_img, $fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(5, 10), $_height / 1.4, $fontcolor, $font, $code[$i]);
}
header('Content-type:image/png');
imagepng($_img);
imagedestroy($_img);
?>  


?>  

