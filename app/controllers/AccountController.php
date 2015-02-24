<?php

class AccountController extends AppController
{

    public $uses = array('Account', 'Freelance', 'Client', 'Skill', 'FreelanceSkill', 'Project', 'Participate');

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Check if user is already log in
     */
    public function beforeroute()
    {
        parent::beforeroute();

        if ($this->Auth->isLogin()) {
            $request = $this->f3->get('PATTERN');
            if (in_array($request, ['/users/register', '/users/login'])) {
                $this->setFlash("Vous etes déjà authentifié.");
                $this->f3->reroute('/users/profile');
            }
        }
    }

    /**
     *    Authenticate and log client when receiving a POST request
     **/
    public function login()
    {
        if ($this->request() == 'POST') {
            if ($user = $this->Account->login($this->f3->get('POST'))) {
                $this->Account->setSession($user);
                $this->setFlash("Authentification reussi.");
                $this->f3->reroute('/');
            } else {
                $this->setFlash("Les informations ne sont pas valides.");
                $this->f3->reroute($this->f3->get('PATTERN'));
            }
        }

        $this->render('accounts/login');
    }

    /**
     *    Logout client and destroy session
     **/
    public function logout()
    {
        $this->f3->clear('SESSION');
        $this->setFlash("Votre compte a bien été deconnecté.");
        $this->f3->reroute('/users/login');
    }

    /**
     *    Register a client using form and post data
     *    Create and save a new Account and his type (Freelance or Client)
     **/
    public function register()
    {
        $user = array();
        $errors = array();

        if ($this->request() == 'POST') {
            $user = $this->f3->get('POST');

            if ($newUser = $this->Account->register($user)) {

                // initialize client or freelance special account
                if ($user['type'] == "CLIENT") {

                    $this->Client->create([
                        'account_id' => $newUser->id
                    ]);

                } else if ($user['type'] == "FREELANCE") {

                    $this->Freelance->create([
                        'account_id' => $newUser->id
                    ]);
                }

                $this->Account->setSession($newUser);
                $this->setFlash("Votre compte a été crée et vous avez automatiquement été connecté.");
                $this->f3->reroute('/users/profile');
            } else {
                $errors = $this->Account->errors;
            }
        }

        $type = $this->Account->getEnumValues('type');
        $this->render('accounts/register', compact('user', 'errors', 'type'));
    }

    /**
     *    Show user profile by ID or User Session ID
     **/
    public function profile()
    {

        $experiences = array();

        // get user profile by ID or with session ID
        $user = $this->Account->find((!empty($this->f3->get('PARAMS.id'))) ?
                $this->f3->get('PARAMS.id') :
                $this->f3->get('SESSION.user.id')
        );


        if (!empty($user)) {
            if ($user['type'] == "FREELANCE") {

                // freelance user info
                $user['freelance'] = $user->freelance;

                $experiences = $this->Freelance->getEnumValues('experience');
                $skills = $this->Skill->getFromFreelanceSkills(
                    $this->FreelanceSkill->getAll('account_id', $user->id));

                $user['freelance']['skills'] = $skills;

            } else if ($user['type'] == 'CLIENT') {

                // client user
                $user['client'] = $user->client;
            }

            if ($user->id == $this->f3->get('SESSION.user.id')) {
                // render the profile editing view
                $this->render('accounts/edit',
                    compact('user', (!empty($experiences) ? 'experiences' : '')));
            } else {
                // render the profile show view
                $this->render('accounts/show',
                    compact('user', (!empty($experiences) ? 'experiences' : '')));
            }

        } else {
            $this->setFlash("Cet utilisateur n'existe pas.");
            $this->f3->reroute('/');
        }
    }

    /**
     *
     **/
    public function update_profile()
    {
        if ($this->request() == 'POST') {
            $profile = $this->f3->get('POST');

            $userId = $this->f3->get('SESSION.user.id');
            $profile['account']['account_id'] = $userId;
            $profile['freelance']['account_id'] = $userId;
            $profile['client']['account_id'] = $userId;

            if (!empty($this->f3->get('FILES.picture.name'))) {
                $upload = new UploadHelper();
                $filename = $upload->upload();

                if ($filename) {
                    $profile['account']['picture'] = $this->f3->get('UPLOADS') . $filename;
                }
            }

            if ($this->Auth->is('freelance')) {
                if ($this->Account->updateAccount($profile['account'])
                    && $this->Freelance->updateProfile($profile['freelance'])
                ) {
                    $skills = $this->Skill->explodeSkills($profile['freelance']['skills']);
                    $this->FreelanceSkill->add($skills);
                    $this->setFlash("Votre profil a bien été mis à jour.");
                } else {
                    $this->setFlash("Certaines informations sont erronées");
                    if ($this->Account->errors) {
                        if ($this->Freelance->errors) {
                            $errors = array_merge($this->Account->errors, $this->Freelance->errors);
                        } else {
                            $errors = $this->Account->errors;
                        }
                    } else {
                        $errors = $this->Freelance->errors;
                    }
                }
            } else {
                if ($this->Auth->is('client')) {
                    if ($this->Account->updateAccount($profile['account'])
                        && $this->Client->updateProfile($profile['client'])
                    ) {
                        $this->setFlash("Votre profil a bien été mis à jour.");
                    } else {
                        $this->setFlash("Certaines informations sont erronées");
                        if ($this->Account->errors) {
                            if ($this->Client->errors) {
                                $errors = array_merge($this->Account->errors, $this->Client->errors);
                            } else {
                                $errors = $this->Account->errors;
                            }
                        } else {
                            $errors = $this->Client->errors;
                        }
                    }

                }
            }
        }

        $this->f3->reroute('/users/profile');
    }

    /**
     * Get user notifications
     * If user is client: get project demands
     */
    public function notification()
    {
        // get project demand if user is a client
        if ($this->Auth->is('client')) {

            $participations = $this->Participate->notification($this->Auth->getId(), 'client');

            if ($participations) {
                foreach ($participations as $key => $participation) {
                    $participations[$key]['freelance'] = $participation->account()->first();
                    $participations[$key]['project'] = $participation->project()->first();
                }
            }

        } else {
            if ($this->Auth->is('freelance')) {

                $participations = $this->Participate->notification($this->Auth->getId(), 'freelance');

            } else {
                $this->f3->reroute('/');
            }
        }

        $this->render('accounts/notification', compact('participations'));
    }

    /**
     * Show all freelance participation
     * Status ACCEPT or CHOOSE only
     */
    public function participations()
    {
        if ($this->Auth->is('freelance')) {

            $participations = $this->Participate->where('freelance_id', $this->Auth->getId())
                ->whereIn('participates.status', ['ACCEPT', 'CHOOSE'])
                ->join('projects', 'project_id', '=', 'id')
                ->orderBy('participates.created_at', 'desc')
                ->get(['projects.id', 'projects.name', 'participates.status', 'participates.updated_at']);

            $this->render('accounts/participation', compact('participations'));

        } else {
            $this->setFlash("Vous n'êtes pas Freelance");
            $this->f3->reroute('/users/profile');
        }

    }

}
