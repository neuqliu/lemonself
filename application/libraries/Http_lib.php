<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* http，get/post公用类
*/
class Http_lib {

    //get请求返回内容是JSON类型
    public function get_json($url)
    {
        return json_decode($this->get($url), true);
    }

    //post请求返回内容是JSON类型
    public function post_json($params, $url)
    {
        if (gettype($params) === 'string')
        {
            return json_decode($this->post_str($params, $url), true);
        }
        return json_decode($this->post($params, $url), true);
    }

    public function get($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $messages = curl_exec($curl);

        curl_close($curl);

        return $messages;
    }

    public function post($params, $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT,20);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $messages = curl_exec($curl);

        curl_close($curl);

        return $messages;
    }

    // 当参数是json string时调用
    public function post_str($params, $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,20);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($params))
        );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $messages = curl_exec($curl);

        curl_close($curl);

        return $messages;
    }

}

/* End of file Http_lib.php */
/* Location: ./application/libraries/Http_lib.php */