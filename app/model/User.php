<?php

class User extends AppModel{

	private $table = 'users';
	private $data;

	public function __construct(){
		parent::__construct();

		// $this->mapper = new DB\SQL\Mapper($this->db, $this->table);
		// $this->data = new \DB\Cortex($this->db, $this->table);
	}


}

 ?>