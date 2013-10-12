<?php

define('PP_WELCOME_DIR', basename(dirname(__FILE__)) . '/');

PHPharo::With('MyRouter')
->addArray(array(
                array('', function(){include_once WWW . PP_WELCOME_DIR . 'welcome.php';}),
                array('welcome.html', function(){include_once WWW . PP_WELCOME_DIR . 'welcome.php';}),
              ));