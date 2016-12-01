<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 定时任务类
*/
class Task extends MY_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function update_mark_info()
    {
        require_once constant("APPPATH")."libraries/Html_parse_lib.php";
        set_time_limit(0);

        $update_queue = $this->update_queue_model->get_all(array('status' => 0), 'id,mark_uuid,mark_url,run_times,status', 'created_at', 0, 200);
        foreach ($update_queue as &$queue) {
            $queue['status'] = 1;
            $this->update_queue_model->update_row($queue, array('id' => $queue['id']));
        }

        $res = array_pad(array(), count($update_queue), 0);
        foreach ($update_queue as $index => $queue) {
            $html_parse = new Html_parse_lib($queue['mark_url']);
            $html_parse->parse_base_info();

            if (!empty($html_parse->screen_capture))
            {
                $mark_info = array(
                    'url'            => $html_parse->url,
                    'icon'           => $html_parse->icon,
                    'screen_capture' => $html_parse->screen_capture,
                    'title'          => $html_parse->title
                );
                $this->book_mark_model->update_row($mark_info, array('uuid' => $queue['mark_uuid']), true) &&
                $this->user_mark_model->update_row($mark_info, array('mark_uuid' => $queue['mark_uuid']), true) &&
                $this->update_queue_model->delete_row(array('id' => $queue['id'])) &&
                $res[$index] = 1;
            }
            else
            {
                $queue['status'] = 0;
                $queue['run_times'] += 1;
                if ($queue['run_times'] > 2)
                {
                    $this->update_queue_model->delete_row(array('id' => $queue['id']));
                }
                else
                {
                    $this->update_queue_model->update_row($queue, array('id' => $queue['id']));
                }
            }
        }

        print_r($res);
    }

}
