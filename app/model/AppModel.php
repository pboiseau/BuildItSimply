<?php

class AppModel {

	protected $db;

	function __construct() {
		$this->db = new DB\SQL(
			'mysql:host=localhost;port=3306;dbname=buildmyproject',
			'root', 'root'
		);
	}



}