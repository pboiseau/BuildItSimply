<?php

class ProjectQuestion extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_question';
    protected $guarded = array('id');


}
