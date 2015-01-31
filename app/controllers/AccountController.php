<?php

class AccountController extends AppController{

	public $uses = array('Account');

	public function __construct(){
		parent::__construct();
	}

	public function login(){
		if($this->request() == 'POST'){
			echo 'coucou anastasia';
			die();
		}

		$this->render('accounts/login');
	}

	public function logout(){

	}

	public function register(){
		$this->render('accounts/login');
	}


}

 ?>