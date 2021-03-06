var nick_name;
var isAndroid;
var ios_or_android;
var isiOS;
var n_name;
var pf = function(g2b, shareInfo, pf_params, passData) {
    this.g2b = g2b;
    this.shareInfo = shareInfo;
    this.pf_params = pf_params;

    this.passData = passData;
    this.reyunurl = "";
    this.init();
};
pf.prototype = new platform();

pf.prototype.init = function() {
    // var u = navigator.userAgent;
    // isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    // isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    // if (isiOS) {
    // 	ios_or_android = '1';
    // } else if (isAndroid) {
    // 	ios_or_android = '2';
    // } else {
    // 	ios_or_android = '3';
    // }
    // alert(JSON.stringify(this.pf_params));
    // alert(this.pf_params);
    // alert(JSON.stringify(this.passData));
    this.g2b.postMessage(this.g2b.MESSAGES.INIT_CALLBACK);

};

pf.prototype.pay = function(amount, orderData) {
    console.log("amount " + amount);
    console.log("orderData " + orderData);
    var param = {};
    param.openId = orderData.openId;
    param.openKey = orderData.openKey;
    param.appId = this.passData.appId;
    param.money = amount;
    param.orderNo = ios_or_android;
    param.ext = orderData.ext || "";
    param.data = orderData.actor_id; //  put actor_id into data
    param.goodsName = orderData.subject;
  param.cproleid = orderData.cproleid;
    param.platform = 'kemengus';
    var search = this.g2b.object2search(param);
    this.g2b.createPay({
        search: search
    }, function(res) {
        // alert("res " + res.d);
        var generate_order_id = res.d.order_id;
        var user_id = res.d.userId;
        var readl_money = amount / 100;
        // var readl_money = 0.01;
        var subject = orderData.subject;
        var goosid = 1;
        switch (readl_money) {
            case 6:
                goosid = 'com.6.cny.lovelive';
                break;
            case 30:
                goosid = 'com.30.cny.lovelive';
                break;
            case 98:
                goosid = 'com.ninety.eight.cny';
                break;
            case 128:
                goosid = 'com.one.hundred.cny';
                break;
            case 328:
                goosid = 'com.328.cny.lovelive';
                break;
            case 648:
                goosid = 'com.648.cny.lovelive';
                break;
            default:
                alert('goodid error');
                break;
        }
        loadScript("//picstatic.dkmol.net/js/aksdk.js", function() {
            var payInfo = {
                "cpbill": Date.parse(new Date()), // 用于游戏方存放订单号
                "productid": goosid, // 商品标识
                "productname": subject, // 商品名
                "productdesc": subject, // 商品说明
                "ApplePrdId": goosid,
                "serverid": param.ext, // 服务器编号,字符串类型
                "servername": orderData.ext, // 服务器名字
                "roleid": orderData.actor_id, // 角色id
                "rolename": n_name, // 角色名
                "rolelevel": 1, // 角色等级,int 类型
                "price": readl_money, // 价格(元)(float 类型)
                "extension": generate_order_id
            };
            // alert(JSON.stringify(payInfo));
            AKSDK.pay(payInfo, function(status, data) {
                // alert("status = " + status + " " + "data=" + JSON.stringify(data));

            });
        });

        closePayWindow();

    });
};
pf.prototype.checkFocus = function(data) {
    this.g2b.postMessage(this.g2b.MESSAGES.FOCUS_RETURNSTATE, -1);
};
pf.prototype.reportData = function(data) {

    if (data.action == 'enterGame') {
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = data.rolename;
        n_name = nickName;
        // var nickName = encodeURIComponent(data.rolename);
        var level = data.rolelevel;
        var t = new Date();
        var time = t.getTime();
        AKSDK.logEnterGame(srvid, srvid, roleid, nickName, level, time);
        var power = data.power;
        var currency = data.currency;
        var cproleid = data.cproleid;
        var url = "//h5sdk.zytxgame.com/index.php/api/login/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&level=" + level + "&power=" + power + "&currency=" + currency + "&cproleid=" + cproleid;

        this.g2b.getDataXHR(url, function(response) {});
    } else if (data.action == 'create_role') {
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = data.rolename;
        // var nickName = encodeURIComponent(data.rolename);
        var t = new Date();
        var time = t.getTime();
        AKSDK.logCreateRole(srvid, srvid, roleid, nickName, 0, time);

        var cproleid = data.cproleid;
        var url = "//h5sdk.zytxgame.com/index.php/api/create_role/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&cproleid=" + cproleid;
        this.g2b.getDataXHR(url, function(response) {});
    } else if (data.action == 'enterCreate') {
        var roleid = data.roleid;
        var srvid = data.srvid;

        var url = "//h5sdk.zytxgame.com/index.php/api/sign_collect/kemengus/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid;
        this.g2b.getDataXHR(url, function(response) {});
    }
};
pf.prototype.logout = function() {
    function logout() {
        AKSDK.logout(function(status, data) {
            document.getElementById('result').innerHTML = "status = " + status + " " + "data=" + JSON.stringify(data);
        });
    }
};
pf.prototype.showQrCode = function() {
    console.log("pf showQrCode called");

};
pf.prototype.isOpenShare = function() {
    this.g2b.postMessage(this.g2b.MESSAGES.RETURNSHARE, false);
};
pf.prototype.isDownloadable = function() {
    // if(isAndroid){

    // alert(this.g2b.postMessage(this.g2b.MESSAGES.RETURNDOWNLOAD, "http://cdn.data.video.iqiyi.com/cdn/ppsgame/20170705/upload/unite/pps/H5/lcby0705.apk"));
    // }else{
    this.g2b.postMessage(this.g2b.MESSAGES.RETURNDOWNLOAD, false);
    // }
};
