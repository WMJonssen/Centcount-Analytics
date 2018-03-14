<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! * 
* module: Centcount Analytics Free Delete Sites PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/12/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/


function delete_site(&$success) {
	
		$err = '';
		$con;

		if (empty($_POST['delpw'])) {
			$err .= '<br/>Password is required';
		} else if (strlen($_POST['delpw']) < 6) {
			$err .= '<br/>Password must be at least 6 characters long';
		}
		
		if ($err === '') {
			
			$err = verify_login_for_change($_POST['delpw'], $success);
			if ($success == false) {
				return $err;
			} else {
				$success = false;
			}
			
			switch ($_POST['deletesite']) {
			case 'pausesite':
			case 'continuesite':
				
				if ($_POST['deletesite'] == 'pausesite') {
					$status = 1;
				} else if ($_POST['deletesite'] == 'continuesite') {
					$status = 0;
				}
				
				$con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, DB_NAME_USER, 'Delete Site', 'delete_site');
				if ($con === false) return;
			
				$sql = "UPDATE st{$GLOBALS['SITELIST_TABLE']} SET SiteStatus={$status} WHERE SiteID={$GLOBALS['SITEID']}";
				if (!mysqli_query($con,$sql)) {
					mysqli_close($con);
					$err .= '<br/>Set site analytics status failed on user table';
					return $err;
				}
				mysqli_close($con);
				//change setting table
				//*************************** Set Site Setting Start *************************** 
				$value = $status;
		
				$v = get_visa();//Authorize for access database
				$host = get_site_info('DataCenter');
				$tz = get_site_info('TimeZone');
		
				$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
				$q = $v . 'q=set site&param=2&key=SiteStatus&sid=' . $GLOBALS['SITEID'] . '&tz=' . $tz . '&value=' . $value;
			
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $curl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);//不返回response头部信息 
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
				$ret = curl_exec($ch);
				curl_close($ch);

				if ($ret !== 'OK') {
					$err .= $ret;
					return $err;
				}
				//**************************** Set Site Setting End **************************** 
				//successfully
				$err .= '<br/>Set site analytics status successfully';
				break;
			
			case 'clearsite':
				
				//*************************** Set Site Setting Start *************************** 
				$v = get_visa();//Authorize for access database
				$host = get_site_info('DataCenter');
				//$tz = get_site_info('TimeZone');
		
				$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
				$q = $v . 'q=clear site&sid=' . $GLOBALS['SITEID'];// . '&tz=' . $tz;
			
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $curl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);//不返回response头部信息 
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
				$ret = curl_exec($ch);
				curl_close($ch);

				$err .= $ret;
				return $err;
				//**************************** Set Site Setting End **************************** 
				break;
			
			case 'deletesite':

				//*************************** Set Site Setting Start *************************** 
				$v = get_visa();//Authorize for access database
				$host = get_site_info('DataCenter');
				//$tz = get_site_info('TimeZone');
		
				$curl = CURL_PROTOCOL . $host . '/api/api_manage.php';
				$q = $v . 'q=del site&sid=' . $GLOBALS['SITEID'];// . '&tz=' . $tz;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $curl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);//不返回response头部信息 
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
				$ret = curl_exec($ch);
				curl_close($ch);

				if ($ret !== 'OK') {
					$err .= $ret;
					return $err;
				}
				//**************************** Set Site Setting End **************************** 
				
				//delete site id record from sites table
				$con = use_db(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL, DB_NAME_USER, 'Delete Site', 'delete_site');
				if ($con === false) return;
			
				$sql = "DELETE FROM st{$GLOBALS['SITELIST_TABLE']} WHERE SiteID={$GLOBALS['SITEID']}";
				
				if (mysqli_query($con,$sql)) {
					$err .= '<br/>Delete site successfully';
					$success=true;
				} else {
					$err .= '<br/>Delete site failed';
				}
				
				mysqli_close($con);
				return $err;
				
				break;
			}
			
		}
		return $err;
		
}


