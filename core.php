<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Core PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/24/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved. *
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

ignore_user_abort(1); 
$START_TIME = $_SERVER['REQUEST_TIME_FLOAT'];
empty($START_TIME) && $START_TIME = microtime(true);
$TIMESTAMP = (int)$START_TIME;
$START_TIME = (int)($START_TIME * 1E6);
if (isset($_GET['rn']) === true) ((int)$_GET['rn'] > 15E14 && (int)$_GET['rn'] < ($START_TIME - 36E8)) AND exit;

@require './config/config_redis.php';

$REDIS_2 = new Redis();
if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit;
$REDIS_2->SELECT(REDIS_DB_2);
$IP = '';
foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
	if (array_key_exists($key, $_SERVER)) {
		foreach (explode(',', $_SERVER[$key]) as $val) {
			if ((bool)filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				$IP = $val;
				goto skip;
			}
		}
	}
}
skip:
if ($REDIS_2->SISMEMBER('NewRobots', $IP) === true) goto SkipAttackCheck;
$IPB = substr($IP, 0, strrpos($IP, '.'));
if ($REDIS_2->SISMEMBER('NewRobots', $IPB) === true) goto SkipAttackCheck;
foreach (array('stp','stat','sid','vid','rn','ipdb','ls','tvs') as $key) isset($_GET[$key]) === false AND Record_BadRequest($REDIS_2, $IP, $TIMESTAMP, 1);
((int)$_GET['rn'] > $START_TIME || (int)$_GET['rn'] < 15E14 || (int)$_GET['vid'] > $START_TIME || (int)$_GET['vid'] < 1E15 || (int)$_GET['sid'] > 2E15 ||  (int)$_GET['sid'] < 15E14 || empty($_GET['tz'])) AND Record_BadRequest($REDIS_2, $IP, $TIMESTAMP, 2);
SkipAttackCheck:
$REDIS_2->GET($_GET['sid'].'-SiteStatus') !== '0' AND Record_BadRequest($REDIS_2, $IP, $TIMESTAMP, 3);
$REDIS_0 = new Redis();
if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) exit;
$REDIS_0->SELECT(REDIS_DB_0);
$RC = $_GET['rn'];
$RC_EXIST = (bool)$REDIS_0->EXISTS($RC);
if ($REDIS_0->RPUSH($RC, '&' . $_SERVER['QUERY_STRING'] . '&ip=' . $IP . '&ts=' . $START_TIME) > 0) {
	$RC_EXIST === false AND $REDIS_0->RPUSH('TicketListL', $RC);
	if ($_GET['stat'] === '1' || $_GET['stat'] === '9') {
		header('Content-type: application/javascript');
		echo "var _caq = _caq || [];_caq.push(['_responseStatusCA',0]);";
	}
	$REDIS_0->ZINCRBY('SSIDS', 1, $_GET['sid']);
}
$REDIS_1 = new Redis();
if ($REDIS_1->CONNECT(REDIS_IP_1, REDIS_PORT_1) !== true) exit;
$REDIS_1->SELECT(REDIS_DB_1);
$EXPIRE = 900;
switch ((int)$_GET['stat']) {
case 1:
case 9:
	if (!date_default_timezone_set($_GET['tz'])) exit;
	$DAY_PERIOD_NOW = date('ymd', $TIMESTAMP);
	$DAY_PERIOD = $REDIS_1->GET('DayPeriod'.$_GET['sid']);
	if ($DAY_PERIOD_NOW !== $DAY_PERIOD) {
		$REDIS_1->MSET(array('PeakPV'.$_GET['sid'] => 1, 'RealPV'.$_GET['sid'] => 1, 'PeakUV'.$_GET['sid'] => 1, 'RealUV'.$_GET['sid'] => 1, 'PeakIP'.$_GET['sid'] => 1, 'RealIP'.$_GET['sid'] => 1, 'DayPeriod'.$_GET['sid'] => $DAY_PERIOD_NOW));
	}
	$REAL_MINUTE = date('H:i', $TIMESTAMP);
	$RANG_MINUTE = floor(($TIMESTAMP - 840) / 60) * 60;
	$LAST_MINUTE = $REDIS_1->GETSET('MinPeriod'.$_GET['sid'], $REAL_MINUTE);
	if ($LAST_MINUTE != $REAL_MINUTE) {
		$CHECK_ARRAY = $REDIS_1->MGET(array('PeakPV'.$_GET['sid'], 'RealPV'.$_GET['sid'], 'PeakUV'.$_GET['sid'], 'RealUV'.$_GET['sid'], 'PeakIP'.$_GET['sid'], 'RealIP'.$_GET['sid'], 'MinData'.$_GET['sid']));
		$REDIS_ARRAY = array();
		if ($CHECK_ARRAY[0] < $CHECK_ARRAY[1]) $REDIS_ARRAY['PeakPV'.$_GET['sid']] = $CHECK_ARRAY[1];
		if ($CHECK_ARRAY[2] < $CHECK_ARRAY[3]) $REDIS_ARRAY['PeakUV'.$_GET['sid']] = $CHECK_ARRAY[3];
		if ($CHECK_ARRAY[4] < $CHECK_ARRAY[5]) $REDIS_ARRAY['PeakIP'.$_GET['sid']] = $CHECK_ARRAY[5];
		$REDIS_1->PIPELINE();
		$REDIS_1->MSET($REDIS_ARRAY);
		if ($CHECK_ARRAY[6]) $REDIS_1->ZADD('SMINS'.$_GET['sid'], (int)substr($CHECK_ARRAY[6], 0, 10), substr($CHECK_ARRAY[6], 10));
		$REDIS_1->DEL('VIDS'.$_GET['sid'],'IPS'.$_GET['sid']);
		$REDIS_1->MSET(array('RealPV'.$_GET['sid'] => 1, 'RealUV'.$_GET['sid'] => 1, 'RealIP'.$_GET['sid'] => 1, 'MinData'.$_GET['sid'] => $TIMESTAMP . $REAL_MINUTE . ',1,1,1'));
		$REDIS_1->SADD('VIDS'.$_GET['sid'], $_GET['vid']);
		$REDIS_1->SADD('IPS'.$_GET['sid'], $IP);
		$REDIS_1->EXPIRE('MinData'.$_GET['sid'], 900);
		$REDIS_1->ZREMRANGEBYSCORE('SVIDS'.$_GET['sid'], 0, ($RANG_MINUTE-60));
		$REDIS_1->ZREMRANGEBYSCORE('SMINS'.$_GET['sid'], 0, ($RANG_MINUTE-60));
		$REDIS_1->EXEC();
	} else {
		$REAL_PV = $REDIS_1->INCR('RealPV'.$_GET['sid']);
		if ($REDIS_1->SISMEMBER('VIDS'.$_GET['sid'], $_GET['vid']) === false) {
			$REAL_UV = $REDIS_1->INCR('RealUV'.$_GET['sid']);
			$REDIS_1->SADD('VIDS'.$_GET['sid'], $_GET['vid']);
		} else {
			$REAL_UV = $REDIS_1->GET('RealUV'.$_GET['sid']);
		}
		if ($REDIS_1->SISMEMBER('IPS'.$_GET['sid'], $IP) === false) {
			$REAL_IP = $REDIS_1->INCR('RealIP'.$_GET['sid']);
			$REDIS_1->SADD('IPS'.$_GET['sid'], $IP);
		} else {
			$REAL_IP = $REDIS_1->GET('RealIP'.$_GET['sid']);
		}
		$CHECK_ARRAY = $REDIS_1->GET('MinData'.$_GET['sid']);
		$CHECK_ARRAY = $CHECK_ARRAY ? substr($CHECK_ARRAY, 0, 10) : $TIMESTAMP;
		$REDIS_1->SET('MinData'.$_GET['sid'], $CHECK_ARRAY . $REAL_MINUTE . ',' . $REAL_PV . ',' . $REAL_UV . ',' . $REAL_IP);	
		$REDIS_1->EXPIRE('MinData'.$_GET['sid'], 900);
	}
case 2:
	$RET  = $REDIS_1->GET($_GET['vid']);
	$REDIS_1->PIPELINE();
	if ($RET) {
		$TMP  = '&pv=' . $_GET['pv'] .
				'&dt=' . $_GET['dt'] .
				'&ds=' . $_GET['ds'] .
				'&rs=' . $_GET['rs'];
		$TMP = MDATA($RET, '&pv=', $TMP);
		$REDIS_1->SET($_GET['vid'], $TMP);
	} else {
		$REDIS_1->ZADD('SVIDS'.$_GET['sid'], $TIMESTAMP, $_GET['vid']);	
		$REDIS_1->APPEND($_GET['vid'], 
			'stat=' . $_GET['stat'] .
			'&rn=' 	. $_GET['rn'] .
			'&vid='	. $_GET['vid'] .
			'&ip=' 	. $IP .
			'&ipdb='. $_GET['ipdb'] .
			'&pg='	. rawurlencode($_GET['pg']) .
			'&rf=' 	. (empty($_GET['rf']) ? '' : parse_url($_GET['rf'], PHP_URL_HOST)) .
			'&kw=' 	. (empty($_GET['kw']) ? '' : $_GET['kw']) .
			'&pv=' 	. $_GET['pv'] .
			'&dt=' 	. $_GET['dt'] .
			'&ds=' 	. $_GET['ds'] .
			'&rs='  . $_GET['rs']
		);
	}
	$REDIS_1->EXPIRE($_GET['vid'], $EXPIRE);
	$REDIS_1->EXEC();
	break;
case 3:
	$RET  = $REDIS_1->GET($_GET['vid']);
	$REDIS_1->PIPELINE();
	if ($RET) {		
		$TMP  = '&pv='  . $_GET['pv'] .
				'&dt='  . $_GET['dt'] .
				'&ds='  . $_GET['ds'] .
				'&rs='  . $_GET['rs'] .
				'&ls='  . $_GET['ls'] .
				'&mnrx='. $_GET['mnrx'] .
				'&mnry='. $_GET['mnry'] .
				'&mxrx='. $_GET['mxrx'] .
				'&mxry='. $_GET['mxry'];
		$TMP = MDATA($RET, '&pv=', $TMP);
		$REDIS_1->SET($_GET['vid'], $TMP);	
	} else {
		$REDIS_1->APPEND($_GET['vid'], 
			'stat='. $_GET['stat'] .
			'&rn=' 	. $_GET['rn'] .
			'&vid='	. $_GET['vid'] .
			'&ip=' 	. $IP .
			'&ipdb='. $_GET['ipdb'] .
			'&pg='	. rawurlencode($_GET['pg']) .
			'&rf=' 	. (empty($_GET['rf']) ? '' : parse_url($_GET['rf'], PHP_URL_HOST)) .
			'&kw=' 	. (empty($_GET['kw']) ? '' : $_GET['kw']) .
			'&pv=' 	. $_GET['pv'] .
			'&dt=' 	. $_GET['dt'] .
			'&ds=' 	. $_GET['ds'] .
			'&rs='  . $_GET['rs'] .
			'&ls='  . $_GET['ls'] .
			'&mnrx='. $_GET['mnrx'] .
			'&mnry='. $_GET['mnry'] .
			'&mxrx='. $_GET['mxrx'] .
			'&mxry='. $_GET['mxry']
		);
	}
	$REDIS_1->EXPIRE($_GET['vid'], $EXPIRE);
	$REDIS_1->EXEC();
	break;
case 4:
case 5:
	$REDIS_1->PIPELINE();
	$REDIS_1->DEL($_GET['vid']);
	$REDIS_1->ZREM('SVIDS'.$_GET['sid'], $_GET['vid']);
	$REDIS_1->EXEC();
	break;
case 6:
	break;
case 7:
	if ((bool)$REDIS_1->EXISTS($_GET['vid']) === false) break;
	$RET  = $REDIS_1->GET($_GET['vid']);
	$TMP  = '&ds='	. $_GET['ds'] .
			'&rs='	. $_GET['rs'] .
			'&ls='	. $_GET['ls'] .
			'&ols=' . $_GET['ols'] .
			'&mnrx='. $_GET['mnrx'] .
			'&mnry='. $_GET['mnry'] .
			'&mxrx='. $_GET['mxrx'] .
			'&mxry='. $_GET['mxry'];
	$TMP = MDATA($RET, '&ds=', $TMP);
	$REDIS_1->PIPELINE();
	$REDIS_1->SET($_GET['vid'], $TMP);
	$REDIS_1->EXPIRE($_GET['vid'], $EXPIRE);
	$REDIS_1->ZADD('SVIDS'.$_GET['sid'], $TIMESTAMP, $_GET['vid']);
	$REDIS_1->EXEC();
	break;
}

