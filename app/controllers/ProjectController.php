<?php

class ProjectController extends AppController{

	public $uses = array('Project', 'Client');

	public function __construct()
	{
		parent::__construct();
	}

    /**
     *
     */
    public function init()
	{
		if($this->request() == "POST")
		{
			$project = $this->f3->get('POST');
			$this->Project->initialize($project);
		}

		$this->render('projects/init', []);
	}

    /**
     *
     */
    public function show()
	{
        $project = $this->Project->show($this->f3->get('PARAMS.id'));
        var_dump($project);
	}

    /**
     *
     */
    public function all()
    {
        $projects = $this->Project->all();
        foreach($projects as $key => $project){
            $projects[$key]['client'] = $project->account()->first();
        }
        $this->render('projects/all', compact('projects'));
	}

}

?>