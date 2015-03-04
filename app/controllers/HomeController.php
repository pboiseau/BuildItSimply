<?php

/**
 *  Home controller 
 */
class HomeController extends AppController
{

    public $uses = array('Account', 'Client', 'Freelance', 'Project');

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
        $this->render('howitworks');
    }

}

?>
