<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Visitors Device PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function visitors_device_html() {
	
	$timezone = get_site_info('TimeZone');
	
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_VISITORS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="frametable" class="frametable">
		<div class="frameinner">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Devices'] .'</span>
					<a id="DevicesTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="DevicesTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="DevicesTB_TB" href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="DevicesTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="DevicesTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="DevicesTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
		</div>
		<div class="frameinner">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Resolutions'] .'</span>
					<a id="ResolutionsTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="ResolutionsTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="ResolutionsTB_TB" href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="ResolutionsTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="ResolutionsTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="ResolutionsTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
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
.frameinner{width:50%; height:auto; text-align:center; float:left;}

.innertable{width:100%; min-width:330px; height:auto; margin:0px; text-align:left; float:none;}
.innertitle{width:auto; height:45px; line-height:45px; padding-left:10px; margin-right:15px; text-align:left; font-size:20px; color:#111; font-family:Verdana,"Microsoft Yahei",Arial; border:1px #ccc solid; border-bottom:0px; float:none;}
.innerbody{width:auto; height:auto; margin-right:15px; text-align:left; border:0px; float:none; scrolling:no;}
.innerfoot{width:auto; height:45px; margin-right:15px; margin-bottom:15px; text-align:left; font-size:16px; font-weight:bold; border:1px #ccc solid; border-top:0px; float:none;}

@media only screen and (max-width: 2400px) {
.frameinner{width:100%; height:auto;}
}

</style>

<link href="css/ichart.css" rel="stylesheet" type="text/css" />
';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script src="js/chart.min.js"></script>
<script src="js/ichart.js"></script>

<script type="text/javascript">

	var DevicesTB, ResolutionsTB;

	Refresh();
	function Refresh() {
		DevicesTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 10, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "DevicesTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		ResolutionsTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 11, "'.$timezone.'", 1, 0, 1, "'.$_SESSION['DATACENTER'].'", "ResolutionsTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		
		DevicesTB.run(0);
		ResolutionsTB.run(0);
	}
	
	function Resize() {
		setTimeout(function(){
			DevicesTB.resize();
			ResolutionsTB.resize();
		}, 500);
	}

</script>';
		
}

?>