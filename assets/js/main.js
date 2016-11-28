(function(){
    var urlRex     = /((http|https):\/\/)([a-zA-Z0-9_-]+\.)*/,
    mobileRex      = /^1((3|5|7|8){1}\d{1}|70)\d{8}$/,
    defaultIcon    = "/tmp/icons/lemonself.ico",
    defaultTitle   = "信息处理中...",
    defaultCapture = "/tmp/icons/lemonself.png",
    updateQueue    = [];

    $(function() {
        init();

        $("#sbumitBookMarks").on("click", function(){
            var markUrl        = $("input[name=newMarkUrl]").val().trim(),
            markClassification = $("select[name=markClassification]").val();
            if (!urlRex.test(markUrl)) {
                layer.msg("URL不合法，必须以http/https开头");
                return false;
            } else {
                layer.load(1, {time: 90000, shade: [0.8, '#393D49']});
                var paramsData = {"url": markUrl, 'classification': markClassification};
                $.post("/user/mark/add", appendCsrf(paramsData), function(data){
                    $("input[name=newMarkUrl]").val("");
                    layer.closeAll();
                    updateCsrf(data.csrf);
                    if ("200" == data.code) {
                        layer.msg("添加书签成功");
                        var markHtml = getMarkHtml(data.mark);
                        $("#myBookMarks").prepend(markHtml);
                    } else {
                        layer.msg(data.msg);
                    }
                });
            }
        });

        $("input[name=newMarkUrl]").on("input", function(){
            var curVal = $(this).val();
            if (!urlRex.test(curVal)) {
                $(this).val("http://" + curVal);
            }
        });

        $("#qqLoginBtn").length && window.QC && QC.Login({
            btnId:"qqLoginBtn",
            scope:"get_user_info"
        }, function(reqData, opts){
            $("#qqLoginBtn").html('<img src="' + reqData.figureurl_qq_1 + '" title="' + reqData.nickname + '" alt="' + reqData.nickname + '">');
            QC.Login.check() && QC.Login.getMe(function(openId, accessToken){
                var paramsData = {
                    "openid": openId,
                    "nickname": reqData.nickname,
                    "gender": reqData.gender,
                    "figureurl_qq_1": reqData.figureurl_qq_1,
                    "figureurl_qq_2": reqData.figureurl_qq_2
                };
                $.post("/user/login", appendCsrf(paramsData), function(data){
                    updateCsrf(data.csrf);
                    if ("200" == data.code) {
                        layer.msg('登录成功');
                        setTimeout("location.reload(true)", 1000);
                    } else {
                        layer.msg(data.msg);
                    }
                });
                console.log("openId: " + openId + "accessToken: " + accessToken);
            })
        }, function(opts){
            location.href = "/";
        });

        var sortable = new Sortable($("#myBookMarks")[0], {
            animation: 400
        });

        $(".lm-marks").on("click", ".mk-x", function(){
            var self = $(this);
            layer.confirm('确定删除此书签？', {
                btn: ['确定','取消'],
                title: '删除不可恢复'
            }, function(){
                layer.load(1, {time: 15000, shade: [0.8, '#393D49']});
                var paramsData = {"m_id": self.attr("mark-id")};
                $.post("/user/mark/delete", appendCsrf(paramsData), function(data){
                    layer.closeAll();
                    updateCsrf(data.csrf);
                    if ("200" == data.code) {
                        layer.msg('删除成功');
                        self.parent().addClass("opacity-hide");
                        setTimeout(function(){self.parent().parent().remove();}, 1000);
                    } else {
                        layer.msg(data.msg);
                    }
                });
            }, function(){
            });
            return false;
        });
    });

    function update_tab(markLength)
    {
        if (markLength > 0) {
            $("#lmTabs li.lm-my-tab > a").trigger("click");
        } else {
            $("#lmTabs li.lm-system-tab > a").trigger("click");
        }
    }

    function init()
    {
        var paramsData = {};
        $.post("/user/marks", appendCsrf(paramsData), function(data){
            updateCsrf(data.csrf);
            if ("200" == data.code) {
                update_tab(data.marks.length);
                var marksHtml = "";
                for(var index in data.marks) {
                    marksHtml += getMarkHtml(data.marks[index]);
                }
                $("#myBookMarks").html(marksHtml);

                marksHtml = "";
                for(var index in data.system_marks) {
                    marksHtml += getSystemMarkHtml(data.system_marks[index]);
                }
                $("#systemBookMarks").html(marksHtml);

                setInterval(updateMarkInfo, 5000);
            }
        });

        // 监听窗口大小变化
        $(window).on("resize", function(){
            $(".mk-thumb").height($(".mk-thumb > img:eq(0)").height())
        });
    }

    function updateMarkInfo()
    {
        var paramsData = {'mark_ids': updateQueue};
        $.post("/user/marks", appendCsrf(paramsData), function(data){
            updateCsrf(data.csrf);
            if ("200" == data.code) {
                for(var index in data.marks) {
                    var mark = data.marks[index];
                    var markId = "#li" + mark['mark_uuid'];
                    $(markId + " > a").attr("title", mark['title'] || defaultTitle);
                    $(markId + " > a > .mk-favicon > img").attr("src", mark['icon'] || defaultIcon);
                    $(markId + " > a > .mk-title").text(mark['title'] || defaultTitle);
                    $(markId + " > a > .mk-thumb > img").attr("src", mark['screen_capture'] || defaultCapture);
                }
            }
        });
    }

    function getMarkHtml(mark)
    {
        mark['screen_capture'] == "" && updateQueue.push(mark['mark_uuid']);
        return '<li id="li' +  mark['mark_uuid'] + '">' +
                    '<a class="lm-mark" target="_blank" href="/user/open?url=' + mark['url'] + '" title="' + (mark['title'] || defaultTitle) + '">' +
                        '<div class="mk-favicon"><img src="' + (mark['icon'] || defaultIcon) + '"></div>' +
                        '<div class="mk-title">' + (mark['title'] || defaultTitle) + '</div>' +
                        '<div class="lm-icon-close mk-x" mark-id="' + mark['mark_uuid'] + '"></div>' +
                        '<div class="mk-thumb"><img src="' + (mark['screen_capture'] || defaultCapture) + '" /></div>' +
                    '</a>' +
                '</li>';
    }

    function getSystemMarkHtml(mark)
    {
        mark['screen_capture'] == "" && updateQueue.push(mark['uuid']);
        return '<li id="li' +  mark['mark_uuid'] + '">' +
                    '<a class="lm-mark" target="_blank" href="/user/open?url=' + mark['url'] + '" title="' + (mark['title'] || defaultTitle) + '">' +
                        '<div class="mk-favicon"><img src="' + (mark['icon'] || defaultIcon) + '"></div>' +
                        '<div class="mk-title">' + (mark['title'] || defaultTitle) + '</div>' +
                        '<div class="lm-icon-close mk-x" mark-id="' + mark['mark_uuid'] + '"></div>' +
                        '<div class="mk-thumb"><img src="' + (mark['screen_capture'] || defaultCapture) + '" /></div>' +
                    '</a>' +
                '</li>';
    }

    function appendCsrf(paramsData)
    {
        paramsData[$("#csrfName").val()] = $("#csrfHash").val();

        return paramsData;
    }

    function updateCsrf(csrf)
    {
        $("#csrfName").val(csrf['name']);
        $("#csrfHash").val(csrf['hash']);
    }
})();