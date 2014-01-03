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
 * Pharo Filesystem Local Class
 *
 * This class enables you to use manage local files/folders easily .
 *
 * @package		Filesystem
 * @author		Mohammed Alashaal
 */
class Filesystem_Local
{
    const DS = DIRECTORY_SEPARATOR;
    const FILE_NEW = 1;
    const FILE_PREPEND = 2;
    const FILE_APPEND = 3;
    
    static $resource_contex = false;
    
    
    /**
     * Filesystem_Local::readFile()
     * read a file
     * @param string $path
     * @return string
     */
    static function readFile($path)
    {
        return file_get_contents($path);
    }
    
    /**
     * Filesystem_Local::readDir()
     * read a directory [recursive]
     * @param string $path
     * @param bool $recursive
     * @return array
     */
    static function readDir($path, $recursive = true)
    {
        $all = array();
        $path = realpath($path);
        if($path === false) return false;
        if(is_file($path)) $all[] = $path;
        elseif(is_dir($path)){
            foreach(scandir($path) as $p):
                if($p !== '.' and $p !== '..'):
                    if($recursive === true)
                        $all = array_merge($all, self::readDir($path . self::DS . $p, (bool)$recursive));
                    else
                        $all[] = $path . self::DS . $p;
                endif;
            endforeach;
            $all[] = $path;
        }
        return (array)$all;
    }
    
    /**
     * Filesystem_Local::writeFile()
     * write data to file
     * @param string $path
     * @param mixed $data
     * @param int $operation
     * @param int $flags
     * @return bool
     */
    static function writeFile($path, $data, $operation = self::FILE_NEW, $flags = null)
    {   
        if($operation === self::FILE_NEW)
            return (bool)file_put_contents($path, $data, (int)$flags);
        elseif($operation === self::FILE_APPEND)
            return (bool)file_put_contents($path, $data, FILE_APPEND + (int)$flags);
        elseif($operation === self::FILE_PREPEND)
            return (bool)file_put_contents($path, $data . file_get_contents($path), (int)$flags);
        else
            return false;
    }
    
    /**
     * Filesystem_Local::createDir()
     * create a directory
     * @param string $path
     * @param int $mode
     * @return bool
     */
    static function createDir($path, $mode = 0777)
    {
        return (bool)@mkdir($path, $mode);
    }
    
    /**
     * Filesystem_Local::unlink()
     * unlike [file/directory(recursive)]
     * @param string $path
     * @return bool
     */
    static function unlink($path)
    {
        $path = realpath($path);
        $state = false;
        if(file_exists($path)) {
            foreach(self::readDir($path) as $p)
                if(is_file($p)) $state = unlink($p);
                elseif(is_dir($p)) $state = rmdir($p);
            return $state;
        } else
            return false;
    }
    
    /**
     * Filesystem_Local::copy()
     * copy file/directory
     * @param string $source
     * @param string $dest
     * @return bool
     */
    static function copy($source, $dest)
    {
        $source = realpath($source);
        if(is_file($source)) return (bool)copy($source, $dest);
        elseif(is_dir($source)) {
            // create the destenation if not exists
            if(!is_dir($dest)) @mkdir($dest, 0777);
            $dest = realpath($dest);
            // now get the source content
            $sc = scandir($source);
            // result
            $result = false;
            // loop over the s-c
            foreach($sc as &$new_src):
                if($new_src !== '.' and $new_src !== '..'):
                    // the new_src is file, copy it directly
                    if(is_file($source . self::DS . $new_src))
                        $result = copy($source . self::DS . $new_src , $dest . self::DS . $new_src);
                    // if the new_src is directory, let's create it
                    elseif(is_dir($source . self::DS . $new_src)) {
                        // create the new_src in the dest if not exists
                        if(!is_dir($dest . self::DS . $new_src))
                            @mkdir($dest . self::DS . $new_src, 0777);
                        // recursive check ...
                        self::copy($source . self::DS . $new_src, $dest . self::DS . $new_src);
                        $result = true;
                    }
                endif;
            endforeach;
            return $result;
        } else return false;
    }
    
    /**
     * Filesystem_Local::move()
     * move file/dir
     * @param string $source
     * @param string $dest
     * @return bool
     */
    static function move($source, $dest)
    {
        self::copy($source, $dest);
        return (bool)self::unlink($source);
    }
    
    /**
     * Filesystem_Local::rename()
     * rename file/dir
     * @param string $path
     * @param string $new_path
     * @return bool
     */
    static function rename($path, $new_path)
    {
        return (bool)rename($path, $new_path);
    }
}