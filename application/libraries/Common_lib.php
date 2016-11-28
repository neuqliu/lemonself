<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 共用模块
*/
class Common_lib {

    function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * 生成UUID
     * 返回字符串
     */
    public function uuid($length = 32)
    {
        mt_srand((double)microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $uuid   = substr($charid, 0, $length);

        return $uuid;
    }

    /**
     * 生成utmp_id
     * 返回字符串
     */
    public function utmp_id()
    {
        return 'LMTU'.$this->uuid(28);
    }

    /**
     * 验证所有参数都不能为空
     * 返回bool
     */
    public function verify_all_params($params)
    {
        foreach ($params as $key => $value) {
            if (is_null($value) || $value == '')
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 验证uid是否是openid
     * 返回bool
     */
    public function is_openid($uid = null)
    {
        $userid = is_null($uid) ? $this->CI->session->userdata('user_id') : $uid;

        return !(mb_strpos($userid, 'LMTU') === 0);
    }

    /**
     * 获取当前userid的类型
     * 返回string
     */
    public function cur_dbuid($uid = null)
    {
        $userid = is_null($uid) ? $this->CI->session->userdata('user_id') : $uid;

        return $this->is_openid($userid) ? 'openid' : 'cookie_uuid';
    }

    // 格式化
    public function money($money)
    {
        return number_format(floatval($money) / 100, 2);
    }

    // 支付时间
    public function pay_time($time)
    {
        return date("Y-m-d H:i:s", strtotime($time));
    }

}

/* End of file Common_lib.php */
/* Location: ./application/libraries/Common_lib.php */