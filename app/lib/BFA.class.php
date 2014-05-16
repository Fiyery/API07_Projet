<?php
/**
 * BFA (Brut-Force Attack) protège contre les attaques massives.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.0
 * @copyright 2011-2014 Yoann Chaumin
 * @uses SingletonSession
 */
class BFA extends SingletonSession
{
	/**
	 * Instance de singleton.
	 * @var unknown
	 */
	protected static $_instance = NULL;
	
	/**
	 * Temps de la dernière génération en timestamp.
	 * @var int
	 */
	private $_time;
	
	/**
	 * Compteur d'accès.
	 * @var int
	 */
	private $_count;
	
	/**
	 * Limite d'accès.
	 * @var int
	 */
	private $_limit_count = 5;
	
	/**
	 * Constructeur.
	 */
	protected function __construct()
	{
		$this->_time = time();
		$this->_count = 1;
	}
	
	/**
	 * Vérifie s'il faut attentre pour exécuter la requête.
	 * @return boolean
	 */
	public function wait()
	{
		if ($this->_count >= $this->_limit_count)
		{
			$seconds = (($this->_count - $this->_limit_count)*2 + 1) * 60;
			if ($this->_time + $seconds > time())
			{
				return TRUE;
			}
		}
		if ($this->_time + 60 > time())
		{
			$this->_count++;
			$this->_time = time();
		}
		else
		{
			$this->reset();
		}
		$this->_count++;
		return FALSE;
	}	
	
	/**
	 * Réinitialise le bloqueur de force brute.
	 */
	public function reset()
	{
		$this->_count = 1;
		$this->_time = time();
	}
}