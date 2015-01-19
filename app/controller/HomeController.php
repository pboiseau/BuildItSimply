<?php

class HomeController extends AppController{

	public $uses = array('User');

	function __construct() {
		parent::__construct();
	}

	public function index($f3){
		$f3->set('content', 'app/view/test.htm');

		echo View::instance()->render('app/view/layout/default.htm');
		// echo Template::instance()->render('app/view/layout/default.htm');
	}

}

?>
