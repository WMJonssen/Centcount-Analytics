<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Kernel Common Function PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


$TMP_FP = popen('hostname', 'r');
define('HOST_NAME', trim(fread($TMP_FP, 128)));
pclose($TMP_FP);

function processSession(&$pid, &$session, &$redis_0, &$redis_2, &$redis_3) {
		$start_time = (int)(microtime(true) * 1E6);
		$IndARR = $redis_3->HMGET($session, array('S', 'T', 'E', 'F'));
		$SID = (count($IndARR) > 0) ? $IndARR['S'] : 0;
		if (empty($SID)) goto Fail;
		$TZ = $IndARR['T'];
		if (!date_default_timezone_set($TZ)) goto Fail;
		$FIRST_PAGE_RN = (int)$IndARR['F'];
		$END_PAGE_RN = (int)$IndARR['E'];
		$RN_ARR = $redis_3->SMEMBERS($session.'A');
		$TB_DATE = date('Ymd', floor($END_PAGE_RN / 1E6));
		$redis_ca =  $SID . '-' . $TB_DATE . '-CA-'; 
		$redis_ind = $SID . '-' . $TB_DATE . '-IND-'; 
		$redis_ses = $SID . '-' . $TB_DATE . '-SES-'; 
		if (count($RN_ARR) > 0) {
			foreach ($RN_ARR as $RN) {
				$redis_key = $redis_ca . $RN;
				$LUT = (int)$redis_3->HGET($redis_key, 'LUT');
				switch ($LUT) {
				case 1:
				case 3:
					if (!viewStatus($RN, $redis_2, $redis_3, $redis_ca, $redis_ind, $redis_ses, 2)) {
						$redis_3->HSET($redis_key, 'LUT', 7);
						$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4000, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . '<br><br>Record Time: ' . date('Y-m-d H:i:s', substr($RN, 0, 10)) . '<br>Session Name: ' . $session . $GLOBALS['ERROR_G'] . '<br><br>');
					} else {
						$redis_3->HSET($redis_key, 'LUT', 6);
					}
					break;
				case 2:
				case 4:
					$redis_3->HSET($redis_key, 'LUT', 6);
					break;
				case 10:
					$redis_3->HSET($redis_key, 'LUT', 7);
					break;	
				case 0:
				default:
					continue;
				}
				if ($RN !== $FIRST_PAGE_RN && $RN !== $END_PAGE_RN ) $redis_3->SADD($redis_ca, $redis_key);
			}
		}
		if (count($RN_ARR) > 1) {
			if ($FIRST_PAGE_RN > 0) {
				if (!exitStatus($FIRST_PAGE_RN, $redis_3, $redis_ca, $redis_ind, $redis_ses, 0)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4001, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . '<br><br>Record Time: ' . date('Y-m-d H:i:s', substr($FIRST_PAGE_RN, 0, 10)) . '<br>Session Name: ' . $session . $GLOBALS['ERROR_G'] . '<br><br>');
				}
				$redis_3->SADD($redis_ca, $redis_ca . $FIRST_PAGE_RN);
			}
			if ($END_PAGE_RN > 0) {
				if (!exitStatus($END_PAGE_RN, $redis_3, $redis_ca, $redis_ind, $redis_ses, 1)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4002, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . '<br><br>Record Time: ' . date('Y-m-d H:i:s', substr($END_PAGE_RN, 0, 10)) . '<br>Session Name: ' . $session . $GLOBALS['ERROR_G'] . '<br><br>');
				}
				$redis_3->SADD($redis_ca, $redis_ca . $END_PAGE_RN);
			}
		} else {
			if ($END_PAGE_RN > 0) {
				if (!exitStatus($END_PAGE_RN, $redis_3, $redis_ca, $redis_ind, $redis_ses, 2)) {
					$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4003, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . '<br><br>Record Time: ' . date('Y-m-d H:i:s', substr($END_PAGE_RN, 0, 10)) . '<br>Session Name: ' . $session . $GLOBALS['ERROR_G'] . '<br><br>');
				}
				$redis_3->SADD($redis_ca, $redis_ca . $END_PAGE_RN);
			}
		}	
		if (!sessionDuration($redis_3, $session, $redis_ind, $redis_ses)) {
			$redis_0->LPUSH(KEY_ERROR_EXECUTE, 'ERROR NO: 4004, TIME: ' . date('Y-m-d H:i:s',time()) . ', ' . $pid . '<br><br>Session Name: ' . $session . $GLOBALS['ERROR_G'] . '<br><br>');
		}
		$process_time = (int)(microtime(true) * 1E6) - $start_time;
		$redis_0->MULTI()
				->INCRBY('PerformanceConsume10', $process_time)
				->INCR('PerformanceCount10')
				->EXEC();
		Fail:
		$redis_3->DEL($session, $session.'A', $session.'B');
}

