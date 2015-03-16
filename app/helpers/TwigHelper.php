<?php

/**
 * Class TwigHelper
 */
class TwigHelper extends BaseHelper
{
    public function __construct()
    {
        parent::__construct();

        $this->addFunction('translateStatus', $this->translateStatus());
        $this->addFunction('javascript', $this->javascriptTag());
    }


    /**
     * @param $name
     * @param $function
     */
    public function addFunction($name, $function)
    {
        $this->twig->addFunction(new \Twig_SimpleFunction($name, $function));
    }

    /**
     * @return callable
     */
    private function javascriptTag()
    {
        return function($file){
            echo sprintf("<script src='/%s'></script>", $this->get('JS') . $file);
        };
    }

    /**
     * @return callable
     */
    private function translateStatus()
    {
        return function ($status) {
            switch ($status) {
                case "ACCEPT":
                    echo sprintf("accepté");
                    break;
                case "DECLINE":
                    echo sprintf("refusé");
                    break;
                case "PENDING":
                    echo sprintf("en attente");
                    break;
                case "CHOOSEN":
                    echo sprintf("choisi");
                    break;
                default:
                    break;
            }
        };
    }


}