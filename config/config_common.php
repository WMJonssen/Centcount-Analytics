<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free COMMON CONFIG *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/31/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


/*********** Load Other Config Start **********/
@require 'config_redis.php';//load Redis Config
@require 'config_mail.php';//load Mail Config
/********* Load Other Config End ********/


/*************** COMMON CONFIG BEGIN **************/
//encode factor
define('ENCODE_FACTOR', 'abcdef123456');//RESET YOUR PRIVATE ENCODE FACTOR, IT IS VERY IMPORTANT
//mysql local host name
define('DB_HOST_LOCAL', '127.0.0.1');//set your mysql host name or ip
//mysql local root name
define('ROOT_USER_LOCAL', 'root');//set your mysql login username here (Creating Database Permission Is Necessary)
//mysql local root password
define('ROOT_PASSWORD_LOCAL', 'password');//set your mysql login password here
//administrator's timezone: PRC
define('ADMIN_TIMEZONE', 'PRC');//set administrator's timezone
//default timezone: PRC
define('DEFAULT_TIME_ZONE', 'PRC');//set default timezone
//error log host
define('ERROR_LOG_HOST', 'www.yourdomainname.com');//set Error Log host 
/**************** COMMON CONFIG END ***************/





/**********************************************/
/****** DO NOT CHANGE ANY CONSTANT BELOW ******/
/**********************************************/


/****** Local Database Information Start ******/
//mysql user database name
define('DB_NAME_USER', 'ccdata');
//mysql robot database name
define('DB_NAME_ROBOT', 'ccrobot');
//mysql error database name
define('DB_NAME_ERROR', 'ccerror');
/******* Local Database Information End *******/



//************* CA Constant Begin *************
//max bigint
define('MAX_BIGINT', 2^63-1 );
//max int
define('MAX_INT', 2147483647);
//max smallint
define('MAX_SMALLINT', 32767);
//max tinyint
define('MAX_TINYINT', 127);


//CA Realtime Array Length
define('CA_READY_ARRAY_LENGTH', 23);//23
//CA BASIC INFO Array Length
define('CA_BASIC_ARRAY_LENGTH', 91);//91
//CA TOTAL Array Length
define('CA_TOTAL_ARRAY_LENGTH', 112);//112
//CA Indicator Array Length
define('CA_INDICATOR_ARRAY_LENGTH', 31);//31
//CA Visitor Action Array Length
define('CA_VA_ARRAY_LENGTH', 8);//8
//CA Visitor Click Array Length
define('CA_VC_ARRAY_LENGTH', 35);//35
//************** CA Constant End **************


?>