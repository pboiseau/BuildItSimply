<?php

class ApiController extends AppController
{
    public $uses = array(
        'Account',
        'Project',
        'Client',
        'Participate',
        'ProjectType',
        'ProjectStep',
        'ProjectResponse',
        'ProjectTag',
        'ProjectFile'
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a list of projects in JSON
     */
    public function getProjects()
    {
        $projects = $this->Project->publicated()
            ->with('account', 'participates', 'freelances')
            ->recent();
        
        if($price = $this->get('PARAMS.price')){
            $projects = $projects->where('price', '<=', $price);
        }

        echo $projects->get()->toJson();
    }

    /**
     * Get a list of freelances in JSON
     */
    public function getFreelances()
    {
        echo $this->Account->with('freelance')->get()->toJson();
    }
}
