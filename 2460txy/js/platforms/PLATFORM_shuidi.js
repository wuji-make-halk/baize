var shareInit = false;
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
    this.g2b.loadScript('http://www.youmengh5.com/static/sdk/cy.sdk.js', function() {
        var sdk = window.CY_GAME_SDK;
        var params = {
            gameId: that.pf_params.gameId, //游戏的ID
            share: {
                success: function() { /*分享好友成功回调*/
                    if(shareInit){
                    that.g2b.postMessage(that.g2b.MESSAGES.SHARE_CALLBACK, true);
                    }

                }
            },
            pay: {
                success: function() { /* 支付成功回调方法（仅针对于快捷支付方式有效）*/
                    console.log('ok');
                }
            }
        };
        sdk.config(params); //初始化
        that.g2b.postMessage(that.g2b.MESSAGES.INIT_CALLBACK);
    })

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
        // var readl_money = 1;

        var url = "http://h5sdk.zytxgame.com/index.php/api/sign_order/" + that.passData.passId + "/" + that.passData.appId + "?order_id=" + generate_order_id +
            "&money=" + readl_money + "&role=" + param.cproleid + "&userId=" + that.pf_params.uid + "&goodsName=" + param.goodsName + "&game_id=" + that.pf_params.gameId + "&server=" + param.ext;

        this.g2b.getDataXHR(url, function(response) {
            if (response.c == 0) {
                sdk.pay({
                    gameId: that.pf_params.gameId,
                    uid: that.pf_params.uid,
                    time: response.d.time, //10位时间戳
                    server: param.ext,
                    role: param.cproleid,
                    goodsId: readl_money,
                    goodsName: response.d.goodsName,
                    money: readl_money,
                    cpOrderId: generate_order_id,
                    ext: generate_order_id,
                    sign: response.d.sign,
                    signType: "md5"
                });
            }
        });

    });
};
pf.prototype.checkFocus = function(data) {

    // var url = "http://h5sdk.zytxgame.com/index.php/api/focus/" + this.passData.passId + "?openid=" + data.openId;
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
        var url = "//h5sdk.zytxgame.com/index.php/api/login/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&level=" + level + "&power=" + power + "&currency=" + currency + "&cproleid=" + cproleid;

        this.g2b.getDataXHR(url, function(response) {
            console.log(JSON.stringify(response));
        });
    } else if (data.action == 'create_role') {
        var roleid = data.roleid;
        var srvid = data.srvid;
        var nickName = encodeURIComponent(data.rolename);
        var cproleid = data.cproleid;
        var url = "//h5sdk.zytxgame.com/index.php/api/create_role/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid + "&nickname=" + nickName + "&cproleid=" + cproleid;
        this.g2b.getDataXHR(url, function(response) {
            console.log(JSON.stringify(response));
        });
    } else if (data.action == 'enterCreate') {
        var roleid = data.roleid;
        var srvid = data.srvid;

        var url = "//h5sdk.zytxgame.com/index.php/api/sign_collect/" + this.passData.passId + "/" + this.passData.appId + "?roleid=" + roleid + "&srvid=" + srvid;
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
    this.g2b.postMessage(this.g2b.MESSAGES.RETURNSHARE, true);
};
pf.prototype.showShare = function() {
    console.log('click share button');
    alert('点击右上角分享至微信好友');
    shareInit = true;
    // this.g2b.postMessage(this.g2b.MESSAGES.SHARE_CALLBACK, true);
    var that = this;
    sdk.showShare({
        title: that.shareinfo.title,
        desc: that.shareinfo.desc,
        imgUrl: that.shareinfo.imgUrl
    });

}
pf.prototype.isDownloadable = function() {
    // this.g2b.postMessage(this.g2b.MESSAGES.RETURNDOWNLOAD, "http://img.h5sdk.zytxgame.com/img/android_apk/lcby/%E9%BE%99%E5%9F%8E%E9%9C%B8%E4%B8%9A.apk");
};
