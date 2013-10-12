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
 * files , directories wrapper
 * @package PHPharo
 * @author mohammed abdullah alashaal
 * @copyright 2013
 * @version 0.2
 * @access public
 */
class MyFile
{
    
    
    /**
     * MyFile::read()
     * Read File Or Directory
     * @param string $path
     * @return bool
     */
    public static function read($path)
    {
        if(!file_exists($path))
            die(self::error("read: The Path '{$path}' Is Not Exists"));
        
        if(is_file($path))
            return file_get_contents($path);
            
        elseif(is_dir($path))
            return self::_read_dir($path);
    }
    
    /**
     * MyFile::write()
     * Write data to file
     * @param string $path
     * @param string $data
     * @return bool
     */
    public static function write($path, $data)
    {
        return (bool) file_put_contents($path, $data);
    }
    
    /**
     * MyFile::append()
     * append data to file at 
     * the end/start of it
     * @param mixed $path
     * @param mixed $data
     * @param string $where
     * @return bool
     */
    public static function append($path, $data, $where = 'end')
    {
        if(!file_exists($path)) file_put_contents($path, '');
        
        $data_end = (string) (file_get_contents($path) . $data);
        $data_start = (string) ($data . file_get_contents($path));
        
        return (bool)(($where == 'start')
                       ? file_put_contents($path, $data_start)
                       : file_put_contents($path, $data_end));
    }
    
    /**
     * MyFile::delete()
     * Delete File Or Directory
     * @param string $path
     * @return bool
     */
    public static function delete($path)
    {
        if(!file_exists($path))
            die(self::error('delete: the path is not found ("'.$path.'")'));
            
        if(is_file($path))
            return unlink($path);
            
        $result = false;
        $all = self::read($path);
        $all = array_merge($all, array($path));
        
        foreach($all as $file)
            $result = self::_rm($file);
        
        return $result;
    }
    
    
    /**
     * MyFile::copy()
     * Copy file or directory
     * @param string $old_path
     * @param string $new_path
     * @return bool
     */
    public static function copy($old_path, $new_path)
    {
        if(is_file($old_path))
            return copy($old_path, $new_path);
        
        elseif(is_dir($old_path))
            return self::_copy_dir($old_path, $new_path);
        
    }
    
    /**
     * MyFile::move()
     * move file or direcotry
     * @param string $old_path
     * @param string $new_path
     * @return bool
     */
    public static function move($old_path, $new_path)
    {
        $result = self::copy($old_path, $new_path);
        $result = self::delete($old_path);
        return $result;
    }
    
    /**
     * MyFile::rename()
     * rename file or directory
     * @param string $oldname
     * @param string $newname
     * @return bool
     */
    public static function rename($oldname, $newname)
    {
        return (bool) rename($oldname, $newname);
    }
    
    /**
     * MyFile::info()
     * get information about file/directory
     * @param string $path
     * @return array
     */
    public static function info($path)
    {
        clearstatcache();
        $info = array();
        $info['name'] = basename($path);
        $info['type'] = (is_dir($path)) ? 'dir' : 'file';
        $info['dir'] = dirname($path);
        $info['size'] = number_format(self::_filesize($path) / (1024 * 1024),2);
        $info['size_type'] = 'mb';
        if(is_dir($path))
            $info['contains'] = self::_dir_contents($path);
        $info['mtime'] = filemtime($path);
        $info['atime'] = fileatime($path);
        $info['perms'] = substr(decoct(fileperms($path)),1);
        $info['ctime'] = filectime($path);
        $info['uid'] = fileowner($path);
        $info['gid'] = filegroup($path);
        $info['inode'] = fileinode($path);
        return $info;
    }
    
