<!doctype html>
<html class="no-js">
<head>
    <?php $html_title = '略有不同'; ?>
    <?php include 'common_head.php'; ?>
</head>
<body>
    <?php include 'header.php'; ?>

    <div data-am-widget="tabs" class="am-tabs am-tabs-d2">
        <ul class="am-tabs-nav am-cf" id="lmTabs">
            <li class="am-active lm-system-tab"><a href="[data-tab-panel-0]">系统推荐</a></li>
            <li class="lm-my-tab"><a href="[data-tab-panel-1]">我的收藏</a></li>
        </ul>
        <div class="am-tabs-bd">
            <div data-tab-panel-0 class="am-tab-panel lm-marks-tab am-active">
                <section>
                    <div class="lm-content lm-marks">
                        <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-4" id="systemBookMarks">
                            <li>
                                <a class="lm-mark" href="#" title="测试">
                                    <div class="mk-favicon"><img src="/tmp/icons/lemonself.ico"></div>
                                    <div class="mk-title">测试测试，测试测试测试测试，测试测试测试测试，测试测试测试测试，测试测试</div>
                                    <div class="mk-edit">
                                        <div class="mk-classification" mark-id="" selected-classification="工具"></div>
                                        <div class="lm-icon-close mk-x" mark-id=""></div>
                                    </div>
                                    <div class="mk-thumb"><img src="/tmp/icons/lemonself.png" /></div>
                                </a>
                            </li>
                            <li>
                                <a class="lm-mark" href="#" title="测试">
                                    <div class="mk-favicon"><img src="/tmp/icons/lemonself.ico"></div>
                                    <div class="mk-title">测试测试，测试测试测试测试，测试测试测试测试，测试测试测试测试，测试测试</div>
                                    <div class="mk-edit" mark-id="">
                                        <div class="mk-classification" selected-classification="前端"></div>
                                        <div class="lm-icon-close mk-x"></div>
                                    </div>
                                    <div class="mk-thumb"><img src="/tmp/icons/lemonself.png" /></div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
            <div data-tab-panel-1 class="am-tab-panel">
                <section>
                    <div class="lm-content lm-marks">
                        <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-4" id="myBookMarks"></ul>
                    </div>
                    <div class="chosen-init">
                        <select class="chosen-select-width classification-chosen" data-placeholder="选择分组" id="classificationChosen"></select>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <?php include 'common_js.php'; ?>
</body>
</html>