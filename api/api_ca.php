<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Access Data API PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

header('Access-Control-Allow-Origin: *');
header('Content-type: text/html; charset=utf-8');
@require '../config/config_common.php';
	$sid = SDATA_OUT('sid', 6, 'EXIT');
	$t = SDATA_OUT('t', 6, 'EXIT');
	$v = SDATA_OUT('v', 4, 'EXIT', 32);
	$id = substr($sid, 0, -3);
	$q = SDATA_OUT('q', 0, '', 32);
	$q === 'heatmap' ? verify_cahm($sid, $t, $v) : verify_user($sid, $t, $v);
	$period = SDATA_OUT('period', 6, 0);
	$from = SDATA_OUT('from', 6, 0);
	$to = SDATA_OUT('to', 6, 0);
	$start = SDATA_OUT('start', 6, 0);
	$end = SDATA_OUT('end', 6, 20);
	$sortorder = SDATA_OUT('sortorder', 6, 0) ? 'ASC' : 'DESC';
	$tz = SDATA_OUT('tz', 0, '', 32);
	!date_default_timezone_set($tz) AND exit;
	$db_site = 'site' . $sid; 
	$tb_log  = 'log' . $from; 
	$tb_act  = 'act' . $from; 
	$tb_clk  = 'clk' . $from; 
	$tb_vid  = 'vid' . $from; 
	$tb_ind  = 'ind' . $from; 
	$db_con = false;
	switch ($q) {
	case 'overview':
		$type = SDATA_OUT('type', 9, 'EXIT');
		$where = "WHERE Type=0 AND MD5='01'";
		$today = $from;
		$from = strtotime($from);
		$yesterday = date('Ymd', $from - 86400);
		$day7 = date('Ymd', $from - 86400 * 8);
		$day30 = date('Ymd', $from - 86400 * 31);
		$arr_data = array();
		$arr_data[] = array(4,0,4,4,4,4,4);
		$tmp_arr = array('PV'=>0, 'UV'=>0, 'UPV'=>0, 'NV'=>0, 'RV'=>0, 'RVS'=>0, 'Visits'=>0, 'Bounces'=>0,'BounceRate'=>0, 'Exits'=>0, 'ExitRate'=>0, 'RV'=>0, 'AvgDelay'=>0, 'AvgReady'=>0, 'AvgLoad'=>0, 'AvgOnline'=>0, 'AvgMRX'=>0, 'AvgMRY'=>0, 'DREntry'=>0, 'SEEntry'=>0, 'RFEntry'=>0, 'Detail'=>'Today', 'Clicks'=>0, 'ValidClicks'=>0);
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$sql = gen_sql('ind', $db_con, $today, $today, 3, $where, '', '', '', '');
		if (isset($sql['MERGE'])) {
			$result = mysqli_query($db_con, $sql['MERGE']);
			if ($result && mysqli_num_rows($result)) {
				$arr_data[] =  mysqli_fetch_assoc($result);
				mysqli_free_result($result);
			} else {
				$arr_data[] = $tmp_arr;
			}
		} else {
			$arr_data[] = $tmp_arr;
		}
		$arr_data[1]['Detail'] = 'Today';
		$sql = gen_sql('ind', $db_con, $yesterday, $yesterday, 3, $where, '', '', '', '');
		if (isset($sql['MERGE'])) {
			$result = mysqli_query($db_con, $sql['MERGE']);
			if ($result && mysqli_num_rows($result)) {
				$arr_data[] =  mysqli_fetch_assoc($result);
				mysqli_free_result($result);
			} else {
				$arr_data[] = $tmp_arr;
			}
		} else {
			$arr_data[] = $tmp_arr;
		}
		$arr_data[2]['Detail'] = 'Yesterday';
		$sql = gen_sql('ind', $db_con, $day7, $yesterday, 3, $where, '', '', '', '');
		if (isset($sql['MERGE'])) {
			$result = mysqli_query($db_con, $sql['MERGE']);
			if ($result && mysqli_num_rows($result)) {
				$arr_data[] =  mysqli_fetch_assoc($result);
				mysqli_free_result($result);
			} else {
				$arr_data[] = $tmp_arr;
			}
		} else {
			$arr_data[] = $tmp_arr;
		}
		$arr_data[3]['Detail'] = 'Avg 7 Days';
		$sql = gen_sql('ind', $db_con, $day30, $yesterday, 3, $where, '', '', '', '');
		if (isset($sql['MERGE'])) {
			$result = mysqli_query($db_con, $sql['MERGE']);
			if ($result && mysqli_num_rows($result)) {
				$arr_data[] =  mysqli_fetch_assoc($result);
				mysqli_free_result($result);
			} else {
				$arr_data[] = $tmp_arr;
			}
		} else{
			$arr_data[] = $tmp_arr;
		}
		$arr_data[4]['Detail'] = 'Avg 30 Days';
		echo json_encode($arr_data);
		get_json_error($q);
		break;
	case 'map':
	case 'geo map':
		$type = SDATA_OUT('type', 9, 'EXIT');
		$where = 'WHERE Type=' . $type;
		$key = SDATA_OUT('key', 9, 'EXIT');
		get_mysql_query_string($key, $order, $title, $sqltext);
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$from = SDATA_OUT('from', 6, 'EXIT');
		$to = SDATA_OUT('to', 6, 'EXIT');
		$sql = gen_sql('ind', $db_con, $from, $to, ($q === 'map' ? 7 : 8), $where, $order, $order, $start, $end);
		$result = mysqli_query($db_con, $sql['COUNT']);
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_row($result);
			$total = (int)$row[0];
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['SUM']);
		if ($result && mysqli_num_rows($result)) {
			$sum = mysqli_fetch_row($result);
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['MERGE']);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = array($total, (int)$sum[0]);
			while ($row = mysqli_fetch_row($result))
			{
				$arr_data[] = $row;
			}
			mysqli_free_result($result);
			$REDIS = new Redis();
			$REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2);
			if ($REDIS->PING() !== '+PONG') exit;
			$REDIS->SELECT(REDIS_DB_2);
			$ipdb = $REDIS->GET($sid.'-IPDatabase');
			if (is_null($ipdb)) $ipdb = 0;
			$arr_data[0][2] = (int)$ipdb;
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;
	case 'realtime map':
		$RANG_MINUTE = floor((time() - 60) / 60) * 60;
		$REDIS = new Redis();
		$REDIS->CONNECT(REDIS_IP_1, REDIS_PORT_1);
		if ($REDIS->PING() !== '+PONG') exit;
		$REDIS->SELECT(REDIS_DB_1);
		$REDIS->ZREMRANGEBYSCORE('SVIDS'.$_GET['sid'], 0, $RANG_MINUTE);
		$total = $REDIS->ZCARD('SVIDS'.$_GET['sid']);
		if (($start + $end) > $total) $end = $total - $start;
		$CHECK_ARRAY = $REDIS->ZREVRANGE('SVIDS'.$_GET['sid'], $start, ($start + $end));
		$LEN = count($CHECK_ARRAY);
		$arr_data = array();
		if ($LEN) {
			$arr_data[] = array($LEN, $total);
			$arr_tmp = array();
			$ipdb = NULL;
			include '../ipdb.class.php';
			$IPH1 = new \IP2Location\Database('../ipdb/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::FILE_IO);
			define('IP_ALL', \IP2Location\Database::ALL);
			include '../vendor/autoload.php';
			$IPH2 = new \GeoIp2\Database\Reader('../ipdb/GeoLite2-City.mmdb');
			for ($i = 0; $i < $LEN; $i++) {
				$tmp = $REDIS->GET($CHECK_ARRAY[$i]);			
				$ip = $tmp ? get_data($tmp,'&ip') : '';
				if ($ip) {
					$ips = array();
					if ($ipdb === NULL) $ipdb = (int)get_data($tmp, '&ipdb');
					switch ($ipdb) {
					case 0:
						$ips = $IPH1->lookup($ip, IP_ALL);
						$Country = $ips['countryName']; 
						$Region = $ips['regionName']; 
						$City = $ips['cityName']; 
						$Latitude = $ips['latitude'];
						$Longitude = $ips['longitude'];
						break;
					case 1:
					case 2:
						$ips = $IPH2->city($ip);
						if ($ipdb === 1) {
							$Country = $ips->country->names['zh-CN']; 
							$Region = isset($ips->mostSpecificSubdivision->names['zh-CN']) ? $ips->mostSpecificSubdivision->names['zh-CN'] : $ips->mostSpecificSubdivision->name; 
							$City = isset($ips->city->names['zh-CN']) ? $ips->city->names['zh-CN'] : $ips->city->name; 
						} else {
							$Country = $ips->country->name; 
							$Region = $ips->mostSpecificSubdivision->name; 
							$City = $ips->city->name; 
						}
						$Latitude = $ips->location->latitude; 
						$Longitude = $ips->location->longitude; 
						break;
					}
					if (isset($arr_tmp[$City])) {
						$arr_tmp[$City][1]++;
					} else {
						$arr_tmp[$City] = array($Country . ($Region ? ' - ' . $Region : '') . ($City ? ' - ' . $City : ''), 1, $Longitude . ',' . $Latitude);
					}
				} else {
					$arr_data[0][0]--;
					$arr_data[0][1]--;
				}
			}
			$arr_data[0][2] = (int)$ipdb;
			foreach($arr_tmp as $val) {
				$arr_data[] = $val;
			}
		} else {
			$arr_data[] = array(0, 0);
		}
		echo json_encode($arr_data);
		get_json_error($q);
		break;
	case 'visitsofsite':
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$arr_data = array();
		$time = time();
		$tmp = 'ind' . date('Ymd', $time);
		$sql = "SELECT PV,UV,Visits,RV FROM {$tmp} WHERE MD5='01'";
		$result = mysqli_query($db_con, $sql);
		if ($result && mysqli_num_rows($result)) {			
			$row = mysqli_fetch_assoc($result);
			$arr_data[0] = array((int)$row['PV'], (int)$row['UV'], (int)$row['Visits'], (int)$row['RV']);
			mysqli_free_result($result);
		} else {
			$arr_data[0] = array(0, 0, 0, 0);
		}
		$time -= 86400;
		$tmp = 'ind' . date('Ymd', $time);
		$sql = "SELECT PV,UV,Visits,RV FROM {$tmp} WHERE MD5='01'";
		$result = mysqli_query($db_con, $sql);
		if ($result && mysqli_num_rows($result)) {			
			$row = mysqli_fetch_assoc($result);
			$arr_data[1] = array((int)$row['PV'], (int)$row['UV'], (int)$row['Visits'], (int)$row['RV']);
			mysqli_free_result($result);
		} else {
			$arr_data[1] = array(0, 0, 0, 0);
		}
		echo json_encode($arr_data);
		get_json_error($q);
		break;
	case 'rtline':
		$time = time();
		$RT = floor(($time - 900) / 60) * 60;
		$RANG_MINUTE = $RT - 60;
		$arr_data = array();
		for ($i = 0; $i < 15; $i++) {
			$RT += 60;
			$RM = date('H:i', $RT);
			$arr_data[$RM] = array($RM, 0, 0, 0);
		}
		$REDIS = new Redis();
		$REDIS->CONNECT(REDIS_IP_1, REDIS_PORT_1);
		if ($REDIS->PING() !== '+PONG') exit;
		$REDIS->SELECT(REDIS_DB_1);
		$REDIS->ZREMRANGEBYSCORE('SMINS'.$_GET['sid'], 0, ($RANG_MINUTE - 60));
		$CHECK_ARRAY = $REDIS->ZRANGE('SMINS'.$_GET['sid'], 0, -1, false);
		$LEN = count($CHECK_ARRAY);
		if ($LEN > 0) {			
			for ($i = 0; $i < $LEN; $i++)
			{
				$row = explode(',', $CHECK_ARRAY[$i]);
				if (count($row) === 4) $arr_data[$row[0]] = array($row[0], (int)$row[1], (int)$row[2], (int)$row[3]);
			}
		}
		$CHECK_ARRAY = $REDIS->GET('MinData'.$_GET['sid']);
		if ($CHECK_ARRAY) {
			$tmp = substr($CHECK_ARRAY,10,5);
			$row = explode(',', substr($CHECK_ARRAY,16));
			if (count($row) === 3) $arr_data[$tmp] = array($tmp, (int)$row[0], (int)$row[1], (int)$row[2]);
		}
		$arr_out = array();
		$arr_out[] = array('PV', 'UV', 'IP');
		foreach ($arr_data as $key=>$value)
		{
			$arr_out[] = $value;
		}
		echo json_encode($arr_out);
		get_json_error($q);
		break;
	case 'online':
	case 'online log':
	case 'online no':
		$RANG_MINUTE = floor((time() - 60) / 60) * 60;
		$REDIS = new Redis();
		$REDIS->CONNECT(REDIS_IP_1, REDIS_PORT_1);
		if ($REDIS->PING() !== '+PONG') exit;
		$REDIS->SELECT(REDIS_DB_1);
		$REDIS->ZREMRANGEBYSCORE('SVIDS'.$_GET['sid'], 0, $RANG_MINUTE);
		$total = $REDIS->ZCARD('SVIDS'.$_GET['sid']);
		if (($start + $end) > $total) $end = $total - $start;
		$PEAK_PV = $REDIS->GET('PeakPV'.$_GET['sid']);
		$PEAK_UV = $REDIS->GET('PeakUV'.$_GET['sid']);
		$PEAK_IP = $REDIS->GET('PeakIP'.$_GET['sid']);
		$arr_data = array();
		$arr_data[] = array($total, $start, $end, $PEAK_PV, $PEAK_UV, $PEAK_IP);
		if ($q === 'online no') {
			echo json_encode($arr_data);
			get_json_error($q);
			break;
		}
		$CHECK_ARRAY = $REDIS->ZREVRANGE('SVIDS'.$_GET['sid'], $start, ($start + $end -1));
		$LEN = count($CHECK_ARRAY);
		if ($LEN) {
			$ipdb = NULL;
			include '../ipdb.class.php';
			$IPH1 = new \IP2Location\Database('../ipdb/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::FILE_IO);
			define('IP_ALL', \IP2Location\Database::ALL);
			include '../vendor/autoload.php';
			$IPH2 = new \GeoIp2\Database\Reader('../ipdb/GeoLite2-City.mmdb');
			for ($i = 0; $i < $LEN; $i++) {
				$tmp = $REDIS->GET($CHECK_ARRAY[$i]);			
				$ip = $tmp ? get_data($tmp,'&ip') : '';
				if ($ip) {
					$ips = array();
					if ($ipdb === NULL) $ipdb = (int)get_data($tmp,'&ipdb');
					switch ($ipdb) {
					case 0:
						$ips = $IPH1->lookup($ip, IP_ALL);
						$Country = $ips['countryName']; 
						$Region = $ips['regionName']; 
						$City = $ips['cityName']; 
						$CountryCode = $ips['countryCode']; 
						break;
					case 1:
					case 2:
						$ips = $IPH2->city($ip);
						if ($ipdb === 1) {
							$Country = $ips->country->names['zh-CN']; 
							$Region = isset($ips->mostSpecificSubdivision->names['zh-CN']) ? $ips->mostSpecificSubdivision->names['zh-CN'] : $ips->mostSpecificSubdivision->name; 
							$City = isset($ips->city->names['zh-CN']) ? $ips->city->names['zh-CN'] : $ips->city->name; 
						} else {
							$Country = $ips->country->name; 
							$Region = $ips->mostSpecificSubdivision->name; 
							$City = $ips->city->name; 
						}
						$CountryCode = $ips->country->isoCode; 
						break;
					case 3:
					case 4:
						$ips = $ipdb === 3 ? \IPIP\IPZH::find($ip) : \IPIP\IPEN::find($ip); 
						$Country = $ips[0]; 
						$Region = $ips[1]; 
						$City = $ips[2]; 
						$CountryCode = $City;
						break;
					}
					$Country = rawurlencode($Country);
					$Region = rawurlencode($Region); 
					$City = rawurlencode($City); 
					$tmp .= '&l1=' . $Country . '&l2=' . $Region . '&l3=' . $City . '&l4=' . $CountryCode;
				} else {
					$tmp .= '&l1=Unknown&l2=Unknown&l3=Unknown&l4=Mars';
				}
				$arr_data[] = $tmp;
			}
		}
		echo json_encode($arr_data);
		get_json_error($q);
		break;
	case 'visitor log':
	case 'robot log':
		$type = SDATA_OUT('type', 9, 'EXIT');
		switch ($type) {
		case 0:
			$where = 'WHERE VID>30000000000';
			break;
		case 2:
			$where = 'WHERE VID<30000000000';
			break;
		}
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$from = SDATA_OUT('from', 6, 'EXIT');
		$to = SDATA_OUT('to', 6, 'EXIT');
		$sql = gen_sql('vid', $db_con, $from, $to, 5, $where, '', '', $start, $end);
		$result = mysqli_query($db_con, $sql['SUM']);
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_row($result);
			$total = (int)$row[0];
			if (($start + $end) > $total) $end = $total - $start;
			mysqli_free_result($result);
		} else {
			exit;
		}
		$sql = gen_sql('vid', $db_con, $from, $to, 6, $where, 'RecordNo', 'DESC', $start, $end);
		$result = mysqli_query($db_con, $sql['MERGE']);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = array($total,$start,$end);
			while ($row = mysqli_fetch_assoc($result)) {
				$last_rn = 0;
				$tb = 'log' . date('Ymd', (int)($row['RecordNo'] / 1E6));
				$sql_1 = "SELECT RecordNo,DelaySecond,ReadySecond,LoadSecond,OnlineSecond,Page,Referrer,SE,Keyword,Device,IP,Country,Region,City,MinReadX,MinReadY,MaxReadX,MaxReadY,CountryISO,EntryCode,ExitCode,Visits,PageViews,IsFakeData,PageAction FROM {$tb} WHERE VID={$row['VID']}";	
				$arr_data_1 = read_record($db_con, $sql_1, $last_rn);
				$sql_2 = "SELECT * FROM {$tb} WHERE RecordNo={$last_rn}";
				$arr_data_2 = read_record($db_con, $sql_2, $last_rn);
				if ($arr_data_1 !== NULL && $arr_data_2 !== NULL) {
					$arr_data[] = array($arr_data_2, $arr_data_1);
				} else {
					$arr_data[0][0]--;
				}
			}
			mysqli_free_result($result);
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;	
	case 'table':
	case 'clicks':
	case 'exitpage':
	case 'bouncepage':
	case 'allpage':
		$type = SDATA_OUT('type', 9, 'EXIT');
		switch ($type) {
			case 63:
				$where = 'WHERE Type=1 AND Visits>0';
				break;
			case 64:
				$where = 'WHERE Type=1 AND Bounces>0';
				break;
			case 65:
				$where = 'WHERE Type=1 AND Exits>0';
				break;
			case 66:
				$where = 'WHERE Type=1 AND Clicks>0';
				break;
			case 67:
				$where = 'WHERE Type=3 OR Type=4 OR Type=5';
				break;
			default:
				$where = 'WHERE Type=' . $type;
				break;
		}
		$key = SDATA_OUT('key', 9, 'EXIT');
		get_mysql_query_string($key, $order, $title, $sqltext);
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$from = SDATA_OUT('from', 6, 'EXIT');
		$to = SDATA_OUT('to', 6, 'EXIT');
		$sql = gen_sql('ind', $db_con, $from, $to, 0, $where, $order, $sortorder, $start, $end);
		$result = mysqli_query($db_con, $sql['COUNT']);
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_row($result);
			$total = (int)$row[0];
			if (($start + $end) > $total) $end = $total - $start;
			$count = array($total, $start, $end);
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['SUM']);
		if ($result && mysqli_num_rows($result)) {
			$sum = mysqli_fetch_assoc($result);
			$sum['Detail'] = 'Total';
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['MERGE']);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = $count;
			$arr_data[] = $sum;
			while ($row = mysqli_fetch_assoc($result))
			{
				$arr_data[] = $row;
			}
			mysqli_free_result($result);
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;
	case 'pie':
		$type = SDATA_OUT('type', 9, 'EXIT');
		$key =  SDATA_OUT('key', 9, 'EXIT');
		switch ($type) {
			case 63:
			case 64:
				$where = 'WHERE Type=1 AND Visits>0';
				break;
			case 65:
				$where = 'WHERE Type=1 AND PV>0';
				break;
			case 66:
				$where = 'WHERE Type=1 AND Clicks>0';
				break;
			case 67:
				$where = 'WHERE Type=3 OR Type=4 OR Type=5';
				break;
			default:
				if ($key === 5) {
					$where = 'WHERE Type=1 AND Visits>0';
				} else{
					$where = 'WHERE Type=' . $type;
				}
				break;
		}
		get_mysql_query_string($key, $order, $title, $sqltext);
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$from = SDATA_OUT('from', 6, 'EXIT');
		$to = SDATA_OUT('to', 6, 'EXIT');
		$sql = gen_sql('ind', $db_con, $from, $to, 1, $where, $order, $sortorder, 0, $end);
		$result = mysqli_query($db_con, $sql['COUNT']);
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_row($result);
			$start = 0;
			$total = (int)$row[0];
			if (($start + $end) > $total) $end = $total - $start;
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['SUM']);
		if ($result && mysqli_num_rows($result)) {
			$sum = mysqli_fetch_row($result);
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['MERGE']);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = array($total,$start,$end,$title,(int)$sum[0]);
			while ($row = mysqli_fetch_row($result))
			{
				$arr_data[] = $row;
			}
			mysqli_free_result($result);
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;
	case 'bar':
		$type = SDATA_OUT('type', 9, 'EXIT');
		$key =  SDATA_OUT('key', 9, 'EXIT');
		switch ($type) {
			case 63:
			case 64:
				$where = 'WHERE Type=1 AND Visits>0';
				break;
			case 65:
				$where = 'WHERE Type=1 AND PV>0';
				break;
			case 66:
				$where = 'WHERE Type=1 AND Clicks>0';
				break;
			case 67:
				$where = 'WHERE Type=3 OR Type=4 OR Type=5';
				break;
			default:
				if ($key === 5) {
					$where = 'WHERE Type=1 AND Visits>0';
				} else{
					$where = 'WHERE Type=' . $type;
				}
				break;
		}
		get_mysql_query_string($key, $order, $title, $sqltext);
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$from = SDATA_OUT('from', 6, 'EXIT');
		$to = SDATA_OUT('to', 6, 'EXIT');
		$sql = gen_sql('ind', $db_con, $from, $to, 1, $where, $order, $sortorder, 0, $end);
		$result = mysqli_query($db_con, $sql['COUNT']);
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_row($result);
			$total = (int)$row[0];
			if (($start + $end) > $total) $end = $total - $start;
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['SUM']);
		if ($result && mysqli_num_rows($result)) {
			$sum = mysqli_fetch_row($result);
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['MERGE']);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = array($total, $start, $end, $title, (int)$sum[0]);
			while ($row = mysqli_fetch_row($result)) {
				$arr_data[] = $row;
			}
			mysqli_free_result($result);
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;
	case 'line':
		$type = SDATA_OUT('type', 9, 'EXIT');
		switch ($type) {
		case 0:
			$key = 'PV,UPV,Visits,UV,NV,RV';
			$title = 'PV,UPV,Visits,UV,NV,RV';
			break;
		case 1:
			$key = 'BounceRate,ExitRate';
			$title = 'Bounce Rate,Exit Rate';
			break;
		case 2:
			$key = 'AvgMRX,AvgMRY';
			$title = 'Avg MRX,Avg MRY';
			break;
		case 3:
			$key = 'AvgDelay,AvgReady,AvgLoad,AvgOnline';
			$title = 'Avg Delay,Avg DOM Ready,Avg Load,Avg Page Duration';
			break;
		case 11:
			$key = 'Visits';
			$title = 'Visits';
			break;
		case 12:
			$key = 'UV';
			$title = 'UV';
			break;
		case 13:
			$key = 'PV';
			$title = 'PV';
			break;
		case 14:
			$key = 'UPV';
			$title = 'UPV';
			break;
		case 15:
			$key = 'RV';
			$title = 'RV';
			break;
		case 16:
			$key = 'BounceRate';
			$title = 'Bounce Rate';
			break;
		case 17:
			$key = 'ExitRate';
			$title = 'Exit Rate';
			break;
		case 18:
			$key = 'AvgMRX';
			$title = 'Avg MRX';
			break;
		case 19:
			$key = 'AvgMRY';
			$title = 'Avg MRY';
			break;
		case 20:
			$key = 'AvgReady';
			$title = 'Avg DOM Ready';
			break;
		case 21:
			$key = 'AvgLoad';
			$title = 'Avg Load';
			break;
		case 22:
			$key = 'AvgOnline';
			$title = 'Avg Online';
			break;
		case 23:
			$key = 'Detail';
			$title = 'Server Time';
			break;
		case 24:
			$key = 'Detail';
			$title = 'Local Time';
			break;
		default:
			exit;
		}
		$time = time();
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$RT = floor($time - 2592E3);
		$arr_data = array();
		$arr_data[0] = explode(',', $title);
		if ($period == 2) {
			$from = $RT;
			$to = $RT + 2592E3;
		} else {
			$from = strtotime($from);
			$to = strtotime($to);
		}
		if ($to === $from) $arr_data[] = array('', array(0, 0, 0, 0, 0), ''); 
		for ($i = $from; $i <= $to; $i+=86400) {
			$RT = $i;
			$RD = date('m/d', $RT);
			$RW = ($to === $from) ? '0' : date('w', $RT);
			$tmp = 'ind' . date('Ymd', $RT);
			$sql = "SELECT {$key} FROM {$tmp} WHERE MD5='01'";
			$result = mysqli_query($db_con, $sql);
			if ($result && mysqli_num_rows($result)) {			
				$row = mysqli_fetch_row($result);
				$arr_data[] = array($RD, $row, $RW);
				mysqli_free_result($result);
			} else {
				$arr_data[] = array($RD, array(0, 0, 0, 0, 0), $RW);
			}
		}
		if ($to === $from) $arr_data[] = array('', array(0, 0, 0, 0, 0), ''); 
		echo json_encode($arr_data);
		get_json_error($q);
		break;
	case 'heatmap':
		$key =  SDATA_OUT('key', 1, 'EXIT', 32);
		$type = SDATA_OUT('type', 9, 'EXIT');
		switch ($type) {
		case 0:
			$where = "WHERE PageMD5='{$key}'";
			break;
		case 1:
			$where = 'WHERE VID=' . $key;
			break;
		case 2:
			$where = 'WHERE NodeActionType=' . $key;
			break;
		default:
			exit;
		}
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$from = SDATA_OUT('from', 6, 'EXIT');
		$to = SDATA_OUT('to', 6, 'EXIT');
		$sql = gen_sql('clk', $db_con, $from, $to, 2, $where, '', '', $start, $end);
		$result = mysqli_query($db_con, $sql['COUNT']);
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_row($result);
			$total = (int)$row[0];
			if (($start + $end) > $total) $end = $total - $start;
			mysqli_free_result($result);
		} else {
			exit;
		}
		$result = mysqli_query($db_con, $sql['MERGE']);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = array($total, $start, $end);
			while ($row = mysqli_fetch_row($result))
			{
				$arr_data[] = $row;
			}
			mysqli_free_result($result);
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;
	case 'daytrend':
		$type = SDATA_OUT('type', 9, 'EXIT');
		$type = 'Type=' . $type;
		$key = 'PV,UPV,Visits,UV,NV,RV';
		$time = time();
		if ($from != date('Ymd', $time)) {
			$RN = '24:00';
		} else {
			$RN = date('H:i', $time);
		}
		$db_con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, $db_site);
		$sql = "SELECT Detail,{$key} FROM {$tb_ind} WHERE {$type}";
		$result = mysqli_query($db_con, $sql);
		if ($result && mysqli_num_rows($result)) {
			$arr_data = array();
			$arr_data[] = explode(',', $key);
			$arr_data[0][6] = $RN; 
			while ($row = mysqli_fetch_row($result))
			{
				$arr_data[] = $row;
			}
			mysqli_free_result($result);
			echo json_encode($arr_data);
			get_json_error($q);
		} else {
			echo '';
		}
		break;
	}
	if ($db_con) mysqli_close($db_con);
