<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (is_null($this->user_id))
        {
            $utmp_id = get_cookie('ut_id', TRUE);
            if (is_null($utmp_id))
            {
                $utmp_id = $this->common_lib->utmp_id();
                $expire  = 3600 * 24 * 30;
                set_cookie('ut_id', $utmp_id, $expire);
                set_cookie('ut_id_expire', time() + $expire, $expire);
            }
            $this->session->set_userdata('user_id', $utmp_id);
        }

        $this->load->view('home_index', $this->data);
    }

    public function test()
    {
        require_once "LMAlgorithm.php";

        $test_data = [6, 5, 3, 1, 8, 7, 2, 4];

        $sort_data = LMAlgorithm\Sort::shell($test_data);
        print_r($sort_data);
        // set_time_limit(90);
        // // $html_content = $this->http_lib->get('http://blog.csdn.net/isea533/article/details/44002219');

        // $html_content = file_get_contents(constant("APPPATH").'../tmp/icons/1.html');
        // // @file_put_contents(constant("APPPATH").'../tmp/icons/1.html', $html_content);

        // if (!empty($html_content))
        // {
        //     $titles = null;
        //     preg_match("/<title>(.+)<\/title>/is", $html_content, $titles);
        //     var_dump($titles);
        //     !is_null($titles) && !empty($titles) && ($this->title = $titles[1]);
        // }
    }

}
