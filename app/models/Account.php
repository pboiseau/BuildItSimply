<?php

class Account extends AppModel{

	public $timestamps = false;
	protected $table = 'accounts';

	public function __construct(){
		parent::__construct();
	}

	public function get(){
		return $this->all();
	}

	public function login($login = array()){
		$user = $this->where('mail', $login['email'])
				->where('password', $login['password'])
				->first();

		return (!empty($user)) ? $user : false;
	}


}

 ?>