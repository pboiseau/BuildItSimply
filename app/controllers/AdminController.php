<?php

class AdminController extends AppController
{
    public $uses = array('Project', 'ProjectType', 'ProjectQuestion', 'ProjectStep', 'ProjectResponse');

    public $layout = 'admin';

    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        $this->render('admin/');
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

        if ($this->request() == "POST") 
        {
            $request = $this->f3->get('POST');
            var_dump($request);
            die();

        }

        $types = ProjectType::all(array('id', 'type'));
        $steps = ProjectStep::where('project_question_id', '>', 0)
            ->join('project_question', 'project_step.project_question_id', '=', 'project_question.id')
            ->orderBy('project_step.step', 'asc')
            ->get();

        $this->render('admin/projects/step', compact('steps', 'types'));


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

                $this->addResponses($request, $question->id);

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

            $this->setFlash("La question a bien été ajouté  ");
            $this->f3->reroute($this->f3->get('PATTERN'));
        }

        $types = ProjectType::all(array('id', 'type'));

        $this->render('admin/projects/question', compact('types', 'error'));
    }

    public function projectResponse()
    {

        if ($this->request() == "POST") 
        {
            $request = $this->f3->get('POST');

            $this->addResponses($request);

            $this->setFlash("Les réponses ont bien été ajoutées");
            $this->f3->reroute($this->f3->get('PATTERN'));
        }

        $questions = ProjectQuestion::all(array('id', 'question'));
        $responses = ProjectResponse::all(array('response', 'question_id'));

        $this->render('admin/projects/response', compact('questions', 'responses'));
    }

    public function addResponses($request, $idQuestion = null)
    {

        if(empty($idQuestion))
            $idQuestion = $request['project_question'];

        foreach ($request['response'] as $key => $response) 
        {
            if ($this->ProjectResponse->validate($response)) 
            {
                if (!empty($this->f3->get('FILES.image.name')[$key])) 
                {
                    $upload = new UploadHelper($this->f3->get('RESPONSE_FILES'));
                    $filename = $upload->upload();

                    if ($filename) 
                        $response['image']= $this->f3->get('RESPONSE_FILES') . $filename;
                }

                if(!empty($response['image']))
                {
                    ProjectResponse::create([
                        'response'      => $response['response'],
                        'description'   => $response['description'],
                        'price'         => $request['price'],
                        'tag'           => $request['tag'],
                        'question_id'   => $idQuestion,
                        'image'         => $response['image']
                    ]);
                }
                else
                {
                    ProjectResponse::create([
                        'response'      => $response['response'],
                        'description'   => $response['description'],
                        'price'         => $request['price'],
                        'tag'           => $request['tag'],
                        'question_id'   => $idQuestion
                    ]);
                }

            }
        }
    }


}