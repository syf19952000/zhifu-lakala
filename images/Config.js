const ElmConfig = {
    UrlYongHu: '', // 用户协议
    UrlYinSi: '', // 隐私政策
    UrlGuoBaiGuiZe: '', // 过白页规则
    UrlKeFu: 'http://kf.furunzhongtian.com/index/index/kefu?id=4', // 客服
    UrlDown: '/dju88/down.html', // 下载页
    UrlSuccess: '/dju88/success.html', // 下载页
    UrlYQDown: '', // 邀请下载页
    serverUrl: 'https://18.qrxwlkj.top',
    dir:'dju38',
    valid: true,
    data: {
        channel: 0,
        channel1: 0,
        paymethod: 6,
        phone: "",
        out_trade_no: "",
        aoid:'',
        boxid: 2,
        agree: !0,
        payWay: null,
        isInCheck: !1,
        _checkTimer:null,
        boxid2: 21,
        is_back: 0,
    },
    name: '金猪笔记',
    banhao:'',
    companyname: '',
    wechat_service :'',
}
ElmConfig.updateOrigin = function(originStr) {
    //ElmConfig.serverUrl = originStr;
    ElmConfig.origin = originStr;
    ElmConfig.UrlYongHu = originStr + '/' + ElmConfig.dir + '/yonghu.html';
    ElmConfig.UrlYinSi = originStr + '/' + ElmConfig.dir + '/yinsi.html';
    ElmConfig.UrlGuoBaiGuiZe = originStr + '/' + ElmConfig.dir + '/huodong.html';
    ElmConfig.UrlKeFu = originStr + '/' + ElmConfig.dir + '/kefu/'; //
    ElmConfig.UrlKeFu2 = 'http://kf.furunzhongtian.com/index/index/kefu?id=4';
    ElmConfig.UrlYQDown = originStr + '/' + ElmConfig.dir + '/downyq.html';

    ElmConfig.name = '数慧社区';
    ElmConfig.banhao = '数慧社区<br>北京数慧生活科技有限公司<br>客服电话：18704551088<br>京ICP备2022030573号- 6';
    ElmConfig.companyname = '北京数慧生活科技有限公司';
};

ElmConfig.updateOrigin(window.location.origin);
function updateAppName(html){
    html = html.replace(/AppName/g,ElmConfig.name);
    html = html.replace(/CompanyName/g,ElmConfig.companyname);
    return html;
}
let ElmAppUtil = {
    args:{},
    isStrEmpty: function(t) {
        return null == t || "undefined" == t || "" == t
    },
    isWhitespace(t) {
        switch (t) {
            case " ":
            case "\t":
            case "\r":
            case "\n":
            case "\f":
                return !0;
            default:
                return !1
        }
    },
    trim: function(t) {
        if (null == t)
            return "";
        for (var e = 0; this.isWhitespace(t.charAt(e)); )
            ++e;
        for (var i = t.length - 1; this.isWhitespace(t.charAt(i)); )
            --i;
        return i >= e ? t.slice(e, i + 1) : ""
    },
    getQueryString: function (t) {
        var e = new RegExp("(^|&)" + t + "=([^&]*)(&|$)")
            , i = window.location.search;
        if (this.isStrEmpty(i) && (i = window.location.hash.split("?")[1]),
            this.isStrEmpty(i))
            return null;
        var n = i.substr(1).match(e);
        return null != n ? unescape(n[2]) : null
    },
    copy(t) {
        const e = document.createElement("input");
        e.setAttribute("value", t),
            document.body.appendChild(e),
            e.select(),
            document.execCommand("copy"),
            document.body.removeChild(e),
            showPopMessage("复制成功")
    },
    stopEmpty: function() {
        var t = window.event || arguments.callee.caller.arguments[0];
        t.preventDefault(), t.stopPropagation()
    },
    checkPhone: function(t) {
        if (this.isStrEmpty(t))
            return "请输入手机号";
        let e = t.match(RegPhone);
        return null == e || 0 == e.length ? "请输入正确的手机号" : null
    },
    checkCode: function(t) {
        if (this.isStrEmpty(t))
            return "请输入验证码";
        let e = t.match(RegCode);
        return null == e || 0 == e.length ? "验证码错误" : null
    },
     UrlArgs: function() {
         try {
             for (var url = location.href, allargs = url.split("?")[1], args = allargs.split("&"), i = 0; i < args.length; i++) {
                 var arg = args[i].split("=");
                 eval('this.args.' + arg[0] + '="' + arg[1] + '";')
             }
         } catch (t) {
         }
         return this.args;
     }
}