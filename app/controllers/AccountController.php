<?php

class AccountController extends AppController{

	public $uses = array('Account');

	public function __construct(){
		parent::__construct();
	}

	/**
	*	Authenticate and log client
	**/
	public function login(){
		if($this->request() == 'POST'){
			if($user = $this->Account->login($this->f3->get('POST'))){
				$user = [
					'firstname' => $user['firstname'],
					'lastname' => $user['lastname']
				];
				$this->f3->set('SESSION', $user);
				$this->setFlash('Authentification reussi');
				$this->f3->reroute('/');
			}else{
				$this->setFlash('erreur');
				$this->f3->reroute($this->f3->get('PATTERN'));
			}
		}

		$this->render('accounts/login');
	}

	public function logout(){

	}

	public function register(){
		if($this->request() == 'POST'){
			$this->Account->register($this->f3->get('POST'));
		}

		$this->render('accounts/register');
	}


}

 ?>