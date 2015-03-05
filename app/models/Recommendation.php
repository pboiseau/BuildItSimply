<?php

/**
 * Class for use Recommendation table
 */
class Recommendation extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'recommendations';
    protected $fillable = array('project_id', 'client_id', 'freelance_id', 'grade', 'message');


}