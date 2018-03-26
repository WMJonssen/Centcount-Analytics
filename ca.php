<?php
/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics Free JS Code Generate PHP Code *
* version: 1.00 Free *
* author: WM Jonssen *
* date: 03/26/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved.*
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/

if ($_GET) {
		header('Access-Control-Allow-Origin: *');
		$t = $_SERVER['REQUEST_TIME_FLOAT'];
		empty($t) && $t = microtime(true);
		$rn = (int)($t * 1E6); 
		if (isset($_GET['wakeup'])) {
			isset($_GET['tz']) ? $tz = $_GET['tz'] : exit;
			date_default_timezone_set($tz) || exit;
			$ft = date('Ynd', (int)$t);
			header('Content-type: application/javascript');
			echo "var _caq = _caq || [];_caq.push(['_wakeupEventCA', '{$ft}', {$rn}]);";
			exit;
		}
echo '//1, ';
		isset($_GET['siteid']) ? $sid = (int)$_GET['siteid'] : exit;
		$sid < 1E15 || $sid > 2E15 AND exit;
		empty($_SERVER['HTTP_HOST']) ? exit : $host = $_SERVER['HTTP_HOST'];
		empty($_SERVER['HTTP_REFERER']) ? exit : $referer = $_SERVER['HTTP_REFERER'];
echo '2, ';
		@require './config/config_common.php';
		$REDIS_2 = new Redis();
		if ($REDIS_2->CONNECT(REDIS_IP_2, REDIS_PORT_2) !== true) exit;
		$REDIS_2->SELECT(REDIS_DB_2);
echo '3, ';
		$ip = get_ip();
		if ($ip === '' || $REDIS_2->SISMEMBER('BlockedIPList', $ip) === true) exit;
echo '4, ';
		$cahm_visa = '';
		if (isset($_GET['cahm_visa'])) {
			$cahm_visa = $_GET['cahm_visa'];
		} else {
			$len = strpos($referer, 'cahm_visa=');
			if ($len !== false) $cahm_visa = substr($referer, $len + 10);
		}
		$verify_result = 1;
		if (strlen($cahm_visa) >= 42) $verify_result = verify_cahm($sid, $ip, $cahm_visa);
echo '5 -> ', $verify_result, ', ';
		if ($cahm_visa === '' || $verify_result === 0) {
			check_block($referer, $sid, $ip, $tz, $ipdb, $REDIS_2);
echo '6 -> ', ((empty($tz) || is_null($ipdb)) ? '0, ' : '1, ');
			empty($tz) || is_null($ipdb) AND exit;
			date_default_timezone_set($tz) || exit;
			$ft = date('Ynd', (int)$t);
			$IS_CAHM = false;
		} else {
echo '7, ';
			$tz = $REDIS_2->GET($sid.'-TimeZone');
			empty($tz) AND exit;
			$IS_CAHM = true;
		}
echo '8, ';
		$errHost = ERROR_LOG_HOST;
echo '9, ';
		
		if ($IS_CAHM) {

			$ca = "";
		} else {
			$ca = "

/*!
* ATTENTION: THIS FREE LICENSE IS ONLY FOR PERSONAL NON-COMMERCIAL USER. FOR COMMERCIAL PURPOSES, PLEASE PURCHASE A COMMERCIAL LICENSE! *
* module: Centcount Analytics JS Code *
* version: 1.00.180326001 Free *
* author: WM Jonssen *
* date: 03/26/2018 *
* copyright 2015-2018 WM Jonssen <wm.jonssen@gmail.com> - All rights reserved. *
* license: Dual licensed under the Free License and Commercial License. *
* https://www.centcount.com *
*/
(function(){function L(a){try{return Q(decodeURIComponent(a))}catch(c){try{return Q(a)}catch(n){t(n,'U failed')}}}function Cb(a){try{var b=(new Date).getTime();return a?b-R:St-R+b}catch(n){t(n,'Gt failed')}}function P(a,c){try{var b='';if(0<m.cookie.length){var p='; '+a+'=';var z=m.cookie.indexOf(p);0>z&&(p=a+'=',z=m.cookie.indexOf(p));if(-1<z){z+=p.length;var r=m.cookie.indexOf(';',z);0>r&&(r=m.cookie.length);b=Db(m.cookie.substring(z,r))}}return b?'number'===typeof c?O(b):b:c}catch(y){t(y,'gC failed')}}
function A(a,c,n,p){try{var b=new Date;p?b.setDate(b.getDate()+n):b.setTime(b.getTime()+n);m.cookie=a+'='+Q(c)+(n?';expires='+b.toUTCString():'')+';path=/'}catch(r){t(r,'sC failed')}}function Eb(){try{q._caq={push:function(){return Fb.apply(this,arguments)}}}catch(b){t(b,'aL failed')}}function Fb(b){try{if('[object Array]'==={}.toString.call(b))switch(b[0]){case '_customVariableCA':Y=b[1];J(6);break;case '_wakeupEventCA':ua=b[1];C=b[2];St=O(C/1E3);if(ua&&C)try{Z&&(Z=0,R=S=(new Date).getTime(),ia=
T=ja=ka=la=ma=M=N=na=va=ca=0,aa=[],v=[0,0,0,0,0,0,0,0,0,0,0,0],wa=[],Va=oa=0,da=K=Wa='',xa=S,ea=Xa=0,U=1,ya=0,Ya=za=!1,jb(),Aa=!1,kb(),a.nt=0,a.lr=0,A('CA_PPI','',0,0),lb(),a.se='',a.sen='',a.kw='',a.rd='',a.rf='',a.fmt=3,J(9))}catch(c){t(c,'iW failed')}break;case '_responseStatusCA':if(!1===F||1>ca)ca=Ba(0);if(1===O(b[1])){D=!0;clearInterval(Za);try{q.addEventListener?(q.removeEventListener('unload',Ca),q.removeEventListener('mousemove',Da),q.removeEventListener('mousedown',Ea),q.removeEventListener('mouseup',
Fa),q.removeEventListener('mousewheel',Ga),q.removeEventListener('touchstart',mb),q.removeEventListener('touchmove',nb),q.removeEventListener('click',Ha),q.removeEventListener('scroll',Ia),m.removeEventListener('submit',Ja),q.removeEventListener('load',Ka),q.removeEventListener('beforeunload',La)):(q.detachEvent('onunload',Ca),m.detachEvent('onmousemove',Da),m.detachEvent('onmousedown',Ea),m.detachEvent('onmouseup',Fa),m.detachEvent('onmousewheel',Ga),m.detachEvent('onclick',Ha),m.detachEvent('onscroll',
Ia),m.detachEvent('onsubmit',Ja),q.detachEvent('onload',Ka),q.detachEvent('onbeforeunload',La))}catch(c){t(c,'RE failed')}}}}catch(c){t(c,'aP failed')}}function t(a,c){try{var b=[];b.push('siteid='+xc);b.push('rn='+C);b.push('vid='+W);b.push('name='+Q(a.name));b.push('msg='+Q(a.message));b.push('pos='+Q('Line: '+a.lineNumber+', Col: '+a.columnNumber));b.push('rf='+Q(m.referrer));b.push('page='+Q(q.location.href));b.push('agent='+Q(ab));b.push('ex='+Q(c));(new Image).src=bb+b.join('&')+'&rnd='+Math.random()}catch(p){}}
function J(b){try{if(!Z){var c=[];Xa++;c.push('stp='+Xa);c.push('stat='+b);c.push('sid='+xc);c.push('vid='+W);c.push('rn='+C);c.push('tz='+ob);c.push('ipdb='+Gb);c.push('rbt='+pb);c.push('ds='+(2<b&&1>ca?S-R:ca));c.push('rs='+qb);c.push('ls='+rb);c.push('ols='+Cb(1));c.push('mxrx='+Ma);c.push('mxry='+Na);c.push('mnrx='+Oa);c.push('mnry='+Pa);c.push('pa='+a.pa);c.push('tvs='+pa);switch(b){case 9:case 1:case 2:case 3:for(var n in a)c.push(n+'='+a[n]);break;case 4:case 5:if(Ya)return;Ya=!0;var p=(new Date).getTime();
0<oa&&Va>p&&c.push('et='+oa);aa.length&&(1===U&&ea++,c.push('uar='+ea),c.push('uas='+U),c.push('va='+aa.join('.')));(new Image).src=Qa+c.join('&')+Wa+'&rnd='+Math.random();A('CA_LAT',5===b?p:0,fa,0);A('CA_PPI',cb+'-'+p+'-'+C,fa,0);return;case 6:Y.length&&(c.push('cvn='+Y[0]),c.push('cvg='+Y[1]),c.push('cvc='+Y[2]),c.push('cvv='+Y[3]),c.push('cvt='+Y[4]));break;case 7:aa.length&&(1===U&&ea++,c.push('uar='+ea),c.push('uas='+U),c.push('dct='+a.dct),c.push('va='+aa.join('.')));break;case 8:ya++;c.push('ucr='+
ya);c.push('dct='+a.dct);c.push('tpv='+a.tpv);c.push('pg='+a.pg);c.push('cs='+a.cs);for(var z in K)c.push(z+'='+K[z]);break;default:return}var r=c.join('&');1===b||9===b?(va=(new Date).getTime(),Ra(Qa+r+'&rnd='+Math.random())):7===b&&4===U?Ra(Qa+r+'&rnd='+Math.random()):(new Image).src=Qa+r+'&rnd='+Math.random();8===b&&(Wa=r.substr(r.indexOf('&ucr=')))}}catch(y){t(y,'MSG failed')}}function Hb(){try{var a=P('CA_VID',0);W=a?a:C;if(2E15<W||1E15>W||W>C)W=C;A('CA_VID',W,365,1)}catch(c){t(c,'cV failed')}}
function db(){try{if(a.dst=G.scrollTop||u.scrollTop,a.dsl=G.scrollLeft||u.scrollLeft,a.bct=G.clientTop||u.clientTop,a.bcl=G.clientLeft||u.clientLeft,a.dsw=G.scrollWidth||u.scrollWidth,a.dsh=G.scrollHeight||u.scrollHeight,a.bcw=G.clientWidth||u.clientWidth,a.bch=G.clientHeight||u.clientHeight,a.dsw&&a.dsh&&sb){var b=Math.round((a.dsl+a.bcw)/a.dsw*100);100<b&&(b=100);if(b<Oa||!Oa)Oa=b;if(b>Ma||!Ma)Ma=b;var c=Math.round((a.dst+a.bch)/a.dsh*100);100<c&&(c=100);if(c<Pa||!Pa)Pa=c;if(c>Na||!Na)Na=c}}catch(n){t(n,
'gWH failed')}}function Ib(){try{a.ua=ab;a.pf=tb;a.app=ba.appName;a.os='';a.osc='';a.osv='';a.dc='';a.dct=0;a.cpu='';a.bn='';a.bv='';a.bc='';a.bcv='';a.bd='';a.md='';a.sp='';var b=ab.toLowerCase(),c=tb.toLowerCase(),n=b.indexOf('('),p=b.indexOf(')'),z='',r='',y='';-1<n&&(z=b.substring(0,n),z=qa(z),-1<p&&(r=b.substring(n+1,p),r=qa(r),y=b.substring(p+1),y=qa(y)))}catch(H){t(H,'gUA Ini failed');return}if(r){try{p={'windows phone os':'Windows Phone','windows phone':'Windows Phone','windows ce':'Windows CE',
mobile:'Windows Mobile'};var q={'10.0':'10','6.1':'7','6.3':'8.1','6.2':'8','5.1':'XP','6.4':'10','6.0':'Vista','5.2':'Server 2003','5.0':'2000'},h={'arch linux':'Arch Linux',linux:'Linux',freebsd:'FreeBSD',cros:'Chrome OS',fedora:'Fedora',sunos:'SunOS',openbsd:'OpenBSD',netbsd:'NetBSD'},k={ubuntu:'Ubuntu',fedora:'Fedora',suse:'SUSE',centos:'CentOS',debian:'Debian','red hat':'Red Hat',gentoo:'Gentoo',mint:'Mint',slackware:'Slackware',mandriva:'Mandriva'},g={iphone:'iPhone OS',macintosh:'Mac OS X',
ipad:'CPU OS',ipod:'iPhone OS','iPhone OS':'iOS','Mac OS X':'Mac OS X','CPU OS':'iOS'},f={iphone:'iPhone',macintosh:'Macintosh',ipad:'iPad',ipod:'iPod'};n={wow64:'Intel64',x86_64:'Intel64',x64:'Intel64',win64:'Intel64',amd64:'AMD64',' arm':'ARM',ppc64:'PPC64',ppc:'PPC',powerpc:'PPC',intel:'Intel',i686:'i686',win32:'IA-32',x86:'IA-32'};OST=r.split(';');var e=qa(OST[0]).split(' ');switch(e[0]){case 'windows':if('undefined'!==typeof e[1]&&'nt'===e[1]){a.os='Windows';e[2]+='';a.osv=e[2];a.osc='undefined'===
typeof q[e[2]]?'NT':q[e[2]];a.dc='PC';a.dct=1;for(var d in n)if(-1<r.indexOf(d)){a.cpu=d;break}'Windows'===a.os&&' arm'===a.cpu&&(a.os='Windows RT');break}case 'compatible':case 'mobile':if(-1<r.indexOf('windows')){if(-1<r.indexOf('windows nt'))a.os='Windows',a.osv=x(r,'windows nt',[';'],[';'],1,0,0),a.osc='undefined'===typeof q[a.osv]?'NT':q[a.osv],a.dc='PC',a.dct=1;else for(d in a.os='Windows',p)if(-1<r.indexOf(d)){a.os=p[d];a.osv=x(r,d,[';'],[';'],1,0,0);a.dc=d;a.dct=2;break}for(d in n)if(-1<r.indexOf(d)){a.cpu=
d;break}'Windows'===a.os&&' arm'===a.cpu&&(a.os='Windows RT')}break;case 'linux':if(-1===r.indexOf('android')){a.os='Linux';for(var m in k)if(-1<b.indexOf(m)){a.os=k[m];a.osv=x(b,m,[' ',';',')','(','/'],[';',' '],1,0,0);break}a.cpu=x(r,'linux',[';'],[';'],1,0,0);a.dc='PC';a.dct=1;break}case 'android':if(-1<r.indexOf('android')){a.os='Android';a.osv=x(r,'android',[';'],[';'],1,0,0);48E4<=a.sw*a.sh?(a.dc='Android Tablet',a.dct=3):(a.dc=a.os,a.dct=2);a.cpu=x(c,'linux',[],[],1,0,0);var u=OST.length-1;
if(0<u)for(var v=[],w=u;0<w;w--)if(OST[w]=qa(OST[w].toUpperCase()),0<OST[w].indexOf('BUILD')){v=OST[w].split(' ');if(2<v.length)a.bd=v[0],a.md=x(OST[w],' ',[' BUILD'],[],0,0,0);else{var I=v[0];m=[' ','-','_','/'];p=a;try{h=q=-1;k=0;l=m.length;for(g=0;g<l;g++)h=I.indexOf(m[g]),-1<h&&(-1===q||q>h)&&(q=h,k=m[g].length);-1<q?(p.bd=I.substring(0,q),p.md=I.substring(q+k)):p.bd=I}catch(H){t(H,'iStrS failed')}}break}}break;case 'iphone':a.dct=2;case 'macintosh':0===a.dct&&(a.dct=1);case 'ipad':0===a.dct&&
(a.dct=3);case 'ipod':0===a.dct&&(a.dct=2);a.bd='Apple';a.os=g[e[0]];d=a.os.toLowerCase();a.osv=ub(x(r,d,[' ',';'],[';'],1,0,0),0);''===a.osv&&'ipad'===e[0]&&(a.os='iPhone OS',d='iphone os',a.osv=ub(x(r,d,[' ',';'],[';'],1,0,0),0));a.dc=f[e[0]];for(d in n)if(-1<b.indexOf(d)){a.cpu=d;break}a.os=g[a.os];break;case 'x11':for(d in h)if(-1<r.indexOf(d)){a.os=h[d];if(-1<d.indexOf('linux'))for(m in k)if(-1<b.indexOf(m)){a.os=k[m];a.osv=x(b,m,[' ',';',')','(','/'],[';',' '],1,0,0);break}a.cpu=x(r,d,[';'],
[';'],1,0,0);a.dc='PC';a.dct=1;break}break;case 'blackberry':a.bd='BlackBerry',a.md=x(r,'blackberry',[' ',';','/'],[],1,1,0),a.os='BlackBerry OS',a.osv=x(b,'version',[' '],[' '],1,0,0),a.dc='BlackBerry',a.dct=2}a.cpu&&(a.cpu='undefined'===typeof n[a.cpu]?a.cpu.toUpperCase():n[a.cpu]);''===a.osc&&a.osv&&(a.osc=Sa(a.osv))}catch(H){t(H,'gOS failed')}try{I={edge:'Edge','opera mini':'Opera Mini','opera mobi':'Opera Mobi',opr:'Opera',opera:'Opera',firefox:'Firefox',chrome:'Chrome'};n={ucbrowser:'UC Browser',
micromessenger:'WeChat',mqqbrowser:'MQQ Browser',qqbrowser:'QQ Browser',qq:'QQ',oppobrowser:'Oppo Browser',samsungbrowser:'Samsung Browser',miuibrowser:'XiaoMi Browser',baidubrowser:'Baidu Browser',bidubrowser:'Baidu Browser',taobrowser:'TaoBao Browser',maxthon:'Maxthon','sogoumse,sogoumobilebrowser':'Sogou Browser',sogoumobilebrowser:'Sogou Browser',metasr:'SouGou Browser',oupeng:'Opera',lbbrowser:'LieBao Browser',baiduboxapp:'BaiduBoxApp',mb2345browser:'2345 Browser','360browser':'360 Browser',
yabrowser:'Yandex Browser',rockmelt:'RockMelt',seamonkey:'SeaMonkey'};m={iemobile:'IE Mobile',msie:'IE',' rv':'IE'};p={'opera mini':'Opera Mini','opera mobi':'Opera Mobi',opr:'Opera',opera:'Opera',version:'Opera'};if(y){BST=y.split(' ');var E=[];for(d=0;d<BST.length;d++)E.push(BST[d].split('/'))}if(-1<z.indexOf('opera')){a.bn='Opera';v=r+y;for(d in p)if(-1<v.indexOf(d)){a.bn=p[d];'Opera Mobi'===a.bn&&(d='version');a.bv=x(v,d,['/',' ',';',')','('],[],1,0,0);break}y.indexOf('webkit')>y.indexOf('presto')?
(a.bc=15<=Math.floor(a.bv)?'Blink':'WebKit',a.bcv=x(y,'webkit',['/',' ',';',')','('],[],1,1,0)):(a.bc='Presto',a.bcv=x(y,'presto',['/',' ',';',')','('],[],1,1,0))}else{if(y){for(w=u=E.length-1;0<=w;w--)if('undefined'!==typeof n[E[w][0]]){a.bn=n[E[w][0]];a.bv=x(y,E[w][0],['/',' ',';',')','('],[],1,1,0);break}else if('undefined'!==typeof n[E[w][1]]){a.bn=n[E[w][1]];a.bv=x(y,E[w][1],['/',' ',';',')','('],[],1,1,0);break}if(''===a.bn)for(w=u;0<=w;w--)if('undefined'!==typeof I[E[w][0]]){a.bn=I[E[w][0]];
a.bv=x(y,E[w][0],['/',' ',';',')','('],[],1,1,0);break}''===a.bn&&-1<y.indexOf('version')&&-1<y.indexOf('safari')&&(a.bn='Safari',a.bv=x(y,'version',['/',' ',';',')','('],[],1,1,0))}var X=a.bn;switch(e[0]){case 'windows':case 'compatible':case 'mobile':if(''===a.bn)for(d in m)if(-1<r.indexOf(d)){a.bn=m[d];a.bv=x(r,d,[' ',';','/',')','('],[],1,0,0);break}break;case 'android':case 'iphone':case 'ipad':case 'ipod':case 'blackberry':a.bn=a.bn?'Mobile '+a.bn:'Unknown Mobile Browser';break;default:-1<y.indexOf('mobile')&&
(a.bn=a.bn?'Mobile '+a.bn:'Unknown Mobile Browser')}if(y)for(z={presto:'Presto',gecko:'Gecko',applewebkit:'WebKit'},w=0;w<u;w++)if('undefined'!==typeof z[E[w][0]]){a.bc=z[E[w][0]];a.bcv='Gecko'===a.bc?x(r,'rv:',['/',' ',';',')','('],[],0,0,0):E[w][1];break}''===a.bc&&-1<r.indexOf('trident')&&(a.bc='Trident',a.bcv=x(r,'trident',['/',' ',';',')','('],[],1,0,0));'Edge'===X?(a.bc='EdgeHTML',a.bcv=a.bv):'WebKit'===a.bc&&('Chrome'===X&&28<=Sa(a.bv)||'Opera'===X&&15<=Sa(a.bv)?a.bc='Blink':-1<y.indexOf('chrome')&&
28<=Sa(x(y,'chrome',['/',' ',';',')','('],[],1,0,0))&&(a.bc='Blink'))}}catch(H){t(H,'gBS failed')}}if(''===a.os&&''!==c)try{for(d in r={win:'Windows','linux arm':'Android','linux arrch':'Android',ip:'iPhone OS',macintel:'Macintosh',linux:'Linux'},r)if(-1<c.indexOf(d)){a.os=r[d];break}}catch(H){t(H,'gOSPF failed')}if(b)try{for(d in c={'baiduspider-ads':'BaiduSpider-Ads','baiduspider-image':'BaiduSpider-Image',baiduspider:'BaiduSpider','googlebot-image':'GoogleBot-Image','adsbot-google-mobile':'Google-AdsBot-Mobile',
'adsbot-google':'Google-AdsBot',googlebot:'GoogleBot',bingbot:'BingBot',bingpreview:'BingBot','sogou spider':'SogouSpider','sogou web spider':'SogouSpider',haosouspider:'HaosouSpider','360spider':'HaosouSpider',yisouspider:'YisouSpider',sosospider:'SosoSpider','spider-ads':'BaiduSpider-Ads',yandexbot:'YandexBot',yandexmobilebot:'YandexMobileBot',yandeximages:'YandexBot-Image','cloudflare-alwaysonline':'CloudFlare-Cache',dnyzbot:'DnyzBot'},c)if(-1<b.indexOf(d)){pb=1;a.dc='Robot';a.dct=4;a.sp=c[d];
break}}catch(H){t(H,'gRBT failed')}}function qa(a){try{return a.replace(/(^\\s*)|(\\s*$)/g,'')}catch(c){t(c,'iTrim failed')}}function x(a,c,n,p,m,r,q){try{var b=a;a=0===q?a:1===q?a.toLowerCase():2===q?a.toUpperCase():a;var h=0===r?a.indexOf(c):a.lastIndexOf(c);if(-1<h){h+=c.length;var k=p.length;for(c=0;c<k;c++)if(b.substring(h,h+1)===p[c])return'';r=p=-1;k=n.length;for(c=0;c<k;c++)r=a.indexOf(n[c],h+1),-1<r&&(-1===p||p>r)&&(p=r);h+=m;return-1<p?b.substring(h,p):b.substring(h)}return''}catch(g){t(g,
'iStrR failed')}}function ub(a,c){try{switch(c){case 0:return a.replace(/_/g,'.')}return a}catch(n){t(n,'iFormat failed')}}function Ba(a){try{var b=(new Date).getTime();if(0===a)return b-va;if(!1===F)return 1<a?b-R:0;switch(a){case 1:return F.responseEnd>=F.requestStart?F.responseEnd-F.requestStart:b-va;case 2:return F.domContentLoadedEventEnd>=F.navigationStart?F.domContentLoadedEventEnd-F.navigationStart:b-R;case 3:return F.loadEventEnd>=F.navigationStart?F.loadEventEnd-F.navigationStart:b-R}}catch(n){return t(n,
'gTI failed'),1<a?b-R:0}}function eb(a){try{if(a){var b=a.match(/.*:\/\/([^\/]*).*/);return b?b[1].toLowerCase():''}return''}catch(n){t(n,'gDM failed')}}function Jb(){try{var b;a.cs=m.charset||m.characterSet;a.dt=L(m.title+'');if(b=q.location.href+''){var c=b.indexOf('cahm_visa=');-1<c&&(b=b.substring(0,c-1))}a.pg=b?L(b):'';cb=b?ha(b,vb):'';c=b.toLowerCase();-1<c.indexOf('utm_source=')&&(a.utms=L(x(b,'utm_source',['&'],[],1,0,1)),a.utmm=L(x(b,'utm_medium',['&'],[],1,0,1)),a.utmt=L(x(b,'utm_term',
['&'],[],1,0,1)),a.utmc=L(x(b,'utm_content',['&'],[],1,0,1)),a.utmp=L(x(b,'utm_campaign',['&'],[],1,0,1)));if(b=m.referrer+'')c=b.indexOf('cahm_visa='),-1<c&&(b=b.substring(0,c-1));a.rf=b?L(b):'';fb=b?ha(b,vb):'';a:try{if(a.se='',a.sen='',a.kw='',b){try{c=[];var n='baidu:wd:Baidu baidu:word:Baidu baidu:query:Baidu baidu:src:Baidu google:q:Google www.google:+:Google/Organic android.googlequicksearchbox:+:Google/Android so.com/link:+:So sogou.com/link:+:Sogou 360.cn:q:360 haosou:q:Haosou sogou:query:Sogou sogou:w:Sogou sogou:keyword:Sogou sm.cn:q:SM so.com:q:So yahoo:p:Yahoo yahoo:q:Yahoo bing:q:Bing yandex:text:Yandex go.mail.ru:q:Mail duckduckgo:q:Duckduckgo msn:q:MSN aol:query:AOL aol:q:AOL auone:q:Auone 58:key:58 youdao:q:Youdao ask:q:Ask lycos:q:Lycos lycos:query:Lycos cnn:query:CNN virgilio:qs:Virgilio alice:qs:Alice najdi:q:Najdi seznam:q:Seznam rakuten:qt:Rakuten biglobe:q:Biglobe goo.ne:MT:Goo search.smt.docomo:MT:Docomo onet:qt:Onet onet:q:Onet kvasir:q:Kvasir terra:query:Terra rambler:query:Rambler rambler:q:Rambler babylon:q:Babylon search-results:q:Search-results avg:q:Avg comcast:q:Comcast incredimail:q:Incredimail startsiden:q:Startsiden centrum.cz:q:Centrum tut.by:query:TUT globo:q:Globo ukr:q:Ukr daum:q:Daum eniro:search_word:Eniro naver:query:Naver pchome:q:PCHome'.split(' ');
for(var p=0;p<n.length;p++)c.push(n[p].split(':'))}catch(f){t(f,'Init SE failed')}var z=eb(b);n='';p='';if(z)for(var r=0;r<c.length;r++){var y=c[r][0];0>y.indexOf('.')&&(y+='.');if(-1<z.indexOf(y)||-1<m.referrer.indexOf(y)){var u=c[r][1];if('+'===u){a.se=z;a.sen=c[r][2];a.kw='Keyword Not Defined';break a}n='&'+u+'=';var h=b.indexOf(n);-1===h&&(n='?'+u+'=',h=b.indexOf(n));if(-1<h){a.se=z;a.sen=c[r][2];var k=n.length;var g=b.indexOf('&',h+k);-1===g&&(g=b.length);p=b.substr(h+k,g-h-k);a.kw=p?L(p):'Keyword Not Defined';
'Baidu'!==a.sen||p||(a.sen='Baidu/Organic');break a}}}}}catch(f){t(f,'gSW failed')}}catch(f){t(f,'gDI failed')}}function Kb(){try{a.plugin='';var b=ba.plugins||'';if(b){for(var c=[],n=b.length,p=0;p<n;p++)c.push(b[p].name);return a.plugin=L(c.join('|'))}}catch(z){t(z,'gPI failed')}}function jb(){try{if(a.lvt=P('CA_LVT',0)||C,2E15<a.lvt||1E15>a.lvt||a.lvt>C)a.lvt=C}catch(b){t(b,'gVT failed')}}function Lb(){try{a.rd='';Aa=!1;var b=eb(q.location.href),c=eb(m.referrer);a.pd=b;if(a.se||(b.length>c.length?
0>b.indexOf(c):0>c.indexOf(b)))a.rd=c;a.se&&P('CA_RF5','')!==fb&&(Aa=!0,A('CA_RF5',fb,365,1))}catch(n){t(n,'cNS failed')}}function kb(){try{a.nv=0,a.vs=0,a.pv=0,V=!1,ua!==P('CA_VSD','')?(za=V=!0,a.vs=1,a.pv=1):P('CA_LAT',0)+fa<S?V=!0:Aa&&(V=!0),V&&(a.nv=1,a.pv=1,A('CA_LVT',C,365,1)),A('CA_LAT',S,fa,0),A('CA_VSD',ua,3,1)}catch(b){t(b,'cNV failed')}}function Mb(){try{a.nt=!1===ra?0:ra.type;var b=R-3E4,c=P('CA_PPI','');if(c){var n=c.split('-');2<n.length&&(!V&&!1===ra&&b<n[1]&&n[0]===cb&&(a.nt=1),a.lr=
n[2])}A('CA_PPI','',fa,0)}catch(p){t(p,'gLR failed')}}function lb(){try{if(!1===za){var b=P('CA_PV',0);a.pv=V?1:b?++b:1}A('CA_PV',a.pv,3,1);!1===za&&(b=P('CA_VS',0),a.vs=b?V?++b:b:1);A('CA_VS',a.vs,3,1);b=P('CA_TPV',0);a.tpv=b?++b:a.pv;a.tpv<a.pv&&(a.tpv=a.pv);A('CA_TPV',a.tpv,365,1);pa=(b=P('CA_TVS',0))?V?++b:b:a.vs;pa<a.vs&&(pa=a.vs);A('CA_TVS',pa,365,1)}catch(c){t(c,'gVV failed')}}function Nb(){try{if(D)clearInterval(Za);else if(!Z){na++;var b=na;try{if(db(),a.mx!==v[1]||a.my!==v[2]||a.dst!==v[3]||
a.mbi!==v[4]||a.dsl!==v[5]||a.dsh!==v[6]||a.dsw!==v[7]||a.bcw!==v[8]||a.bch!==v[9]||a.bcl!==v[10]||a.bct!==v[11]){T&&!a.mbi&&(a.mx=ma,a.my=la);a.mbi&&ia&&(a.mx=ka,a.my=ja);a.mbi=T;ia&&(ja=ka=la=ma=ia=T=0);wa=[b-v[0],a.mx-v[1]||'',a.my-v[2]||'',a.dst-v[3]||'',a.mbi-v[4]||'',a.dsl-v[5]||'',a.dsh-v[6]||'',a.dsw-v[7]||'',a.bcw-v[8]||'',a.bch-v[9]||'',a.bcl-v[10]||'',a.bct-v[11]||''];for(var c=0,n=[],p=0;12>p;p++)wa[p]&&(c=p);for(p=0;p<=c;p++)n[p]=wa[p];aa.push(n);v=[b,a.mx,a.my,a.dst,a.mbi,a.dsl,a.dsh,
a.dsw,a.bcw,a.bch,a.bcl,a.bct]}}catch(z){t(z,'gRS failed')}0===na%150?(J(7),aa=[],v=[0,0,0,0,0,0,0,0,0,0,0,0],4===U?U=1:U++):50===na&&(J(7),ea=0)}}catch(z){t(z,'RVA failed')}}function Ob(){try{(new Date).getTime()-xa>fa&&(J(4),Z=1)}catch(b){t(b,'CT failed')}}function Ta(){try{1===Z&&(Z=3,Ra(sa+'/ca.php?wakeup=1&tz='+ob+'&r='+Math.random()))}catch(b){t(b,'eW failed')}}function Pb(){try{if(!D){try{q.addEventListener?(q.addEventListener('click',Ha),q.addEventListener('mousedown',Ea),q.addEventListener('mouseup',
Fa),q.addEventListener('mousemove',Da),q.addEventListener('mousewheel',Ga),q.addEventListener('touchstart',mb),q.addEventListener('touchmove',nb),q.addEventListener('scroll',Ia),m.addEventListener('submit',Ja),m.addEventListener('keydown',wb)):q.attachEvent&&(m.attachEvent('onclick',Ha),m.attachEvent('onmousedown',Ea),m.attachEvent('onmouseup',Fa),m.attachEvent('onmousemove',Da),m.attachEvent('onmousewheel',Ga),m.attachEvent('onscroll',Ia),m.attachEvent('onsubmit',Ja),m.attachEvent('onkeydown',wb))}catch(b){t(b,
'AE failed')}qb=Ba(2);a.cs=(m.charset||m.characterSet)+'';a.dt=L(m.title+'');db();J(2);Za=setInterval(Nb,100);setInterval(Ob,1E3)}}catch(b){t(b,'eR failed')}}function Ka(){try{if(!D){u=m.body;G=m.documentElement;rb=Ba(3);a.cs=(m.charset||m.characterSet)+'';a.dt=L(m.title+'');sb=1;db();J(3);Qb();try{var b=document.getElementsByTagName('video');if(!(1>b.length||'function'!==typeof b[0].addEventListener))for(var c=0;c<b.length;c++)new Rb(b[c])}catch(n){t(n,'addVideoEvent failed')}}}catch(n){t(n,'eL failed')}}
function La(){D||J(5)}function Ca(){D||J(5)}function Ha(b){try{if(!D){b=b||window.event;var c=b.srcElement||b.target;if(!('undefined'===typeof c||''!==da&&gb+450>(new Date).getTime())){var n=N!==a.mx||M!==a.my?0:1;N=a.mx;M=a.my;K=Ua(c,n,'INPUT'!==c.tagName&&'BUTTON'!==c.tagName||'submit'!==c.type?0:3);''!==K&&J(8)}}}catch(p){t(p,'eC failed')}}function Ea(b){try{if(!D){Ta();1>a.pa&&(a.pa=1);b=b||window.event;if(b.pageX||b.pageY)ma=b.pageX,la=b.pageY,T=b.button+1;else switch(ma=b.clientX+u.scrollLeft-
u.clientLeft,la=b.clientY+u.scrollTop-u.clientTop,b.button){case 1:T=1;break;case 4:T=2;break;case 2:T=3;break;default:T=b.button+1}xa=(new Date).getTime()}}catch(c){t(c,'eD failed')}}function Fa(a){try{D||(a=a||window.event,a.pageX||a.pageY?(ka=a.pageX,ja=a.pageY):(ka=a.clientX+u.scrollLeft-u.clientLeft,ja=a.clientY+u.scrollTop-u.clientTop),ia=1)}catch(c){t(c,'eP failed')}}function Da(b){try{D||(Ta(),2>a.pa&&(CPA++,2<CPA&&(a.pa=2)),b=b||window.event,b.pageX||b.pageY?(a.mx=b.pageX,a.my=b.pageY):(a.mx=
b.clientX+u.scrollLeft-u.clientLeft,a.my=b.clientY+u.scrollTop-u.clientTop),N!==a.mx&&(N=-1),M!==a.my&&(M=-1),xa=(new Date).getTime())}catch(c){}}function Ga(b){try{2>a.pa&&(a.pa=2)}catch(c){t(c,'eMW failed')}}function mb(b){try{3>a.pa&&(a.pa=3)}catch(c){t(c,'eTS failed')}}function nb(b){try{a.pa=4}catch(c){t(c,'eTM failed')}}function Ia(){try{D||Ta()}catch(b){t(b,'eS failed')}}function wb(b){try{if(!D){Ta();b=b||window.event;var c=b.srcElement||b.target;13!==(b.which?b.which:b.keyCode)||'INPUT'!==
c.tagName&&'BUTTON'!==c.tagName||(N=a.mx,M=a.my,da=Ua(c,0,4),gb=(new Date).getTime())}}catch(n){t(n,'eS failed')}}function Sb(a){function b(){try{u=m.body,G=m.documentElement,G.scrollLeft,u.scrollLeft,G.scrollTop,u.scrollTop,G.scrollWidth,u.scrollWidth,G.scrollHeight,u.scrollHeight,a&&a()}catch(n){setTimeout(b,1)}}setTimeout(b,1)}function Ra(a){try{if(!a)return!1;var b=m.getElementsByTagName('head')[0],n=m.createElement('script');n.setAttribute('src',a);n.setAttribute('type','text/javascript');b.appendChild(n);
return!0}catch(p){t(p,'dL failed')}}function Ua(a,c,n){try{if('undefined'===typeof a||1===Tb[a.tagName])return'';var b='',m='',r='',t='',v='',h='',k='',g='',f='',e='',d='',x='',D='',F='',w=0,I=0,E=0,X=0,H=0,xb=0,yb=0,B={},C,A;for(A=a;A;)0===xb&&(X=A.offsetWidth||0,H=A.offsetHeight||0,xb=1),0===yb&&'fixed'===(q.getComputedStyle?q.getComputedStyle(A,null).position:A.currentStyle.position)&&(I+=u.scrollLeft||G.scrollLeft||0,E+=u.scrollTop||G.scrollTop||0,yb=1),I+=A.offsetLeft||0,E+=A.offsetTop||0,A=
A.offsetParent;f=3===n?'Submit By Click':4===n?'Submit By Enter':'';e=hb(a.innerText,256);x=a.id;do{if('undefined'===typeof a.tagName)break;''===v&&(v=hb(a.href,1024));''===t&&(t=hb(a.onclick,1024));''===g&&(g=Ub(a.outerHTML));''===k&&(k=a.id?a.id:'');m=a.tagName;d=a.className?a.className:'';w=0;for(C=a.previousSibling;C;)F=C.tagName+'',F===m&&w++,C=C.previousSibling;''===k&&(b=m+'('+d+')->'+b);''===h&&k&&(h=k+':'+b);D=m+'['+w+']('+d+')->'+D;r=m+'['+w+']->'+r;a=a.parentNode}while('HTML'!==a.tagName);
h+='HTML('+g+')'+(v||t?'{'+(v||t)+'}':'');B.id=ha(x,2);B.html=ha(h,2);B.tag=ha(D,2);B.node=ha(r,2);B.href=v||t||f;B.act=n?n:v?1:t?2:0;B.txt=B.act?e:'';B.rpc=c;B.x=N>I&&N<I+X?O((N-I)/X*100):-1;B.y=M>E&&M<E+H?O((M-E)/H*100):-1;B.bcl=u.clientLeft||G.clientLeft||0;B.bct=u.clientTop||G.clientTop||0;B.bcw=u.clientWidth||G.clientWidth||0;B.bch=u.clientHeight||G.clientHeight||0;-1===B.x?(B.mx=O(I+Math.random()*X),B.x=O(100*Math.random())):B.mx=N;-1===B.y?(B.my=O(E+Math.random()*H),B.y=O(100*Math.random())):
B.my=M;oa=B.act;Va=0<oa?(new Date).getTime()+1E3:0;return B}catch(Xb){return''}}function Ub(a){try{if(!a)return'';a+='';a=a.replace(/\\s/g,'');512<a.length&&(a=a.substring(0,384)+'...'+a.substr(-128,128));return a}catch(c){return''}}function hb(a,c){try{if(!a)return'';a+='';a=a.replace(/(^\\s*)|(\\s*$)/g,'');a.length>c&&(a=a.substring(0,c-3)+'...');return L(a)}catch(n){return''}}function Ja(){try{D||(''!==da&&gb+500>(new Date).getTime()?(K=da,J(8)):3!==K.act&&(ya--,K.act=3,K.href='Submit By Click',
K.fix=1,J(8)),da='')}catch(b){t(b,'submitEvent failed')}}function Rb(a){try{this.obj=a,this.maxtime=this.duration=0,a.addEventListener('play',zb),a.addEventListener('pause',zb)}catch(c){t(c,'videoEvent failed')}}function zb(b){try{if(!D&&(N!==a.mx||M!==a.my)){b=b||window.event;N=a.mx;M=a.my;var c=b.srcElement||b.target;'undefined'!==typeof c&&(K=Ua(c,0,5),''!==K&&J(8))}}catch(n){t(n,'getVideoClick failed')}}function Qb(){try{for(var b=m.getElementsByTagName('iframe'),c=0;c<b.length;c++)Vb.track(b[c],
function(){D||(N=a.mx,M=a.my,K=Ua(this,0,6),''!==K&&J(8))})}catch(n){t(n,'addIframeEvent failed')}}function ha(a,c){try{var b=function(a){var b='',c;for(c=0;3>=c;c++){var d=a>>>8*c&255;d='0'+d.toString(16);b+=d.substr(d.length-2,2)}return b},p=function(a,b,c,d,g,e,f){a=u(a,u(u(c^(b|~d),g),f));return u(a<<e|a>>>32-e,b)},m=function(a,b,c,d,g,e,f){a=u(a,u(u(b^c^d,g),f));return u(a<<e|a>>>32-e,b)},r=function(a,b,c,d,e,g,f){a=u(a,u(u(b&d|c&~d,e),f));return u(a<<g|a>>>32-g,b)},q=function(a,b,c,d,g,e,f){a=
u(a,u(u(b&c|~b&d,g),f));return u(a<<e|a>>>32-e,b)},u=function(a,b){var c=a&2147483648;var d=b&2147483648;var e=a&1073741824;var g=b&1073741824;var f=(a&1073741823)+(b&1073741823);return e&g?f^2147483648^c^d:e|g?f&1073741824?f^3221225472^c^d:f^1073741824^c^d:f^c^d};if(''===a)return'';var h=[],k;a=function(a){a=a.replace(/\\r\\n/g,'\\n');for(var b='',c=0;c<a.length;c++){var d=a.charCodeAt(c);128>d?b+=String.fromCharCode(d):(127<d&&2048>d?b+=String.fromCharCode(d>>6|192):(b+=String.fromCharCode(d>>12|
224),b+=String.fromCharCode(d>>6&63|128)),b+=String.fromCharCode(d&63|128))}return b}(a);h=function(a){var b=a.length;var c=b+8;for(var d=16*((c-c%64)/64+1),e=Array(d-1),g,f=0;f<b;)c=(f-f%4)/4,g=f%4*8,e[c]|=a.charCodeAt(f)<<g,f++;c=(f-f%4)/4;e[c]|=128<<f%4*8;e[d-2]=b<<3;e[d-1]=b>>>29;return e}(a);var g=1732584193;var f=4023233417;var e=2562383102;var d=271733878;for(k=0;k<h.length;k+=16){var v=g;var x=f;var A=e;var w=d;g=q(g,f,e,d,h[k+0],7,3614090360);d=q(d,g,f,e,h[k+1],12,3905402710);e=q(e,d,g,f,
h[k+2],17,606105819);f=q(f,e,d,g,h[k+3],22,3250441966);g=q(g,f,e,d,h[k+4],7,4118548399);d=q(d,g,f,e,h[k+5],12,1200080426);e=q(e,d,g,f,h[k+6],17,2821735955);f=q(f,e,d,g,h[k+7],22,4249261313);g=q(g,f,e,d,h[k+8],7,1770035416);d=q(d,g,f,e,h[k+9],12,2336552879);e=q(e,d,g,f,h[k+10],17,4294925233);f=q(f,e,d,g,h[k+11],22,2304563134);g=q(g,f,e,d,h[k+12],7,1804603682);d=q(d,g,f,e,h[k+13],12,4254626195);e=q(e,d,g,f,h[k+14],17,2792965006);f=q(f,e,d,g,h[k+15],22,1236535329);g=r(g,f,e,d,h[k+1],5,4129170786);d=
r(d,g,f,e,h[k+6],9,3225465664);e=r(e,d,g,f,h[k+11],14,643717713);f=r(f,e,d,g,h[k+0],20,3921069994);g=r(g,f,e,d,h[k+5],5,3593408605);d=r(d,g,f,e,h[k+10],9,38016083);e=r(e,d,g,f,h[k+15],14,3634488961);f=r(f,e,d,g,h[k+4],20,3889429448);g=r(g,f,e,d,h[k+9],5,568446438);d=r(d,g,f,e,h[k+14],9,3275163606);e=r(e,d,g,f,h[k+3],14,4107603335);f=r(f,e,d,g,h[k+8],20,1163531501);g=r(g,f,e,d,h[k+13],5,2850285829);d=r(d,g,f,e,h[k+2],9,4243563512);e=r(e,d,g,f,h[k+7],14,1735328473);f=r(f,e,d,g,h[k+12],20,2368359562);
g=m(g,f,e,d,h[k+5],4,4294588738);d=m(d,g,f,e,h[k+8],11,2272392833);e=m(e,d,g,f,h[k+11],16,1839030562);f=m(f,e,d,g,h[k+14],23,4259657740);g=m(g,f,e,d,h[k+1],4,2763975236);d=m(d,g,f,e,h[k+4],11,1272893353);e=m(e,d,g,f,h[k+7],16,4139469664);f=m(f,e,d,g,h[k+10],23,3200236656);g=m(g,f,e,d,h[k+13],4,681279174);d=m(d,g,f,e,h[k+0],11,3936430074);e=m(e,d,g,f,h[k+3],16,3572445317);f=m(f,e,d,g,h[k+6],23,76029189);g=m(g,f,e,d,h[k+9],4,3654602809);d=m(d,g,f,e,h[k+12],11,3873151461);e=m(e,d,g,f,h[k+15],16,530742520);
f=m(f,e,d,g,h[k+2],23,3299628645);g=p(g,f,e,d,h[k+0],6,4096336452);d=p(d,g,f,e,h[k+7],10,1126891415);e=p(e,d,g,f,h[k+14],15,2878612391);f=p(f,e,d,g,h[k+5],21,4237533241);g=p(g,f,e,d,h[k+12],6,1700485571);d=p(d,g,f,e,h[k+3],10,2399980690);e=p(e,d,g,f,h[k+10],15,4293915773);f=p(f,e,d,g,h[k+1],21,2240044497);g=p(g,f,e,d,h[k+8],6,1873313359);d=p(d,g,f,e,h[k+15],10,4264355552);e=p(e,d,g,f,h[k+6],15,2734768916);f=p(f,e,d,g,h[k+13],21,1309151649);g=p(g,f,e,d,h[k+4],6,4149444226);d=p(d,g,f,e,h[k+11],10,3174756917);
e=p(e,d,g,f,h[k+2],15,718787259);f=p(f,e,d,g,h[k+9],21,3951481745);g=u(g,v);f=u(f,x);e=u(e,A);d=u(d,w)}switch(c){default:case 0:temp=b(g)+b(f)+b(e)+b(d);break;case 1:temp=b(f)+b(e);break;case 2:temp=b(f)}return temp.toLowerCase()}catch(I){return t(I,'MD5 failed'),''}}try{var fa=18E5,vb=1,C='{$rn}',ua='{$ft}',xc='{$sid}',ob='{$tz}',Gb='{$ipdb}',sa='{$host}',bb='{$errHost}/errlog.php?',ta='{$verify_result}',S=(new Date).getTime(),q=window,m=document,u=document.body,G=document.documentElement,Ab=location.protocol+
'//',ib=screen,ba=navigator,ab=ba.userAgent+'',tb=ba.platform+'',O=parseInt,Sa=parseFloat,Q=encodeURIComponent,Db=decodeURIComponent,R=q._caq_rt||S,va=0,ca=0,qb=0,rb=0,pa=0,F=!1,ra=!1,D=!1;CPA=0;try{'undefined'!==typeof q.performance.timing.responseEnd&&null!==q.performance.timing.responseEnd&&(F=q.performance.timing)}catch(b){F=!1}try{'undefined'!==typeof q.performance.navigation.type&&null!==q.performance.navigation.type&&(ra=q.performance.navigation)}catch(b){ra=!1}sa=Ab+sa;bb=Ab+bb;var Qa=sa+
'/core.php?',Wb=sa+'/ca.php?siteid='+xc;C=O(C);St=O(C/1E3);'1'===ta?ta=P('CAHM_VISA',''):A('CAHM_VISA','',0,0);if(58===ta.length){var Bb=1E3*O(ta.substr(32,10));if(S>Bb||S+9E7<Bb)A('CAHM_VISA','',0,0),console.log('CA: Visa has been expired, Please apply a new visa!');else{Ra(Wb+'&cahm_visa='+ta+'&rnd='+Math.random());D=!0;return}}var Za,na=0,sb=0,N=0,M=0,ma=0,la=0,ka=0,ja=0,T=0,ia=0,aa=[],v=[0,0,0,0,0,0,0,0,0,0,0,0],wa=[],Y=[],xa=S,Z=0,oa=0,Va=0,K='',Wa='',da='',gb=0,cb='',fb='',W=0,Xa=0,V=!1,Aa=
!1,Ma=0,Na=0,Oa=0,Pa=0,ea=0,U=1,ya=0,pb=0,za=!1,Ya=!1}catch(b){t(b,'Init CA data failed');return}var Vb={arr:[],timer:null,obj:function(a,c){this.el=a;this.fn=c;this.tf=!1},track:function(a,c){try{if(this.arr.push(new this.obj(a,c)),!this.timer){var b=this;this.timer=setInterval(function(){b.check()},100)}}catch(p){t(p,'iframe track failed')}},del:function(a){for(var b in this.arr)a==this.arr[b].el&&this.arr.pop(this.arr[b])},destory:function(){for(var a in this.arr)this.arr.pop(this.arr[a])},check:function(){try{if(m.activeElement){var a=
m.activeElement,c;for(c in this.arr)a===this.arr[c].el?0==this.arr[c].tf&&(this.arr[c].fn.apply(a,[]),this.arr[c].tf=!0):this.arr[c].tf=!1}}catch(n){t(n,'iframe check failed')}}};try{if(D)return;var a={};a.ct=(new Date).getHours();a.pg='';a.pv=0;a.dt='';a.pa=0;try{Hb();try{a.sw=ib.width+0,a.sh=ib.height+0,a.cd=ib.colorDepth+0,a.tc=q.hasOwnProperty?q.hasOwnProperty('ontouchstart')?1:0:0}catch(b){a.tc=0,t(b,'gMI failed')}Ib();Jb();try{a.ce=ba.cookieEnabled?1:0,a.lan=(ba.systemLanguage||ba.language)+'',a.lan=a.lan.toLowerCase()}catch(b){t(b,
'gNI failed')}Kb();jb();Lb();kb();Mb();lb();try{2===a.nt?(a.se='',a.sen='',a.kw='',a.rd='',a.rf='',a.fmt=2):a.fmt=a.se?4:a.rd?5:a.rf?2:3}catch(b){t(b,'gRI failed')}Eb()}catch(b){t(b,'Init CA property failed')}!1!==F&&(ca=Ba(1));J(1);a.mx=0;a.my=0;a.mbi=0}catch(b){t(b,'Build CA failed');return}try{if(D)return;Sb(Pb);q.addEventListener?(q.addEventListener('load',Ka),q.addEventListener('beforeunload',La),q.addEventListener('unload',Ca)):q.attachEvent&&(q.attachEvent('onload',Ka),q.attachEvent('onbeforeunload',
La),q.attachEvent('onunload',Ca))}catch(b){t(b,'add events failed');return}var Tb={BR:1,SCRIPT:1,STYLE:1}})();

			";
		}
		
		header('Content-type: application/javascript');
		
		echo $ca;

	//****************** RECORD PERFORMANCE BEGIN *****************
	//*********** connect redis start ************
	$REDIS_0 = new Redis();
	if ($REDIS_0->CONNECT(REDIS_IP_0, REDIS_PORT_0) !== true) exit('//*/');
	$REDIS_0->SELECT(REDIS_DB_0);//select redis No 0 database for record process infomation & query list
	//************ connect redis end *************
	$REDIS_0->MULTI()
			->INCRBY('PerformanceConsumeJS', (int)(microtime(true) * 1E6) - $rn)//set performance consume
			->INCR('PerformanceCountJS')//set performance count
			->EXEC();
	//******************* RECORD PERFORMANCE END ******************	

}


