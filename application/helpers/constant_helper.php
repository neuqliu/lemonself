<?php defined('BASEPATH') OR exit('No direct script access allowed');

// 返回code汇总
$GLOBALS['result_codes'] = array(
    '200'  => '请求成功',
    '400'  => '请求失败',
    '4001' => '权限不足',
    '4002' => '保存失败',
    '4003' => '参数非法',
    '4004' => '手机号不匹配',
    '4005' => '书签已存在'
);

// 默认书签分类
$GLOBALS['mark_classification'] = array('默认', '工具', '前端', '桌面', '移动', '后端', '视频', '游戏', '体育', '军事', '购物', '音乐', '动漫', '彩票', '其他');
