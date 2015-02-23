<?php

class ProjectResponse extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_response';
    protected $guarded = array('id');

    public function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (empty($data['response'])) {
            $errors['response'] = 'Reponse vide.';
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }
}