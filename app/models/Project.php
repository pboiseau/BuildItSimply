<?php

class Project extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'projects';
    protected $guarded = array('id', 'created_at', 'updated_at');

    public function client()
    {
        return $this->belongsTo('Client', 'client_id', 'account_id');
    }

    /**
     *    Validate mandatory information before save into database
     *    Fill errors property if needed
     * @param array $data
     * @return boolean
     **/
    private function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

}