function exitStatus($rn, &$redis_3, $redis_ca, $redis_ind, $redis_ses, $flag) {
			$ret = false;
			$RS = $redis_3->HGETALL($redis_ca . $rn);
			$redis_up = $redis_ind . 'UPDATE';
			if (count($RS) === CA_TOTAL_ARRAY_LENGTH) {
				switch ($flag) {
				case 0: 
					$IsBounce = -1;
					$IsExit = -1;
					$ExitCode = $RS['ExitCode'] === '1' ? 3 : $RS['ExitCode'];
					$RS['IsBounce'] = 0;
					$RS['IsExit'] = 0;
					break;
				case 1: 
					$IsBounce = 0;
					$IsExit = 1;
					$ExitCode = 2;
					$RS['IsBounce'] = 0;
					$RS['IsExit'] = 1;
					break;
				case 2: 
					$IsBounce = 1;
					$IsExit = 1;
					$ExitCode = 1;
					break;
				}
				$IndMD5Arr = array();
				if ($flag < 2) {
					$RS['LanCountry'] = $RS['Language'] ? substr($RS['Language'],0,2) : '';
					$RS['FromType'] = (int)$RS['FromType'];
					$IsRVS = (int)$RS['TotalVisits'] > 1 ? 1 : 0;
					$IndFV = $RS['FromVal'];
					$IndFVMD5 = $RS['FromValMD5'];
					$IndSEN = $RS['SEName'];
					switch ($RS['FromType']) {
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
						break;
					case 4: 
						$IndFT = 4;
						$IndFK = $RS['FromKey'];
						$IndFKMD5 = 'D' . $RS['FromKeyMD5']; 
						break;
					case 5: 
						$IndFT = 5;
						$IndFK = $RS['FromKey'];
						$IndFKMD5 = 'E' . $RS['FromKeyMD5']; 
						break;
					}
					$redis_key = $redis_ca . $rn;
					if ((int)$RS['LUT'] === 10) {
						$redis_3->HMSET($redis_key, array('ExitCode' => $ExitCode, 'IsBounce' => $RS['IsBounce'], 'IsExit' => $RS['IsExit'], 'LUT' => 4));
					} else {
						$redis_3->HMSET($redis_key, array('ExitCode' => $ExitCode, 'IsBounce' => $RS['IsBounce'], 'IsExit' => $RS['IsExit']));
					}
					$IndMD5Arr[] = $redis_ses . $RS['VID'] . '-' . $RS['TotalVisits'];
					$IndMD5Arr[] = '00';
					$IndMD5Arr[] = '01';
					$IndMD5Arr[] = ($RS['TotalVisits'] > 1 ? '03' : '02');
					$IndMD5Arr[] = 'A' . $RS['PageMD5'];
					if ($IndFKMD5 !== '') {
						$IndMD5Arr[] = $IndFKMD5;
					}
					if ($IndFT === 4 && $IndFVMD5 !== '') {
						$IndMD5Arr[] = 'F' . $IndFVMD5;
					}
					if ($RS['BrowserName'] !== '') {
						$IndMD5Arr[] = 'G' . $RS['BrowserName'];
					}
					if ($RS['OS'] !== '') {
						$IndMD5Arr[] = 'H' . $RS['OS'];
					}
					if ($RS['BrowserCore'] !== '') {
						$IndMD5Arr[] = 'I' . $RS['BrowserCore'];
					}
					if ($RS['Device'] !== '') {
						$IndMD5Arr[] = 'J' . $RS['Device'];
					}
						$IndMD5Arr[] = 'K' . $RS['ScreenWidth'] . 'x' . $RS['ScreenHeight'];
					if ($RS['Country'] !== '') {
						$IndMD5Arr[] = 'L' . $RS['CountryMD5'];
					}
					if ($RS['Region'] !== '') {
						$IndMD5Arr[] = 'M' . $RS['RegionMD5'];
					}
					if ($RS['City'] !== '') {
						$IndMD5Arr[] = 'N' . $RS['CityMD5'];
					}
					if ($IndSEN !== '') {
						$IndMD5Arr[] = 'O' . $IndSEN;
					}
					if ($IndFT === 5 && $IndFVMD5 !== '') {
						$IndMD5Arr[] = 'P' . $IndFVMD5;
					}
					if ($RS['TotalVisits'] > 1) {
						$TOS = date('Z') * 1E6;
						$ACTIVATE_DAYS = floor(($RS['RecordNo'] + $TOS) / 864E8) - floor(($RS['LastVisitTime'] + $TOS) / 864E8);
						if ($ACTIVATE_DAYS === 0 && $RS['Visits'] === 1 && $RS['IsNVS'] === 1) {
							$IndMD5Arr[] = 'QNewVisitor';
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
									$IndMD5Arr[] = 'Q' . $key;
									break;
								}
							}
						}
					} else {
						$IndMD5Arr[] = 'QNewVisitor';
					}
					if ($RS['Language'] !== '') {
						$IndMD5Arr[] = 'S' . $RS['Language'];
					}
					if ($RS['LanCountry'] !== '') {
						$IndMD5Arr[] = 'T' . $RS['LanCountry'];
					}
					$IndMD5Arr[] = 'U' . date('H', (int)($RS['RecordNo'] / 1E6));
					$IndMD5Arr[] = 'V' . ($RS['ClientTime'] < 10 ? '0' . $RS['ClientTime'] : $RS['ClientTime']);
					$IndMD5Arr[] = 'a' . $RS['PDMD5'];
					$IndMD5Arr[] = 'b' . $RS['BrowserName'] . $RS['BrowserVersionN'];
					$IndMD5Arr[] = 'c' . $RS['OS'] . $RS['OSCodename'];
					if ($RS['UTMSource'] !== '') {
						$IndMD5Arr[] = 'd' . $RS['UTMSource'];
					}
					if ($RS['UTMMedium'] !== '') {
						$IndMD5Arr[] = 'e' . $RS['UTMMedium'];
					}
					if ($RS['UTMTerm'] !== '') {
						$IndMD5Arr[] = 'f' . $RS['UTMTerm'];
					}
					if ($RS['UTMContent'] !== '') {
						$IndMD5Arr[] = 'g' . $RS['UTMContent'];
					}
					if ($RS['UTMCampaign'] !== '') {
						$IndMD5Arr[] = 'h' . $RS['UTMCampaign'];
					}
				}
				if ($flag > 0) {
					static $PAGE_DURATION_SECOND = array(
										'0-10s'=>1E4,
										'11-30s'=>3E4,
										'31-60s'=>6E4,
										'1-2 min'=>12E4,
										'2-3 min'=>18E4,
										'3-5 min'=>3E5,
										'5-10 min'=>6E5,
										'10-15 min'=>9E5,
										'15-30 min'=>18E5,
										'30+ min'=>MAX_INT
										);
					foreach ($PAGE_DURATION_SECOND as $key=>$val) {
						if ($RS['OnlineSecond'] <= $val) { 
							$IndMD5Arr[] = 'W' . $key ;
							break;
						}
					}
				}
				$len = count($IndMD5Arr);
				if ($len > 0) {
					$redis_3->PIPELINE();
					for ($i = 0; $i < $len; $i++) {
						$redis_key = $i === 0 && $flag < 2 ? $IndMD5Arr[$i] : $redis_ind . $IndMD5Arr[$i];
						$redis_3->HINCRBY($redis_key, 'Bounces', $IsBounce);
						$redis_3->HINCRBY($redis_key, 'Exits', $IsExit);
						$redis_3->HSET($redis_key, 'LUT', 3);
						if ($i > 0 && $flag < 2 OR $flag === 2) $redis_3->SADD($redis_up, $redis_key);
					}
					$redis_3->EXEC();
				}
				$ret = true;
			} else {
				$GLOBALS['ERROR_G'] = '<br>Redis Name: ' . $redis_ca . $rn . export_array($RS);
			}
			return $ret;
}

