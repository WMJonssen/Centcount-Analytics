<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Dashboard PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/13/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function dashboard_body_html() {

	$tabletext = '
	<div id="frametable" class="frametable">
	
		<div id="wf0" class="framesum">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Overview'] .'</span>
					<a id="OverviewTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="OverviewTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="OverviewTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="OverviewTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
				</div>
			</div>
			<div class="innertable frameinner">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Visits Over Time'] .'</span>
					<a id="VisitsOTTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="VisitsOTTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable frameinner">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Exit Status Overview'] .'</span>
					<a id="ExitStatusOVTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="ExitStatusOVTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable frameinner">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Duration Status Overview'] .'</span>
					<a id="DurationStatusOVTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="DurationStatusOVTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
		</div>
		
		<div id="wf1" class="frameinner">
		
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Today Trend'] .'</span>
					<a id="TTTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="TTTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Visitor Map'] .'</span>
					<a id="MapTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="MapTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
					<a id="MapTB_MAP" href="" class="ca_map_btn" alt="Visitor Map" title="'. $GLOBALS['indicator']['Visitor Map'] .'"></a>
					<a id="MapTB_LOC" href="" class="ca_location_btn" alt="Visitor Location" title="'. $GLOBALS['indicator']['Visitor Location'] .'"></a>
					<a id="MapTB_RTM" href="" class="ca_realmap_btn" alt="Realtime Visitor" title="'. $GLOBALS['indicator']['Realtime Visitor'] .'"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Visited Pages'] .'</span>
					<a id="AllPagesTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="AllPagesTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
					<a id="AllPagesTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="AllPagesTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="AllPagesTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="AllPagesTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Search Engines'] .'</span>
					<a id="SETB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="SETB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="SETB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="SETB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="SETB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="SETB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Keywords'] .'</span>
					<a id="KeywordsTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="KeywordsTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
					<a id="KeywordsTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="KeywordsTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="KeywordsTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="KeywordsTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			
		</div>
		
		<div id="wf2" class="frameinner">
			
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Online Visitors'] .'</span>
					<a id="OnlineVisitorNoTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="OnlineVisitorNoTB" class="innerbodywithborder">
			
				</div>
				<div class="innerfoot">
			
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Visitors In Realtime'] .'</span>
					<a id="RTVisitorLogTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="RTVisitorLogTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
			
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Realtime Visits'] .'</span>
					<a id="RTVisitsTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="RTVisitsTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Country'] .'</span>
					<a id="CountryTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="CountryTB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="CountryTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="CountryTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="CountryTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="CountryTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Region'] .'</span>
					<a id="RegionTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="RegionTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
					<a id="RegionTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="RegionTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="RegionTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="RegionTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['City'] .'</span>
					<a id="CityTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="CityTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
					<a id="CityTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="CityTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="CityTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="CityTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			
		</div>
		
		<div id="wf3" class="frameinner">
		
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Operating Systems'] .'</span>
					<a id="OSTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="OSTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="OSTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="OSTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="OSTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="OSTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Browsers'] .'</span>
					<a id="BrowsersTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="BrowsersTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="BrowsersTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="BrowsersTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="BrowsersTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="BrowsersTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Devices'] .'</span>
					<a id="DevicesTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="DevicesTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="DevicesTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="DevicesTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="DevicesTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="DevicesTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Resolutions'] .'</span>
					<a id="ResolutionsTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="ResolutionsTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="ResolutionsTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="ResolutionsTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="ResolutionsTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="ResolutionsTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Browser Language'] .'</span>
					<a id="LanTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="LanTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
					<a id="LanTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="LanTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="LanTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="LanTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			
		</div>
		
		<div id="chartjs-tooltip"></div>
		
	</div>';
	return $tabletext;
	
}


