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
 * -----------------------------------------------------------
 *  Initialize Horus
 * -----------------------------------------------------------
 * This line will initialize Horus, the new routing mecanism
 * and set simulation to [pharo-default] this means that 
 * Horus will auto-detect if it must you index.php/ or not .
 * 
 * If you want to work from any other directory , just include 
 * the base routes file here :) .
 */

    Horus::init(PP_SIMULATE_REWRITER);

// -----------------------------------------------------------


    // Route welcome page .
    Horus::rewrite('/ => ?home', true, create_function('', 
        'include_once PP_WWW_DIR . "welcome.php";'
    ));