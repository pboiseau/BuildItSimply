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
		$this->getEnumValues("accounts", "type");
		die();

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


}

 ?>