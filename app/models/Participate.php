<?php

/**
 * Class Participate
 */
class Participate extends AppModel
{
    public $timestamps = true;
    public $errors;

    protected $table = 'participates';
    protected $guarded = array('created_at', 'updated_at');

    /**
     * @param $project_id
     * @param $freelance_id
     * @return bool|static
     */
    public function demand($project_id, $freelance_id)
    {
        if(!$this->exists($project_id, $freelance_id)){
            return $this->create([
                'project_id' => $project_id,
                'freelance_id' => $freelance_id,
                'status' => 'PENDING'
            ]);
        }else{
            return false;
        }
    }

    /**
     * @param $project_id
     * @param $freelance_id
     * @return mixed
     */
    public function exists($project_id, $freelance_id)
    {
        return $this->where('project_id', $project_id)
            ->where('freelance_id', $freelance_id)->first();
    }
}
