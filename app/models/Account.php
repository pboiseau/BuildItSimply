<?php

class Account extends AppModel
{

    public $timestamps = true;
    public $errors;

    protected $table = 'accounts';
    protected $guarded = array('id');

    private $projects = array();


    /**
     * @param $type
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type($type)
    {
        $type = strtoupper($type);

        if ($type == "FREELANCE") {
            return $this->hasOne('Freelance', 'account_id', 'id');
        } else {
            if ($type == "CLIENT") {
                return $this->hasOne('Client', 'account_id', 'id');
            }
        }
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function freelance()
    {
        return $this->hasOne('Freelance', 'account_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function client()
    {
        return $this->hasOne('Client', 'account_id', 'id');
    }

    /**
     * Send welcome mail
     * @param $template
     * @param $user
     */
    public function sendMail($template, $user)
    {
        $mailHelper = new MailHelper();
        $mailHelper->sendMail($template, $user->mail, [
            'subject' => "Bienvenue sur BuiltItSimply",
            'firstname' => $user->firstname,
            'lastname' => $user->lastname
        ]);
    }

    /**
     * Get account by ID and select field
     * @param $id
     * @param array $field
     * @return mixed
     */
    public function getById($id, $field = array('*'))
    {
        return $this->where($this->table . '.id', $id)
            ->first($field);
    }

    /**
     * Check the couple login/password for authenticate client
     * @param array $login
     * @return bool
     */
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
            $user['picture'] = Base::instance()->get('AVATAR_DEFAULT');

            $newUser = $this->create($user);
            return (!empty($newUser)) ? $newUser : false;
        } else {
            return false;
        }
    }

    /**
     * Get all projets of a client
     * Or get all projects build by the freelance
     * @param Account $user
     * @return array|bool
     */
    public function getProjects($user)
    {
        $userWithType = $user->type($user->type)->first();

        if ($user->type == "FREELANCE") {
            $participations = $userWithType->participates()->status('choosen')->get();

            $participations->each(function ($participation) {
                $project = $participation->project()->first();
                $project['tags'] = $project->tags()->get();
                $project['demand'] = $project->participates()->count();
                $this->projects[] = $project;
            });

        } else if ($user->type == "CLIENT") {
            $projects = $userWithType->project()->recent()->limit(4)->get();

            if($projects->count() > 0){
                $projects->each(function($project){
                    $project['tags'] = $project->tags()->get();
                    $project['demand'] = $project->participates()->count();
                });
                $this->projects = $projects;
            }
        }


        return (!empty($this->projects)) ? $this->projects : false;
    }

    /**
     * Update user account
     * @params array $user
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
     * Set user data in Session
     * @param array $user
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
     * Verify syntax of each field before update informations in database
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
        return (empty($errors)) ? true : false;
    }

}