    /**
     * MyFile::zip()
     * zip file or directory 
     * @param string $src
     * @param string $dst
     * @return bool
     */
    public static function zip($src, $dst)
    {
        $zip = new ZipArchive;
        if(!$zip->open($dst, ZIPARCHIVE::CREATE)) return false;
        $result = false;
        if(is_file($src)) $result = $zip->addFile($src);
        elseif(is_dir($src)){
            $all = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src));
            foreach($all as $k => $v)
                $result = $zip->addFile($v);
        }
        $zip->close();
        return $result;
    }
    
    /**
     * MyFile::unzip()
     * unzip a zip file
     * @param string $src
     * @param string $dst
     * @return bool
     */
    public static function unzip($src, $dst)
    {
        @mkdir($dst,'0777');
        $zip = new ZipArchive;
        if(!$zip->open($src)) return false;
        $result = (bool) $zip->extractTo($dst);
        $zip->close();
        return $result;
    }
    
    /**
     * MyFile::upload()
     * upload files
     * @param array $config
     * @return array / void 'if $_FILES is not set'
     */
    public static function upload(array $config = array())
    {
        if(isset($_FILES)):
        
            if(!isset($config['name'])) $config['name'] = 'file';
            if(!isset($config['upload_to'])) die(self::error('upload: no `upload_to` set to save files '));
            if(isset($config['save_path'])) $config['upload_to'] = $config['save_path'];
            if(!isset($config['blocked_ext'])) $config['blocked_ext'] = array();
            if(!isset($config['blocked_mime'])) $config['blocked_mime'] = array();
            
            if(!isset($_FILES[$config['name']])) return '';
            
            $info = self::_upload_optimize_info($config['name']);
            $state = array();
            
            foreach((array) $info as $i):
                
                // (1)- extension checker
                if(in_array($i['ext'], $config['blocked_ext'])) $i['error'] = self::_upload_error('5');
                
                // (2)- mime-type checker
                elseif(in_array($i['type'], $config['blocked_mime'])) $i['error'] = self::_upload_error('9');
                
                // there is error ?
                if($i['error'] !== '') $state[$i['id']] = $i['name'] . ' => ' . $i['error'];
                // no ? then let's do last check
                else {
                    
                    $s = self::move($i['tmp'], $config['upload_to'] . '/' . $i['name']);
                    $state[$i['id']] = $i['name'] . ' => ' . (($s) ? 'File Successfully Uploaded' : ' Cannot Move The File');
                }
            endforeach;
            return $state;
        endif;
    }
    
    /* ------------------------------------------------------------------- */
    protected static function _upload_optimize_info($name)
    {
        if(isset($_FILES)):

            $files = $_FILES[$name];
            $info = array();
            for( $a=0; $a<count($files['name']); ++$a ):
                if(!strpos($files['name'][$a], '.')) $ext = '';
                else {$x = explode('.', $files['name'][$a]); $ext = end($x);}
                $info[$a] = array(
                                'id' => $a,
                                'name' => $files['name'][$a],
                                'size' => $files['size'][$a],
                                'type' => $files['type'][$a],
                                'ext' => $ext,
                                'error' => self::_upload_error($files['error'][$a]),
                                'tmp' => $files['tmp_name'][$a]
                                );
                
            endfor;
            return $info;
        endif;
    }
    
    protected static function _upload_error($num)
    {
        $array = array('0' => '',
                       '1' => 'file size exceeded max limit',
                       '2' => 'file size exceeded max limit',
                       '3' => 'upload not completed',
                       '4' => 'no file uploaded',
                       '5' => 'file extension is not allowed', // my custom error
                       '6' => 'no temp directory',
                       '7' => 'cannot write to disk',
                       '8' => 'un-known extension stopped uploading the file',
                       '9' => 'file type is not allowed');
        if(isset($array[$num])) return $array[$num];
        else return 'un-known error occured';
    }
    
    protected static function _dir_contents($path)
    {
        $all = self::read($path);
        $files = 0;
        $dirs = 0;
        foreach($all as $p)
            if(is_dir($p)) ++$dirs;
            else ++$files;
        return array('files' => $files, 'dirs' => $dirs);
    }
    
    protected static function _filesize($path)
    {
        if(is_file($path)) return filesize($path);
        else
            if(is_dir($path)):
                $size = 0;
                foreach( self::read($path) as $file )
                    $size = filesize($file) + $size;
                return $size;
            endif;
    }
    
    /**
     * MyFile::_copy_dir()
     * 
     * @param mixed $dir
     * @param mixed $dst
     * @return
     */
    protected static function _copy_dir($dir, $dst)
    {
        $all = glob($dir . '/*');
        @mkdir($dst, 0777);
        $dst = $dst . '/' . basename($dir);
        $result = false;
        @mkdir($dst, 0777);
        foreach( $all as $file ):
            
            if(is_dir($file)) $result = self::_copy_dir($file, $dst );
            elseif(is_file($file)) $result = copy($file, $dst . '/' . basename($file));
            
        endforeach;
        return $result;
    }
    
    /**
     * MyFile::_rm()
     * 
     * @param mixed $path
     * @return
     */
    protected static function _rm($path)
    {
        if(is_file($path))
            return unlink($path);
        elseif(is_dir($path))
            return rmdir($path);
    }
    
    /**
     * MyFile::_read_dir()
     * 
     * @param mixed $dir
     * @return
     */
    protected static function _read_dir($dir)
    {
        $files = array();
        $all  = glob($dir . '/*');
        foreach($all as $path):
            if(is_dir($path)):
                $files = array_merge($files, self::_read_dir($path));
            endif;
        $files[] = preg_replace('~/+~i','/',$path);
        endforeach;
        return $files;
    }
    
    /**
     * MyFile::error()
     * 
     * @param mixed $err_str
     * @return
     */
    protected static function error($err_str)
    {
        echo(PHPharo::Error(__CLASS__ . ' Error: ' . $err_str));
    }
    
}