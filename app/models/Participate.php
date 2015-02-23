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
        if ($status == "accept") {
            if ($this->number($project_id) >= 3) {
                return false;
            }
        }

        return $this->where('project_id', $project_id)
            ->where('freelance_id', $freelance_id)
            ->update([
                'status' => $status
            ]);
    }

    /**
     * Get the number of participations of a particular status
     * @param $project_id
     * @param string $status
     */
    public function number($project_id, $status = 'ACCEPT')
    {
        return $this->where('project_id', $project_id)->where('status', $status)->count();
    }

    /**
     * Get all proposition of a project
     * @param $project_id
     * @return array|bool
     */
    public function proposition($project_id, $status = null)
    {
        $propositions = $this->where('project_id', $project_id)
            ->join('accounts', 'freelance_id', '=', 'id')
            ->join('freelances', 'freelance_id', '=', 'account_id')
            ->orderBy('participates.created_at', 'desc');

        if ($status) {
            $propositions->where('status', $status);
        }

        return ($propositions->count() > 0) ? $propositions->get() : false;
    }

    /**
     * Get notifications by client type
     * @param $user_id
     * @param $user_type
     * @return array|bool
     */
    public function notification($user_id, $user_type)
    {
        $this->user_id = $user_id;
        if ($user_type == 'freelance') {

            $participations = $this->where('freelance_id', $user_id)
                ->where('participates.status', '!=', 'PENDING')
                ->join('projects', 'project_id', '=', 'id')
                ->orderBy('participates.updated_at', 'desc')
                ->get(['projects.id', 'projects.name', 'participates.status', 'participates.updated_at']);

        } else if($user_type == 'client') {

            $participations = $this->where('status', 'PENDING')
                ->whereIn('project_id', function ($query) {
                    $query->select('id')
                        ->from('projects')
                        ->where('client_id', $this->user_id);
                })->orderBy('created_at', 'desc')->get();
        }

        return ($participations->count() > 0) ? $participations : false;
    }

}
