<?php

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
     * @param array $project
     * @return static
     */
    public function initialize($project = array())
    {
        $project['client_id'] = Base::instance()->get('SESSION.user.id');
        $project['status'] = 'OUVERT';
        return $this->create($project);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->where('id', $id)->get();
    }

    /**
     *    Validate mandatory information before save into database
     *    Fill errors property if needed
     * @param array $data
     * @return boolean
     **/
    private function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

}

