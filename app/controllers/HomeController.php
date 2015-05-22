<?php

/**
 *  Home controller
 */
class HomeController extends AppController
{

    public $uses = ['Account', 'Client', 'Freelance', 'Project'];

    /**
     *   Initialize with AppController's construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Show home page
     */
    public function index()
    {
        $project = $this->Project->publicated()->count();
        $client = $this->Client->all()->count();
        $freelance = $this->Freelance->all()->count();

        $this->render('index', compact('project', 'client', 'freelance'));
    }

    /**
     *   Show how it works page
     */
    public function howItWorks()
    {
        $this->setSeo([
            'title'       => "Comment ca marche",
            'description' => "Vous avez une idée, un projet et vous ne savez pas vraiment qui contacter.
Ne vous inquiétez pas, à travers différentes étapes ultra-simplifiées, nous allons vous accompagner dans la soumission de votre projet"
        ]);

        $this->render('howitworks');
    }

}

?>
