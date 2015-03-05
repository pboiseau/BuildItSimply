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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project()
    {
        return $this->hasMany('Project', 'project_type_id', 'id');
    }

}
