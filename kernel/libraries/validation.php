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

if (!function_exists('is_email')):

    /**
     * is_email()
     * check str if it valid email
     * @param string $email
     * @return bool
     */
    function is_email($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_HOST_REQUIRED);
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('is_url')):

    /**
     * is_url()
     * check if the str is valid url
     * @param swtring $url
     * @return bool
     */
    function is_url($url)
    {
        $m1 = preg_match('#(http|https|ftp|ftps):\/\/([^www\.][\p{Arabic}a-z0-9-_]+)\.([\w-]+)#iu', $url);
        $m2 = preg_match('#(http|https|ftp|ftps):\/\/www\.([\p{Arabic}a-z0-9-_]+)\.([\w-]+)#iu', $url);
        return (bool) ($m1 || $m2) ;
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('is_ip')):

    /**
     * is_ip()
     * check the ip if it valid
     * @param string $ip
     * @return bool
     */
    function is_ip($ip)
    {
        $m1 = preg_match('#\d{2,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip);
        $m2 = (bool) (filter_var($ip, FILTER_VALIDATE_IP));
        return (bool) ($m1 && $m2);
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('is_alpha')):

    /**
     * is_alpha()
     * check if the given subject alphabatical 
     * @param string $str
     * @return bool
     */
    function is_alpha($str)
    {
        return (bool) (preg_match('#^[\p{L}]+$#iu', $str) || ctype_alpha($str));
    }
    
endif;

/* ---------------------------------------------------------- */

if(!function_exists('is_alnum')):

    /**
     * is_alnum()
     * check if str valid alphanumeric
     * @param string $str
     * @return bool
     */
    function is_alnum($str)
    {
        return (bool) (preg_match('#^[\p{L}\p{N}]+$#ui', $str) || ctype_alnum($str));
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('is_num')):

    /**
     * is_num()
     * check if str is number
     * @param string $str
     * @return bool
     */
    function is_num($str)
    {
        return (bool) (preg_match('#^[\p{N}]+$#', $str));
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('filter_alpha')):

    /**
     * filter_alpha()
     * only get the alphabatical strings with spaces if exists
     * @param subject $str
     * @return string or false on failer
     */
    function filter_alpha($str)
    {
            preg_match_all('#[\p{L}\s]+#iu', $str, $m);
            return implode(' ', $m[0]);
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('filter_num')):

    /**
     * filter_num()
     * only get numeric
     * @param subject $str
     * @return numeric
     */
    function filter_num($str)
    {
        preg_match_all('#[0-9]+#', $str, $m);
        return implode(' ', $m[0]);
    }

endif;

/* ---------------------------------------------------------- */

if(!function_exists('filter_alnum')):

    /**
     * filter_alnum()
     * only get alphanumeric str
     * @param subject $str
     * @return alnum string
     */
    function filter_alnum($str)
    {
        preg_match_all('#[\p{L}\p{N}]+#iu', $str, $m);
        return implode(' ', $m[0]);
    }

endif;