<?php

/**
 * PHP Pharaoh 'PHPharo'
 * PHPharo is a full featured oop non-mvc modular framework that
 * helps you create any type of app(s)
 * it`s very fast light
 * @license GPL-V3
 * @author PHPharo | Mohammed Abdullah Al-Ashaal
 * @link <https://twitter.com/phpharo> <PHPharo@Gmail.Com>
 * @copyright 2013
 */

/* ------------------------------------------------------------- */

if(!function_exists('set_content_type')):

    /**
     * set_content_type()
     * set http_content type
     * @param string $mim
     * @param string $charset
     * @return void
     */
    function set_content_type($mim = 'text/html', $charset = 'UTF-8')
    {
        header('Content-Type: '.$mim.';charset='.$charset);
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('htttp_redirect')):

    /**
     * http_redirect()
     * Redirect or Refresh Using Http
     * @param mixed $location
     * @param integer $delay
     * @param integer $type
     * @return void
     */
    function http_redirect($location, $delay = 0, $type = 301)
    {
        header('Refresh: ' . $delay .';url=' . $location, true, $type);
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('html_redirect')):

    /**
     * html_redirect()
     * Redirect or Refresh Using Html
     * @param mixed $location
     * @param integer $delay
     * @return void
     */
    function html_redirect($location, $delay = 0)
    {
        echo '<meta http-equiv="refresh" content="'.$delay.';URL='.$location.'" />';
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('js_redirect')):

    /**
     * js_redirect()
     * Redirect or Refresh Using Javascript
     * @param mixed $location
     * @param integer $delay
     * @return void
     */
    function js_redirect($location, $delay = 0)
    {
        echo '<script>setTimeout(function(){window.location.href="'.$location.'";}, '.($delay * 1000).');</script>';
    }

endif;

/* --------------------------------------------------------------- */

if(!function_exists('http_xss_filter')):

    /**
     * http_xss_filter()
     * force browser to filter xss ?
     * @param bool $state
     * @return void
     */
    function http_xss_filter($state = true)
    {
        if((bool) $state) header('X-XSS-Protection: 1; mode=block');
        else header('X-XSS-Protection: 0');
    }

endif;

/* --------------------------------------------------------------- */

if(!function_exists('http_block_ua')):

    /**
     * http_block_ua()
     * block certain useragent
     * @param mixed $ua
     * @return void
     */
    function http_block_ua($ua)
    {
        if(preg_match('~'.$ua.'~i', $_SERVER['HTTP_USER_AGENT']))
        {
            header('HTTP/1.1 403 Forbidden');
            die('<h1>404 Forbidden</h1><p>Your User Agent Is Blocked</p>');
        }   
    }

endif;

/* --------------------------------------------------------------- */

if(!function_exists('http_auth')):

    /**
     * http_auth()
     * Require User Authorization
     * @param mixed $required_username
     * @param mixed $required_password
     * @param string $message
     * @return true if every thing is ok
     */
    function http_auth($required_username, $required_password, $message = 'Please Auth-Your Self')
    {
        // Check if is Authorized
        if(!isset($_SERVER['PHP_AUTH_USER'])):
            header('WWW-Authenticate: Basic Realm="'.$message.'"'); 
            header('HTTP/1.1 401 Unauthorized'); 
            die('<h1>'.$message.'</h1>');
        endif;
        // Check if it valid authorization
        if($_SERVER['PHP_AUTH_USER'] !== $required_username || $_SERVER['PHP_AUTH_PW'] !== $required_password):
            header('WWW-Authenticate: Basic Realm="Wrong Authorization Data"'); 
            header('HTTP/1.1 401 Unauthorized'); 
            die('<h1> Wrong Authorization Data </h1>');
        endif;
        // every thing ok, then return true
        return true;
    }

endif;

/* ------------------------------------------------------------------ */

if(!function_exists('http_block_ips')):

    /**
     * block_ips()
     * block array of ips
     * @param mixed $ips
     * @return void
     */
    function http_block_ips(array $ips)
    {
        if(in_array($_SERVER['REMOTE_ADDR'], $ips))
        {
            header('HTTP/1.1 403 Forbidden');
            die('<h1>404 Forbidden</h1><p>Your User Agent Is Blocked</p>');
        }
    }

endif;

/* ------------------------------------------------------------------ */

