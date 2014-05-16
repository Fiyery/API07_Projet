<?php
/**
 * SingletonSession est une spécialisation de Singleton. Le Singleton est aussi sauvegardé en session.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.1
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Singleton
 * @uses Session
 */
class SingletonSession extends Singleton
{	
	// Constructeur.
	protected function __construct() 
	{

	}

	// Destructeur.
	public function __destruct()
	{
		$class = get_called_class();
		Session::get_instance()->$class = $class::$_instance;
	}
	
	// Fonction de chargement de l'instance unique.
	public static function get_instance() 
	{
		$class = get_called_class();
		if (property_exists($class,'_instance') == FALSE)
		{
			return NULL;
		}
		if ($class::$_instance === NULL)
		{
			$instance = Session::get_instance()->$class;
			if ($instance !== NULL)
			{
				$class::$_instance = $instance;
			}
			else 
			{
				$class::$_instance = $class::__create(func_get_args());
			}
		}
		return $class::$_instance;
	} 
	
	/**
	 * Réinitialise le singleton.
	 */
	public static function reset_instance()
	{
		$class = get_called_class();
		unset(Session::get_instance()->$class);
		parent::reset_instance();
	}
}
?>