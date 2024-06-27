$(document).ready(function(){
    let timer = null, index = 0,obj = $('.maoScroll-main ul'), max = obj.find('li').length;
    //console.log(max)
    timer = setInterval(function(){
        if(index >= max) index = 0;
        var h = (obj.find('li').eq(0).height());
        obj.css('margin-top',(index*-1*h)+'px');
        obj.css('-webkit-transition','0.5s');
        index++;
    },1000);
    //$('.company').html(ElmConfig.banhao);
    //init();
    // $('.submit').unbind('click').bind('click',function(){
    //     doPayDetail();
    //     return false;
    // })
    // if(ElmConfig.data.out_trade_no != null && ElmConfig.data.out_trade_no != 0){
    //     ElmConfig.data.is_back = 1;
    //     $('.pop-wl').css('display','flex');
    //     $('.pon-win-1').show();
    // }
});
function init(){
    updateConfig();
    updatePayList();
}
updateCompany = function(){
    var obj = $('.rule_content .cont'), html = obj.html();
    if(html != null) {
        $('#service_url').attr('href',ElmConfig.UrlKeFu2);
        $('.company').html(ElmConfig.banhao);
        html = html.replace(/AppName/g, ElmConfig.name);
        html = html.replace(/CompanyName/g, ElmConfig.companyname);
        obj.html(html);
    }
}
updateConfig = function(){
    var r = ElmAppUtil.UrlArgs();
    ElmConfig.data.channel = ElmAppUtil.isStrEmpty(r.channel) ? ElmConfig.data.platform : r.channel,
        ElmConfig.data.channel1 = ElmAppUtil.isStrEmpty(r.channel1) ? 0 : r.channel1,
        ElmConfig.data.paymethod = setPayMethod(ElmAppUtil.isStrEmpty(r.paymethod) ? 6 : r.paymethod),
        ElmConfig.data.phone = ElmAppUtil.isStrEmpty(r.phone) ? '' : r.phone,
        ElmConfig.data.out_trade_no = ElmAppUtil.isStrEmpty(r.out_trade_no) ? 0 : r.out_trade_no,
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.channel1) ? 0 : r.aoid;
    //console.log(ElmConfig.data.out_trade_no);
    if(ElmConfig.data.channel == 22){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.userkey) ? '': r.userkey;
    }else if(ElmConfig.data.channel == 18){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.landingid) ? '': r.landingid;
    }else if(ElmConfig.data.channel == 19){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.nadkey) ? '': r.nadkey;
    }else if(ElmConfig.data.channel == 17){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.bxm_id) ? '': r.bxm_id;
    }else if(ElmConfig.data.channel == 21){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.qcjParamStr) ? '': r.qcjParamStr;
    }else if(ElmConfig.data.channel == 24){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.r_id) ? '': r.r_id;
    }else if(ElmConfig.data.channel == 25){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.logId) ? '': r.logId;
    }else if(ElmConfig.data.channel == 26){
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.track_id) ? '': r.track_id;
    }else{
        ElmConfig.data.aoid = ElmAppUtil.isStrEmpty(r.a_oId) ? '': r.a_oId;
    }
}
updatePayList = function() {
    if (null != ElmConfig.data.payWay)
        return ElmConfig.data.referer_url = ElmConfig.data.payWay.referer_url;
    $.get(ElmConfig.serverUrl + "/api/MemberQyk/getPaylist?paymethod=" + ElmConfig.data.paymethod +'&channel='+ElmConfig.data.channel+'&channel2='+ElmConfig.data.channel1,
        function (e) {
            ElmConfig.data.payWay = e.data[0];
            if(e.data[1] != null) {
                ElmConfig.name = e.data[1]['open_name'];
                ElmConfig.banhao = e.data[1]['open_name'] + '<br>' + e.data[1]['name'] + '<br>' + e.data[1]['address'] + '<br>客服电话：' + e.data[1]['tel'] + '<br>' + e.data[1]['icp'];
                ElmConfig.companyname = e.data[1]['name'];
                ElmConfig.wechat_service = e.data[1]['wechat_kf'];
                ElmConfig.UrlKeFu2 = e.data[1]['service_url'];
            }
            updateCompany();
        });
}
setPayMethod = function(p){
    return isWechat() ? 4 : p;
}
isWechat = function(){
    return navigator.userAgent.toLowerCase().match(/MicroMessenger/i) == "micromessenger";
}
doPayDetail = function() {
    if(ElmConfig.valid) {
        let e = getReturnUrl();
        var ua = navigator.userAgent.toLowerCase();
        var isWeixin = isWechat();
        //showLoading();
        let i = {
            mobile: ElmConfig.data.phone,
            return_url: e,
            pay_id: ElmConfig.data.payWay.id,
            referer: window.location.href,
            paymethod: ElmConfig.data.paymethod,
            isweixin: isWeixin ? 1 : 0,
            channel: ElmConfig.data.channel,
            channel2: ElmConfig.data.channel1,
            aoid: ElmConfig.data.aoid,
            device_id: '',
            backurl: e,
            boxid: ElmConfig.data.is_back ? ElmConfig.data.boxid2 : ElmConfig.data.boxid,
            type: 3,
            trade_no: ElmConfig.data.out_trade_no
        };
        $.post(ElmConfig.serverUrl + "/api/OrderQyk/create_new",
            i,
            function (t) {
                // hideLoading();
                if (t.status == 200) {
                    ElmConfig.data.out_trade_no = t.data.out_trade_no,
                        //hideOrderPop(),
                        localStorage.removeItem("JinZhuBiJiPayCheck"),
                        localStorage.setItem("JinZhuBiJiPay", ElmConfig.data.channel + "_" + ElmConfig.data.phone);
                    localStorage.setItem("out_trade_no", t.data.order_id);
                    switch (i.paymethod) {
                        case "2":
                            window.location.href = t.data.data;
                            break;
                        case "4":
                        case "6":
                            window.location.href = t.data.data;
                            break;
                        default:
                            window.location.href = t.data.data
                    }
                } else {
                    alert(t.msg)
                }
            });
    }
    else{
        alert('今日活动已结束，敬请期待下次开启。');
        return false;
    }
}
getReturnUrl = function() {
    let t = window.location.href
        , e = t.indexOf("#/"),f=t.indexOf("?");
    if(e > -1)
        t = t.substring(0, e);
    if(f > -1)
        t = t.substring(0, f);
    return t;
}
startCheckPay = function(t) {
    //console.log('run here')
    if (!ElmAppUtil.isStrEmpty(ElmConfig.data.out_trade_no)) {
        interval_num = 5;
        clearInterval(ElmConfig.data._checkTimer);
        ElmConfig.data._checkTimer = setInterval(() => {
            checkPay(t);
            $('.countdown').html(interval_num + ' S');
            if (interval_num-- <= 0) {
                clearInterval(ElmConfig.data._checkTimer);
                window.location.href = "/"+ElmConfig.dir+'/?' + t;
            }
        }, 1e3)
    }else{
        console.log('no trade_no')
    }
}
checkPay = function(t) {
    if (ElmAppUtil.isStrEmpty(ElmConfig.data.out_trade_no))
        return;

    $.get(ElmConfig.serverUrl + "/api/OrderQyk/check_status?trade_no=" + ElmConfig.data.out_trade_no,
        function (i) {
            //hideLoading();
            let n = !1;
            if (null == i || null == i.data)
                n = !0;
            else {
                switch (i.status) {
                    case '200':
                        clearInterval(ElmConfig.data._checkTimer);
                        localStorage.removeItem('order_sn');
                        localStorage.removeItem('out_trade_no');
                        window.location.href = "success.html?phone=" + ElmConfig.data.phone + "&order_sn=" + ElmConfig.data.out_trade_no + "&out_trade_no=" + ElmConfig.data.out_trade_no
                        break;
                    default:
                        n = !0;
                        break;
                }
            }
            //onPhoneFocus(0);
        })
}
close_rule = function (){
    $('.pop-win').fadeOut('slow', 'linear', function () {
        $(this).hide();
    });
}
open_rule = function(){
    $('.pop-win').fadeIn('slow', 'linear', function () {
        $(this).show();
    });
}
open_ad = function (){
    $('.pon-win-1').hide();
    $('.pon-win-2').show();
}
goto_ad = function (){
    toLottery();
}
toLottery = function (){
    if(ElmConfig.data.channel == 17)
        location.href = 'https://game.adtianshi.cn/index/entry?appkey=1A392F27F4D1B5B12A0839D022FACD20&protocol=https';
    else
        location.href = 'https://u.tuiabcedc.cn/2c11';
}