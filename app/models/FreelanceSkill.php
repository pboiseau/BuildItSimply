<?php

class FreelanceSkill extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'freelance_skills';
	protected $guarded = array('created_at');

	public function freelance(){
		return $this->hasOne('Account', 'account_id');
	}

	public function add($skills = array()){
		var_dump($skills);
	}


}

 ?>