<?php

/**
 * This File Used For Testing
 * While Developing or update something
 */


/** PHPharo */

fsc('vd','v');
fsc('print_r','p');

PHPharo::Globals('output_rm_whitespaces',false);

v(MyFile::info(realpath(PP_PATH)));