function viewStatus($rn, &$redis_2, &$redis_3, $redis_ca, $redis_ind, $redis_ses, $flag) {
			$ret = false;
			$RS = $redis_3->HGETALL($redis_ca . $rn);
			$redis_up = $redis_ind . 'UPDATE';
			if (count($RS) === CA_TOTAL_ARRAY_LENGTH) {
				$RS['LanCountry'] = $RS['Language'] ? substr($RS['Language'],0,2) : '';
				$RS['FromType'] = (int)$RS['FromType'];
				$RS['EntryCode'] = (int)$RS['EntryCode'];
				$ReadySec = (int)$RS['ReadySecond'];
				$ReadyTimes = $ReadySec ? 1 : 0;
				$LoadSec = (int)$RS['LoadSecond'];
				$LoadTimes = $LoadSec ? 1 : 0;
				$DelaySec = (int)$RS['DelaySecond'];
				$DelayTimes = $DelaySec ? 1 : 0;
				$OnlineSec = (int)$RS['OnlineSecond'];
				$OnlineTimes = $OnlineSec ? 1 : 0;
				$MRX = (int)$RS['MaxReadX'];
				$MRY = (int)$RS['MaxReadY'];
				if ($MRX && $MRY && $OnlineTimes) {
					$MRTimes = 1;
				} else {
					$MRTimes = 0;
					$MRX = 0;
					$MRY = 0;
				}
				$IsDR = 0;
				$IsSE = 0;
				$IsRF = 0;
				$IndFV = $RS['FromVal'];
				$IndFVMD5 = $RS['FromValMD5'];
				$IndSEN = $RS['SEName'];
				switch ($RS['FromType']) {
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
					break;
				case 4: 
					$IndFT = 4;
					$IndFK = $RS['FromKey'];
					$IndFKMD5 = 'D' . $RS['FromKeyMD5']; 
					break;
				case 5: 
					$IndFT = 5;
					$IndFK = $RS['FromKey'];
					$IndFKMD5 = 'E' . $RS['FromKeyMD5']; 
					break;
				}
				if ($RS['IsNVS'] == 1) {
					switch ($RS['EntryCode']) {
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
				}
				$IsPV  = 1; 
				$IsUV  = (int)$RS['IsUV'];
				$IsUPV = (int)$RS['IsUPV'];
				$IsRVS = (int)$RS['IsRVS'];
				$IsNVS = (int)$RS['IsNVS'];
				$IsRV  = (int)$RS['IsRV'];
				$IsNV  = (int)$RS['IsNV'];
				$IsBounce = (int)$RS['IsBounce'];
				$IsExit   = (int)$RS['IsExit'];
				$RS['Clicks']		= (int)$RS['Clicks'];
				$RS['ValidClicks']  = (int)$RS['ValidClicks'];
				$redis_key = $redis_ca . $rn;
				if ($flag === 1) {
					$redis_3->HMSET($redis_key, array('ExitCode' => 4, 'LUT' => 2));
				} else {
					$redis_3->HMSET($redis_key, array('LUT' => 2));
				}
				$redis_3->SADD($redis_ca, $redis_key);
				$IndMD5Arr = array();
				$IndMD5Arr[] = $redis_ses . $RS['VID'] . '-' . $RS['TotalVisits'];
				$IndMD5Arr[] = '00';
				$IndMD5Arr[] = '01';
				$IndMD5Arr[] = ($RS['TotalVisits'] > 1 ? '03' : '02');
					$IndMD5Arr[] = 'A' . $RS['PageMD5'];
				if ($IndFKMD5 !== '') {
					$IndMD5Arr[] = $IndFKMD5; 
				}
				if ($IndFT === 4 && $IndFVMD5 !== '') {
					$IndMD5Arr[] = 'F' . $IndFVMD5;
				}
				if ($RS['BrowserName'] !== '') {
					$IndMD5Arr[] = 'G' . $RS['BrowserName'];
				}
				if ($RS['OS'] !== '') {
					$IndMD5Arr[] = 'H' . $RS['OS'];
				}
				if ($RS['BrowserCore'] !== '') {
					$IndMD5Arr[] = 'I' . $RS['BrowserCore'];
				}
				if ($RS['Device'] !== '') {
					$IndMD5Arr[] = 'J' . $RS['Device'];
				}
					$IndMD5Arr[] = 'K' . $RS['ScreenWidth'] . 'x' . $RS['ScreenHeight'];
				if ($RS['Country'] !== '') {
					$IndMD5Arr[] = 'L' . $RS['CountryMD5'];
				}
				if ($RS['Region'] !== '') {
					$IndMD5Arr[] = 'M' . $RS['RegionMD5'];
				}
				if ($RS['City'] !== '') {
					$IndMD5Arr[] = 'N' . $RS['CityMD5'];
				}
				if ($IndSEN !== '') {
					$IndMD5Arr[] = 'O' . $IndSEN;
				}
				if ($IndFT === 5 && $IndFVMD5 !== '') {
					$IndMD5Arr[] = 'P' . $IndFVMD5;
				}
				if ($RS['TotalVisits'] > 1) {
					$TOS = date('Z') * 1E6;
					$ACTIVATE_DAYS = floor(($RS['RecordNo'] + $TOS) / 864E8) - floor(($RS['LastVisitTime'] + $TOS) / 864E8);
					if ($ACTIVATE_DAYS === 0 && $RS['Visits'] === 1 && $RS['IsNVS'] === 1) {
						$IndMD5Arr[] = 'QNewVisitor';
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
								$IndMD5Arr[] = 'Q' . $key;
								break;
							}
						}	
					}
				} else {
					$IndMD5Arr[] = 'QNewVisitor';
				}
				if ($RS['Language'] !== '') {
					$IndMD5Arr[] = 'S' . $RS['Language'];
				}
				if ($RS['LanCountry'] !== '') {
					$IndMD5Arr[] = 'T' . $RS['LanCountry'];
				}
				$IndMD5Arr[] = 'U' . date('H', (int)($RS['RecordNo'] / 1E6));
				$IndMD5Arr[] = 'V' . ($RS['ClientTime'] < 10 ? '0' . $RS['ClientTime'] : $RS['ClientTime']);
				$IndMD5Arr[] = 'a' . $RS['PDMD5'];
				$IndMD5Arr[] = 'b' . $RS['BrowserName'] . $RS['BrowserVersionN'];
				$IndMD5Arr[] = 'c' . $RS['OS'] . $RS['OSCodename'];
				if ($RS['UTMSource'] !== '') {
					$IndMD5Arr[] = 'd' . $RS['UTMSource'];
				}
				if ($RS['UTMMedium'] !== '') {
					$IndMD5Arr[] = 'e' . $RS['UTMMedium'];
				}
				if ($RS['UTMTerm'] !== '') {
					$IndMD5Arr[] = 'f' . $RS['UTMTerm'];
				}
				if ($RS['UTMContent'] !== '') {
					$IndMD5Arr[] = 'g' . $RS['UTMContent'];
				}
				if ($RS['UTMCampaign'] !== '') {
					$IndMD5Arr[] = 'h' . $RS['UTMCampaign'];
				}
				$len = count($IndMD5Arr);
				if ($len > 0) {
					$redis_3->PIPELINE();
					for ($i = 0; $i < $len; $i++) {
						$redis_key = $i === 0 ? $IndMD5Arr[$i] : $redis_ind . $IndMD5Arr[$i];
						$redis_3->HINCRBY($redis_key, 'TotalDelay', $DelaySec);
						$redis_3->HINCRBY($redis_key, 'DelayTimes', $DelayTimes);
						$redis_3->HINCRBY($redis_key, 'TotalReady', $ReadySec);
						$redis_3->HINCRBY($redis_key, 'ReadyTimes', $ReadyTimes);
						$redis_3->HINCRBY($redis_key, 'TotalLoad', $LoadSec);
						$redis_3->HINCRBY($redis_key, 'LoadTimes', $LoadTimes);
						$redis_3->HINCRBY($redis_key, 'TotalOnline', $OnlineSec);
						$redis_3->HINCRBY($redis_key, 'OnlineTimes', $OnlineTimes);
						$redis_3->HINCRBY($redis_key, 'MaxReadX', $MRX);
						$redis_3->HINCRBY($redis_key, 'MaxReadY', $MRY);
						$redis_3->HINCRBY($redis_key, 'MRTimes', $MRTimes);
						$redis_3->HINCRBY($redis_key, 'Clicks', $RS['Clicks']);
						$redis_3->HINCRBY($redis_key, 'ValidClicks', $RS['ValidClicks']);
						$redis_3->HSET($redis_key, 'LUT', 3);
						if ($i > 0) $redis_3->SADD($redis_up, $redis_key);
					}
					$redis_3->EXEC();
				}
				static $PAGE_DURATION_SECOND = array(
									'0-10s'=>1E4,
									'11-30s'=>3E4,
									'31-60s'=>6E4,
									'1-2 min'=>12E4,
									'2-3 min'=>18E4,
									'3-5 min'=>3E5,
									'5-10 min'=>6E5,
									'10-15 min'=>9E5,
									'15-30 min'=>18E5,
									'30+ min'=>MAX_INT
									);
				foreach ($PAGE_DURATION_SECOND as $key=>$val) {
					if ($OnlineSec <= $val) {
						$IndMD5 = 'W' . $key;
						break;
					}
				}
				$redis_key = $redis_ind . $IndMD5;
				if ($redis_3->HLEN($redis_key) !== 31) {
						$IndData = array(
								'MD5' => $IndMD5, 
								'Type' => 23, 
								'PV' => $IsPV, 
								'UV' => $IsUV, 
								'UPV' => $IsUPV,
								'RVS' => $IsRVS, 
								'NV' => $IsNV, 
								'RV' => $IsRV, 
								'Visits' => $IsNVS, 
								'Bounces' => 0,
								'Exits' => 0,
								'DREntry' => $IsDR, 
								'SEEntry' => $IsSE, 
								'RFEntry' => $IsRF, 
								'TotalDelay' => $DelaySec, 
								'DelayTimes' => $DelayTimes, 
								'TotalReady' => $ReadySec, 
								'ReadyTimes' => $ReadyTimes, 
								'TotalLoad' => $LoadSec, 
								'LoadTimes' => $LoadTimes, 
								'TotalOnline' => $OnlineSec, 
								'OnlineTimes' => $OnlineTimes, 
								'MaxReadX' => $MRX, 
								'MaxReadY' => $MRY,
								'MRTimes' => $MRTimes, 
								'Clicks' => $RS['Clicks'], 
								'ValidClicks' => $RS['ValidClicks'], 
								'Detail' => $key, 
								'Extra' => '',
								'ExtraMD5' => '',
								'LUT' => 1
								);
						$redis_3->HMSET($redis_key, $IndData);
						$redis_3->SADD($redis_up, $redis_key);
				} else {
						$redis_3->PIPELINE();
						$redis_3->HINCRBY($redis_key, 'PV', $IsPV);
						$redis_3->HINCRBY($redis_key, 'UV', $IsUV);
						$redis_3->HINCRBY($redis_key, 'UPV', $IsUPV);
						$redis_3->HINCRBY($redis_key, 'RVS', $IsRVS);
						$redis_3->HINCRBY($redis_key, 'NV', $IsNV);
						$redis_3->HINCRBY($redis_key, 'RV', $IsRV);
						$redis_3->HINCRBY($redis_key, 'Visits', $IsNVS);
						$redis_3->HINCRBY($redis_key, 'DREntry', $IsDR);
						$redis_3->HINCRBY($redis_key, 'SEEntry', $IsSE);
						$redis_3->HINCRBY($redis_key, 'RFEntry', $IsRF);
						$redis_3->HINCRBY($redis_key, 'TotalDelay', $DelaySec);
						$redis_3->HINCRBY($redis_key, 'DelayTimes', $DelayTimes);
						$redis_3->HINCRBY($redis_key, 'TotalReady', $ReadySec);
						$redis_3->HINCRBY($redis_key, 'ReadyTimes', $ReadyTimes);
						$redis_3->HINCRBY($redis_key, 'TotalLoad', $LoadSec);
						$redis_3->HINCRBY($redis_key, 'LoadTimes', $LoadTimes);
						$redis_3->HINCRBY($redis_key, 'TotalOnline', $OnlineSec);
						$redis_3->HINCRBY($redis_key, 'OnlineTimes', $OnlineTimes);
						$redis_3->HINCRBY($redis_key, 'MaxReadX', $MRX);
						$redis_3->HINCRBY($redis_key, 'MaxReadY', $MRY);
						$redis_3->HINCRBY($redis_key, 'MRTimes', $MRTimes);
						$redis_3->HINCRBY($redis_key, 'Clicks', $RS['Clicks']);
						$redis_3->HINCRBY($redis_key, 'ValidClicks', $RS['ValidClicks']);
						$redis_3->HSET($redis_key, 'LUT', 3);
						$redis_3->SADD($redis_up, $redis_key);
						$redis_3->EXEC();
				}
				$RS['Plugin'] === 'Shockwave Flash' && $RS['Step'] < 5 && $RS['OnlineSecond'] < 7000 && $RS['IP'] AND filter_robot_ip($redis_2, $RS['IP']); 
				$ret = true;
			} else {
				$GLOBALS['ERROR_G'] = '<br>Redis Name: ' . $redis_ca . $rn . export_array($RS);
			}
			return $ret;
}

