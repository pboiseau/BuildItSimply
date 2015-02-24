<?php

class Freelance extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'freelances';
    protected $primaryKey = 'account_id';
    protected $guarded = array('created_at');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne('Account', 'account_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function skills()
    {
        return $this->belongsToMany('Skill', 'freelances_skills', 'account_id', 'skill_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function freelances_skills()
    {
        return $this->hasMany('FreelanceSkill', 'account_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany('Project', 'participates', 'freelance_id', 'project_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participates()
    {
        return $this->hasMany('Participate', 'freelance_id', 'account_id');
    }

    /**
     * Update freelance profile
     * @param $freelance
     * @return bool|static
     */
    public function updateProfile($freelance)
    {
        unset($freelance['skills']);
        if (!$this->validate($freelance)) {
            return false;
        }

        if ($profile = $this->where('account_id', $freelance['account_id'])->first()) {
            // update freelance
            return $this->where('account_id', $freelance['account_id'])->update([
                'activity' => $freelance['activity'],
                'url' => $freelance['url'],
                'experience' => $freelance['experience'],
            ]);
        }
    }


    /**
     * Check if data is valid
     * @param array $data
     * @return bool
     */
    private function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (!empty($data['url']) && !$validator->url($data['url'])) {
            $errors['url'] = "L'adresse web est incorrect";
        }

        if (!in_array($data['experience'], ['DEBUTANT', 'CONFIRME', 'EXPERT'])) {
            $errors['experience'] = "Votre niveau d'experience est incorrect";
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

}

?>