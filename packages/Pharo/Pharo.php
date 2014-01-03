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


/*
 * ------------------------------------------------------
 *  Compress and Handle the output
 * ------------------------------------------------------
 *  This will check if you have zlib extension, then
 *  Check if the browser supports compression .
 *  After that we start ob again so we bypass the 
 *  "Encoding Error"
 */

    if (extension_loaded('zlib'))
    {
    	if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) and strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
    	{
    	   // start output buffer (compressed)
    		ob_start('ob_gzhandler');
    	}
    }
    // start output buffer .
    ob_start();
    // set the default header[content-type]
    header('Content-Type: text/html; charset=UTF-8', true);

/*
 * ------------------------------------------------------
 *  Set PHP-Pharo "PP" Constants 
 * ------------------------------------------------------
 */
 
    // Version Constants
    define('PP_VERSION_NUMBER', '1.1');
    define('PP_VERSION_TYPE', 'STABLE');
    define('PP_VERSION_NAME', 'HORUS');
    define('PP_REQUIRED_PHP', '5.2.17');
    
    // Environment > Local Paths Constants
    define('DS', DIRECTORY_SEPARATOR);
    define('PP_DIR', realpath(dirname(__FILE__)) . DS);
    define('PP_BASE_DIR', realpath(dirname(dirname(PP_DIR))) . DS);
    define('PP_WWW_DIR', PP_BASE_DIR . 'www' . DS);
    define('PP_PACKAGES_DIR', PP_BASE_DIR . 'packages' . DS);
    define('PP_FILES_EXTENSION', '.php');
    define('PHP_TEMP_DIR', realpath(session_save_path()) . DS);
    define('SYS_TEMP_DIR', realpath(sys_get_temp_dir()) . DS);
    
    // Environment > HTTP Constants
    define('IS_HTTPS', (bool)isset($_SERVER['HTTPS']), true);
    define('IS_CLI', (bool)(strtolower(php_sapi_name()) === 'cli' or defined('STDIN')), true);
    define('IS_APACHE', (bool)function_exists('apache_get_version'), true);
    define('IS_AJAX', (bool)(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'), true);
    define('PP_SIMULATE_REWRITER', (bool)!isset($_SERVER['PP_DONNOT_SIMULATE_REWRITER']));
    define('PP_SERVER_URL', (IS_HTTPS ? 'https' : 'http') . '://' . rtrim($_SERVER['SERVER_NAME'], '/') . '/');
    define('PP_URL', PP_SERVER_URL . ltrim(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'), '/') . '/');
    define('PP_URI', rtrim(PP_URL, '/') . '/' . (PP_SIMULATE_REWRITER == true ? basename($_SERVER['SCRIPT_NAME']) . '/' : ''));
    define('PP_WWW_URL', rtrim(PP_URL, '/') . '/www/');
    
    // REGEX_* Constants
    define('PP_REGEX_PATTERN_ANY', '(.+)');
    define('PP_REGEX_PATTERN_WORDS', '([\w]+)');
    define('PP_REGEX_PATTERN_ALPHA', '([[:alpha:]]+)');
    define('PP_REGEX_PATTERN_ALNUM', '([[:alnum:]]+)');
    define('PP_REGEX_PATTERN_INT', '([[:digit:]]+)');
    define('PP_REGEX_PATTERN_CTRL', '([[:cntrl:]]+)');
    define('PP_REGEX_PATTERN_SLUG', '([a-z0-9_-]+)');
    define('PP_REGEX_PATTERN_EMAIL', '([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})');
    define('PP_REGEX_PATTERN_URL', '(((http|https|ftp|ftps)?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?)');
    define('PP_REGEX_PATTERN_IP', '(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)');
    
    
/*
 * ------------------------------------------------------
 *  Run only on (>=) required version
 * ------------------------------------------------------
 */
    if(version_compare(phpversion(), PP_REQUIRED_PHP) < 0)
        die('<h3>Pharo Run Only on PHP >= ' . PP_REQUIRED_PHP .' Yours is ' . phpversion().'</h3>');

/*
 * ------------------------------------------------------
 *  load the common functions file
 * ------------------------------------------------------
 */
    include PP_DIR . 'Common.php';

/*
 * ------------------------------------------------------
 *  Register "PP" loader in SPL-Autoload
 * ------------------------------------------------------
 */
    spl_autoload_register('pp_loader');
    
/*
 * ------------------------------------------------------
 *  Load WWW Index
 * ------------------------------------------------------
 */
    
    include_once PP_WWW_DIR . 'index.php';

/*
 * ------------------------------------------------------
 *  End The Framework .
 * ------------------------------------------------------
 */
    // end output buffering
    ob_end_flush();
    exit(0);