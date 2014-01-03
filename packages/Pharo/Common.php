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

	/** PHP-Pharo "PP" Exception Class */
	class PPException extends Exception{}


// -------------------------------------------------------

    /**
     * pp_loader()
     * load library file [Registered As SPL-AutoLoad]
     * @param string $name
     * @param integer $dir
     * @return bool
     */
    function pp_loader($filename)
    {
        static $paths = array();
        // add packages dir if not exists
        if(!isset($paths[PP_PACKAGES_DIR]))
            $paths[PP_PACKAGES_DIR] = PP_PACKAGES_DIR;
        // if the name is full-path and exists, load it .
        if(file_exists($filename) and is_readable($filename))
            return include $filename;
        // register new if you requested
        if(stripos($filename, 'addNewAutoloadDir:') === 0) {
            $new_path = trim(ltrim($filename, 'addNewAutoloadDir:'));
            $paths[$new_path] = $new_path;
            return true;
        }
        // not a file let's find it .
        // filename paths
        $filename = str_replace(array('\\', '_'), DS, $filename);
        foreach($paths as &$inc_path):
            if(file_exists($tmp = $inc_path . $filename . DS . basename($filename) . PP_FILES_EXTENSION))
                return include $tmp;
            elseif(file_exists($tmp = $inc_path . $filename . PP_FILES_EXTENSION))
                return include $tmp;
        endforeach;
        // not exists !
        return false;
    }
    
// -------------------------------------------------------

    /**
     * pp_error()
     * send custom PHP-Pharaoh Error Message [Exception]
     * @param string $message
     * @return void
     */
    function pp_error($message)
    {
        echo '<div style="margin:15px auto 15px auto; box-shadow: 0 0 10px #333; color: maroon; font-weight: bolder;border-radius: 3px;background: #fff; padding: 15px; max-width: 80%"> '.$message.' </div>';
    }
    
// -------------------------------------------------------

    /**
     * pp_store()
     * global PHP-Pharo key, value store Managaer
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    function pp_store($key, $value = ':g:')
    {
        static $_PPSTORE = array();
        // if the key is empty , return the whole "PP" array
        // if the value is emoty then return the
        // required key if exists
        // else set the key = value
        if(empty($key))
            return $_PPSTORE;
        if($value === ':g:')
            if(isset($_PPSTORE[$key]))
                return $_PPSTORE[$key];
            else
                return false;
        else
            $_PPSTORE[$key] = $value;
    }

// -------------------------------------------------------------

    /**
     * trim_invisible()
     * trim invisible characters 
     * @param string $str
     * @return string
     */
    function trim_invisible($str)
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
    }

// -------------------------------------------------------------

    /**
     * redirect()
     * redirect to an url
     * NOTE: Will Force (html) if headers already sent .
     * @param string $to
     * @param integer $using
     * @return void
     */
    function redirect($to, $using = 302)
    {
        if(headers_sent()) redirect($to, 'html');
        switch(strtolower($using)):
            case 'html':
                echo('<meta http-equiv="refresh" content="0; URL='.$to.'">');
                break;
            case 'js':
                echo('<script type="text/javascript">window.location="'.$to.'";</script>');
                break;
            default:
                header('Location: ' . $to, true, $using);
        endswitch;
        exit(0);
    }