function sessionDuration(&$redis_3, $session, $redis_ind, $redis_ses) {
		$ret = false;
		$VID = substr($session, 1, 16);
		$TVS = substr($session, 18);
		$redis_ses .= $VID . '-' . $TVS;
		$redis_up = $redis_ind . 'UPDATE';
		$RS = $redis_3->HGETALL($redis_ses);
		if (count($RS) === 31) {
			$IndMD5Arr = array();
			static $SESSION_DURATION_SECOND = array(
									'0-10s'=>1E4,
									'11-30s'=>3E4,
									'31-60s'=>6E4,
									'1-2 min'=>12E4,
									'2-3 min'=>18E4,
									'3-5 min'=>3E5,
									'5-10 min'=>6E5,
									'10-15 min'=>9E5,
									'15-30 min'=>18E5,
									'30+ min'=>MAX_INT
									);
			foreach ($SESSION_DURATION_SECOND as $key=>$val) {
				if ((int)$RS['TotalOnline'] <= $val) {
					$IndMD5Arr[] = array('X'.$key, 24, $key);
					break;
				}
			}
			static $PAGES_PER_SESSION = array(
								'1 Page'=>1,
								'2 Pages'=>2,
								'3 Pages'=>3,
								'4 Pages'=>4,
								'5 Pages'=>5,
								'6-7 Pages'=>7,
								'8-10 Pages'=>10,
								'11-15 Pages'=>15,
								'16-20 Pages'=>20,
								'21-30 Pages'=>30,
								'31-50 Pages'=>50,
								'51-100 Pages'=>100,
								'100+ Pages'=>MAX_INT
								);
			foreach ($PAGES_PER_SESSION as $key=>$val) {
				if ((int)$RS['PV'] <= $val) {
					$IndMD5Arr[] = array('k'.$key, 37, $key);
					break;
				}
			}
			$len = count($IndMD5Arr);
			for ($i = 0; $i < $len; $i++) {
				$redis_key = $redis_ind . $IndMD5Arr[$i][0];
				if ($redis_3->HLEN($redis_key) !== 31) {
					$IndData = array(
							'MD5' => $IndMD5Arr[$i][0],
							'Type' => $IndMD5Arr[$i][1],
							'PV' => $RS['PV'],
							'UV' => $RS['UV'],
							'UPV' => $RS['UPV'],
							'RVS' => $RS['RVS'],
							'NV' => $RS['NV'],
							'RV' => $RS['RV'],
							'Visits' => 1,
							'Bounces' => $RS['Bounces'],
							'Exits' => 1,
							'DREntry' => $RS['DREntry'],
							'SEEntry' => $RS['SEEntry'],
							'RFEntry' => $RS['RFEntry'],
							'TotalDelay' => $RS['TotalDelay'],
							'DelayTimes' => $RS['DelayTimes'],
							'TotalReady' => $RS['TotalReady'],
							'ReadyTimes' => $RS['ReadyTimes'],
							'TotalLoad' => $RS['TotalLoad'],
							'LoadTimes' => $RS['LoadTimes'],
							'TotalOnline' => $RS['TotalOnline'],
							'OnlineTimes' => $RS['OnlineTimes'],
							'MaxReadX' => $RS['MaxReadX'],
							'MaxReadY' => $RS['MaxReadY'],
							'MRTimes' => $RS['MRTimes'],
							'Detail' => $IndMD5Arr[$i][2],
							'Clicks' => $RS['Clicks'],
							'ValidClicks' => $RS['ValidClicks'],
							'Extra' => '',
							'ExtraMD5' => '',
							'LUT' => 1
							);
					$redis_3->HMSET($redis_key, $IndData);
					$redis_3->SADD($redis_up, $redis_key);
				} else {
					$redis_3->PIPELINE();
					$redis_3->HINCRBY($redis_key, 'PV', $RS['PV']);
					$redis_3->HINCRBY($redis_key, 'UV', $RS['UV']);
					$redis_3->HINCRBY($redis_key, 'UPV', $RS['UPV']);
					$redis_3->HINCRBY($redis_key, 'RVS', $RS['RVS']);
					$redis_3->HINCRBY($redis_key, 'NV', $RS['NV']);
					$redis_3->HINCRBY($redis_key, 'RV', $RS['RV']);
					$redis_3->HINCRBY($redis_key, 'Visits', $RS['Visits']);
					$redis_3->HINCRBY($redis_key, 'Bounces', $RS['Bounces']); 
					$redis_3->HINCRBY($redis_key, 'Exits', $RS['Exits']);
					$redis_3->HINCRBY($redis_key, 'DREntry', $RS['DREntry']);
					$redis_3->HINCRBY($redis_key, 'SEEntry', $RS['SEEntry']);
					$redis_3->HINCRBY($redis_key, 'RFEntry', $RS['RFEntry']);
					$redis_3->HINCRBY($redis_key, 'TotalDelay', $RS['TotalDelay']);
					$redis_3->HINCRBY($redis_key, 'DelayTimes', $RS['DelayTimes']);
					$redis_3->HINCRBY($redis_key, 'TotalReady', $RS['TotalReady']);
					$redis_3->HINCRBY($redis_key, 'ReadyTimes', $RS['ReadyTimes']);
					$redis_3->HINCRBY($redis_key, 'TotalLoad', $RS['TotalLoad']);
					$redis_3->HINCRBY($redis_key, 'LoadTimes', $RS['LoadTimes']);
					$redis_3->HINCRBY($redis_key, 'TotalOnline', $RS['TotalOnline']);
					$redis_3->HINCRBY($redis_key, 'OnlineTimes', $RS['OnlineTimes']);
					$redis_3->HINCRBY($redis_key, 'MaxReadX', $RS['MaxReadX']);
					$redis_3->HINCRBY($redis_key, 'MaxReadY', $RS['MaxReadY']);
					$redis_3->HINCRBY($redis_key, 'MRTimes', $RS['MRTimes']);
					$redis_3->HINCRBY($redis_key, 'Clicks', $RS['Clicks']);
					$redis_3->HINCRBY($redis_key, 'ValidClicks', $RS['ValidClicks']);
					$redis_3->HSET($redis_key, 'LUT', 3);
					$redis_3->SADD($redis_up, $redis_key);
					$redis_3->EXEC();
				}
			}
			$redis_3->DEL($redis_ses);
			$ret = true;
		} else {
			$GLOBALS['ERROR_G'] = '<br>Redis Name: ' . $redis_ses . export_array($RS);
		}
		return $ret;
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

function create_table(&$con, $tbname, $tbrows) {
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $tbname . $tbrows . ' ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
		if (!mysqli_query($con, $sql)) {
			$GLOBALS['ERROR_G'] .= 'OP: 0, Create table failed. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
			return false;
		}
		return true;
}

function record_data(&$con, &$sql, $op, $row, &$Error) {
		switch ($op) {
		case 1:
			if (!mysqli_query($con, $sql)) {
				$Error .= 'Op: 1, Insert record failed. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
				return false;
			} 
			break;
		case 2:
			if (!mysqli_query($con, $sql)) {
				$Error .= 'Op: 2, Update record failed. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
				return false;
			} else if (mysqli_affected_rows($con) !== $row) {
				$Error .= 'Op: 2, Update record OK! But effected rows are not correct. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
				return false;
			}
			break;
		case 3:
			if (!mysqli_query($con, $sql)) {
				$Error .= 'Op: 3, Update record failed. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
				return false;
			}
			break;
		case 4:
			if (!mysqli_query($con, $sql)) {
				$Error .= 'Op: 4, Replace record failed. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
				return false;
			} else if (mysqli_affected_rows($con) == 0) {
				$Error .= 'Op: 4, Replace record OK! But no effected rows. <br><br>Error ' . mysqli_errno($con) . ': ' . mysqli_error($con) . '<br><br>Sql: ' . $sql . '<br><br>';
				return false;
			}
			break;
		case 5:
			$result = mysqli_query($con, $sql);
			if ($result && mysqli_num_rows($result)) {
				mysqli_free_result($result);
				return true;
			}
			return false;
		}
		return true;
}

function SDATA(&$con, &$request, $k, $opt, $def, $maxL=0, $minL=0, $decode=0, $charset='UTF-8') {
		$val = '';
		$key = '&' . $k . '=';
		$spos = strrpos($request, $key);
		if ($spos !== false) {
			$spos += strlen($key);
			$epos = strpos($request, '&', $spos);
			if ($epos === false) $epos = strlen($request);
			if ($epos > $spos) $val = substr($request, $spos, ($epos - $spos));
		}
		if ($val === '') {
			if ($def === 'EXIT') {
				return false;
			} else {
				return $def;
			}
		}
		if ($opt === 1) $val = $k === 'kw' ? urldecode(urldecode($val)) : rawurldecode($val);
		if ($decode === 1) {
			$encoding = $charset !== 'UTF-8' ? 'UTF-8, ' . $charset . ', GB18030' : 'UTF-8, GB18030'; 
			$encoding = mb_detect_encoding($val, $encoding, true);
			if ('UTF-8' !== $encoding) {
				if (false === $encoding) {
					$val = mb_convert_encoding($val, 'UTF-8');
				} else {
					$val = mb_convert_encoding($val, 'UTF-8', $encoding);
				}
			}
		}
		switch ($opt) {
		case 1:
			$val = filter_var($val, FILTER_SANITIZE_STRING);
			if (mb_strlen($val, 'UTF-8') > $maxL) $val = mb_substr($val, 0, $maxL, 'UTF-8');
			return mysqli_real_escape_string($con, $val);
		case 2:
			$tmp = (int)$val;
			return ($tmp > $maxL || $tmp < $minL) ? $def : $tmp;
		case 3:
			$mval = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION + FILTER_FLAG_ALLOW_THOUSAND);
			if (strlen($mval) != strlen($val)) {
				return '';
			} else {
				if (strlen($val) > $maxL) {
					return '';
				} else {
					return $val;
				}
			}
		case 4:
			$tmp = (int)$val;
			return ($tmp > $maxL || $tmp < $minL) ? false : $tmp;
		case 5:
			$val = (int)$val;
			return ($val < 0 ? 0 : $val);
		case 6:
			$val = (float)$val;
			return ($val < 0 ? 0 : $val);
		case 7:
			return abs((int)$val);
		case 8:
			return abs((float)$val);
		case 9:
			return ((int)$val === 0 ? 0 : 1);
		}
}

