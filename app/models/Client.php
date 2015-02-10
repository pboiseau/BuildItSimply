<?php

class Client extends AppModel {

	public $timestamps = true;
	public $errors;

	protected $table = 'clients';
	protected $guarded = array('created_at');


}

?>