<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analyticsb Free Persistence PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


ignore_user_abort(true); // run script in background
set_time_limit(0); // run script forever 

@require './config/config_common.php';
require 'kernel.sql.php';
require 'kernel.func.php';

define('SESSION_CLOSE_TIME', 36E8);
define('KEY_ERROR_FATAL', 'ErrorFatal');
define('KEY_ERROR_EXECUTE', 'ErrorExecute');
//storage version
define('STORAGE_VERSION', '1.00.180127031');


//SET TIMEZONE
date_default_timezone_set(ADMIN_TIMEZONE);

	
//create redis object
$REDIS_0 = new Redis();
if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) {
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Create REDIS_0 Object Failed In Persistence Module');
	exit;
}
$REDIS_0->SELECT(REDIS_DB_0);//select No 0 redis database for process information

//set Mutual Exclusion flag for only one processor
$RET = $REDIS_0->SET('PersistenceMutualExclusion', '', array('nx', 'ex'=>600));		
if ($RET !== true && isset($_SERVER['argv'][1]) === false) exit;
//*/

//******** set persistence version begin ********
$REDIS_0->SET('PersistenceVersion', STORAGE_VERSION);
//********* set persistence version end *********

$REDIS_2 = new Redis();
if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) {
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Create REDIS_2 Object Failed In Persistence Module');
	exit;
}
$REDIS_2->SELECT(REDIS_DB_2);//select No 2 redis database for check blocked IP

$REDIS_3 = new Redis();
if ($REDIS_3->CONNECT(REDIS_IP_3, REDIS_PORT_3) !== true) {
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Create REDIS_3 Object Failed In Persistence Module');
	exit;
}
$REDIS_3->SELECT(REDIS_DB_3);//select No 3 redis database for record process infomation & query list


//connect database
$DB = false;
$DB = con_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
if ($DB === false) {
	$REDIS_0->LPUSH(KEY_ERROR_FATAL, 'Connect Database Failed In Persistence Module');//fatal error
	$REDIS_0->DEL('PersistenceMutualExclusion');//delete Mutual Exclusion flag
	autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'Connect Database Failed In Persistence Module');
	exit;
}


autoresponse(NOTIFICATION_MAIL, ADMIN_MAIL, 'NOTIFICATION OF CA PERSISTENCE', 'CA PERSISTENCE WAS RESTARTED');


$PERIOD_MIN = (int)(microtime(true) * 1E6) + 6E7;

while (true) {
	//check peristence status [On/Off]
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
					//check peristence status [On/Off]
					$PERSISTENCE_STATUS = (int)$REDIS_0->GET('PersistenceStatus');
					if ($PERSISTENCE_STATUS !== 0) {//
						$REDIS_0->PERSIST('PersistenceMutualExclusion');
						exit;
					}

					//do persistence
					$COUNT += persistence($REDIS_TB, $DB, $REDIS_0, $REDIS_2, $REDIS_3, $TIMELINE);
				}

				if ($COUNT > 0) {
					$CONSUME = (int)(microtime(true) * 1E6) - $START_TIME;
					$REDIS_0->MULTI()
							->INCRBY('PerformanceConsume12', $CONSUME)//set persistence performance consume
							->INCR('PerformanceCount12')//set persistence performance count
							->EXEC();
				}

			}
		}
	}

	sleep(1); //1 minute sleep
	$REDIS_0->EXPIRE('PersistenceMutualExclusion', 600);
}




