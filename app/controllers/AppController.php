<?php

/**
 *  Main controller class
 */
class AppController
{

    public $f3;

    protected $twig;
    protected $layout = 'default';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->f3 = Base::instance();
        $this->twig = $this->get('TWIG');
        $this->twigExtention();

        // instanciate helpers
        $this->MailHelper = new MailHelper();
        $this->Auth = new AuthHelper();

        $this->config = [
            'prod' => $this->get('PROD'),
            'root' => ($this->get('PROD')) ? $this->get('ROOT') : $this->get('DEV_ROOT'),
            'home' => ($this->get('PROD')) ? $this->get('ROOT') : $this->get('DEV_ROOT') . '/',
            'webroot' => $this->get('WEBROOT'),
            'css' => $this->get('CSS'),
            'js' => $this->get('JS'),
            'image' => ($this->get('PROD')) ? $this->get('ROOT') . $this->get('IMAGE') : $this->get('DEV_ROOT') . '/' . $this->get('IMAGE'),
            'request' => substr($this->get('PATTERN'), 1, strlen($this->get('PATTERN'))),
            'message' => $this->get('SESSION.message'),
            'login' => $this->get('SESSION.user'),
            'url' => $this->get('URL')
        ];

        if (!empty($this->uses)) {
            foreach ($this->uses as $model) {
                $this->loadModel($model);
            }
        }

    }

    /**
     *    FatFree before route trigger
     **/
    public function beforeroute()
    {
        if (!$this->Auth->isLogin() && !$this->get('ERROR')) {
            if (!in_array($this->get('PATTERN'), ['/', '/howitworks', '/users/login', '/users/register'])) {
                $this->setFlash("Vous devez vous authentifier pour effectuer cette action.");
                $this->f3->reroute('/');
            }
        }
    }

    /**
     * Trigger when error is detected
     */
    public function error()
    {
        $error = $this->get('ERROR');
        $this->render('errors/' . $error['code'], compact('error'));
    }


    /**
     *    Render a view using twig template
     * @param string $template
     * @param array $data
     **/
    protected function render($template = null, $data = array())
    {
        $data['layout'] = $this->layout;

        echo $this->twig->render($template . '.twig',
            array_merge($data, $this->config)
        );

        if ($this->get('SESSION.message')) {
            $this->set('SESSION.message', '');
        }
    }

    /**
     * Get the request type (get, post ...)
     * @return string request type
     **/
    protected function request()
    {
        return $this->f3->get('VERB');
    }

    /**
     * Set flash message into user session
     * @param string $message
     **/
    protected function setFlash($message)
    {
        $this->set('SESSION.message', $message);
    }


    /**
     * Encode data into JSON
     * @param $name
     * @param array $data
     * @param string $status
     * @return string
     */
    protected function encode($name, $data = array(), $status = null)
    {
        header('Access-Control-Allow-Origin: *');
        header('Acces-Control-Allow-Headers: Auth-Token');
        header('Access-Control-Allow-Methods: *');
        header('Content-Type: application/json');

        if ($status == "ok") {
            header("HTTP/1.0 200 OK");
        } else {
            if ($status == "ko") {
                header("HTTP/1.0 404 Not Found");
            }
        }

        return '{"' . $name . '": ' . json_encode($data, CASE_LOWER) . '}';
    }

    /**
     * Fatfree get method
     * @param $params
     * @return mixed
     */
    protected function get($params)
    {
        return $this->f3->get($params);
    }

    /**
     * Fatfree set method
     * @param $key
     * @param $value
     */
    protected function set($key, $value)
    {
        $this->f3->set($key, $value);
    }


    /**
     * Instanciate and load a database model
     * @param $model
     * @throws Exception
     */
    private function loadModel($model)
    {
        if (class_exists($model)) {
            $this->$model = new $model();
        } else {
            throw new Exception("Class " . $model . " doesn't exist");
        }
    }

    /**
     *  Add extension to twig
     */
    private function twigExtention()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('javascript', function ($file) {
            echo sprintf("<script src='/%s'></script>", $this->get('JS') . $file);
        }));

        $this->twig->addFunction(new \Twig_SimpleFunction('translateStatus', function ($status) {
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
        }));
    }

}

?>