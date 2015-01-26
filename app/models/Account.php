<?php

class Account extends AppModel{

	public $timestamps = false;
	protected $table = 'accounts';

	public function __construct(){
		parent::__construct();
	}

	public function get(){
		return $this->all();
	}


}

 ?>