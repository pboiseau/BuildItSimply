<?php

class ProjectQuestion extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_question';
    protected $guarded = array('id');

    public function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (empty($data['question'])) {
            $errors['question'] = 'Question vide.';
        }

        if (empty($data['step'])) {
            $errors['step'] = 'Etape vide';
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }
}
