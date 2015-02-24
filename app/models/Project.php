<?php

/**
 * Class Project
 */
class Project extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'projects';
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('Client', 'client_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Account', 'client_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function freelances()
    {
        return $this->belongsToMany('Freelance', 'participates', 'project_id', 'freelance_id');
    }

    public function accounts()
    {
        return $this->belongsToMany('Account', 'participates', 'project_id', 'freelance_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participates()
    {
        return $this->hasMany('Participate', 'project_id', 'id');
    }

    /**
     *
     */
    public function exists($id)
    {
        return ($project = $this->find($id)) ? $project : false;
    }

    /**
     * @param $id
     * @param array $field
     * @return mixed
     */
    public function getById($id, $field = array('*'))
    {
        return $this->where($this->table.'.id', $id)
            ->join('accounts', 'client_id', '=', 'accounts.id')
            ->first($field);
    }

    /**
     * @param array $project
     * @return static
     */
    public function initialize($project = array())
    {
        if ($this->validate($project)) {
            $project['client_id'] = Base::instance()->get('SESSION.user.id');
            $project['status'] = 'EN CREATION';
            return $this->create($project);
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $project = $this->where('id', $id)->first();
        if (!empty($project)) {
            $project['client'] = $project->account()->first();
        }
        return (!empty($project)) ? $project : false;
    }


    /**
     * Update a project
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateProject($id, $data)
    {
        return $this->where('id', $id)->update($data);
    }

    /**
     * Validate mandatory information before save into database
     * Fill errors property if needed
     * @param array $data
     * @return boolean
     **/
    private function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (!empty($data['url']) && !$validator->url($data['url'])) {
            $errors['url'] = "Ceci n'est pas une URL de site web valide.";
        }

        if (empty($data['name'])) {
            $errors['name'] = "Le titre du projet ne peut pas être vide.";
        }

        if (empty($data['description'])) {
            $errors['description'] = "La description du projet ne peut pas être vide.";
        }

        if (empty($data['targets'])) {
            $errors['targets'] = "Vous devez renseigner au moins une cible.";
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

}

