<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Common PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/19/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

isset($_SESSION['admin']) || die(header('Location: relogin.php?tip=Session has expired, Please re-login.'));
function get_guide_menu_html() {
	$menu_html = '<div id="accountmenu" class="popup"><span>' . $GLOBALS['USER'] . '</span>';	
	if ($_SESSION['admin'] == 2 || $_SESSION['admin'] == 4) {
		$menu_html .= '<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=Settings&action=Settings">' . $GLOBALS['language']['guideSettings'] . '</a>';
	} else if ($_SESSION['admin'] == 1 || $_SESSION['admin'] == 0) {
		$menu_html .= '<a href="login.php">' . $GLOBALS['language']['guideLogin'] . '</a>';
	}
	$menu_html .= '<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=Settings&action=About CA"  name="About CA">' . $GLOBALS['language']['submenuSettingsAboutCA'] . '</a>';
	$menu_html .= '<a href="logout.php">' . $GLOBALS['language']['guideLogOut'] . '</a>
			</div>';
	$menu_html .= '<div id="languagemenu" class="popup">
				<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=en-US">English</a>
				<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=zh-CN">简体中文</a>
				<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=zh-TW">繁體中文</a>
				<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=de-DE">Deutsch</a>
				<a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=ja-JP">日本語</a>
			</div>';
	return $menu_html;
}
function get_side_menu_html() {
	$menu_url =  'manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'];
	return '<div id="leftbox" class="body_left_side">
		<div id="side_menu_bar" class="div_side_menu_bar">
			<div class="div_logo">
				<a href="#" onclick="switch_menu()"><img class="img_logo" src="images/logo-free.png" alt="Centcount Analytics Professional Pro Logo" /></a>
			</div>
			<div id="side_menu" class="div_side_menu">
			<ul style="display:block;">
				<li><a class="'. ($GLOBALS['MENU']=='Dashboard' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Dashboard">'.$GLOBALS['language']['menuDashboard'].'</a>
					<ul '. ($GLOBALS['MENU']=='Dashboard' ? 'style="display:block;"' : '').'>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Dashboard&action=Dashboard" name="Dashboard">'.$GLOBALS['language']['submenuDashboardDashboard'].'</a></li>
					</ul>
				</li>
				<li><a class="'. ($GLOBALS['MENU']=='Visitor' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Visitor">'.$GLOBALS['language']['menuVisitor'].'</a>
					<ul '. ($GLOBALS['MENU']=='Visitor' ? 'style="display:block;"' : '').'>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Visits Overview" name="Visits Overview">'.$GLOBALS['language']['submenuVisitorVisitsOverview'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Day Trend" name="Day Trend">'.$GLOBALS['language']['submenuVisitorDayTrend'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Realtime Visitor" name="Realtime Visitor">'.$GLOBALS['language']['submenuVisitorRealtimeVisitor'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Visitor Log" name="Visitor Log">'.$GLOBALS['language']['submenuVisitorVisitorLog'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Robot Log" name="Robot Log">'.$GLOBALS['language']['submenuVisitorRobotLog'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Devices" name="Devices">'.$GLOBALS['language']['submenuVisitorDevices'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Software" name="Software">'.$GLOBALS['language']['submenuVisitorSoftware'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Locations" name="Locations">'.$GLOBALS['language']['submenuVisitorLocations'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Times" name="Times">'.$GLOBALS['language']['submenuVisitorTimes'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Visitor Map" name="Visitor Map">'.$GLOBALS['language']['submenuVisitorVisitorMap'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Visitor&action=Active Visitor" name="Active Visitor">'.$GLOBALS['language']['submenuVisitorActiveVisitor'].'</a></li>
					</ul>
				</li>
				<li><a class="'. ($GLOBALS['MENU']=='Action' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Action">'.$GLOBALS['language']['menuAction'].'</a>
					<ul '. ($GLOBALS['MENU']=='Action' ? 'style="display:block;"' : '').'>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Action&action=All Pages" name="All Pages">'.$GLOBALS['language']['submenuActionAllPages'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Action&action=Entry Pages" name="Entry Pages">'.$GLOBALS['language']['submenuActionEntryPages'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Action&action=Bounce Pages" name="Bounce Pages">'.$GLOBALS['language']['submenuActionBouncePages'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Action&action=Exit Pages" name="Exit Pages">'.$GLOBALS['language']['submenuActionExitPages'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Action&action=Robot Crawled Pages" name="Robot Crawled Pages">'.$GLOBALS['language']['submenuActionRobotCrawledPages'].'</a></li>
					</ul>
				</li>
				<li><a class="'. ($GLOBALS['MENU']=='Referrer' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Referrer">'.$GLOBALS['language']['menuReferrer'].'</a>
					<ul '. ($GLOBALS['MENU']=='Referrer' ? 'style="display:block;"' : '').'>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Referrer&action=All Referrers" name="All Referrers">'.$GLOBALS['language']['submenuReferrerAllReferrers'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Referrer&action=Search Engines" name="Search Engines">'.$GLOBALS['language']['submenuReferrerSearchEngines'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Referrer&action=Websites" name="Websites">'.$GLOBALS['language']['submenuReferrerWebsites'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Referrer&action=Channels" name="Channels">'.$GLOBALS['language']['submenuReferrerChannels'].'</a></li>
					</ul>
				</li>'.
				($_SESSION['admin'] > 1 ? 
				'<li><a class="'. ($GLOBALS['MENU']=='Sites' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Sites">'.$GLOBALS['language']['menuManageSites'].'</a>
					<ul '. ($GLOBALS['MENU']=='Sites' ? 'style="display:block;"' : '').'>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=All Sites"  name="All Sites">'.$GLOBALS['language']['submenuSitesAllSites'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Add Site"  name="Add Site">'.$GLOBALS['language']['submenuSitesAddSite'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Site Configuration"  name="Site Configuration">'.$GLOBALS['language']['submenuSitesSiteConfiguration'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Get JS Code"  name="Get JS Code">'.$GLOBALS['language']['submenuSitesGetJSCode'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Domains"  name="Domains">'.$GLOBALS['language']['submenuSitesDomains'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Blocked Sites"  name="Blocked Sites">'.$GLOBALS['language']['submenuSitesBlockedSites'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Blocked Pages"  name="Blocked Pages">'.$GLOBALS['language']['submenuSitesBlockedPages'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Blocked IPs"  name="Blocked IPs">'.$GLOBALS['language']['submenuSitesBlockedIPs'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Blocked IDs"  name="Blocked IDs">'.$GLOBALS['language']['submenuSitesBlockedIDs'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Filtered Domains"  name="Filtered Domains">'.$GLOBALS['language']['submenuSitesFilteredDomains'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Visitor Password"  name="Visitor Password">'.$GLOBALS['language']['submenuSitesVisitorPassword'].'</a></li>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Sites&action=Delete Site"  name="Delete Site">'.$GLOBALS['language']['submenuSitesDeleteSite'].'</a></li>
					</ul>
				</li>' : ''). 
				($_SESSION['admin'] == 4 ? '
				<li><a class="'. ($GLOBALS['MENU']=='Hosts' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Hosts">'.$GLOBALS['language']['menuManageHosts'].'</a>
					<ul '. ($GLOBALS['MENU']=='Hosts' ? 'style="display:block;"' : '').'>
						<li><a class="submenubtn" href="'.$menu_url.'&menu=Hosts&action=Host Status" name="Host Status">'.$GLOBALS['language']['submenuHostsHostStatus'].'</a></li>
					</ul>
				</li>' : '').
				'<li><a class="'. ($GLOBALS['MENU']=='Language' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Language">'.$GLOBALS['language']['menuLanguage'].'</a>
					<ul '. ($GLOBALS['MENU']=='Language' ? 'style="display:block;"' : '').'>
						<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=en-US">English</a></li>
						<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=zh-CN">简体中文</a></li>
						<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=zh-TW">繁體中文</a></li>
						<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=de-DE">Deutsch</a></li>
						<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=' . $GLOBALS['MENU'] . '&action=' . $GLOBALS['ACTION'] . '&lan=ja-JP">日本語</a></li>
					</ul>
				</li>
				<li><a class="'. ($GLOBALS['MENU']=='Settings' ? 'menu_shown' : 'menu_hidden').'" onclick="menu_click(this)" name="Settings">'.$GLOBALS['language']['menuSettings'].'</a>
					<ul '. ($GLOBALS['MENU']=='Settings' ? 'style="display:block;"' : '').'>'.
						($_SESSION['admin'] == 2 || $_SESSION['admin'] == 4 ? 
						'<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=Settings&action=Settings">' . $GLOBALS['language']['guideSettings'] . '</a></li>' : 
						'<li><a href="login.php">' . $GLOBALS['language']['guideLogin'] . '</a></li>').
						'<li><a href="https://www.centcount.com/doc.php" target="_blank" title="Help" alt="Help">' . $GLOBALS['language']['guideDoc'] . '</a>
						<li><a href="manager.php?id=' . $GLOBALS['USERID'] . '&siteid=' . $GLOBALS['SITEID'] . '&menu=Settings&action=About CA"  name="About CA">' . $GLOBALS['language']['submenuSettingsAboutCA'] . '</a></li>
						<li><a href="logout.php">' . $GLOBALS['language']['guideLogOut'] . '</a></li>
					</ul>
				</li>
			</ul>
			</div>
			<div id="copyright" class="div_copyright">
				Powered by <a href="https://www.centcount.com">Centcount Analytics</a>
			</div>
		</div>
		<div id="hidebtn" class="div_side_menu_hide_btn" onclick="switch_menu()">
			<div class="title">
				'.$GLOBALS['language']['submenu'.$GLOBALS['MENU']. str_replace(' ', '', $GLOBALS['ACTION'])].'
			</div>
		</div>
	</div>';
}
function get_sites_menu_html() {
		$i = count($GLOBALS['SITES']);
		$sn = '';
		if ($i) {	
			if ($_SESSION['admin'] == 2 || $_SESSION['admin'] == 4) {
				for ($row = 0; $row < $i; $row++) {
					if (empty($GLOBALS['SITEID'])) $GLOBALS['SITEID'] = $GLOBALS['SITES'][$row]['SiteID'];
					if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
						$sn .= '<a class="active">' . $GLOBALS['SITES'][$row]['SiteName'] . '</a>';
					} else {
						$sn .= '<a onclick="selectSite(' . $GLOBALS['SITES'][$row]['SiteID'] . ')">' . $GLOBALS['SITES'][$row]['SiteName'] . '</a>';
					}
				}
				return $sn;
			} else if ($_SESSION['admin'] < 2) {
				for ($row = 0; $row < $i; $row++) {
					if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
						$sn .= '<a class="active">' . $GLOBALS['SITES'][$row]['SiteName'] . '</a>';
						break;
					}
				}
				return $sn;
			}
		}
		return '';
}
function get_hosts_menu_html() {
		$i = count($GLOBALS['HOSTS']);
		$sn = '';
		if ($i) {	
			if ($_SESSION['admin'] == 1 || $_SESSION['admin'] == 4) {
				for ($row = 0; $row < $i; $row++) {
					if ($GLOBALS['HOSTS'][$row]['Domain'] == $GLOBALS['DOMAIN_NAME']) {
						$sn .= '<a class="active">' . $GLOBALS['HOSTS'][$row]['Hostname'] . '</a>';
						$_SESSION['HOSTNAME'] = $GLOBALS['HOSTS'][$row]['Hostname'];
					} else {
						$sn .= '<a onclick="selectHost(\'' . $GLOBALS['HOSTS'][$row]['Domain'] . '\')">' . $GLOBALS['HOSTS'][$row]['Hostname'] . '</a>';
					}
				}
				return $sn;
			} 
		}
		return '';
}
function get_option_html() {
		switch($GLOBALS['MENU']) {
		case 'Settings':
			return '';
			break;
		case 'Hosts':
			switch($GLOBALS['ACTION']) {
			default:
			case 'Hosts':
			case 'Add Host':
				return '';
				break;
			case 'Host Status':
				return '
					<div class="frameoptionsites">
					<div id="option" class="frameoption"> 
						<div class="d_clone"> 
							<div id="sites" class="d_frame" onclick="DropDown(this, \'hostsmenu\')"> 
								<div class="dd_title">'. $GLOBALS['indicator']['HOST'] .' : ' . $_SESSION['HOSTNAME'] . '</div> 
								<div id="hostsmenu" class="dd_body"> 
									' . get_hosts_menu_html() . ' 
								</div> 
							</div> 
						</div>
						<div id="refresh" class="d_refresh" onclick="RefreshPage()" ></div> 
					</div></div>';
				break;
			}
			break;	
		}
		if (empty($GLOBALS['SITEID'])) return '<div class="framespace"></div>';
		return '
			<div class="frameoptionsites">
				<div id="option" class="frameoption"> 
					<div class="d_clone"> 
						<div id="sites" class="d_frame" onclick="DropDown(this, \'sitesmenu\')"> 
							<div class="dd_title">'. $GLOBALS['indicator']['WEBSITE'] .' : ' . $GLOBALS['SITENAME'] . '</div> 
							<div id="sitesmenu" class="dd_body"> 
								' . get_sites_menu_html() . ' 
							</div> 
						</div> 
					</div> 
					<div class="d_clone"> 
						<div id="period" class="d_frame"> 
							<div id="period_date" class="dp_title" onClick="from_date.Draw();to_date.Draw();from_dp.Status();"><span id="period_from">' . $GLOBALS['TODAY'] . '</span><span id="period_to">' . $GLOBALS['TODAY'] . '</span></div>
							<div class="d_body"> 
								<div style="width:auto; height:auto; line-height:22px; margin-right:25px;">
									<a href="javascript:SetPeriod(0);" style="margin-right:5px; color:#555;">'. $GLOBALS['indicator']['Today'] .' </a>
									<a href="javascript:SetPeriod(1);" style="margin-left:5px; margin-right:5px; color:#555;">'. $GLOBALS['indicator']['Yesterday'] .' </a>
									<a href="javascript:SetPeriod(2);" style="margin-left:5px; margin-right:5px; color:#555;">'. $GLOBALS['indicator']['Past 7 Days'] .' </a>
									<a href="javascript:SetPeriod(3);" style="margin-left:5px; margin-right:5px; color:#555;">'. $GLOBALS['indicator']['Past 30 Days'] .' </a>
									<a href="javascript:SetPeriod(4);" style="margin-left:5px; margin-right:5px; color:#555;">'. $GLOBALS['indicator']['Last Week'] .' </a>
									<a href="javascript:SetPeriod(5);" style="margin-left:5px; color:#555;">'. $GLOBALS['indicator']['Last Month'] .' </a>
								</div>
								<div class="c_body"> 
									<div class="c_box"> 
										<div class="c_year"> 
											<ul> 
												<li><a id="last_from" class="last" onclick="from_date.LastMonth()"></a></li> 
												<li><a id="year_from" class="year" onclick="selectButton(this)">2001</a> 
													<ul id="yearlist_from"> 
														??? 
													</ul> 
												</li> 
												<li><a id="month_from" class="year" onclick="selectButton(this)">01</a> 
													<ul id="monthlist_from"> 
														??? 
													</ul> 
												</li> 
												<li><a id="next_from" class="next" onclick="from_date.NextMonth()"></a></li> 
											</ul> 
										</div> 
									</div> 
									<div id="weekday_from" class="c_box"> 
										<weekday>'. $GLOBALS['indicator']['Sun'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Mon'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Tue'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Wed'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Thu'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Fri'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Sat'] .'</weekday> 
									</div> 
									<div id="day_from" class="c_box" style="height:180px;"> 
										??? 
									</div> 
								</div>
								<div class="c_body"> 
									<div class="c_box"> 
										<div class="c_year"> 
											<ul> 
												<li><a id="last_to" class="last" onclick="to_date.LastMonth()"></a></li> 
												<li><a id="year_to" class="year" onclick="selectButton(this)">2001</a> 
													<ul id="yearlist_to"> 
														
													</ul> 
												</li> 
												<li><a id="month_to" class="year" onclick="selectButton(this)">01</a> 
													<ul id="monthlist_to"> 
														
													</ul> 
												</li> 
												<li><a id="next_to" class="next" onclick="to_date.NextMonth()"></a></li> 
											</ul> 
										</div> 
									</div> 
									<div id="weekday_to" class="c_box"> 
										<weekday>'. $GLOBALS['indicator']['Sun'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Mon'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Tue'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Wed'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Thu'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Fri'] .'</weekday> 
										<weekday>'. $GLOBALS['indicator']['Sat'] .'</weekday> 
									</div> 
									<div id="day_to" class="c_box" style="height:180px;"> 
										??? 
									</div> 
								</div>
								<div style="width:100%; height:1px; float:none;"></div>
								<div style="width:100%; height:auto; margin-right:20px; float:left;">
									<a href="javascript:from_dp.Status();SetDay(0);" class="d_btn">OK</a>
									<a href="javascript:from_dp.Status();SetDay(1);" class="d_btn">Cancel</a>
								</div>
							</div>
						</div> 
					</div>
					<div id="refresh" class="d_refresh" onclick="RefreshPage()" ></div> 
				</div>
			</div>';
}
function SDATA($val, $opt, $maxL=0, $minL=0, $con=0) {
	if ($con) {
		$val = rawurldecode($val);
		$encoding = mb_detect_encoding($val, 'UTF-8, GB18030', true);
		switch ($encoding) {
		case 'UTF-8':
			break;
		case 'GB18030':
			$val = mb_convert_encoding($val, 'UTF-8', 'GB18030');
			break;
		default:
			$val = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
			break;
		}
		if (mb_detect_encoding($val, 'UTF-8', true) === false) return ''; 
	}
	switch ($opt) {
	case 0:
		if ($con) $val = mysqli_real_escape_string($con, $val);
		$mval = filter_var($val, FILTER_SANITIZE_STRING);
		if (mb_strlen($mval,'UTF-8') !== mb_strlen($val,'UTF-8')) {
			return '';
		} else {
			if (mb_strlen($val,'UTF-8') > $maxL) {
				return mb_substr($val, 0, $maxL, 'UTF-8');
			} else {
				return $val;
			}
		}
	case 1:
		$val = filter_var($val, FILTER_SANITIZE_STRING);
		if ($con) $val = mysqli_real_escape_string($con, $val);
		if (mb_strlen($val,'UTF-8') > $maxL) {
			return mb_substr($val, 0, $maxL, 'UTF-8');
		} else {
			return $val;
		}
	case 2:
		$tmp = (int)$val;
		return ($tmp > $maxL || $tmp < $minL) ? 0 : $tmp;
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
		if ($con) $mval = mysqli_real_escape_string($con, $mval);
		if (strlen($mval) === $maxL) {
			return $val;
		} else {
			exit;
		}
	case 5:
		$mval = filter_var($val, FILTER_VALIDATE_EMAIL);
		if ($con) $mval = mysqli_real_escape_string($con, $mval);
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
			return false;
		} else {
			return $tmp;
		}
	case 7:
		if (mb_strlen($val,'UTF-8') > $maxL) {
			return mb_substr($val, 0, $maxL, 'UTF-8');
		} else {
			return $val;
		}
	}
	return NULL;
}
function check_sites() {
		$GLOBALS['PERIOD_FROM'] = '';
		$GLOBALS['PERIOD_TO'] = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
	 		die('Could not connect database host. Please contact Administrator!');
	 	}
		$db_selected = mysqli_select_db($con, DB_NAME_USER);
		if (!$db_selected) {
			mysqli_close($con);
			die( 'Could not select database. Please contact Administrator!');
		} else {
			$result = mysqli_query($con, "SELECT * FROM st{$GLOBALS['SITELIST_TABLE']} WHERE UserID={$GLOBALS['USERID']}");
			if ($result && mysqli_num_rows($result)) {
				$GLOBALS['SITES'] = array();
				while ($row = mysqli_fetch_assoc($result)) {
					$GLOBALS['SITES'][] = $row; 
				}
				mysqli_free_result($result);
			}
			mysqli_close($con);
		}
		if (empty($GLOBALS['SITES']) && $GLOBALS['ACTION'] !== 'Add Site' && $GLOBALS['MENU'] !== 'Hosts') {
			header("Location: manager.php?id={$GLOBALS['USERID']}&menu=Sites&action=Add Site"); 
			exit;
		} else if (!empty($GLOBALS['SITES']) && count($GLOBALS['SITES']) > 0) {
			if (empty($GLOBALS['SITEID'])) {
				$GLOBALS['SITEID']		= $GLOBALS['SITES'][0]['SiteID'];
				$GLOBALS['TIMEZONE']	= $GLOBALS['SITES'][0]['TimeZone'];
				$GLOBALS['SITENAME']	= $GLOBALS['SITES'][0]['SiteName'];
				$GLOBALS['PERIOD_FROM'] = $GLOBALS['SITES'][0]['CreateTime'];
			} else {
				$i = count($GLOBALS['SITES']);
				$t = 0;
				for ($row = 0; $row < $i; $row++) {
					if ($GLOBALS['SITEID'] == $GLOBALS['SITES'][$row]['SiteID']) {
						$GLOBALS['TIMEZONE']	= $GLOBALS['SITES'][$row]['TimeZone'];
						$GLOBALS['SITENAME']	= $GLOBALS['SITES'][$row]['SiteName'];
						$GLOBALS['PERIOD_FROM'] = $GLOBALS['SITES'][$row]['CreateTime'];
						$t = 1;
						break;
					}
				}
				if ($t == 0) $GLOBALS['SITEID'] = 0;
			}
		}
		if (!empty($GLOBALS['SITEID'])) {
			date_default_timezone_set($GLOBALS['TIMEZONE']) ? $today = time() : exit;
			$GLOBALS['TODAY'] = date("Y-m-d", $today);
			$GLOBALS['PERIOD_TO'] = date("Ymd", $today);
			$GLOBALS['PERIOD_FROM'] = date("Ymd", $GLOBALS['PERIOD_FROM']);
		}
}
function get_sites() {
		$GLOBALS['SITES'] = array();
		$err = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
	 		die('Could not connect database host. Please contact Administrator! - Common Function[get_sites]');
	 	}
		$db_selected = mysqli_select_db($con, DB_NAME_USER);
		if (!$db_selected) {
			mysqli_close($con);
			die('Could not select database. Please contact Administrator! - Common Function[get_sites]');
		} else {
			$result = mysqli_query($con, "SELECT * FROM st{$GLOBALS['SITELIST_TABLE']} WHERE UserID={$GLOBALS['USERID']}");
			if ($result && mysqli_num_rows($result)) {
				while ($row = mysqli_fetch_assoc($result)) {
					$GLOBALS['SITES'][] = $row;
				}
				mysqli_free_result($result);	
			}
			mysqli_close($con);
		}
		return true;
}
function get_hosts() {
		$err = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
	 		die('Could not connect database. Please contact Administrator! - Common Function[get_hosts]');
	 	}
		$db_selected = mysqli_select_db($con, DB_NAME_USER);
		if (!$db_selected) {
			mysqli_close($con);
			die('Could not use database. Please contact Administrator! - Common Function[get_hosts]');
		} else {
			$result = mysqli_query($con, "SELECT * FROM host");
			if ($result && mysqli_num_rows($result)) {
				$GLOBALS['HOSTS'] = array();
				while ($row = mysqli_fetch_assoc($result)) {
					$GLOBALS['HOSTS'][] = $row;
					if ($row['Domain'] == $GLOBALS['DOMAIN_NAME']) $_SESSION['HOSTNAME'] = $row['Hostname'];
				}
				mysqli_free_result($result);	
			} else if ($GLOBALS['ACTION'] !== 'Add Host') {
				mysqli_close($con);
				header("Location: manager.php?id={$GLOBALS['USERID']}&menu=Hosts&action=Add Host"); 
				exit;
			}
			mysqli_close($con);
		}
		return true;
}
function get_datacenter_region($server) {
	$len = count($GLOBALS['HOSTS']);
	for ($i=0; $i<$len; $i++) if ($GLOBALS['HOSTS'][$i]['Domain'] == $server) return $GLOBALS['HOSTS'][$i]['Hostname'];
	return '';
}
function get_site_info($info) {
		if (empty($GLOBALS['SITES'])) return '';
		$i = count($GLOBALS['SITES']);
		for ($row = 0; $row < $i; $row++) {
			if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
				return $GLOBALS['SITES'][$row][$info];
			}
		}
		return '';
}
function get_domains() {
		$v = get_visa();
		$host = get_site_info('DataCenter');
		$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
		$q = $v . 'sid='.$GLOBALS['SITEID'].'&q=get domain';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
		$ret = curl_exec($ch);
		$GLOBALS['DOMAINS'] = empty($ret) ? '' : json_decode($ret, true);
		curl_close($ch);
}
function check_table($con, $tb, $db) {
		$ret = false;
		$sql = "SHOW TABLES FROM {$db}";
		$result = mysqli_query($con, $sql);
		if ($result && mysqli_num_rows($result)) {
			while ($row = mysqli_fetch_row($result)) {
				if ($row[0] == $tb) {
					$ret = true;
					break;
				}
			}
			mysqli_free_result($result);
		}
		return $ret;
}
function con_db($host, $username, $pw) {
		$con = mysqli_connect($host, $username, $pw);
		if (mysqli_connect_errno($con)) {
			$GLOBALS['ERROR'] .= '<br/>Could not connect mysql. Please contact Administrator! - Common Function[con_db]';
			return 0;
 		}
		return $con;
}
function use_db($host, $username, $pw, $db, $mod, $func) {
		$con = mysqli_connect($host, $username, $pw);
		if (mysqli_connect_errno($con)) {
			$GLOBALS['ERROR'] .= "<br/>Could not connect mysql - Common Function[use_db], Called From Module[{$mod}] Function[{$func}]";
			return 0;
 		}
		$db_selected = mysqli_select_db($con, $db);
		if (!$db_selected) {
			$GLOBALS['ERROR'] .= "<br/>Could not select database - Common Function[use_db], Called From Module[{$mod}] Function[{$func}]";
			mysqli_close($con);
			return 0;
		}
		return $con;
}
function verify_login_for_change($pw, &$success) {
		$success = false;
		$err = '';
		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
 			return 'Could not connect mysql. Please contact Administrator! - Common Function[verify_login_for_change]';
 		}
		$db_selected = mysqli_select_db($con, DB_NAME_USER);
		if (!$db_selected) {
 			$err .= '<br/>Could not select database. Please contact Administrator! - Common Function[verify_login_for_change]';
			mysqli_close($con);
			return $err;
		}
		$result = mysqli_query($con, "SELECT * FROM User WHERE UserID={$GLOBALS['USERID']}");
		if ($result && mysqli_num_rows($result) == 1) {
			while ($row = mysqli_fetch_array($result))
			{
				$apw = $row['Password'];
				break;
			}
			mysqli_free_result($result);
			mysqli_close($con);
		} else {
			$err .= '<br/>Login failed! User is not existed';
			mysqli_close($con);
			return $err;
		}
		$md5pw = md5($pw);
		if ($md5pw === $apw && strlen($md5pw) !== 0) {
			$success = true;
		} else {
			$err .= '<br/>Wrong password';
		}
		return $err;
}
function get_visa($sid=0) {
		if ($sid === 0) $sid = $GLOBALS['SITEID'];
		if (!$sid) return false;
		$t = time() + 30;
		$v = md5($sid . $t . ENCODE_FACTOR);
		return 't=' . $t . '&v=' . $v . '&';
}
function get_cahm_visa() {	
		if (empty($GLOBALS['SITEID'])) return false;
		$t = time() + 86400;
		$pass = md5($GLOBALS['SITEID'] . $t . ENCODE_FACTOR);
		return 'cahm_visa=' . $pass . $t;
}
function verify_user() {
		if (!isset($_SESSION['admin'])) {
			return false;
		}
		if (empty($_SESSION['visa'])) {
			return false;
		} else {
			$visa = $_SESSION['visa'];
		}
		if (empty($_SESSION['r'])) {
			return false;
		} else {
			$r = $_SESSION['r'];
		}
		if (empty($GLOBALS['USERID'])) return false;
		if ($_SESSION['admin'] === 4) {
			$matchvisa = md5($r . ENCODE_FACTOR);
			if ($visa === $matchvisa) return true;
		} else if ($_SESSION['admin'] === 2) {
			$matchvisa = md5($GLOBALS['USERID'] . $r . ENCODE_FACTOR);
			if ($visa === $matchvisa) return true;
		} else if ($_SESSION['admin'] < 2) {
			if (empty($GLOBALS['SITEID'])) return false;
			$matchvisa = md5($GLOBALS['USERID'] . $GLOBALS['SITEID'] . $r . ENCODE_FACTOR);
			if ($visa === $matchvisa) return true;
		}
		return false;
}
function get_ip() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER)) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if ((bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
						return $ip;
					}
				}
			}
		}
		return '';
}

	
?> 