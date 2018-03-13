<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Visitors Location PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/



function visitors_location_html() {
	
	$timezone = get_site_info('TimeZone');
		
	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_VISITORS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="frametable" class="frametable">
		<div class="frameinner">
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Country'] .'</span>
					<a id="CountryTB_R" href="" class="ca_refresh_btn"></a>
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
					<a id="RegionTB_R" href="" class="ca_refresh_btn"></a>
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
					<a id="CityTB_R" href="" class="ca_refresh_btn"></a>
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
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Browser Language'] .'</span>
					<a id="LanTB_R" href="" class="ca_refresh_btn"></a>
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
			<div class="innertable">
				<div class="innertitle">
					<span>'. $GLOBALS['indicator']['Language Code'] .'</span>
					<a id="LanCodeTB_R" href="" class="ca_refresh_btn"></a>
				</div>
				<div id="LanCodeTB" class="innerbody">
			
				</div>
				<div class="innerfoot">
					<a id="LanCodeTB_TB"  href="" class="ca_table_btn" alt="table" title="table"></a>
					<a id="LanCodeTB_ALL" href="" class="ca_all_btn" alt="show all data" title="show all data"></a>
					<a id="LanCodeTB_PIE" href="" class="ca_pie_btn" alt="pie" title="pie"></a>
					<a id="LanCodeTB_BAR" href="" class="ca_bar_btn" alt="bar" title="bar"></a>
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
.innerbodywithborder{width:auto; height:auto; margin-right:15px; text-align:left; border:1px #ccc solid; float:none; scrolling:no;}
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

	var CountryTB, RegionTB, CityTB, LanCodeTB, LanTB;

	Refresh();
	function Refresh() {
		CountryTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 12,"'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "CountryTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		RegionTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 13,"'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "RegionTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		CityTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 14,"'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "CityTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		LanCodeTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 19,"'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "LanCodeTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);
		LanTB = new CHARTAPI('.$GLOBALS['SITEID'].', '.$_SESSION['r'].', VisitorsAPI.from, VisitorsAPI.to, 0, 20,"'.$timezone.'", 1, 0, 0, "'.$_SESSION['DATACENTER'].'", "LanTB", 0, 0, 0, "'.$_SESSION['lan'].'", LAN);

		CountryTB.run(0);//country table
		RegionTB.run(0);//region table
		CityTB.run(0);
		LanCodeTB.run(0);
		LanTB.run(0);
	}
	
	function Resize() {
		setTimeout(function(){
			CountryTB.resize();//country table
			RegionTB.resize();//region table
			CityTB.resize();
			LanCodeTB.resize();
			LanTB.resize();
		}, 500);
	}

</script>';
		
}

?>