exit('//*/');



function verify_cahm($sid, $ip, $visa) {

		$v = substr($visa, 0, 32); 
		$t = substr($visa, 32, 10);
		$n = time();
		if ($t < $n) return 0;
		
		if (empty($ip)) return 0;
		
		$matchvisa = md5($sid . $t . ENCODE_FACTOR);

		if ($v === $matchvisa) return 1;

		return 0;
	
}

function get_ip() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER)) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if ((bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return $ip;
				}
			}
		}
		return '';
}


function use_db($siteid) {

		$con = mysqli_connect(DB_HOST_LOCAL, ROOT_USER_LOCAL, ROOT_PASSWORD_LOCAL);
		if (mysqli_connect_errno($con)) {
			exit('//*/');
 		}
	
		$db_selected = mysqli_select_db($con, 'site'.$siteid);
		if (!$db_selected) {
 			mysqli_close($con);
			exit('//*/');
  		}
		
		return $con;

}

function check_block($ref, $siteid, $ip, &$tz, &$ipdb, &$REDIS_2) {

//echo '5-1, ';
		if ($REDIS_2->EXISTS($siteid.'-Updating') === true) return check_block_mysql($ref, $siteid, $ip, $tz, $ipdb);
//echo '5-2, ';
		if ($REDIS_2->EXISTS($siteid.'-UpdateTime') === true AND $REDIS_2->SCARD($siteid.'-1') > 0) {//return data from redis cache
//echo '5-3, ';	
			return check_block_redis($ref, $siteid, $ip, $tz, $ipdb, $REDIS_2);
		} else {//return data from mysql
//echo '5-4, ';
			pclose(popen('php -f ' . __DIR__ . '/cache_settings.php ' . $siteid . ' 0 &', 'r'));//argv[2] = 0, normal update domains & settings cache data
			return check_block_mysql($ref, $siteid, $ip, $tz, $ipdb);
		}
	
}

