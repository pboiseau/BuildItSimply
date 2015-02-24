<?php

class ProjectResponse extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_response';
    protected $guarded = array('id');

    /**
     * Get responses from an array of ids
     * @param array $ids
     * @return array|bool
     */
    public function getResponses($ids = array())
    {
        $responses = array();
        for($i = 1; $i < sizeof($ids); $i++){
            $responses[] = $this->find($ids[$i]);
        }

        return (sizeof($responses) > 0) ? $responses : false;
    }

    /**
     * Validate response
     * @param array $data
     * @return bool
     */
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