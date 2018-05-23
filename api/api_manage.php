<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analyticsb Free Manage Site API PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 05/23/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

header('Access-Control-Allow-Origin:*');
header('Content-type: text/html; charset=utf-8');

@require '../config/config_common.php';

$sid = SDATA_IN('sid',23,'EXIT');
$t = SDATA_IN('t',23,'EXIT');
$v = SDATA_IN('v',14,'EXIT',32);
$id = substr($sid,0,-3);
if (!verify_user($sid, $t, $v)) exit;

$q = SDATA_IN('q',16,'EXIT',32);
$sort = SDATA_IN('sort',23,0);
$sort = ($sort === 0) ? 'DESC' : 'ASC';
$tz = SDATA_IN('tz',16,'',32);
if ($tz) date_default_timezone_set($tz);
$db_site = 'site' . $sid; 

switch ($q) {
case 'add site':
	echo add_site($db_site);
	exit;
case 'set site':
	echo change_site_setting($db_site);
	exit;
case 'clear site':
	echo clear_site_data($db_site);
	break;
case 'del site':
	echo delete_site($db_site, $sid);
	exit;
case 'get domain':
	get_domains($sid);
	exit;
case 'set domain':
	echo set_domains($sid);
	exit;
}

exit;

function con_db($host, $user, $pw) {
		$server = $_SERVER['HTTP_HOST'];
		$con = mysqli_connect($host, $user, $pw);
		if (mysqli_connect_errno($con)) {
 			die('Could not connect mysql host - Manage API Function[con_db]');
 		}
		return $con;
}

function use_db($host,$user,$pw,$db) {
		$server = $_SERVER['HTTP_HOST'];
		$con = mysqli_connect($host, $user, $pw);
		if (mysqli_connect_errno($con)) {
 			die('Could not connect mysql host - Manage API Function[use_db]');
 		}
		$db_selected = mysqli_select_db($con, $db);
		if (!$db_selected) {
			mysqli_close($con);
			die('Database is not existed - Manage API Function[use_db]');
		}
		return $con;
}

function SDATA_IN($key, $opt, $def, $maxL=0, $minL=0, $con=0) {
		if (isset($_POST[$key])) {
			$val = $_POST[$key];
		} else {
			if ($def === 'EXIT') {
				exit;
			} else {
				return $def;
			}
		}
		switch ($opt) {
		case 10:
			$mval = filter_var($val, FILTER_SANITIZE_STRING);
			if (strlen($mval) != strlen($val)) {
				return '';
			} else {
				if (strlen($mval) > $maxL) $mval = substr($mval,0,$maxL);
				return mysqli_real_escape_string($con, $mval);
			}
		case 11:
			$mval = filter_var($val, FILTER_SANITIZE_STRING);
			if (strlen($mval) > $maxL) $mval = substr($mval,0,$maxL);
			return mysqli_real_escape_string($con, $mval);
		case 12:
			$mval = filter_var($val, FILTER_SANITIZE_STRING);
			if (strlen($mval) != strlen($val)) {
				return '';
			} else {
				if (strlen($mval) > $maxL) {
					return substr($mval,0,$maxL);
				} else {
					return $mval;
				}
			}
		case 13:
			$mval = filter_var($val, FILTER_SANITIZE_STRING);
			if (strlen($mval) == $maxL) {
				return mysqli_real_escape_string($con, $val);
			} else {
				exit;
			}
		case 14:
			$mval = filter_var($val, FILTER_SANITIZE_STRING);
			if (strlen($mval) == $maxL) {
				return $val;
			} else {
				exit;
			}
		case 15:
			$mval = filter_var($val, FILTER_VALIDATE_EMAIL);
			$mval = mysqli_real_escape_string($con, $mval);
			if (strlen($mval) != strlen($val)) {
				return '';
			} else {
				if (strlen($val) > $maxL) {
					return '';
				} else {
					return $val;
				}
			}
		case 16:
			if (strlen($val) > $maxL) {
				return substr($val, 0, $maxL);
			} else {
				return $val;
			}
		case 20:
			$tmp = (int)$val;
			return ($tmp > $maxL || $tmp < $minL) ? 0 : $tmp;
		case 21:
			return (int)$val;
		case 22:
			$tmp = (int)$val;
			if ((string)$tmp != (string)$val) {
				exit;
			} else {
				return ($tmp - 20000000);
			}
		case 23:
			$tmp = (int)$val;
			if ((string)$tmp != (string)$val) {
				exit;
			} else {
				return $tmp;
			}
		case 30:
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
		case 31:
			return (float)$val;
		}
		return NULL;
}