function persistence($redis_tb, &$db_con, &$redis_0, &$redis_2, &$redis_3, $TIMELINE) {
	
	$START_TIME = (int)(microtime(true) * 1E6);
	$PROCESS_INSERT_COUNT = 0;
	$PROCESS_INSERT_CONSUME = 0;
	$PROCESS_CA_MISS_COUNT = 0;
	$PROCESS_VA_MISS_COUNT = 0;
	$PROCESS_VC_MISS_COUNT = 0;
	$PROCESS_VID_MISS_COUNT = 0;
	$PROCESS_IND_MISS_COUNT = 0;
	
	$TMP	 = explode('-', $redis_tb);
	$SID	 = $TMP[0];
	$TB_DATE = $TMP[1];
	
	$REDIS_RETURN = $redis_3->HMGET($redis_tb, array('PeriodTime', 'LastPersistenceTime')); 

	if (empty($SID) || empty($TB_DATE) || empty($REDIS_RETURN)) {
		$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR: Init data failed!<br><br>Redis TB: ' . $redis_tb . '<br>Period Time: '. $REDIS_RETURN['PeriodTime'] . '<br>Last Process Time: '. $REDIS_RETURN['LastPersistenceTime'] . '<br>Timeline: '. $TIMELINE . '<br><br>');//error message
		exit;
	}


	//CHECK MYSQL CONNECTION BEGIN
	if (use_db($db_con, 'site' . $SID) === false) {
			$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR: Use database failed! TIME: ' . date('Y-m-d H:i:s',time()) . '<br><br>DB Name: site' . $SID . '<br><br>ERROR ' . mysqli_errno($db_con) . ': ' . mysqli_error($db_con) . '<br><br>');//error message
			restartPersistence();
	}
	//CHECK MYSQL CONNECTION END

	//CHECK REDIS CONNECTION BEGIN
	if ($redis_0->PING() !== '+PONG') { 
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'PERSISTENCE ERROR: REDIS_0 connection broke down.<br><br>DB Name: site' . $SID . '<br><br>');//error message
			restartPersistence();
	}
	if ($redis_2->PING() !== '+PONG') {
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'PERSISTENCE ERROR: REDIS_2 connection broke down.<br><br>DB Name: site' . $SID . '<br><br>');//error message
			restartPersistence();
	}
	if ($redis_3->PING() !== '+PONG') {
			autoresponse(FATALERROR_MAIL, ADMIN_MAIL, 'CA PERSISTENCE FATAL ERROR', 'PERSISTENCE ERROR: REDIS_3 connection broke down.<br><br>DB Name: site' . $SID . '<br><br>');//error message
			restartPersistence();
	}
	//CHECK REDIS CONNECTION END



	$FINAL = ($TIMELINE - (int)$REDIS_RETURN['PeriodTime']) > SESSION_CLOSE_TIME ? true : false;
