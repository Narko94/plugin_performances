<?php
if(!defined('IN_CMS')) exit;

class PerformancesFile extends PerformancesFiles {
    public $full;
    public $content;
    public $name;
    public $type        = '.json';
    public $checkFile   = false;
    public $createFile  = false;
    public $deleteFile  = false;
    public $saveData    = false;

    public function __construct($data = []) {
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                if(!property_exists($this, $key)) continue;
                $this->$key = $value;
            }
            
            $this->full = $this->dir . DS . $this->name . '.' . $this->type;
        }
    }

    public function updateFull() {
        $this->full = $this->dir . DS . $this->name . '.' . $this->type;
        
        return $this;
    }
    
    public function setFull($full) {
    	$this->full = $full;
    	
    	return $this;
    }
    
    public function setContent($content) {
    	$this->content = $content;
    	
    	return $this;
    }

    public function __check_file() {
        if(!$this->getDir()->is_dir) {
        	$this->errors[] = __FUNCTION__ . ' -> не найдена директория -> ' . $this->dir;
	        $this->checkFile = false;
        	
        	return $this;
        }
        
        if(empty($this->name)) {
	        $this->errors[] = __FUNCTION__ . ' -> имя файла не задано -> ' . $this->name;
	        $this->checkFile = false;
	
	        return $this;
        }
	
	    if(!is_writable($this->dir)) {
		    $this->errors[] = __FUNCTION__ . ' -> директория не доступна для записи ->' . $this->dir;
		    $this->checkFile = false;
		
		    return $this;
	    }
	
	    if(!is_file($this->dir . $this->name . $this->type)) {
		    $this->errors[] = __FUNCTION__ . ' -> файл не найден -> ' . $this->dir . $this->name . $this->type;
		    $this->checkFile = false;
		
		    return $this;
	    }
	
	    if(!is_writable($this->dir . $this->name . $this->type)) {
		    $this->errors[] = __FUNCTION__ . ' -> файл не доступен для записи -> ' . $this->dir . $this->name . $this->type;
		    $this->checkFile = false;
		
		    return $this;
	    }
	    $this->checkFile = true;
        
        return $this;
    }

    public function getContent($no_create = false) {
        if(!$this->__check_file()->checkFile)
            if($no_create === false) {
                if(!$this->createFile()->createFile) {
                	$this->errors[] = __FUNCTION__ . ' -> ошибка создания файла';
                	
                	return $this;
                }
            } else {
            	$this->errors[] = __FUNCTION__ . ' -> отсуствует файл, создайте его либо же передайте в getContent(true)';
            	
            	return $this;
            }

        $this->content = @file_get_contents($this->full);
        
        return $this;
    }

    public function createFile($mode = 'x+') {
    	if(file_exists($this->full)) {
		    print '[     УДАЛЕНИЕ    ] ' . $this->full . PHP_EOL;
		    $this->deleteFile();
	    }
    	
    	print '[    СОХРАНЕНИЕ   ] ' . $this->full . PHP_EOL;
        $fp = fopen($this->full, $mode);
        if(!$fp) {
        	$this->createFile = false;
        	
        	return $this;
        }
        fwrite($fp, $this->content);
        fclose($fp);
        $this->createFile = true;
	
	    print '[     СОХРАНЕН    ] ' . $this->full . PHP_EOL;
        
        return $this;
    }

    public function deleteFile() {
        if(!$this->__check_file()->checkFile) {
        	$this->errors[] = __FUNCTION__ . ' -> файл не найден';
        	
	        return $this;
        }
        
        $this->deleteFile = unlink($this->full);
        
        return $this;
    }

    public function saveData() {
	    $this->getDirFile();
	    
        if(!$this->createFile('w')->createFile) {
        	$this->errors[] = __FUNCTION__ . ' -> ошибка записи';
        }
        
        return $this;
    }
}