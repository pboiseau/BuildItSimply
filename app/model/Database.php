<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class Database{

	private $settings = array();

	private $capsule;

	public function __construct() {
		$this->initSettings();
		$this->capsule = new Capsule;
		$this->capsule->addConnection($this->settings);
		$this->capsule->bootEloquent();

		return $this->capsule;
	}

	private function initSettings() {
		$f3 = \Base::instance();
		$this->settings = [
			'driver' => 'mysql',
			'host' => $f3->get('DB_HOST'),
			'database' => $f3->get('DB_NAME'),
			'username' => $f3->get('DB_USER'),
			'password' => $f3->get('DB_PASS'),
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => ''
		];
	}
}