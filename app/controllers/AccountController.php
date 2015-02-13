<?php

class AccountController extends AppController{

	public $uses = array('Account', 'Freelance', 'Client', 'Skill', 'FreelanceSkill');

	public function __construct(){
		parent::__construct();
	}

	/**
	*	Authenticate and log client when receiving a POST request
	**/
	public function login()
	{
		if($this->request() == 'POST')
		{
			if($user = $this->Account->login($this->f3->get('POST')))
			{
				$this->Account->setSession($user);
				$this->setFlash("Authentification reussi.");
				$this->f3->reroute('/');
			}else{
				$this->setFlash("Les informations ne sont pas valides.");
				$this->f3->reroute($this->f3->get('PATTERN'));
			}
		}

		$this->render('accounts/login');
	}

	/**
	*	Logout client and destroy session
	**/
	public function logout()
	{
		$this->f3->clear('SESSION');
		$this->setFlash("Votre compte a bien été deconnecté.");
		$this->f3->reroute('/users/login');
	}

	/**
	*	Register a client using form and post data
	*	Create and save a new Account and his type (Freelance or Client)
	**/
	public function register()
	{
		$user = array();
		$errors = array();

		if($this->request() == 'POST')
		{
			$user = $this->f3->get('POST');

			if($newUser = $this->Account->register($user))
			{
				// initialize client or freelance special account
				if($newUser->type == "CLIENT")
					$this->Client->create(['account_id' => $newUser->id]);
				else if($newUser->type == "FREELANCE")
					$this->Freelance->create(['account_id' => $newUser->id]);

				$this->Account->setSession($newUser);
				$this->setFlash('Votre compte a été crée et vous avez automatiquement été connecté.');
				$this->f3->reroute('/users/profile');
			}else{
				$errors = $this->Account->errors;
			}
		}

		$type = $this->Account->getEnumValues('type');
		$this->render('accounts/register', compact('user', 'errors', 'type'));
	}

	/**
	*	Show user profile by ID or User Session ID
	**/
	public function profile($f3, $params = null)
	{
		// get user profile by ID or with session ID
		$user = $this->Account->find((!empty($params['id'])) ?
			$params['id'] :
			$this->f3->get('SESSION.user.id')
		);

		if(!empty($user))
		{
			if($user['type'] == "FREELANCE"){
				// freelance user info
				$experiences = $this->Freelance->getEnumValues('experience');
				$user['freelance'] = $user->freelance;
				$user['freelance']['skills'] = $this->Skill->getFromFreelanceSkills(
					$this->FreelanceSkill->getAll('account_id', $user->id));

			}else if($user['type'] == 'CLIENT'){
				// client user
				$user['client'] = $user->client;
			}

			if($user->id == $this->f3->get('SESSION.user.id'))
			{
				// render the profile editing view
				$this->render('accounts/edit',
					compact('user',(!empty($experiences) ? 'experiences' : '')));
			}else{
				// render the profile show view
				$this->render('accounts/show',
					compact('user',(!empty($experiences) ? 'experiences' : '')));
			}

		}else{
			$this->setFlash("Cet utilisateur n'existe pas.");
			$this->f3->reroute('/');
		}
	}

	/**
	*
	**/
	public function update_profile()
	{
		if($this->request() == 'POST')
		{
			$profile = $this->f3->get('POST');



			$userId = $this->f3->get('SESSION.user.id');
			$profile['account']['account_id'] = $userId;
			$profile['freelance']['account_id'] = $userId;
			$profile['client']['account_id'] = $userId;
			$type = $this->f3->get('SESSION.user.type');

			/*
				Upload working, have to configure view and BDD now
			$this->upload($this->f3, $userId);
			die();
			*/

			$this->Account->updateAccount($profile['account']);

			if($type == 'FREELANCE')
			{
				if($this->Freelance->updateProfile($profile['freelance']))
				{
					$skills = $this->Skill->explodeSkills($profile['freelance']['skills']);
					$this->FreelanceSkill->add($skills);
					$this->setFlash("Votre profil a bien été mis à jour.");
				}else{
					$this->setFlash("Certaines informations sont erronées");
					$errors = $this->Freelance->errors;
				}
			}else if($type == 'CLIENT'){
				if($this->Client->updateProfile($profile['client']))
				{
					$this->setFlash("Votre profil a bien été mis à jour.");
				}else{
					$this->setFlash("Certaines informations sont erronées");
					$errors = $this->Client->errors;
				}
			}
		}

		$this->f3->reroute('/users/profile');
	}

	public function upload($f3, $idUser)
	{
		$this->idUser = $idUser;
    	\Web::instance()->receive(
    		function($file)
    		{
    			var_dump($file);
    		},
    		true, 
    		function($BaseFileName)
    		{
				return ($this->idUser.'.'.(explode('.', $BaseFileName)[1]));
			}
    	);
 	}


}

?>