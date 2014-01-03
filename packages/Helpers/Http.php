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

if(!function_exists('http_cache')):

    /**
     * http_cache()
     * enable http cache on a certain page for a while .
     * @param integer $lastfor
     * @return void
     */
    function http_cache($lastfor = 3600)
    {
        $time = gmdate('D, d M Y H:i:s ', time()) . 'GMT';
        if(!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $expires = gmdate('D, d M Y H:i:s ', time() + $lastfor) . 'GMT';
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', time()) . 'GMT');
            header('Cache-Control: public');
            header('Expires: '. $expires);
            echo '<!-- Cached On: '. $time .' , Expired On: '. $expires .'-->' . PHP_EOL;
        } else {
            $expires = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) + $lastfor;
            if(time() > $expires) {
                $expires = gmdate('D, d M Y H:i:s ', time() + $lastfor) . 'GMT';
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', time()) . 'GMT');
                header('Cache-Control: public');
                header('Expires: '. $expires);
                echo '<!-- Re-Cached On: '. $time .' , Expired On: '. $expires .'-->' . PHP_EOL;
            }
            else {
                header('HTTP/1.1 304 NOT MOIFIED', true, 304);
                exit(PHP_EOL . '<!-- Cached ! -->');
            }
        }
    }

endif;

// ------------------------------------------------------------------------

if(!function_exists('http_force_download')):

    /**
     * http_force_download()
     * force the brwoser to download a file .
     * @param string $file
     * @return void
     */
    function http_force_download($file = '')
    {
        if(!file_exists($file)) die(trigger_error(__FUNCTION__ . ': file not found'));
        elseif(empty($file)) $file = $_SERVER['SCRIPT_FILENAME'];
        
        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename='.$file);
        header("Content-Length: " . filesize($file));
        header("Content-Transfer-Encoding:  binary");
        
        readfile($file);
    }

endif;

// -------------------------------------------------------------------------

if(!function_exists('http_authorize')):

    /**
     * http_authorize()
     * require auth. over http
     * @param array $array_auth
     * @param string $message
     * @return mixed
     */
    function http_authorize(array $array_auth, $message = 'Please Auth. Your Self')
    {
        // Check if is Authorized
        if(!isset($_SERVER['PHP_AUTH_USER'])):
            header('WWW-Authenticate: Basic Realm="'.$message.'"'); 
            header('HTTP/1.1 401 Unauthorized'); 
            die('<h1>'.$message.'</h1>');
        endif;
        $usernames = array_keys($array_auth);
        $passwords = array_values($array_auth);
        $username_exists = (bool) in_array($_SERVER['PHP_AUTH_USER'], $usernames);
        $password_exists = (bool) (in_array($_SERVER['PHP_AUTH_PW'], $passwords) && $array_auth[$_SERVER['PHP_AUTH_USER']] === $_SERVER['PHP_AUTH_PW']);
        if(!$username_exists && !$password_exists):
            header('WWW-Authenticate: Basic Realm="Wrong Authorization Data"'); 
            header('HTTP/1.1 401 Unauthorized'); 
            die('<h1> Wrong Authorization Data </h1>');
        endif;
        // every thing ok, then return true
        return true;
    }
    
endif;
    
// ----------------------------------------------------------------

    /**
     * curl()
     * PHP-Pharo Quick cURL connection .
     * @param string $url
     * @param array $curl_options
     * @return false|object
     */
    function curl($url, array $curl_options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true))
    {
        $ch = curl_init($url);
        if(($e = curl_errno($ch))) return $e;
        curl_setopt_array($ch, $curl_options);
        $r['content']  = curl_exec($ch);
        $r['info'] = curl_getinfo($ch);
        curl_close($ch);
        return $r;
    }