function check_block_redis($ref, $siteid, $ip, &$tz, &$ipdb, &$REDIS_2) {
//echo '5-3-0, ';
		//check sitestatus
		$RETURN_ARRAY = $REDIS_2->MGET(array($siteid.'-SiteStatus', $siteid.'-TimeZone', $siteid.'-IPDatabase'));
		if ($RETURN_ARRAY[0] !== '0') exit('//*/');
//echo '5-3-1, ';
		//check blocked IP of setting
		check_block_url_redis(5, $ip, $siteid, $REDIS_2);
//echo '5-3-2, ';
		//get settings
		$tz = $RETURN_ARRAY[1];//$REDIS_2->GET($siteid.'-TimeZone');
		$ipdb = $RETURN_ARRAY[2];//$REDIS_2->GET($siteid.'-IPDatabase');
		if (empty($tz) || is_null($ipdb)) {
			pclose(popen('php -f ' . __DIR__ . '/cache_settings.php ' . $siteid . ' 1 &', 'r'));//argv[2] = 1, force update domains & settings cache data
			return check_block_mysql($ref, $siteid, $ip, $tz, $ipdb);
		}

		//check blocked site
		$siteurl = parse_url($ref, PHP_URL_SCHEME) . '://' . parse_url($ref, PHP_URL_HOST);
		if ($siteurl) {
			check_block_url_redis(3, $siteurl, $siteid, $REDIS_2);
		} else {
			exit('//*/');
		}
//echo '5-3-3, ';
		//check blocked page
		check_block_url_redis(4, $ref, $siteid, $REDIS_2);

		//check domain
		$subdomain = parse_url($ref, PHP_URL_HOST);
		$domain = substr($subdomain, stripos($subdomain, '.') + 1);
		if (stripos($domain, '.') === false) $domain = '';
		if ($domain) {
			if (check_block_url_redis(1, $domain, $siteid, $REDIS_2) === false) {
				if (check_block_url_redis(1, $subdomain, $siteid, $REDIS_2) === false) {
					check_block_url_redis(2, $subdomain, $siteid, $REDIS_2);
				}
			}
		} else if ($subdomain) {
			if (check_block_url_redis(1, $subdomain, $siteid, $REDIS_2) === false) {
				check_block_url_redis(2, $subdomain, $siteid, $REDIS_2);
			}
		} else {
			exit('//*/');
		}

}

