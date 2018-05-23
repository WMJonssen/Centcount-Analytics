<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Contorl Host Command PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

header('Access-Control-Allow-Origin:*');
header('Content-type: text/html; charset=utf-8');
@require './config/config_common.php';

	$uid = SDATA_OUT('uid', 6, 'EXIT');
	$t = SDATA_OUT('t', 6, 'EXIT');
	$v = SDATA_OUT('v', 4, 'EXIT', 32);
	$q = SDATA_OUT('q', 0, '', 32);
	verify_admin($uid, $t, $v);
	$REDIS_0 = new Redis();
	if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) exit('Conect REDIS_0 Failed.');
	$REDIS_0->SELECT(REDIS_DB_0);
	switch ($q) {
	case 'process info':
		$REDIS_2 = new Redis();
		if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit('Conect REDIS_2 Failed.');
		$REDIS_2->SELECT(REDIS_DB_2);
		$arr_data = array();
		$PROCESS_ARRAY = $REDIS_0->MGET(array('ProcessGlobalCount','ProcessGlobalConsume','ProcessMax','ProcessMin','ProcessLimit','KernelVersion','PersistenceStatus', 'PersistenceVersion'));
		$Requests = $REDIS_0->LLEN('TicketListL');
		$TotalProcessed = $PROCESS_ARRAY[0]; 
		$TotalConsume = $PROCESS_ARRAY[1]; 
		$FatalErrors = $REDIS_0->LLEN('ErrorFatal');
		$BadRequests = $REDIS_2->LLEN('BadRequests');
		$ExecuteFailures = $REDIS_0->LLEN('ErrorExecute');
		$PROCESS_MAX= $PROCESS_ARRAY[2]; 
		$PROCESS_MIN = $PROCESS_ARRAY[3]; 
		$PROCESS_LIMIT = $PROCESS_ARRAY[4]; 
		$KERNEL_VERSION = $PROCESS_ARRAY[5]; 
		$PERSISTENCE_STATUS = $PROCESS_ARRAY[6];
		$PERSISTENCE_VERSION = $PROCESS_ARRAY[7]; 
		$arr_data[0] = array((int)$Requests, (int)$TotalProcessed, (int)$TotalConsume, (int)$FatalErrors, (int)$BadRequests, (int)$ExecuteFailures, (int)$PROCESS_MAX, (int)$PROCESS_MIN, (int)$PROCESS_LIMIT, $KERNEL_VERSION, (int)$PERSISTENCE_STATUS, $PERSISTENCE_VERSION);
		$PROCESS_ARRAY = $REDIS_0->SMEMBERS('ProcessList');
		if (count($PROCESS_ARRAY) > 0) {
			foreach ($PROCESS_ARRAY as $PID) {
				$PROCESS_STRUCTURE = $REDIS_0->HGETALL($PID);
				if (count($PROCESS_STRUCTURE) === 11) {
					$arr_data[] = array($PROCESS_STRUCTURE['PID'], $PROCESS_STRUCTURE['Status'], $PROCESS_STRUCTURE['StartTime'], $PROCESS_STRUCTURE['LastResponseTime'], $PROCESS_STRUCTURE['TotalCount'], $PROCESS_STRUCTURE['TotalConsume'], $PROCESS_STRUCTURE['CurrentCount'], $PROCESS_STRUCTURE['PeakCount'], $PROCESS_STRUCTURE['MaxConsume'], $PROCESS_STRUCTURE['MinConsume'], $PROCESS_STRUCTURE['MemoryUsage']);
				}
			}
		}
		$PROCESS_ARRAY = $REDIS_0->ZREVRANGEBYSCORE('SSIDS', '+INF', '-INF', array('withscores'=>true, 'limit'=>array(0, 20))); 
		$arr_data[] = $PROCESS_ARRAY;
		$PROCESS_ARRAY = $REDIS_0->MGET(array('PerformanceCountJS', 'PerformanceConsumeJS', 'PerformanceCount0', 'PerformanceConsume0','PerformanceCount1', 'PerformanceConsume1','PerformanceCount2', 'PerformanceConsume2','PerformanceCount3', 'PerformanceConsume3','PerformanceCount4', 'PerformanceConsume4','PerformanceCount5', 'PerformanceConsume5','PerformanceCount6', 'PerformanceConsume6','PerformanceCount7', 'PerformanceConsume7','PerformanceCount8', 'PerformanceConsume8','PerformanceCount9', 'PerformanceConsume9','PerformanceCount10', 'PerformanceConsume10','PerformanceCount11', 'PerformanceConsume11','PerformanceCount12', 'PerformanceConsume12', 'TotalMemory','UsedMemory','FreeMemory', 'TotalDisk','UsedDisk')); 
		$arr_data[] = $PROCESS_ARRAY;
		echo json_encode($arr_data);
		break;
	case 'process sum':
		$REDIS_2 = new Redis();
		if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit('Conect REDIS_2 Failed.');
		$REDIS_2->SELECT(REDIS_DB_2);
		$arr_data = array();
		$PROCESS_ARRAY = $REDIS_0->MGET(array('ProcessGlobalCount','ProcessGlobalConsume','ProcessMax','ProcessMin','ProcessLimit','KernelVersion','PersistenceStatus', 'PersistenceVersion'));
		$Requests = $REDIS_0->LLEN('TicketListL');
		$TotalProcessed = $PROCESS_ARRAY[0]; 
		$TotalConsume = $PROCESS_ARRAY[1]; 
		$FatalErrors = $REDIS_0->LLEN('ErrorFatal');
		$BadRequests = $REDIS_2->LLEN('BadRequests');
		$ExecuteFailures = $REDIS_0->LLEN('ErrorExecute');
		$PROCESS_MAX= $PROCESS_ARRAY[2]; 
		$PROCESS_MIN = $PROCESS_ARRAY[3]; 
		$PROCESS_LIMIT = $PROCESS_ARRAY[4]; 
		$KERNEL_VERSION = $PROCESS_ARRAY[5]; 
		$PERSISTENCE_STATUS = $PROCESS_ARRAY[6];
		$PERSISTENCE_VERSION = $PROCESS_ARRAY[7]; 
		$arr_data[0] = array((int)$Requests, (int)$TotalProcessed, (int)$TotalConsume, (int)$FatalErrors, (int)$BadRequests, (int)$ExecuteFailures, (int)$PROCESS_MAX, (int)$PROCESS_MIN, (int)$PROCESS_LIMIT, $KERNEL_VERSION, (int)$PERSISTENCE_STATUS, $PERSISTENCE_VERSION);
		$PROCESS_ARRAY = $REDIS_0->SMEMBERS('ProcessList');
		if (count($PROCESS_ARRAY) > 0) {
			foreach ($PROCESS_ARRAY as $PID) {
				$PROCESS_STRUCTURE = $REDIS_0->HGETALL($PID);
				if (count($PROCESS_STRUCTURE) === 11) {
					$arr_data[] = array($PROCESS_STRUCTURE['PID'], $PROCESS_STRUCTURE['Status'], $PROCESS_STRUCTURE['StartTime'], $PROCESS_STRUCTURE['LastResponseTime'], $PROCESS_STRUCTURE['TotalCount'], $PROCESS_STRUCTURE['TotalConsume'], $PROCESS_STRUCTURE['CurrentCount'], $PROCESS_STRUCTURE['PeakCount'], $PROCESS_STRUCTURE['MaxConsume'], $PROCESS_STRUCTURE['MinConsume'], $PROCESS_STRUCTURE['MemoryUsage']);
				}
			}
		}
		echo json_encode($arr_data);
		break;
	case 'set process':
		$opt = SDATA_OUT('opt', 6, 'EXIT');
		$pid = SDATA_OUT('pid', 0, 'EXIT', 32);
		switch ($opt) {
		case 0:
			$REDIS_0->HSET($pid, 'Status', 0);
			echo $pid . ' is terminated successful!';
			break;
		case 1:
			$REDIS_0->HSET($pid, 'Status', 1);
			echo $pid . ' is running now!';
			break;
		case 2:
			$REDIS_0->HSET($pid, 'Status', 2);
			echo $pid . ' is paused successful!';
			break;
		case 4:
			$PROCESS_MIN = $REDIS_0->GET('ProcessMin');
			if ((int)$pid > 8) {
				echo 'Error: Max Processes is setted up 8 by administrator!';
				exit;
			}
			if ((int)$PROCESS_MIN <= (int)$pid) {
				$REDIS_0->SET('ProcessMax', (int)$pid);
				echo 'Max Processes is ' . $pid . ' now!';
			} else {
				echo 'Error: Max Processes must be larger than Min Processes!';
			}
			break;
		case 5:
			$PROCESS_MAX = $REDIS_0->GET('ProcessMax');
			if ((int)$PROCESS_MAX >= (int)$pid) {
				$REDIS_0->SET('ProcessMin', (int)$pid);
				echo 'Min Processes is ' . $pid . ' now!';
			} else {
				echo 'Error: Min Processes must be less than Max Processes!';
			}
			break;
		case 13:
			$PROCESS_COUNT = $REDIS_0->SCARD('ProcessList');
			$PROCESS_MAX = $REDIS_0->GET('ProcessMax');
			if ($PROCESS_COUNT < $PROCESS_MAX) {
				pclose(popen('php -f ' . __DIR__ . '/kernel.php &', 'r'));
				echo 'A new process is running now!';
			} else {
				echo 'Error: The count of process exceeds maximum limit.';
			}
			break;
		case 14:
			$REDIS_0->SET('ProcessLimit', 1);
			$PROCESS_COUNT = $REDIS_0->SCARD('ProcessList');
			if ((int)$PROCESS_COUNT > 0) {
				$PROCESS_ARRAY = $REDIS_0->LRANGE('ProcessList',0,($PROCESS_COUNT - 1));
				for ($i = 0; $i < $PROCESS_COUNT; $i++) {
					$PID = $PROCESS_ARRAY[$i];
					$REDIS_0->LSET($PID, 1, 0);
				}
			}
			sleep(3);
			$PROCESS_ARRAY = $REDIS_0->KEYS('PID*');
			$PROCESS_COUNT = count($PROCESS_ARRAY);
			if ($PROCESS_COUNT > 0) for ($i = 0; $i < $PROCESS_COUNT; $i++) {
				$REDIS_0->DEL($PROCESS_ARRAY[$i]);
			}
			$REDIS_0->DEL('ProcessList');
			echo 'This host is disabled now!';
			break;
		case 15:
			$REDIS_0->SET('ProcessLimit', 0);
			$PROCESS_MAX = $REDIS_0->GET('ProcessMax');
			for ($i = 0; $i < (int)$PROCESS_MAX; $i++) {
				pclose(popen('php -f ' . __DIR__ . '/kernel.php &', 'r'));
			}
			echo 'This host is enabled now!';
			break;
		case 20:
			$REDIS_0->SET('PersistenceStatus', 1);
			sleep(3);
			echo 'Persistence is disabled now!';
			break;
		case 21:
			$REDIS_0->SET('PersistenceStatus', 0);
			$REDIS_0->DEL('PersistenceMutualExclusion');
			pclose(popen('php -f ' . __DIR__ . '/persistence.php &', 'r'));
			echo 'Persistence is enabled now!';
			break;
		case 30:
			$REDIS_0->SET('ProcessLimit', 1);
			$PROCESS_ARRAY = $REDIS_0->SMEMBERS('ProcessList');
			if (count($PROCESS_ARRAY) > 0) {
				foreach ($PROCESS_ARRAY as $PID) {
					$REDIS_0->HSET($PID, 'Status', 0);
				}
			}
			sleep(3);
			$PROCESS_ARRAY = $REDIS_0->KEYS('PID*');
			if (count($PROCESS_ARRAY) > 0) {
				foreach ($PROCESS_ARRAY as $PID) $REDIS_0->DEL($PID);
			}
			$REDIS_0->DEL('ProcessList');
			$REDIS_0->SET('PersistenceStatus', 1);
			sleep(3);
			echo 'This host is disabled now!';
			break;
		case 31:
			$REDIS_0->SET('ProcessLimit', 0);
			$PROCESS_MAX = (int)$REDIS_0->GET('ProcessMax');
			for ($i = 0; $i < $PROCESS_MAX; $i++) {
				pclose(popen('php -f ' . __DIR__ . '/kernel.php &', 'r'));
			}
			$REDIS_0->SET('PersistenceStatus', 0);
			$REDIS_0->DEL('PersistenceMutualExclusion');
			pclose(popen('php -f ' . __DIR__ . '/persistence.php &', 'r'));
			echo 'This host is enabled now!';
			break;
		case 90:
			$REDIS_0->DEL('PerformanceCountJS', 'PerformanceConsumeJS','PerformanceCount0', 'PerformanceConsume0','PerformanceCount1', 'PerformanceConsume1','PerformanceCount2', 'PerformanceConsume2','PerformanceCount3', 'PerformanceConsume3','PerformanceCount4', 'PerformanceConsume4','PerformanceCount5', 'PerformanceConsume5','PerformanceCount6', 'PerformanceConsume6','PerformanceCount7', 'PerformanceConsume7','PerformanceCount8', 'PerformanceConsume8','PerformanceCount9', 'PerformanceConsume9','PerformanceCount10', 'PerformanceConsume10','PerformanceCount11', 'PerformanceConsume11','PerformanceCount12', 'PerformanceConsume12','ProcessGlobalCount','ProcessGlobalConsume', 'MissedCA', 'MissedVA', 'MissedVC', 'MissedVID', 'MissedIND');
			$REDIS_0->DEL('SSIDS');
			echo 'Clean all log successfully!';
			break;
		case 91:
			$REDIS = new Redis();
			$REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true AND exit;
			$REDIS->SELECT(REDIS_DB_2);
			$RETURN_ARRAY = $REDIS->KEYS('1*');
			$REDIS->DEL($RETURN_ARRAY);
			echo 'Clean settings cache successfully!';
			break;
		case 99:
			$REDIS_0->SET('ProcessLimit', 1);
			$PROCESS_ARRAY = $REDIS_0->SMEMBERS('ProcessList');
			if (count($PROCESS_ARRAY) > 0) {
				foreach ($PROCESS_ARRAY as $PID) {
					$REDIS_0->HSET($PID, 'Status', 0);
				}
			}
			$REDIS_0->SET('PersistenceStatus', 1);
			sleep(3);
			$PROCESS_ARRAY = $REDIS_0->KEYS('PID*');
			if (count($PROCESS_ARRAY) > 0) {
				foreach ($PROCESS_ARRAY as $PID) $REDIS_0->DEL($PID);
			}
			$REDIS_0->DEL('ProcessList');
			$REDIS_ARR = $REDIS_0->SMEMBERS('DayPeriod');
			if (count($REDIS_ARR) > 0) $REDIS_0->DEL($REDIS_ARR);
			$REDIS_0->DEL('DayPeriod', 'TimeLine');
			$REDIS_1 = new Redis();
			if ($REDIS_1->CONNECT(REDIS_IP_1, REDIS_PORT_1) !== true) exit('Clean today data failed from redis 1.');
			$REDIS_1->SELECT(REDIS_DB_1);
			$REDIS_1->FLUSHDB();
			$REDIS_3 = new Redis();
			if ($REDIS_3->CONNECT(REDIS_IP_3, REDIS_PORT_3) !== true) exit('Clean today data failed from redis 3.');
			$REDIS_3->SELECT(REDIS_DB_3);
			$REDIS_3->FLUSHDB();
			$DB = con_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
			if ($DB === false) exit('Clean today data failed from connecting database');
			foreach($REDIS_ARR AS $redis_tb) {
				$TMP	 = explode('-', $redis_tb);
				$SID	 = $TMP[0];
				$TB_DATE = $TMP[1];
				if (use_db($DB, 'site' . $SID)) {
					$sql = "DROP TABLE log{$TB_DATE}";
					mysqli_query($DB, $sql);
					$sql = "DROP TABLE act{$TB_DATE}";
					mysqli_query($DB, $sql);
					$sql = "DROP TABLE clk{$TB_DATE}";
					mysqli_query($DB, $sql);
					$sql = "DROP TABLE vid{$TB_DATE}";
					mysqli_query($DB, $sql);
					$sql = "DROP TABLE ind{$TB_DATE}";
					mysqli_query($DB, $sql);
				}
			}
			echo 'Clean today data successfully!';
			break;
		}
		break;
	case 'error':
		$REDIS = $REDIS_0;
		$opt = SDATA_OUT('opt', 6, 'EXIT');
		switch ($opt) {
		case 1:
			$error_type = 'ErrorExecute';
			break;
		case 2:
			$error_type = 'ErrorFatal';
			break;
		case 3:
			$REDIS = new Redis();
			if ($REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit('Conect REDIS_2 Failed.');
			$REDIS->SELECT(REDIS_DB_2);
			$error_type = 'BadRequests';
			break;
		case 101:
			$error_type = 'ErrorExecute';
			break;
		case 102:
			$error_type = 'ErrorFatal';
			break;
		case 103:
			$REDIS = new Redis();
			if ($REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit('Conect REDIS_2 Failed.');
			$REDIS->SELECT(REDIS_DB_2);
			$error_type = 'BadRequests';
			break;
		}
		if ($opt > 100) {
			if ($REDIS->DEL($error_type) > 0) {
				echo 'Delete successfully!';
			} else {
				echo 'Delete failed!';
			}
			exit;
		}
		$start = SDATA_OUT('start', 6, 0);
		$row = 20;
		$arr_data = array();
		$PROCESS_COUNT = $REDIS->LLEN($error_type);
		if ($PROCESS_COUNT > 0) {
			if ($start === -1 || $start > $PROCESS_COUNT) $start = ($PROCESS_COUNT > $row ? $PROCESS_COUNT - $row : 0);
			$arr_data = $REDIS->LRANGE($error_type, $start, ($start + $row - 1));
		} else {
			$start = 0;
		}
		$arr_data[] = array($PROCESS_COUNT, $start);
		echo json_encode($arr_data);
		break;
	}
exit;
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
		case 4:
			$mval = filter_var($val, FILTER_SANITIZE_STRING);
			if (strlen($mval) === $maxL) {
				return $val;
			} else {
				exit;
			}
		case 6:
			$tmp = (int)$val;
			if ((string)$tmp !== (string)$val) {
				exit;
			} else {
				return $tmp;
			}
		}
		return NULL;
}
function verify_admin($uid, $t, $v) {
		$n = time();
		if ($t < $n) exit;
		$matchvisa = md5($uid . $t . ENCODE_FACTOR . '4');
		if ($v === $matchvisa) return true;
		exit;
}
function con_db($host, $user, $pw) {
		$con = mysqli_connect($host,$user,$pw);
		if (mysqli_connect_errno($con)) return false;
		return $con;
}
function use_db(&$con, $db) {
		$db_selected = mysqli_select_db($con, $db);
		if ($db_selected) return true;
		return false;
}


?>