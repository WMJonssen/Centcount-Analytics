<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Modify Site Configuration PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

function modify_site_config() {
	
		$err = '';
		$con;
	
		if (empty($_POST['SiteName'])) {
			$err .= '<br/>Site name must not be empty';
		} else {
			$tmp = filter_var($_POST['SiteName'], FILTER_SANITIZE_STRING);
			if (strlen($tmp) != strlen($_POST['SiteName'])) {
				$err .= '<br/>Site name is not valid';
			}
		}

		if (isset($_POST['IPDB'])) {
			$IPDB = (int)$_POST['IPDB'];
			if ($IPDB > 4 || $IPDB < 0) {
				$err .= '<br/>IP database is not set';
			}
		} else {
			$err .= '<br/>IP database is not set';
		}
		
		if (empty($_POST['TimeZone'])) {
			$err .= '<br/>Timezone is not set';
		} else {
			if (get_tz_city($_POST['TimeZone']) === '') {
				$err .= '<br/>Timezone is not valid';
			}
		}
		
		if (empty($_POST['Description']) === false) {
			$tmp = filter_var($_POST['Description'], FILTER_SANITIZE_STRING);
			if (strlen($tmp) != strlen($_POST['Description'])) {
				$err .= '<br/>Description is not valid';
			}
		}
		
		
		if ($err == '') {

			$con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, DB_NAME_USER, 'Modify Site Info', 'modify_site_info');
			if (!$con) return;
			
			$SID = $GLOBALS['SITEID'];
			$sitename = SDATA($_POST['SiteName'], 1, 255, 0, $con);
			$description = empty($_POST['Description']) === false ? SDATA($_POST['Description'], 1, 255, 0, $con) : '';
			$timezone = SDATA($_POST['TimeZone'], 1, 32, 0, $con);
			
			$sql = "UPDATE st{$GLOBALS['SITELIST_TABLE']} SET SiteName='{$sitename}', TimeZone='{$timezone}', IPDatabase={$IPDB}, SiteDescription='{$description}' WHERE SiteID={$SID}";
			if (mysqli_query($con, $sql)) {
				
				//*************************** Set Site Setting Start *************************** 
				$value = $timezone;
		
				$v = get_visa($SID);//Authorize for access database
				$host = $_SESSION['DATACENTER'];//get_site_info('DataCenter');
				$tz = $timezone;//get_site_info('TimeZone');
		
				$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
				$q = $v . 'q=set site&param=1&key=TimeZone&key2=IPDatabase&sid=' . $SID. '&tz=' . $tz . '&value=' . $value . '&value2=' . $IPDB;
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $curl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);//不返回response头部信息 
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
				$ret = curl_exec($ch);
				curl_close($ch);

				if ($ret !== 'OK') {
					mysqli_close($con);
					$err .= $ret;
					return $err;
				}
				//**************************** Set Site Setting End ****************************

				//************************* Update Setting Cache Start *************************
				pclose(popen('php -f ' . __DIR__ . '/cache_settings.php ' . $SID . ' 0 &', 'r'));//argv[2] = 0, normal update domains & settings cache data
				//************************** Update Setting Cache End **************************

				$err .= '<br/>Modify site information successfully';
			} else {
				$err .= '<br/>Modify site information failed';
			}

			mysqli_close($con);
		}
		return $err;
}


function site_config_html() {
	
	$sitename; $createtime; $sitestaus; $textsitestatus; $sitedes;
	
	$i = count($GLOBALS['SITES']);
	for ($row = 0; $row < $i; $row++) {
		if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
			$sitename = $GLOBALS['SITES'][$row]['SiteName'];
			$sitedes = $GLOBALS['SITES'][$row]['SiteDescription'];
			$sitestatus = $GLOBALS['SITES'][$row]['SiteStatus'];
			$createtime = $GLOBALS['SITES'][$row]['CreateTime'];
			break;
		}
	}
	
	switch ($sitestatus) {
	case 0:
		$textsitestatus = $GLOBALS['indicator']['Running'];
		break;
	case 1:
		$textsitestatus = $GLOBALS['indicator']['Stopped'];
		break;
	}

	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
	echo get_site_info('IPDatabase'),get_site_info('TimeZone');
			
	$GLOBALS['TEXT_BODY'] = '
<div class="dTitle">'. $GLOBALS['indicator']['Modify Site Configuration'] .'</div>

