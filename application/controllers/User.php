<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        require_once constant("APPPATH")."libraries/Html_parse_lib.php";
    }

    public function login()
    {
       if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->json_result_init();

            $mobile = $this->input->post('mobile', true);
            if ($mobile)
            {
                $user_info = $this->user_model->get_by(array($this->db_uid => $this->user_id), 'mobile');
                if (is_null($user_info['mobile']) || empty($user_info['mobile']))
                {
                    $this->user_model->update_row(array('mobile' => $mobile), array($this->db_uid => $this->user_id)) &&
                    $this->user_mark_model->update_row(array('user_id' => $mobile), array('user_id' => $this->user_id)) &&
                    ($this->set_user_id($mobile)) && ($this->result['code'] = '200');
                }
                elseif ($user_info['mobile'] == $mobile)
                {
                    ($this->set_user_id($mobile)) && ($this->result['code'] = '200');
                }
                else
                {
                    $this->result['code'] = '4004';
                }
            }
            else
            {
                $this->result['code'] = '4003';
            }

            $this->json_result_echo();
        }
        else
        {
            show_error('拒绝访问！', 400, '瞎访问！');
        }
    }

    public function marks()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->json_result_init();

            if (!is_null($this->user_id))
            {
                $this->result['marks'] = $this->user_mark_model->get_all(array('user_id' => $this->user_id, 'is_delete' => 0),
                    'mark_uuid,url,icon,title,click_count,screen_capture', 'index ASC, updated_at DESC');
                $this->result['code'] = '200';
            }

            $this->json_result_echo();
        }
        else
        {
            show_error('拒绝访问！', 400, '瞎访问！');
        }
    }

    public function mark_add()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->json_result_init();

            if (!is_null($this->user_id))
            {
                $params = $this->input->post(array('url', 'cookie_uuid'), true);

                if (!$this->user_model->is_exist($this->user_id))
                {
                    $this->user_model->insert(array($this->db_uid => $this->user_id));
                }

                $mark_uuid   =  md5($params['url']);
                $create_mark = false;
                $book_mark   = $this->book_mark_model->get_by(array('uuid' => $mark_uuid), 'uuid,url,icon,title,screen_capture');
                if (count($book_mark) == 0)
                {
                    $html_parse = new Html_parse_lib($params['url']);
                    $book_mark  = array(
                        'uuid'           => $mark_uuid,
                        'url'            => $html_parse->url,
                        'icon'           => $html_parse->icon,
                        'screen_capture' => $html_parse->screen_capture,
                        'title'          => $html_parse->title
                    );
                    $create_mark = $this->book_mark_model->insert($book_mark);
                }

                $user_mark_db = $this->user_mark_model->get_by(array('mark_uuid' => $mark_uuid, 'user_id' => $this->user_id), 'user_id,mark_uuid,url,icon,screen_capture,title,is_delete');
                if ($create_mark && count($user_mark_db) == 0)
                {
                    $user_mark = array(
                        'user_id'        => $this->user_id,
                        'mark_uuid'      => $mark_uuid,
                        'url'            => $book_mark['url'],
                        'icon'           => $book_mark['icon'],
                        'screen_capture' => $book_mark['screen_capture'],
                        'title'          => $book_mark['title']
                    );
                    $this->user_mark_model->insert($user_mark) && ($this->result['code'] = '200') &&
                    ($this->result['mark'] = $user_mark) && $this->book_mark_model->feild_pp('mark_count', array('uuid' => $mark_uuid)) &&
                    $this->book_mark_model->feild_pp('history_mark', array('uuid' => $mark_uuid));
                }
                else
                {
                    if ($user_mark_db['is_delete'] == 1)
                    {
                        $this->user_mark_model->update_row(array('is_delete' => 0), array('mark_uuid' => $mark_uuid, 'user_id' => $this->user_id)) &&
                        ($this->result['code'] = '200') && ($this->result['mark'] = $user_mark_db) &&
                        $this->user_model->feild_pp('mark_count', array($this->db_uid => $this->user_id)) &&
                        $this->book_mark_model->feild_pp('mark_count', array('uuid' => $mark_uuid)) &&
                        $this->book_mark_model->feild_pp('history_mark', array('uuid' => $mark_uuid));
                    }
                    else
                    {
                        $this->result['code'] = '4005';
                    }
                }
            }

            $this->json_result_echo();
        }
        else
        {
            show_error('拒绝访问！', 400, '瞎访问！');
        }
    }

    public function mark_delete()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->json_result_init();

            if (!is_null($this->user_id))
            {
                $mark_uuid = $this->input->post('m_id', true);
                if ($this->user_mark_model->update_row(array('is_delete' => 1), array('user_id' => $this->user_id, 'mark_uuid' => $mark_uuid)))
                {
                    $this->user_model->feild_change('mark_count', array($this->db_uid => $this->user_id), '-1') && ($this->result['code'] = '200') &&
                    $this->book_mark_model->feild_change('mark_count', array('uuid' => $mark_uuid), '-1');
                }
            }

            $this->json_result_echo();
        }
        else
        {
            show_error('拒绝访问！', 400, '瞎访问！');
        }
    }

    public function open()
    {
        $url = $this->input->get('url', true);
        if (!is_null($url))
        {
            $mark_uuid = md5($url);
            $this->book_mark_model->feild_pp('click_count', array('uuid' => $mark_uuid));
            $this->user_mark_model->feild_pp('click_count', array('user_id' => $this->user_id, 'mark_uuid' => $mark_uuid));

            redirect($url);
        }

        redirect("/");
    }

}
