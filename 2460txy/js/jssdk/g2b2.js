var g2b=function(){function e(t,n){var o=document.createElement("script");o.type="text/javascript",o.onload=function(){n()},o.onerror=function(){o.parentNode.removeChild(o),setTimeout(function(){e(t,n)},1e3)},o.src=t,document.getElementsByTagName("head")[0].appendChild(o)}function t(e){}var n,o,a,s,i,r,d,l,c,m,g,u,p={SHOWRECHARGE:"msg_recharge",GETORDERNO:"msg_get_order_no",RETURNORDERNO:"mgs_ret_order_no",RECHARGE_CALLBACK:"msg_recharge_cb",INIT:"msg_init",SET_SHARE:"msg_share_init",SHOWQRCODE:"msg_qr",RETURN_FOCUSSTATE:"msg_focus",SHARE_CALLBACK:"msg_share_cb",REPORTDATA:"msg_report",SENDTODESKTOP:"msg_send_desktop",SENDTODESKTOP_CALLBACK:"msg_send_desktop_cb",RECHARGE_PAY:"msg_pay",CHECKSHARE:"msg_check",FOCUS_GETSTATE:"msg_get_foucus",INIT_CALLBACK:"msg_init_cb",RETURNSHARE:"msg_ret_share",ON_LOGINERROR:"msg_error",FOCUS_RETURNSTATE:"msg_ret_focus",CHECKDOWNLOAD:"msg_check_download",RETURNDOWNLOAD:"msg_download",WEIBO_SHARE:"msg_weibo_share"},y="",h=!1,f={},E=navigator.userAgent,b=(E.indexOf("Android")>-1,function(e){for(var t=location.search,n=t.substr(1,t.length-1).split("&"),o=0;o<n.length;o++)if(n[o].split("=")[0]==e)return n[o].split("=")[1]}),v=function(){var e=window.frameHeight,t=frameWidth>frameHeight?.6*frameHeight:frameWidth;return"&frameHeight="+e+"&frameWidth="+t},C=function(){var e=location.search,t={};if(!e.length)return t;for(var n=e.substr(1,e.length-1).split("&"),o=0;o<n.length;o++)t[n[o].split("=")[0]]=n[o].split("=")[1];return t},_=function(){return b("passIds")},I=function(e,t,n){var o=document.createElement("iframe");return o.scrolling="no",o.style.width=Math.ceil(frameWidth>frameHeight?.6*frameHeight:frameWidth)+"px",o.style.height=(window.frameHeight||window.innerHeight)+"px",o.style.margin="auto",o.style.position="absolute",o.style.top="0",o.style.left="0",o.style.backgroundColor="white",o.id=t,o.frameborder="no",o.style.border="none",o.border="0px",o.style.zIndex=99,(n||document.body).appendChild(o),o.src=e,o},R=function(e){if("object"==typeof e){for(var t="?",n=Object.keys(e),o=0;o<n.length;o++)t+=0==o?n[o]+"="+e[n[o]]:"&"+(n[o]+"="+e[n[o]]);return t}},A=function(e,t){localStorage.setItem(e,t)},T=function(e,t){var n={};n.identify="g2460",n.msg=e,n.data=t,c.contentWindow.postMessage(n,"*")},w=function(e,t,n,o){var n=n||{},a=n.type||"get",s=n.data||null;try{var i=new XMLHttpRequest;if(i.open(a,e,!0),i.onreadystatechange=function(){if(4==i.readyState){var n=JSON.parse(i.responseText);if(n.c<0)return void S(n.m);if("error"==i.responseText)return void alert("\u8bf7\u6c42"+e+"\u8fd4\u56deerror");t&&t(n)}},o)try{i.setRequestHeader("Content-Type",o)}catch(r){alert(r)}i.send(s)}catch(r){return!1}},S=function(e,t){var n=document.createElement("div");n.id="toast",n.style.width="260px",n.style.height="130px",n.style.margin="auto",n.style.fontSize="16px",n.style.position="absolute",n.style.top="0",n.style.bottom="0",n.style.right="0",n.style.left="0",n.style.zIndex="999";var o=document.createElement("div");o.innerText=e,o.style.margin="auto",o.style.position="absolute",o.style.top="0",o.style.bottom="0",o.style.right="0",o.style.left="0",o.style.width="250px",n.appendChild(o),n.classList.add("toast");var a=document.getElementById("recharge")&&"block"==document.getElementById("recharge").style.display?document.getElementById("recharge"):document.body;a.appendChild(n),setTimeout(function(){a.removeChild(n)},t||2e3)},x=function(e,t,n){document.getElementById("confirm")&&document.body.removeChild(document.getElementById("confirm"));var o=document.createElement("div"),a=document.getElementById("recharge")&&"block"==document.getElementById("recharge").style.display?document.getElementById("recharge"):document.body;o.id="confirm",o.style.width="270px",o.style.height="160px",o.style.position="absolute",o.style.margin="auto",o.style.top="30%",o.style.left=0,o.style.right=0,o.style.zIndex="999",o.style.fontSize="16px",o.innerText=e,o.classList.add("toast");var s=document.createElement("button");s.style.position="absolute",s.style.left="40px",s.style.top="120px",s.innerText="\u786e\u5b9a",s.style.height="30px",s.classList.add("button"),s.classList.add("blue"),s.onclick=function(){a.removeChild(o),t&&t()};var i=document.createElement("button");i.style.top="120px",i.style.position="absolute",i.style.right="40px",i.innerText="\u53d6\u6d88",i.style.height="30px",i.classList.add("button"),i.classList.add("blue"),i.onclick=function(){a.removeChild(o),n&&n()},o.appendChild(s),o.appendChild(i),a.appendChild(o)},O=function(e){if(void 0!=e){s=e,document.getElementById("recharge").style.display="block";for(var t=0;t<e.length;t++)D(e[t],t)}},D=function(e,t){var n=document.createElement("div"),o=document.createElement("img");o.src=e.icon,o.style.margin="auto",o.style.left="20px",o.style.top="0",o.style.bottom="0",o.style.position="absolute";var a=document.createElement("div");a.style.top="20px",a.style.position="absolute",a.style.margin="auto",a.style.right="0",a.style.left="0",a.innerText=e.itemName;var s=document.createElement("div");s.style.bottom="20px",s.style.position="absolute",s.style.margin="auto",s.style.right="0",s.style.left="0",s.innerText=e.desc;var i=document.createElement("div");i.style.top="0",i.style.bottom="0",i.style.position="absolute",i.style.margin="auto",i.style.right="2px",i.innerText="\u8d2d\u4e70",i.classList.add("buybtn"),i.onclick=function(){d=e.id,l=e.itemName,cindex=t,T(p.GETORDERNO,{amount:e.amount,id:e.id})},n.appendChild(o),n.appendChild(a),n.appendChild(s),n.appendChild(i),n.classList.add("payItem"),document.getElementById("items").appendChild(n)},k=function(){return s[i]},B=function(){window.addEventListener("message",function(e){var n=e.data;if(n.identify&&"g2460"==n.identify)switch(n.msg){case p.INIT:N(n.data);break;case p.SHOWRECHARGE:O(n.data);break;case p.RETURNORDERNO:var o=n.data.orderData,a=n.data.amount;try{j(a,o)}catch(e){alert(e)}break;case p.SET_SHARE:t(n.data),g.showShare();break;case p.WEIBO_SHARE:g.weiboShare(n.data);break;case p.SHOWQRCODE:g.showQrCode();break;case p.REPORTDATA:g.reportData(n.data);break;case p.SENDTODESKTOP:g.sendToDesktop();break;case p.FOCUS_GETSTATE:g.checkFocus(n.data);break;case p.RECHARGE_PAY:j(n.data.amount,n.data.orderData);break;case p.CHECKSHARE:g.isOpenShare();break;case p.CHECKDOWNLOAD:g.isDownloadable();break;case p.ON_LOGINERROR:g.onLoginError()}else switch(n){case"shareSuccess":T(p.SHARE_CALLBACK,!0);break;case"shareCancel":T(p.SHARE_CALLBACK,!1);break;case"shareok":T(p.SHARE_CALLBACK,!0)}})},H=function(e,t){var n=b("passIds");n&&n.split(",").length<=1?(t&&t(1),L(e)):t&&t(2)},L=function(t,n){var o=t&&t.passId||_(),a=t&&t.appId||b("appId"),s="/index.php/enter/login/"+o+"/"+a;try{A("screenInfo",v()),A("appId",a),A("passId",o)}catch(i){}"hiwan"==o?e("http://g.hwwh5.com/js/hww_sdk_sub.js",function(){Hwwsdk.onuserinfo(function(e){var o=e.regfrom;Hwwsdk.onshare(function(e){1==e.status?T(p.SHARE_CALLBACK,!0):T(p.SHARE_CALLBACK,!1)},o),t.userAccount=e.userAccount,t.nickname=encodeURIComponent(e.nickname),t.sex=e.sex,t.headImgUrl=encodeURIComponent(e.headImgUrl),s+=R(t),w(s,function(e){0==e.c&&(document.getElementById("gameDiv").style.display="block",c=I(e.d.url+v(),"gameFrame",document.getElementById("gameDiv")),"landscape"==e.d.orientation&&(c.width="100%",c.height="100%",c.style.width="",c.style.height="",c.style.marginTop="0px",document.getElementById("gameDiv").style.position="",document.getElementById("gameDiv").style.marginTop="0px",document.getElementById("gameDiv").width="100%",document.getElementById("gameDiv").style.height="0px",document.getElementById("gameDiv").style.backgroundColor="green"),document.body.removeChild(document.getElementById("gbox")),c.onload=function(){h||(B(),h=!0),document.getElementById("loader").style.display="none"},n&&n())})}),Hwwsdk.userInfo()}):(f=t,s+=R(t),w(s,function(e){0==e.c&&(document.getElementById("gameDiv").style.display="block",c=I(e.d.url+v(),"gameFrame",document.getElementById("gameDiv")),"landscape"==e.d.orientation&&(c.width="100%",c.height="100%",c.style.width="",c.style.height="",document.getElementById("gameDiv").style.position="",document.getElementById("gameDiv").style.marginTop="0px",document.getElementById("gameDiv").width="100%",document.getElementById("gameDiv").style.height="0px",document.getElementById("gameDiv").style.backgroundColor="white"),document.body.removeChild(document.getElementById("gbox")),c.onload=function(){h||(B(),h=!0),document.getElementById("loader").style.display="none"},n&&n())}))},N=function(t){n=t.platform,o=t.platform,y=t.appid,u=t.server_id,r=t.game_id,a=t.shareInfo,m=t.test?"http://"+location.host:"http://h5sdk.zytxgame.com",t.test?e("http://"+location.host+"/js/platform.js?v="+(new Date).getTime(),function(){e("http://"+location.host+"/js/platforms/PLATFORM_"+n+".js?v="+(new Date).getTime(),function(){g=new pf(g2b,a,f,{appId:y,passId:o,gameId:r})})}):e(m+"/js/platform.js?v="+(new Date).getTime(),function(){e(m+"/js/platforms/PLATFORM_"+n+".js?v="+(new Date).getTime(),function(){g=new pf(g2b,a,f,{appId:y,passId:o,gameId:r})})})},K=function(e,t){var e=e||{},n=m+"/index.php/api/createPay"+e.search;w(n,function(e){var n=e;t&&t(n)})},W=function(){},j=function(e,t){g.pay(e,t)};return{init:N,checkLogin:H,getParameters:C,getParameter:b,object2search:R,getDataXHR:w,loadScript:e,getScreenInfo:v,login:L,toastMsg:S,showConfirm:x,showQrCode:W,citem:k,createPay:K,MESSAGES:p,postMessage:T}}();