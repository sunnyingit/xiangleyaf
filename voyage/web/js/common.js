var voyage = {

    // 显示遮罩
    loading: function()
    {
        var height = Math.max(document.documentElement.clientHeight, document.documentElement.scrollHeight);
        $('#div_loading_mask').show().css({"width": "480px", "height": height + "px"});
    },

    // 隐藏遮罩
    unload: function()
    {
        $('#div_loading_mask').hide();
    },

    // 刷新顶部个人信息
    reloadTop: function()
    {
        if (typeof android != 'undefined' && android != null) {
            android.loadTitle(); // in mobile webview mode
        } else if (typeof top.ifr_head != 'undefined') {
            top.ifr_head.location.reload(); // in web iframe mode
        }
    },

    // 获取HP/精力/移动力条的宽度
    getPropWidth: function(currentValue, maxValue, maxWidth)
    {
        return Math.min(maxWidth, Math.ceil((currentValue / maxValue) * maxWidth));
    }
};

function sleep(ms)
{
    var endTime = new Date().getTime() + ms;
    while (new Date().getTime() < endTime);
}

function __(str, vars)
{
    if (voyage.langs[str] == undefined || !voyage.langs[str]) {
        return '';
    }

    if (vars == undefined) {
        return voyage.langs[str];
    }

    var searchs = [];
    var replaces = [];

    for (key in vars) {
        searchs.push('{' + key + '}');
        replaces.push(vars[key]);
    }

    return str_replace(searchs, replaces, voyage.langs[str]);
}