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
				$this->f3->set('SESSION.user', $user);
				$this->setFlash('Authentification reussi');
				$this->f3->reroute('/');
			}else{
				$this->setFlash('erreur');
				$this->f3->reroute($this->f3->get('PATTERN'));
			}
		}

		$this->render('accounts/login');
	}

	/**
	*
	**/
	public function logout(){
		$this->f3->clear('SESSION');
		$this->f3->reroute('/users/login');
	}

	/**
	*	Register a client using form and post data
	**/
	public function register(){
		$user = array();
		$errors = array();

		if($this->request() == 'POST'){
			$user = $this->f3->get('POST');
			if(strcmp($user['password'], $user['repeatpassword']) == 0){
				if($this->Account->register($user)){
					$this->setFlash('Votre compte a été crée, bienvenue');
					$this->f3->reroute('/users/profile');
				}else{
					$errors = $this->Account->errors;
				}
			}else{
				$this->setFlash("Les mots de passe ne correspondent pas");
				// $this->f3->reroute($this->f3->get('PATTERN'));
			}
		}

		$type = $this->Account->getEnumValues('type');
		$this->render('accounts/register', compact('user', 'errors', 'type'));
	}

	public function profile(){
		$this->render('accounts/profile', []);
	}

}

 ?>