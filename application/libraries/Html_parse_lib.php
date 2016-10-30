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
        $this->CI  =& get_instance();

        $this->url = $url;

        $this->parse_base_info();
    }

    private function parse_base_info()
    {
        set_time_limit(60);
        $html_content = $this->CI->http_lib->get($this->url);

        if (!empty($html_content))
        {
            $titles = null;
            $img_path = constant("APPPATH").'../tmp/icons/';
            preg_match("/<title>(.*)<\/title>/i", $html_content, $titles);
            !is_null($titles) && !empty($titles) && ($this->title = $titles[1]);

            $url_info       = parse_url($this->url);
            $icon_path      = $url_info['scheme'].'://'.$url_info['host'];
            $icon_url       = $icon_path.'/favicon.ico';
            $icon_img       = $this->CI->http_lib->get($icon_url);
            $icon_name      = preg_replace('/:|\//', '_', $icon_path);
            $icon_full_name = $icon_name.'.ico';

            @file_put_contents($img_path.$icon_full_name, $icon_img) > 0 &&
            (image_type_to_mime_type(exif_imagetype($img_path.$icon_full_name)) == 'image/vnd.microsoft.icon') &&
            ($this->icon = '/tmp/icons/'.$icon_full_name);
            empty($this->icon) && ($icon_img = $this->CI->http_lib->get('http://g.soz.im/'.$icon_path)) &&
            @file_put_contents($img_path.$icon_full_name, $icon_img) > 0 &&
            ($this->icon = '/tmp/icons/'.$icon_full_name);
            $cmd_str = 'wkhtmltoimage --width 1024 --height 600 --quality 1 '.$this->url.' '.$img_path.$icon_name.'.png';
            @system($cmd_str, $result);
            $capture_img = '/tmp/icons/'.$icon_name.'.png';
            file_exists(constant("APPPATH").'..'.$capture_img) && $this->screen_capture = $capture_img;
        }
    }

}

/* End of file Html_parse_lib.php */
/* Location: ./application/libraries/Html_parse_lib.php */