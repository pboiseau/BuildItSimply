<?php

class ProjectController extends AppController
{

    public $uses = array('Project', 'Client', 'Participate');

    public function __construct()
    {
        parent::__construct();
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
     *
     */
    public function all()
    {
        $projects = $this->Project->all();
        foreach ($projects as $key => $project) {
            $projects[$key]['client'] = $project->account()->first();
        }
        $this->render('projects/all', compact('projects'));
    }

    /**
     * Ask a client for participate to his project
     * Create a new project demand with pending status
     * @param GET ID of the project
     */
    public function join()
    {
        $project = $this->Project->getById($this->f3->get('PARAMS.id'), array('id', 'client_id'));
        $user = $this->f3->get('SESSION.user');
        if ($project && $user['type'] == 'FREELANCE') {

            $client = $project->account()->first();

            if ($this->Participate->demand($project->id, $user['id'])) {

                $subject = $user['firstname'] . " " . $user['lastname'] . " souhaite participer à votre projet";
                $message = $user['firstname'] . " " . $user['lastname'] . " souhaite participer à votre projet";

                $this->Participate->sendMail($client->mail, $subject, $message);
                $this->setFlash("Votre participation a bien été prise en compte.");

            } else {
                $this->setFlash("Vous participez déjà à ce projet.");
            }
        }
        $this->f3->reroute('/projects/' . $this->f3->get('PARAMS.id'));
    }

    /**
     *
     */
    public function edit()
    {

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
}

?>