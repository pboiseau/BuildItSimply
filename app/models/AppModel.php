<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class AppModel extends Eloquent{

	public $incrementing = true;

	private $salt = '4234ePc9M28eWyx9';

	/**
	*	Hash string using sha256 algorithm
	*	@param string $data
	*	@return string hashed
	**/
	public function hash($data){
		return hash('sha256', $this->salt . $data);
	}

	/**
	*	Get enumerate values from a table field
	*	Feature not supported in Eloquent ORM
	*	Need to use F3 DB for SQL request
	*	@param string $field
	*	@return array $values
	**/
	public function getEnumValues($field){
		$db = $this->getFFdb();
		$field = $db->exec("SHOW COLUMNS FROM " . $this->table . " WHERE Field = :field",
			[':field' => $field])[0];
		preg_match('/^enum\((.*)\)$/', $field['Type'], $matches);
		$values = array();
		foreach(explode(',', $matches[1]) as $value){
			$values[] = trim($value, "'");
		}
		return $values;
	}

	/**
	*	Return new F3 DB instance (using only if necessary)
	*	@return object $db
	**/
	private function getFFdb(){
		$f3 = Base::instance();
		$db = new DB\SQL(
			'mysql:host=' . $f3->get('DB_HOST') . ';port=3306;
			dbname=' . $f3->get('DB_NAME') . '',
			$f3->get('DB_USER'),
			$f3->get('DB_PASSWORD')
			);

		return (!empty($db)) ? $db : false;
	}

}