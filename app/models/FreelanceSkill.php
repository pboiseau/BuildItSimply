<?php

class FreelanceSkill extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'freelances_skills';
    protected $fillable = array('account_id', 'skill_id');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function freelance()
    {
        return $this->hasOne('Freelance', 'account_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skills()
    {
        return $this->hasOne('Skill', 'id', 'skill_id');
    }

    /**
     * Add skill
     * @param array $skills
     */
    public function add($skills = array())
    {
        $f3 = Base::instance();

        foreach ($skills as $key => $skill) {
            // check if skill already exist
            $actual_skill = $this->where('account_id', $f3->get('SESSION.user.id'))
                ->where('skill_id', $skill->id)->first();

            if (empty($actual_skill)) {
                $this->create([
                    'account_id' => $f3->get('SESSION.user.id'),
                    'skill_id' => $skill->id
                ]);
            }
        }
    }

    /**
     * Get all Freelance Skills with restriction by account_id or skill_id
     * @param string $field
     * @param int $id
     * @return array of Freelance Skills or false
     **/
    public function getAll($field, $id)
    {
        $freelance_skills = $this->where($field, $id)->get();
        return (!empty($freelance_skills)) ? $freelance_skills : false;
    }


}

?>