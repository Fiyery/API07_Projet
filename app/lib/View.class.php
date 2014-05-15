<?php
/**
 * View permet le transfert des variables du controller à la vue.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.0
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Singleton
 */
class View extends Singleton
{
	/**
	 * Variable d'instance de singleton.
	 * @var View
	 */
	protected static $_instance = NULL;
	
	/**
	 * Liste des paramètres à transmettre.
	 * @var array
	 */
	private $_values = array();
	
	/**
	 * Retourne la liste des paramètres.
	 * @return array
	 */
	public function get()
	{
		return $this->_values;
	}
	
	/**
	 * Ajoute d'une valeur au template.
	 * @param string $name Nom de la variable.
	 * @param string $value Valeur de la variable.
	 */
	public function __set($name, $value)
	{
		$this->_values[$name] = $value;
	}
	
	/**
	 * Returne une valeur au template.
	 * @param string $name Nom de la variable.
	 * @return string Valeur de la variable.
	 */
	public function __get($name)
	{
		return $this->_values[$name];
	}
}
?>