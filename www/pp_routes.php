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

/*
 * Pharo Routes File .
 * Here Is EveryThing, You Tell Pharo What You Want To Do When 
 * A Certain "Uri" Is Hit By The Browser . 
 */
 
    Router::addUri('/', function(){
        include __DIR__ . DS . 'welcome.php';
    });
    