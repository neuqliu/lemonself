<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* model基础类
*/
class MY_Model extends CI_Model {

    private $model_common;

    function __construct($table_name)
    {
        parent::__construct();
        require_once constant("APPPATH")."libraries/Model_common_lib.php";
        $this->model_common = new Model_common_lib($table_name);
    }

    public function insert($table)
    {
        $table['created_at'] = mdate("%Y-%m-%d %H:%i:%s", time());

        return $this->model_common->insert($table);
    }

    public function is_exist($where)
    {
        $table = $this->model_common->get($where, 'id');

        return $table !== false;
    }

    public function get_by($where, $select)
    {
        $table_data = $this->model_common->get($where, $select);
        false == $table_data && $table_data = array();

        return $table_data;
    }

    public function get_all($where, $select = '*', $order = '', $limit_start = '', $limit_counts = '')
    {
        $table_data = $this->model_common->get_all($where, $select, $order, $limit_start, $limit_counts);
        false == $table_data && $table_data = array();

        return $table_data;
    }

    public function get_all_in($feild, $where_in, $select, $order = '', $where = null)
    {
        $table_data = $this->model_common->get_all_in($feild, $where_in, $select, $order, $where);
        false == $table_data && $table_data = array();

        return $table_data;
    }

    public function update_row($table, $where)
    {
        return $this->model_common->update($table, $where);
    }

    public function update_all($tables, $where)
    {
        return $this->model_common->update_batch($tables, $where);
    }

    public function delete_row($where)
    {
        return $this->model_common->delete($where);
    }

    public function query($query_string)
    {
        return $this->db->query($query_string)->result_array();
    }

    public function feild_pp($feild, $where)
    {
        return $this->feild_change($feild, $where, '+1');
    }

    public function feild_change($feild, $where, $increment)
    {
        return $this->model_common->feild_change($feild, $where, $increment);
    }
}

/* End of file MY_Model.php */
/* Location: ./application/controllers/MY_Model.php */