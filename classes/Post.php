<?php

class Post {
	public function __construct() {
		if(isset($_POST['params']))
			foreach($_POST['params'] as $key => $post) {
				$this->$key = $post;
			}
	}
	
	public function get() {
		return $this;
	}
	
	public function getToArray() {
		return get_object_vars($this);
	}
}