function check_table($con, $cTable, $cDB) {
		$ret = false;
		$sql = "SHOW TABLES FROM {$cDB}";
		$result = mysqli_query($con, $sql);
		if ($result && mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_row($result)) {
				if ($row[0] == $cTable) {
					$ret = true;
					break;
				}
			}
			mysqli_free_result($result);
		}
		return $ret;
}

function check_value($value, $type) {
		switch($type) {
		case 1:
			return check_domain_ip(trim($value));
		case 2:
			return check_site_url(trim($value));
		case 3:
			return check_site_url(trim($value));
		case 4:
			return check_page_url(trim($value));
		case 5:
			return check_ip(trim($value));
		case 6:
			return check_id(trim($value));
		}
		return '';
}

function check_domain_ip($x) {
		$tmp = parse_url($x, PHP_URL_HOST);
		if (!$tmp) $tmp = $x;
		if (preg_match("/^[0-9a-zA-Z]+[0-9a-zA-Z\.-]*\.[a-zA-Z]{2,6}$/", $tmp)) {
			return strtolower($tmp);
		} else {
			if ((bool)filter_var($tmp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				return $tmp;
			}
		}
		return '';
}

function check_ip($x) {
		if ((bool)filter_var($x, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $x;
		}
		return '';
}

function check_id($x) {
		if ((bool)filter_var($x, FILTER_SANITIZE_STRING)) {
			if (strlen($x) === 16) return strtoupper($x);
		}
		return '';
}

function check_site_url($x) {
		$tmp = parse_url($x, PHP_URL_SCHEME) . '://' . parse_url($x, PHP_URL_HOST);
		if ((bool)filter_var($tmp, FILTER_VALIDATE_URL)) {
			return $tmp;
		}
		return '';
}

function check_page_url($x) {
		$tmp = parse_url($x, PHP_URL_SCHEME) . '://' . parse_url($x, PHP_URL_HOST);
		if ((bool)filter_var($tmp, FILTER_VALIDATE_URL)) {
			if (strlen($x) > (strlen($tmp) + 1)) return $x;
		}
		return '';
}

function get_ip() {
		foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER)) {
				foreach(explode(',', $_SERVER[$key]) as $ip) {
					if ((bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
						return $ip;
					}
				}
			}
		}
		return '';
}

