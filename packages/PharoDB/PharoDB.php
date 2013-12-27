<?php


class PharoDB
{
    const DS = DIRECTORY_SEPARATOR;
    
    
    function group_create($name){}
    function group_exists($name){}
    function group_rename($old_name, $new_name){}
    function group_get(){}
    function group_delete(){}
    
    
    function entry_get(){}
    function entry_create(){}
    function entry_exists(){}
    function entry_rename(){}
    function entry_update(){}
    function entry_delete(){}
    
    function _write($type = 'file', $filename, $replace = true, $data = null)
    {
        if(strtolower($type) === 'file')
            if(file_exists($filename))
                if($replace === true)
                    return (bool)file_put_contents($filename, $data);
                else
                    return true;
            else
                return (bool)file_put_contents($filename, $data);
        else
            @mkdir($filename, 0777);
    }
    
    function _rename($old_path, $new_name)
    {
        $old_path = realpath($old_path);
        if(!$old_path) return false;
        return (bool)rename($old_path, realpath(dirname($old_path)) . self::DS . basename($new_name));
    }
     
    function _read($filename, $dir_pattern = '.*', $dir_flags = null)
    {
        $filename = realpath($filename);
        if(!$filename) return false;
        if(file_exists($filename)):
            if(is_file($filename)) {
                return file_get_contents($filename);
            } else if(is_dir($filename)) {
                return (array)glob($filename . self::DS . $dir_pattern, $dir_flags);
            }
        endif;
        return false;
    }
    
    function _delete($filename)
    {
        $filename = realpath($filename);
        if(!$filename) return false;
        if(file_exists($filename)):
            if(is_file($filename)) return (bool)unlink($filename);
            elseif(is_dir($filename)) {
                foreach(scandir($filename) as $file):
                    if($file !== '.' and $file !== '..'):
                        $file = realpath($filename . self::DS . $file);
                        if(is_file($file)) $result = (bool)unlink($file);
                        elseif(is_dir($file)) { 
                            $this->_delete($file);
                            @rmdir($file);
                        }
                    endif;
                endforeach;
                @rmdir($filename);
                unset($file);
                return (bool)(file_exists($filename) === false);
            }
        endif;
    }
    
}