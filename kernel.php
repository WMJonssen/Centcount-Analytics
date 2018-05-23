<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Kernel PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


ignore_user_abort(true); 
set_time_limit(0); 
define('KERNEL_VERSION', '1.00.180523001');

@require './config/config_common.php';
require 'kernel.sql.php';
require 'kernel.func.php';
require 'ipdb.class.php';
require 'vendor/autoload.php';

$REDIS_0 = new Redis();
if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) exit;
$REDIS_0->SELECT(REDIS_DB_0);
//debug 0 
$REDIS_0->SET('DEBUG', 0);

date_default_timezone_set(ADMIN_TIMEZONE);

$IPH[0] = new \IP2Location\Database('./ipdb/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::FILE_IO);
define('IP_ALL', \IP2Location\Database::ALL);
$IPH[1] = new \GeoIp2\Database\Reader('./ipdb/GeoLite2-City.mmdb');
//debug 1
$REDIS_0->SET('DEBUG', 1);

$REDIS_2 = new Redis();
if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit;
$REDIS_2->SELECT(REDIS_DB_2);
$REDIS_3 = new Redis();
if ($REDIS_3->CONNECT(REDIS_IP_3, REDIS_PORT_3) !== true) exit;
$REDIS_3->SELECT(REDIS_DB_3);
$REDIS_0->SET('KernelVersion', KERNEL_VERSION);
define('KEY_REQUEST_LIST', 'RequestList');
define('KEY_PROCESS_MAX', 'ProcessMax');
define('KEY_PROCESS_MIN', 'ProcessMin');
define('KEY_PROCESS_LIST', 'ProcessList');
define('KEY_PROCESS_GLOBAL_COUNT', 'ProcessGlobalCount');
define('KEY_PROCESS_GLOBAL_CONSUME', 'ProcessGlobalConsume');
define('KEY_ERROR_FATAL', 'ErrorFatal');
define('KEY_ERROR_EXECUTE', 'ErrorExecute');
$PROCESS_MAX = $REDIS_0->GET(KEY_PROCESS_MAX);
if ($PROCESS_MAX) {
	$PROCESS_MAX = (int)$PROCESS_MAX;
	if ($PROCESS_MAX < 1) $PROCESS_MAX = 4;
} else {
	$PROCESS_MAX = 4;
	$REDIS_0->SET(KEY_PROCESS_MAX, 4);
}
$PROCESS_MIN = $REDIS_0->GET(KEY_PROCESS_MIN);
if ($PROCESS_MIN) {
	$PROCESS_MIN = (int)$PROCESS_MIN;
	if ($PROCESS_MIN < 1) $PROCESS_MIN = 2;
} else {
	$PROCESS_MIN = 2;
	$REDIS_0->SET(KEY_PROCESS_MIN, 2);
}
$REQUEST = $REDIS_0->SCARD(KEY_PROCESS_LIST);
if ($REQUEST >= $PROCESS_MAX) exit;
$DB = con_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
if ($DB === false) exit;
//debug 2
$REDIS_0->SET('DEBUG', 2);
$PID = 'PID' . mt_rand(1E6,9E6);
$PROCESS_STATUS = 1; 
$PROCESS_START_TIME = (int)(microtime(true) * 1E6);
$PROCESS_LAST_RESPONSE_TIME = $PROCESS_START_TIME;
$PROCESS_TOTAL_COUNT = 0;
$PROCESS_TOTAL_CONSUME = 0;
$PROCESS_MAX_CONSUME = 0;
$PROCESS_MIN_CONSUME = 1E6;
$PROCESS_PEAK_COUNT = 0;
$PROCESS_CURRENT_COUNT = 0;
$PROCESS_PERIOD_COUNT = 0;
$PROCESS_LAST_COUNT = 0;
$PROCESS_SECOND_PERIOD_TIME = $PROCESS_START_TIME;
$PROCESS_MEMORY_USAGE = 0;
$REDIS_0->SADD(KEY_PROCESS_LIST, $PID);
$REDIS_0->HMSET($PID, array('PID' => $PID, 'Status' => $PROCESS_STATUS, 'StartTime' => $PROCESS_START_TIME, 'LastResponseTime' => $PROCESS_LAST_RESPONSE_TIME, 'TotalCount' => $PROCESS_TOTAL_COUNT, 'TotalConsume' => $PROCESS_TOTAL_CONSUME, 'CurrentCount' => $PROCESS_CURRENT_COUNT, 'PeakCount' => $PROCESS_PEAK_COUNT, 'MaxConsume' => $PROCESS_MAX_CONSUME, 'MinConsume' => $PROCESS_MIN_CONSUME, 'MemoryUsage' => $PROCESS_MEMORY_USAGE));
$REDIS_0->INCR(KEY_PROCESS_GLOBAL_COUNT);
$REDIS_0->INCR(KEY_PROCESS_GLOBAL_CONSUME);
$TICK = '';
$REQUEST = '';
$RET = 0;
$QUIT = 0;
$RETRY = 0;
$TIMER_MIN_CRON_JOB = $PROCESS_START_TIME;
$TIMER_DAY_CRON_JOB = (int)floor($PROCESS_START_TIME / 864E8) * 86400000000;
$GLOBALS['RN_G'] = $PROCESS_START_TIME;
$GLOBALS['ERROR_G'] = '';
$ATTACK_BAN_TIMES = 5;
$ATTACK_BAN_SECONDS = 15;
$IP = '';
//debug 3
$REDIS_0->SET('DEBUG', 3);
autoresponse(NOTIFICATION_MAIL, ADMIN_MAIL, 'NOTIFICATION OF CA KERNEL', 'CA KERNEL WAS RESTARTED');
if ($REDIS_0->SCARD('TicketRegS') > 0) {
	$RETURN_ARRAY = $REDIS_0->SET('CheckTicketMutualExclusion', '', array('NX'));
	if ($RETURN_ARRAY === true) {
		$RETURN_ARRAY = $REDIS_0->SMEMBERS('TicketRegS');
		if (count($RETURN_ARRAY) > 0) {
			$REDIS_0->DEL('TicketRegS');
			sleep(3);
			foreach ($RETURN_ARRAY as $val) $REDIS_0->LPUSH('TicketListL', $val);
		}
	}
	$REDIS_0->DEL('CheckTicketMutualExclusion');
}
//debug 4
$REDIS_0->SET('DEBUG', 4);
get_server_info($REDIS_0);
//debug 5
$REDIS_0->SET('DEBUG', 5);
while (true) {
		$PROCESS_LAST_RESPONSE_TIME = (int)(microtime(true) * 1E6);
		$PROCESS_STATUS = $REDIS_0->HGET($PID, 'Status');
		$REDIS_0->HSET($PID, 'LastResponseTime', $PROCESS_LAST_RESPONSE_TIME);
		switch ($PROCESS_STATUS) {
		case '0':
			if ($TICK) {
				$REDIS_0->LPUSH('TicketListL', $TICK);
				if ($RET < -7000 && $REQUEST !== '') $REDIS_0->LPUSH($TICK, $REQUEST);
			}
			$REDIS_0->DEL($PID);
			$REDIS_0->SREM(KEY_PROCESS_LIST, $PID);
			mysqli_close($DB);
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'NOTIFICATION OF CA KERNEL TERMINATED', 'CA KERNEL IS TERMINATED, PID: ' . $PID);
			exit;
		case '1':
			if ($RET < -7000) {
				$QUIT++;
				if ($RET === -7001 && $QUIT < 3) {
					mysqli_close($DB);
					sleep(3);
					$DB = con_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL); 
					if ($DB === false) {
						if ($QUIT === 2) $REDIS_0->LPUSH($TICK, $REQUEST);
						continue;
					}
				} else {
					$REDIS_0->LPUSH(KEY_ERROR_FATAL, 'ERROR NO: ' . abs($RET) . ', TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $PID . '<br><br>REQUEST: ' . $REQUEST . '<br><br>DB ERROR - ' . $GLOBALS['ERROR_G']); 
					$RET = 0;
				}
			} else if ($RET < 0) {
				$REDIS_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: ' . abs($RET) . ', TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $PID . '<br><br>REQUEST: ' . $REQUEST . '<br><br>DB ERROR - ' . $GLOBALS['ERROR_G']);
				if ($IP && ($REDIS_2->SISMEMBER('BlockedIPList', $IP) === false)) {
					if ($REDIS_2->INCR('A'.$IP) > $ATTACK_BAN_TIMES OR $RET > -100) {
						$REDIS_2->SADD('BlockedIPList', $IP);
						$REDIS_2->SADD('BlockedIPHistory', $IP);
						$REDIS_2->DEL('A'.$IP);
					} else {
						$REDIS_2->EXPIRE('A'.$IP, $ATTACK_BAN_SECONDS);
					}
				}
				$RET = 0;
			}
			if (($PROCESS_LAST_RESPONSE_TIME - $PROCESS_SECOND_PERIOD_TIME) > 1E6) {
				$PROCESS_PERIOD_COUNT = $PROCESS_TOTAL_COUNT - $PROCESS_LAST_COUNT;
				if ($PROCESS_CURRENT_COUNT !== $PROCESS_PERIOD_COUNT) {
					$PROCESS_CURRENT_COUNT = $PROCESS_PERIOD_COUNT;
					if ($PROCESS_PERIOD_COUNT > $PROCESS_PEAK_COUNT) $PROCESS_PEAK_COUNT = $PROCESS_PERIOD_COUNT;
					$REDIS_0->HMSET($PID, array('CurrentCount' => $PROCESS_CURRENT_COUNT, 'PeakCount' => $PROCESS_PEAK_COUNT));
				}
				$PROCESS_LAST_COUNT = $PROCESS_TOTAL_COUNT;
				$PROCESS_SECOND_PERIOD_TIME = $PROCESS_LAST_RESPONSE_TIME;
			}
			if ($RET > -1000) {
				$QUIT = 0;
				$RET = 0;
				if ($TICK === '') {
					$RETURN_ARRAY = $REDIS_0->LPOP('TicketListL');
					if (empty($RETURN_ARRAY)) {
						sleep(1);
					} else {
						$TICK = $RETURN_ARRAY;
						if ($REDIS_0->SADD('TicketRegS', $TICK) === 0) {
							$TICK = '';
							continue;
						}
					}
				}
				if ($TICK !== '') {
					if ((bool)$REDIS_0->EXISTS($TICK)) {
						$RETURN_ARRAY = $REDIS_0->LPOP($TICK);
						if (empty($RETURN_ARRAY)) {
							continue;
						} else {
							if ($REQUEST === $RETURN_ARRAY) {
								continue;
							} else {
								$REQUEST = $RETURN_ARRAY;
							}
						}
					} else {
						$REDIS_0->SREM('TicketRegS', $TICK);
						$TICK = '';
						continue;
					}
				} else {
					$REQUEST = '';
				}
			}
			$RET = 0;
			$GLOBALS['ERROR_G'] = '';
			if ($REQUEST !== '') {
				$IP = SDATA($DB, $REQUEST, 'ip' ,1 , '', 15); 
				if ($IP) {
					if ($REDIS_2->SISMEMBER('BlockedIPList', $IP) === true) {
						$REDIS_2->INCR('BlockedIPCount');
						$RET = 0;
						continue;
					}
				} else {
					continue;
				}
				$RET = execute($DB, $PID, $REQUEST, $PROCESS_LAST_RESPONSE_TIME, $IPH, $REDIS_0, $REDIS_2, $REDIS_3);
				if ($RET > 0) {
					$REDIS_0->MULTI()
						->INCR(KEY_PROCESS_GLOBAL_COUNT)
						->INCRBY(KEY_PROCESS_GLOBAL_CONSUME, $RET)
						->EXEC();
					$PROCESS_TOTAL_COUNT++;
					$PROCESS_TOTAL_CONSUME += $RET;
					if ($RET > $PROCESS_MAX_CONSUME) $PROCESS_MAX_CONSUME = $RET;
					if ($RET < $PROCESS_MIN_CONSUME) $PROCESS_MIN_CONSUME = $RET;
					$REDIS_0->HMSET($PID, array('TotalCount' => $PROCESS_TOTAL_COUNT, 'TotalConsume' => $PROCESS_TOTAL_CONSUME, 'MaxConsume' => $PROCESS_MAX_CONSUME, 'MinConsume' => $PROCESS_MIN_CONSUME));
				}
			}
			break;
		default:
		case '2':
			sleep(1);
			break;
		}
		if (($PROCESS_LAST_RESPONSE_TIME - $TIMER_MIN_CRON_JOB) > 6E7) {
			$TIMER_MIN_CRON_JOB = $PROCESS_LAST_RESPONSE_TIME; 
			if ($REDIS_0->PING() !== '+PONG') { 
				$REDIS_0 = new Redis();
				if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) {
					autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA KERNEL FATAL ERROR', 'ERROR NO: 8002, PID: ' . $PID);
					mysqli_close($DB);
					pclose(popen('php -f ' . __DIR__ . '/kernel.php &', 'r'));
					exit;
				}
			}
			$PROCESS_MEMORY_USAGE = memory_get_usage(true);
			if ($REDIS_0->SISMEMBER(KEY_PROCESS_LIST, $PID) === false) $REDIS_0->SADD(KEY_PROCESS_LIST, $PID);
			$REDIS_0->HMSET($PID, array('PID' => $PID, 'Status' => $PROCESS_STATUS, 'StartTime' => $PROCESS_START_TIME, 'LastResponseTime' => $PROCESS_LAST_RESPONSE_TIME, 'TotalCount' => $PROCESS_TOTAL_COUNT, 'TotalConsume' => $PROCESS_TOTAL_CONSUME, 'CurrentCount' => $PROCESS_CURRENT_COUNT, 'PeakCount' => $PROCESS_PEAK_COUNT, 'MaxConsume' => $PROCESS_MAX_CONSUME, 'MinConsume' => $PROCESS_MIN_CONSUME, 'MemoryUsage' => $PROCESS_MEMORY_USAGE));
			if (($PROCESS_LAST_RESPONSE_TIME - $TIMER_DAY_CRON_JOB) > 864E8) {
				$TIMER_DAY_CRON_JOB = $PROCESS_LAST_RESPONSE_TIME;
				$REDIS_2->DEL('BlockedIPList');
			}
			$CURRENT_TIME = $GLOBALS['RN_G'] - 1860000000;
			$SITES = $REDIS_0->SMEMBERS('DayPeriod');
			if (count($SITES) > 0) {
				foreach ($SITES as $REDIS_TB) {
					$REDIS_RETURN = $REDIS_3->MULTI()
											->ZRANGEBYSCORE($REDIS_TB.'-SessionList', 0, $CURRENT_TIME, array('withscores'=>true))
											->ZREMRANGEBYSCORE($REDIS_TB.'-SessionList', 0, $CURRENT_TIME)
											->EXEC();
					if (count($REDIS_RETURN[0]) > 0) {
						foreach ($REDIS_RETURN[0] as $SESSION_ID => $val) {
							processSession($PID, $SESSION_ID, $REDIS_0, $REDIS_2, $REDIS_3);
						}
					}
				}
			}
			$REDIS_0->SET('TimeLine', $GLOBALS['RN_G']);
			if ((bool)$REDIS_0->EXISTS('PersistenceMutualExclusion') === false) pclose(popen('php -f ' . __DIR__ . '/persistence.php &', 'r'));
			if ((bool)$REDIS_0->EXISTS(KEY_REQUEST_LIST) === false) $GLOBALS['RN_G'] = $PROCESS_LAST_RESPONSE_TIME;
			get_server_info($REDIS_0);
		}
		if ($QUIT > 2) {
			$REDIS_0->LPUSH(KEY_ERROR_FATAL, 'ERROR NO: 8001, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $PID . '<br><br>REQUEST: ' . $REQUEST . '<br><br>DB ERROR - ' . $GLOBALS['ERROR_G'].'<br><br>');
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA KERNEL FATAL ERROR', 'ERROR NO: 8001, PID: ' . $PID);
			if ($TICK) $REDIS_0->LPUSH('TicketListL', $TICK);
			$REDIS_0->DEL($PID);
			$REDIS_0->SREM(KEY_PROCESS_LIST, $PID);
			mysqli_close($DB);
			pclose(popen('php -f ' . __DIR__ . '/kernel.php &', 'r'));
			exit;
		}
}