function set_domains($sid) {
		$err = '';
		$param = SDATA_IN('param',12,'',64);
		$key = SDATA_IN('key',12,'',32);
		$value = preg_replace('/(^\s*)|(\s*$)/','',SDATA_IN('value',16,'',2048));
		$type = SDATA_IN('type',23,'EXIT');
		if ($param === '' && ($key === '' && $value === '')) {
			$err = '<br/>Miss Argument - Manage API Function[set_domains]';
			return $err;
		}
		$REDIS = new Redis();
		$REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true AND exit;
		$REDIS->SELECT(REDIS_DB_2);
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
			$err .= '<br/>Could not connect mysql host. Please contact Administrator! - Manage API Function[set_domains]';
			return $err;
	 	}
		$db_selected = mysqli_select_db($con, 'site'.$sid);
		if (!$db_selected) {
			$err .= create_site($con, 'site'.$sid);
			if ($err === '') {
				$db_selected = mysqli_select_db($con, 'site'.$sid);
			} else {
				return $err . '[set_domains]';
			}
		}
		if (!$db_selected) {
	 		mysqli_close($con);
			$err .= '<br/>Could not use database. Please contact Administrator! - Manage API Function[set_domains]';
			return $err;
	 	} else {
			switch($param) {
			case 'delete':
				$sql = "DELETE FROM domain WHERE MD5='{$key}'";
				if (!mysqli_query($con, $sql)) {
					mysqli_close($con);
					$err .= '<br/>Delete ('.$value.') failed - Manage API Function[set_domains]';
					return $err;
				} else {
					$REDIS->SREM($sid.'-'.$type, $key);
				}
				break;
			case 'add':
				$domain = check_value($value, $type);
				if (!$domain) {
					mysqli_close($con);
					$err .= '<br/>('.$value.') is not valid - Manage API Function[set_domains]';
					return $err;
				}
				$now = time();
				$md5 = md5($domain);
				$sql = "INSERT INTO domain(Domain, MD5, DomainType, CreateTime, UpdateTime) VALUES('{$domain}', '{$md5}', {$type}, {$now}, {$now})";
				if (!mysqli_query($con, $sql)) {
					mysqli_close($con);
					$err .= '<br/>Add domain ('.$domain.') failed, or it is already existed - Manage API Function[set_domains]';
					return $err;
				} else {
					$REDIS->SADD($sid.'-'.$type, $md5);
				}
				break;
			case 'modify':
				$domain = check_value($value, $type);
				if (!$domain) {
					mysqli_close($con);
					$err .= '<br/>('.$value.') is not valid - Manage API Function[set_domains]';
					return $err;
				}
				$md5 = md5($domain);
				$sql = "UPDATE domain SET Domain='{$domain}',MD5='{$md5}' WHERE MD5='{$key}'";
				if (!mysqli_query($con, $sql)) {
					mysqli_close($con);
					$err .= '<br/>Modify ('.$domain.') failed, or it is already existed - Manage API Function[set_domains]';
					return $err;
				} else {
					$REDIS->SREM($sid.'-'.$type, $key);
					$REDIS->SADD($sid.'-'.$type, $md5);
				}
				break;
			case 'batch':
				$domains = array();
				$arrVal = array();
				if ($value) {
					strpos($value,PHP_EOL) !== false ? $arrVal = explode(PHP_EOL, $value) : $arrVal[0] = $value;
					foreach ($arrVal as $val) {
						$tmp = check_value($val, $type);
						if ($tmp) {
							$domains[] = $tmp;
						} else {
							$err .= '<br/>('.$val.') is not valid - Manage API Function[set_domains]';
						}
					}
					if (count($domains) === 0) {
						mysqli_close($con);
						return $err;
					}
				} else {
					mysqli_close($con);
					$err .= '<br/>No data to be added - Manage API Function[set_domains]';
					return $err;
				}
				$now = time();
				foreach($domains as $tmp) {
					$md5 = md5($tmp);
					$sql = "INSERT INTO domain(Domain, MD5, DomainType, CreateTime, UpdateTime) VALUES('{$tmp}', '{$md5}', {$type}, {$now}, {$now})";
					if (!mysqli_query($con, $sql)) {
						$err .= '<br/>Batch Add ('.$tmp.') failed, or it is already existed - Manage API Function[set_domains]';
					} else {
						$REDIS->SADD($sid.'-'.$type, $md5);
					}
				}
				break;
			case 'addto':
				$sql="UPDATE domain SET DomainType=1 WHERE MD5='{$key}'";
				if (!mysqli_query($con, $sql)) {
					mysqli_close($con);
					$err .= '<br/>Add ('.$domain.') failed, or it is already existed.';
					return $err;
				} else {
					$REDIS->SREM($sid.'-2', $key);
					$REDIS->SADD($sid.'-1', $key);
				}
				break;
			}
		}
		mysqli_close($con);
		if ($err === '') $err = 'OK';
		return $err;
}

