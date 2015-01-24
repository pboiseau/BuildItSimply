<?php

class AppController {

	protected $twig;
	protected $layout = 'default';

	public function __construct() {

		$this->twig = $GLOBALS['twig'];

		if(!empty($this->uses)){
			foreach($this->uses as $model){
				$this->loadModel($model);
			}
		}

	}

	protected function render($template, $data = array()){
		$data['layout'] = $this->layout;
		echo $this->twig->render($template . '.twig', $data);
	}

	private function loadModel($model){
		$this->$model = new $model();
	}


}

?>