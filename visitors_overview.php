<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Visitors Overview PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function visitors_overview_html() {		
		
	$timezone = get_site_info('TimeZone');
	
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_VISITORS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="frametable" class="frametable">
		<div class="frameinner">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Visit Overview'] .'</span>
					<a id="VisitsOVTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="VisitsOVTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Browsing Status Overview'] .'</span>
					<a id="BrowsingStatusOVTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="BrowsingStatusOVTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Exit Status Overview'] .'</span>
					<a id="ExitStatusOVTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="ExitStatusOVTB" class="innerbodywithborder">
				
				</div>
				<div class="innerfoot">
				
				</div>
			</div>
			<div class="innertable">
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
.innerbodywithborder{width:auto; height:auto; margin-right:15px; text-align:left; border:1px #ccc solid; float:none; scrolling:no;}
.innerfoot{width:auto; height:45px; margin-right:15px; margin-bottom:15px; text-align:left; font-size:16px; font-weight:bold; border:1px #ccc solid; border-top:0px; float:none;}


@media only screen and (max-width: 1000px) {
.frameinner{width:100%; height:auto;}
}

</style>

<link href="css/ichart.css" rel="stylesheet" type="text/css" />
';

	$GLOBALS['TEXT_SCRIPT'] = '
<script src="js/chart.min.js"></script>
<script src="js/iline.js"></script>

<script type="text/javascript">

	var VisitsOVTB, BrowsingStatusOVTB, ExitStatusOVTB, DurationStatusOVTB;

	Refresh();
	function Refresh() {
		VisitsOVTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 0, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "VisitsOVTB", 0, LAN);
		BrowsingStatusOVTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 2, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "BrowsingStatusOVTB", 0, LAN);
		ExitStatusOVTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 1, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "ExitStatusOVTB", 0, LAN);
		DurationStatusOVTB = new ILINE('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 3, "'.$timezone.'", 0, "'.$_SESSION['DATACENTER'].'", "DurationStatusOVTB", 0, LAN);
		
		VisitsOVTB.run();//visits over time
		BrowsingStatusOVTB.run();
		ExitStatusOVTB.run();
		DurationStatusOVTB.run();
	}
	
	function Resize() {
		setTimeout(function(){
			VisitsOVTB.resize();//visits over time
			BrowsingStatusOVTB.resize();
			ExitStatusOVTB.resize();
			DurationStatusOVTB.resize();
		}, 500);
	}

</script>';
		
}

?>