<?php

class HomeController extends AppController{

	public $uses = array('Account');

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->render('index');
	}

	public function howItWorks()
	{
		$this->render('howitworks');
	}

}

?>