function get_domains($sid) {
		$db_site = 'site' . $sid;
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con))
		{
			die ('<br/>Could not connect mysql host. Please contact Administrator! - Manage API Function[get_domains]');
 		}
		$db_selected = mysqli_select_db($con, $db_site);
		if (!$db_selected)
 		{
 			mysqli_close($con);
			die ('<br/>Could not use database. Please contact Administrator! - Manage API Function[get_domains]');
		} else {
			if (!check_table($con,'domain',$db_site)) { 
$sql = 'CREATE TABLE IF NOT EXISTS domain (
Domain varchar(1024) NOT NULL DEFAULT "", 
MD5 varchar(32) NOT NULL PRIMARY KEY,
BlockedTimes bigint NOT NULL DEFAULT 0,
DomainType tinyint NOT NULL DEFAULT 0, 
CreateTime int NOT NULL DEFAULT 0,
UpdateTime int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
				if (!mysqli_query($con, $sql)) {
					die ('<br/>Create domain table failed. Please contact Administrator! - Manage API Function[get_domains]');
				}
			}
			$REDIS = new Redis();
			$REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true AND exit;
			$REDIS->SELECT(REDIS_DB_2);
			if ($REDIS->ZCARD($sid . 'BlockedTime') > 0) {
				$ARR_RETURN = $REDIS->ZRANGE($sid . 'BlockedTime', 0, -1, 'WITHSCORES');
				if (!empty($ARR_RETURN)) {
					foreach ($ARR_RETURN as $key=>$val) {
						if (!empty($key) AND !empty($val)) {
							$md5 = md5($key);
							$sql = "UPDATE domain SET UpdateTime={$val} WHERE MD5='{$md5}'";
							if (!mysqli_query($con, $sql)) {
								$sql = "INSERT INTO domain(Domain, MD5, BlockedTimes, DomainType, CreateTime, UpdateTime) VALUES('{$key}', '{$md5}', 1, 2, {$val}, {$val})";
								mysqli_query($con, $sql);
							}
						}
					}
					$REDIS->DEL($sid . 'BlockedTime');
				}
			}
			if ($REDIS->ZCARD($sid . 'BlockedCount') > 0) {
				$ARR_RETURN = $REDIS->ZRANGE($sid . 'BlockedCount', 0, -1, 'WITHSCORES');
				if (!empty($ARR_RETURN)) {
					foreach ($ARR_RETURN as $key=>$val) {
						if (!empty($key) AND !empty($val)) {
							$md5 = md5($key);
							$sql = "UPDATE domain SET BlockedTimes=BlockedTimes+{$val} WHERE MD5='{$md5}'";
							mysqli_query($con, $sql);
						}
					}
					$REDIS->DEL($sid . 'BlockedCount');
				}
			}
			global $sort;
			$sql = 'SELECT * FROM domain ORDER BY CreateTime ' . $sort;
			$result = mysqli_query($con, $sql);
			if ($result && mysqli_num_rows($result)) {
				$GLOBAL_DOMAINS = array();
				while ($row = mysqli_fetch_assoc($result)) {
					$GLOBAL_DOMAINS[] = $row;
				}
				mysqli_free_result($result);
			} else {
				mysqli_close($con);
				die ('<br/>Could not list domains');
			}
		}
		mysqli_close($con);
		echo json_encode($GLOBAL_DOMAINS);
}

function add_site($db_site) {
		$err = '';
		$value = preg_replace('/(^\s*)|(\s*$)/','',SDATA_IN('value',16,'',2048));
		$type = SDATA_IN('type',23,'EXIT');
		$domains = array();
		$arrVal = array();
		if ($value) {
			strpos($value,PHP_EOL) !== false ? $arrVal = explode(PHP_EOL, $value) : $arrVal[0] = $value;
			foreach ($arrVal as $val) {
				$tmp = check_value($val, $type);
				if ($tmp) {
					$domains[] = $tmp;
				} else {
					$err .= '<br/>('.$val.') are not valid - Manage API Function[add_site]';
				}
			}
			if (count($domains) == 0) {
				$err .= '<br/>('.$value.') are not valid - Manage API Function[add_site]';
			}
		} else {
			$err .= '<br/>No data to be added - Manage API Function[add_site]';
		}
		if ($err) return $err;
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con))
		{
			$err .= '<br/>Could not connect mysql host. Please contact Administrator!';
			return $err;
 		}
		$db_selected = mysqli_select_db($con, $db_site);
		if (!$db_selected) {
			if (!mysqli_query($con, "CREATE DATABASE IF NOT EXISTS {$db_site} DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci")) {
				mysqli_close($con);
				$err .= '<br/>Create site database failed. Please contact Administrator!';
				return $err;
			}
		}
		$db_selected = mysqli_select_db($con, $db_site);
		if (!$db_selected) {
			$err .= '<br/>Could not use database. Please contact Administrator!';
		} else {
$sql = 'CREATE TABLE IF NOT EXISTS domain (
Domain varchar(1024) NOT NULL DEFAULT "",
MD5 varchar(32) NOT NULL PRIMARY KEY,
BlockedTimes bigint NOT NULL DEFAULT 0,
DomainType tinyint NOT NULL DEFAULT 0,
CreateTime int NOT NULL DEFAULT 0,
UpdateTime int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
			if (mysqli_query($con, $sql)) {
				$now = time();
				foreach ($domains as $tmp) {
					$md5 = md5($tmp);
					$sql = "INSERT INTO domain(Domain, MD5, DomainType, CreateTime) VALUES('{$tmp}','{$md5}',1,{$now})";
					if (!mysqli_query($con, $sql)) {
						$err .= "<br/>Add domain ({$tmp}) failed";
					}
				}
			} else {
				$err .= '<br/>Create domain table failed';
			}
		}
		mysqli_close($con);
		if ($err === '') $err = 'OK';
		return $err;
}

