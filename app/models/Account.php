<?php

class Account extends AppModel{

	public $timestamps = false;

	protected $table = 'accounts';
	protected $guarded = array('id');
	protected $fillable = array('firstname', 'lastname', 'mail', 'password');

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

	public function register($user){
		$this->create($user);
	}


}

 ?>