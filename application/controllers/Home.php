<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (is_null($this->user_id))
        {
            $utmp_id = get_cookie('utmp_id', TRUE);
            if (is_null($utmp_id))
            {
                $utmp_id = $this->common_lib->uuid();
                $expire  = 3600 * 24 * 30;
                set_cookie('utmp_id', $utmp_id, $expire);
                set_cookie('utmp_id_expire', time() + $expire, $expire);
            }
            $this->session->set_userdata('user_id', $utmp_id);
        }

        $this->load->view('home_index', $this->data);
    }

}
