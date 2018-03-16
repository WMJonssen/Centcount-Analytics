<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free All Sites PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/16/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function all_sites() {

		$ret = array();
		$i = count($GLOBALS['SITES']);
		if ($i) {
			for ($row = 0; $row < $i; $row++) {
				$tmp = [];
				$data = get_visits($GLOBALS['SITES'][$row]['SiteID'], $GLOBALS['SITES'][$row]['DataCenter'], $GLOBALS['SITES'][$row]['TimeZone']);
		
				$tmp['PV0'] = $data[0][0];
				$tmp['PV1'] = $data[1][0];
				$tmp['UV0'] = $data[0][1];
				$tmp['UV1'] = $data[1][1];
				$tmp['VS0'] = $data[0][2];
				$tmp['VS1'] = $data[1][2];
				$tmp['RV0'] = $data[0][3];
				$tmp['RV1'] = $data[1][3];

				$tmp['SiteName']    = $GLOBALS['SITES'][$row]['SiteName'];
				$tmp['DataCenter']  = get_datacenter_region($GLOBALS['SITES'][$row]['DataCenter']);
				$tmp['TimeZone']    = get_tz_city($GLOBALS['SITES'][$row]['TimeZone']);
				$tmp['Url']			= 'manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITES'][$row]['SiteID'];

				$ret[] = $tmp;
			}
		}

		return json_encode($ret, true);
}


function get_visits($sid, $host, $tz) {

		$ret = '';
		$ch;

		$v = get_visa($sid);//Authorize for access database
		$q = $v . 'q=visitsofsite&sid=' . $sid . '&tz=' . $tz;
		$curl = CURL_PROTOCOL . $host . '/api/api_ca.php?' . $q;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);//不返回response头部信息 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
		$ret = curl_exec($ch);
		curl_close($ch);

		return (!$ret ? array(array(0,0,0,0),array(0,0,0,0)) : json_decode($ret, true));

}


