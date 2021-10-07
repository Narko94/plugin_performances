<?php

class Files {
	public function __construct() {
		if(isset($_FILES))
			foreach($_FILES as $key => $file) {
				$this->$key = json_decode(json_encode($file, JSON_UNESCAPED_UNICODE));
			}
	}
	
	public function get() {
		return $this;
	}
	
	public function getToArray() {
		return get_object_vars($this);
	}
}