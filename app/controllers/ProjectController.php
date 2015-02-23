<?php

class ProjectController extends AppController
{

    public $uses = array('Account', 'Project', 'Client', 'Participate', 'ProjectType', 'ProjectStep');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Trigger execute before routing the client
     * Overload the parent beforeroute method
     * Adding some control routing for the project controller
     */
    public function beforeroute()
    {
        // call parent beforeroute
        parent::beforeroute();

        if ($this->f3->get('PATTERN') == "/projects/@id") {
            $project = $this->Project->find($this->f3->get('PARAMS.id'));
            if($project) {
                if ($project->client_id == $this->Auth->getId()) {
                    $this->f3->reroute('/projects/edit/' . $this->f3->get('PARAMS.id'));
                }
            }else{
                $this->f3->reroute('/projects/');
            }
        }

        if ($this->f3->get('PATTERN') == "/projects/edit/@id") {
            if (!$this->Auth->is('client')) {
                $this->setFlash("En tant que Freelance vous ne pouvez pas acceder à cette zone");
                $this->f3->reroute('/projects/');
            }
        }

        // if request content @id params
        // check if project exist
        if (!empty($this->f3->get('PARAMS.id'))) {
            if (!$this->Project->exists($this->f3->get('PARAMS.id'))) {
                $this->setFlash("Ce projet n'existe pas.");
                $this->f3->reroute('/projects/');
            }
        }

        if (in_array($this->f3->get('PATTERN'), ["/projects/detail/step", "/projects/detail/step/@step"])) {
            if (!$this->Auth->is('client') || !$this->f3->get('SESSION.project')) {
                $this->f3->reroute('/projects/');
            }
        }
    }

    /**
     * Initialize a project
     */
    public function init()
    {
        $project = array();
        $errors = array();

        if ($this->request() == "POST") {
            $newProject = $this->f3->get('POST');
            if ($this->Project->initialize($newProject)) {

                $this->f3->set('SESSION.project', $newProject);
                $this->f3->reroute('/projects/detail/step');

            } else {
                $project = $newProject;
                $errors = $this->Project->errors;
            }
        }

        $this->render('projects/init', compact('project', 'errors'));
    }

    /**
     * Show a project by ID
     */
    public function show()
    {
        $project = $this->Project->show($this->f3->get('PARAMS.id'));

        if ($project) {
            $this->render('projects/show', compact('project'));
        } else {
            $this->setFlash("Ce projet n'existe pas.");
            $this->f3->reroute('/projects/');
        }
    }

