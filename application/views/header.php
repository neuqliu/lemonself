<?php if (!$this->common_lib->is_openid()): ?>
    <div class="am-text-center am-alert am-alert-warning lm-fixed-top" data-am-alert>
        <button type="button" class="am-close">&times;</button>
        <p>登录后可以永久保存数据，否则数据不能在多台设备共享，并且极易丢失！</p>
    </div>
<?php endif; ?>
<header>
    <div class="am-vertical-align lm-content lm-header">
        <a class="am-vertical-align-middle lm-logo-header" href="/">
            <img src="/assets/img/logo.png" alt="柠檬本色">
            <div class="lm-logo-text">柠檬本色</div>
        </a>
        <nav class="lm-header-nav">
            <ul>
                <!-- <li>
                    <a class="am-icon-search lm-button-search" href="javascript:;"></a>
                </li> -->
                <li>
                    <?php if ($this->common_lib->is_openid()): ?>
                        <span class="lm-qq-login"><img src="<?= $user_info['figureurl_qq_1'] ?>" title="<?= $user_info['nickname'] ?>" alt="<?= $user_info['nickname'] ?>"></span>
                    <?php else: ?>
                        <span id="qqLoginBtn" class="lm-qq-login"></span>
                    <?php endif; ?>
                </li>
                <li>
                    <a title="添加书签" class="lm-button lm-icon-plus" href="javascript:;" data-am-modal="{target: '#addBookMarksInput', closeViaDimmer: 0}">添加书签</a>
                </li>
            </ul>
        </nav>
    </div>
    <input type="hidden" id="csrfName" value="<?= isset($csrf) ? $csrf['name'] : ''; ?>">
    <input type="hidden" id="csrfHash" value="<?= isset($csrf) ? $csrf['hash'] : ''; ?>">
    <div class="am-modal am-modal-prompt" tabindex="-1" id="addBookMarksInput">
        <div class="am-modal-dialog am-form">
            <div class="am-modal-hd">添加新书签</div>
            <div class="am-modal-bd">
                粘贴完整URL(以http://或https://开头)
                <input type="text" class="am-modal-prompt-input" placeholder="书签地址" name="newMarkUrl" value="">
                <select class="am-modal-prompt-select" name="markClassification">
                    <?php foreach ($GLOBALS['mark_classification'] as $classification): ?>
                        <option value="<?= $classification ?>"><?= $classification ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="am-modal-footer">
                <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                <span class="am-modal-btn" id="sbumitBookMarks">添加</span>
            </div>
        </div>
    </div>
    <div class="am-modal am-modal-prompt" tabindex="-1" id="loginBtnModal">
        <div class="am-modal-dialog">
            <div class="am-modal-hd">账号登录</div>
            <div class="am-modal-bd">
                使用手机号，可以永久保存书签数据
                <input type="text" class="am-modal-prompt-input" placeholder="手机号" name="mobile">
            </div>
            <div class="am-modal-footer">
                <span class="am-modal-btn" data-am-modal-cancel>取消</span>
                <span class="am-modal-btn" id="userBind">登录</span>
            </div>
        </div>
    </div>
</header>