function smd5($str) {
		return substr(md5($str),8,16);  
}

function tmd5($str) {
		return substr(md5($str),8,8); 
}

function check_blocked_id(&$redis, $sid, $vid, $stp) {
		$md5 = md5($vid);
		if ($redis->SISMEMBER($sid.'-6', $md5) === true) {
			if ($stp === 1) {
				$redis->ZADD($sid.'BlockedTime', time(), $vid);
				$redis->ZINCRBY($sid.'BlockedCount', 1, $vid);
			}
			return 0; 
		}
		return 1;
}

function record_robot_ip(&$redis, $ip, $rbn) {
		$redis->SADD('NewRobots', $ip);
		$redis->SET('RB'.$ip, $rbn);
		$ip = substr($ip, 0, strrpos($ip, '.'));
		if ($redis->SISMEMBER('FilterRobots', $ip) === true) {
			if ($redis->ZINCRBY('FilterRobotCount', 1, $ip) > 19) {
				$redis->PIPELINE();
				$redis->SADD('NewRobots', $ip);
				$redis->SET('RB'.$ip, $rbn);
				$redis->ZREM('FilterRobotCount', $ip);
				$redis->SREM('FilterRobots', $ip);
				$redis->EXEC();
			}
		} else {
			$redis->SADD('FilterRobots', $ip);
		}
}

