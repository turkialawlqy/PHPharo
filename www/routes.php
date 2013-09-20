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

function index()
{
    include_once WWW . 'welcome/welcome.php';
}

PHPharo::With('MyRouter')->addArray(array(
                                            array('', 'index'),
                                            array('welcome.html', 'index')
                                          ));

//PHPharo::With('MyRouter')->add('', 'index');


