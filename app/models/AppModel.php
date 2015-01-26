<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class AppModel extends Eloquent{

	private $f3;

	protected $db;
	protected $mapper;

	public function __construct() {
		$this->f3 = Base::instance();

		// $this->db = new DB\SQL(
		// 	'mysql:host=' . $this->f3->get('DB_HOST') . ';port=3306;dbname=' . $this->f3->get('DB_NAME'),
		// 	$this->f3->get('DB_USER'), $this->f3->get('DB_PASSWORD')
		// );

		// $this->mapper = new \DB\Cortex($this->db, $this->table);
	}


}