function check_block_url_redis($type, $ref, $siteid, &$REDIS_2) {

		switch ($type) {
		case 1://check domain
//echo '5-8-1, ';
			$md5 = md5($ref);
			if ($REDIS_2->SISMEMBER($siteid.'-1', $md5)) return true;
			return false;
		case 2://record filter domain
//echo '5-8-2, ';
			$REDIS_2->ZADD($siteid.'-BlockedTime', time(), $ref);
			$REDIS_2->ZINCRBY($siteid.'-BlockedCount', 1, $ref);
			exit('//*/');
		case 3://check blocked site
		case 4://check blocked page
		case 5://check blocked IP
//echo '5-8-3, ';
			$md5 = md5($ref);
			if ($REDIS_2->SISMEMBER($siteid.'-'.$type, $md5)) {
				$REDIS_2->ZADD($siteid.'-BlockedTime', time(), $ref);
				$REDIS_2->ZINCRBY($siteid.'-BlockedCount', 1, $ref);
				exit('//*/');
			}
			break;
		}
	
}



function check_block_mysql($ref, $siteid, $ip, &$tz, &$ipdb) {
//echo '5-4-0, ';
		//connect database
		$con = use_db($siteid);
//echo '5-4-1, ';
		//check blocked ip
		check_block_url_mysql(5, $ip, $con);
//echo '5-4-2, ';
		//get settings
		$result = mysqli_query($con, "SELECT TimeZone,IPDatabase,SiteStatus FROM setting WHERE pKey=0");
		if ($result && mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			if ($row['SiteStatus'] !== '0') {
				mysqli_close($con);
				exit('//*/');
			}
			$tz = $row['TimeZone'];		
			$ipdb = $row['IPDatabase'];	
		} else {
			exit('//*/');
		}
//echo '5-4-3, ';
		//check blocked site
		$siteurl = parse_url($ref, PHP_URL_SCHEME) . '://' . parse_url($ref, PHP_URL_HOST);
		if ($siteurl) {
			check_block_url_mysql(3, $siteurl, $con);
		} else {
			mysqli_close($con);
			exit('//*/');
		}
//echo '5-4-4, ';
		//check blocked page
		check_block_url_mysql(4, $ref, $con);

		//check domain
		$subdomain = parse_url($ref, PHP_URL_HOST);
		$domain = substr($subdomain, stripos($subdomain,'.') + 1);
		if (stripos($domain,'.') === false) $domain = '';
		if ($domain) {
			if (!check_block_url_mysql(1, $domain, $con)) {
				if (!check_block_url_mysql(1, $subdomain, $con)) {
					check_block_url_mysql(2, $subdomain, $con);
					mysqli_close($con);
					exit('//*/');
				}
			}
		} else if ($subdomain) {
			if (!check_block_url_mysql(1, $subdomain, $con)) {
				check_block_url_mysql(2, $subdomain, $con);
				mysqli_close($con);
				exit('//*/');
			}
		} else {
			mysqli_close($con);
			exit('//*/');
		}

		mysqli_close($con);

}

