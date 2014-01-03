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
 * Pharo Cache Class
 *
 * This class enables you to use caching in you app(s) .
 * You Can Also Use Multiple Caching Engines (This Is Modular) .
 * 
 * @package	    Cache
 * @author		Mohammed Alashaal
 */
class Cache
{  
    protected $extension = 'PharoCache';
    protected $save_handler = '';
    protected $get_handler = '';
    protected $delete_handler = '';
    protected $exists_handler = '';
    protected $flush_handler = '';
    protected $save_path = '';

    /**
     * Cache::store()
     * store new key ,value & ttl
     * @param mixed $key
     * @param mixed $value
     * @param mixed $ttl
     * @return bool
     */
    function store($key, $value, $ttl)
    {
        if(!is_callable($this->save_handler))
            return call_user_func_array(array($this, '_save'), func_get_args());
        else
            return call_user_func_array($this->save_handler, func_get_args());
    }
    
    /**
     * Cache::get()
     * get a key's value
     * @param mixed $key
     * @return mixed
     */
    function get($key)
    {
        if(!is_callable($this->get_handler))
            return call_user_func_array(array($this, '_get'), func_get_args());
        else
            return call_user_func_array($this->get_handler, func_get_args());
    }
    
    /**
     * Cache::exists()
     * check if a key already Cached
     * @param mixed $key
     * @return bool
     */
    function exists($key)
    {
        if(!is_callable($this->exists_handler))
            return $this->_exists($key);
        else
            return call_user_func_array($this->exists_handler, func_get_args());
    }

    /**
     * Cache::delete()
     * delete a key
     * @param mixed $key
     * @return bool
     */
    function delete($key)
    {
        if(!is_callable($this->delete_handler))
            return call_user_func_array(array($this, '_delete'), func_get_args());
        else
            return call_user_func_array($this->delete_handler, func_get_args());
    }

    /**
     * Cache::flush()
     * clear all Cache
     * @return bool
     */
    function flush()
    {
        if(!is_callable($this->delete_handler))
            return call_user_func_array(array($this, '_flush'));
        else
            return call_user_func_array($this->flush_handler,array());
    }
    
    /**
     * Cache::set_save_handler()
     * set custom save function
     * @param callback $handler
     * @return this
     */
    function set_save_handler($handler)
    {
        if(is_callable($handler)) $this->save_handler = $handler;
        else die(trigger_error(__METHOD__ . " the save handler you entered is not valid ({$handler})"));
        return $this;
    }
    
    /**
     * Cache::set_get_handler()
     * set custom get function
     * @param callback $handler
     * @return this
     */
    function set_get_handler($handler)
    {
        if(is_callable($handler)) $this->get_handler = $handler;
        else die(trigger_error(__METHOD__ . " the get handler you entered is not valid ({$handler})"));
        return $this;
    }
    
    /**
     * Cache::set_delete_handler()
     * set custom delete function
     * @param callback $handler
     * @return this
     */
    function set_delete_handler($handler)
    {
        if(is_callable($handler)) $this->delete_handler = $handler;
        else die(trigger_error(__METHOD__ . " the delete handler you entered is not valid ({$handler})"));
        return $this;
    }
    
    /**
     * Cache::set_flush_handler()
     * set custom flush function
     * @param callback $handler
     * @return this
     */
    function set_flush_handler($handler)
    {
        if(is_callable($handler)) $this->flush_handler = $handler;
        else die(trigger_error(__METHOD__ . " the flush handler you entered is not valid ({$handler})"));
        return $this;
    }
    
    /**
     * Cache::set_exists_handler()
     * set custom chache exists function
     * @param callback $handler
     * @return this
     */
    function set_exists_handler($handler)
    {
        if(is_callable($handler)) $this->exists_handler = $handler;
        else die(trigger_error(__METHOD__ . " the save handler you entered is not valid ({$handler})"));
        return $this;
    }
    
    /**
     * Cache::save_path()
     * Get / Set save path
     * @param string $path
     * @return this
     */
    function save_path($path = null)
    {
        if(empty($path)) return $this->save_path;
        else $this->save_path = $path;
    }
    
    /**
     * Cache::usingAPC()
     * use apc for Cache
     * @return this
     */
    function usingAPC()
    {
        if(!function_exists('apc_store'))
            exit(trigger_error(__METHOD__ . ' , the apc extension is not exists'));
        $this->set_save_handler('apc_store');
        $this->set_delete_handler('apc_delete');
        $this->set_get_handler('apc_fetch');
        $this->set_exists_handler('apc_exists');
        $this->set_flush_handler('apc_clear_Cache');
        return $this;
    }
    

    /**
     * Cache::_flush()
     * 
     * @return
     */
    function _flush()
    {
        @array_map('unlink', glob($this->save_path . '*.' . ltrim($this->extension, '.')));
        return true;
    }
    
    /**
     * Cache::_exists()
     * check if a key already Cached
     * @param mixed $key
     * @return bool
     */
    function _exists($key)
    {
        return (bool)file_exists($this->save_path . md5($key) . '.' . ltrim($this->extension, '.'));
    }
    
    /**
     * Cache::_save()
     * 
     * @param mixed $key
     * @param mixed $value
     * @param mixed $ttl
     * @return
     */
    protected function _save($key, $value, $ttl, $update = false)
    {
        if(empty($this->save_path))
            $this->save_path = realpath(session_save_path()) . DIRECTORY_SEPARATOR;
        elseif(!file_exists($this->save_path) and !is_writable($this->save_path))
            die(trigger_error(__CLASS__ . " this path '{$this->save_path}' must be exists and writable "));
        $this->save_path = rtrim($this->save_path, '/\\') . DIRECTORY_SEPARATOR;
        // --------------------------
        $insert = serialize(array($value, (time() + $ttl)));
        $file = $this->save_path . md5($key) . '.' . ltrim($this->extension, '.');
        if(file_exists($file))
            if($update === true)
                return (bool)file_put_contents($file, $insert);
            else
                return true;
        else
            return (bool)file_put_contents($file, $insert);
    }
    
    /**
     * Cache::_delete()
     * 
     * @param mixed $key
     * @return
     */
    protected function _delete($key)
    {
        if(file_exists(($file = $this->save_path . md5($key) . '.' . ltrim($this->extension, '.')))) {
            return (bool)unlink($file);
        } else
            return true;
    }
    
    /**
     * Cache::_get()
     * 
     * @param mixed $key
     * @return
     */
    protected function _get($key)
    {
        if(empty($this->save_path))
            $this->save_path = realpath(session_save_path()) . DIRECTORY_SEPARATOR;
        elseif(!file_exists($this->save_path) and !is_writable($this->save_path))
            die(trigger_error(__CLASS__ . " this path '{$this->save_path}' must be exists and writable "));
        $this->save_path = rtrim($this->save_path, '/\\') . DIRECTORY_SEPARATOR;
        // --------------------------
        $file = $this->save_path . md5($key) . '.' . ltrim($this->extension, '.');
        if(!file_exists($file))
            return false;
        list($value, $expires) = unserialize(file_get_contents($file));
        if(time() >= $expires){
            @unlink($file);
            return false;
        }
        else
            return $value;
    }
}

