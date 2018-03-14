<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free All Referrer PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function referrers_channel_html() {
		
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_REFERRERS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="frametable" class="frametable">
		<div class="frameinner">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Sources'] .'</span>
					<a id="SourceTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="SourceTB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="SourceTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="SourceTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="SourceTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="SourceTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Mediums'] .'</span>
					<a id="MediumTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="MediumTB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="MediumTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="MediumTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="MediumTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="MediumTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Terms'] .'</span>
					<a id="TermTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="TermTB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="TermTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="TermTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="TermTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="TermTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Campaigns'] .'</span>
					<a id="CampaignTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="CampaignTB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="CampaignTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="CampaignTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="CampaignTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="CampaignTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
				</div>
			</div>
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Contents'] .'</span>
					<a id="ContentTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="ContentTB" class="innerbody">
					
				</div>
				<div class="innerfoot">
					<a id="ContentTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="ContentTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="ContentTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="ContentTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
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

	var SourceTB,MediumTB,TermTB,CampaignTB,ContentTB;

	Refresh();
	function Refresh() {
		SourceTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 30,"'.get_site_info('TimeZone').'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "SourceTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		SourceTB.run(0);
		MediumTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 31,"'.get_site_info('TimeZone').'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "MediumTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		MediumTB.run(0);
		TermTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 32,"'.get_site_info('TimeZone').'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "TermTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		TermTB.run(0);
		ContentTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 33,"'.get_site_info('TimeZone').'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "ContentTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		ContentTB.run(0);
		CampaignTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 34,"'.get_site_info('TimeZone').'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "CampaignTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		CampaignTB.run(0);
	}
	
	function Resize() {
		setTimeout(function(){
			SourcesTB.resize();
			MediumTB.resize();
			TermTB.resize();
			ContentTB.resize();
			CampaignTB.resize();
		}, 500);
	}

</script>';

}

?>