function check_block_url_mysql($type, $ref, $con) {

		switch ($type) {
		case 1://check domain
//echo '5-9-1, ';
			$md5 = md5($ref);
			$result = mysqli_query($con, "SELECT DomainType FROM domain WHERE MD5='$md5'");
			if ($result && mysqli_num_rows($result)) {
				while ($row = mysqli_fetch_assoc($result)) {
					$domainType = (int)$row['DomainType'];
					if ($domainType === 1) {
						mysqli_free_result($result);
						return true;
					} else if ($domainType === 2) {
						mysqli_query($con, "UPDATE domain SET BlockedTimes=BlockedTimes+1 WHERE MD5='$md5'");
						mysqli_free_result($result);
						mysqli_close($con);
						exit('//*/');
					}
				}
				mysqli_free_result($result);
				return true;
			} else {
				return false;
			}
			break;
		case 2://insert fliter domain
//echo '5-9-2, ';
			$now = time();
			$md5 = md5($ref);
			mysqli_query($con, "INSERT INTO domain(Domain,MD5,BlockedTimes,DomainType,CreateTime) VALUES('{$ref}','{$md5}',1,2,{$now})");
			break;
		case 3://check blocked site
		case 4://check blocked page
		case 5://check blocked IP
//echo '5-9-3, ';
			$md5 = md5($ref);
			$result = mysqli_query($con, "SELECT MD5 FROM domain WHERE MD5='{$md5}'");
			if ($result && mysqli_num_rows($result)) {
				mysqli_query($con, "UPDATE domain SET BlockedTimes=BlockedTimes+1 WHERE MD5='{$md5}'");
				mysqli_free_result($result);
				mysqli_close($con);
				exit('//*/');
			}
			break;
		}
	
}

?>