function execute(&$db_con, $pid, &$request, $start_time, &$IPH, &$redis_0, &$redis_2, &$redis_3) {
		$CA = INIT_CA();
		if (($CA['Step'] = SDATA($db_con,$request,'stp',4,'EXIT',MAX_INT,1)) === false) return -11; 
		if (($CA['StatusCode'] = SDATA($db_con,$request,'stat',4,'EXIT',MAX_INT,1)) === false) return -12; 
		if (($SID = SDATA($db_con,$request,'sid',4,'EXIT',2E15,15E14)) === false) return -13; 
		if ($redis_2->GET($SID.'-SiteStatus') !== '0') return -99;
		if (($CA['VID'] = SDATA($db_con,$request,'vid',4,'EXIT',$start_time,1E15)) === false) return -14; 
		if (($CA['TotalVisits'] = SDATA($db_con,$request,'tvs',4,1,MAX_SMALLINT,1)) === false) return -15; 
		if (($TZ = SDATA($db_con,$request,'tz',1,'EXIT',48)) === false) return -16; 
		if (!date_default_timezone_set($TZ)) return -17;
		if (($CA['UpdateTime'] = SDATA($db_con,$request,'ts',5,'EXIT')) === false) return -18; 
		if (use_db($db_con, 'site' . $SID) === false) return -7001;
		if (check_blocked_id($redis_2, $SID, $CA['VID'], $CA['Step']) === 0) return 0;
		$CA['IP'] = SDATA($db_con,$request,'ip',1,'',15); 
		if (($RBT = SDATA($db_con, $request,'rbt',4,'EXIT',1,0)) === false) return -19;
		if ($RBT === 0 && $CA['IP'] !== '') $RBT = check_robot_ip($redis_2, $CA['IP'], $CA['Spider']);
		if ($RBT === 0 && $CA['VID'] < 1E15) {
			$RBT = 1;
			if (empty($CA['Spider'])) $CA['Spider'] = 'Bad Request';
			record_robot_ip($redis_2, $CA['IP'], $CA['Spider']); 
		}
		if ($RBT === 0) {
			$SessionName = 'S'.$CA['VID'].'-'.$CA['TotalVisits'];
			if (($CA['RecordNo'] = SDATA($db_con,$request,'rn',5,'EXIT')) === false) return -20; 
		} else {
			if ($CA['StatusCode'] !== 1) return 0;
			$SessionName = '';
			$CA['VID'] = (int)('2' . mt_rand(1E9, 9E9));
			$CA['RecordNo'] = $CA['UpdateTime'];
			if (empty($CA['Spider'])) $CA['Spider'] = 'Unknown Spider';
		}
		$TIME = (int)($CA['RecordNo'] / 1E6);
		$CA['RecordTime'] = $TIME;
		$TB_DATE =  date('Ymd', $TIME);
		$REAL_MIN = date('H:i', $TIME);
		$redis_tb  = $SID . '-' . $TB_DATE; 
		$redis_ca  = $SID . '-' . $TB_DATE . '-CA-'; 
		$redis_va  = $SID . '-' . $TB_DATE . '-VA-'; 
		$redis_vc  = $SID . '-' . $TB_DATE . '-VC-'; 
		$redis_ind = $SID . '-' . $TB_DATE . '-IND-'; 
		$redis_up  = $SID . '-' . $TB_DATE . '-IND-UPDATE'; 
		$redis_ses = $SID . '-' . $TB_DATE . '-SES-'; 
		$redis_rt  = $SID . '-' . $TB_DATE . '-RT-' . $REAL_MIN; 
		if ($redis_0->SISMEMBER('DayPeriod', $redis_tb) === false) {
			$tb_log = 'log' . $TB_DATE; 
			$tb_act = 'act' . $TB_DATE; 
			$tb_clk = 'clk' . $TB_DATE; 
			$tb_vid = 'vid' . $TB_DATE; 
			$tb_ind = 'ind' . $TB_DATE; 
			if (!create_table($db_con, $tb_log, TB_CA_CREATE_TEXT))  return (-6001);
			if (!create_table($db_con, $tb_act, TB_VA_CREATE_TEXT))  return (-6002);
			if (!create_table($db_con, $tb_clk, TB_VC_CREATE_TEXT))  return (-6003);
			if (!create_table($db_con, $tb_vid, TB_VID_CREATE_TEXT)) return (-6004);
			if (!create_table($db_con, $tb_ind, TB_IND_CREATE_TEXT)) return (-6005);
			$sql = "INSERT INTO {$tb_ind} (MD5,Type,Detail) VALUES('00',0,'All Visitor'),('01',0,'All Human Visitor'),('02',0,'All New Visitor'),('03',0,'All Returning Visitor'),('04',0,'All Robot') ON DUPLICATE KEY UPDATE PV=PV";
			if (!record_data($db_con, $sql, 1, 5, $GLOBALS['ERROR_G'])) return (-6006);
			$tmp = array('All Visitor', 'All Human Visitor', 'All New Visitor', 'All Returning Visitor', 'All Robot');
			$redis_array = init_ind_insert('00');
			for ($i=0; $i<5; $i++) {
				$redis_key = $redis_ind . '0' . $i;
				if ((bool)$redis_3->EXISTS($redis_key) === false) {
					$redis_array['MD5'] = '0' . $i;
					$redis_array['Detail'] = $tmp[$i];
					$redis_3->HMSET($redis_key, $redis_array);
				}
			}
			$redis_0->SADD('DayPeriod', $redis_tb);
			$redis_3->HMSET($redis_tb, array('PeriodTime' => (strtotime($TB_DATE) + 86400) * 1000000, 'LastPersistenceTime' => 0));
		}
		$CA['DelaySecond']  = SDATA($db_con,$request,'ds',2,0,3E4,0); 
		$CA['ReadySecond']  = SDATA($db_con,$request,'rs',2,0,3E4,0); 
		$CA['LoadSecond']   = SDATA($db_con,$request,'ls',2,0,3E4,0); 
		$CA['OnlineSecond'] = $CA['UpdateTime'] > $CA['RecordNo'] ? (int)(($CA['UpdateTime'] - $CA['RecordNo']) / 1000) : 0;
		$CA['MinReadX']   = SDATA($db_con,$request,'mnrx',2,0,100,0); 
		$CA['MinReadY']   = SDATA($db_con,$request,'mnry',2,0,100,0); 
		$CA['MaxReadX']   = SDATA($db_con,$request,'mxrx',2,0,100,0); 
		$CA['MaxReadY']   = SDATA($db_con,$request,'mxry',2,0,100,0); 
		$CA['PageAction'] = SDATA($db_con,$request,'pa',2,0,4,0); 
		$GLOBALS['RN_G'] = $CA['RecordNo'];
		$SessionList = $redis_tb.'-SessionList';
		$ExitType = 0;
		$CA_REDIS = $redis_ca . $CA['RecordNo'];
		$ErrNO = $CA['StatusCode'] * -100;
		switch ($CA['StatusCode']) {
		case 1:
		case 2:
		case 3:
		case 9:
			$EXIST_CA = $redis_3->HSETNX($CA_REDIS, 'RecordNo', $CA['RecordNo']);
			$LENGTH_CA = $EXIST_CA === false ? $redis_3->HLEN($CA_REDIS) : 0;
			if ($LENGTH_CA === CA_TOTAL_ARRAY_LENGTH && $CA['StatusCode'] < $CA['Step']) return 0;
			$EXIST_SES = $RBT === 0 ? $redis_3->ZADD($SessionList, $CA['UpdateTime'], $SessionName) : 1;
			$CA['Charset'] = SDATA($db_con,$request,'cs',1,'UTF-8',32); 
			if (($LENGTH_CA !== CA_READY_ARRAY_LENGTH && $LENGTH_CA !== CA_TOTAL_ARRAY_LENGTH) OR ($LENGTH_CA === CA_TOTAL_ARRAY_LENGTH && $CA['StatusCode'] !== 1)) {
				$CA['ScrollWidth'] = SDATA($db_con,$request,'dsw',2,0,MAX_INT,0); 
				$CA['ScrollHeight'] = SDATA($db_con,$request,'dsh',2,0,MAX_INT,0); 
				$CA['ScrollLeft'] = SDATA($db_con,$request,'dsl',2,0,MAX_INT,0); 
				$CA['ScrollTop'] = SDATA($db_con,$request,'dst',2,0,MAX_INT,0); 
				$CA['Title'] = SDATA($db_con,$request,'dt',1,'',512,0,1,$CA['Charset']); 
				$CA['TitleMD5'] = $CA['Title'] ? smd5($CA['Title']) : ''; 
				$CA['ClientWidth'] = SDATA($db_con,$request,'bcw',2,0,MAX_INT,0); 
				$CA['ClientHeight'] = SDATA($db_con,$request,'bch',2,0,MAX_INT,0); 
				$CA['ClientLeft'] = SDATA($db_con,$request,'bcl',2,0,MAX_INT,0); 
				$CA['ClientTop'] = SDATA($db_con,$request,'bct',2,0,MAX_INT,0); 
				$redis_array = get_redis_array('ca_ready', $CA);
				$redis_3->HMSET($CA_REDIS, $redis_array);
			}
			if ($LENGTH_CA === CA_TOTAL_ARRAY_LENGTH || $LENGTH_CA === CA_BASIC_ARRAY_LENGTH) break;
			$CA['ClientTime'] = SDATA($db_con,$request,'ct',2,(int)date('G',$TIME),23,0);
			$CA['LastRN'] = SDATA($db_con,$request,'lr',2,0,$start_time,1E15); 
			$CA['LastVisitTime'] = SDATA($db_con,$request,'lvt',2,($CA['LastRN'] > 0 ? $CA['LastRN'] : $CA['RecordNo']),$start_time,1E14); 
			$CA['TotalPageViews'] = SDATA($db_con,$request,'tpv',2,1,MAX_INT,1); 
			$CA['PageViews'] = SDATA($db_con,$request,'pv',2,1,MAX_INT,1); 
			if ($CA['PageViews'] > $CA['TotalPageViews']) $CA['PageViews'] = $CA['TotalPageViews'];
			$CA['Visits'] = SDATA($db_con,$request,'vs',2,1,MAX_INT,1); 
			if ($CA['Visits'] > $CA['TotalVisits']) $CA['Visits'] = $CA['TotalVisits'];
			$CA['IsNVS'] = SDATA($db_con,$request,'nv',2,0,1,0); 
			if ($CA['IsNVS'] === 0) {
				$CA['IsNVS'] = $EXIST_SES;
			} else {
				$CA['LastRN'] = 0;
			}
			$CA['Page'] = SDATA($db_con,$request,'pg',1,'',1024,0,1,$CA['Charset']); 
			$CA['PageMD5'] = $CA['Page'] ? smd5($CA['Page']) : ''; 
			$CA['Referrer'] = SDATA($db_con,$request,'rf',1,'',3072,0,1,$CA['Charset']); 
			$CA['RFMD5'] = $CA['Referrer'] ? smd5($CA['Referrer']) : ''; 
			$CA['SE'] = SDATA($db_con,$request,'se',1,'',64,0,1,$CA['Charset']); 
			$CA['SEName'] = SDATA($db_con,$request,'sen',1,'',32,0,1,$CA['Charset']); 
			$CA['Keyword'] = SDATA($db_con,$request,'kw',1,'',128,0,1,$CA['Charset']); 
			$CA['KWMD5'] = $CA['Keyword'] ? smd5($CA['Keyword']) : ''; 
			$CA['PageDomain'] = SDATA($db_con,$request,'pd',1,'',128); 
			$CA['PDMD5'] = $CA['PageDomain'] ? smd5($CA['PageDomain']) : ''; 
			$CA['RefDomain'] = SDATA($db_con,$request,'rd',1,'',128); 
			$CA['RDMD5'] = $CA['RefDomain'] ? smd5($CA['RefDomain']) : ''; 
			$CA['UserAgent'] = SDATA($db_con,$request,'ua',1,'',512); 
			$CA['Platform'] = SDATA($db_con,$request,'pf',1,'',32); 
			$CA['AppName'] = SDATA($db_con,$request,'app',1,'',32); 
			$CA['OS'] = SDATA($db_con,$request,'os',1,'Unknown OS',32); 
			$CA['OSCodename'] = SDATA($db_con,$request,'osc',1,'',32); 
			$CA['OSVersion'] = SDATA($db_con,$request,'osv',1,'',32); 
			$CA['OSVersionN'] =  $CA['OSVersion'] ? abs((float)$CA['OSVersion']) : 0; 
			if ($CA['OSVersionN'] > MAX_BIGINT) $CA['OSVersionN'] = 0;
			$CA['CPU'] = SDATA($db_con,$request,'cpu',1,'',16); 
			$CA['Device'] = $RBT === 1 ? 'Robot' : SDATA($db_con,$request,'dc',1,'Unknown',32); 
			$CA['DeviceType'] = $RBT === 1 ? 4 : SDATA($db_con,$request,'dct',2,0,4,0); 
			$CA['Brand'] = SDATA($db_con,$request,'bd',1,'',32); 
			$CA['Model'] = SDATA($db_con,$request,'md',1,'',32); 
			$CA['SmartPhone'] = $CA['Brand'] && $CA['Model'] ? $CA['Brand'].' - '.$CA['Model'] : ($CA['Brand'] || $CA['Model']); 
			$CA['Spider'] = SDATA($db_con,$request,'sp',1,$CA['Spider'],32); 
			$CA['ScreenWidth'] = SDATA($db_con,$request,'sw',2,0,MAX_INT,0); 
			$CA['ScreenHeight'] = SDATA($db_con,$request,'sh',2,0,MAX_INT,0); 
			$CA['ColorDepth'] = SDATA($db_con,$request,'cd',2,0,MAX_TINYINT,0); 
			$CA['TouchScreen'] = SDATA($db_con,$request,'tc',2,0,1,0); 
			$CA['Plugin'] = SDATA($db_con,$request,'plugin',1,'',2048,0,1,$CA['Charset']); 
			$CA['Language'] = SDATA($db_con,$request,'lan',1,'',16); 
			$CA['LanCountry'] = $CA['Language'] ? substr($CA['Language'],0,2) : '';
			$CA['BrowserName'] = SDATA($db_con,$request,'bn',1,'Unknown Browser',32); 
			$CA['BrowserVersion'] = SDATA($db_con,$request,'bv',1,'',32); 
			$CA['BrowserVersionN'] =  $CA['BrowserVersion'] ? abs($CA['BrowserName'] === 'Edge' ? (int)$CA['BrowserVersion'] : (float)$CA['BrowserVersion']) : 0;
			if ($CA['BrowserVersionN'] > MAX_BIGINT) $CA['BrowserVersionN'] = 0;
			$CA['BrowserCore'] = SDATA($db_con,$request,'bc',1,'Unknown',16); 
			$CA['BrowserCoreVersion'] = SDATA($db_con,$request,'bcv',1,'',32); 
			$CA['BrowserCoreVersionN'] = $CA['BrowserCoreVersion'] ? abs((float)$CA['BrowserCoreVersion']) : 0;
			if ($CA['BrowserCoreVersionN'] > MAX_BIGINT) $CA['BrowserCoreVersionN'] = 0;
			$CA['CookieEnabled'] = SDATA($db_con,$request,'ce',2,0,1,0); 
			$EntryType = SDATA($db_con,$request,'nt',2,0,2,0); 
			$CA['IP'] = SDATA($db_con,$request,'ip',1,'',15); 
			if ($CA['IP'] !== '') {
				$CA['NIP'] = ip2long($CA['IP']); 
				$ipdb = SDATA($db_con,$request,'ipdb',2,0,9,0);
				$ips = array();
				switch ($ipdb) {
				case 0:
					$ips = $IPH[0]->lookup($CA['IP'], IP_ALL); 
					$CA['Country'] = $ips['countryName']; 
					$CA['Region'] = $ips['regionName']; 
					$CA['City'] = $ips['cityName']; 
					$CA['Latitude'] = (float)$ips['latitude'];
					$CA['Longitude'] = (float)$ips['longitude'];
					$CA['CountryISO'] = $ips['countryCode'];
					break;
				case 1:
				case 2:
					$ips = $IPH[1]->city($CA['IP']);				
					if (!empty($ips)) {
						if ($ipdb === 1) {
							$CA['Country'] = $ips->country->names['zh-CN']; 
							$CA['Region'] = isset($ips->mostSpecificSubdivision->names['zh-CN']) ? $ips->mostSpecificSubdivision->names['zh-CN'] : $ips->mostSpecificSubdivision->name; 
							$CA['City'] = isset($ips->city->names['zh-CN']) ? $ips->city->names['zh-CN'] : $ips->city->name; 
						} else {
							$CA['Country'] = $ips->country->name; 
							$CA['Region'] = $ips->mostSpecificSubdivision->name; 
							$CA['City'] = $ips->city->name; 
						}
						$CA['Latitude'] = (float)$ips->location->latitude; 
						$CA['Longitude'] = (float)$ips->location->longitude; 
						$CA['CountryISO'] = $ips->country->isoCode; 
					}
					break;
				}
				$CA['Country'] = $CA['Country'] ? mysqli_real_escape_string($db_con, $CA['Country']) : ''; 
				$CA['Region'] = $CA['Region'] ? mysqli_real_escape_string($db_con, $CA['Region']) : ''; 
				$CA['City'] = $CA['City'] ? mysqli_real_escape_string($db_con, $CA['City']) : ''; 
				$CA['CountryMD5'] = $CA['Country'] ? smd5($CA['Country']) : ''; 
				$CA['RegionMD5'] = $CA['Region'] ? smd5($CA['Region']) : ''; 
				$CA['CityMD5'] = $CA['City'] ? smd5($CA['City']) : ''; 
				$CA['ISPMD5'] = $CA['ISP'] ? smd5($CA['ISP']) : ''; 
			}
			$CA['FromType'] = SDATA($db_con,$request,'fmt',2,3,MAX_TINYINT,0);
			if ($CA['FromType'] < 3 && $EXIST_SES === 1) $CA['FromType'] = 3;
			switch ($CA['FromType']) {
			case 4:
				$CA['FromKey'] = $CA['SE'];
				$CA['FromKeyMD5'] = $CA['FromKey'] ? smd5($CA['FromKey']) : '';
				$CA['FromVal'] = $CA['Keyword'];
				$CA['FromValMD5'] = $CA['KWMD5'];
				break;
			case 5:
				$CA['FromKey'] = $CA['RefDomain'];
				$CA['FromKeyMD5'] = $CA['RDMD5'];
				$CA['FromVal'] = SDATA($db_con,$request,'rf',1,'',1024,0,1,$CA['Charset']); 
				$CA['FromValMD5'] = $CA['RFMD5'];
				break;
			}
			$CA['UTMSource']   = SDATA($db_con,$request,'utms',1,'',32, 0,1,$CA['Charset']); 
			$CA['UTMMedium']   = SDATA($db_con,$request,'utmm',1,'',32, 0,1,$CA['Charset']); 
			$CA['UTMTerm']	   = SDATA($db_con,$request,'utmt',1,'',128,0,1,$CA['Charset']); 
			$CA['UTMContent']  = SDATA($db_con,$request,'utmc',1,'',64, 0,1,$CA['Charset']); 
			$CA['UTMCampaign'] = SDATA($db_con,$request,'utmp',1,'',64, 0,1,$CA['Charset']); 
			if ($CA['StatusCode'] === 1 && $RBT === 0 && $CA['IP'] !== '' && strlen($CA['Plugin']) < 48) {
				if ($CA['SE'] === 'www.baidu.com' && $CA['Keyword'] === 'www') {
					$CA['Spider'] = 'BaiduSpider';
					goto ProcessRobot;
				} 
				if ($CA['Language'] === 'c' || $CA['ColorDepth'] === 0 || $CA['ScreenWidth'] === 0) {
					$CA['Spider'] = 'Unknown Spider';
					goto ProcessRobot;
				}
				$R_PF = substr($CA['Platform'], 0, 3);
				$R_OS = substr($CA['OS'], 0, 3);
				if (($R_OS === 'Win' || $R_PF === 'Win' || $R_OS === 'Mac' || $R_PF === 'Mac') && $R_OS !== $R_PF) {
					$CA['Spider'] = 'Unknown Spider';
					goto ProcessRobot;
				}
				if ($CA['OS'] === 'Android' && strpos($CA['Platform'], 'Linux') === false && strpos($CA['Platform'], 'Pike') === false) {
					$CA['Spider'] = 'Unknown Spider';
					goto ProcessRobot;
				}
				if ($CA['OS'] === 'iPhone OS' && 'iP' !== substr($CA['Platform'], 0, 2)) {
					$CA['Spider'] = 'Unknown Spider';
					goto ProcessRobot;
				}
				goto SkipRobot;
				ProcessRobot:
				record_robot_ip($redis_2, $CA['IP'], $CA['Spider']); 
				$RBT = 1;
				$CA['Device'] = 'Robot';
				$CA['VID'] = (int)('2' . mt_rand(1E9, 9E9));
				$CA['RecordNo'] = $CA['UpdateTime'];
				$redis_3->RENAME($CA_REDIS,  $redis_ca . $CA['RecordNo']);
				$CA_REDIS = $redis_ca . $CA['RecordNo'];
				SkipRobot:
			} else if ($CA['StatusCode'] === 1 && $RBT === 1 && $CA['Spider'] !== 'Unknown Spider' && $CA['IP'] !== '') {
				record_robot_ip($redis_2, $CA['IP'], $CA['Spider']); 
			}
			$CA['StatusCode'] === 3 && $RBT === 0 && $CA['MaxReadY'] === 0 && strlen($CA['Plugin']) < 48 && $CA['IP'] AND filter_robot_ip($redis_2, $CA['IP']);
			switch ($EntryType) {
			case 0:
				$CA['EntryCode'] = $CA['FromType']; 
				break;
			case 1:
				$CA['EntryCode'] = 1;
				break;
			case 2:
				$CA['EntryCode'] = 127;
				break;
			default:
				$CA['EntryCode'] = 3;
				break;
			}
			$CA['ExitCode'] = $CA['IsNVS'] === 1 ? 1 : 3;
			$IndFT = 3;
			$IndFK = '';
			$IndFKMD5 = '';
			$IndFV = $CA['FromVal'];
			$IndFVMD5 = $CA['FromValMD5'];
			$IndSEN = $CA['SEName'];
			$IsDR = 0;
			$IsRF = 0;
			$IsSE = 0;
			if ($RBT === 0) {
				$IndARR = $redis_3->HGETALL($SessionName);
				if (count($IndARR) > 13) {
					switch ($CA['FromType']) {
					case 3: 
						$IsDR = 1;
						break;
					case 4: 
						$IsSE = 1;
						break;
					case 5: 
						$IsRF = 1;
						break;
					}
					$IndFT = (int)$IndARR['FT'];
					$IndFK = $IndARR['FK'];
					$IndFKMD5 = $IndARR['FKMD5'];
					$IndFV = $IndARR['FV'];
					$IndFVMD5 = $IndARR['FVMD5'];
					$IndSEN = $IndARR['SEN'];
					$CA['FromType'] = $IndFT;
					$CA['FromKey'] = $IndFK;
					$CA['FromKeyMD5'] = substr($IndFKMD5, 1);
					$CA['FromVal'] = $IndFV;
					$CA['FromValMD5'] = $IndFVMD5;
					$CA['SEName'] = $IndSEN;
					$CA['LastVisitTime'] = $IndARR['LVT'];
					$CA['UTMSource'] = $IndARR['UTMS'];
					$CA['UTMMedium'] = $IndARR['UTMM'];
					$CA['UTMTerm'] = $IndARR['UTMT']; 
					$CA['UTMContent'] = $IndARR['UTMC'];
					$CA['UTMCampaign'] = $IndARR['UTMP'];
				} else {
					switch ($CA['FromType']) {
					case 2: 
						$IndFT = 2;
						$IndFK = 'Internal Entry';
						$IndFKMD5 = 'B'; 
						break;
					default:
					case 3: 
						$IndFT = 3;
						$IndFK = 'Direct Entry';
						$IndFKMD5 = 'C'; 
						$IsDR = 1;
						break;
					case 4: 
						$IndFT = 4;
						$IndFK = $CA['FromKey'];
						$IndFKMD5 = 'D' . $CA['FromKeyMD5']; 
						$IsSE = 1;
						break;
					case 5: 
						$IndFT = 5;
						$IndFK = $CA['FromKey'];
						$IndFKMD5 = 'E' . $CA['FromKeyMD5']; 
						$IsRF = 1;
						break;
					}		
				}
			} else {
				$IndFT = 3;
				$IndFK = 'Direct Entry';
				$IndFKMD5 = 'C'; 
			}
			if ($CA['IsNVS'] !== 1) {
				$IsDR = 0;
				$IsRF = 0;
				$IsSE = 0;
			}
			$CA['IsUPV'] = ($RBT === 0) ? $redis_3->SADD($SessionName.'B', $CA['PageMD5']) : 1;
			$IsUPV = $CA['IsUPV'];
			$CA['IsUTM'] = $CA['UTMSource'] !== '' ? 1 : 0; 
			$CA['IsRBT'] = $RBT;
			$CA['IsPV'] = 1;
			$CA['IsRVS'] = $CA['TotalVisits'] > 1  && $CA['IsNVS'] === 1 ? 1 : 0; 
			$CA['IsNV'] = $CA['TotalVisits'] === 1  && $CA['IsNVS'] === 1 ? 1 : 0; 
			$CA['IsUV'] = $CA['Visits'] === 1 && $CA['IsNVS'] === 1 ? 1 : 0; 
			$CA['IsRV'] = $CA['Visits'] === 1 && $CA['IsRVS'] === 1 ? 1 : 0; 
			$IsPV = $CA['IsPV']; 
			$IsRVS = $CA['IsRVS']; 
			$IsNVS = $CA['IsNVS']; 
			$IsUV = $CA['IsUV']; 
			$IsNV = $CA['IsNV']; 
			$IsRV = $CA['IsRV']; 
			if ($RBT === 0) {
				switch ($CA['IsNVS']) {
				case 1: 
					$IsBounce = 1;
					$IniBR = 10000;
					$IsExit = 1;
					$IniER = 10000;
					break;
				case 0: 
					$IsBounce = 0;
					$IniBR = 0;
					$IsExit = 0;
					$IniER = 0;
					break;
				}
			} else { 
				$IsBounce = 1;
				$IniBR = 10000;
				$IsExit = 1;
				$IniER = 10000;
			}
			$CA['IsBounce'] = $IsBounce;
			$CA['IsExit'] = $IsExit;
			if ($RBT === 0) {	
				$redis_3->SADD($SessionName.'A', $CA['RecordNo']);
				if ($CA['IsNVS'] === 1) {
					$redis_3->HMSET($SessionName, 
						array(
							'F' => $CA['RecordNo'], 
							'E' => $CA['RecordNo'],
							'S' => $SID, 
							'T' => $TZ,
							'FT' => $IndFT, 
							'FK' => $IndFK, 
							'FKMD5' => $IndFKMD5, 
							'FV' => $IndFV, 
							'FVMD5' => $IndFVMD5, 
							'SEN' => $CA['SEName'], 
							'LVT' => $CA['LastVisitTime'], 
							'UTMS' => $CA['UTMSource'], 
							'UTMM' => $CA['UTMMedium'], 
							'UTMT' => $CA['UTMTerm'], 
							'UTMC' => $CA['UTMContent'], 
							'UTMP' => $CA['UTMCampaign']
							)
						);
				} else {
					if ($CA['RecordNo'] > (int)($redis_3->HGET($SessionName, 'E'))) $redis_3->HSET($SessionName, 'E', $CA['RecordNo']);
				}
			}
			$redis_array = get_redis_array('ca_update', $CA);
			if ($RBT === 1) $redis_array['LUT'] = 6;
			$redis_3->HMSET($CA_REDIS, $redis_array);
			if ($RBT === 1) $redis_3->SADD($redis_ca, $CA_REDIS);
			$IndMD5Arr = array();
			if ($RBT === 0) {
				$IndMD5Arr[] = array($CA['VID'] . '-' . $CA['TotalVisits'], 99, 'Session');
				$IndMD5Arr[] = array('00', 0, 'All Visitor');
				$IndMD5Arr[] = array('01', 0, 'All Human Visitor');
				$IndMD5Arr[] = array(($CA['TotalVisits'] > 1 ? '03' : '02'), 0, ($CA['TotalVisits'] > 1 ? 'All Returning Visitor' : 'All New Visitor'));
					$IndMD5Arr[] = array('A' . $CA['PageMD5'], 1, $CA['Page']);
				if ($IndFKMD5 !== '') {
					$IndMD5Arr[] = array($IndFKMD5, $IndFT, $IndFK);
				}
				if ($IndFT === 4 && $IndFVMD5 !== '') {
					$IndMD5Arr[] = array('F' . $IndFVMD5, 6, $IndFV);
				}
				if ($CA['BrowserName'] !== '') {
					$IndMD5Arr[] = array('G' . $CA['BrowserName'], 7, $CA['BrowserName']);
				}
				if ($CA['OS'] !== '') {
					$IndMD5Arr[] = array('H' . $CA['OS'], 8, $CA['OS']);
				}
				if ($CA['BrowserCore'] !== '') {
					$IndMD5Arr[] = array('I' . $CA['BrowserCore'], 9, $CA['BrowserCore']);
				}
					$IndMD5Arr[] = array('J' . $CA['Device'], 10, $CA['Device']);
					$tmp = $CA['ScreenWidth'] . 'x' . $CA['ScreenHeight'];
					$IndMD5Arr[] = array('K' . $tmp, 11, $tmp);
				if ($CA['Country'] !== '') {
					$IndMD5Arr[] = array('L' . $CA['CountryMD5'], 12, $CA['Country']);
				}
				if ($CA['Region'] !== '') {
					$IndMD5Arr[] = array('M' . $CA['RegionMD5'], 13, $CA['Region']);
				}
				if ($CA['City'] !== '') {
					$IndMD5Arr[] = array('N' . $CA['CityMD5'], 14, $CA['City']);
				}
				if ($IndSEN !== '') { 
					$IndMD5Arr[] = array('O' . $IndSEN, 15, $IndSEN);
				}
				if ($IndFT === 5 && $IndFVMD5 !== '') {
					$IndMD5Arr[] = array('P' . $IndFVMD5, 16, $IndFV);
				}
				if ($CA['TotalVisits'] > 1) {
					$TOS = date('Z') * 1E6;
					$ACTIVATE_DAYS = floor(($CA['RecordNo'] + $TOS) / 864E8) - floor(($CA['LastVisitTime'] + $TOS) / 864E8);
					if ($ACTIVATE_DAYS === 0 && $CA['Visits'] === 1 && $CA['IsNVS'] === 1) {
						$IndMD5Arr[] = array('QNewVisitor', 17, 'New Visitor');
					} else {
						static $ACTIVATE_DAYS_ARRAY = array(
											'0 Day'=>0,
											'1 Day'=>1,
											'2 Days'=>2,
											'3 Days'=>3,
											'4 Days'=>4,
											'5 Days'=>5,
											'6 Days'=>6,
											'7 Days'=>7,
											'14 Days'=>14,
											'30 Days'=>30, 
											'60 Days'=>60, 
											'120 Days'=>120, 
											'365 Days'=>365, 
											'365+ Days'=>MAX_INT
											);
						foreach ($ACTIVATE_DAYS_ARRAY as $key=>$val) {
							if ($ACTIVATE_DAYS <= $val) {
								$IndMD5Arr[] = array('Q' . $key, 17, $key);
								break;
							}
						}	
					}
				} else {
					$IndMD5Arr[] = array('QNewVisitor', 17, 'New Visitor');
				}
					$IndMD5Arr[] = array('R' . $REAL_MIN, 18, $REAL_MIN);
				if ($CA['Language'] !== '') {
					$IndMD5Arr[] = array('S' . $CA['Language'], 19, $CA['Language']);
				}
				if ($CA['LanCountry'] !== '') {
					$IndMD5Arr[] = array('T' . $CA['LanCountry'], 20, $CA['LanCountry']);
				}
				$STZ = date('H', $TIME);
				$IndMD5Arr[] = array('U' . $STZ, 21, $STZ);
				$VTZ = $CA['ClientTime'] < 10 ? '0' . $CA['ClientTime'] : $CA['ClientTime'];
				$IndMD5Arr[] = array('V' . $VTZ, 22, $VTZ);
				$IndMD5Arr[] = array('a' . $CA['PDMD5'], 27, $CA['PageDomain']);
				if ($CA['BrowserName'] !== '') {
					$IndMD5Arr[] = array('b' . $CA['BrowserName'] . $CA['BrowserVersionN'], 28, $CA['BrowserName'] . ' ' . $CA['BrowserVersionN']);
				}
				if ($CA['OS'] !== '') {
					$IndMD5Arr[] = array('c' . $CA['OS'] . $CA['OSCodename'], 29, $CA['OS'] . ' ' . $CA['OSCodename']);
				}
				if ($CA['UTMSource'] !== '') {
					$IndMD5Arr[] = array('d' . $CA['UTMSource'] , 30, $CA['UTMSource']);
				}
				if ($CA['UTMMedium'] !== '') {
					$IndMD5Arr[] = array('e' . $CA['UTMMedium'] , 31, $CA['UTMMedium']);
				}
				if ($CA['UTMTerm'] !== '') {
					$IndMD5Arr[] = array('f' . $CA['UTMTerm'] , 32, $CA['UTMTerm']);
				}
				if ($CA['UTMContent'] !== '') {
					$IndMD5Arr[] = array('g' . $CA['UTMContent'] , 33, $CA['UTMContent']);
				}
				if ($CA['UTMCampaign'] !== '') {
					$IndMD5Arr[] = array('h' . $CA['UTMCampaign'] , 34, $CA['UTMCampaign']);
				}
				$len = count($IndMD5Arr);
				for ($i = 0; $i < $len; $i++) {
					$redis_key = $i === 0 ? $redis_ses . $IndMD5Arr[$i][0] : $redis_ind . $IndMD5Arr[$i][0];
					if ($redis_3->HLEN($redis_key) !== 31) {
						switch ($IndMD5Arr[$i][1]) {
						default:
							if ($IndMD5Arr[$i][1] === 14) {
								$Extra = $CA['Longitude'] . ',' . $CA['Latitude'];
								$ExtraMD5 = tmd5($Extra);
							} else {
								$Extra = '';
								$ExtraMD5 = '';
							}
							$IndData = array(
								'MD5' => $IndMD5Arr[$i][0], 
								'Type' => $IndMD5Arr[$i][1], 
								'PV' => $IsPV, 
								'UV' => $IsUV, 
								'UPV' => $IsUPV, 
								'RVS' => $IsRVS,
								'NV' => $IsNV, 
								'RV' => $IsRV, 
								'Visits' => $IsNVS,
								'Bounces' => $IsBounce,
								'Exits' => $IsExit,
								'DREntry' => $IsDR, 
								'SEEntry' => $IsSE, 
								'RFEntry' => $IsRF, 
								'Detail' => $IndMD5Arr[$i][2], 
								'Extra' => $Extra, 
								'ExtraMD5' => $ExtraMD5,
								'TotalReady' => 0,
								'ReadyTimes' => 0,
								'TotalLoad' => 0,
								'LoadTimes' => 0,
								'TotalOnline' => 0,
								'OnlineTimes' => 0,
								'TotalDelay' => 0,
								'DelayTimes' => 0,
								'MaxReadX' => 0,
								'MaxReadY' => 0,
								'MRTimes' => 0,
								'Clicks' => 0,
								'ValidClicks' => 0,								
								'LUT' => 1
								);
							break;
						case 18:
							$IndData = $redis_3->HGETALL($redis_ind . '01');
							$IndData['MD5'] = $IndMD5Arr[$i][0];
							$IndData['Type'] = $IndMD5Arr[$i][1];
							$IndData['Detail'] = $IndMD5Arr[$i][2];
							$IndData['LUT'] = 1;
							break;
						}
						$redis_3->HMSET($redis_key, $IndData);
						if ($i > 0) $redis_3->SADD($redis_up, $redis_key);
					} else {
						$redis_3->PIPELINE();
						$redis_3->HINCRBY($redis_key, 'PV', $IsPV);
						$redis_3->HINCRBY($redis_key, 'UV', $IsUV);
						$redis_3->HINCRBY($redis_key, 'UPV', $IsUPV);
						$redis_3->HINCRBY($redis_key, 'RVS', $IsRVS);
						$redis_3->HINCRBY($redis_key, 'NV', $IsNV);
						$redis_3->HINCRBY($redis_key, 'RV', $IsRV);
						$redis_3->HINCRBY($redis_key, 'Visits', $IsNVS);
						$redis_3->HINCRBY($redis_key, 'Bounces', $IsBounce); 
						$redis_3->HINCRBY($redis_key, 'Exits', $IsExit);
						$redis_3->HINCRBY($redis_key, 'DREntry', $IsDR);
						$redis_3->HINCRBY($redis_key, 'SEEntry', $IsSE);
						$redis_3->HINCRBY($redis_key, 'RFEntry', $IsRF);
						$redis_3->HSET($redis_key, 'LUT', 3);
						if ($i > 0) $redis_3->SADD($redis_up, $redis_key);
						$redis_3->EXEC();
					}
				}
			} else {
				$IndMD5Arr[] = array('00', 0, 'All Visitor');
				$IndMD5Arr[] = array('04', 0, 'All Robot');
				if ($CA['Spider'] !== '') {
					$IndMD5Arr[] = array('Y' . $CA['PageMD5'], 25, $CA['Page']);
				}
				if ($CA['Spider'] !== '') {
					$IndMD5Arr[] = array('Z' . $CA['Spider'], 26, $CA['Spider']);
				}
				if ($CA['IP'] !== '') {
					$IndMD5Arr[] = array('i' . $CA['IP'], 35, $CA['IP']);
					$IPBlock = substr($CA['IP'], 0, strrpos($CA['IP'], '.')) . '.*';
					$IndMD5Arr[] = array('j' . $IPBlock, 36, $IPBlock);
				}
				$len = count($IndMD5Arr);
				for ($i = 0; $i < $len; $i++) {
					$redis_key = $redis_ind . $IndMD5Arr[$i][0];
					$IndData = $redis_3->HGETALL($redis_key);
					if (count($IndData) !== 31) {
						$IndData = array(
								'MD5' => $IndMD5Arr[$i][0],
								'Type' => $IndMD5Arr[$i][1],
								'PV' => 1,
								'UV' => 1,
								'UPV' => 1,
								'RVS' => 0,
								'NV' => 1,
								'RV' => 0,
								'Visits' => 1,
								'Bounces' => 1,
								'Exits' => 1, 
								'DREntry' => 0,
								'SEEntry' => 0,
								'RFEntry' => 0,
								'Detail' => $IndMD5Arr[$i][2],
								'Extra' => '',
								'ExtraMD5' => '',
								'TotalReady' => 0,
								'ReadyTimes' => 0,
								'TotalLoad' => 0,
								'LoadTimes' => 0,
								'TotalOnline' => 0,
								'OnlineTimes' => 0,
								'TotalDelay' => 0,
								'DelayTimes' => 0,
								'MaxReadX' => 0,
								'MaxReadY' => 0,
								'MRTimes' => 0,
								'Clicks' => 0,
								'ValidClicks' => 0,	
								'LUT' => 1
								);
						$redis_3->HMSET($redis_key, $IndData);
						$redis_3->SADD($redis_up, $redis_key);
					} else {
						$redis_3->PIPELINE();
						$redis_3->HINCRBY($redis_key, 'PV', 1);
						$redis_3->HINCRBY($redis_key, 'UV', 1);
						$redis_3->HINCRBY($redis_key, 'UPV', 1);
						$redis_3->HINCRBY($redis_key, 'NV', 1);
						$redis_3->HINCRBY($redis_key, 'Visits', 1);
						$redis_3->HINCRBY($redis_key, 'Bounces', 1); 
						$redis_3->HINCRBY($redis_key, 'Exits', 1);
						$redis_3->HSET($redis_key, 'LUT', 3);
						$redis_3->SADD($redis_up, $redis_key);
						$redis_3->EXEC();
					}
				}
			}
			if ($IsNVS === 0 && $RBT === 0) {
				$FIRST_PAGE_RN = (int)($redis_3->HGET($SessionName, 'F'));
				if ($FIRST_PAGE_RN > 0) {
					if ($redis_3->HEXISTS($redis_ca . $FIRST_PAGE_RN, 'LUT') === false) {
						if (!viewStatus($FIRST_PAGE_RN, $redis_2, $redis_3, $redis_ca, $redis_ind, $redis_ses, 0)) {
							$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4008, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($FIRST_PAGE_RN, 0, 10)) . '<br>Session Name: ' . $SessionName . $GLOBALS['ERROR_G'] . '<br><br>');
						}
					}
					if (exitStatus($FIRST_PAGE_RN, $redis_3, $redis_ca, $redis_ind, $redis_ses, 0)) {
						$redis_3->HSET($SessionName, 'F', 0);
					} else {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4005, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($FIRST_PAGE_RN, 0, 10)) . '<br>Session Name: ' . $SessionName . $GLOBALS['ERROR_G'] . '<br><br>');
					}
				}
			}
			if ($CA['LastRN'] > 0 && $RBT === 0) {
				$redis_key = $redis_ca . $CA['LastRN'];
				$LUT = (int)$redis_3->HGET($redis_key, 'LUT');
				if ($LUT === 1 || $LUT === 3) {
					if (!viewStatus($CA['LastRN'], $redis_2, $redis_3, $redis_ca, $redis_ind, $redis_ses, 1)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4006, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($CA['LastRN'], 0, 10)) . '<br>Session Name: ' . $SessionName . $GLOBALS['ERROR_G'] . '<br><br>');
					}
				} else if ($LUT === 2 || $LUT === 4) {
					$redis_3->HSET($redis_key, 'ExitCode', 4);
					$redis_3->SADD($redis_ca, $redis_key);
				}
			}
			break;
		case 6:
			break;
		case 5:
			$ExitType = SDATA($db_con,$request,'et',2,0,127,0);
		case 8:
			if ($redis_3->HLEN($CA_REDIS) < CA_TOTAL_ARRAY_LENGTH) return 0;
			if (($CA['UCR'] = SDATA($db_con,$request,'ucr',5,'EXIT')) === false) {
				if ($CA['StatusCode'] === 5) goto SkipTo5;
				return ($ErrNO - 1); 
			}
			$CA['DeviceType'] = SDATA($db_con,$request,'dct',2,0,4,0); 
			$IndARR = $redis_3->HMGET($SessionName, array('FT', 'FK', 'FKMD5', 'FV', 'FVMD5', 'FK', 'SEN', 'LVT', 'UTMS', 'UTMM', 'UTMT', 'UTMC', 'UTMP'));
			if (count($IndARR) > 0) {
				$CA['FromType'] = (int)$IndARR['FT'];
				$CA['FromKey'] = $IndARR['FK'];
				$CA['FromKeyMD5'] = substr($IndARR['FKMD5'], 1);
				$CA['FromVal'] = $IndARR['FV'];
				$CA['FromValMD5'] = $IndARR['FVMD5'];
				$CA['UTMSource'] = $IndARR['UTMS'];
				$CA['UTMMedium'] = $IndARR['UTMM'];
				$CA['UTMTerm'] = $IndARR['UTMT']; 
				$CA['UTMContent'] = $IndARR['UTMC'];
				$CA['UTMCampaign'] = $IndARR['UTMP'];
			}
			if (($CA['TotalPageViews'] = SDATA($db_con,$request,'tpv',5,'EXIT')) === false) {
				if ($CA['StatusCode'] === 5) goto SkipTo5;
				return ($ErrNO - 2); 
			}
			$CA['Charset'] = SDATA($db_con,$request,'cs',1,'UTF-8',32); 
			$CA['MX'] = SDATA($db_con,$request,'mx',2,0,MAX_INT,0); 
			$CA['MY'] = SDATA($db_con,$request,'my',2,0,MAX_INT,0); 
			$CA['X'] = SDATA($db_con,$request,'x',2,-1,100,-1); 
			$CA['Y'] = SDATA($db_con,$request,'y',2,-1,100,-1); 
			$CA['ClientWidth'] = SDATA($db_con,$request,'bcw',2,0,MAX_INT,0); 
			$CA['ClientHeight'] = SDATA($db_con,$request,'bch',2,0,MAX_INT,0); 
			$CA['ClientLeft'] = SDATA($db_con,$request,'bcl',2,0,MAX_INT,0); 
			$CA['ClientTop'] = SDATA($db_con,$request,'bct',2,0,MAX_INT,0); 
			$CA['Page'] = SDATA($db_con,$request,'pg',1,'',1024,0,1,$CA['Charset']); 
			$CA['PageMD5'] = $CA['Page'] ? smd5($CA['Page']) : ''; 
			$CA['NodeActionType'] = SDATA($db_con,$request,'act',2,0,127,0); 
			$CA['NodeIDMD5'] = SDATA($db_con,$request,'id',1,'',32); 
			$CA['NodeHtmlMD5'] = SDATA($db_con,$request,'html',1,'',32); 
			$CA['NodeTagMD5'] = SDATA($db_con,$request,'tag',1,'',32); 
			$CA['NodeNodeMD5'] = SDATA($db_con,$request,'node',1,'',32); 
			$CA['NodeHref'] = SDATA($db_con,$request,'href',1,'',1024); 
			$CA['NodeHrefMD5'] = $CA['NodeHref'] ? smd5($CA['NodeHref']) : ''; 
			$CA['NodeText'] = SDATA($db_con,$request,'txt',1,'',256); 
			$CA['NodeRepeatClick'] = SDATA($db_con,$request,'rpc',2,0,1,0); 
			$Is_FIX = SDATA($db_con,$request,'fix',2,0,1,0); 
			$redis_key = $redis_vc . $CA['RecordNo'] . '-' . $CA['UCR'];
			$VC_EXIST = $redis_3->SADD($redis_tb.'-VCKey', $CA['RecordNo'] . '-' . $CA['UCR']);
			if ($VC_EXIST === 1) {
				$redis_3->PIPELINE();
				$redis_array = get_redis_array('vc_insert', $CA);
				$redis_3->HMSET($redis_key, $redis_array);
				$redis_3->SADD($redis_vc, $redis_key);
				$redis_3->HINCRBY($CA_REDIS, 'Clicks', 1); 
				if ($CA['NodeActionType'] > 0) $redis_3->HINCRBY($CA_REDIS, 'ValidClicks', 1);
				$redis_3->EXEC();
			} else if ($Is_FIX === 1) {
				$redis_3->PIPELINE();
				$redis_array = get_redis_array('vc_insert', $CA);
				$redis_3->HMSET($redis_key, $redis_array);
				$redis_3->SADD($redis_vc, $redis_key);
				$redis_3->EXEC();
			} 
			if ($CA['StatusCode'] === 8) break;
			SkipTo5:
		case 4:
		case 7:
			if ($CA['StatusCode'] !== 5 && $redis_3->HLEN($CA_REDIS) < CA_TOTAL_ARRAY_LENGTH) return 0;
			if ($ExitType > 0) {
				$CA['ExitCode'] = 4;
				$redis_array = get_redis_array('ca_beat_exit', $CA);
			} else {
				$redis_array = get_redis_array('ca_beat', $CA);
			}
			$redis_3->HMSET($CA_REDIS, $redis_array);
			if (($CA['UAR'] = SDATA($db_con,$request,'uar',5,'EXIT')) !== false) {
				$CA['DeviceType'] = SDATA($db_con,$request,'dct',2,0,4,0); 
				$CA['FromType'] = (int)$redis_3->HGET($SessionName, 'FT');
				if (($CA['VA'] = SDATA($db_con,$request,'va',3,'EXIT',5120)) === false) return ($ErrNO - 3); 
				if (($CA['UAS'] = SDATA($db_con,$request,'uas',2,'EXIT',4,1)) === false) return ($ErrNO - 4); 
				$redis_key = $redis_va . $CA['RecordNo'] . '-' . $CA['UAR'];
				$tmp = 'VA' . $CA['UAS'];
				if ((bool)$redis_3->EXISTS($redis_key) === false) {
					$redis_array = array('VID' => $CA['VID'], 'RecordNo' => $CA['RecordNo'], 'DeviceType' => $CA['DeviceType'], 'FromType' => $CA['FromType'], 'UAR' => $CA['UAR'], $tmp => $CA['VA'], 'RecordTime' => $CA['RecordTime'], 'UpdateTime' => $CA['UpdateTime']);
				} else {
					$redis_array = array($tmp => $CA['VA'], 'RecordTime' => $CA['RecordTime'], 'UpdateTime' => $CA['UpdateTime']);
				}
				$redis_3->HMSET($redis_key, $redis_array);
				$redis_3->SADD($redis_va, $redis_key);
			}
			if ($CA['StatusCode'] === 4) {
				$redis_3->ZADD($SessionList, 1, $SessionName);
			}
			break;
		default:
			return 0;
		}
		$process_time = (int)(microtime(true) * 1E6) - $start_time;
		$redis_0->MULTI()
				->INCRBY('PerformanceConsume'.$CA['StatusCode'], $process_time)
				->INCR('PerformanceCount'.$CA['StatusCode'])
				->EXEC();
		return $process_time;
}


?> 
