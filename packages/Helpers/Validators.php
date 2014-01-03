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

if(!function_exists('is_email')):

    /**
     * is_email()
     * Find whether a value is valid email
     * @param string $string
     * @return bool
     */
    function is_email($string)
    {
        return (bool)(filter_var($string, FILTER_VALIDATE_EMAIL, FILTER_FLAG_HOST_REQUIRED));
    }

endif;

// --------------------------------------------------------

if(!function_exists('is_url')):

    /**
     * is_url()
     * Find whether a value is valid url
     * @param string $string
     * @return bool
     */
    function is_url($string)
    {
        return (bool)(filter_var($string, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED+FILTER_FLAG_SCHEME_REQUIRED));
    }

endif;

// --------------------------------------------------------

if(!function_exists('is_ip')):

    /**
     * is_ip()
     * Find whether a value is valid ip
     * @param string $string
     * @return bool
     */
    function is_ip($string)
    {
        return (bool)(filter_var($string, FILTER_VALIDATE_IP));
    }

endif;

// --------------------------------------------------------

if(!function_exists('is_ipv4')):

    /**
     * is_ipv4()
     * Find whether a value is valid ipv4
     * @param string $string
     * @return bool
     */
    function is_ipv4($string)
    {
        return (bool)(filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
    }

endif;

// --------------------------------------------------------

if(!function_exists('is_ipv6')):

    /**
     * is_ipv6()
     * Find whether a value is valid ipv6
     * @param string $string
     * @return bool
     */
    function is_ipv6($string)
    {
        return (bool)(filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
    }

endif;

