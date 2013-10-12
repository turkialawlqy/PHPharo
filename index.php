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

/** Define The PHP-PHARO PATH IF NOT DEFINED */
defined('PP_PATH') or define('PP_PATH', './', TRUE) ;

/** Require[MustLoad] PHPharo Kernel Object */
require_once PP_PATH . 'kernel/required/PHPharo.php';

/** PHPharo Initialization */
PHPharo::Start();

/** Hook An Action Before Routes */
PHPharo::With(H)->apply_actions('phpharo.routes');

/** Load The WWW Routes File */
PHPharo::Load(WWW . 'routes.php');

/** End or Shutdown PHPharo */
PHPharo::End();