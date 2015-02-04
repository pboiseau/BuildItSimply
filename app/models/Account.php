<?php

class Account extends AppModel {

	public $timestamps = true;
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
		$user['password'] = $this->hash($user['password']);
		$this->create($user);
	}

	private function validate($data = array()){
		$validator = new Validate();
		$errors = array();

		if(!$validator->email($data['mail'])){
			$errors['mail'] = 'Email invalide ou faux';
		}

		return (empty($errors)) ? true : $errors;
	}


}

 ?>