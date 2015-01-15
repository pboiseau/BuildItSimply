<?php

class HomeController extends AppController{

	public function __construct() {
		parent::__construct();
	}

	public function index(){
		echo 'we are in index';
	}

}

?>