function check_robot_ip(&$redis, $ip, $rbn) {
		if ($redis->SISMEMBER('Robots', $ip) === true) { $rbn = $redis->GET('RB'.$ip); return 1; } 
		if ($redis->SISMEMBER('NewRobots', $ip) === true) { $rbn = $redis->GET('RB'.$ip); return 1; } 
		$ip = substr($ip, 0, strrpos($ip, '.'));
		if ($redis->SISMEMBER('Robots', $ip) === true) { $rbn = $redis->GET('RB'.$ip); return 1; } 
		if ($redis->SISMEMBER('NewRobots', $ip) === true) { $rbn = $redis->GET('RB'.$ip); return 1; } 
		return 0;
}

function filter_robot_ip(&$redis, $ip) {
		if ($redis->SISMEMBER('FilterRobots', $ip) === true) {
			if ($redis->ZINCRBY('FilterRobotCount', 1, $ip) > 4) {
				$redis->PIPELINE();
				$redis->SADD('NewRobots', $ip);
				$redis->SET('RB'.$ip, 'Unknown Spider');
				$redis->ZREM('FilterRobotCount', $ip);
				$redis->SREM('FilterRobots', $ip);
				$redis->EXEC();
			}
		} else {
			$redis->SADD('FilterRobots', $ip);
			$ip = substr($ip, 0, strrpos($ip, '.'));
			if ($redis->SISMEMBER('FilterRobots', $ip) === true) {
				if ($redis->ZINCRBY('FilterRobotCount', 1, $ip) > 19) {
					$redis->PIPELINE();
					$redis->SADD('NewRobots', $ip);
					$redis->SET('RB'.$ip, 'Unknown Spider');
					$redis->ZREM('FilterRobotCount', $ip);
					$redis->SREM('FilterRobots', $ip);
					$redis->EXEC();
				}
			} else {
				$redis->SADD('FilterRobots', $ip);
			}
		}	
}

