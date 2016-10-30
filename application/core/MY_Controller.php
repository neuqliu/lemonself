<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 控制器基础类
*/
class MY_Controller extends CI_Controller {

    public $crsf_name = '';
    public $crsf_hash = '';
    public $user_id   = NULL;
    public $db_uid    = 'cookie_uuid';
    public $result    = array('code' => '400');
    public $data      = array();

    function __construct()
    {
        parent::__construct();
        $this->user_id   = $this->session->userdata('user_id');
        $this->crsf_name = $this->security->get_csrf_token_name();
        $this->crsf_hash = $this->security->get_csrf_hash();

        $this->result['csrf'] = array('name' => $this->crsf_name, 'hash' => $this->crsf_hash);

        $this->data['csrf']   = array('name' => $this->crsf_name, 'hash' => $this->crsf_hash);

        strlen($this->user_id) === 11 ? ($this->db_uid = 'mobile') : ($this->db_uid = 'cookie_uuid');
    }

    public function set_user_id($val)
    {
        $this->session->set_userdata('user_id', $val);
        $this->user_id = $val;
        strlen($val) === 11 ? ($this->db_uid = 'mobile') : ($this->db_uid = 'cookie_uuid');
        return  true;
    }

    public function json_result_init()
    {
        header('Content-Type: application/json');
    }

    public function json_result_echo()
    {
        $this->result['msg'] = $GLOBALS['result_codes'][$this->result['code']];

        echo json_encode($this->result);
    }

}

/* End of file MY_Controller.php */
/* Location: ./application/controllers/MY_Controller.php */