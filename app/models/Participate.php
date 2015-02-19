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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Account', 'freelance_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Project', 'project_id', 'id');
    }

    /**
     * @param $project_id
     * @param $freelance_id
     * @return bool|static
     */
    public function demand($project_id, $freelance_id)
    {
        if (!$this->exists($project_id, $freelance_id)) {
            return $this->create([
                'project_id' => $project_id,
                'freelance_id' => $freelance_id,
                'status' => 'PENDING'
            ]);
        } else {
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


    /**
     * @param $project_id
     * @param $freelance_id
     * @param $status
     * @return mixed
     */
    public function choice($project_id, $freelance_id, $status)
    {
        return $this->where('project_id', $project_id)
            ->where('freelance_id', $freelance_id)
            ->update([
                'status' => $status
            ]);
    }

}
