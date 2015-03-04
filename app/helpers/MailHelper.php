<?php

/**
 * Class MailHelper
 */
class MailHelper extends BaseHelper{

    public function __construct()
    {
        parent::__construct();
        $this->smtp = new SMTP(
            $this->f3->get('HOST_MAIL'),
            $this->f3->get('PORT_MAIL'),
            'ssl',
            $this->f3->get('ACCOUNT_MAIL'),
            $this->f3->get('PWD_MAIL')
        );
    }

    /**
     * Send email using Fatfree SMTP class
     * @param $template
     * @param $to recipient of mail
     * @param array $data mail's body
     * @return bool
     */
    public function sendMail($template, $to, $data = array())
    {
        $this->smtp->set('Content-type', 'text/html; charset=UTF-8');
        $this->smtp->set('From', '"Build It Simply" <builditsimply@paulboiseau.com>');
        $this->smtp->set('To', '<' . $to . '>');
        $this->smtp->set('Subject', $data['subject']);
        return $this->smtp->send($this->layout($template, $data));
    }

    /**
     * Get render for one template mail
     * @param $template
     * @param array $data
     * @return mixed
     */
    private function layout($template, $data = array())
    {
        return $this->twig->render('mails/' . $template . '.twig', compact('data'));
    }
}