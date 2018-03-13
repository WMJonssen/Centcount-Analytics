<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analyticsb Free Persistence PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/13/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

ignore_user_abort(true); 
set_time_limit(0); 
@require './config/config_common.php';
require 'kernel.sql.php';
require 'kernel.func.php';
define('SESSION_CLOSE_TIME', 36E8);
define('KEY_ERROR_FATAL', 'ErrorFatal');
define('KEY_ERROR_EXECUTE', 'ErrorExecute');
define('STORAGE_VERSION', '1.00.180313001');
date_default_timezone_set(ADMIN_TIMEZONE);
$REDIS_0 = new Redis();
if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) {
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Create REDIS_0 Object Failed In Persistence Module');
	exit;
}
$REDIS_0->SELECT(REDIS_DB_0);
$RET = $REDIS_0->SET('PersistenceMutualExclusion', '', array('nx', 'ex'=>600));		
if ($RET !== true && isset($_SERVER['argv'][1]) === false) exit;
$REDIS_0->SET('PersistenceVersion', STORAGE_VERSION);
$REDIS_2 = new Redis();
if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) {
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Create REDIS_2 Object Failed In Persistence Module');
	exit;
}
$REDIS_2->SELECT(REDIS_DB_2);
$REDIS_3 = new Redis();
if ($REDIS_3->CONNECT(REDIS_IP_3, REDIS_PORT_3) !== true) {
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Create REDIS_3 Object Failed In Persistence Module');
	exit;
}
$REDIS_3->SELECT(REDIS_DB_3);
$DB = false;
$DB = con_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
if ($DB === false) {
	$REDIS_0->LPUSH(KEY_ERROR_FATAL, 'Connect Database Failed In Persistence Module');
	$REDIS_0->DEL('PersistenceMutualExclusion');
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Connect Database Failed In Persistence Module');
	exit;
}
autoresponse(NOTIFICATION_MAIL, ADMIN_MAIL, 'NOTIFICATION OF CA PERSISTENCE', 'CA PERSISTENCE WAS RESTARTED');
$PERIOD_MIN = (int)(microtime(true) * 1E6) + 6E7;
while (true) {
	$PERSISTENCE_STATUS = (int)$REDIS_0->GET('PersistenceStatus');
	if ($PERSISTENCE_STATUS !== 0) {
		$REDIS_0->PERSIST('PersistenceMutualExclusion');
		exit;
	}
	$START_TIME = (int)(microtime(true) * 1E6);
	if ($START_TIME > $PERIOD_MIN) {
		$PERIOD_MIN = $START_TIME + 6E7;
		$TIMELINE = (int)($REDIS_0->GET('TimeLine'));
		if ($TIMELINE) {
			$SITES = $REDIS_0->SMEMBERS('DayPeriod');
			if (count($SITES) > 0) {
				$COUNT = 0;
				foreach ($SITES as $REDIS_TB) {
					$PERSISTENCE_STATUS = (int)$REDIS_0->GET('PersistenceStatus');
					if ($PERSISTENCE_STATUS !== 0) {
						$REDIS_0->PERSIST('PersistenceMutualExclusion');
						exit;
					}
					$COUNT += persistence($REDIS_TB, $DB, $REDIS_0, $REDIS_2, $REDIS_3, $TIMELINE);
				}
				if ($COUNT > 0) {
					$CONSUME = (int)(microtime(true) * 1E6) - $START_TIME;
					$REDIS_0->MULTI()
							->INCRBY('PerformanceConsume12', $CONSUME)
							->INCR('PerformanceCount12')
							->EXEC();
				}
			}
		}
	}
	sleep(1); 
	$REDIS_0->EXPIRE('PersistenceMutualExclusion', 600);
}
function persistence($redis_tb, &$db_con, &$redis_0, &$redis_2, &$redis_3, $TIMELINE) {
	$START_TIME = (int)(microtime(true) * 1E6);
	$PROCESS_INSERT_COUNT = 0;
	$PROCESS_INSERT_CONSUME = 0;
	$TMP	 = explode('-', $redis_tb);
	$SID	 = $TMP[0];
	$TB_DATE = $TMP[1];
	$REDIS_RETURN = $redis_3->HMGET($redis_tb, array('PeriodTime', 'LastPersistenceTime')); 
	if (empty($SID) || empty($TB_DATE) || empty($REDIS_RETURN)) {
		$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR: Init data failed!<br><br>Redis TB: ' . $redis_tb . '<br>Period Time: '. $REDIS_RETURN['PeriodTime'] . '<br>Last Process Time: '. $REDIS_RETURN['LastPersistenceTime'] . '<br>Timeline: '. $TIMELINE . '<br><br>');
		exit;
	}
	if (use_db($db_con, 'site' . $SID) === false) {
			$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR: Use database failed! TIME: ' . date('Y-m-d H:i:s',time()) . '<br><br>DB Name: site' . $SID . '<br><br>ERROR ' . mysqli_errno($db_con) . ': ' . mysqli_error($db_con) . '<br><br>');
			restartPersistence();
	}
	if ($redis_0->PING() !== '+PONG') { 
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'PERSISTENCE ERROR: REDIS_0 connection broke down.<br><br>DB Name: site' . $SID . '<br><br>');
			restartPersistence();
	}
	if ($redis_2->PING() !== '+PONG') {
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'PERSISTENCE ERROR: REDIS_2 connection broke down.<br><br>DB Name: site' . $SID . '<br><br>');
			restartPersistence();
	}
	if ($redis_3->PING() !== '+PONG') {
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'PERSISTENCE ERROR: REDIS_3 connection broke down.<br><br>DB Name: site' . $SID . '<br><br>');
			restartPersistence();
	}
	$FINAL = ($TIMELINE - (int)$REDIS_RETURN['PeriodTime']) > SESSION_CLOSE_TIME ? true : false;
	$redis_ca =  $redis_tb . '-CA-'; 
	$redis_va =  $redis_tb . '-VA-'; 
	$redis_vc =  $redis_tb . '-VC-'; 
	$redis_ind = $redis_tb . '-IND-'; 
	$tb_log = 'log' . $TB_DATE; 
	$tb_act = 'act' . $TB_DATE; 
	$tb_clk = 'clk' . $TB_DATE; 
	$tb_vid = 'vid' . $TB_DATE; 
	$tb_ind = 'ind' . $TB_DATE; 
	$VIDS = array();
	if ($FINAL) {
		$REDIS_RETURN = $redis_3->MULTI()
								->ZRANGE($redis_tb.'-SessionList', 0, -1, true)
								->DEL($redis_tb.'-SessionList')
								->EXEC();
		if (count($REDIS_RETURN[0]) > 0) {
			foreach ($REDIS_RETURN[0] as $SESSION_ID => $VAL) {
				processSession('PERSISTENCE', $SESSION_ID, $redis_0, $redis_2, $redis_3);
			}
		}
	}
	if ($FINAL) {
		$REDIS_RETURN = $redis_3->KEYS($redis_ca.'*'); 
		if (count($REDIS_RETURN) > 0) {
			$i = 0;
			$n = 0;
			$ca_insert = "REPLACE INTO {$tb_log} ".CA_INSERT." VALUES";
			$ca_delete = array();
			foreach ($REDIS_RETURN as $val) {
				array_push($ca_delete, $val);
				$n++;
				$RS = $redis_3->HGETALL($val);
				if (count($RS) < CA_TOTAL_ARRAY_LENGTH) {
					if ($n > 999) {
						$redis_3->DEL($ca_delete);
						$ca_delete = array();
						$n = 0;
					}
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 107, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($val, 29, 10)) . ', <br>Redis Name: ' . $val . export_array($RS));
					continue;
				}
				if ((int)$RS['LUT'] < 7) {
					$ca_insert .= ($i === 0 ? '' : ',') . get_ca_insert_value($RS);
					if (!isset($VIDS[$RS['VID']]) || $VIDS[$RS['VID']] < $RS['RecordNo']) $VIDS[$RS['VID']] = $RS['RecordNo'];
					$i++;
					break;	
				}
				if ($i > 999) {
					$Err = '';
					if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 107, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
					}
					$ca_insert = "REPLACE INTO {$tb_log} ".CA_INSERT." VALUES";
					$PROCESS_INSERT_COUNT += $i;
					$i = 0;
				}
				if ($n > 999) {
					$redis_3->DEL($ca_delete);
					$ca_delete = array();
					$n = 0;
				}
			}
			if ($i > 0) {
				$Err = '';
				if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 107, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
				}
				$PROCESS_INSERT_COUNT += $i;
			}
			if ($n > 0) {
				$redis_3->DEL($ca_delete);
			}
		}
	} else {
		$REDIS_RETURN = $redis_3->MULTI()
								->SMEMBERS($redis_ca)
								->DEL($redis_ca)
								->EXEC();
		if (count($REDIS_RETURN[0]) > 0) {
			$i = 0;
			$n = 0;
			$ca_insert = "REPLACE INTO {$tb_log} ".CA_INSERT." VALUES";
			$ca_delete = array();
			foreach ($REDIS_RETURN[0] as $val) {
				$RS = $redis_3->HGETALL($val);
				if (count($RS) < CA_TOTAL_ARRAY_LENGTH) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 101, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($val, 29, 10)) . ', <br>Redis Name: ' . $val . export_array($RS));
					continue;
				}
				switch ((int)$RS['LUT']) {
				case 2:
				case 4:
					$redis_3->HSET($val, 'LUT', 10);
					$ca_insert .= ($i === 0 ? '' : ',') . get_ca_insert_value($RS);
					if (!isset($VIDS[$RS['VID']]) || $VIDS[$RS['VID']] < $RS['RecordNo']) $VIDS[$RS['VID']] = $RS['RecordNo'];
					$i++;
					break;
				case 6:
					$ca_insert .= ($i === 0 ? '' : ',') . get_ca_insert_value($RS);
					if (!isset($VIDS[$RS['VID']]) || $VIDS[$RS['VID']] < $RS['RecordNo']) $VIDS[$RS['VID']] = $RS['RecordNo'];
					$i++;
				case 7:
					array_push($ca_delete, $val);
					$n++;
					break;
				default:
					continue;	
				}
				if ($i > 999) {
					$Err = '';
					if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 101, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
					}
					$ca_insert = "REPLACE INTO {$tb_log} ".CA_INSERT." VALUES";
					$PROCESS_INSERT_COUNT += $i;
					$i = 0;
				}
				if ($n > 999) {
					$redis_3->DEL($ca_delete);
					$ca_delete = array();
					$n = 0;
				}
			}
			if ($i > 0) {
				$Err = '';
				if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 101, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
				}
				$PROCESS_INSERT_COUNT += $i;
			}
			if ($n > 0) {
				$redis_3->DEL($ca_delete);	
			}
		}
	}
	if (count($VIDS) > 0) {
			$i = 0;
			$vid_replace = "REPLACE INTO {$tb_vid} (VID, RecordNo) VALUES";
			foreach ($VIDS as $key => $val) {
				if (empty($key) || empty($val)) {
					continue;
				}
				$vid_replace .= $i === 0 ? "({$key}, {$val})" : ",({$key}, {$val})";
				$i++;
				if ($i > 999) {
					$Err = '';
					if (!record_data($db_con, $vid_replace, 4, $i, $Err)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 103, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
					}
					$vid_replace = "REPLACE INTO {$tb_vid} (VID, RecordNo) VALUES";
					$PROCESS_INSERT_COUNT += $i;
					$i = 0;
				}
			}
			if ($i > 0) {
				$Err = '';
				if (!record_data($db_con, $vid_replace, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 103, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
				}
				$PROCESS_INSERT_COUNT += $i;
			}
			unset($VIDS);
	}
	$REDIS_RETURN = $redis_3->MULTI()
							->SMEMBERS($redis_ind.'UPDATE')
							->DEL($redis_ind.'UPDATE')
							->EXEC();
	if (count($REDIS_RETURN[0]) > 0) {
		$i = 0;
		$ind_insert = "REPLACE INTO {$tb_ind} ".IND_INSERT." VALUES";
		foreach ($REDIS_RETURN[0] as $val) {
			$RS = $redis_3->HGETALL($val);
			if (count($RS) < CA_INDICATOR_ARRAY_LENGTH) {
				$redis_3->DEL($val);
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 104, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Redis Name: ' . $val . export_array($RS));
				continue;
			}
				$ind_insert .=  ($i === 0 ? '' : ',') . get_ind_insert_value($RS);
				$redis_3->HMSET($val, array('LUT' => 9));
				$i++;
			if ($i > 999) {
				$Err = '';
				if (!record_data($db_con, $ind_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 104, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
				}
				$ind_insert = "REPLACE INTO {$tb_ind} ".IND_INSERT." VALUES";
				$PROCESS_INSERT_COUNT += $i;
				$i = 0;
			}
		}
		if ($i > 0) {
			$Err = '';
			if (!record_data($db_con, $ind_insert, 4, $i, $Err)) {
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 104, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
			}
			$PROCESS_INSERT_COUNT += $i;
		}
	}
	$REDIS_RETURN = $redis_3->SMEMBERS($redis_va);
	if (count($REDIS_RETURN) > 0) {
		$i = 0;
		$n = 0;
		$va_insert = "REPLACE INTO {$tb_act} (VID,RecordNo,DeviceType,FromType,UAR,VA1,VA2,VA3,VA4,RecordTime) VALUES";
		$va_delete = array();
		foreach ($REDIS_RETURN as $val) {
			$RS = $redis_3->HGETALL($val);
			if (count($RS) < CA_VA_ARRAY_LENGTH) {
				array_push($va_delete, $val);
				$n++;
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 105, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Redis Name: ' . $val . export_array($RS));
				continue;
			}
			if ($RS['UpdateTime'] < ($TIMELINE - 9E7)) {
				if (!isset($RS['VA1'])) $RS['VA1'] = '';
				if (!isset($RS['VA2'])) $RS['VA2'] = '';
				if (!isset($RS['VA3'])) $RS['VA3'] = '';
				if (!isset($RS['VA4'])) $RS['VA4'] = '';
				$va_insert .= ($i === 0 ? '' : ',') . "({$RS['VID']},{$RS['RecordNo']},{$RS['DeviceType']},{$RS['FromType']},{$RS['UAR']},'{$RS['VA1']}','{$RS['VA2']}','{$RS['VA3']}','{$RS['VA4']}',{$RS['RecordTime']})";
				$i++;
				array_push($va_delete, $val);
				$n++;
			} else {
				continue;
			}
			if ($i > 999) {
				$Err = '';
				if (!record_data($db_con, $va_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 105, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
				}
				$va_insert = "REPLACE INTO {$tb_act} (VID,RecordNo,DeviceType,FromType,UAR,VA1,VA2,VA3,VA4,RecordTime) VALUES";
				$PROCESS_INSERT_COUNT += $i;
				$i = 0;
			}
			if ($n > 999) {
				$redis_3->PIPELINE();
					$redis_3->DEL($va_delete);
					foreach ($va_delete as $val) {
						$redis_3->SREM($redis_va, $val);
					}
				$redis_3->EXEC();
				$va_delete = array();
				$n = 0;
			}
		}
		if ($i > 0) {
			$Err = '';
			if (!record_data($db_con, $va_insert, 4, $i, $Err)) {
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 105, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
			}
			$PROCESS_INSERT_COUNT += $i;
		}
		if ($n > 0) {
			$redis_3->PIPELINE();
				$redis_3->DEL($va_delete);
				foreach ($va_delete as $val) {
					$redis_3->SREM($redis_va, $val);
				}
			$redis_3->EXEC();
		}
	}
	$REDIS_RETURN = $redis_3->MULTI()
							->SMEMBERS($redis_vc)
							->DEL($redis_vc)
							->EXEC();
	if (count($REDIS_RETURN[0]) > 0) {
		$i = 0;
		$n = 0;
		$vc_insert = "REPLACE INTO {$tb_clk} " . VC_INSERT . " VALUES";
		$vc_delete = array();
		foreach ($REDIS_RETURN[0] as $val) {
			$RS = $redis_3->HGETALL($val);
			if (count($RS) < CA_VC_ARRAY_LENGTH) {
				array_push($vc_delete, $val);
				$n++;
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 106, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Redis Name: ' . $val . export_array($RS));
				continue;
			}
			$vc_insert .= ($i === 0 ? '' : ',') . get_vc_insert_value($RS);
			$i++;
			array_push($vc_delete, $val);
			$n++;
			if ($i > 999) {
				$Err = '';
				if (!record_data($db_con, $vc_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 106, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
				}
				$vc_insert = "REPLACE INTO {$tb_clk} " . VC_INSERT . " VALUES";
				$PROCESS_INSERT_COUNT += $i;
				$i = 0;
			}
			if ($n > 999) {
				$redis_3->DEL($vc_delete);
				$vc_delete = array();
				$n = 0;
			}
		}
		if ($i > 0) {
			$Err = '';
			if (!record_data($db_con, $vc_insert, 4, $i, $Err)) {
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 106, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
			}
			$PROCESS_INSERT_COUNT += $i;
		}
		if ($n > 0) $redis_3->DEL($vc_delete);
	}
	$redis_3->HMSET($redis_tb, array('LastPersistenceTime' => $TIMELINE));
	if ($FINAL) {	
		$REDIS_RETURN = $redis_3->KEYS($redis_tb.'*'); 
		if (count($REDIS_RETURN) > 0) {
			$n = 0;
			$ca_delete = array();
			foreach ($REDIS_RETURN as $val) {
				array_push($ca_delete, $val);
				$n++;
				if ($n > 999) {
					$redis_3->DEL($ca_delete);
					$ca_delete = array();
					$n = 0;
				}
			}
			if ($n > 0) {
				$redis_3->DEL($ca_delete);
			}
		}
		$redis_0->SREM('DayPeriod', $redis_tb);
		$redis_3->DEL($redis_tb);
	}
	if ($PROCESS_INSERT_COUNT > 0) {
		$CONSUME = (int)(microtime(true) * 1E6) - $START_TIME;
		$PROCESS_INSERT_CONSUME += $CONSUME;
		$redis_0->MULTI()
				->INCRBY('PerformanceConsume11', $PROCESS_INSERT_CONSUME)
				->INCRBY('PerformanceCount11', $PROCESS_INSERT_COUNT)
				->EXEC();
		return $CONSUME;
	} else {
		return 0;
	}
}
function restartPersistence() {
	pclose(popen('php -f ' . __DIR__ . '/persistence.php 1 &', 'r'));
	exit;
}


?>