function delete_site_html() {
	
		$sitename; $createtime; $sitestaus; $textsitestatus; $textbtn;
		
		$i = count($GLOBALS['SITES']);
		for ($row = 0; $row < $i; $row++) {
			if ($GLOBALS['SITES'][$row]['SiteID'] == $GLOBALS['SITEID']) {
				$sitename = $GLOBALS['SITES'][$row]['SiteName'];
				$sitestatus = $GLOBALS['SITES'][$row]['SiteStatus'];
				$createtime = $GLOBALS['SITES'][$row]['CreateTime'];
				break;
			}
		}
	
		switch ($sitestatus) {
		case 0:
			$textsitestatus = $GLOBALS['indicator']['Running'];
			$textbtn = '
			<tr><td>To stop CA service for this site.</td></tr>
			<tr><td class="mTop"><button name="deletesite" value="pausesite" id="pausesite" type="submit">'. $GLOBALS['indicator']['Stop CA'] .'</button></td></tr>';
			break;
		case 1:
			$textsitestatus = $GLOBALS['indicator']['Stopped'];
			$textbtn = '
			<tr><td>To run CA service for this site.</td></tr>
			<tr><td class="mTop"><button name="deletesite" value="continuesite" id="continuesite" type="submit">'. $GLOBALS['indicator']['Run CA'] .'</button></td></tr>';
			break;
		}

		//$GLOBALS['TEXT_SUBMENU'] = SUBMENU_MANAGE_SITES;
				
		$GLOBALS['TEXT_BODY'] = '

<div class="dTitle">'. $GLOBALS['indicator']['Site Information'] .'</div>
<form>
	<table>
		<tr><td>'. $GLOBALS['indicator']['Site Name']   .' : '. $sitename.'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Site ID']     .' : '. $GLOBALS['SITEID'].'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Create Date'] .' : '. date('Y-m-d', $createtime) .'</td></tr>
		<tr><td>'. $GLOBALS['indicator']['Site Status'] .' : '. $textsitestatus .'</td></tr>
	</table>
</form>


<div class="dTitle mTop30">'. $GLOBALS['indicator']['Site Status'] .'</div>
<form name="DeleteSiteForm" autocomplete="off" method="POST" action="" onSubmit="return confirmdel()">
	<table>
		
		<tr><td>'. $GLOBALS['indicator']['Login Password'] .' : </td></tr>
		<tr><td class="mTop"><input type="password" class="dIme" name="delpw" id="password" maxlength="16" autocomplete="new-password" placeholder="type your login password here" onBlur="checkPW(this.id)" onfocus="setStyle(this.id)"/></td></tr>
			
		'.$textbtn.'
		
		<tr><td>To keep settings & remove historical data, start a new record.</td></tr>
		<tr><td class="mTop"><button name="deletesite" value="clearsite" id="clearsite" type="submit">'. $GLOBALS['indicator']['Empty Data'] .'</button></td></tr>
		
		<tr><td>To delete site, all settings & statistical data will be deleted.</td></tr>
		<tr><td class="mTop"><button name="deletesite" value="deletesite" id="deletesite" type="submit">'. $GLOBALS['indicator']['Delete Site'] .'</button></td></tr>
				
	</table>
</form>';
				
		$GLOBALS['TEXT_CSS'] = '
<style type="text/css">

.errmsg{margin-bottom:15px;}

.framebody{width:calc(100% - 30px); max-width:640px; height:auto; padding:15px; padding-top:0px; margin:0; text-align:center; border:0; float:left;}
.dTitle{width:calc(100% - 30px); height:40px; line-height:40px; padding-left:15px; padding-right:15px; font-family:Verdana,"Microsoft Yahei",Arial; font-size:16px; text-align:left; background-color:#555; color:#fff; float:left;}

td{width:100%; height:auto; font-size:13px; line-height:18px; border:0px; border-collapse:collapse; border-spacing:0; margin:0; margin-top:15px; text-align:left; float:left;}
tr,tbody,table,form{width:100%; border:0px; border-collapse:collapse; border-spacing:0; font-family:"Microsoft Yahei",Arial,Verdana;}

input, select, textarea{width:calc(100% - 13px); height:auto; padding:5px; margin:0; vertical-align:middle; border:#ccc 1px solid; font-family:"Microsoft Yahei",Arial,Verdana; float:left;} 
select{width:calc(100% - 1px);}
textarea{color:#555; resize:none; ime-mode:disabled;}
button{width:150px; height:30px; font-family:"Microsoft Yahei",Arial,Verdana; color:#333;}

.dIme{ime-mode:disabled;}
.mTop{margin-top:0px;}
.mTop15{margin-top:15px;}
.mTop30{margin-top:30px;}
b{font-size:14px; color:#111;}

</style>';
	
		$GLOBALS['TEXT_SCRIPT'] = '
<script type="text/javascript">
	
	function setStyle(x) {
		document.getElementById(x).style.borderColor="#39F";
	}
	
	function checkValue(x) {
		if (document.getElementById(x).value == "") {
			document.getElementById("errormsg").innerHTML = "Password is required";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
			return false;
		} else {
			document.getElementById("errormsg").style.display = "";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	function checkPW(x) {
		var pw = document.getElementById(x).value;
		if (pw == "") {
			document.getElementById(x).style.borderColor = "#ccc";
			return;
		}

		if (pw.length < 6) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrPW2'] . '";
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else if (pw.length > 16) {
			document.getElementById("errormsg").innerHTML = "' . $GLOBALS['language']['RegErrPW3'] . '",
			document.getElementById("errormsg").style.display = "block";
			document.getElementById(x).style.borderColor = "#f00";
		} else {
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("errormsg").style.display = "none";
			document.getElementById(x).style.borderColor = "#ccc";
		}
	}
	
	function confirmdel() {
		if (confirm("Are you sure?")) {
			return true;
		} else {
			return false;
		}
	}
	
	function Resize() {
		return;
	}

</script>';
	
}

?>