if(!function_exists('http_no_flood')):

    /**
     * http_no_flood()
     * http anti-flood gateaway
     * @param mixed $tmpdir
     * @param integer $block_time
     * @param integer $limit_intval
     * @param integer $alerts_number
     * @return void
     */
    function http_no_flood($tmpdir, $block_time = 3600, $limit_intval = 5, $alerts_number = 3)
    {
        // tmp dir exists and writable
        if(!file_exists($tmpdir)) die('<h3>http_no_flood: temp dir not exists</h3>');
        elseif(!is_writable($tmpdir)) die('<h3>http_no_flood: temp dir must be writable</h3>');
        // custome css for alets
        $css = 'padding:10px;margin:auto;background:#333;color:#ddd;font-weight:bolder;box-shadow:0 0 10px #444';
        // load dbr if not loaded
        PHPharo::LoadLibrary('dbr');
        // prepare data
        $tmpfile = $tmpdir . '/http_no_flood.tmp';
        if(!file_exists($tmpfile)) file_put_contents($tmpfile, '');
        dbr::config($tmpfile);
        $ip = $_SERVER['REMOTE_ADDR'];
        $last_time = time();
        $alert_count = 0;
        $blocked = false;
        if(!dbr::get($ip)) dbr::set($ip, array($last_time, $alert_count, $blocked));
        else {
            $ip = dbr::get($ip);
            // blocked ?
            if($ip[2]) {
                // yes blocked, lets check if block time ended
                if(time() > ($ip[0] + $block_time)) {
                    // yes ended lets refresh this ip
                    dbr::set($_SERVER['REMOTE_ADDR'], array(time(), 0, false));
                } else {
                    // already blocked & time not ended
                    die('<div style="'.$css.'">
                            PHPharo:ANTI-FLOOD :
                            <br /> This is Alert Number ('.$ip[1].') 
                            <br /> You Are Blocked For ('.$block_time.') second(s)
                            <br /> Timer (Left) : ('.(($ip[0] + $block_time) - time()).')
                            <br /> Do\'nt Do It Again !!!
                            <br /> Requests Limit is One Per ('.$limit_intval.') second(s)
                         </div>');
                }
            }
            // alerts
            if((time() - $ip[0]) < (int)$limit_intval) {
                // if alerts < alerts_number
                if($ip[1] <= (int)$alerts_number) {
                    dbr::set($_SERVER['REMOTE_ADDR'], array(time(), ++$ip[1], false));
                    die('<div style="'.$css.'">
                            PHPharo:ANTI-FLOOD :
                            <br /> This is Alert Number ('.$ip[1].') 
                            <br /> Requests Limit is One Per ('.$limit_intval.') second(s)
                            <br /> You Will See This Alert Only ('.$alerts_number.') Times
                            <br /> After that if you repeat it again you will be blocked for ('.$block_time.') second(s)
                        </div>');
                } // if alerts count are bigger than alerts_number
                elseif($ip[1] > (int)$alerts_number) {
                    dbr::set($_SERVER['REMOTE_ADDR'], array(time(), ++$ip[1], true));
                    die('<div style="'.$css.'">
                            PHPharo:ANTI-FLOOD :
                            <br /> This is Alert Number ('.$ip[1].') 
                            <br /> You Are Blocked For ('.$block_time.') second(s)
                            <br /> Do\'nt Do It Again !!!
                            <br /> Requests Limit is One Per ('.$limit_intval.') second(s)
                         </div>');
                }
            } else {
                dbr::set($_SERVER['REMOTE_ADDR'], array(time(), 0, false));
            }
        }
    }

endif;

/* ------------------{From V1.0.0 RC3}------------------------- */

if(!function_exists('http_requests_no_quotes')):

    /**
     * http_requests_no_quotes()
     * filter and reomves quotes from requests
     * @param string $request
     * @return bool
     */
    function http_requests_no_quotes($request = '*')
    {
        // this works only from PHP >= 5.3 and PHPharo Also From 5.3
        $callback = function($str){return preg_replace('~(\'|")~i', '', $str);};
        if($request == '*'):
            $_GET = array_map($callback, $_GET);
            $_POST = array_map($callback, $_POST);
            return true;
        endif;
        
        if(strtoupper($request) == 'GET') {
            $_GET = array_map($callback, $_GET);
            return true;
        }
        if(strtoupper($request) == 'POST') {
            $_POST = array_map($callback, $_POST);
            return true;
        }
    }

endif;

/* ------------------------------------------------------------- */

if(!function_exists('http_force_download')):

    /**
     * http_force_download()
     * force browser to dwonload file
     * @param string $file
     * @return void
     */
    function http_force_download($file = '')
    {
        if(empty($file)) $file = __FILE__ ;
        if(!file_exists($file)) die(PHPharo::Error(__FUNCTION__ . ': file not found'));
        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename='.$file);
        header("Content-Length: " . filesize($file));
        header("Content-Transfer-Encoding:  binary");
        readfile($file);
    }

endif;

/* -------------------------------------------------------------- */

if(!function_exists('http_filter_request')):        
    /**
     * http_filter_request()
     * http gate that filter registered requests 
     * @param string $method
     * @param string $key
     * @param regex $value
     * @return mixed
     */
    function http_filter_request($method, $key, $value)
    {
        $REGEX = array('{INT}' => '#[.0-9]+#',
                       '{STR}' => '#[\p{L}\p{N}-_.]+#iu',
                       '{ALPHA}' => '#[\p{L}]+#iu',
                       '{ALNUM}' => '#[\p{L}\p{N}]+#iu',
                       '{ANY}' => '.+',
                       '{ALL}' => '.+'
                       );
        $REQUESTS[$method][$key] = str_replace(array_keys($REGEX), array_values($REGEX), $value);
        $GLOBALS['REQUESTS'] = $REQUESTS; 
        // current requested method
        $c_method = strtoupper($_SERVER['REQUEST_METHOD']);
        // set current method values
        if($c_method !== 'GET' && $c_method !== 'POST')
            $_INPUT = parse_str(file_get_contents('php://input'));
        elseif($c_method == 'GET') $_INPUT = $_GET;
        elseif($c_method == 'POST') $_INPUT = $_POST;
        // let's start requests filter
        foreach( $_INPUT as $k => $v ):
            if(isset($REQUESTS[$c_method][$k]))
            {
                preg_match_all($REQUESTS[$c_method][$k], $v,$m);
                //var_dump($m);
                $_INPUT[$k] = implode('',$m[0]);
            }
        endforeach;
        if($c_method == 'GET') $_GET = $_INPUT;
        elseif($c_method == 'POST') $_POST = $_INPUT;
        else return $_INPUT;
    }
    
endif;

/* ------------------------------------------------------------ */

if(!function_exists('http_filter_requests')):

    /**
     * http_filter_requests()
     * build on top of http_filter_request()
     * to makes you add array of requests
     * @param array $requests
     * @return void
     */
    function http_filter_requests(array $requests)
    {
        if(!empty($requests))
            foreach($requests as $r)
                call_user_func_array('http_filter_request', $r);
    }

endif;