<form name="modifysiteinfoform" method="POST" action="" >
	<table>
		<tr><td>'. $GLOBALS['indicator']['Site Name'] .' : </td></tr>
		<tr><td class="mTop"><input type="text" name="SiteName" id="Site Name" maxlength="255" value="'.$sitename.'" placeholder="required" onblur="checkValue(this.id)" onfocus="setStyle(this.id, 1)"/></td></tr>
					
		<tr><td>'. $GLOBALS['indicator']['IP Database'] .' : </td></tr>
		<tr>	
			<td class="mTop">
				<select name="IPDB" id="IPDB">'. get_ipdb_list(get_site_info('IPDatabase')) .'</select>
			</td>
		</tr>
				
		<tr><td>'. $GLOBALS['indicator']['Timezone'] .' : </td></tr>
		<tr><td class="mTop"><select name="TimeZone" id="TimeZone">'. get_tz_list(get_site_info('TimeZone')) .'</select></td></tr>

		<tr><td>'. $GLOBALS['indicator']['Site Description'] .' : </td></tr>
		<tr><td class="mTop"><textarea name="Description" id="Description" rows="5" maxlength="255" onblur="setStyle(this.id,0)" onfocus="setStyle(this.id,1)">'.$sitedes.'</textarea></td></tr>
		
		<tr><td>'. $GLOBALS['indicator']['Site ID'] .' : '.$GLOBALS['SITEID'].'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Create Date'] .' : '.date('Y-m-d', $createtime) .'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Site Status'] .' : '. $textsitestatus .'</td></tr>
				
		<tr><td><button name="modifysiteinfo" value="modifysiteinfo" id="modifysiteinfosubmit" type="submit" class="middle">'. $GLOBALS['indicator']['Modify'] .'</button></td></tr>
	</table>
</form>';
			
	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">

.errmsg{margin-bottom:15px;}

