<?php

class SkillController extends AppController{

	public $uses = array('Skill');

	public function getAll(){
		$skills = $this->Skill->all();
		echo $this->encode('skills', $skills);
	}


}

?>