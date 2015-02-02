<?php

class Account extends AppModel {

	public $timestamps = false;

	protected $table = 'accounts';
	protected $guarded = array('id');
	// protected $fillable = array();

	public function get(){
		return $this->all();
	}

	public function login($login = array()){
		$user = $this->where('mail', $login['email'])
				->where('password', $login['password'])
				->first();

		return (!empty($user)) ? $user : false;
	}

	public function register($user){
		unset($user['repeatpassword']);
		$user['password'] = $this->hash($user['password']);
		$this->create($user);

	}


}

 ?>