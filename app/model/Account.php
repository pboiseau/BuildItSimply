<?php

class Account extends AppModel{

	public $table = 'accounts';

	public function __construct(){
		parent::__construct();
	}

	public function get(){
		return $this->mapper->load();
	}


}

 ?>