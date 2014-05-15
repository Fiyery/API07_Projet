<?php
/**
 * ControllerManager gère le chargement de controller spécfique.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.0
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Singleton
 * @uses View
 */
class ControllerManager extends Singleton
{		
	/**
	 * Variable d'instance de singleton.
	 * @var Config
	 */
	protected static $_instance = NULL;
	
	/**
	 * Nom du controller.
	 * @var string
	 */
	private $_controller = NULL;
	
	/**
	 * Nom de l'action
	 * @var string
	 */
	private $_action = NULL;
	
	/**
	 * Dossier des controlleurs.
	 * @var string
	 */
	private $_controllers_dir = NULL;
	
	/**
	 * Dossier des vues.
	 * @var string
	 */
	private $_views_dir = NULL;
	
	/**
	 * Liste des paramaètres à transmettre au controlleurs.
	 * @var array
	 */
	private $_params = array();
	
	
	/**
	 * Connecteur des valeurs du controlleur à passer à la vue.
	 * @var View
	 */
	private $_views = NULL;
	
	/**
	 * Constructeur
	 */
	protected function __construct()
	{
		
	}
	
	/**
	 * Définie le dossier des controlleurs.
	 * @param string $dir Chemin du dossier.
	 */
	public function set_controllers_dir($dir)
	{
		$this->_controllers_dir = (substr($dir, -1) != '/') ? ($dir.'/') : ($dir);	
	}
	
	/**
	 * Définie le dossier des vues.
	 * @param string $dir Chemin du dossier.
	 */
	public function set_views_dir($dir)
	{
		$this->_views_dir = (substr($dir, -1) != '/') ? ($dir.'/') : ($dir);
	}
	
	/**
	 * Définie les paramètres à transmettres au controlleur.
	 * @param array $params Liste des paramètres.
	 */
	public function set_params($params)
	{
		$this->_params = $params;
	}
	
	/**
	 * Définie l'objet de liaison des valeurs à transmettre à la vue.
	 * @param View $view Liste des paramètres.
	 */
	public function set_view(View $view)
	{
		$this->_view = $view;
	}
	
	/**
	 * Charge un controller spécifique.
	 * @param string $controller Nom du module.
	 * @param string $action Nom de l'action.
	 */
	public function load($controller, $action)
	{
		$this->_controller = $controller;
		$this->_action = $action;
	}
	
	/**
	 * Lance le controller.
	 * return boolean
	 */
	public function execute()
	{
		$file = $this->_controllers_dir.$this->_controller.'.php';
		if (file_exists($file) == FALSE)
		{
			return FALSE;
		}
		require($file);
		$class = $this->_controller;
		$controller = new $class();
		if (method_exists($controller, $this->_action) == FALSE)
		{
			return FALSE;
		}
		foreach ($this->_params as $name => $value)
		{
			$controller->$name = $value;
		}
		$method =  $this->_action;
		$controller->$method();
		return TRUE;
	}
	
	/**
	 * Retourne le contenu du controller.
	 * @param View $view Instance de liste de paramètre vers la template.
	 * return string
	 */
	public function show()
	{
		$file = $this->_views_dir.$this->_controller.'-'.$this->_action.'.php';
		if (file_exists($file) == FALSE)
		{
			return FALSE;
		}
		$list_values = $this->_view->get();
		foreach ($list_values as $name => $value)
		{
			$$name = $value;
		}
		ob_start();
		require($file);
		return ob_get_clean();
	}
}

?>