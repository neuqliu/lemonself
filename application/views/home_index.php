<!doctype html>
<html class="no-js">
<head>
    <?php $html_title = '每个人都与众不同'; ?>
    <?php include 'common_head.php'; ?>
</head>
<body>
    <?php include 'header.php'; ?>

    <div data-am-widget="tabs" class="am-tabs am-tabs-d2">
        <ul class="am-tabs-nav am-cf">
            <li class="am-active"><a href="[data-tab-panel-0]">系统推荐</a></li>
            <li class=""><a href="[data-tab-panel-1]">我的收藏</a></li>
        </ul>
        <div class="am-tabs-bd">
            <div data-tab-panel-0 class="am-tab-panel lm-marks-tab am-active">
                <section>
                    <div class="lm-content lm-marks">
                        <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-4" id="systemBookMarks">
                            <li>
                                <a class="lm-mark" target="_blank" href="/user/open?url=http://izsw.net" title="测试">
                                    <div class="mk-favicon"><img src="/tmp/icons/lemonself.ico"></div>
                                    <div class="mk-title">测试测试，测试测试</div>
                                    <div class="lm-icon-close mk-x" mark-id=""></div>
                                    <div class="mk-thumb"><img src="/tmp/icons/lemonself.png" /></div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
            <div data-tab-panel-1 class="am-tab-panel ">
                <section>
                    <div class="lm-content lm-marks">
                        <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-4" id="myBookMarks"></ul>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <?php include 'common_js.php'; ?>
</body>
</html>