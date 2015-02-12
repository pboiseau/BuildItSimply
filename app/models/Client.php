<?php

class Client extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'clients';
    protected $guarded = array('created_at', 'updated_at');

    public function project()
    {
        return $this->hasMany('Project', 'account_id', 'client_id');
    }

    public function coucou(){
        echo 'coucou';
    }

    public function updateProfile($client)
    {
        if (!$this->validate($client)) {
            return false;
        }

        if ($profile = $this->where('account_id', $client['account_id'])->first()) {
            // update client
            return $this->where('account_id', $client['account_id'])->update([
                'activity' => $client['activity']
            ]);
        } else {
            // create client
            $create = $this->create($client);
            return (!empty($create)) ? $create : false;
        }
    }

    /**
     * Check if data are valide
     * @param array data
     *
     * @return bool
     */
    private function validate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (!$validator->isString($data['activity'])) {
            $errors['url'] = "Votre activité est incorrect.";
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

}

?>