<?php

class ProjectType extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_type';
    protected $guarded = array('id');


}
