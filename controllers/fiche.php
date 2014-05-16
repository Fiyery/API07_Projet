<?php
class fiche 
{
	public function default_action()
	{
		if ($this->session->is_open() == FALSE)
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'home/');
		}
		if ($this->session->user->type == 'employee')
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/afficher/?id='.$this->session->user->id);
		}
		$list = User::search();
		$fiches = array();
		foreach ($list as $f)
		{
			$fiches[] = array(
				'nom' => $f->nom,
				'prenom' => $f->prenom,
				'id' => $f->id
			);
		}
		$this->view->fiches = $fiches;
	}
	
	public function afficher()
	{
		if ($this->session->is_open() == FALSE)
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'home/');
		}
		if (empty($this->req->id) || is_numeric($this->req->id) == FALSE)
		{
			$this->site->add_message("Aucun employé trouvé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$id = $this->req->id;
		if ($this->session->user->type == 'employee' && $id != $this->session->user->id)
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/?id='.$this->session->user->id);
		}
		if ($id != $this->session->user->id)
		{
			$user = User::load($id);
			if (is_object($user) == FALSE)
			{
				$this->site->add_message("Aucun employé trouvé", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'fiche/');
			}
		}
		else
		{
			$user = $this->session->user;
		}
		if (in_array($user->type, array('medecin', 'employee', 'secretaire')) == FALSE)
		{
			$this->site->add_message("Employé invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}		
		if ($this->session->user->type == 'secretaire' || $this->session->user->type == 'employee')
		{
			$this->view->fiche_tel_disabled = "name='tel'";
			$this->view->fiche_email_disabled = "name='email'";
			$this->view->fiche_civilite_disabled = FALSE;
		}
		else
		{
			$vaccins = Vaccination::search();
			$user_vaccins = User_Vaccination::search();
			$illness = Maladie::search();
			$user_illness = User_Maladie::search();
			$vaccins_array = array();
			$user_vaccins_array = array();
			$illness_array = array();
			$user_illness_array = array();
			if (is_array($vaccins))
			{
				$vaccins_array = array();
				$user_vaccins_array = array();
				if (is_array($user_vaccins))
				{
					$root = $this->site->get_root();
					foreach ($user_vaccins as $uv)
					{
						foreach ($vaccins as $v)
						{
							if ($uv->id_vaccination == $v->id)
							{
								$find = TRUE;
								$uv->link = $root.'fiche/supprimer_vaccin/?id='.$v->id.'&id_user='.$user->id.'&time='.$uv->time;
								$uv->nom = $v->nom;
							}
						}
						$user_vaccins_array[] = $uv;
					}
				}
				$vaccins_array = $vaccins;
			}
			if (is_array($illness))
			{
				$illness_array = array();
				$user_illness_array = array();
				if (is_array($user_illness))
				{
					$root = $this->site->get_root();
					foreach ($user_illness as $uv)
					{
						foreach ($illness as $v)
						{
							if ($uv->id_maladie == $v->id)
							{
								$find = TRUE;
								$uv->link = $root.'fiche/supprimer_maladie/?id='.$v->id.'&id_user='.$user->id.'&time='.$uv->time;
								$uv->nom = $v->nom;
							}
						}
						$user_illness_array[] = $uv;
					}
				}
				$illness_array = $illness;
			}
			$this->view->fiche_vaccins = $vaccins_array;
			$this->view->fiche_user_vaccins = $user_vaccins_array;
			$this->view->fiche_illness = $illness_array;
			$this->view->fiche_user_illness = $user_illness_array;
			$this->view->fiche_tel_disabled = "disabled='disabled'";
			$this->view->fiche_email_disabled = "disabled='disabled'";
			$this->view->fiche_civilite_disabled = TRUE;
		}
		$this->view->fiche_nom = $user->nom;
		$this->view->fiche_prenom = $user->prenom;
		$this->view->fiche_tel = $user->tel;
		$this->view->fiche_civilite = $user->civilite;
		$this->view->fiche_email = $user->email;
		$this->view->fiche_id = $user->id;
		$this->view->user_type = $user->type;
	}
	
	public function modifier()
	{
		if ($this->session->is_open() == FALSE)
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'home/');
		}
		$id = $this->req->id;
		if ($id != $this->session->user->id)
		{
			$user = User::load($id);
			if (is_object($user) == FALSE)
			{
				$this->site->add_message("Aucun employé trouvé", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'fiche/');
			}
		}
		else
		{
			$user = $this->session->user;
		}
		if ($this->session->user->type == 'secretaire' || $this->session->user->type == 'employee')
		{
			$modifs = array();
			$tel = $this->req->tel;
			if ($tel != $user->tel)
			{
				if (empty($tel) == FALSE && preg_match('#^\d{10}$#', $tel))
				{
					$this->site->add_message("Téléphone modifié", Site::ALERT_OK);
					$modifs['tel'] = $tel;
				}
				else
				{
					$this->site->add_message("Téléphone invalide", Site::ALERT_ERROR);
				}
			}
			$email = $this->req->email;
			if ($email != $user->email)
			{
				if (empty($email) == FALSE && filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$this->site->add_message("Email modifiée", Site::ALERT_OK);
					$modifs['email'] = $email;
				}
				else
				{
					$this->site->add_message("Email invalide", Site::ALERT_ERROR);
				}
			}
			$civilite = $this->req->civilite;
			if ($civilite != $user->civilite)
			{
				if (empty($civilite) == FALSE && in_array($civilite, array('M', 'Mme', 'Mlle')))
				{
					$this->site->add_message("Civilite modifiée", Site::ALERT_OK);
					$modifs['civilite'] = $civilite;
				}
				else
				{
					$this->site->add_message("Civilité invalide", Site::ALERT_ERROR);
				}
			}
			if (count($modifs) > 0)
			{
				$user->modify($modifs);
			}
			else 
			{
				$this->site->add_message("Aucune modification apportée", Site::ALERT_WARNING);
			}
		}
		$this->site->redirect($this->site->get_root().'fiche/afficher/?id='.$user->id);
	}

	public function supprimer_vaccin()
	{
		if ($this->session->is_open() == FALSE || $this->session->user->type != 'medecin')
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		if (empty($this->req->id) || empty($this->req->id_user) || empty($this->req->time))
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$user_vaccin = User_Vaccination::load($this->req->id_user, $this->req->id, $this->req->time);
		if (is_object($user_vaccin) == FALSE)
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		User_Vaccination::delete(array($this->req->id_user, $this->req->id, $this->req->time));
		$this->site->add_message("Suppression du vaccin réussite", Site::ALERT_OK);
		$this->site->redirect($this->site->get_root().'fiche/afficher/?id='.$this->req->id_user);
	}
	
	public function ajouter_vaccin()
	{
		if ($this->session->is_open() == FALSE || $this->session->user->type != 'medecin')
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		if (empty($this->req->id_user) || empty($this->req->id_vaccination) || empty($this->req->date))
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$id_user = $this->req->id_user;
		if (is_numeric($id_user) == FALSE || is_object(User::load($id_user)) == FALSE)
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$id_vac = $this->req->id_vaccination;
		if (is_numeric($id_vac) == FALSE || is_object(Vaccination::load($id_vac)) == FALSE)
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$date = $this->req->date;
		if (preg_match(Regex::FRENCH_DATE, $date) == FALSE || Regex::check_date($date) == FALSE)
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$date = implode('-', array_reverse(explode('/', $date))).' 00:00:00';
		User_Vaccination::add(array($this->req->id_user, $this->req->id_vaccination, $date));
		$this->site->add_message("Ajout du vaccin réussite", Site::ALERT_OK);
		$this->site->redirect($this->site->get_root().'fiche/afficher/?id='.$this->req->id_user);
	}
	
	public function supprimer_maladie()
	{
		if ($this->session->is_open() == FALSE || $this->session->user->type != 'medecin')
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		if (empty($this->req->id) || empty($this->req->id_user) || empty($this->req->time))
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$user_illness = User_Maladie::load($this->req->id_user, $this->req->id, $this->req->time);
		if (is_object($user_illness) == FALSE)
		{
			$this->site->add_message("Information invalide", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		User_Maladie::delete(array($this->req->id_user, $this->req->id, $this->req->time));
		$this->site->add_message("Suppression du maladie réussite", Site::ALERT_OK);
		$this->site->redirect($this->site->get_root().'fiche/afficher/?id='.$this->req->id_user);
	}
	
	public function ajouter_maladie()
	{
		if ($this->session->is_open() == FALSE || $this->session->user->type != 'medecin')
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		if (empty($this->req->id_user) || empty($this->req->id_maladie) || empty($this->req->date))
		{
			$this->site->add_message("Information invalide 1", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$id_user = $this->req->id_user;
		if (is_numeric($id_user) == FALSE || is_object(User::load($id_user)) == FALSE)
		{
			$this->site->add_message("Information invalide 2", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$id_mal = $this->req->id_maladie;
		if (is_numeric($id_mal) == FALSE || is_object(Maladie::load($id_mal)) == FALSE)
		{
			$this->site->add_message("Information invalide 3", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$date = $this->req->date;
		if (preg_match(Regex::FRENCH_DATE, $date) == FALSE || Regex::check_date($date) == FALSE)
		{
			$this->site->add_message("Information invalide 4", Site::ALERT_ERROR);
			$this->site->redirect($this->site->get_root().'fiche/');
		}
		$date = implode('-', array_reverse(explode('/', $date))).' 00:00:00';
		User_Maladie::add(array($this->req->id_user, $this->req->id_maladie, $date));
		$this->site->add_message("Ajout du maladie réussite", Site::ALERT_OK);
		$this->site->redirect($this->site->get_root().'fiche/afficher/?id='.$this->req->id_user);
	}
	
}
?>