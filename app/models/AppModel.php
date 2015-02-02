<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class AppModel extends Eloquent{

	public $incrementing = true;

	private $salt = '4234ePc9M28eWyx9';

	public function hash($data){
		return hash('sha256', $this->salt . $data);
	}

}