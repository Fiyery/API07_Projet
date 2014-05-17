<?php
class home
{
	public function default_action()
	{
		if ($this->session->is_open())
		{
			$this->site->add_message("Accès refusé", Site::ALERT_ERROR);
			if ($this->session->user->type == 'employee')
			{
				$this->site->redirect($this->site->get_root().'fiche/?id='.$this->session->user->id);
			}
			else 
			{
				$this->site->redirect($this->site->get_root().'fiche/');
			}
		}
	}
	
	public function connecter()
	{
		// Connexion et ouverture de la session.
		if ($this->session->is_open() == FALSE)
		{
			// Brute-force attack
			$bfa = BFA::get_instance();
			if ($bfa->wait())
			{
				// Sauvegarde de l'IP.
				if (isset($_SERVER['REMOTE_ADDR']))
				{
					$content = (file_exists('blacklist.txt')) ? (file_get_contents('blacklist.txt')) : ('');
					file_put_contents('blacklist.txt', $_SERVER['REMOTE_ADDR'].' : '.date('d/m/Y h:i:s')."\r\n");
				}
				$this->site->add_message("Vous devez patienter avant une nouvelle tentative", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'home/');
			}
			// Traitement connexion.
			if (empty($this->req->login) || empty($this->req->pass) || preg_match(Regex::MAIL, $this->req->login) == FALSE)
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
			$bfa->reset();
			$this->session->open($users[0]);
			$this->site->add_message("Connexion réussie", Site::ALERT_OK);
		}
		$this->site->redirect($this->site->get_root().'fiche/');
	}
	
	public function deconnecter()
	{
		$this->session->close();
		$this->site->add_message("Vous avez été déconnecté", Site::ALERT_OK);
		$this->site->redirect($this->site->get_root().'home/');
		
	}
}
?>