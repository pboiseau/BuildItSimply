<?php

class FreelanceSkill extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'freelances_skills';
    	protected $fillable = array('account_id', 'skill_id');

	public function freelance(){
		return $this->hasOne('Account', 'account_id');
	}

	public function add($skills = array()){
		$f3 = Base::instance();

		foreach ($skills as $key => $skill) {
			// check if skill already exist
			$actual_skill = $this->where('account_id', $f3->get('SESSION.user.id'))
				->where('skill_id', $skill->id)->first();

			if(empty($actual_skill)){
				$this->create([
					'account_id' => $f3->get('SESSION.user.id'),
					'skill_id' => $skill->id
				]);
			}
		}
	}


}

 ?>