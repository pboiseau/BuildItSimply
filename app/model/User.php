<?php

class User extends AppModel{

	private $table = 'users';
	private $mapper;

	function __construct(){
		parent::__construct();
		$this->mapper = new DB\SQL\Mapper($this->db, $this->table);
	}


}

 ?>