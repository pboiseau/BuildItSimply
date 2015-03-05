<?php

/**
 *  Recommendation controller
 */
class RecommendationController extends AppController
{

    public $uses = array(
        'Project',
        'Recommendation'
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add recommendation for a freelance on a project work
     */
    public function add()
    {
        $project = Project::find($this->get('POST.project_id'));
        $recommendation = $project->recommendation()->get();

        if ($project && $recommendation->count() == 0) {

            $freelance = $project->participates()->status('choosen')->first()->account()->first(['id']);
            $client = $project->account()->first(['id']);

            // create recommendation
            $this->Recommendation->create([
                'client_id' => $client->id,
                'freelance_id' => $freelance->id,
                'project_id' => $project->id,
                'grade' => $this->get('POST.grade'),
                'message' => $this->get('POST.message')
            ]);

            $this->setFlash("Votre avis à bien été publié.");
            $this->f3->reroute('/projects/' . $project->id);

        } else {
            $this->setFlash("Le projet n'existe pas ou vous avez déjà publier une recommendation concernant ce projet.");
            $this->f3->reroute('/projects');
        }
    }


}