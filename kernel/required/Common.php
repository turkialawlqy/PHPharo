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

if(!function_exists('func_shorct_creator')):

    /**
     * func_shorcut_creator()
     * Just copy an exists function to a new name
     * @author <fb.me/alash3al><phpharo@gmail.com>
     * @license exclusive by PHPharo But free to all
     * @param mixed $callback_name
     * @param string $new_name
     * @return void
     */
    function func_shorcut_creator($callback_name, $new_name = null)
    {
        // if you want to copy array of callbacks
        if(is_array($callback_name)):
            foreach($callback_name as $o => $n):
                // check if is callback
                if(!is_callable($o)) 
                    die(PHPharo::Error('func_shortcut_creator: the function you entered is not-callback'));
                 @eval("
                        if(!function_exists({$n})):
                            function {$n}()
                            {
                                return call_user_func_array('{$o}', func_get_args());    
                            }
                        endif;
                    ");
            endforeach;
            // stop !
            return 0;
        endif;
        // if not array
        if(!is_callable($callback_name)) 
            die(PHPharo::Error('func_shortcut_creator: the function you entered is not-callback'));
         @eval("
                if(!function_exists({$new_name})):
                    function {$new_name}()
                    {
                        return call_user_func_array('{$callback_name}', func_get_args());    
                    }
                endif;
            ");
    }

endif;

/* ------------------------------------------------------------- */