exit;
function use_db($host, $user, $pw, $db) {
		$server = $_SERVER['HTTP_HOST'];
		$con = mysqli_connect($host, $user, $pw);
		if (mysqli_connect_errno($con)) {
			exit;
 		}
		$db_selected = mysqli_select_db($con, $db);
		if (!$db_selected) {
			mysqli_close($con);
			exit;
  		}
		return $con;
}
function read_record($con, $sql, &$last_rn) {
		$result = mysqli_query($con, $sql);
		if ($result && mysqli_num_rows($result)) {
			$arr = array();
			while ($row = mysqli_fetch_assoc($result)) { 
				$last_rn = $row['RecordNo'];
				$arr[] = $row;
			}
			mysqli_free_result($result);
			return $arr;
		}
		return NULL;	
}
function SDATA_OUT($key, $opt, $def, $maxL=0, $minL=0) {
		if (isset($_GET[$key])) {
			$val = $_GET[$key];
		} else {
			if ($def === 'EXIT') {
				exit;
			} else {
				return $def;
			}
		}
		switch ($opt) {
			case 0:
				$mval = filter_var($val, FILTER_SANITIZE_STRING);
				if (strlen($mval) !== strlen($val)) {
					return '';
				} else {
					if (strlen($val) > $maxL) {
						return substr($val, 0, $maxL);
					} else {
						return $val;
					}
				}
			case 1:
				$mval = filter_var($val, FILTER_SANITIZE_STRING);
				if (strlen($mval) > $maxL) {
					return substr($mval, 0, $maxL);
				} else {
					return $mval;
				}
			case 2:
				$tmp=(int)$val;
				return ($tmp > $maxL || $tmp < $minL ? 0 : $tmp);
			case 3:
				$mval = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION + FILTER_FLAG_ALLOW_THOUSAND);
				if (strlen($mval) !== strlen($val)) {
					return '';
				} else {
					if (strlen($val) > $maxL) {
						return '';
					} else {
						return $val;
					}
				}
			case 4:
				$mval = filter_var($val, FILTER_SANITIZE_STRING);
				if (strlen($mval) === $maxL) {
					return $val;
				} else {
					exit;
				}
			case 5:
				$mval = filter_var($val, FILTER_VALIDATE_EMAIL);
				if (strlen($mval) !== strlen($val)) {
					return '';
				} else {
					if (strlen($val) > $maxL) {
						return '';
					} else {
						return $val;
					}
				}
			case 6:
				$tmp = (int)$val;
				if ((string)$tmp !== (string)$val) {
					exit;
				} else {
					return $tmp;
				}
			case 7:
				if (strlen($val) > $maxL) {
					return substr($val, 0, $maxL);
				} else {
					return $val;
				}
			case 8:
				$tmp = (int)$val;
				if ((string)$tmp !== (string)$val) {
					exit;
				} else {
					return ($tmp - 20000000);
				}
			case 9:
				return (int)$val;
			case 10:
				return (float)$val;
		}
		return NULL;
}
function verify_user($sid, $t, $v) {
		$n = time();
		if ($t < $n) exit;
		$matchvisa = md5($sid . $t . ENCODE_FACTOR);
		if ($v === $matchvisa) return true;
		exit;
}
function verify_cahm($sid, $t, $v) {
		$n = time();
		if ($t < $n) die('911');
		$matchvisa = md5($sid . $t . ENCODE_FACTOR);
		if ($v === $matchvisa) return true;
		die('911');
}
function get_ip() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER)) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if ((bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
						return $ip;
					}
				}
			}
		}
		return '';
}
function get_mysql_query_string($key, &$order, &$title, &$sql='') {
	switch ($key) {
		case 1:
			$order = 'Visits';
			$title = 'Visits';
			break;
		case 2:
			$order = 'PV';
			$title = 'PV';
			break;
		case 3:
			$order = 'UV';
			$title = 'UV';
			break;
		case 4:
			$order = 'UPV';
			$title = 'UPV';
			break;
		case 5:
			$order = 'BounceRate';
			$title = 'Bounce Rate';
			$sql = 'IFNULL(SUM(Bounces)/SUM(Visits),0)*10000 AS BounceRate';
			break;
		case 6:
			$order = 'ExitRate';
			$title = 'Exit Rate';
			$sql = 'IFNULL(SUM(Exits)/SUM(PV),0)*10000 AS ExitRate';
			break;
		case 7:
			$order = 'AvgMRX';
			$title = 'Avg MRX';
			break;
		case 8:
			$order = 'AvgMRY';
			$title = 'Avg MRY';
			break;
		case 9:
			$order = 'AvgReady';
			$title = 'Avg DOM Ready';
			$sql = 'IFNULL(SUM(TotalReady)/SUM(ReadyTimes),0) AS AvgReady';
			break;
		case 10:
			$order = 'AvgLoad';
			$title = 'Avg Load';
			$sql = 'IFNULL(SUM(TotalLoad)/SUM(LoadTimes),0) AS AvgLoad';
			break;
		case 11:
			$order = 'AvgOnline';
			$title = 'Avg Online';
			$sql = 'IFNULL(SUM(TotalOnline)/SUM(PV),0) AS AvgOnline';
			break;
		case 12:
			$order = 'Detail';
			$title = 'Detail';
			break;
		case 13:
			$order = 'Exits';
			$title = 'Exits';
			break;
		case 14:
			$order = 'ValidClicks';
			$title = 'Valid Clicks';
			break;
		case 15:
			$order = 'Clicks';
			$title = 'Clicks';
			break;
		case 16:
			$order = 'Bounces';
			$title = 'Bounces';
			break;
		case 17:
			$order = 'SEEntry';
			$title = 'SE Entry';
			break;
		case 18:
			$order = 'RFEntry';
			$title = 'Backlink Entry';
			break;
		case 19:
			$order = 'DREntry';
			$title = 'Direct Entry';
			break;
		case 20:
			$order = 'AvgDelay';
			$title = 'Avg Delay';
			$sql = 'IFNULL(SUM(TotalDelay)/SUM(DelayTimes),0) AS AvgDelay';
			break;
		case 21:
			$order = 'NV';
			$title = 'NV';
			break;
		case 22:
			$order = 'RV';
			$title = 'RV';
			break;
		case 23:
			$order = 'RVS';
			$title = 'RVS';
			break;
		case 24:
			$order = 'Detail';
			$title = 'Local Time';
			break;
		case 25:
			$order = 'Detail';
			$title = 'Server Time';
			break;
		case 30:
			$order = 'AvgOnlinePerVisit';
			$title = 'Avg Online Per Visit';
			$sql = 'IFNULL(SUM(TotalOnline)/SUM(Visits),0) AS AvgOnlinePerVisit';
			break;
		default:
			exit;
	}
	$sql OR $sql = $order;
}
function get_json_error($func) {
	switch (json_last_error()) {
		case JSON_ERROR_NONE:
		break;
		case JSON_ERROR_DEPTH:
			echo $func, ' - Maximum stack depth exceeded';
		break;
		case JSON_ERROR_STATE_MISMATCH:
			echo $func, ' - Underflow or the modes mismatch';
		break;
		case JSON_ERROR_CTRL_CHAR:
			echo $func, ' - Unexpected control character found';
		break;
		case JSON_ERROR_SYNTAX:
			echo $func, ' - Syntax error, malformed JSON';
		break;
		case JSON_ERROR_UTF8:
			echo $func, ' - Malformed UTF-8 characters, possibly incorrectly encoded';
		break;
		default:
			echo $func, ' - Unknown error';
		break;
	}
}
function get_data($request, $key) {
		$val = '';
		$key .= '='; 
		$spos = strpos($request, $key);
		if ($spos !== false) {
			$spos += strlen($key);
			$epos = strpos($request, '&', $spos);
			if ($epos === false) $epos = strlen($request);
			if ($epos > $spos) $val = substr($request, $spos, ($epos - $spos));
		}
		return $val;
}
function check_table_exist($tb, &$tb_arr) {
		foreach ($tb_arr as $val) 
		{
			if ($tb == $val) return true;
		}
		return false;
}
function gen_sql($tbname, $con, $from, $to, $type, $where, $order, $sortorder, $start, $end, $key='') {
		$sql = array();
		if ($from === $to) {
			$tb = $tbname . $from;
			$sql['COUNT'] = "SELECT COUNT(MD5) FROM {$tb} {$where}";
			switch ($type) {
			case 0:
				$sql['SUM'] = "SELECT SUM(PV) AS PV,SUM(UV) AS UV,SUM(UPV) AS UPV,SUM(Visits) AS Visits,SUM(NV) AS NV,SUM(RV) AS RV,SUM(RVS) AS RVS,SUM(Bounces) AS Bounces,IFNULL(SUM(Bounces)/SUM(Visits),0)*10000 AS BounceRate,SUM(Exits) AS Exits,IFNULL(SUM(Exits)/SUM(PV),0)*10000 AS ExitRate,SUM(TotalDelay) AS TotalDelay,SUM(DelayTimes) AS DelayTimes,IFNULL(SUM(TotalDelay)/SUM(DelayTimes),0) AS AvgDelay,SUM(TotalReady) AS TotalReady,SUM(ReadyTimes) AS ReadyTimes,IFNULL(SUM(TotalReady)/SUM(ReadyTimes),0) AS AvgReady,SUM(TotalLoad) AS TotalLoad,SUM(LoadTimes) AS LoadTimes,IFNULL(SUM(TotalLoad)/SUM(LoadTimes),0) AS AvgLoad,SUM(TotalOnline) AS TotalOnline,SUM(OnlineTimes) AS OnlineTimes,IFNULL(SUM(TotalOnline)/SUM(OnlineTimes),0) AS AvgOnline,SUM(MaxReadX) AS MaxReadX,SUM(MaxReadY) AS MaxReadY,SUM(MRTimes) AS MRTimes,IFNULL(SUM(MaxReadX)/SUM(MRTimes),0) AS AvgMRX,IFNULL(SUM(MaxReadY)/SUM(MRTimes),0) AS AvgMRY,SUM(DREntry) AS DREntry,SUM(SEEntry) AS SEEntry,SUM(RFEntry) AS RFEntry,SUM(Clicks) AS Clicks,SUM(ValidClicks) AS ValidClicks FROM {$tb} {$where}";
				$sql['MERGE'] = "SELECT * FROM {$tb} {$where} ORDER BY {$order} {$sortorder} LIMIT {$start},{$end}";
				break;
			case 1:
				$sql['SUM'] = "SELECT SUM({$order}) FROM {$tb} {$where}";
				$sql['MERGE'] = "SELECT {$order},Detail FROM {$tb} {$where} ORDER BY {$order} {$sortorder} LIMIT 0,{$end}";
				break;
			case 2:
				$sql['COUNT'] = "SELECT COUNT(pKey) FROM {$tb} {$where}";
				$sql['MERGE'] = "SELECT VID,NodeIDMD5,NodeHtmlMD5,NodeTagMD5,NodeNodeMD5,NodeActionType,NodeRepeatClick,X,Y,MX,MY,RecordTime FROM {$tb} {$where} LIMIT {$start},{$end}";
				break;
			case 3:
				$sql['MERGE'] = "SELECT * FROM {$tb} {$where}";
				break;
			case 5:
				$sql['SUM'] = "SELECT COUNT(VID) FROM {$tb} {$where}";
				break;
			case 6:
				$sql['MERGE'] = "SELECT VID,RecordNo FROM {$tb} {$where} ORDER BY {$order} {$sortorder} LIMIT {$start},{$end}";
				break;
			case 7:
				$sql['SUM'] = "SELECT SUM({$order}) FROM {$tb} {$where}";
				$sql['MERGE'] = "SELECT Detail,{$order},MD5 FROM {$tb} {$where} ORDER BY {$sortorder} DESC";
				break;
			case 8:
				$sql['SUM'] = "SELECT SUM({$order}) FROM {$tb} {$where}";
				$sql['MERGE'] = "SELECT Detail,{$order},Extra,MD5 FROM {$tb} {$where} ORDER BY {$sortorder} DESC";
				break;
			}
		} else {
			$from = strtotime($from);
			$to = strtotime($to);
			$tb_arr = array();
			$sql2 = "SHOW TABLES LIKE '{$tbname}%'";
			$result = mysqli_query($con, $sql2);
			if ($result && mysqli_num_rows($result)) {
				while ($row = mysqli_fetch_row($result))
				{
					array_push($tb_arr, $row[0]);
				}
				mysqli_free_result($result);
			} else {
				return '';
			}
			$sql['COUNT'] = 'SELECT COUNT(MD5) FROM (';
			switch ($type) {
			case 0:
				$sql['SUM'] = 'SELECT SUM(PV) AS PV,SUM(UV) AS UV,SUM(UPV) AS UPV,SUM(Visits) AS Visits,SUM(NV) AS NV,SUM(RV) AS RV,SUM(RVS) AS RVS,SUM(Bounces) AS Bounces,IFNULL(SUM(Bounces)/SUM(Visits),0)*10000 AS BounceRate,SUM(Exits) AS Exits,IFNULL(SUM(Exits)/SUM(PV),0)*10000 AS ExitRate,SUM(TotalDelay) AS TotalDelay,SUM(DelayTimes) AS DelayTimes,IFNULL(SUM(TotalDelay)/SUM(DelayTimes),0) AS AvgDelay,SUM(TotalReady) AS TotalReady,SUM(ReadyTimes) AS ReadyTimes,IFNULL(SUM(TotalReady)/SUM(ReadyTimes),0) AS AvgReady,SUM(TotalLoad) AS TotalLoad,SUM(LoadTimes) AS LoadTimes,IFNULL(SUM(TotalLoad)/SUM(LoadTimes),0) AS AvgLoad,SUM(TotalOnline) AS TotalOnline,SUM(OnlineTimes) AS OnlineTimes,IFNULL(SUM(TotalOnline)/SUM(OnlineTimes),0) AS AvgOnline,SUM(MaxReadX) AS MaxReadX,SUM(MaxReadY) AS MaxReadY,SUM(MRTimes) AS MRTimes,IFNULL(SUM(MaxReadX)/SUM(MRTimes),0) AS AvgMRX,IFNULL(SUM(MaxReadY)/SUM(MRTimes),0) AS AvgMRY,SUM(DREntry) AS DREntry,SUM(SEEntry) AS SEEntry,SUM(RFEntry) AS RFEntry,SUM(Clicks) AS Clicks,SUM(ValidClicks) AS ValidClicks FROM (';
				$sql['MERGE'] = 'SELECT MD5,Type,SUM(PV) AS PV,SUM(UV) AS UV,SUM(UPV) AS UPV,SUM(Visits) AS Visits,SUM(NV) AS NV,SUM(RV) AS RV,SUM(RVS) AS RVS,SUM(Bounces) AS Bounces,IFNULL(SUM(Bounces)/SUM(Visits),0)*10000 AS BounceRate,SUM(Exits) AS Exits,IFNULL(SUM(Exits)/SUM(PV),0)*10000 AS ExitRate,SUM(TotalDelay) AS TotalDelay,SUM(DelayTimes) AS DelayTimes,IFNULL(SUM(TotalDelay)/SUM(DelayTimes),0) AS AvgDelay,SUM(TotalReady) AS TotalReady,SUM(ReadyTimes) AS ReadyTimes,IFNULL(SUM(TotalReady)/SUM(ReadyTimes),0) AS AvgReady,SUM(TotalLoad) AS TotalLoad,SUM(LoadTimes) AS LoadTimes,IFNULL(SUM(TotalLoad)/SUM(LoadTimes),0) AS AvgLoad,SUM(TotalOnline) AS TotalOnline,SUM(OnlineTimes) AS OnlineTimes,IFNULL(SUM(TotalOnline)/SUM(OnlineTimes),0) AS AvgOnline,SUM(MaxReadX) AS MaxReadX,SUM(MaxReadY) AS MaxReadY,SUM(MRTimes) AS MRTimes,IFNULL(SUM(MaxReadX)/SUM(MRTimes),0) AS AvgMRX,IFNULL(SUM(MaxReadY)/SUM(MRTimes),0) AS AvgMRY,SUM(DREntry) AS DREntry,SUM(SEEntry) AS SEEntry,SUM(RFEntry) AS RFEntry,Detail,SUM(Clicks) AS Clicks,SUM(ValidClicks) AS ValidClicks,Extra,ExtraMD5 FROM (';
				break;
			case 1:
				$sql['SUM'] = "SELECT SUM({$order}) AS {$order} FROM (";
				$sql['MERGE'] = "SELECT SUM({$order}) AS {$order},Detail,MD5 FROM (";
				break;
			case 2:
				$sql['COUNT'] = "SELECT COUNT(pKey) FROM (";
				$sql['MERGE'] = "SELECT VID,NodeIDMD5,NodeHtmlMD5,NodeTagMD5,NodeNodeMD5,NodeActionType,NodeRepeatClick,X,Y,MX,MY,RecordTime FROM (";
				break;
			case 3:
				$sql['MERGE'] = 'SELECT MD5,Type,AVG(PV) AS PV,AVG(UV) AS UV,AVG(NV) AS NV,AVG(UPV) AS UPV,AVG(RV) AS RV,AVG(RVS) AS RVS,AVG(Visits) AS Visits,AVG(Bounces) AS Bounces,IFNULL(SUM(Bounces)/SUM(Visits),0)*10000 AS BounceRate,AVG(Exits) AS Exits,IFNULL(SUM(Exits)/SUM(PV),0)*10000 AS ExitRate,AVG(RV) AS RV,IFNULL(SUM(TotalDelay)/SUM(DelayTimes),0) AS AvgDelay,IFNULL(SUM(TotalReady)/SUM(ReadyTimes),0) AS AvgReady,IFNULL(SUM(TotalLoad)/SUM(LoadTimes),0) AS AvgLoad,IFNULL(SUM(TotalOnline)/SUM(OnlineTimes),0) AS AvgOnline,IFNULL(SUM(MaxReadX)/SUM(MRTimes),0) AS AvgMRX,IFNULL(SUM(MaxReadY)/SUM(MRTimes),0) AS AvgMRY,AVG(DREntry) AS DREntry,AVG(SEEntry) AS SEEntry,AVG(RFEntry) AS RFEntry,Detail,AVG(Clicks) AS Clicks,AVG(ValidClicks) AS ValidClicks,Extra,ExtraMD5 FROM (';
				break;
			case 5:
				$sql['SUM'] = "SELECT COUNT(VID) FROM (";
				break;
			case 6:
				$sql['MERGE'] = "SELECT VID,RecordNo FROM (";
				break;
			case 7:
				$sql['SUM'] = "SELECT SUM({$order}) AS Visits FROM (";
				$sql['MERGE'] = "SELECT Detail,SUM({$order}) AS {$order},MD5 FROM (";
				break;
			case 8:
				$sql['SUM'] = "SELECT SUM({$order}) AS Visits FROM (";
				$sql['MERGE'] = "SELECT Detail,SUM({$order}) AS {$order},Extra,MD5 FROM (";
				break;
			}
			$bool = 1;
			for ($i = $from; $i <= $to; $i+=86400) {
				$tb = $tbname . date('Ymd', $i);
				if (check_table_exist($tb, $tb_arr)) {
					if ($bool) {
						switch ($type) {
						case 0:
							$sql['COUNT'] .= "(SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= "(SELECT * FROM {$tb} {$where})";
							$sql['MERGE'] .= "(SELECT * FROM {$tb} {$where})";
							break;
						case 1:
							$sql['COUNT'] .= "(SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= "(SELECT {$order} FROM {$tb} {$where})";
							$sql['MERGE'] .= "(SELECT {$order},Detail,MD5 FROM {$tb} {$where})";
							break;
						case 2:
							$sql['COUNT'] .= "(SELECT pKey FROM {$tb} {$where})";
							$sql['MERGE'] .= "(SELECT VID,NodeIDMD5,NodeHtmlMD5,NodeTagMD5,NodeNodeMD5,NodeActionType,NodeRepeatClick,X,Y,MX,MY,RecordTime FROM {$tb} {$where})";
							break;
						case 3:
							$sql['MERGE'] .= "(SELECT * FROM {$tb} {$where})";
							break;
						case 5:
							$sql['SUM'] .= "(SELECT VID FROM {$tb} {$where})";
							break;
						case 6:
							$sql['MERGE'] .= "(SELECT VID,RecordNo FROM {$tb} {$where})";
							break;
						case 7:
							$sql['COUNT'] .= "(SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= "(SELECT {$order} FROM {$tb} {$where})";
							$sql['MERGE'] .= "(SELECT Detail,{$order},MD5 FROM {$tb} {$where})";
							break;
						case 8:
							$sql['COUNT'] .= "(SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= "(SELECT {$order} FROM {$tb} {$where})";
							$sql['MERGE'] .= "(SELECT Detail,{$order},Extra,MD5 FROM {$tb} {$where})";
							break;
						}
						$bool = 0;
					} else {
						switch ($type) {
						case 0:
							$sql['COUNT'] .= " UNION (SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= " UNION ALL (SELECT * FROM {$tb} {$where})";
							$sql['MERGE'] .= " UNION ALL (SELECT * FROM {$tb} {$where})";
							break;
						case 1:
							$sql['COUNT'] .= " UNION (SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= " UNION ALL (SELECT {$order} FROM {$tb} {$where})";
							$sql['MERGE'] .= " UNION ALL (SELECT {$order},Detail,MD5 FROM {$tb} {$where})";
							break;
						case 2:
							$sql['COUNT'] .= " UNION ALL (SELECT pKey FROM {$tb} {$where})";
							$sql['MERGE'] .= " UNION ALL (SELECT VID,NodeIDMD5,NodeHtmlMD5,NodeTagMD5,NodeNodeMD5,NodeActionType,NodeRepeatClick,X,Y,MX,MY,RecordTime FROM {$tb} {$where})";
							break;
						case 3:
							$sql['MERGE'] .= " UNION ALL (SELECT * FROM {$tb} {$where})";
							break;
						case 5:
							$sql['SUM'] .= " UNION ALL (SELECT VID FROM {$tb} {$where})";
							break;
						case 6:
							$sql['MERGE'] .= " UNION ALL (SELECT VID,RecordNo FROM {$tb} {$where})";
							break;
						case 7:
							$sql['COUNT'] .= " UNION (SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= " UNION ALL (SELECT {$order} FROM {$tb} {$where})";
							$sql['MERGE'] .= " UNION ALL (SELECT Detail,{$order},MD5 FROM {$tb} {$where})";
							break;
						case 8:
							$sql['COUNT'] .= " UNION (SELECT MD5 FROM {$tb} {$where})";
							$sql['SUM'] .= " UNION ALL (SELECT {$order} FROM {$tb} {$where})";
							$sql['MERGE'] .= " UNION ALL (SELECT Detail,{$order},Extra,MD5 FROM {$tb} {$where})";
							break;
						}
					}
				}
			}
			switch ($type) {
			case 0:
			case 1:
			case 4:
				$sql['COUNT'] .= ')CACHETABLE';
				$sql['SUM'] .= ')CACHETABLE';
				$sql['MERGE'] .= ")CACHETABLE GROUP BY MD5 ORDER BY {$order} {$sortorder} LIMIT {$start},{$end}";
				break;
			case 3:
				$sql['MERGE'] .= ')CACHETABLE';
				break;
			case 2:
				$sql['COUNT'] .= ')CACHETABLE';
				$sql['MERGE'] .= ")CACHETABLE LIMIT {$start},{$end}";
				break;
			case 5:
				$sql['SUM'] .= ')CACHETABLE';
				break;
			case 6:
				$sql['MERGE'] .= ")CACHETABLE ORDER BY RecordNo DESC LIMIT {$start},{$end}";
				break;
			case 7:
			case 8:
				$sql['COUNT'] .= ')CACHETABLE';
				$sql['SUM'] .= ')CACHETABLE';
				$sql['MERGE'] .= ")CACHETABLE GROUP BY MD5 ORDER BY {$sortorder} DESC";
			}
		}
		return $sql;
}


?>