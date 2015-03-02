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
     * Create new participation
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
     * Check if participation exist
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
     * Update participate status with the choice of the client
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
                ->join('accounts', 'projects.client_id', '=', 'accounts.id')
                ->orderBy('participates.updated_at', 'desc')
                ->get([
                    'projects.id as project_id',
                    'projects.name',
                    'projects.client_id',
                    'participates.status',
                    'participates.updated_at',
                    'accounts.firstname',
                    'accounts.lastname',
                    'accounts.picture'
                ]);

        } else if($user_type == 'client') {

            $participations = $this->where('status', 'PENDING')
                ->whereIn('project_id', function ($query) {
                    $query->select('id')
                        ->from('projects')
                        ->where('client_id', $this->user_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return ($participations->count() > 0) ? $participations : false;
    }

    /**
     * Group collection by date
     * @param Collection $participations
     * @return array of Collection
     */
    public function groupByDate($participations)
    {
        return $participations->groupBy(function ($self) {
            $date = new DateTime($self->created_at);
            return $date->format('d');
        });
    }

    /**
     * @param $project_status
     * @return bool|string
     */
    public function statusReference($project_status){
        switch($project_status){
            case "OUVERT":
                return "PENDING";
            case "DECISION":
                return "ACCEPT";
            case "EN COURS":
                return "CHOOSEN";
        }
        return false;
    }

}
