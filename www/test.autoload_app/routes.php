<?php

PHPharo::With('MyRouter')
->addArray(array(
                /** -- start developing the framework -- */
                array('test', function(){include_once WWW . basename(dirname(__FILE__)) . '/test.php';}),
                array('t', function(){include_once WWW . basename(dirname(__FILE__)) . '/test.php';}),
                array('dev', function(){include_once WWW . basename(dirname(__FILE__)) . '/test.php';})
                /** -- end  developing the framework -- */
              ));