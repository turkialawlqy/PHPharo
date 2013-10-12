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
 * MyPlugins
 * 
 * @package phpmyplugins
 * @author PHPharo
 * @copyright 2013
 * @version 0.2
 * @access public
 */
class MyPlugins
{

    protected static $pluginsDir;
    protected static $tmpFile;
    protected static $EXT;


    /**
     * MyPlugins::config()
     * Config phpmyplugins
     * @param File $tmpFile
     * @param Directory $pluginsDir
     * @param extinsion $pluginExt
     * @return
     */
    static function config($tmpFile, $pluginsDir, $pluginExt = 'php')
    {
        // Check if tmp file exists
        (!file_exists($tmpFile)) ? die(self::error('The TmpFile Not Exists')) : '';

        // Check if pluginsDirectory Exists
        (!file_exists($pluginsDir)) ? die(self::error('The PluginsDir Not Exists')) : '';

        // Check if tmp file writable
        (!is_writable($tmpFile)) ? die(self::error('The TMP File Must Be Writable')) :
            '';

        // Check if pluginsDir writable
        (!is_writable($pluginsDir)) ? die(self::error('The PluginsDir Must Be Writable')) :
            '';

        // Every thing is right ! :)
        self::$pluginsDir = rtrim($pluginsDir, '/') . '/';
        self::$tmpFile = $tmpFile;
        self::$EXT = $pluginExt;
    }

    /**
     * MyPlugins::loader()
     * load only active plugins
     * @return
     */
    static function loader()
    {
        foreach (self::plugins() as $plugin => $info):
            $pluginFile = self::$pluginsDir . $plugin . '/' . $plugin . '.' . self::$EXT;
            // if the plugin state is true [active]
            // then include it once
            ($info['state']) ? include_once $pluginFile : '';
        endforeach;
    }

    /**
     * MyPlugins::plugins()
     * get array of all correct plugins
     * @return
     */
    static function plugins()
    {
        // parse the plugins folder
        $all = glob(self::$pluginsDir . '*');
        // this is where the plugin stored
        $plugins = array();
        foreach ($all as $pluginDir) {
            // get only the plugin folder name
            $plugin = basename($pluginDir);
            // if the plugin file correct
            $info = self::_info($plugin);
            if ($info !== false):
                $plugins[$plugin] = array('state' => self::is_enabled($plugin));
                $plugins[$plugin] = array_merge($plugins[$plugin], $info);
            endif;
        }
        return $plugins;
    }


    /**
     * MyPlugins::enable()
     * enable an plugin
     * @param name $plugin
     * @return
     */
    static function enable($plugin)
    {
        $plugin = basename($plugin);
        if (self::_info($plugin) == false)
            return false;
        $all = self::_getTmp();
        $all[$plugin] = true;
        return self::_saveTmp($all);
    }


    /**
     * MyPlugins::disable()
     * disable an plugin
     * @param name $plugin
     * @return
     */
    static function disable($plugin)
    {
        $plugin = basename($plugin);
        $all = self::_getTmp();
        unset($all[$plugin]);
        return self::_saveTmp($all);
    }


    /**
     * MyPlugins::remove()
     * remove an plugin ['be sure']
     * @param name $plugin
     * @return
     */
    static function remove($plugin)
    {
        $plugin = basename($plugin);
        $pluginDir = self::$pluginsDir . $plugin . '/';
        // disable it
        self::disable($plugin);
        // if there is an remove.php file then call it
        (file_exists($pluginDir . 'remove.php')) ? include_once $pluginDir .
            'remove.php' : '';
        // remove it's directory
        return self::_deleteDir($pluginDir);
    }


    /**
     * MyPlugins::is_enabled()
     * check if plugin enabled or disabled
     * @param name $plugin
     * @return
     */
    static function is_enabled($plugin)
    {
        $plugin = basename($plugin);
        $all = self::_getTmp();
        return in_array($plugin, array_keys($all));
    }


    /**
     * MyPlugins::_deleteDir()
     * delete directory and it's content
     * @param directory $dir
     * @return
     */
    protected static function _deleteDir($dir)
    {
        // is the directory exists
        if (!file_exists($dir))
            return false;
        // parse all directory files
        $all = glob(rtrim($dir, '/') . '/*');
        // loop and check if the path is directory
        // then call _deleteDir again else is file then
        // unlink the file
        foreach ($all as $path) {
            if (is_dir($path))
                self::_deleteDir($path);
            elseif (is_file($path))
                unlink($path);
        }
        rmdir($dir);
    }


    /**
     * MyPlugins::_info()
     * get info f a plugin
     * @param name $plugin
     * @return
     */
    protected static function _info($plugin)
    {
        $plugin = basename($plugin);
        $pluginPath = self::$pluginsDir . $plugin . '/' . $plugin . '.' . self::$EXT;
        // check if the plugin exists
        if (!file_exists($pluginPath))
            return false; // plugin file not exists
        $content = file_get_contents($pluginPath);
        // check if header exists
        if (preg_match('#/\*(.*?)\*/#s', $content, $matches)) {
            $info = array();
            $header = trim($matches[1]);
            $x = explode(PHP_EOL, $header);
            // check if there is any info or return false
            if (is_array($x)) {
                foreach ($x as $y):
                    $z = explode(':', $y);
                    // check if the information is good formated [has :]
                    if (is_array($z)) {
                        // split info to $k , $v
                        list($k, $v) = $z;
                        $info[rtrim(ltrim(strtolower($k)))] = rtrim(ltrim(strtolower($v)));
                    } else // info not good formated

                        return false;
                endforeach;
            } else // no info in the header

                return false;
        } else // no header

            return false;
        // end finally :)
        return $info;
    }


    /**
     * MyPlugins::_getTmp()
     * get array of the tmp saved array
     * @return
     */
    protected static function _getTmp()
    {
        // get the tmp data
        $data = file_get_contents(self::$tmpFile);

        // check if there is an error in serialized data
        // then fix it if exists and create new data
        (!@unserialize($data)) ? $all = array() : $all = unserialize($data);

        // return the array
        return $all;
    }


    /**
     * MyPlugins::_saveTmp()
     * save array
     * @param mixed $data
     * @return
     */
    protected static function _saveTmp(array $data)
    {
        return (bool)file_put_contents(self::$tmpFile, serialize($data));
    }


    /**
     * MyPlugins::error()
     * custome error
     * @param mixed $msg
     * @return
     */
    protected static function error($msg)
    {
        $css = 'margin:auto;padding:15px;color:#555;border:1px solid #ccc;background:#f9f9f9;';
        return '<div style=\'' . $css . '\'> <b>' . __class__ . ' Error</b> : ' . $msg .
            '</div>';
    }


}
