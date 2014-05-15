<?php
class admin
{
	public function default_action()
	{
		if ($this->session->is_open())
		{
			$this->site->redirect($this->site->get_root().'admin/panel/');
		}
	}
	
	public function panel()
	{
		// Connexion et ouverture de la session.
		if ($this->session->is_open() == FALSE)
		{
			if (empty($this->req->login) || empty($this->req->pass))
			{
				$this->site->add_message("Votre identifiant ou mot de passe sont invalides", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'admin/');
			}
			$admins = Administrator::search('pseudo', $this->req->login);
			if (is_array($admins) == FALSE && count($admins) == 0)
			{
				$this->site->add_message("Votre identifiant ou mot de passe sont invalides", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'admin/');
			}
			$pass = sha1(md5($this->req->pass));
			if ($admins[0]->pass != $pass)
			{
				$this->site->add_message("Votre identifiant ou mot de passe sont invalides", Site::ALERT_ERROR);
				$this->site->redirect($this->site->get_root().'admin/');
			}
			$this->session->open($admins[0]);
			$this->site->add_message("Connexion réussie", Site::ALERT_OK);
		}
		$menus = Menu::search(NULL, NULL, NULL, NULL, array('ASC'=>array('order', 'name')));
		$sous_menus = Sous_Menu::search(NULL, NULL, NULL, NULL, array('ASC'=>array('order', 'name')));
		foreach ($menus as $m)
		{
			$link_list[] = array(
				'name' => $m->name,
				'order'=> $m->order,
				'id' => $m->id
			);
		}
	}
}
?>