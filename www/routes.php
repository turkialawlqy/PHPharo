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


PHPharo::With('MyRouter')
->addArray(array(
                array('', function(){include_once WWW . 'welcome/welcome.php';}),
                array('welcome.html', function(){include_once WWW . 'welcome/welcome.php';}),
                /** -- start developing the framework -- */
                array('test', function(){include WWW . 'test.php';}),
                array('t', function(){include WWW . 'test.php';}),
                array('dev', function(){include WWW . 'test.php';})
                /** -- end  developing the framework -- */
              ));
