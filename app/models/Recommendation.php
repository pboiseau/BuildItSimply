<?php

/**
 * Class for use Recommendation table
 */
class Recommendation extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'recommendations';
    protected $fillable = array('project_id', 'client_id', 'freelance_id', 'grade', 'message');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('Account', 'client_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function freelance()
    {
        return $this->belongsTo('Account', 'freelance_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Project', 'project_id', 'id');
    }

}