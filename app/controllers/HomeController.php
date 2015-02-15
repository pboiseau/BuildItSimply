<?php

class HomeController extends AppController
{

    public $uses = array('Account', 'Client', 'Freelance', 'Project');

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function index()
    {
        $project = $this->Project->all()->count();
        $client = $this->Client->all()->count();
        $freelance = $this->Freelance->all()->count();

        $this->render('index', compact('project', 'client', 'freelance'));
    }

    /**
     *
     */
    public function howItWorks()
    {
        $this->render('howitworks');
    }

}

?>
