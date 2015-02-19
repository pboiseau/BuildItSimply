<?php

class Account extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'accounts';
    protected $guarded = array('id');

    public function freelance()
    {
        return $this->hasOne('Freelance', 'account_id', 'id');
    }

    public function client()
    {
        return $this->hasOne('Client', 'account_id', 'id');
    }

    /**
     *
     **/
    public function login($login = array())
    {
        $user = $this->where('mail', $login['email'])
            ->where('password', $this->hash($login['password']))
            ->first();

        return (!empty($user)) ? $user : false;
    }

    /**
     *    Register a new account
     * @param $user
     * @internal param array $use
     *
     * @return bool|static
     */
    public function register($user)
    {
        // check valide user
        if ($this->validate($user)) {
            unset($user['repeatpassword']);

            $user['firstname'] = ucfirst(strtolower($user['firstname']));
            $user['lastname'] = ucfirst(strtolower($user['lastname']));
            $user['password'] = $this->hash($user['password']);

            $newUser = $this->create($user);
            return (!empty($newUser)) ? $newUser : false;
        } else {
            return false;
        }
    }

    /**
     *
     **/
    public function updateAccount($user)
    {
        if (($this->find($user['account_id'])) && ($this->validateUpdate($user))) {
            return $this->where('id', $user['account_id'])->update(
                $this->generateUpdate($user)
            );
        }
        return false;
    }

    /**
     *
     **/
    public function setSession($user)
    {
        Base::instance()->set('SESSION.user', [
            'id' => $user['id'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'type' => $user['type']
        ]);
    }

    /**
     *    Generate array structure of an Account in order to save data
     *    Unset all missing field to avoid destroy them when data will be updated
     * @param array data
     * @return array $data restructured
     **/
    private function generateUpdate($data = array())
    {
        unset($data['account_id']);

        if (empty($data['phone'])) {
            unset($data['phone']);
        }

        if (empty($data['city'])) {
            unset($data['city']);
        }

        if (empty($data['description'])) {
            unset($data['description']);
        }

        if (empty($data['picture'])) {
            unset($data['picture']);
        }

        if (empty($data['lat']) || empty($data['lng'])) {
            unset($data['lat'], $data['lng']);
        }

        return $data;
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

        if (!$validator->email($data['mail'])) {
            $errors['mail'] = 'Adresse mail invalide.';
        }

        if (!$validator->isString($data['lastname'], 100)) {
            $errors['lastname'] = 'Nom invalide.';
        }

        if (!$validator->isString($data['firstname'], 100)) {
            $errors['firstname'] = 'Prenom invalide.';
        }

        if (!$validator->isPassword($data['password'], 15, 25)) {
            $errors['password'] = "Votre mot de passe doit faire entre 8 et 25 caractères.";
        }

        if (strcmp($data['password'], $data['repeatpassword']) != 0) {
            $errors['password'] = "Les mots de passe ne correspondent pas.";
        }

        if ($this->where('mail', '=', $data['mail'])->first()) {
            $errors['mail'] = 'Adresse mail déjà utilisé.';
        }

        $this->errors = $errors;
        return (empty($errors)) ? true : false;
    }

    /**
     *    Verify syntax of each field before update informations in database
     * @param array $data
     * @return boolean
     **/
    private function validateUpdate($data = array())
    {
        $validator = new Validate();
        $errors = array();

        if (!empty($data['phone']) && !$validator->isPhone($data['phone'], 16)) {
            $errors['phone'] = 'Téléphone invalide.';
        }

        $this->errors = $errors;
        print_r($this->errors);
        return (empty($errors)) ? true : false;
    }

}

?>