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
 * Pharo RESTful API "Client" Class
 *
 * This class enables you to use create RESTful Based App(s) .
 *
 * @package		REST
 * @category    Client
 * @author		Mohammed Alashaal
 */
 
class Rest_Client
{

    /**
     * Rest_Cleint::GET()
     * Do a GET request
     * @param string $url
     * @param array $data
     * @return mixed
     */
    function GET($url, array $data)
    {
        $d = $this->_curl($url, 'GET', $data);
        if(!$d) return false;
        else return json_decode($d);
    }
    
    /**
     * Rest_Cleint::POST()
     * Do a POST request
     * @param string $url
     * @param array $data
     * @return mixed
     */
    function POST($url, array $data)
    {
        $d = $this->_curl($url, 'POST', $data);
        if(!$d) return false;
        else return json_decode($d);
    }
    
    /**
     * Rest_Cleint::PUT()
     * Do a PUT request
     * @param string $url
     * @param array $data
     * @return mixed
     */
    function PUT($url, array $data)
    {
        $d = $this->_curl($url, 'PUT', $data);
        if(!$d) return false;
        else return json_decode($d);
    }
    
    /**
     * Rest_Cleint::DELETE()
     * Do a DELETE request
     * @param string $url
     * @param array $data
     * @return mixed
     */
    function DELETE($url, array $data)
    {
        $d = $this->_curl($url, 'DELETE', $data);
        if(!$d) return false;
        else return json_decode($d);
    }
    
    /**
     * Rest_Cleint::_curl()
     * 
     * @param mixed $url
     * @param mixed $method
     * @param mixed $params
     * @return
     */
    protected function _curl($url, $method, array $params)
    {
        $ch = curl_init($url);
        if(!$ch) return false;
        $opts = array(CURLOPT_AUTOREFERER => true, 
                      CURLOPT_FRESH_CONNECT => true,
                      CURLOPT_HEADER => false,
                      CURLOPT_SSL_VERIFYHOST => false,
                      CURLOPT_SSL_VERIFYPEER => false,
                      CURLOPT_RETURNTRANSFER => true);
        if(strtoupper($method) !== 'GET') {
            $opts[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            $opts[CURLOPT_POSTFIELDS] = http_build_query($params);
        }
        curl_setopt_array($ch, $opts);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
    
    /**
     * Rest_Cleint::_()
     * 
     * @param mixed $message
     * @param bool $die
     * @return
     */
    protected function _($message, $die = true)
    {
        throw new RestClientException($message);
        if($die) exit(0);
    }
}

class RestClientException extends Exception{}