.framebody{width:calc(100% - 30px); max-width:640px; height:auto; padding:15px; padding-top:0px; margin:0; text-align:center; border:0; float:left;}
.dTitle{width:calc(100% - 30px); height:40px; line-height:40px; padding-left:15px; padding-right:15px; font-family:Verdana,"Microsoft Yahei",Arial; font-size:16px; text-align:left; background-color:#555; color:#fff; float:left;}

td{width:100%; height:auto; font-size:13px; line-height:18px; border:0px; border-collapse:collapse; border-spacing:0; margin:0; margin-top:15px; text-align:left; float:left;}
tr,tbody,table,form{width:100%; border:0px; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana;}

input, select, textarea{width:calc(100% - 1px); height:auto; padding:5px; margin:0; vertical-align:middle; border:#ccc 1px solid; font-family:"Microsoft Yahei",Arial,Verdana; float:left;} 
select{width:calc(100% - 1px);}
textarea{color:#555; resize:none;}
button{width:150px; height:30px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333;}

.dIme{ime-mode:disabled;}
.mTop{margin-top:0px;}
b{font-size:14px; color:#111;}

</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">
	
	function setStyle(x,y) {
		if (y === 1) {
			document.getElementById(x).style.borderColor = "#39F";
		} else {
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	function checkValue(x) {
		if (document.getElementById(x).value == "") {
			document.getElementById("errormsg").innerHTML = document.getElementById(x).name + " must not be empty";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
			return false;
		} else {
			document.getElementById("errormsg").style.display = "";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	
	function Resize() {
		return;
	}

</script>';
		
}



function get_ipdb_list($ipdb) {
	
	global $IPDBS;
	$ret = '';
	foreach ($IPDBS as $key=>$value) 
	{
		if ($key == $ipdb) {
			$ret .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
		} else {
			$ret .= '<option value="' . $key . '">' . $value . '</option>';
		}
	}
	return $ret;
}



function get_tz_list($timezone) {
	
	global $TIMEZONES;
	$tz = '';
	foreach ($TIMEZONES as $key=>$value) 
	{
		if ($value == $timezone) {//(GMT+08:00) Beijing
			$tz = '<option value="' . $value . '" selected="selected">' . $key . '</option>' . $tz;
		} else {
			$tz = '<option value="' . $value . '">' . $key . '</option>' . $tz;
		}
	}
	return $tz;
}


function get_tz_city($tz) {
		
		global $TIMEZONES;
		foreach ($TIMEZONES as $key => $value) 
		{
			if ($value == $tz) {
				return $key;
			}
		}
		return '';
}

$IPDBS = array(
	'IP2LOCATION (English)',
	'GeoIP (简体中文)',
	'GeoIP (English)'
);


$TIMEZONES = array(
	'(GMT-12:00) International Date Line West' => 'Pacific/Kwajalein',
	'(GMT-11:00) Midway Island' => 'Pacific/Midway',
	'(GMT-11:00) Samoa' => 'Pacific/Apia',
	'(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
	'(GMT-09:00) Alaska' => 'America/Anchorage',
	'(GMT-08:00) Pacific Time (US & Canada)' => 'America/Los_Angeles',
	'(GMT-08:00) Tijuana' => 'America/Tijuana',
	'(GMT-07:00) Arizona' => 'America/Phoenix',
	'(GMT-07:00) Mountain Time (US & Canada)' => 'America/Denver',
	'(GMT-07:00) Chihuahua' => 'America/Chihuahua',
	'(GMT-07:00) La Paz' => 'America/Chihuahua',
	'(GMT-07:00) Mazatlan' => 'America/Mazatlan',
	'(GMT-06:00) Central Time (US & Canada)' => 'America/Chicago',
	'(GMT-06:00) Central America' => 'America/Managua',
	'(GMT-06:00) Guadalajara' => 'America/Mexico_City',
	'(GMT-06:00) Mexico City' => 'America/Mexico_City',
	'(GMT-06:00) Monterrey' => 'America/Monterrey',
	'(GMT-06:00) Saskatchewan' => 'America/Regina',
	'(GMT-05:00) Eastern Time (US & Canada)' => 'America/New_York',
	'(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
	'(GMT-05:00) Bogota' => 'America/Bogota',
	'(GMT-05:00) Lima' => 'America/Lima',
	'(GMT-05:00) Quito' => 'America/Bogota',
	'(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
	'(GMT-04:00) Caracas' => 'America/Caracas',
	'(GMT-04:00) La Paz' => 'America/La_Paz',
	'(GMT-04:00) Santiago' => 'America/Santiago',
	'(GMT-03:30) Newfoundland' => 'America/St_Johns',
	'(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
	'(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
	'(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
	'(GMT-03:00) Greenland' => 'America/Godthab',
	'(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
	'(GMT-01:00) Azores' => 'Atlantic/Azores',
	'(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
	'(GMT) Casablanca' => 'Africa/Casablanca',
	'(GMT) Dublin' => 'Europe/London',
	'(GMT) Edinburgh' => 'Europe/London',
	'(GMT) Lisbon' => 'Europe/Lisbon',
	'(GMT) London' => 'Europe/London',
	'(GMT) Monrovia' => 'Africa/Monrovia',
	'(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
	'(GMT+01:00) Belgrade' => 'Europe/Belgrade',
	'(GMT+01:00) Berlin' => 'Europe/Berlin',
	'(GMT+01:00) Bern' => 'Europe/Berlin',
	'(GMT+01:00) Bratislava' => 'Europe/Bratislava',
	'(GMT+01:00) Brussels' => 'Europe/Brussels',
	'(GMT+01:00) Budapest' => 'Europe/Budapest',
	'(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
	'(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
	'(GMT+01:00) Madrid' => 'Europe/Madrid',
	'(GMT+01:00) Paris' => 'Europe/Paris',
	'(GMT+01:00) Prague' => 'Europe/Prague',
	'(GMT+01:00) Rome' => 'Europe/Rome',
	'(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
	'(GMT+01:00) Skopje' => 'Europe/Skopje',
	'(GMT+01:00) Stockholm' => 'Europe/Stockholm',
	'(GMT+01:00) Vienna' => 'Europe/Vienna',
	'(GMT+01:00) Warsaw' => 'Europe/Warsaw',
	'(GMT+01:00) West Central Africa' => 'Africa/Lagos',
	'(GMT+01:00) Zagreb' => 'Europe/Zagreb',
	'(GMT+02:00) Athens' => 'Europe/Athens',
	'(GMT+02:00) Bucharest' => 'Europe/Bucharest',
	'(GMT+02:00) Cairo' => 'Africa/Cairo',
	'(GMT+02:00) Harare' => 'Africa/Harare',
	'(GMT+02:00) Helsinki' => 'Europe/Helsinki',
	'(GMT+02:00) Istanbul' => 'Europe/Istanbul',
	'(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
	'(GMT+02:00) Kyev' => 'Europe/Kiev',
	'(GMT+02:00) Minsk' => 'Europe/Minsk',
	'(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
	'(GMT+02:00) Riga' => 'Europe/Riga',
	'(GMT+02:00) Sofia' => 'Europe/Sofia',
	'(GMT+02:00) Tallinn' => 'Europe/Tallinn',
	'(GMT+02:00) Vilnius' => 'Europe/Vilnius',
	'(GMT+03:00) Baghdad' => 'Asia/Baghdad',
	'(GMT+03:00) Kuwait' => 'Asia/Kuwait',
	'(GMT+03:00) Moscow' => 'Europe/Moscow',
	'(GMT+03:00) Nairobi' => 'Africa/Nairobi',
	'(GMT+03:00) Riyadh' => 'Asia/Riyadh',
	'(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
	'(GMT+03:00) Volgograd' => 'Europe/Volgograd',
	'(GMT+03:30) Tehran' => 'Asia/Tehran',
	'(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
	'(GMT+04:00) Baku' => 'Asia/Baku',
	'(GMT+04:00) Muscat' => 'Asia/Muscat',
	'(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
	'(GMT+04:00) Yerevan' => 'Asia/Yerevan',
	'(GMT+04:30) Kabul' => 'Asia/Kabul',
	'(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
	'(GMT+05:00) Islamabad' => 'Asia/Karachi',
	'(GMT+05:00) Karachi' => 'Asia/Karachi',
	'(GMT+05:00) Tashkent' => 'Asia/Tashkent',
	'(GMT+05:30) Chennai' => 'Asia/Kolkata',
	'(GMT+05:30) Kolkata' => 'Asia/Kolkata',
	'(GMT+05:30) Mumbai' => 'Asia/Kolkata',
	'(GMT+05:30) New Delhi' => 'Asia/Kolkata',
	'(GMT+05:45) Kathmandu' => 'Asia/Kathmandu',
	'(GMT+06:00) Almaty' => 'Asia/Almaty',
	'(GMT+06:00) Astana' => 'Asia/Dhaka',
	'(GMT+06:00) Dhaka' => 'Asia/Dhaka',
	'(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
	'(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
	'(GMT+06:30) Rangoon' => 'Asia/Rangoon',
	'(GMT+07:00) Bangkok' => 'Asia/Bangkok',
	'(GMT+07:00) Hanoi' => 'Asia/Bangkok',
	'(GMT+07:00) Jakarta' => 'Asia/Jakarta',
	'(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
	'(GMT+08:00) Beijing' => 'Asia/Shanghai',
	'(GMT+08:00) Shanghai' => 'Asia/Shanghai',
	'(GMT+08:00) Chongqing' => 'Asia/Chongqing',
	'(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
	'(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
	'(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
	'(GMT+08:00) Perth' => 'Australia/Perth',
	'(GMT+08:00) Singapore' => 'Asia/Singapore',
	'(GMT+08:00) Taipei' => 'Asia/Taipei',
	'(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
	'(GMT+08:00) Urumqi' => 'Asia/Urumqi',
	'(GMT+09:00) Osaka' => 'Asia/Tokyo',
	'(GMT+09:00) Sapporo' => 'Asia/Tokyo',
	'(GMT+09:00) Seoul' => 'Asia/Seoul',
	'(GMT+09:00) Tokyo' => 'Asia/Tokyo',
	'(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
	'(GMT+09:30) Adelaide' => 'Australia/Adelaide',
	'(GMT+09:30) Darwin' => 'Australia/Darwin',
	'(GMT+10:00) Brisbane' => 'Australia/Brisbane',
	'(GMT+10:00) Canberra' => 'Australia/Sydney',
	'(GMT+10:00) Guam' => 'Pacific/Guam',
	'(GMT+10:00) Hobart' => 'Australia/Hobart',
	'(GMT+10:00) Melbourne' => 'Australia/Melbourne',
	'(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
	'(GMT+10:00) Sydney' => 'Australia/Sydney',
	'(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
	'(GMT+11:00) Magadan' => 'Asia/Magadan',
	'(GMT+11:00) New Caledonia' => 'Asia/Magadan',
	'(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
	'(GMT+12:00) Auckland' => 'Pacific/Auckland',
	'(GMT+12:00) Fiji' => 'Pacific/Fiji',
	'(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
	'(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
	'(GMT+12:00) Wellington' => 'Pacific/Auckland',
	'(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
);


?>