<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 解析网页内容
*/
class Html_parse_lib {

    public $url            = '/';
    public $icon           = '';
    public $screen_capture = '';
    public $title          = '';

    function __construct($url)
    {
        include_once constant("APPPATH").'third_party/simple_html_dom.php';
        $this->CI  =& get_instance();

        $this->url = $url;

        $this->init();
    }

    public function parse_base_info()
    {
        set_time_limit(120);

        if ($this->url !== '/')
        {
            $this->init();

            $capture_name = md5($this->url).'_capture.png';
            $cmd_str      = 'wkhtmltoimage --width 1024 --height 600 --quality 1 '.$this->url.' '.constant("APPPATH").'../tmp/icons/'.$capture_name;
            @system($cmd_str);
            $capture_img = '/tmp/icons/'.$capture_name;
            file_exists(constant("APPPATH").'..'.$capture_img) && $this->screen_capture = $capture_img;
        }
    }

    private function init()
    {
        if ($this->url !== '/')
        {
            $img_path = constant("APPPATH").'../tmp/icons/';
            $html = file_get_html($this->url);
            ($title = $html->find('title', -1)) && !is_null($title) && ($this->title = $title->innertext);

            $icon_url = $html->find('link[rel="shortcut icon"]', -1);
            is_null($icon_url) && $icon_url = $html->find('link[rel="icon"]', -1);
            is_null($icon_url) && $icon_url = $html->find('link[rel="alternate icon"]', -1);
            $url_info    = parse_url($this->url);
            $icon_domain = $url_info['scheme'].'://'.$url_info['host'].'/';
            if (!is_null($icon_url))
            {
                $icon_url = $icon_url->href;
                !preg_match('/(((http|https):\/\/)|(\/\/))([a-zA-Z0-9_-]+\.)*/', $icon_url) && $icon_url = $icon_domain.$icon_url;
                mb_strpos($icon_url, '//') === 0 && $icon_url = 'http:'.$icon_url;
            }
            else
            {
                $icon_url = $icon_domain.'favicon.ico';
            }

            $icon_img       = $this->CI->http_lib->get($icon_url);
            $icon_full_name = md5($icon_url).'_favicon.ico';

            @file_put_contents($img_path.$icon_full_name, $icon_img) > 0 &&
            (mb_strpos(image_type_to_mime_type(exif_imagetype($img_path.$icon_full_name)), 'image') !== false) &&
            ($this->icon = '/tmp/icons/'.$icon_full_name);
        }
    }

}

/* End of file Html_parse_lib.php */
/* Location: ./application/libraries/Html_parse_lib.php */