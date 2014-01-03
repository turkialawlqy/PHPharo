<?php

/**
 * PHP-Pharo .
 *
 * An open source application development framework for PHP 5.2.17 or newer .
 *
 * @package		PHP-Pharo
 * @author		Mohammed Alashaal, fb.me/alash3al
 * @copyright	Copyright (c) 2013 - 2015 .
 * @license		GPL-v3
 * @link		http://github.com/alash3al/PHPharo
 */

// ------------------------------------------------------------------------

/**
 * Pharo Horus Class
 *
 * This is the pharo-smart router system .
 *
 * @package		Horus
 * @author		Mohammed Alashaal
 */
class Horus
{
    
    /**
     * Horus::init()
     * initialize Horus Handler .
     * @param bool $simulate
     * @return void
     */
    public static function init($simulate = true)
    {
        // set new server vars
        $_SERVER['SERVER_URL'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . rtrim($_SERVER['SERVER_NAME'], '/') . '/' ;
        $_SERVER['SCRIPT_URL'] = $_SERVER['SERVER_URL'].ltrim(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/', '/');
        $_SERVER['SCRIPT_URI'] = $_SERVER['SCRIPT_URL'].($simulate === true ? basename($_SERVER['SCRIPT_NAME']) . '/' : '');
        
        // Force Redirection To 'script_name/' if simulation enabled
        if($simulate === true and !isset($_SERVER['PATH_INFO'])):
            self::go($_SERVER['SCRIPT_URI']);
        endif;
        
        // Update the PATH_INFO
        $uri = $_SERVER['REQUEST_URI'];
        @list($u, $_SERVER['QUERY_STRING']) = (array)explode('?', $uri);
        $_SERVER['PATH_INFO'] = preg_replace('/^'.addcslashes($_SERVER['SCRIPT_NAME'], '/.').'/i', '', preg_replace('/\/+/','/',$u), 1);
        $_SERVER['PATH_INFO'] = preg_replace('/\/+/', '/', $_SERVER['PATH_INFO']);
        
        // Update $_GET
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        
        unset($uri, $u);
        return null;
    }
    
    /**
     * Horus::rewrite()
     * rewrite a uri .
     * @param string $rule
     * @param bool $strict
     * @param callback $callback
     * @param bool $case_sensitive
     * @return void
     */
    public static function rewrite($rule, $strict = false, $callback = null, $case_sensitive = false)
    {
        // get what i need
        @list($uri_from, $uri_to) = explode(' => ', $rule);
        
        // must provide uri_to
        if(empty($uri_to)) trigger_error(__METHOD__ . ' you must provide the rewrite to uri, there is no second-part in "'.$rule.'" ', E_USER_ERROR);
        
        // convert => query_str to slash separated
        if(strpos($uri_to, '&') !== false and strpos($uri_to, '/') === false) {
            parse_str(ltrim($uri_to, '?'), $x);
            $uri_to = implode('/', array_keys($x));
            unset($x);
        }
        
        // prepare the uri_from & uri_to
        $uri_from = trim(str_replace(array(':h', '/'), array('(.*?)', '\/'), rtrim(ltrim($uri_from, '/'), '/')));
        $uri_to = trim(rtrim(ltrim($uri_to, '/'), '/'));
        $regex_opts = $case_sensitive === false ? 'i' : '';
        $regex_internal = $strict === true ? '$' : '';
        
        // the count of uri_from params must be the same as uri_to params
        if(count($x1 = explode('/', $uri_from)) !== count($x2 = (array)explode('/', $uri_to)))
            trigger_error(__METHOD__ . ' The params count of uri_(from & to) are not the same for ("'.$uri_from.' , '.$uri_to.'") ', E_USER_ERROR);
        
        // if need-callback, must be valid
        if(!empty($callback) and !is_callable($callback))
            trigger_error(__METHOD__ . ' The Callback "'.$callback.'" is not valid .');
        
        // only work if the uri_from is hit by the browser
        if(preg_match('/^'.$uri_from.$regex_internal.'/'.$regex_opts, ltrim(rtrim($_SERVER['PATH_INFO'], '/'), '/'))):
            
            // the requested uri parts
            $x1 = (array)explode('/', ltrim(rtrim($_SERVER['PATH_INFO'], '/'), '/'));
            
            // set keys and values, free memory from unwanted .
            $vals = array_values($x1);
            $keys = array_values($x2);
            unset($x1, $x2, $strict, $regex_internal, $regex_opts, $rule, $case_sensitive);
            
            // keys must be the same count as values .
            if(count($vals) > count($keys))
                    $vals = array_slice($vals, 0, count($keys));
            
            // combine keys => values and add them to _GET array .
            $_GET = (array)(array_combine($keys, $vals)) + (array)$_GET;
            
            // if you added a callback and it is valid, let's call it .
            if(!empty($callback) and is_callable($callback)) call_user_func($callback);
        endif;
        
        unset($callback, $keys, $vals);
        return null;
    }

    /**
     * Horus::errorDoc()
     * show error document
     * @param string $title
     * @param string $body
     * @return mixed
     */
    public static function errorDoc($title, $body)
    {
        // end and clean the last buffer
        ob_get_clean();
        // create new buffer
        ob_start();
        $boxCss = 'margin:18%;border:1px solid #ccc;padding:15px;color:#555;border-radius:10px;width:60%;box-shadow:0 0 10px #ddd;text-shadow:0 0 1px #fff';
        $titCss = 'border-bottom: 1px solid #ddd;padding:5px;font-size:20px;color:maroon';
        $bodCss = 'margin-top:5px;word-wrap:break-word;';
        $e  = '<!DOCTYPE html><html><head><title>' . $title . '</title></head><body>';
        $e .= '<div style=\'' . $boxCss . '\'>';
        $e .= '<div style=\'' . $titCss . '\'><b>' . $title . '</b></div>';
        $e .= '<div style=\'' . $bodCss . '\'><b>' . $body . '</b></div>';
        $e .= '</div></body></html>';
        unset($body,$title,$bodCss,$boxCss);
        exit($e);
        unset($e);
    }
    
    /**
     * Horus::go()
     * internal redirection .
     * @param string $url
     * @param integer $http_code
     * @return void
     */
    protected static function go($url, $http_code = 302)
    {
        if(headers_sent()) exit('<meta http-equiv="refresh" content="0;URL='.$url.'"/>');
        header('Location: '. $url, true, $http_code);
        exit(0);
    }

}