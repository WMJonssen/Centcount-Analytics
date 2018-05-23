<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Settings Cache PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


		if (isset($_SERVER['argv'][1])) {
			$siteid = (int)$_SERVER['argv'][1];   
			$force_update = (int)$_SERVER['argv'][2]; 
		} else if (isset($_GET['siteid'])) {
			$siteid = (int)$_GET['siteid'];
		}
		empty($siteid) AND exit;
		@require './config/config_common.php';
		ignore_user_abort(1); 
		set_time_limit(0); 
		$REDIS = new Redis();
		$REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true AND exit;
		$REDIS->SELECT(REDIS_DB_2);
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con))	exit;
		$db_selected = mysqli_select_db($con, 'site'.$siteid);
		if (!$db_selected) {
 			mysqli_close($con);
			exit;
  		}
		if ($REDIS->SETNX($siteid.'-Updating', '') !== true && $force_update === 0) exit;
		$REDIS->SET($siteid.'-UpdateTime', time());
		$REDIS->DEL($siteid.'-1');
		$REDIS->DEL($siteid.'-2');
		$REDIS->DEL($siteid.'-3');
		$REDIS->DEL($siteid.'-4');
		$REDIS->DEL($siteid.'-5');
		$REDIS->DEL($siteid.'-6');
		$result = mysqli_query($con, 'SELECT MD5, DomainType FROM domain');
		if ($result && mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$REDIS->SADD($siteid.'-'.$row['DomainType'], $row['MD5']);
			}
			mysqli_free_result($result);
		}
		$result = mysqli_query($con, "SELECT TimeZone,IPDatabase,SiteStatus FROM setting WHERE pKey=0");
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
			$REDIS->SET($siteid.'-SiteStatus', $row['SiteStatus']);
			$REDIS->SET($siteid.'-TimeZone', $row['TimeZone']);
			$REDIS->SET($siteid.'-IPDatabase', $row['IPDatabase']);
			mysqli_free_result($result);
		}
		$result = mysqli_query($con, 'SELECT NIP, DomainType FROM robot');
		if ($result && mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_assoc($result)) {
				$REDIS->SADD($siteid.'-'.$row['DomainType'], $row['MD5']);
			}
			mysqli_free_result($result);
		}
		$REDIS->DEL($siteid.'-Updating');
		mysqli_close($con);
		if (isset($_GET['siteid'])) echo 'Update OK!';


?>