function all_sites_html() {
	
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
			
	$GLOBALS['TEXT_BODY'] = '<div id="frametable" class="frametable"></div>';//get_sites_info();

	$GLOBALS['TEXT_CSS']  = '
<style type="text/css">
	
.framebody{width:auto; min-width:330px; height:auto; float:none; margin:15px; margin-top:0px; text-align:left; overflow-x:auto; overflow-y:hidden;}
.errmsg{margin-bottom:10px;}
table{width:100%; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana; table-layout:fixed;}
td{border:#ccc 1px solid; font-size:12px; line-height:18px; color:#555; padding-left:10px; padding-right:10px; overflow:hidden;}

.trhead{width:100%; height:45px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#e7e7e7; font-family:"Microsoft Yahei",Arial,Verdana;}
.trfoot{width:100%; height:45px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#e7e7e7; font-family:"Microsoft Yahei",Arial,Verdana;}
.tra{width:100%; height:60px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#fff; font-family:"Microsoft Yahei",Arial,Verdana;}
.trb{width:100%; height:60px; border:#ccc 1px solid; border-collapse:collapse; border-spacing:0; color:#000; background-color:#f7f7f7; font-family:"Microsoft Yahei",Arial,Verdana;}
.tra:hover,.trb:hover{border-left:#39F 2px solid; background-color:#C6E2FF;}

.tdhlt{border:0px; text-align:left; font-size:13px; color:#000;}
.tdhrt{border:0px; text-align:right; font-size:13px; color:#000;}
.tdhmid{border:0px; text-align:center; font-size:13px; color:#000;}

.tdlt{text-align:left;}
.tdltnoborder{text-align:left; border:0px;}
.tdrt{text-align:right;}
.tdmid{text-align:center;}
.tdmidnoborder{text-align:center; border:0px;}

p{white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:24px; margin:0;}
.plast{color:#999;}

@media only screen and (max-width: 960px) {
td{padding-left:5px; padding-right:5px;}
}

</style>
';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">

	var SITES = ' . all_sites() . ';
	var TB_WIDTH = I(document.getElementById("frametable").offsetWidth);
	var TB_RESIZE = (TB_WIDTH > 960 ? 0 : 1);

	all_sites_html(TB_WIDTH);

	function Resize() {
		TB_WIDTH = I(document.getElementById("frametable").offsetWidth);
		var tmp = (TB_WIDTH > 960 ? 0 : 1);
		if (tmp != TB_RESIZE) {
			all_sites_html(TB_WIDTH);
			TB_RESIZE = tmp;
		}
	}

	function all_sites_html(w) {
		var cols = w > 960 ? 13 : 6;
		var tabletext = 
		"<table>" +
		"<tr class=\"trhead\">" +
			(w > 960 ? "<td class=\"tdhmid\" style=\"width:3%\" >No</td>" : "") +
			"<td class=\"tdhlt\"  style=\"width:12%\">" + LAN["Site Name"] + "</td>" +
			(w > 960 ? "<td class=\"tdhlt\"  style=\"width:12%\">" + LAN["Data Center"] + "</td>" +
			"<td class=\"tdhlt\"  style=\"width:10%\">" + LAN["Timezone"] + "</td>" : "") +
			"<td class=\"tdhlt\"  style=\"width:7%\" ></td>" +
			"<td class=\"tdhmid\" style=\"width:7%\" >" + LAN["PV"] + "</td>" +
			"<td class=\"tdhmid\" style=\"width:7%\" >" + LAN["UV"] + "</td>" +
			"<td class=\"tdhmid\" style=\"width:7%\" >" + LAN["Visits"] + "</td>" +
			"<td class=\"tdhmid\" style=\"width:7%\" >" + LAN["RV"] + "</td>" +
			(w > 960 ? "<td class=\"tdhmid\" style=\"width:7%\" >" + LAN["Report"] + "</td>" +
			"<td class=\"tdhmid\" style=\"width:21%\" colspan=\"3\">" + LAN["Settings"] + "</td>" : "") +
		"</tr>";
		
		var i = SITES.length;
		if (i) {
			var n = true;
			var c = 1;
			for (var row = 0; row < i; row++) {
				tabletext += 
					(n ? "<tr class=\"tra\">" : "<tr class=\"trb\">") +
					(w > 960 ? "<td class=\"tdmid\">" + c + "</td>" : "") +
					"<td class=\"tdlt\" ><a class=\"taba\" href=\"" + SITES[row]["Url"] + "&menu=Dashboard&action=Dashboard\">" + SITES[row]["SiteName"] + "</a></td>" +
					(w > 960 ? "<td class=\"tdlt\" >" + SITES[row]["DataCenter"] + "</td>" +
					"<td class=\"tdlt\" >" + SITES[row]["TimeZone"] + "</td>" : "") +
					"<td class=\"tdltnoborder\">" +
						"<p>" + LAN["Today"] + "</p>" +
						"<p class=\"plast\">" + LAN["Yesterday"] + "</p>" +
					"</td>" +
					"<td class=\"tdmidnoborder\">" +
						"<p>" + SITES[row]["PV0"] + "</p>" +
						"<p class=\"plast\">" + SITES[row]["PV1"] + "</p>" +
					"</td>" +
					"<td class=\"tdmidnoborder\">" +
						"<p>" + SITES[row]["UV0"] + "</p>" +
						"<p class=\"plast\">" + SITES[row]["UV1"] + "</p>" +
					"</td>" +
					"<td class=\"tdmidnoborder\">" +
						"<p>" + SITES[row]["VS0"] + "</p>" +
						"<p class=\"plast\">" + SITES[row]["VS1"] + "</p>" +
					"</td>" +
					"<td class=\"tdmidnoborder\">" +
						"<p>" + SITES[row]["RV0"] + "</p>" +
						"<p class=\"plast\">" + SITES[row]["RV1"] + "</p>" +
					"</td>" +
					(w > 960 ? "<td class=\"tdmid\" ><a class=\"taba\" href=\"" + SITES[row]["Url"] + "&menu=Dashboard&action=Dashboard\">" + LAN["View"] + "</a></td>" +
					"<td class=\"tdmidnoborder\" ><a class=\"taba\" href=\"" + SITES[row]["Url"] + "&menu=Sites&action=Get JS Code\">" + LAN["JS Code"] + "</a></td>" +
					"<td class=\"tdmidnoborder\" ><a class=\"taba\" href=\"" + SITES[row]["Url"] + "&menu=Sites&action=Domains\">" + LAN["Set"] + "</a></td>" +
					"<td class=\"tdmidnoborder\" ><a class=\"taba\" href=\"" + SITES[row]["Url"] + "&menu=Sites&action=Delete Site\">" + LAN["Delete"] + "</a></td>" : "") +
					"</tr>";
				
				c++;
				n = !n;
			}
			
			tabletext += "<tr class=\"trfoot\"><td class=\"tdrt\" colspan=\"" + cols + "\"></td></tr></table>";
		} else {
			tabletext = "No Data";
		}

		document.getElementById("frametable").innerHTML = tabletext;
	}
	
</script>';

}


function get_tz_city($tz) {
		
		global $TIMEZONES;
		foreach ($TIMEZONES as $key => $value) {
			if ($value == $tz) {
				return $key;
			}
		}
		return;
}

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