function get_server_info(&$redis) {
	$fp = popen('free -m | grep -E "^(Mem:)"',"r");
	$rs = fread($fp, 1024);
	pclose($fp);
	if ($rs) {
		$rs = preg_replace("/\s(?=\s)/", "\\1", $rs);
		$tmp = explode(' ', $rs);
		if (count($tmp) > 3) {
			$redis->PIPELINE();
			$redis->SET('TotalMemory', $tmp[1]);
			$redis->SET('UsedMemory',  $tmp[2]);
			$redis->SET('FreeMemory',  $tmp[3]);
			$redis->EXEC();	
		}
	}

	$fp = popen('df -BG | grep -E "^(/)"',"r");
	$rs = fread($fp, 1024);
	pclose($fp);
	if ($rs) {
		$rs = preg_replace("/\s(?=\s)/", "\\1", $rs);
		$tmp = explode(' ', $rs);
		$n = (int)(count($tmp) / 6);
		if ($n > 0) {
			$total = 0;
			$used = 0;
			for ($i=0; $i<$n; $i++) {
				$t = $i * 6 + 1;
				$total += (int)$tmp[$t];
				$t = $i * 6 + 2;
				$used += (int)$tmp[$t];
			}
			$redis->PIPELINE();
			$redis->SET('TotalDisk', $total);
			$redis->SET('UsedDisk',  $used);
			$redis->EXEC();
		}
	}
}