function change_site_setting($db_site) {
		$err = '';
		$IS_TB_EXIST = 1;
		$param = SDATA_IN('param',23,'EXIT');
		$key = SDATA_IN('key',12,'',32);
		$value = SDATA_IN('value',12,'',32);
		if ($key === 'TimeZone') $value = '\'' . $value . '\'';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
			$err .= '<br/>Could not connect mysql host. Please contact Administrator! - Manage API Function [change_site_setting]';
			return $err;
 		}
		$db_selected = mysqli_select_db($con, $db_site);
		if (!$db_selected) {
 			mysqli_close($con);
			$err .= '<br/>Could not use database. Please contact Administrator! - Manage API Function [change_site_setting]';
			return $err;
 		}
		if (!check_table($con, 'setting', $db_site)) {
$sql = 'CREATE TABLE IF NOT EXISTS setting (
pKey int NOT NULL PRIMARY KEY DEFAULT 0,
TimeZone varchar(32) NOT NULL DEFAULT "PRC",
IPDatabase tinyint NOT NULL DEFAULT 0,
SiteStatus tinyint NOT NULL DEFAULT 0,
MD5Type tinyint NOT NULL DEFAULT 0,
SessionPeriod int NOT NULL DEFAULT 180000,
CreateTime int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
			if (!mysqli_query($con, $sql)) {
				$err .= '<br/>Create table (setting) failed - Manage API Function [change_site_setting]';
				mysqli_close($con);
				return $err;
			}
			$IS_TB_EXIST = 0;
		}
		switch ($param) {
		case 1:
			$now = time();
			$key2 = SDATA_IN('key2',12,'',32);
			$value2 = SDATA_IN('value2',20,0,2,0);
			$sql = "INSERT INTO setting(pKey, {$key}, {$key2}, CreateTime) VALUES(0, {$value}, {$value2}, {$now}) ON DUPLICATE KEY UPDATE {$key}={$value}, {$key2}={$value2}";
			if (!mysqli_query($con, $sql)) {
				mysqli_close($con);
				$err .= "<br/>Insert {$key} : {$value} failed - Manage API Function [change_site_setting]";
				return $err;
			}
			break;
		case 2:
			$now = time();
			$sql = "INSERT INTO setting(pKey, {$key}, CreateTime) VALUES(0, {$value}, {$now}) ON DUPLICATE KEY UPDATE {$key}={$value}";
			if (!mysqli_query($con, $sql)) {
				mysqli_close($con);
				$err .= "<br/>Update {$key} : {$value} failed - Manage API Function [change_site_setting]";
				return $err;	
			}
			break;
		case 3:
			if ($IS_TB_EXIST === 1) {
				$sql = "DELETE FROM setting WHERE {$key}={$value}";
				if (!mysqli_query($con, $sql)) {
					mysqli_close($con);
					$err .= "<br/>Delete {$key} : {$value} failed - Manage API Function [change_site_setting]";
					return $err;
				}
			}
			break;
		}
		mysqli_close($con);
		if ($err === '') $err = 'OK';
		return $err;
}