    /**
     * Get all projects and display them on a list
     */
    public function all()
    {
        $projects = $this->Project->whereNotIn('status', ['ANNULE'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($projects as $key => $project) {
            $projects[$key]['client'] = $project->account()->first();
            $projects[$key]['proposition'] = $project->participates()->count();
        }
        $this->render('projects/all', compact('projects'));
    }


    /**
     * Get projects that contains keywords and display them on a list
     */
    public function search()
    {
        if ($this->request() == "POST") {
            $this->validator = new Validate();
            $searchWords = $this->f3->get('POST')['searchWords'];
            $this->words = explode(' ', $searchWords);

            $request = $this->Project->whereNotIn('status', ['ANNULE']);

            $request->where(function ($query) {
                foreach ($this->words as $word) {
                    if ($this->validator->isKeyword($word, 100)) {
                        $query->orWhere('name', 'like', '%' . $word . '%')
                            ->orWhere('description', 'like', '%' . $word . '%')
                            ->orWhere('targets', 'like', '%' . $word . '%');
                    }
                }
            });

            $projects = $request->orderBy('created_at', 'desc')->get();

            foreach ($projects as $key => $project) {
                $projects[$key]['client'] = $project->account()->first();
            }

            $this->render('projects/all', compact('projects', 'searchWords'));
        }

    }


    /**
     * Ask a client for participate to his project
     * Create a new project demand with pending status
     * @param GET ID of the project
     */
    public function join()
    {
        $project = $this->Project->getById($this->f3->get('PARAMS.id'));
        $user = $this->f3->get('SESSION.user');

        // check if project is status OPEN
        if ($project->status != "OUVERT") {
            $this->setFlash("Ce projet n'est pas ouvert à de nouveaux freelances.");
            $this->f3->reroute("/projects/" . $project->id);
        }


        if ($project && $this->Auth->is('freelance')) {

            $client = $project->account()->first();

            if ($this->Participate->demand($project->id, $user['id'])) {

                $this->setFlash("Votre participation a bien été prise en compte.");

                $this->MailHelper->sendMail("demand", $client->mail, [
                    'subject' => "Nouvelle demande pour votre projet " . $project->name,
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'project' => $project
                ]);

            } else {
                $this->setFlash("Vous participez déjà à ce projet.");
            }
        }
        $this->f3->reroute('/projects/' . $this->f3->get('PARAMS.id'));
    }

    /**
     * Edit a project with his ID
     */
    public function edit()
    {
        $project = $this->Project->find($this->f3->get('PARAMS.id'));

        // if project doesn't exist or isn't own by the client
        if (empty($project) || $project->client_id != $this->f3->get('SESSION.user.id')) {
            return $this->f3->reroute('/projects/');
        }

        /*
         * Get all proposition filter by status accept
         * if project is in progress
         */
        $status = ($project->status == 'EN COURS') ? 'ACCEPT' : null;
        $propositions = $this->Participate->proposition($project->id, $status);

        if ($this->request() == "POST") {
            if ($this->Project->updateProject($project->id, $this->f3->get('POST'))) {
                $this->setFlash("Les modifications de votre projet ont bien été effectué.");
                $this->f3->reroute('/projects/edit/' . $project->id);
            }
        }

        $this->render('projects/edit', compact('project', 'propositions'));
    }

    /**
     * Cancel a project and update his status to "ANNULE"
     * @param GET ID of the project
     */
    public function delete()
    {
        $project = $this->Project->find($this->f3->get('PARAMS.id'));
        if ($project->client_id == $this->f3->get('SESSION.user.id')) {
            $delete = $project->update([
                'status' => 'ANNULE'
            ]);
            if ($delete) {
                $this->setFlash("Votre projet a bien été annulé");
            }
        }
        $this->f3->reroute('/projects/' . $this->f3->get('PARAMS.id'));
    }

    /**
     * Show all client's projects
     */
    public function client_list()
    {
        $projects = $this->Project->where('client_id', $this->f3->get('SESSION.user.id'))->get();

        $this->render('projects/client_list', compact('projects'));
    }


    /**
     * Update the proposition status with the client's choice
     * Call in AJAX mode
     * @return JSON
     */
    public function choice()
    {
        if ($this->f3->get('AJAX')) {

            $req = $this->f3->get('POST');


            $participate = $this->Participate->where('project_id', $req['project_id'])
                ->where('freelance_id', $req['freelance_id'])
                ->first();

            if ($participate->status == "PENDING") {

                // update status
                $update = $this->Participate->choice($req['project_id'], $req['freelance_id'], $req['status']);

                if ($update) {
                    echo $this->encode("proposition", [
                        "error" => false,
                        "status" => $req['status'],
                        "message" => "freelance " . $req['status']
                    ]);

                } else {
                    echo $this->encode("proposition", [
                        "error" => true,
                        "message" => "Vous ne pouvez choisir que 3 freelances au maximum"
                    ]);
                }
            }
        }
    }

    public function sendResponse()
    {
        if ($this->f3->get('AJAX')) {
            $req = $this->f3->get('POST');
            $project = $this->Project->getById($req['project_id']);
            $freelance = $this->Account->getById($req['freelance_id']);

            $this->MailHelper->sendMail('response', $freelance->mail, [
                'subject' => "Votre demande concernant le projet " . $project->name,
                'project' => [
                    'name' => $project->name,
                    'firstname' => $project->firstname,
                    'lastname' => $project->lastname
                ],
                'demand' => [
                    'status' => $req['status']
                ]
            ]);
        }
    }

    /**
     * Close proposition for this project
     */
    public function close()
    {
        $project_id = $this->f3->get('PARAMS.id');

        // stay in request builder mode
        $propositions = $this->Participate->where('project_id', $project_id);

        if ($propositions->where('status', 'ACCEPT')->count() >= 1) {
            $update = $this->Project->where('id', $project_id)->update([
                'status' => 'EN COURS'
            ]);
            if ($update) {
                $this->setFlash("Les demandes pour votre projet sont maintenant fermés.");
            }
        } else {
            $this->setFlash("Vous ne pouvez cloturer les demandes que si vous en avez au moins accepté une.");
        }

        $this->f3->reroute("/projects/" . $project_id);
    }

    /**
     * First step of detail information about the project
     * Client choose between our project type
     */
    public function startingStep()
    {
        $step = 0;
        $types = ProjectType::all();
        $this->render('projects/start', compact('types', 'step'));
    }

    /**
     * AJAX Call
     * Get next or prev step for project details informations
     * After all step redirect client to the finish page
     */
    public function step()
    {
        if ($this->f3->get('AJAX')) {

            $request = $this->f3->get('POST');
            $questions = $this->ProjectStep->changeStep($request['step'], $request['type']);

            if ($questions) {
                $step = $request['step'];
                $type = $request['type'];
                return $this->render('projects/step', compact('step', 'questions', 'type'));
            } else {
                // all step finish send json object for redirect client to the validation page
                echo $this->encode('step',
                    ['status' => 'finish', 'redirect' => $this->config['home'] . 'projects/finish']);
            }
        }
    }

    public function finish()
    {

        $this->render('projects/finish', []);
    }


}


?>