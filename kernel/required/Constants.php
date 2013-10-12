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


/** Define All Env Paths */
foreach(PHPharo::Env() as $k => $v):
    define($k, $v, True);
endforeach;

/** The PHP-Pharaoh Required PHP Version */
define('PHPHARO_PHP_VERSION', '5.3', TRUE);

/** The PHPharo Version */
define('PHPHARO_VERSION_INT', '1.0.0');
define('PHPHARO_VERSION_TYPE', 'RC3');
define('PP_VERSION', PHPHARO_VERSION_INT . ' ' . PHPHARO_VERSION_TYPE);
define('PHPHARO_VERSION', PP_VERSION);