function clear_site_data($db_site) {
		$err = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
			$err .= '<br/>Could not connect mysql host. Please contact Administrator! - Manage API Function [clear_site_data]';
			return $err;
 		}
		$db_selected = mysqli_select_db($con, $db_site);
		if (!$db_selected) {
 		 	mysqli_close($con);
			$err .= '<br/>Could not use database. Please contact Administrator!';
			return $err;
		} else {
			$sql = 'SHOW TABLES';
			$result = mysqli_query($con, $sql);
			if ($result && mysqli_num_rows($result)) {
				$savedtb = 'domain,setting';
				while ($row = mysqli_fetch_row($result)) {
					if (stripos($savedtb,$row[0]) === false) {
						if (!mysqli_query($con, "DROP TABLE {$row[0]}")) {
							mysqli_close($con);
							$err .= "<br/>Drop table {$row[0]} failed";
							return $err;
						}
					}
				}
				mysqli_free_result($result);
			} else {
				mysqli_close($con);
				$err .= '<br/>Could not list tables';
				return $err;
			}
		}
		mysqli_close($con);
		$err .= '<br/>Clear site analytics data successfully';
		return $err;
}

function delete_site($db_site, $sid) {
		$err = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
			$err .= '<br/>Could not connect mysql host. Please contact Administrator! - Manage API Function[delete_site]';
			return $err;
 		}
		$sql = 'SHOW DATABASES';
		$result = mysqli_query($con, $sql);
		if (!$result) {
			mysqli_close($con);
			$err .= '<br/>Could not list databases';
			return $err;
		} else if (mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_row($result)) {
				if ($row[0] == $db_site) {
					if (!mysqli_query($con, "DROP DATABASE {$db_site}")) {
						mysqli_close($con);
						$err .= '<br/>delete site failed';
						return $err;
					}
					break;
				}
			}
		}
		mysqli_free_result($result);
		mysqli_close($con);	
		$REDIS = new Redis();
		$REDIS->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true AND exit;
		$REDIS->SELECT(REDIS_DB_2);
		$RETURN_ARRAY = $REDIS->KEYS($sid.'*');
		$REDIS->DEL($RETURN_ARRAY);
		$err = 'OK';
		return $err;
}

function create_site($con, $db_site) {
		$err = '';
		if (!mysqli_query($con, "CREATE DATABASE IF NOT EXISTS {$db_site} DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci")) {
			mysqli_close($con);
			$err .= '<br/>Create site database failed. Please contact Administrator! - Manage API Function[create_site]';
			return $err;
		}
		$db_selected = mysqli_select_db($con, $db_site);
		if (!$db_selected) {
			$err .= '<br/>Could not use database. Please contact Administrator! - Manage API Function[create_site]';
		} else {
$sql = 'CREATE TABLE IF NOT EXISTS domain (
Domain varchar(1024) NOT NULL DEFAULT "",
MD5 varchar(32) NOT NULL PRIMARY KEY,
BlockedTimes bigint NOT NULL DEFAULT 0,
DomainType tinyint NOT NULL DEFAULT 0,
CreateTime int NOT NULL DEFAULT 0,
UpdateTime int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
			if (!mysqli_query($con, $sql)) $err .= '<br/>Create domain table failed - Manage API Function[create_site]';
$sql = 'CREATE TABLE IF NOT EXISTS setting (
pKey int NOT NULL PRIMARY KEY DEFAULT 0,
TimeZone varchar(32) NOT NULL DEFAULT "PRC",
IPDatabase tinyint NOT NULL DEFAULT 0,
SiteStatus tinyint NOT NULL DEFAULT 0,
MD5Type tinyint NOT NULL DEFAULT 0,
SessionPeriod int NOT NULL DEFAULT 1800000,
CreateTime int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
			if (mysqli_query($con, $sql)) {
				$now = time();
				global $tz;
				$sql = "INSERT INTO setting Set pKey=0, TimeZone='{$tz}', IPDatabase=0, SiteStatus=0, MD5Type=0, SessionPeriod=1800000, CreateTime={$now}";
				if (!mysqli_query($con, $sql)) $err .= "Set default site setting failed - Manage API Function[create_site]";
			} else {
				$err .= '<br/>Create setting table failed - Manage API Function[create_site]';
			}
		}
		return $err;
}

function verify_user($sid, $t, $v) {
		$n = time();
		if ($t < $n) exit;
		$matchvisa = md5($sid . $t . ENCODE_FACTOR);
		if ($v === $matchvisa) return true;
		exit;
}

?>
