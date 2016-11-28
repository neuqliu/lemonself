<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* user表对应的model
*/
class User_model extends MY_Model {

    function __construct()
    {
        parent::__construct('user');
    }

    public function is_exist($user_id)
    {
        return parent::is_exist(array($this->common_lib->cur_dbuid($user_id) => $user_id));
    }

}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */