<?php
/**
 * Config est le manageur de paramètres.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.0
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Singleton
 */
class Config extends Singleton
{
	/**
	 * Variable d'instance de singleton.
	 * @var Config
	 */
	protected static $_instance = NULL;
	
	/**
	 * Instance de stdClass contenant les paramètres de config.json
	 * @var stdClass
	 */
	private $_json = NULL;
	
	/**
	 * Retourne un paramètre.
	 * @param string $name Nom du paramètre.
	 */
	public function __get($name)
	{
		return $this->_json->$name;
	}
	
	/**
	 * Vérifie l'existance d'une variable.
	 * @param string $name Nom de la variable.
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->_json->$name);
	}
	
	/**
	 * Intialise les paramètres.
	 * @param string $file Adresse du ficheir de configuration.
	 * @return boolean
	 */
	public function set_json($file)
	{
		if (file_exists($file) == FALSE)
		{
			return FALSE;
		}
		$this->_json = json_decode(file_get_contents($file));
		return (is_object($this->_json));
	}
}
?>