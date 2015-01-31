<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class AppModel extends Eloquent{

	private $f3;

	protected $db;
	protected $mapper;

	public function __construct() {
		$this->f3 = Base::instance();
	}


}