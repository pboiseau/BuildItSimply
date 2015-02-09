<?php

class Skill extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'skills';
	protected $guarded = array('id');

	/**
	*	@param string skills
	**/
	private function explodeSkills($skills) {

	}
}

 ?>