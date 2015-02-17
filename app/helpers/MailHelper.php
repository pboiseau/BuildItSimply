<?php

class MailHelper{

    public function __construct()
    {
        $this->f3 = Base::instance();
    }

    /**
     * Send email using Fatfree SMTP class
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    public function sendMail($to, $subject, $message)
    {
        $smtp = new SMTP(
            $this->f3->get('HOST_MAIL'),
            $this->f3->get('PORT_MAIL'),
            'ssl',
            'paul.boiseau@hetic.net',
            $this->f3->get('PWD_MAIL')
        );

        $smtp->set('From', '"Build It Simply" <builditsimply@paulboiseau.com>');
        $smtp->set('To', '<' . $to . '>');
        $smtp->set('Subject', $subject);
        return $smtp->send($message);
    }
}