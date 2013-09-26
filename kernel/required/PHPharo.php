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

/**
 * PHPharo
 * What You Need Is What I Need Too !
 * @package PHPharo
 * @copyright 2013
 * @version 1 RC2
 */
class PHPharo
{
    
    protected static $Globals;
    protected static $Started = false;

    
    /**
     * PHPharo::Start()
     * Initialize PHPharo
     * @return void
     */
    public static function Start()
    {
        // if started show exception
        if(self::$Started) self::Error('PHPharo is already started');
        
        // start output control
        ob_start();
        
        // set my headers
        header('X-Powered-By: PHP; Based-On: PHPharo', true);
        
        // start the framework
        self::$Started = true;
        
        // register spl_autoload
        spl_autoload_register(array('self', 'Load'));
        
        // required files
        $rq = self::Env()->KERNEL_RQ;
        self::Load($rq . 'MyEHandler.php',
                   $rq . 'MyBenchmark.php',
                   $rq . 'Constants.php',
                   $rq . 'MyHooks.php',
                   $rq . 'MyRouter.php');
                   
        // Version
        if(version_compare(PHP_VERSION, PHPHARO_PHP_VERSION) < 0)
            die(self::Error('PHPharo Requires PHP At Least (version '.PHPHARO_PHP_VERSION.') Yours is (version '.PHP_VERSION.')'));
        
        
        // Initialize, Call some methods & classes
        MyEHandler::handle();
        MyBenchmark::new_point('phpharo.start');
        self::ModsLoader();
        MyHooks::apply_actions('phpharo.start');
        PHPharo::With('MyRouter')->start();
    }
    
    /**
     * PHPharo::Render()
     * Render A Template And Replace stirngs in it
     * @param mixed $tpl
     * @param bool $echo
     * @param array $replacements
     * @return string
     */
    public static function Render($tpl , $echo = true, array $replacements = array())
    {
        $tpl = self::Env()->WWW . $tpl;
        if(!file_exists($tpl))
            throw new Exception('PHPharo::Render() : (' . $tpl . ') file not found');
        else {
            $content = file_get_contents($tpl);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            if($echo) echo $content;
            else return $content;
        } 
    }
    
    /**
     * PHPharo::With()
     * With : Class Constructor
     * @param class $classname
     * @param $args
     * @return false on failer or object on success
     */
    public static function With($classname)
    {
        if(class_exists($classname))
            return new $classname;
        else
            throw new Exception('PHPHARO::WITH : Class (' . $classname . ') NOT FOUND' );
    }
    
    /**
     * PHPharo::Globals()
     * SET, GET Global Vals
     * @param string $key
     * @param string $value
     * @return object
     */
    public static function Globals($key = '', $value = '')
    {
            self::$Globals[$key] = $value;
            return json_decode(json_encode(self::$Globals));
    }
    
    
    /**
     * PHPharo::Load()
     * Load A File 
     * This Method Based On SPL-STANDARD
     * @param mixed $paths
     * @return void
     */
    public static function  Load($files)
    {
        $paths = (is_array($files)) ? $files : func_get_args();
        if(!empty($paths)):
            foreach( $paths as $path ):
                if(!empty($path)):
                    if(file_exists($path))
                        include_once $path;
                    else
                        self::Error('PHPPharo::Load The File ('. $path .') Not Found');
                endif;
            endforeach;
        endif;
    }
    
    /**
     * PHPharo::LoadLibrary()
     * Load libraries
     * @param mixed $library
     * @return void
     */
    public static function LoadLibrary($library)
    {
        $libraries = (is_array($library)) ? $library : func_get_args();
        foreach($libraries as $library):
            $path1 = self::Env()->KERNEL_LIBS . $library . '.php';
            $path2 = self::Env()->WWW_LIBS . $library . '.php';
            if(file_exists($path1))
                self::Load($path1);
            elseif(file_exists($path2))
                self::Load($path2);
            else
                self::Errors('PHPharo::LoadLibrary The Library ('.$library.') Not Found in any libraries folder');
        endforeach;
    }
    
    /**
     * PHPharo::LoadMod()
     * Load Module file
     * @param mixed $libraries
     * @return void
     */
    public static function LoadMod($Mod)
    {
        self::Load(self::Env()->MODS . $Mod . '/' . basename($Mod) . '.php');
    }
    
    /**
     * PHPharo::Assets()
     * Get direct url for an asset
     * @param string $path
     * @return url
     */
    public static function Assets($path = '')
    {
        return self::Env()->URL . 'www/assets/'. $path ;
    }
    
    /**
     * PHPharo::Env()
     * Get An Environment
     * @return object
     */
    public static function Env()
    {
        $url = ((isset($_SERVER['HTTPS']))) ? 'https' : 'http' . '://' . $_SERVER['SERVER_NAME']
               . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']); 
        $uri = (isset($_SERVER['USE_MOD_REWRITE']))
               ? $url
               : $url . basename($_SERVER['SCRIPT_NAME']) . '/';
        $env = array(
                        'URL' => $url,
                        'URI' => $uri,
                        'WWW' => PP_PATH . 'www/',
                        'MODS' => PP_PATH . 'mods/',
                        'KERNEL' => PP_PATH . 'kernel/',
                        'KERNEL_RQ' => PP_PATH . 'kernel/required/',
                        'KERNEL_LIBS' => PP_PATH . 'kernel/libraries/',
                        'TEMP' => PP_PATH . 'www/temp/',
                        'ASSETS' => PP_PATH . 'www/assets/',
                        'WWW_LIBS' => PP_PATH . 'www/libraries/'
                    );
        
        // convert it to object
        return json_decode(json_encode($env));
    }
    

    /**
     * PHPharo::Error()
     * Show An Error
     * @param string $err_str
     * @return string
     */
    public  static function Error($err_str)
    {
        $css = 'font-weight:bolder;padding:10px;border:1px solid #222;background:#333;color:#eee;width:70%;margin:auto;box-shadow:0 0 5px #555;margin-top:2%';
        
        $e = '<div style="'.$css.'">
                An error occured <br /> 
                it`s &laquo;<i style="color:yellowgreen">'.$err_str.'</i>&raquo;
              </div>';

        echo $e;
    }
    
    /**
     * PHPharo::End()
     * Shutdown !
     * @return void
     */
    public static function End()
    {
        MyRouter::route();
        /* extend the output */
        MyHooks::apply_actions('phpharo.output');
        /* start output control again */
        ob_start();
        /* show the output */
        echo ob_get_clean();
        /* exceute some actions in the end */
        MyHooks::apply_actions('phpharo.shutdown');
        /* flush the output */
        ob_end_flush();
        // Close PHP Pharaoh
        self::$Started = false;
        exit;
    }
    
    /**
     * PHPharo::ModsLoader()
     * Load The Autoload_mods
     * @return void
     */
    protected static function ModsLoader()
    {
        $mods = self::Env()->MODS;
        $autoloads = glob($mods . '*.autoload');
        if(!empty($autoloads)):
            foreach($autoloads as $mod):
                if(is_file($mod))
                    self::Load($mod);
                elseif(is_dir($mod))
                    self::Load($mod . '/' . str_replace('.autoload', '', basename($mod)) . '.php');
            endforeach;
        endif;
    }
    
}