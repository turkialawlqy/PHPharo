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
 * This class enables you to hash, encrypt & decrypt any string .
 *
 * @package		Hash
 * @author		Mohammed Alashaal
 */
class Hash
{
    protected static $mcrypt_key;
    protected static $mcrypt_iv;
    protected static $mcrypt_mode;
    protected static $mcrypt_cipher;
    
    /**
     * Hash::encrypt()
     * encrypt a string using mcrypt_encrypt
     * @param string $data
     * @param string $salt_key
     * @param string $cipher
     * @param string $mode
     * @param string $iv_source
     * @return string
     */
    static function encrypt($data, $salt_key = null, $cipher = MCRYPT_RIJNDAEL_256,$mode = MCRYPT_MODE_ECB, $iv_source = MCRYPT_RAND)
    {
        self::$mcrypt_cipher = $cipher;
        self::$mcrypt_iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), $iv_source);;
        self::$mcrypt_mode = $mode;
        if(empty($salt_key)) $salt_key = time() . mt_rand(0,99) . 'saH98SFDS@.5w4sfs%^abcdefghijklmnop[{}":&*^%#&^_)fsk';
        self::$mcrypt_key = self::md5($salt_key);
        // -------------------------------------------
        return base64_encode(mcrypt_encrypt(
                self::$mcrypt_cipher, self::$mcrypt_key, $data
                , self::$mcrypt_mode, self::$mcrypt_iv));
    }
    
    /**
     * Hash::decrypt()
     * decrypt an encrypted string using mcrypt_decrypt
     * @param string $data
     * @return string
     */
    static function decrypt($data)
    {
        return mcrypt_decrypt(
                self::$mcrypt_cipher, self::$mcrypt_key, base64_decode($data)
                , self::$mcrypt_mode, self::$mcrypt_iv);
    }
    
    function __call($name, $args)
    {
        $args = (array)$args;
        $h = array_flip(hash_algos());
        if(isset($h[$name]))
            return hash($name, reset($args));
        elseif(isset($h[($name = str_replace('_', ',', $name))]))
            return hash($name, reset($args));
        else
            return false;
    }
    
    static function __callStatic($name, $args)
    {
        $h = array_flip(hash_algos());
        if(isset($h[$name]))
            return hash($name, reset($args));
        elseif(isset($h[($name = str_replace('_', ',', $name))]))
            return hash($name, reset($args));
        else
            return false;
    } 
}