//$redis_0->SET('Final' , ($FINAL ? 1 : 0) . '; Timeline: ' . $TIMELINE . '; PeriodTime: ' . $REDIS_RETURN['PeriodTime'] . '; SESSION_CLOSE_TIME:' . SESSION_CLOSE_TIME);


	//get database name & table name
	$redis_ca =  $redis_tb . '-CA-'; //centcount analytics table name
	$redis_va =  $redis_tb . '-VA-'; //visitor action table name
	$redis_vc =  $redis_tb . '-VC-'; //visitor click event table name
	$redis_ind = $redis_tb . '-IND-'; //indicator data table name


	//gen database name & table name
	$tb_log = 'log' . $TB_DATE; //centcount analytics table name
	$tb_act = 'act' . $TB_DATE; //visitor action table name
	$tb_clk = 'clk' . $TB_DATE; //visitor click event table name
	$tb_vid = 'vid' . $TB_DATE; //visitor VID table name
	$tb_ind = 'ind' . $TB_DATE; //indicator data table name


	$VIDS = array();





	//PROCESS SESSION WHEN AT FINAL PROCESS BEGIN 
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
	//PROCESS SESSION WHEN AT FINAL PROCESS END





	
	// PROCESS CA TABLE BEGIN
	if ($FINAL) {
		
		//FINAL DELETE REDIS CA BEGIN
		$REDIS_RETURN = $redis_3->KEYS($redis_ca.'*'); 
		if (count($REDIS_RETURN) > 0) {
	
			//INSERT CA BEGIN 
			$i = 0;
			$n = 0;
			$ca_insert = "REPLACE INTO {$tb_log} ".CA_INSERT." VALUES";
			$ca_delete = array();
			
			foreach ($REDIS_RETURN as $val) {

				array_push($ca_delete, $val);
				$n++;
				
				$RS = $redis_3->HGETALL($val);
				if (count($RS) < CA_TOTAL_ARRAY_LENGTH) {//112
					$PROCESS_CA_MISS_COUNT++;
					
					if ($n > 999) {
						$redis_3->DEL($ca_delete);
						$ca_delete = array();
						$n = 0;
					}

					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 107, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($val, 29, 10)) . ', <br>Redis Name: ' . $val . export_array($RS));
					continue;
				}
				
				
				if ((int)$RS['LUT'] < 7) {//LUT(Last Update Flag): 0->无记录无更新, 1->无记录未更新, 2->无记录未更新最终状态, 3->有记录未更新, 4->有记录未更新最终状态, 6->更新后删除,7->直接删除, 9->有记录已更新, 10->已持久化最终状态
					$ca_insert .= ($i === 0 ? '' : ',') . get_ca_insert_value($RS);
					if (!isset($VIDS[$RS['VID']]) || $VIDS[$RS['VID']] < $RS['RecordNo']) $VIDS[$RS['VID']] = $RS['RecordNo'];
					$i++;
					break;	
				}
	
				if ($i > 999) {
					$Err = '';
					if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 107, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
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
			
			//OUT OF LOOP
			if ($i > 0) {
				$Err = '';
				if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 107, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
				}
				$PROCESS_INSERT_COUNT += $i;
			}
			
			if ($n > 0) {
				$redis_3->DEL($ca_delete);
			}
			//INSERT CA END
		}
		//FINAL DELETE REDIS CA END
		
	} else {//not final operating
		
		// PROCESS CA TABLE BEGIN
		$REDIS_RETURN = $redis_3->MULTI()
								->SMEMBERS($redis_ca)//set insert performance consume
								->DEL($redis_ca)//set insert performance count
								->EXEC();
		//$REDIS_RETURN = $redis_3->KEYS($redis_ca.'*'); 
		if (count($REDIS_RETURN[0]) > 0) {
			//INSERT CA BEGIN 
			$i = 0;
			$n = 0;
			$ca_insert = "REPLACE INTO {$tb_log} ".CA_INSERT." VALUES";
			$ca_delete = array();

			foreach ($REDIS_RETURN[0] as $val) {
	
				$RS = $redis_3->HGETALL($val);
				if (count($RS) < CA_TOTAL_ARRAY_LENGTH) {//112
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 101, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Record Time: ' . date('Y-m-d H:i:s', substr($val, 29, 10)) . ', <br>Redis Name: ' . $val . export_array($RS));
					continue;
				}

				
				switch ((int)$RS['LUT']) {//LUT(Last Update Flag): 0->无记录无更新, 1->无记录未更新, 2->无记录未更新最终状态, 3->有记录未更新, 4->有记录未更新最终状态, 6->更新后删除,7->直接删除, 9->有记录已更新, 10->已持久化最终状态
				case 2://无记录未更新最终状态
				case 4:
					$redis_3->HSET($val, 'LUT', 10);
					$ca_insert .= ($i === 0 ? '' : ',') . get_ca_insert_value($RS);
					if (!isset($VIDS[$RS['VID']]) || $VIDS[$RS['VID']] < $RS['RecordNo']) $VIDS[$RS['VID']] = $RS['RecordNo'];
					$i++;
					break;

				case 6://更新后删除
					$ca_insert .= ($i === 0 ? '' : ',') . get_ca_insert_value($RS);
					if (!isset($VIDS[$RS['VID']]) || $VIDS[$RS['VID']] < $RS['RecordNo']) $VIDS[$RS['VID']] = $RS['RecordNo'];
					$i++;

				case 7://直接删除
					array_push($ca_delete, $val);
					$n++;
					break;

				default:
					continue;	
				}
	
	
				if ($i > 999) {
					$Err = '';
					if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 101, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
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
			
			//OUT OF LOOP
			if ($i > 0) {
				$Err = '';
				if (!record_data($db_con, $ca_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 101, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
				}
				$PROCESS_INSERT_COUNT += $i;
			}
			
			if ($n > 0) {
				$redis_3->DEL($ca_delete);	
			}
			//INSERT CA END
		}
	}
	// PROCESS CA TABLE END
	


	
	
	
	// PROCESS UPDATE VID TABLE BEGIN
	if (count($VIDS) > 0) {
			
			$i = 0;
			$vid_replace = "REPLACE INTO {$tb_vid} (VID, RecordNo) VALUES";
			foreach ($VIDS as $key => $val) {

				if (empty($key) || empty($val)) {
					$PROCESS_VID_MISS_COUNT++;
					continue;
				}

				$vid_replace .= $i === 0 ? "({$key}, {$val})" : ",({$key}, {$val})";
	
				$i++;
	
				if ($i > 999) {
					$Err = '';
					if (!record_data($db_con, $vid_replace, 4, $i, $Err)) {
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 103, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
					}
					
					$vid_replace = "REPLACE INTO {$tb_vid} (VID, RecordNo) VALUES";
					$PROCESS_INSERT_COUNT += $i;
					$i = 0;
				}
			}
			
			//OUT OF LOOP
			if ($i > 0) {
				$Err = '';
				if (!record_data($db_con, $vid_replace, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 103, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
				}
				
				$PROCESS_INSERT_COUNT += $i;
			}


			//release VIDS
			unset($VIDS);

	}
	// PROCESS UPDATE VID TABLE END
	
	
	
	


	
	// PROCESS INDICATOR TABLE BEGIN
	$REDIS_RETURN = $redis_3->MULTI()
							->SMEMBERS($redis_ind.'UPDATE')//set insert performance consume
							->DEL($redis_ind.'UPDATE')//set insert performance count
							->EXEC();
	//$REDIS_RETURN = $redis_3->KEYS($redis_ind.'*'); 
	if (count($REDIS_RETURN[0]) > 0) {
		
		//INSERT OR UPDATE INDICATOR BEGIN 
		$i = 0;
		$ind_insert = "REPLACE INTO {$tb_ind} ".IND_INSERT." VALUES";
		foreach ($REDIS_RETURN[0] as $val) {

			$RS = $redis_3->HGETALL($val);
			if (count($RS) < CA_INDICATOR_ARRAY_LENGTH) {//31
				$redis_3->DEL($val);
				$PROCESS_IND_MISS_COUNT++;
				
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 104, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Redis Name: ' . $val . export_array($RS));
				continue;
			}
			
			//if ((int)$RS['LUT'] < 9) {//LUT(Last Update Flag): 0->无记录无更新, 1->无记录未更新, 2->无记录未更新最终状态, 3->有记录未更新, 4->有记录未更新最终状态, 9->有记录已更新
				$ind_insert .=  ($i === 0 ? '' : ',') . get_ind_insert_value($RS);
				$redis_3->HMSET($val, array('LUT' => 9));
				$i++;
			//} else {
			//	continue;	
			//}
			
			if ($i > 999) {
				$Err = '';
				if (!record_data($db_con, $ind_insert, 4, $i, $Err)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 104, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
				}

				$ind_insert = "REPLACE INTO {$tb_ind} ".IND_INSERT." VALUES";
				$PROCESS_INSERT_COUNT += $i;
				$i = 0;
			}
		}
		
		//OUT OF LOOP
		if ($i > 0) {
			$Err = '';
			if (!record_data($db_con, $ind_insert, 4, $i, $Err)) {
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 104, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);//error message
			}
			
			$PROCESS_INSERT_COUNT += $i;
		}
		//INSERT OR UPDATE INDICATOR END
	}
	// PROCESS INDICATOR TABLE END
	
	


	
	
	
	// PROCESS INSERT VA TABLE BEGIN
	$REDIS_RETURN = $redis_3->SMEMBERS($redis_va);
	//$REDIS_RETURN = $redis_3->KEYS($redis_va.'*'); 
	if (count($REDIS_RETURN) > 0) {
		$i = 0;
		$n = 0;
		$va_insert = "REPLACE INTO {$tb_act} (VID,RecordNo,DeviceType,FromType,UAR,VA1,VA2,VA3,VA4,RecordTime) VALUES";
		$va_delete = array();
		foreach ($REDIS_RETURN as $val) {

			$RS = $redis_3->HGETALL($val);
			if (count($RS) < CA_VA_ARRAY_LENGTH) {//8
				$PROCESS_VA_MISS_COUNT++;
				array_push($va_delete, $val);
				$n++;

				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 105, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>Redis Name: ' . $val . export_array($RS));
				continue;
			}
			
			if ($RS['UpdateTime'] < ($TIMELINE - 9E7)) {//如果超过90秒无更新则做持久化处理
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
		
		//OUT OF LOOP
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
	// PROCESS INSERT VA TABLE END
	
	
	
	
	
	
	
	
	// PROCESS VC TABLE BEGIN
	$REDIS_RETURN = $redis_3->MULTI()
							->SMEMBERS($redis_vc)//set insert performance consume
							->DEL($redis_vc)//set insert performance count
							->EXEC();
	//$REDIS_RETURN = $redis_3->KEYS($redis_vc.'*'); 
	if (count($REDIS_RETURN[0]) > 0) {
		//INSERT VC BEGIN
		$i = 0;
		$n = 0;
		$vc_insert = "REPLACE INTO {$tb_clk} " . VC_INSERT . " VALUES";
		$vc_delete = array();
		foreach ($REDIS_RETURN[0] as $val) {

			$RS = $redis_3->HGETALL($val);
			if (count($RS) < CA_VC_ARRAY_LENGTH) {//35
				$PROCESS_VC_MISS_COUNT++;
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
		
		//OUT OF LOOP
		if ($i > 0) {
			$Err = '';
			if (!record_data($db_con, $vc_insert, 4, $i, $Err)) {
				$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'PERSISTENCE ERROR NO: 106, TIME: ' . date('Y-m-d H:i:s',time()) . ', <br><br>DB ERROR - ' . $Err);
			}
			
			$PROCESS_INSERT_COUNT += $i;
		}

		if ($n > 0) $redis_3->DEL($vc_delete);
		//INSERT VC END
	}
	// PROCESS VC TABLE END
	
	
	
		
	//SET LAST UPDATE TIME FOR NEXT PROCESS
	$redis_3->HMSET($redis_tb, array('LastPersistenceTime' => $TIMELINE));




	
	//final process to remove all redis keys
	if ($FINAL) {	
		//FINAL DELETE BEGIN
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
			
			//OUT OF LOOP
			if ($n > 0) {
				$redis_3->DEL($ca_delete);
			}
		}
		//FINAL DELETE END
		
		$redis_0->SREM('DayPeriod', $redis_tb);
		$redis_3->DEL($redis_tb);
	}




	//set performance
	if ($PROCESS_INSERT_COUNT > 0) {
		$CONSUME = (int)(microtime(true) * 1E6) - $START_TIME;
		$PROCESS_INSERT_CONSUME += $CONSUME;
		
		$redis_0->MULTI()
				->INCRBY('PerformanceConsume11', $PROCESS_INSERT_CONSUME)//set insert performance consume
				->INCRBY('PerformanceCount11', $PROCESS_INSERT_COUNT)//set insert performance count
				->EXEC();

		$redis_0->PIPELINE();
		if ($PROCESS_CA_MISS_COUNT) $redis_0->INCRBY('MissedCA', $PROCESS_CA_MISS_COUNT);//SET MISSED CA COUNT
		if ($PROCESS_VA_MISS_COUNT) $redis_0->INCRBY('MissedVA', $PROCESS_VA_MISS_COUNT);//SET MISSED VA COUNT
		if ($PROCESS_VC_MISS_COUNT) $redis_0->INCRBY('MissedVC', $PROCESS_VC_MISS_COUNT);//SET MISSED VC COUNT
		if ($PROCESS_VID_MISS_COUNT) $redis_0->INCRBY('MissedVID', $PROCESS_VID_MISS_COUNT);//SET MISSED VID COUNT
		if ($PROCESS_IND_MISS_COUNT) $redis_0->INCRBY('MissedIND', $PROCESS_IND_MISS_COUNT);//SET MISSED IND COUNT
		$redis_0->EXEC();

		return $CONSUME;
	} else {
		return 0;
	}
		
}// PERSISTENCE FUNCTION END


function restartPersistence() {
	pclose(popen('php -f ' . __DIR__ . '/persistence.php 1 &', 'r'));
	exit;
}



?>