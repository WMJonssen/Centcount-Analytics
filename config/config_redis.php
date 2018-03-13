<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free REDIS CONFIG *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


/************* Redis Config Start *************/
//redis instance 0 for kernel process (information of process, ticket, session)
define('REDIS_IP_0', '127.0.0.1');
define('REDIS_PORT_0', 6379);
define('REDIS_DB_0', 0);
//redis instance 1 for realtime visitor data (all information of realtime)
define('REDIS_IP_1', '127.0.0.1');
define('REDIS_PORT_1', 6379);
define('REDIS_DB_1', 1);
//redis instance 2 for CA javascript (site settings, site domains, robots list)
define('REDIS_IP_2', '127.0.0.1');
define('REDIS_PORT_2', 6379);
define('REDIS_DB_2', 2);
//redis instance 3 for session (session information)
define('REDIS_IP_3', '127.0.0.1');
define('REDIS_PORT_3', 6379);
define('REDIS_DB_3', 3);
/************** Redis Config End **************/


?>