<?php

class Account extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'accounts';
	protected $guarded = array('id');


	public function get(){
		return $this->all();
	}

	/**
	*
	**/
	public function login($login = array()){
		$user = $this->where('mail', $login['email'])
				->where('password', $this->hash($login['password']))
				->first();

		return (!empty($user)) ? $user : false;
	}

	/**
	*	Register a new account
	*	@param array $user
	**/
	public function register($user){
		unset($user['repeatpassword']);
		if($this->validate($user)){
			$user['password'] = $this->hash($user['password']);
			return $this->create($user);
		}else{
			return false;
		}
	}

	private function validate($data = array()){
		$validator = new Validate();
		$errors = array();

		if(!$validator->email($data['mail'])){
			$errors['mail'] = 'Email invalide ou faux.';
		}

		if(!$validator->isString($data['lastname'], 100)){
			$errors['lastname'] = 'Nom invalide.';
		}

		if(!$validator->isString($data['firstname'], 100)){
			$errors['lastname'] = 'Prenom invalide.';
		}

		if(!$validator->isPassword($data['password'], 15, 25)){
			$errors['password'] = "Votre mot de passe doit faire entre 8 et 25 caractères.";
		}

		$this->errors = $errors;
		return (empty($errors)) ? true : false;
	}


}

 ?>