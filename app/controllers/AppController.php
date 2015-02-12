<?php

class AppController {

	public $f3;

	protected $twig;
	protected $layout = 'default';

	public function __construct()
	{
		$this->f3 = Base::instance();
		$this->twig = $this->f3->get('TWIG');

		$this->config = [
			'prod' => $this->f3->get('PROD'),
			'root' => ($this->f3->get('PROD')) ? $this->f3->get('ROOT') : $this->f3->get('DEV_ROOT'),
			'home' => ($this->f3->get('PROD')) ? $this->f3->get('ROOT') : $this->f3->get('DEV_ROOT') . '/',
			'webroot' => $this->f3->get('WEBROOT'),
			'css' => $this->f3->get('CSS'),
			'js' => $this->f3->get('JS'),
			'request' => substr($this->f3->get('PATTERN'), 1, strlen($this->f3->get('PATTERN'))),
			'message' => $this->f3->get('SESSION.message'),
			'login' => $this->f3->get('SESSION.user'),
		];

		if(!empty($this->uses))
		{
			foreach($this->uses as $model){
				$this->loadModel($model);
			}
		}

	}

	/**
	*	FatFree before route trigger
	**/
	public function beforeroute()
	{
		if(!$this->f3->get('SESSION.user')){
			if (!in_array($this->f3->get('PATTERN'), ['/', '/howitworks', '/users/login', '/users/register'])) {
				$this->setFlash("Vous devez vous authentifier pour effectuer cette action.");
				$this->f3->reroute('/');
			}
		}
	}

	/**
	*	Render a view using twig template
	*	@param string $template
	*	@param array $data
	**/
	protected function render($template = null, $data = array())
	{
		$data['layout'] = $this->layout;

		echo $this->twig->render($template . '.twig',
			array_merge($data, $this->config)
			);

		if($this->f3->get('SESSION.message')){
			$this->f3->set('SESSION.message', '');
		}
	}

	/**
	*	Get the request type (get, post ...)
	*	@return string request type
	**/
	protected function request()
	{
		return $this->f3->get('VERB');
	}

	/**
	*	Set flash message into user session
	*	@param string $message
	**/
	protected function setFlash($message)
	{
		$this->f3->set('SESSION.message', $message);
	}


	/**
	*	Data encode into JSON
	*	@param $name
	*	@param $data
	**/
	protected function encode($name, $data = array())
	{
		header('Access-Control-Allow-Origin: *');
		header('Acces-Control-Allow-Headers: Auth-Token');
		header('Access-Control-Allow-Methods: *');
		header('Content-Type: application/json');
		return '{"' . $name . '": ' . json_encode($data, CASE_LOWER) . '}';
	}

	/**
	*	Instanciate and load a database model
	*	@param string $model name
	**/
	private function loadModel($model)
	{
		if(class_exists($model)){
			$this->$model = new $model();
		}else{
			throw new Exception("Class " . $model . " doesn't exist");
		}
	}

	private function twigExtention()
	{
		$this->twig->addFunction(new \Twig_SimpleFunction('javascript', function ($file) {
			echo sprintf("<script src='/%s'></script>", $this->f3->get('JS') . $file);
		}));
	}

}

?>