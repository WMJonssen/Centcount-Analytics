<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Manager PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/19/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

session_name('CASESSID');
session_start();
header('Content-type: text/html; charset=utf-8');

@require './config/config_security.php';
@require './config/config_common.php';
require './language.php';
@require './common.php';
require './html.php';

	$GLOBALS['ERROR'] = '';

	$GLOBALS['USERID'] = empty($_GET['id']) ? false : SDATA($_GET['id'], 6);
	$GLOBALS['SITEID'] = empty($_GET['siteid']) ? 0 : SDATA($_GET['siteid'], 6);
	if ($GLOBALS['USERID'] === false || $GLOBALS['SITEID'] === false) {
		die(header('Location: relogin.php?tip=Access denied, Please re-login.'));//echo 'Unauthorized access, please relogin.';
	}
	

	if (!verify_user()) {
		if (isset($_POST['param'])) die(header('Location: relogin.php?tip=Session has expired, Please re-login.'));
		die(header('Location: relogin.php?tip=Unauthorized access, Please re-login.'));
	}


	$GLOBALS['ACTION'] = empty($_GET['action']) ? '' : SDATA($_GET['action'], 0, 32);
	$GLOBALS['MENU']   = empty($_GET['menu']) ? '' : SDATA($_GET['menu'], 0, 32);
	$GLOBALS['PARAM']  = empty($_GET['param']) ? '' : SDATA($_GET['param'], 0, 32);
	
	$GLOBALS['USER']   = empty($_SESSION['user']) ? '' : $_SESSION['user'];
	if ($GLOBALS['USER'] === '') die("User ID: {$GLOBALS['USERID']} - Login failed, Can not get Username. Error No: 1010");
	$GLOBALS['SITELIST_TABLE'] = empty($_SESSION['stb']) ? '' : $_SESSION['stb'];
	if ($GLOBALS['SITELIST_TABLE'] === '') die("User ID: {$GLOBALS['USERID']} - Login failed, Can not get SiteTB. Error No: 1011");
	
	check_sites();
	$_SESSION['DATACENTER'] = get_site_info('DataCenter');
	$GLOBALS['DOMAIN_NAME'] = empty($GLOBALS['PARAM']) ? $_SESSION['DATACENTER'] : $GLOBALS['PARAM'];


	
	if (isset($_SESSION['admin'])) {
		if ($_SESSION['admin'] === 2 || $_SESSION['admin'] === 4) {
			if ($GLOBALS['MENU'] === '') $GLOBALS['MENU'] = 'Sites';
		} else if ($_SESSION['admin'] === 0 || $_SESSION['admin'] === 1) {
			if ($GLOBALS['MENU'] === '' || $GLOBALS['MENU'] === 'Sites') $GLOBALS['MENU'] = 'Dashboard';
		} else {
			die(header('Location: relogin.php?tip=Unauthorized access, Please re-login.'));
		}
	} else {
		die(header('Location: relogin.php?tip=Unauthorized access, Please re-login.'));
	}
	

	switch ($GLOBALS['MENU']) {
	case 'Dashboard':
		switch ($GLOBALS['ACTION']) {
		default:
			$GLOBALS['ACTION'] = 'Dashboard';
		case 'Dashboard':
			include './report/dashboard.php';
			$GLOBALS['ERROR'] .= dashboard_html();
			break;
			
		}
		break;

	case 'Visitor':
		switch ($GLOBALS['ACTION']) {
		default:
			$GLOBALS['ACTION'] = 'Visits Overview';
		case 'Visits Overview':
			include './report/visitors_overview.php';
			$GLOBALS['ERROR'] .= visitors_overview_html();
			break;

		case 'Day Trend':
			include './report/visitors_day_trend.php';
			$GLOBALS['ERROR'] .= visitors_day_trend_html();
			break;
			
		case 'Realtime Visitor':
			include './report/visitors_realtime.php';
			$GLOBALS['ERROR'] .= visitors_realtime_html();
			break;
			
		case 'Visitor Log':
			include './report/visitors_log.php';
			$GLOBALS['ERROR'] .= visitors_log_html();
			break;
			
		case 'Returning Visitor Log':
			include './report/visitors_rv_log.php';
			$GLOBALS['ERROR'] .= visitors_rv_log_html();
			break;
			
		case 'Robot Log':
			include './report/visitors_robot_log.php';
			$GLOBALS['ERROR'] .= visitors_robot_log_html();
			break;
			
		case 'Devices':
			include './report/visitors_device.php';
			$GLOBALS['ERROR'] .= visitors_device_html();
			break;
			
		case 'Software':
			include './report/visitors_software.php';
			$GLOBALS['ERROR'] .= visitors_software_html();
			break;
			
		case 'Locations':
			include './report/visitors_location.php';
			$GLOBALS['ERROR'] .= visitors_location_html();
			break;
			
		case 'Times':
			include './report/visitors_times.php';
			$GLOBALS['ERROR'] .= visitors_times_html();
			break;

		case 'Visitor Map':
			include './report/visitors_map.php';
			$GLOBALS['ERROR'] .= visitors_map_html();
			break;

		case 'Active Visitor':
			include './report/visitors_active_visitor.php';
			$GLOBALS['ERROR'] .= visitors_active_visitor_html();
			break;

		}
		break;
		
	case 'Action':
		switch ($GLOBALS['ACTION']) {
		default:
			$GLOBALS['ACTION'] = 'All Pages';
		case 'All Pages':
			include './report/actions_all_page.php';
			$GLOBALS['ERROR'] .= actions_all_page_html();
			break;
			
		case 'Entry Pages':
			include './report/actions_entry_page.php';
			$GLOBALS['ERROR'] .= actions_entry_page_html();
			break;
			
		case 'Bounce Pages':
			include './report/actions_bounce_page.php';
			$GLOBALS['ERROR'] .= actions_bounce_page_html();
			break;
		
		case 'Exit Pages':
			include './report/actions_exit_page.php';
			$GLOBALS['ERROR'] .= actions_exit_page_html();
			break;
			
		case 'Robot Crawled Pages':
			include './report/actions_robot_crawled_page.php';
			$GLOBALS['ERROR'] .= actions_robot_crawled_page_html();
			break;
			
		}
		break;
	
	case 'Referrer':
		switch ($GLOBALS['ACTION']) {
		default:
			$GLOBALS['ACTION'] = 'All Referrers';
		case 'All Referrers':
			include './report/referrers_all_referrer.php';
			$GLOBALS['ERROR'] .= referrers_all_referrer_html();
			break;
			
		case 'Search Engines':
			include './report/referrers_se_keyword.php';
			$GLOBALS['ERROR'] .= referrers_se_keyword_html();
			break;
			
		case 'Websites':
			include './report/referrers_website.php';
			$GLOBALS['ERROR'] .= referrers_website_html();
			break;
		
		case 'Channels':
			include './report/referrers_channel.php';
			$GLOBALS['ERROR'] .= referrers_channel_html();
			break;
		}
		break;
	
	case 'Sites':
		if ($_SESSION['admin'] != 2 && $_SESSION['admin'] != 4) {
			die(header('Location: logout.php')); 
		}

		switch ($GLOBALS['ACTION']) {
		default:
			$GLOBALS['ACTION'] = 'All Sites';
		case 'All Sites':
			get_hosts();
			include './site/sites_all_sites.php';
			$GLOBALS['ERROR'] .= all_sites_html();		
			break;

		case 'Add Site':
			get_hosts();
			include './site/sites_add_site.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= add_site($success);
				if ($success) {
					die(header("Location: manager.php?id={$GLOBALS['USERID']}&siteid={$GLOBALS['SITEID']}&menu=Sites&action=Get Code"));
				}
			}
			add_site_html();
			break;
			
		case 'Site Configuration':
			include './site/sites_modify_site_config.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= modify_site_config();
				check_sites();
			}
			site_config_html();
			break;
		
		case 'Get JS Code':
			include './site/sites_get_js_code.php';
			get_js_code_html();
			break;
		
		case 'Domains':
			include './site/sites_domains.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= domains_operation();
				$GLOBALS['ERROR'] .= get_domains();
				$GLOBALS['TEXT_BODY'] = domains_table_html();
				if ($GLOBALS['ERROR']) {
					echo $GLOBALS['ERROR'] . '<br/><br/>';
				} else {
					echo $GLOBALS['TEXT_BODY'];
				}
				exit;
			}
			$GLOBALS['ERROR'] .= get_domains();
			domains_html();
			break;	
			
		case 'Blocked Sites':
			include './site/sites_blocked_sites.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= blocked_sites_operation();
				$GLOBALS['ERROR'] .= get_domains();
				$GLOBALS['TEXT_BODY'] = blocked_sites_table_html();
				if ($GLOBALS['ERROR']) {
					echo $GLOBALS['ERROR'].'<br/><br/>';
				} else {
					echo $GLOBALS['TEXT_BODY'];
				}
				exit;
			}
			$GLOBALS['ERROR'] .= get_domains();
			blocked_sites_html();
			break;
				
		case 'Blocked Pages':
			include './site/sites_blocked_pages.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= blocked_pages_operation();
				$GLOBALS['ERROR'] .= get_domains();
				$GLOBALS['TEXT_BODY'] = blocked_pages_table_html();
				if ($GLOBALS['ERROR']) {
					echo $GLOBALS['ERROR'].'<br/><br/>';
				} else {
					echo $GLOBALS['TEXT_BODY'];
				}
				exit;
			}
			$GLOBALS['ERROR'] .= get_domains();
			blocked_pages_html();
			break;	
			
		case 'Blocked IPs':
			include './site/sites_blocked_ips.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= blocked_ips_operation();
				$GLOBALS['ERROR'] .= get_domains();
				$GLOBALS['TEXT_BODY'] = blocked_ips_table_html();
				if ($GLOBALS['ERROR']) {
					echo $GLOBALS['ERROR'].'<br/><br/>';
				} else {
					echo $GLOBALS['TEXT_BODY'];
				}
				exit;
			}
			$GLOBALS['ERROR'] .= get_domains();
			blocked_ips_html();
			break;	
			
		case 'Blocked IDs':
			include './site/sites_blocked_ids.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= blocked_ids_operation();
				$GLOBALS['ERROR'] .= get_domains();
				$GLOBALS['TEXT_BODY'] = blocked_ids();//blocked_ids_table_html();
				if ($GLOBALS['ERROR']) {
					echo '0' . $GLOBALS['ERROR'].'<br/><br/>';
				} else {
					echo '1' . $GLOBALS['TEXT_BODY'];
				}
				exit;
			}
			$GLOBALS['ERROR'] .= get_domains();
			blocked_ids_html();
			break;
			
		case 'Filtered Domains':
			include './site/sites_filtered_domains.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= filtered_domains_operation();
				$GLOBALS['ERROR'] .= get_domains();
				$GLOBALS['TEXT_BODY'] = filtered_domains_table_html();
				if ($GLOBALS['ERROR']) {
					echo $GLOBALS['ERROR'].'<br/><br/>';
				} else {
					echo $GLOBALS['TEXT_BODY'];
				}
				exit;
			}
			$GLOBALS['ERROR'] .= get_domains();
			filtered_domains_html();
			break;
			
		case 'Visitor Password':
			include './site/sites_visitor_password.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= modify_visitor_password();
				check_sites();
			}
			visitor_password_html();
			break;	
		
		case 'Delete Site':
			include './site/sites_delete_site.php';
			if ($_POST) {
				$success = false;
				$GLOBALS['ERROR'] .= delete_site($success);
				if ($success) {
					get_sites();
					die(header("Location: manager.php?id={$GLOBALS['USERID']}&siteid={$GLOBALS['SITES'][0]['SiteID']}&menu=Sites&action=Sites"));
				}
				check_sites();
			}
			delete_site_html();
			break;
			
		}
		break;
	
	case 'Hosts':
		if ($_SESSION['admin'] != 4) {//1->demo, 4->super admin 
			die(header('Location: logout.php')); 
		}
		
		get_hosts();
		
		switch ($GLOBALS['ACTION']) {			
		default:
			$GLOBALS['ACTION'] = 'Host Status';
		case 'Host Status':
			include './host/hosts_host_status.php';
			$GLOBALS['ERROR'] .= host_status_html();
			break;
		}
		break;
	
	case 'Settings':
		switch ($GLOBALS['ACTION']) {
		default:
			$GLOBALS['ACTION'] = 'Settings';
		case 'Settings':
			if ($_SESSION['admin'] < 2) die(header('Location: logout.php')); 

			include './setting/settings.php';
			if ($_POST) {
				$GLOBALS['ERROR'] .= change_settings();
			}
			settings_html();
			break;
		case 'About CA':
			include './setting/settings_about_ca.php';
			settings_about_ca_html();
			break;
		}
		break;
	
	}
	
	if (substr($GLOBALS['ERROR'],0,5) == '<br/>') $GLOBALS['ERROR'] = substr($GLOBALS['ERROR'],5);	

