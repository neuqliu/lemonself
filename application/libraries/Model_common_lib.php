<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* model公有类
*/
class Model_common_lib {

    private $table_name;

    function __construct($name)
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
        $this->table_name = $name;
    }

    public function insert($row)
    {
        $this->db->trans_start();

        $this->db->insert($this->table_name, $row);
        $affected_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() && $affected_rows > 0)
        {
            return true;
        }

        return false;
    }

    public function get($where, $select = '*')
    {
        $this->db->select($select);
        $query = $this->db->get_where($this->table_name, $where);
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }

        return false;
    }

    public function get_all($where, $select = '*', $order = '', $limit_start = '', $limit_counts = '')
    {
        $this->db->select($select);
        $order !== '' && $this->db->order_by($order);
        $limit_start !== '' && $limit_counts !== '' && $this->db->limit($limit_counts, $limit_start);
        $query = $this->db->get_where($this->table_name, $where);
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }

        return false;
    }

    public function get_all_in($feild, $where_in, $select, $order = '', $where = null)
    {
        $this->db->select($select);
        $this->db->where_in($feild, $where_in);
        !is_null($where) && $this->db->where($where);
        $order !== '' && $this->db->order_by($order);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }

        return false;
    }

    public function update($data, $where)
    {
        return $this->update_base('update', $data, $where);
    }

    public function delete($where)
    {
        $this->db->trans_start();

        $this->db->delete($this->table_name, $where);
        $affected_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() && $affected_rows > 0)
        {
            return true;
        }

        return false;
    }

    public function update_batch($data, $where)
    {
        return $this->update_base('update_batch', $data, $where);
    }

    public function feild_change($feild, $where, $increment)
    {
        $this->db->trans_start();

        $this->db->set($feild, $feild.$increment, FALSE)
                 ->where($where)
                 ->update($this->table_name);

        $affected_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() && $affected_rows > 0)
        {
            return true;
        }

        return false;
    }

    // 此函数data中的，必须要有主键或唯一索引的字段
    public function replace($data)
    {
        $this->db->trans_start();

        $this->db->replace($this->table_name, $data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    private function update_base($func_name, $data, $where)
    {
        $this->db->trans_start();

        $this->db->$func_name($this->table_name, $data, $where);
        $affected_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() && $affected_rows > 0)
        {
            return true;
        }

        return false;
    }

}

/* End of file Model_common_lib.php */
/* Location: ./application/libraries/Model_common_lib.php */