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
 * Pharo RESTful API "Server" Class
 *
 * This class enables you to use create RESTful Based App(s) .
 *
 * @package		REST
 * @category    Server
 * @author		Mohammed Alashaal
 */
 
class Rest_Server
{
    protected $server;
    
    /**
     * Rest_ServeronGET()
     * action to do when GET request happen .
     * @param callback $function
     * @return object
     */
    function onGET($function)
    {
        if(!is_callable($function)) $this->_(__METHOD__ . ' The Callback "'.$function.'" is not valid');
        $this->server['GET'] = $function;
        return $this;
    }
    
    /**
     * Rest_ServeronPUT()
     * action to do when PUT request happen .
     * @param callback $function
     * @return object
     */
    function onPUT($function)
    {
        if(!is_callable($function)) $this->_(__METHOD__ . ' The Callback "'.$function.'" is not valid');
        $this->server['PUT'] = $function;
        return $this; 
    }
    
    /**
     * Rest_ServeronPOST()
     * action to do when POST request happen .
     * @param callback $function
     * @return object
     */
    function onPOST($function)
    {
        if(!is_callable($function)) $this->_(__METHOD__ . ' The Callback "'.$function.'" is not valid');
        $this->server['POST'] = $function;
        return $this;
    }
    
    /**
     * Rest_ServeronDELETE()
     * action to do when DELETE request happen .
     * @param callback $function
     * @return object
     */
    function onDELETE($function)
    {
        if(!is_callable($function)) $this->_(__METHOD__ . ' The Callback "'.$function.'" is not valid');
        $this->server['DELETE'] = $function;
        return $this;
    }
    
    /**
     * Rest_ServerHandle()
     * start the server handler
     * @param bool $return
     * @return mixed
     */
    function Handle($return = false)
    {
        header('Content-Type: application/json; charest=UTF-8', true);
        $method = $_SERVER['REQUEST_METHOD'];
        if(strtoupper($method) === 'GET')
            $input = $_GET;
        else
            parse_str(file_get_contents('php://input'), $input);
        if(!isset($this->server[strtoupper($method)])) {
            $response = json_encode(array(
                            'status' => 405,
                            'message' => 'Method Not Allowed'
                        ));
            if($return) return $response;
            else echo $response;
            return null;
        } else {
            $response = json_encode(call_user_func_array($this->server[strtoupper($method)], array($input)));
            unset($input, $method);
            if($return) return $response;
            else echo $response; 
            return null;  
        }
    }
    
    /**
     * Rest_Server::_()
     * 
     * @param mixed $message
     * @param bool $die
     * @return
     */
    protected function _($message, $die = true)
    {
        throw new RestServerException($message);
        if($die) exit(0);
    }
}

class RestServerException extends Exception{}