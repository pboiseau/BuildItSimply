<?php

class Skill extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'skills';
	protected $guarded = array('id');

	/**
	*	@param string skills
	**/
	public function explodeSkills($request_skills) {
		$request_skills = explode(', ', $request_skills);
		$skills = $this->whereIn('name', $request_skills)->get();
		return $skills;
	}
}

 ?>