var g_g2b;
var is_inited = false;

var pf = function(g2b, shareInfo, pf_params, passData) {
    this.g2b = g2b;
    g_g2b = g2b;
    this.shareInfo = shareInfo;
    this.pf_params = pf_params;

    this.passData = passData;
    this.reyunurl = "";
    this.init();
};
pf.prototype = new platform();

pf.prototype.init = function() {
    var hlmy_gw = "";
    var appKey = "";
    var that = this;

    that.g2b.postMessage(that.g2b.MESSAGES.INIT_CALLBACK);
}

pf.prototype.pay = function(amount, orderData) {
    console.log("amount " + amount);
    console.log("orderData " + orderData);
    console.log(" goodsName " + orderData.subject);
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
    var search = this.g2b.object2search(param);
    this.g2b.createPay({
        search: search
    }, function(res) {
        var generate_order_id = res.d.order_id;

        var real_money = amount / 100;
        // var real_money = 1; //  for test

        var goodsName = orderData.subject;
        var roleName = orderData.appUserName;
        var callBackInfo = generate_order_id;


        parent.postMessage({
            eventType: 'openPay',
            data: {
                // 充值金额（人民币：元） 只能为整数
                money: real_money,
                // 游戏的充值订单编号 最多32位
                mark: generate_order_id,
                // 服务器编号 不能为空或者0
                server: 1,
                // 透传参数，用于充值成功后的服务端回调地址
                extra: generate_order_id
            }
        }, '*');


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
        var nickName = encodeURIComponent(data.rolename);
        var level = data.rolelevel;
        var power = data.power;
        var currency = data.currency;
        var cproleid = data.cproleid;
        var url = "//h5sdk.zytxgame.com/index.php/api/login/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&level=" + level + "&power=" + power + "&currency=" + currency + "&cproleid=" + cproleid;


        this.g2b.getDataXHR(url, function(response) {});
    } else if (data.action == 'create_role') {
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = encodeURIComponent(data.rolename);

        var cproleid = data.cproleid;
        var url = "//h5sdk.zytxgame.com/index.php/api/create_role/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&cproleid=" + cproleid;
        this.g2b.getDataXHR(url, function(response) {});
    } else if (data.action == 'enterCreate') {
        var roleid = data.roleid;
        var srvid = data.srvid;

        var url = "//h5sdk.zytxgame.com/index.php/api/sign_collect/ftnn/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid;
        this.g2b.getDataXHR(url, function(response) {});
    }
};
pf.prototype.logout = function() {};
pf.prototype.showQrCode = function() {
    console.log("pf showQrCode called");
};
pf.prototype.isOpenShare = function() {
    this.g2b.postMessage(this.g2b.MESSAGES.RETURNSHARE, false);
};


pf.prototype.showShare = function() {
    console.log("shareShare");
    this.g2b.postMessage(this.g2b.MESSAGES.SHARE_CALLBACK, false);

}


pf.prototype.isDownloadable = function() {
    this.g2b.postMessage(this.g2b.MESSAGES.RETURNDOWNLOAD, false);
};
