<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* update_queue表对应的model
*/
class Update_queue_model extends MY_Model {

    function __construct()
    {
        parent::__construct('update_queue');
    }

    public function insert($update_queue)
    {
        if (parent::is_exist(array('mark_uuid' => $update_queue['mark_uuid'])))
        {
            return parent::update_row(array('status' => 0), array('mark_uuid' => $update_queue['mark_uuid']));
        }
        else
        {
            return parent::insert($update_queue);
        }
    }

}

/* End of file Update_queue_model.php */
/* Location: ./application/models/Update_queue_model.php */