<?php

class ProjectStep extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'project_step';
    protected $guarded = array('id');

    /**
     * @param $project_type
     * @param $step
     * @return bool
     */
    public function exists($project_type, $step)
    {
        $project_step = $this->where('step', $step)->where('project_type_id', $project_type)->first();
        return (!empty($project_step)) ? true : false;
    }

    /**
     * @param $step
     * @param $type
     * @return bool
     */
    public function changeStep($step, $type)
    {
        $questions = $this->where('project_type_id', $type)
            ->where('step', $step)
            ->join('project_question', 'project_question_id', '=', 'project_question.id')
            ->join('project_response', 'project_response.question_id', '=', 'project_question.id')
            ->get([
                'project_question.id AS project_id',
                'project_question.question',
                'project_response.id AS response_id',
                'project_response.response',
                'project_response.description',
                'project_response.image',
            ]);

        return ($questions->count() > 0) ? $questions : false;
    }

}
