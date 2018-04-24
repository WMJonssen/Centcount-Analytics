<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free Host Status PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 04/24/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/



function host_status_html() {

	//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_HOSTS;
			
	$GLOBALS['TEXT_BODY'] = '
	<div id="ProcessInfoTB" style="width:100%; height:auto; padding-bottom:0px; float:left;"></div>
	
	<div id="frameinput" class="frameinput">
		<div id="tipbox" class="framebox">
			<div class="tipbox">
				Processing...
			</div>
		</div>

		<div id="msgbox" class="framebox">
			<div class="inputbox">
				<div class="msgTitle">Process Options</div>
				<div class="closebtn">
					<a href="javascript:showInputBox(0)" style="height:24px; float:left;"><img src="images/inputclose.png" style="width:24px; height:24px;" alt="CLOSE" title="CLOSE"/></a>
				</div>

				<div id="msgText" class="msgText">text message</div>
				<div class="msgOKBtn">
					<a href="javascript:showInputBox(0,0)" class="okbtn">OK</a>
				</div>
			</div>
		</div>
	</div>

	<div id="errMsg" class="frameerror">
	
		<div class="errorbtnframe">
			Error Report
			<a href="javascript:showInputBox(0)" style="position:fixed; top:9px; right:12px; float:right; height:32px;"><img src="images/errorclose.png" style="width:32px; height:32px;" alt="CLOSE" title="CLOSE"/></a>
		</div>
				
		<div class="errorbox">
			<p id="errText" style="padding:10px; word-break:break-all;"></p>
		</div>
				
		<div id="errBtn" class="errorbtnframe" style="font-family:Verdana,Microsoft Yahei,Arial;">
			
		</div>

	</div>'
	;
			
	$GLOBALS['TEXT_CSS'] = '
<style type="text/css">
	
body{padding:0; margin:0;text-align:center;}
.ctrl-frame{width:254px; height:498px; margin-left:15px; margin-bottom:15px; float:left; border: #ccc 1px solid; border-radius:5px; text-align:center; overflow:hidden;}
.ctrl-frame p{font-size:30px; font-family:Verdana, Arial, "Microsoft Yahei"; margin-top:20px; margin-bottom:0px;}
.canvas-box{width:200px; height:200px; position:relative; margin:27px; margin-top:20px; margin-bottom:20px; padding:0px; float:left;}
.canvas-tips{width:100%; height:100%; position:absolute; text-align:center; line-height:200px; z-index:9999;background-color:transparent; font-size:48px; font-family:Verdana, Arial;}
.detail{width:auto; height:auto; float:left; text-align:left; margin:27px; margin-top:0px; margin-bottom:15px; line-height:22px; font-size:14px; font-family:Arial, Helvetica, sans-serif;}
.ctrl-box, .option-box, .top-box{width:auto; height:auto; margin:27px; margin-top:0px; margin-bottom:15px; float:left; text-align:left; line-height:22px; font-size:14px; font-family:Arial, Helvetica, sans-serif;}
.option-box{margin:0px;}
.option-box span{width:200px; height:33px; line-height:35px; margin:0px; margin-bottom:10px; border-radius:4px; background-color:rgb(196,196,196); text-align:center; padding:0px; color:#fff; font-size:13px; float:left;}
.top-box{margin:0px; line-height:20px; white-space:nowrap; text-overflow:ellipsis;}

.set{font-size:14px; font-family:Verdana, Arial, "Microsoft Yahei";}
input{width:90px; height:24px; padding-left:5px; padding-right:5px;}
			
span{color:#aaa; text-decoration:none;}
a{color:#39F; text-decoration:none;}
a:hover{text-decoration:underline;}
.btn, .lbtn, .wbtn, .okbtn, .redbtn{display:block; margin:auto; text-align:center; width:60px; height:28px; line-height:30px; margin-right:10px; padding:0px; background-color:rgb(16,151,228); color:#fff; font-size:13px; border-radius:3px; float:left;}
.lbtn{margin:0px;}
.wbtn, .redbtn{width:200px; height:33px; line-height:35px; margin:0px; margin-bottom:10px; border-radius:4px;}
.redbtn{background-color:#D00;}
.okbtn{width:90px; height:30px; line-height:32px; text-align:center; margin-right:15px; float:none;}
.btn:hover, .lbtn:hover, .wbtn:hover, .okbtn:hover{background-color:rgb(64,172,233); text-decoration:none;}
.redbtn:hover{background-color:#F00; text-decoration:none;}


.frameinput{width:100%; height:100%; display:none; position:fixed; top:0; left:0; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background:rgba(0,0,0,0.5); float:left; z-index:999999999;}
.framebox{width:auto; max-width:360px; min-width:300px; height:100%; min-height:210px; margin:auto; background:transparent; float:none; overflow:hidden; display:none;}
.tipbox{width:auto; height:60px; line-height:60px; font-Size:14px; text-align:center; margin:auto; background:#eee; color:#555; float:none; position:relative; top:50%; margin-top:-30px;}
.inputbox{width:auto; height:210px; margin:auto; background:#eee; color:#555; float:none; border:#777 1px solid; position:relative; top:50%; margin-top:-105px;}
.closebtn{width:auto; height:auto; font-size:14px; color:#fff; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; background-color:transparent; float:right; margin:15px; margin-bottom:0px;}
.msgTitle{width:auto; height:auto; font-size:16px; color:#555; text-align:left; font-family:Verdana,"Microsoft Yahei",Arial; background-color:transparent; float:left; margin:15px; margin-bottom:0px;}
.msgText{width:100%; max-width:330px; height:45%; padding:15px; text-align:left; float:left; font-size:12px;}
.msgOKBtn{width:100%; height:45px; margin:auto; padding:0px; text-align:center; float:left;}

.selectbtn{width:auto; height:33px; line-height:35px; float:left; border:0px; background-color:transparent; margin-bottom:10px;}

.selectbtn a.select{width:188px; height:31px; line-height:33px; font-size:14px; text-align:left; color:#333; padding-left:10px; border:#ddd 1px solid; border-radius:4px; margin:0px; margin-bottom:4px; background:url(images/dp.png) 170px -39px no-repeat; background-color:transparent; display:block; float:left; cursor:pointer;}
.selectbtn a.select:hover{text-decoration:none; border:#ccc 1px solid; border-bottom:#bbb 1px solid; border-right:#bbb 1px solid; -webkit-box-shadow:0px 0px 3px 0px #ccc; box-shadow:0px 0px 3px 0px #ccc; -webkit-transition:all 0.2s ease-in; transition:all 0.2s ease-in;}


.selectbtn ul{width:auto; height:auto; float:left; margin:0px; padding:0px; background-color:transparent; list-style:none;}
.selectbtn li{background-color:transparent; width:auto; height:auto; font-size:14px; float:left; margin:0px; padding:0px;}

.selectbtn li ul{display:none; width:auto; height:auto; max-height:168px; border:#eee 1px solid; position:absolute; z-index:999; background-color:#fff; padding-right:0px; margin-top:33px; overflow-x:hidden; overflow-y:auto;}
.selectbtn li li {display:block; width:auto; height:auto; float:none; text-align:left;}

.selectbtn li ul li a{width:100%; height:28px; line-height:28px; font-size:14px; text-align:center; color:#333; display:block; padding-left:0px; cursor:pointer;}
.selectbtn li ul li a:hover{text-decoration:none; background-color:#eee;}
.selectbtn li ul li span{width:100%; height:28px; line-height:28px; font-size:14px; text-align:center; color:#333; display:block; padding-left:0px; cursor:pointer; margin:0px; float:none; background-color:#f6f6f6;}


.frameerror{width:100%; height:100%; display:none; position:fixed; top:0; left:0; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background:rgba(0,0,0,1); float:left; z-index:999999999;}
.errorbox{width:100%; min-width:330px; height:-moz-calc(100% - 120px); height:-webkit-calc(100% - 120px); height:calc(100% - 120px); line-height:20px; text-align:left; font-size:14px; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background:#eee; color:#555; float:none; overflow-y:auto; z-index:999999999;}
.errorbtnframe{width:100%; min-width:330px; height:60px; line-height:60px; font-size:16px; color:#fff; text-align:center; font-family:"Microsoft Yahei",Arial,Verdana; margin:auto; background-color:transparent; float:left; z-index:999999999;}

.errA, .errSpan{width:24px; height:24px; line-height:22px; margin-left:5px; margin-right:5px; display:inline-block; text-decoration:none; color:#aaa; border:#aaa 1px solid; border-radius:3px;}
.errSpan{color:#777; border:#777 1px solid;}
.errA:hover, .errSapn:hover{text-decoration:none; color:#eee; border:#eee 1px solid;}


</style>';
	
	$GLOBALS['TEXT_SCRIPT'] = '
<script src="js/chart.min.js"></script>
<script src="js/iprocess.js"></script>

<script type="text/javascript">

	function showInputBox(x, y, z) {
		switch (x) {
		case 0:
			document.getElementById("frameinput").style.display = "none";
			document.getElementById("errMsg").style.display = "none";
			document.getElementById("msgbox").style.display = "none";
			document.getElementById("tipbox").style.display = "none";
			break;
		case 1:
			document.getElementById("msgText").innerHTML = y;
			document.getElementById("tipbox").style.display = "none";
			document.getElementById("msgbox").style.display = "block";
			document.getElementById("frameinput").style.display = "block";
			break;
		case 2:
			document.getElementById("msgbox").style.display = "none";
			document.getElementById("tipbox").style.display = "block";
			document.getElementById("frameinput").style.display = "block";
			break;
		case 3://error msg
			document.getElementById("errText").innerHTML = y;
			document.getElementById("errBtn").innerHTML = z;
			document.getElementById("errMsg").style.display = "block";
			break;
		}
	}
	
	

	var ProcessInfoTB;
		
	Refresh();
	function Refresh() {
		ProcessInfoTB = new IPROCESS('.$GLOBALS['USERID'].', '.$_SESSION['r'].', 1000, "'.($GLOBALS['PARAM'] ? $GLOBALS['PARAM'] : $_SESSION['DATACENTER']).'", "ProcessInfoTB", 0, 0);
		ProcessInfoTB.create();	
	}
	
	function Resize() {
		return;
	}

</script>';
		
}

?>