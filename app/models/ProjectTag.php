<?php

class ProjectTag extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_tag';
    protected $guarded = array('id');


    /**
     * Create all project tags
     * @param $project_id
     * @param $tags_unformatted
     */
    public function addTags($project_id, $tags_unformatted)
    {
        $tags = explode(',', substr($tags_unformatted, 1));

        for($i = 0; $i < sizeof($tags); $i++){
            $this->create([
                'tag' => $tags[$i],
                'project_id' => $project_id
            ]);
        }
    }

}