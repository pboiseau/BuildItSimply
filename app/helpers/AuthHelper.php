<?php

/**
 * Class MailHelper
 */
class AuthHelper extends BaseHelper
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check user type
     * @param $type
     * @return bool
     */
    public function is($type)
    {
        return ($this->f3->get('SESSION.user.type') == strtoupper($type)) ? true : false;
    }

    /**
     * Check if user log in
     * @return bool
     */
    public function isLogin(){
        $user = $this->f3->get('SESSION.user');
        return (!empty($user)) ? true : false;
    }

}