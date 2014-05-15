<?php
class home
{
	public function default_action()
	{
		
	}
	
	public function connecter()
	{
		// Connexion et ouverture de la session.
		if ($this->session->is_open() == FALSE)
		{
			if (empty($this->req->login) || empty($this->req->pass))
			{
				$this->site->add_message("Votre identifiant ou mot de passe sont invalides", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'home/');
			}
			$users = User::search('email', $this->req->login);
			if (is_array($users) == FALSE && count($users) == 0)
			{
				$this->site->add_message("Votre identifiant ou mot de passe sont invalides", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'home/');
			}
			$pass = sha1(md5($this->req->pass));
			if ($users[0]->mdp != $pass)
			{
				$this->site->add_message("Votre identifiant ou mot de passe sont invalides", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'home/');
			}
			$this->session->open($users[0]);
			$this->site->add_message("Connexion réussie", Site::ALERT_OK);
		}
		$this->site->redirect($this->site->get_root().'fiche/');
	}
}
?>