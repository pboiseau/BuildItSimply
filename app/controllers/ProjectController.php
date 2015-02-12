<?php

class ProjectController extends AppController{

	public $uses = array('Project', 'Client');

	public function __construct()
	{
		parent::__construct();
	}

	public function init()
	{
		if($this->request() == "POST")
		{
			$project = $this->f3->get('POST');
			$project['client_id'] = $this->f3->get('SESSION.user.id');
			$this->Project->create($project);
		}

		$this->render('projects/init', []);
	}

	public function show()
	{

	}

	public function all()
	{
        $projects = $this->Project->client()->get();
        // $projects = $this->Project->all();

		var_dump($projects);

        $this->render('projects/all', compact('projects'));
	}

}

?>