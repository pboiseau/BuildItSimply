<?php

class ProjectStep extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_step';
    protected $guarded = array('id');

    /**
     * @param $project_type
     * @param $step
     * @return bool
     */
    public function exists($project_type, $step)
    {
        $project_step = $this->where('step', $step)->where('project_type_id', $project_type)->first();
        return (!empty($project_step)) ? true : false;
    }
}
