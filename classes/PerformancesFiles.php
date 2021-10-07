<?php
if(!defined('IN_CMS')) exit;

class PerformancesFiles {
    public $files;
    public $dir;
    public $errors  = [];
    public $is_dir  = false;
    public $rmdir   = false;

    public function __construct($dir) {
    	if($dir)
            $this->dir = $dir;
    }

    public function getDirFile() {
        if(empty($this->dir)) {
	        $this->errors[] = __FUNCTION__ . ' -> не задан параметр директории';
	        return $this;
        }
        if(!is_dir($this->dir))
            if(!mkdir($this->dir, 0755, true)) {
	            $this->errors[] = __FUNCTION__ . ' -> не удалось создать директорию: [' . $this->dir . ']';
	            return $this;
            }
        $this->files = array_slice(scandir($this->dir), 2);
        
        return $this;
    }

    public function getDir() {
        $this->is_dir = is_dir($this->dir);
        
        return $this;
    }

    public function delDir() {
        if(!is_dir($this->dir)) {
	        $this->errors[] = __FUNCTION__ . ' -> директория не найдена';
	        return $this;
        }
        $this->getDirFile();
        if(!empty($this->files)) {
            foreach($this->files as $item) {
                unlink($this->dir . DS . $item);
            }
        }
        $this->rmdir = rmdir($this->dir);
        
        return $this;
    }
}