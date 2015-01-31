<?php

class AppController {

	public $f3;

	protected $twig;
	protected $layout = 'default';

	public function __construct() {
		$this->f3 = Base::instance();
		$this->twig = $this->f3->get('TWIG');

		$this->config = [
			'root' => $this->f3->get('ROOT'),
			'webroot' => $this->f3->get('WEBROOT'),
			'css' => $this->f3->get('CSS'),
			'js' => $this->f3->get('JS'),
			'message' => $this->f3->get('SESSION.message'),
		];

		if(!empty($this->uses)){
			foreach($this->uses as $model){
				$this->loadModel($model);
			}
		}
	}

	protected function render($template, $data = array()){
		$data['layout'] = $this->layout;

		echo $this->twig->render($template . '.twig',
			array_merge($data, $this->config)
		);

		if($this->f3->get('SESSION.message')){
			$this->f3->set('SESSION.message', '');
		}
	}

	protected function request(){
		return $this->f3->get('VERB');
	}

	protected function setFlash($message){
		$this->f3->set('SESSION.message', $message);
	}

	private function loadModel($model){
		if(class_exists($model)){
			$this->$model = new $model();
		}else{
			throw new Exception("Class " . $model . " doesn't exist");
		}
	}

}

?>