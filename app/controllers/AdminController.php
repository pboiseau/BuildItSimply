<?php

class AdminController extends AppController
{
    public $uses = array('Project', 'ProjectType', 'ProjectQuestion', 'ProjectStep', 'ProjectResponse');

    public $layout = 'admin';

    public function __construct()
    {
        parent::__construct();
    }

    public function projectType()
    {
        if ($this->request() == "POST") {
            ProjectType::create($this->f3->get('POST'));
            $this->setFlash("Le type de project a bien été crée");
            $this->f3->reroute($this->f3->get('PATTERN'));
        }

        $types = ProjectType::all();

        $this->render('admin/projects/type', compact('types'));
    }

    public function projectStep()
    {
        $this->render('admin/projects/step', []);
    }

    public function projectQuestion()
    {
        $error = array();

        if ($this->request() == "POST") {
            $request = $this->f3->get('POST');


            if ($this->ProjectQuestion->validate($request)) {
                $question = ProjectQuestion::create([
                    'question' => $request['question'],
                    'description' => $request['description']
                ]);

                foreach ($request['response'] as $key => $response) {
                    if ($this->ProjectResponse->validate($response)) {
                        ProjectResponse::create([
                            'response' => $response['response'],
                            'description' => $response['description'],
                            'question_id' => $question->id
                        ]);
                    }
                }

            } else {
                $error = $this->ProjectQuestion->errors;
            }


            if (!$this->ProjectStep->exists($request['project_type'], $request['step'])) {
                ProjectStep::create([
                    'project_type_id' => $request['project_type'],
                    'project_question_id' => $question->id,
                    'step' => $request['step']
                ]);
            }

            $this->setFlash("La question a bien été ajouté");
            $this->f3->reroute($this->f3->get('PATTERN'));
        }

        $types = ProjectType::all(array('id', 'type'));

        $this->render('admin/projects/question', compact('types', 'error'));
    }

    public function projectResponse()
    {
        $this->render('admin/projects/response', []);
    }


}