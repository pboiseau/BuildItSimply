<?php

class HomeController extends AppController{

	public $uses = array('Account');

	public function __construct() {
		parent::__construct();
	}

	public function index($f3){
		$this->render('index');
	}

}

?>
