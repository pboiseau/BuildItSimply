<?php

class Freelance extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'freelances';
	protected $guarded = array('created_at');

	public function updateProfile($freelance){
		unset($freelance['skills']);
		if(!$this->validate($freelance)){ return false; }

		if($profile = $this->where('account_id', $freelance['account_id'])->get()){
			// update freelance
			$this->where('account_id', $freelance['account_id'])->update([
					'url' => $freelance['url'],
					'experience' => $freelance['experience'],
				]);
		}else {
			// create freelance
			$create = $this->create($freelance);
			return (!empty($create)) ? $create : false;
		}
	}

	/**
	*	Check if data are valide
	*	@param array data
	**/
	private function validate($data = array()){
		$validator = new Validate();
		$errors = array();

		if(!$validator->url($data['url'])){
			$errors['url'] = "L'adresse web est incorrect";
		}

		if(!in_array($data['experience'], ['BEGINNER', 'CONFIRMED', 'EXPERT'])){
			$errors['experience'] = "Votre niveau d'experience est incorrect";
		}

		$this->errors = $errors;
		return (empty($errors)) ? true : false;
	}

}

?>