function export_array(&$Arr) {
	$ret = '<br>Array Length: ' . count($Arr) . '<br><br>';
	foreach ($Arr as $key=>$val) {
		$ret .= $key . '=>' . $val . '<br>';
	}
	return $ret;
}

function autoresponse($from, $to, $subject, $errmsg) {
	date_default_timezone_set(ADMIN_TIMEZONE);
	$message = "
	<html>
	<head>
	<title>" . $subject . "</title>
	</head>
	<body>
	<div style='font-family: Microsoft Yahei,Arial,Verdana; font-size:13px;'>
	<p>
	Hi,<br/><br/>
	YOU HAVE GOT A SERIOUSE PROBLEM FROM HOST : <i>". HOST_NAME . "</i>.<br/><br/>
	" . $errmsg . ".<br/><br/><br/><br/>".
	date('Y-m-d H:i:s',time()) . ', ' . ADMIN_TIMEZONE . "<br><br>
	Regards,<br/>
	WM Jonssen<br/><br/>
	This is an auto-response mail. Please do not reply.<br/>
	</p>
	</div>
	</body>
	</html>
	";
	$headers =  "MIME-Version: 1.0\r\n".
				"Content-type: text/html; charset=utf-8\r\n".
				"From: <" . $from . ">";
	return mail($to, $subject, $message, $headers);
}


?> 
