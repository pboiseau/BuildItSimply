<?php

class AdminController extends AppController
{
    public $uses = array('Project', 'ProjectType', 'ProjectQuestion', 'ProjectStep', 'ProjectResponse');

    public $layout = 'admin';

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Back-office's home
    */
    public function main()
    {
        $this->render('admin/');
    }

    /*
     * Add type of project
    */
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


    /*
     * Manage step of project
    */
    public function projectStep()
    {

        if ($this->request() == "POST") 
        {
            $request = $this->f3->get('POST');

            if(!empty($request))
            {
                $questions = new ProjectQuestion();

                // Si l'utilisateur veut update
                if($request['button']=='update')
                {
                    foreach ($request['question'] as $key => $question) 
                    {
                        $questions->where('id', $key)->update(['question' => $question]);
                    }
                    $this->setFlash("Les étapes ont bien été mises à jours");
                }

                // Si l'utilisateur veut remove
                else if($request['button']=='remove')
                {
                    $steps = new ProjectStep();

                    foreach ($request['checked_question'] as $key => $value) 
                    {
                        $steps->where('project_question_id', $key)->delete();
                        $questions->destroy($key);
                    }
                    $this->setFlash("La sélection a bien été supprimé");
                }
            }

            $this->f3->reroute($this->f3->get('PATTERN'));
        

        }

        $types = ProjectType::all(array('id', 'type'));
        $steps = ProjectStep::where('project_question_id', '>', 0)
            ->join('project_question', 'project_step.project_question_id', '=', 'project_question.id')
            ->orderBy('project_step.step', 'asc')
            ->get();

        $this->render('admin/projects/step', compact('steps', 'types'));
    }



    /*
     * Add questions for one type of project
    */
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


    /*
     * For answer one question added previously
    */
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


    /*
     * Function for add answer
    */
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


    /**
     * POST request for add skill in the database
     */
    public function freelanceSkillNew()
    {
        if($this->request() == "POST")
        {

            if( Skill::create($this->f3->get('POST')))
            {
                $this->setFlash("La compétence a bien été ajouté.");
                $this->f3->reroute($this->f3->get('PATTERN'));
            }
        }

        $categories = CategorySkill::all();
        $this->render('admin/freelance/skill/new', compact('categories'));
    }


}