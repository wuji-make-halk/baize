var cpNickname;
var cpLevel;
var cpSrvid;
var cpUid;
var cpPower;
var cpGkey;
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
    console.log('init done');
    var that = this;
    this.g2b.loadScript("https://wan.panda.tv/mobi_H5/xmwan.js", function() {
        cpUid = that.pf_params.rid;
        cpGkey = that.pf_params.gkey;
        that.g2b.postMessage(that.g2b.MESSAGES.INIT_CALLBACK);
    });
};

pf.prototype.pay = function(amount, orderData) {
    console.log("amount " + amount);
    console.log("orderData " + JSON.stringify(orderData));
    var that = this;
    var param = {};
    param.openId = orderData.openId;
    param.openKey = orderData.openKey;
    param.appId = this.passData.appId;
    param.money = amount;
    param.orderNo = orderData.orderNo;
    param.ext = orderData.ext || "";
    param.data = orderData.actor_id; //  put actor_id into data
    param.goodsName = orderData.subject;
    param.cproleid = orderData.cproleid;
    param.platform = this.passData.passId;
    var search = this.g2b.object2search(param);
    this.g2b.createPay({
        search: search
    }, function(res) {
        console.log(JSON.stringify(res));
        var generate_order_id = res.d.order_id;
        var userId = res.d.userId;
        var readl_money = amount / 100; // for test

        var url = "//" + location.host + "/index.php/api/sign_order/" + that.passData.passId + "/" + that.passData.appId;

        this.g2b.getDataXHR(url, function(response) {
            if (response.c == 0) {
                xmwan.open({
                    gkey: response.d.gkey,
                    skey: "S"+cpSrvid,
                    amount: readl_money,
                    uid: userId,
                    ext: generate_order_id
                });
            }
        });

    });
};
pf.prototype.checkFocus = function(data) {

    // var url = "//" + location.host + "/index.php/api/focus/" + this.passData.passId + "?openid=" + data.openId;
    // this.g2b.getDataXHR(url, function(response) {
    //     if (response.c == 0) {
    this.g2b.postMessage(this.g2b.MESSAGES.FOCUS_RETURNSTATE, -1);
    // } else {
    //     this.g2b.postMessage(this.g2b.MESSAGES.FOCUS_RETURNSTATE, -1);
    // }
    // });

};
pf.prototype.reportData = function(data) {
    console.log(JSON.stringify(data));
    if (data.action == 'enterGame') {
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = encodeURIComponent(data.rolename);
        var level = data.rolelevel;
        var power = data.power;
        var currency = data.currency;
        var cproleid = data.cproleid;
        var url = "//" + location.host + "/index.php/api/login/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&level=" + level + "&power=" + power + "&currency=" + currency + "&cproleid=" + cproleid;
        cpLevel = level;
        cpNickname = data.rolename;
        cpPower = power;
        cpSrvid = srvid;
        this.g2b.getDataXHR(url, function(response) {
            console.log(JSON.stringify(response));
        });
    } else if (data.action == 'create_role') {
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = encodeURIComponent(data.rolename);
        var cproleid = data.cproleid;
        var url = "//" + location.host + "/index.php/api/create_role/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&cproleid=" + cproleid;
        this.g2b.getDataXHR(url, function(response) {
            console.log(JSON.stringify(response));
        });
    } else if (data.action == 'level_up') {
        var my_roleid = data.roleid;
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = encodeURIComponent(data.rolename);
        var level = data.rolelevel;
        var cproleid = data.cproleid;
        var url = "//" + location.host + "/index.php/api/focus/" + this.passData.passId + "/" + this.passData.appId +
        "?rid=" + cpUid +
        "&level=" + level +
        "&name=" + data.rolename +
        "&gkey=" + cpGkey;
        this.g2b.getDataXHR(url, function(response) {
            console.log('level up');
            console.log(response);
        });

    } else if (data.action == 'enterCreate') {
        var roleid = data.roleid;
        var srvid = data.srvid;

        var url = "//" + location.host + "/index.php/api/sign_collect/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid;
        this.g2b.getDataXHR(url, function(response) {
            console.log(JSON.stringify(response));
        });
    }
};
pf.prototype.logout = function() {};
pf.prototype.showQrCode = function() {
    console.log("pf showQrCode called");
    // document.getElementById("qr_modal").style.display = "block";
};
pf.prototype.isOpenShare = function() {
    this.g2b.postMessage(this.g2b.MESSAGES.RETURNSHARE, false);
};
pf.prototype.showShare = function() {
    console.log('click share button');
    this.g2b.postMessage(this.g2b.MESSAGES.SHARE_CALLBACK, true);
}
pf.prototype.isDownloadable = function() {
    // this.g2b.postMessage(this.g2b.MESSAGES.RETURNDOWNLOAD, "http://img.h5sdk.zytxgame.com/img/android_apk/lcby/%E9%BE%99%E5%9F%8E%E9%9C%B8%E4%B8%9A.apk");
};