?>


<!DOCTYPE html>

<head>

	<title>Centcount Analytics Pro Manager - <?php echo $GLOBALS['MENU'].' - '.$GLOBALS['ACTION']; ?></title>
	<?php echo META_TEXT(); ?>
	<link href="css/manager.css" rel="stylesheet" type="text/css"/>
	<?php echo $GLOBALS['TEXT_CSS']; ?>
	<link href="css/icontrol.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="js/icontrol.js"></script>

</head>

<body>

	<?php echo get_side_menu_html() ?>
	<?php echo get_guide_menu_html() ?>


	<div id="rightbox" class="body_right_side">

		<div id="GuideMenuBox" class="div_guide_frame">
			<div class="guide">
				<a class="btn_help" href="https://www.centcount.com/doc.php" target="_blank" title="Help" alt="Help"></a>
				<a class="btn_language" onclick="PopupMenu(this, 'languagemenu', 1, 1)" title="Language" alt="Language"></a>
				<a class="btn_account" onclick="PopupMenu(this, 'accountmenu', 1, 1)" title="Account" alt="Account"></a>
			</div>
		</div>
		
		<div style="width:100%; height:60px; border-bottom:#ccc 1px solid; float:left;"></div>
		
		<?php echo get_option_html(); ?>
		
		<div class="framebody">
			<?php 
				echo ($GLOBALS['ERROR'] ? '<div id="errormsg" class="errmsg" style="display:block;">'.$GLOBALS['ERROR'].'</div>' : '<div id="errormsg" class="errmsg"></div>');//error msg 
				echo $GLOBALS['TEXT_BODY'];
			?>	
		</div>
		
	</div>

	<div id="footer"></div>



	<script type="text/javascript">

		var UID  = <?php echo $GLOBALS['USERID'] ?>;
		var SID  = <?php echo $GLOBALS['SITEID'] ?>;
		var MENU = "<?php echo $GLOBALS['MENU'] ?>";
		var ACT  = "<?php echo $GLOBALS['ACTION'] ?>";
		var period_from = "<?php echo $GLOBALS['PERIOD_FROM'] ?>";
		var period_to   = "<?php echo $GLOBALS['PERIOD_TO'] ?>";
		var PERIOD_TODAY   = "<?php echo strtotime(date('Ymd', time()))*1000 ?>";
		var PERIOD_YESTERDAY   = "<?php echo date('Ymd', (time() - 86400)) ?>";
		var LAN = <?php echo json_encode($GLOBALS['indicator']) ?>;

	</script>

	<script type="text/javascript" src="js/common.js"></script>

	<?php echo $GLOBALS['TEXT_SCRIPT'] ?>

</body>
</html>