<?php

/**
 * Class model for manage Client table
 */
class Client extends AppModel
{
    public $timestamps = true;
    public $errors;

    protected $table = 'clients';
    protected $primaryKey = 'account_id';
    protected $guarded = array('created_at', 'updated_at');

    /**
     * Get projects of the client by his id
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project()
    {
        return $this->hasMany('Project', 'client_id', 'account_id');
    }

    /**
     * Get Client's data
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne('Account', 'id', 'account_id');
    }

    /**
     * Update client profile
     * @param $client
     * @return bool|static
     */
    public function updateProfile($client)
    {
        if (!$this->validate($client))
            return false;

        if ($profile = $this->where('account_id', $client['account_id'])->first()) {
            // update client
            return $this->where('account_id', $client['account_id'])->update([
                'activity' => $client['activity']
            ]);
        }

        /*else {
            // create client
            $create = $this->create($client);
            return (!empty($create)) ? $create : false;
        }*/
    }

    /**
     * Check if data are valide
     * @param array data
     * @return bool
     */
    private function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (!empty($data['activity']) && !$validator->isString($data['activity'])) {
            $errors['activity'] = "Votre activité est incorrect.";
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

}

?>