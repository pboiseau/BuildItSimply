<?php

/**
 * Class BaseHelper
 */
class BaseHelper
{
    protected $f3;
    protected $web;
    protected $twig;
    protected $url;

    public function __construct()
    {
        $this->web = Web::instance();
        $this->f3 = Base::instance();
        $this->twig = $this->f3->get('TWIG');
        $this->url = $this->f3->get('URL');
    }
}

