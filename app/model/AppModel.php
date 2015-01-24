<?php

class AppModel {

	protected $db;
	private $f3;

	public function __construct() {
		$this->f3 = Base::instance();

		$this->db = new DB\SQL(
			'mysql:host=' . $this->f3->get('DB_HOST') . ';port=3306;dbname=' . $this->f3->get('DB_NAME'),
			$this->f3->get('DB_USER'), $this->f3->get('DB_PASSWORD')
		);

	}



}