<?php

class ProjectQuestion extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_question';
    protected $guarded = array('id');

    /**
     * Check validate date of question
     * @param array $data
     * @return bool
     */
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