function dashboard_html() {

	$timezone = get_site_info('TimeZone');
	date_default_timezone_set($timezone) ? $today = time() : exit;
			
	$GLOBALS['TEXT_BODY'] = dashboard_body_html();

	$GLOBALS['TEXT_CSS'] = '

<link href="css/ichart.css" rel="stylesheet" type="text/css" />

<style type="text/css">

	.frameinner{width:33.33%;}

	@media only screen and (max-width: 1100px) {
	.frameinner{width:100%; height:auto;}
	}

</style>

';

	$GLOBALS['TEXT_SCRIPT'] = '
<script src="js/chart.min.js"></script>
<script src="js/ichart.js"></script>
<script src="js/iline.js"></script>
<script src="js/itable.js"></script>
<script src="js/echarts.min.js"></script>
<script src="js/imap.js"></script>
<script src="maps/world.js"></script>

<script type="text/javascript">

	var OverviewTB, VisitsOTTB, TTTB, RTVisitsTB, RTVisitorLogTB, AllPagesTB, SETB, KeywordsTB, CountryTB, RegionTB, CityTB, OSTB, BrowsersTB, DevicesTB, ResolutionsTB, LanTB, ExitStatusOVTB, DurationStatusOVTB, MapTB;


	Refresh();
	function Refresh() {

		//Overview
		OverviewTB = new CHARTAPI('.$GLOBALS['SITEID'].','.$_SESSION['r'].','.date("Ymd", $today).',VisitorsAPI.to, 0, 60, "'.$timezone.'", 1, 0, 8, "'.$_SESSION['DATACENTER'].'", "OverviewTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//visits Map
		MapTB = new MAPAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 12, "'.$timezone.'", 1, 15000, 0, "'.$_SESSION['DATACENTER'].'", "MapTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//visits over time
		VisitsOTTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 2, 0, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "VisitsOTTB", 0, LAN);
		//day Trend line chart
		TTTB = new IDTLINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', '.date("Ymd", $today).', VisitorsAPI.to, 0, 18, "'.$timezone.'", 60000, "'.$_SESSION['DATACENTER'].'", "TTTB", 0, LAN);
		//realtime visitors table
		RTVisitsTB = new IRTLINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 0, "'.$timezone.'", 15000, "'.$_SESSION['DATACENTER'].'", "RTVisitsTB", 0, LAN);
		RTVisitorLogTB = new RTTBAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', 0,"'.$timezone.'", 15000, "'.$_SESSION['DATACENTER'].'", "RTVisitorLogTB", "OnlineVisitorNoTB", LAN, ' .(date_offset_get(new DateTime) / 3600). ');
		//pages
		AllPagesTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 1, "'.$timezone.'", 2, 0, 0, "'.$_SESSION['DATACENTER'].'", "AllPagesTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//SE Name & keywords
		SETB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 15, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "SETB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		KeywordsTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 6, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "KeywordsTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//location
		CountryTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 12, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "CountryTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		RegionTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 13, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "RegionTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		CityTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 14, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "CityTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//software
		OSTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 8, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "OSTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//browser
		BrowsersTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 7, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "BrowsersTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//device
		DevicesTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 10, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "DevicesTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//resolutions
		ResolutionsTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 11, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "ResolutionsTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//laguage
		LanTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 20, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "LanTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		//exit status
		ExitStatusOVTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 2, 1, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "ExitStatusOVTB", 0, LAN);
		//online status
		DurationStatusOVTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 2, 3, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "DurationStatusOVTB", 0, LAN);

			OverviewTB.run(8);//overview

			MapTB.run(0);//visits Map
			
			VisitsOTTB.run();//visits over time
			
			TTTB.run();//day Trend line chart
			
			RTVisitsTB.run();//realtime visits table
			RTVisitorLogTB.run();//realtime visitor log table
			
			AllPagesTB.run(0);//visited pages
			
			SETB.run(0);//SE Name
			KeywordsTB.run(0);//SE keywords
			
			CountryTB.run(0);//location
			RegionTB.run(0);
			CityTB.run(0);
			
			OSTB.run(1);//software
			
			BrowsersTB.run(1);//browser
			
			DevicesTB.run(1);//device
			
			ResolutionsTB.run(1);//resolutions
			
			LanTB.run(1);//laguage
			
			ExitStatusOVTB.run();//exit status
			
			DurationStatusOVTB.run();//online status

	}


	function Resize() {
	
		setTimeout(function(){
			OverviewTB.resize();//overview
			
			MapTB.resize();//visits Map
			
			VisitsOTTB.resize();//visits over time
			
			TTTB.resize();//realtime visitors line chart
			
			RTVisitsTB.resize();//realtime visits table
			RTVisitorLogTB.resize();//realtime visitor log table
			
			AllPagesTB.resize();//visited pages
			
			SETB.resize();//SE Name & keywords
			KeywordsTB.resize();
			
			CountryTB.resize();//location
			RegionTB.resize();
			CityTB.resize();
			
			OSTB.resize();//software
			
			BrowsersTB.resize();//browser
			
			DevicesTB.resize();//device
			
			ResolutionsTB.resize();//resolutions
			
			LanTB.resize();//laguage
			
			ExitStatusOVTB.resize();//exit status
			
			DurationStatusOVTB.resize();//online status
		}, 500);
	}

</script>';
		
}

?>