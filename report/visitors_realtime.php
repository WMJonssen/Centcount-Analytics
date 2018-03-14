<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Visitors Realtime PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function visitors_realtime_html() {
		
	
	$timezone = get_site_info('TimeZone');
	date_default_timezone_set($timezone) ? $today = time() : exit;
	
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_VISITORS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="frametable" class="frametable">
		<div class="frameinner">
			<div class="innertable" style="margin-bottom:15px;">
				<div id="MapTB" class="innerbodynoborder">
				
				</div>
			</div>
			<div class="innertable" style="margin-bottom:15px;">
				<div id="RTVisitorLogTB" class="innerbody">
			
				</div>
			</div>
			<div class="innertable" style="padding-bottom:15px;">
				<div id="OnlineVisitorNoTB" class="innerbodynoborder">
			
				</div>
			</div>
			<div class="innertable" style="padding-bottom:15px;">
				<div id="TTTB" class="innerbodynoborder">
				
				</div>
			</div>
			<div class="innertable" style="padding-bottom:15px;">
				<div id="RTVisitsTB" class="innerbodynoborder">
				
				</div>
			</div>
			
		</div>
	</div>';

	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">
	
.selectframe{font-size:14px; font-family:"Microsoft Yahei",Arial,Verdana; width:100%; min-width:360px; height:auto; float:left; border-bottom:#ccc 1px solid; margin:auto; text-align:center;}	

.framebody{width:auto; min-width:345px; height:auto; float:none; margin:0px; margin-left:15px; text-align:left; overflow-x:auto; overflow-y:hidden;}
.errmsg{margin-bottom:10px;}
.frametable{width:100%; height:auto; text-align:center; float:none;}
.frameinner{width:100%; height:auto; text-align:center; float:left;}

.innertable{width:100%; min-width:330px; height:auto; margin:0px; text-align:left; float:none;}
.innertitle{width:auto; height:45px; line-height:45px; padding-left:10px; margin-right:15px; text-align:left; font-size:20px; color:#111; font-family:Verdana,"Microsoft Yahei",Arial; border:1px #ccc solid; border-bottom:0px; float:none;}
.innerbody{width:auto; height:auto; margin-right:15px; text-align:left; border:0px; float:none; scrolling:no;}
.innerfoot{width:auto; height:45px; margin-right:15px; margin-bottom:15px; text-align:left; font-size:16px; font-weight:bold; border:1px #ccc solid; border-top:0px; float:none;}

</style>

<link href="css/ichart.css" rel="stylesheet" type="text/css" />
';

	$GLOBALS['TEXT_SCRIPT'] = '
<script src="js/chart.min.js"></script>
<script src="js/iline.js"></script>
<script src="js/itable.js"></script>
<script src="js/echarts.min.js"></script>
<script src="js/imap.js"></script>
<script src="maps/world.js"></script>

<script type="text/javascript">

	var TTTB, RTVisitsTB, RTVisitorLogTB, MapTB;

	Refresh();
	function Refresh() {
		MapTB = new MAPAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 12, "'.$timezone.'", 1, 15000, 2, "'.$_SESSION['DATACENTER'].'", "MapTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		TTTB = new IDTLINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', '.date("Ymd", $today).', VisitorsAPI.to, 0, 18, "'.$timezone.'", 60000, "'.$_SESSION['DATACENTER'].'", "TTTB", 0, LAN);
		RTVisitsTB = new IRTLINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 0, "'.$timezone.'", 15000, "'.$_SESSION['DATACENTER'].'", "RTVisitsTB", 0, LAN);
		RTVisitorLogTB = new RTTBAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', 0, "'.$timezone.'", 15000, "'.$_SESSION['DATACENTER'].'", "RTVisitorLogTB", "OnlineVisitorNoTB", LAN, ' .(date_offset_get(new DateTime) / 3600). ');
		
		MapTB.run(2);//visits Map, MAPAPI(sid, r, from, to, period, type, timezone, key, timer, cType, host, box, roaming, W, H, Lan, Extra) 
		TTTB.run();
		RTVisitsTB.run();
		RTVisitorLogTB.run();
	}
	
	function Resize() {
		setTimeout(function(){
			MapTB.resize();//visits Map, MAPAPI(sid, r, from, to, period, type, timezone, key, timer, cType, host, box, roaming, W, H, Lan, Extra) 
			TTTB.resize();
			RTVisitsTB.resize();
			RTVisitorLogTB.resize();
		}, 500);
	}

</script>';
		
}

?>