$CHECK_ARRAY = $REDIS_0->MGET(array('ProcessLimit', 'ProcessCheckTime', 'ProcessMax', 'ProcessMin'));
if ($CHECK_ARRAY[0] !== '1') {
	$PROCESS_CHECK_TIME = (int)$CHECK_ARRAY[1];
	if (($START_TIME - $PROCESS_CHECK_TIME) > 6E7) {
		$PROCESS_MAX = (int)$CHECK_ARRAY[2];
		if ($PROCESS_MAX < 1 || $PROCESS_MAX > 128) $PROCESS_MAX = 4;
		$PROCESS_MIN = (int)$CHECK_ARRAY[3];
		if ($PROCESS_MIN < 1 || $PROCESS_MAX > 128) $PROCESS_MIN = 2;
		$REDIS_0->MSET(array('ProcessCheckTime' => $START_TIME, 'ProcessMax' => $PROCESS_MAX, 'ProcessMin' => $PROCESS_MIN));
		$PROCESS_ENABLED = 0;
		$PROCESS_ARRAY = $REDIS_0->SMEMBERS('ProcessList');
		if (count($PROCESS_ARRAY) > 0) {
			foreach ($PROCESS_ARRAY as $PID) {
				$PROCESS_STRUCTURE = $REDIS_0->HMGET($PID, array('Status', 'LastResponseTime'));
				if (count($PROCESS_STRUCTURE) < 2 OR $PROCESS_STRUCTURE['Status'] === '1' && ($START_TIME - $PROCESS_STRUCTURE['LastResponseTime']) > 6E7) {
					$REDIS_0->DEL($PID);
					$REDIS_0->SREM('ProcessList', $PID);
				} else {
					$PROCESS_ENABLED++;
					if ($PROCESS_ENABLED > $PROCESS_MAX) $REDIS_0->HSET($PID, 'Status', 0);
				}
			}
		}
		if ($PROCESS_ENABLED < $PROCESS_MIN) {
			$n = $PROCESS_MIN - $PROCESS_ENABLED;		
			for ($i = 0; $i < $n; $i++) {
				pclose(popen('php -f ' . __DIR__ . '/kernel.php &', 'r'));
			}
		}
	}
}

$REDIS_0->MULTI()
		->INCRBY('PerformanceConsume0', (int)(microtime(true) * 1E6) - $START_TIME)
		->INCR('PerformanceCount0')
		->EXEC();

exit;

function Record_BadRequest(&$redis, $ip, $time, $type) {
	$redis->PIPELINE();
	$redis->INCR('BlockedIPCount');
	$redis->RPUSH('BadRequests', $_SERVER['QUERY_STRING'] . '&ip=' . $ip . '&time=' . $time . '&type=' . $type);
	if ($type > 0) {
		$redis->SADD('BlockedIPList', $ip);
		$redis->SADD('BlockedIPHistory', $ip);
	}
	$redis->EXEC();
	exit;
}

function MDATA($request, $key, $value) {
		$ret = $request;
		$spos = strpos($request, $key);
		if ($spos !== false) {
			$ret = substr($request, 0, $spos) . $value;
		} else {
			$ret = $request . $value;
		}
		return $ret;
}

?> 
