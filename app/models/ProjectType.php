<?php

/**
 * Class for use ProjectType table
 */
class ProjectType extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_type';
    protected $guarded = array('id');


}
