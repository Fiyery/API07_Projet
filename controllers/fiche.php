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
		$this->session->user->type = 'secretaire';
		if ($this->session->user->type == 'secretaire' || $this->session->user->type == 'employee')
		{
			$this->view->fiche_tel_disabled = "name='tel'";
			$this->view->fiche_email_disabled = "name='email'";
			$this->view->fiche_civilite_disabled = FALSE;
		}
		else
		{
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
}
?>