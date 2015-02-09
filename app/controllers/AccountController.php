<?php

class AccountController extends AppController{

	public $uses = array('Account', 'Freelance');

	public function __construct(){
		parent::__construct();
	}

	public function beforeroute(){
	}

	public function afterroute(){
	}

	/**
	*	Authenticate and log client
	**/
	public function login(){
		if($this->request() == 'POST'){
			if($user = $this->Account->login($this->f3->get('POST'))){
				$this->Account->setSession($user);
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
	*	Logout client and destroy session
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
				if($user = $this->Account->register($user)){
					$this->Account->setSession($user);
					$this->setFlash('Votre compte a été crée et vous avez automatiquement été connecté.');
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

	/**
	*
	**/
	public function profile(){

		$user = $this->Account->find($this->f3->get('SESSION.user.id'));

		if($user['type'] == "FREELANCE"){
			$experiences = $this->Freelance->getEnumValues('experience');
		}

		$this->render('accounts/profile', compact(
			'user',
			(!empty($experiences) ? 'experiences' : ''))
		);
	}

	public function update_profile() {
		if($this->request() == 'POST'){
			$profile = $this->f3->get('POST');
			$profile['account_id'] = $this->f3->get('SESSION.user.id');
			$type = $this->f3->get('SESSION.user.type');

			if($type == 'FREELANCE'){
				if($this->Freelance->updateProfile($profile)){

				}else{
					$errors = $this->Freelance->errors;
				}
			}else if($type == 'CLIENT'){

			}
		}
	}


}

 ?>