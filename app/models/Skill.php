<?php

/**
 * Class for manage skill
 */
class Skill extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'skills';
    protected $guarded = array('id');

    /**
     * Get all Freelance who has this skill
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function freelance()
    {
        return $this->belongsToMany('Freelance', 'freelances_skills', 'skill_id', 'account_id');
    }

    /**
     * Get the category of a skill
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories_skills()
    {
        return $this->belongsTo('CategorySkill', 'category_skill_id', 'id');
    }

    /**
     * Get skills in string format like 'Php, Javascript, HTML'
     * Need to explode the string on the comma in order to execute request skill by skill
     * @param string skills
     * @return array $skills
     **/
    public function explodeSkills($request_skills)
    {
        $request_skills = explode(', ', $request_skills);
        $skills = $this->whereIn('name', $request_skills)->get();
        return $skills;
    }

    /**
     * Get skills from an array of FreelanceSkills ('account_id', 'skill_id')
     * @param array $skills
     * @return array $freelance_skills or false
     **/
    public function getFromFreelanceSkills($skills = array())
    {
        $freelance_skills = array();
        foreach ($skills as $key => $skill) {
            $freelance_skills[] = $this->where('id', $skill->skill_id)->first();
        }
        return (!empty($freelance_skills)) ? $freelance_skills : false;
    }


}

?>