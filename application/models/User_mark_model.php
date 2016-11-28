<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* user_mark表对应的model
*/
class User_mark_model extends MY_Model {

    function __construct()
    {
        parent::__construct('user_mark');
    }

    public function insert($user_mark)
    {
        if (parent::insert($user_mark))
        {
            return $this->user_model->feild_pp('mark_count', array($this->common_lib->cur_dbuid($user_mark['user_id']) => $user_mark['user_id']));
        }

        return false;
    }

}

/* End of file User_mark_model.php */
/* Location: ./application/models/User_mark_model.php */