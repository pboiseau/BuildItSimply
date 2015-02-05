<?php

class Freelance extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'freelances';
	protected $guarded = array('account_id');

}

?>