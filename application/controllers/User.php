<?php defined('BASEPATH') OR exit('No direct script access allowed');

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

            $info   = $this->input->post(array('openid', 'nickname', 'gender', 'figureurl_qq_1', 'figureurl_qq_2'), true);
            $openid = $info['openid'];
            if ($openid)
            {
                $user_info = $this->user_model->get_by(array('openid' => $openid), 'openid,cookie_uuid');
                count($user_info) == 0 && $user_info = $this->user_model->get_by(array($this->db_uid => $this->user_id), 'openid,cookie_uuid');
                if (count($user_info) == 0)
                {
                    $this->user_model->insert($info) && ($this->update_userinfo($info)) && ($this->result['code'] = '200');
                }
                elseif (is_null($user_info['openid']) || empty($user_info['openid']))
                {
                    $this->user_model->update_row($info, array($this->db_uid => $this->user_id)) &&
                    $this->user_mark_model->update_row(array('user_id' => $openid), array('user_id' => $this->user_id)) &&
                    ($this->update_userinfo($info)) && ($this->result['code'] = '200');
                }
                elseif ($user_info['openid'] == $openid)
                {
                    ($this->update_userinfo($info)) && ($this->result['code'] = '200') &&
                    $this->user_model->update_row($info, array('openid' => $openid));
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

            $mark_ids = $this->input->post('mark_ids', true);
            $fields   = 'mark_uuid,url,icon,title,click_count,screen_capture,classification';
            $order_by = 'index ASC, updated_at DESC';
            if (is_null($mark_ids))
            {
                $this->result['system_marks'] = $this->book_mark_model->get_all(array('is_recommend' => 1), 'uuid,url,icon,title,click_count,screen_capture', 'updated_at DESC');
                $this->result['classifications']['system'] = $GLOBALS['mark_classification'];
                $this->result['code'] = '200';
                if (!is_null($this->user_id))
                {
                    $this->result['marks'] = $this->user_mark_model->get_all(array('user_id' => $this->user_id, 'is_delete' => 0), $fields, $order_by);
                    $my_classification     = $this->user_mark_model->get_all(array('user_id' => $this->user_id), 'classification', 'updated_at DESC');
                    $this->result['classifications']['my'] = array_diff(array_column($my_classification, 'classification'), $this->result['classifications']['system']);
                }
            }
            else
            {
                $this->result['marks'] = $this->user_mark_model->get_all_in('mark_uuid', $mark_ids, $fields, $order_by);
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
                $params = $this->input->post(array('url', 'classification', 'cookie_uuid'), true);

                if (!$this->user_model->is_exist($this->user_id))
                {
                    $this->user_model->insert(array($this->db_uid => $this->user_id));
                }

                $mark_uuid   =  md5($params['url']);
                $create_mark = false;
                $book_mark   = $this->book_mark_model->get_by(array('uuid' => $mark_uuid), 'uuid,url,icon,title,screen_capture');
                $this->update_queue_model->insert(array('mark_uuid' => $mark_uuid, 'mark_url'  => $params['url']));
                if (count($book_mark) == 0)
                {
                    $html_parse = new Html_parse_lib($params['url']);
                    $book_mark  = array(
                        'uuid'           => $mark_uuid,
                        'url'            => $html_parse->url,
                        'icon'           => $html_parse->icon,
                        'title'          => $html_parse->title,
                        'screen_capture' => $html_parse->screen_capture,
                        'classification' => $params['classification']
                    );
                    $create_mark = $this->book_mark_model->insert($book_mark);
                }

                $user_mark_db = $this->user_mark_model->get_by(array('mark_uuid' => $mark_uuid, 'user_id' => $this->user_id), 'user_id,mark_uuid,url,icon,screen_capture,title,is_delete');
                if (($create_mark || count($book_mark) > 0) && count($user_mark_db) == 0)
                {
                    $user_mark = array(
                        'user_id'        => $this->user_id,
                        'mark_uuid'      => $mark_uuid,
                        'url'            => $book_mark['url'],
                        'icon'           => $book_mark['icon'],
                        'screen_capture' => $book_mark['screen_capture'],
                        'title'          => $book_mark['title'],
                        'classification' => $params['classification']
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

    public function mark_update()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->json_result_init();

            if (!is_null($this->user_id))
            {
                $mark_uuid      = $this->input->post('m_id', true);
                $classification = $this->input->post('classification', true);
                !empty($classification) &&
                $this->user_mark_model->update_row(array('classification' => $classification), array('user_id' => $this->user_id, 'mark_uuid' => $mark_uuid)) &&
                $this->result['code'] = '200';
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
