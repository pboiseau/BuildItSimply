<?php

/**
 * Class BaseHelper
 */
class BaseHelper
{
    protected $f3;
    protected $web;
    protected $twig;

    public function __construct()
    {
        $this->web = Web::instance();
        $this->f3 = Base::instance();
        $this->twig = $this->f3->get('TWIG');
    }
}

