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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany('ProjectTag', 'project_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('ProjectType', 'project_type_id', 'id');
    }

    /**
     * Scope for getting project order by date
     * @param $query
     * @return mixed
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('projects.created_at', 'DESC');
    }

    /**
     * Check if project exists in the database
     * @param $id
     * @return bool|\Illuminate\Support\Collection|null|static
     */
    public function exists($id)
    {
        return ($project = $this->find($id)) ? $project : false;
    }

    /**
     * Get project status
     * @param $id
     * @return mixed
     */
    public function getStatus($id)
    {
        $project = $this->where('id', $id)->first(['id', 'status']);
        return (!empty($project)) ? $project->status : false;
    }

    /**
     * Get project by ID
     * @param $id
     * @param array $field
     * @return mixed
     */
    public function getById($id, $field = array('*'))
    {
        return $this->where($this->table . '.id', $id)
            ->join('accounts', 'projects.client_id', '=', 'accounts.id')
            ->first($field);
    }

    /**
     * Get projects by category ID
     * @param $category_id
     * @return bool
     */
    public function getByCategory($category_id)
    {
        $projects = $this->whereNotIn('status', ['EN CREATION', 'ANNULE'])
            ->where('project_type_id', $category_id)
            ->join('project_type', 'project_type.id', '=', 'project_type_id')
            ->recent()
            ->get([
                'projects.*',
                'project_type.type'
            ]);

        $projects = $this->getInformation($projects);

        return ($projects->count() > 0) ? $projects : false;
    }

    /**
     * Count category by category ID
     * @param $category_id
     * @return mixed
     */
    public function countCategory($category_id)
    {
        return $this->where('project_type_id', $category_id)
            ->whereNotIn('status', ['EN CREATION', 'ANNULE'])
            ->count();
    }

    /**
     * Count all publish project
     * @return int
     */
    public function countPublish()
    {
        return $this->whereNotIn('status', ['EN CREATION', 'ANNULE'])->count();
    }

    /**
     * Initialize a project and create project with first essentials data
     * Put it in "EN CREATION" status
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

    public function getInformation($projects)
    {
        return $projects->each(function ($project) {
            $project['proposition'] = $project->participates()->count();
            $project['tags'] = $project->tags;
        });
    }

    /**
     * Get project and client information
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
     * Publish the project to public
     * @param $id
     * @param $project
     * @return bool
     */
    public function publish($id, $project)
    {
        if ($this->validate($project)) {
            $project['status'] = 'OUVERT';
            return $this->where('id', $id)->update($project);
        }
        return false;
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

