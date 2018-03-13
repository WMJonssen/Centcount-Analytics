<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Visitors Times PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function visitors_active_visitor_html() {
	
	$timezone = get_site_info('TimeZone');
	
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_VISITORS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="frametable" class="frametable">
		<div class="frameinner">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Active Visitor'] .'</span>
					<a id="ActiveVisitorTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="ActiveVisitorTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="ActiveVisitorTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="ActiveVisitorTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="ActiveVisitorTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="ActiveVisitorTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Session Duration'] .'</span>
					<a id="SessionDurationTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="SessionDurationTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="SessionDurationTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="SessionDurationTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="SessionDurationTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="SessionDurationTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Page Duration'] .'</span>
					<a id="PageDurationTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="PageDurationTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="PageDurationTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="PageDurationTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="PageDurationTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="PageDurationTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Viewed Pages Per Session'] .'</span>
					<a id="PVPerSessionTB_R" href="" class="ca_refresh_btn" alt="refresh" title="refresh"></a>
				</div>
				<div id="PVPerSessionTB" class="innerbody">
				
				</div>
				<div class="innerfoot">
					<a id="PVPerSessionTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="PVPerSessionTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="PVPerSessionTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="PVPerSessionTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
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
<script src="js/ichart.js"></script>

<script type="text/javascript">

	var ActiveVisitorTB, SessionDurationTB, PageDurationTB, PVPerSessionTB;

	Refresh();
	function Refresh() {
		ActiveVisitorTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 17, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "ActiveVisitorTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		SessionDurationTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 24, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "SessionDurationTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		PageDurationTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 23, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "PageDurationTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		PVPerSessionTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 37, "'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "PVPerSessionTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		
		ActiveVisitorTB.run(0);
		SessionDurationTB.run(0);
		PageDurationTB.run(0);
		PVPerSessionTB.run(0);
	}
	
	function Resize() {
		setTimeout(function(){
			ActiveVisitorTB.resize();
			SessionDurationTB.resize();
			PageDurationTB.resize();
			PVPerSessionTB.resize();
		}, 500);
	}

</script>';
		
}

?>