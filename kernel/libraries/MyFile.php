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
 * MyFile
 * 
 * @package PHPharo 2
 * @copyright 2013
 * @version 0.1
 */
class MyFile
{

    /**
     * MyFile::write()
     * Write data to a file
     * @param string $path
     * @param subject $data
     * @param int $flags
     * @return bool
     */
    public static function write($path, $data, $flags = null)
    {
        return (bool)file_put_contents($path, $data, (int)$flags);
    }

    /**
     * MyFile::append()
     * Append Data to a file
     * @param string $path
     * @param subject $data
     * @param int $flags
     * @return bool
     */
    public static function append($path, $data, $flags = null)
    {
        return (bool) self::write($path, file_get_contents($path) . $data, (int)$flags);
    }

    /**
     * MyFile::rename()
     * rename file or directory
     * @param oldPath $old
     * @param newPath $new
     * @return bool
     */
    public static function rename($old, $new)
    {
        return (bool) rename($old, $new);
    }

    /**
     * MyFile::read_dir()
     * read & list directory files / sub-directories
     * @param string $path
     * @return array
     */
    public static function read_dir($path)
    {
        $paths = array();
        if(is_dir($path)) {
            $dir = new RecursiveDirectoryIterator($path);
            $recursive = new RecursiveIteratorIterator($dir);
            if(empty($recursive)) return $path;
            foreach( $recursive as $k => $v )
            {
                $paths[] = str_replace('\\', '/', $k) ;
            }
        }
        return $paths;
    }
    
    /**
     * MyFile::copy()
     * copy file/directory to another destination
     * @param sourcePath $src
     * @param distinaionPath $dest
     * @return void
     */
    public static function copy($src, $dest)
    {
        if(is_file($src))
            return (bool) copy($src, $dest);
        elseif(is_dir($src)) {
            $src = rtrim($src, '/') . '/';
            $dest = rtrim($dest, '/') . '/';
            mkdir($dest . $src, 0777);
            $all = self::read_dir($src);
            if(!empty($all)):
                foreach($all as $file):
                    self::copy($file, $dest . $file);
                endforeach;
            endif;
        }
    }
    
    /**
     * MyFile::delete()
     * Delete file/folder "Be Aware"
     * @param string $path
     * @return bool
     */
    public static function delete($path)
    {
        if (is_file($path))
            return unlink($path);
        elseif (is_dir($path)) {
            $all = self::read_dir($path);
            if(!empty($all)):
                foreach($all as $file):
                    self::delete($file);
                endforeach;
            endif;
            return (bool)rmdir($path);
        }
    }
    
    /**
     * MyFile::move()
     * Move file/folder to new destination
     * @param sourcePath $src
     * @param destinationPath $dest
     * @return void
     */
    public static function move($src, $dest)
    {
        self::copy($src, $dest);
        self::delete($src);
    }
    
    /**
     * MyFile::zip()
     * Zip File / Directory
     * @param source $src
     * @param destinationPath $dest
     * @return void
     */
    public static function zip($src, $dest)
    {
       $zip = new ZipArchive;
       if(!$zip->open($dest, ZipArchive::CREATE)) {
            throw new Exception(__CLASS__ . '::Zip error unable to Write ZipArchive');
            exit(0);
       }
       else{
            if(is_file($src)) $zip->addFile($src);
            elseif(is_dir($src)) {
                $all = self::read_dir($src);
                if(!empty($all)):
                    foreach($all as $file):
                        $zip->addFile($file);
                    endforeach;
                endif;
            }
       }
       $zip->close();
    }
    
    /**
     * MyFile::read_zip()
     * read zip files
     * @param sourcePath $srcZip
     * @return array
     */
    public static function read_zip($srcZip)
    {
        $files = array();
        
        $zip = new ZipArchive;
        $zip->open($srcZip);
        for($a = 0; $a < $zip->numFiles; ++$a) {
            $name = $zip->statIndex($a);
            $files[] = $name['name'];
        }
        $zip->close();
        return $files;
    }
    
    /**
     * MyFile::unzip()
     * unzip ziped archive
     * @param source $srcZip
     * @param destination $dest
     * @return bool
     */
    public static function unzip($srcZip, $dest)
    {
        $zip = new ZipArchive;
        $zip->open($srcZip);
        $state = $zip->extractTo($dest);
        $zip->close();
        return (bool) $state;
    }
}
