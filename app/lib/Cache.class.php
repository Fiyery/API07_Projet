<?php
/**
 * Cache est le système de mise en cache du traitement des variables du site.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.1
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Singleton
 */
class Cache extends Singleton
{	
	/**
	 * Nombre de seconde par minute.
	 * @var int
	 */
	const MINUTE = 60;
	
	/**
	 * Nombre de seconde par heure.
	 * @var int
	 */
	const HOUR = 3600;
	
	/**
	 * Nombre de seconde par jour.
	 * @var int
	 */
	const DAY = 86400;
	
	/**
	 * Nombre de seconde par semaine.
	 * @var int
	 */
	const WEEK = 604800;
	
	/**
	 * Nombre de seconde par mois.
	 * @var int
	 */
	const MONTH = 2592000;
	
	/**
	 * Nombre de seconde par année.
	 * @var int
	 */
	const YEAR = 31557600;
	
	/**
	 * Instance de singleton.
	 * var Cache
	 */
	protected static $_instance = NULL;
	
	/**
	 * Définie si la classe est active ou non.
	 * @var boolean
	 */
	private $_enabled = FALSE;
	
	/**
	 * Nom du fichier de cache.
	 * @var string
	 */
	private $_file = NULL;
	
	/**
	 * Nom du dossier de cache.
	 * @var string
	 */
	private $_dir = NULL;
	
	/**
	 * Tableau contenant la liste des informations à sauvegarder.
	 * @var array
	 */
	private $_vars = array();

	/**
	 * Constructeur.
	 */
	protected function __construct($dir)
	{
	    if (file_exists($dir) == FALSE)
	    {
	    	mkdir($dir,0755,TRUE);
	    }
	    $this->_dir = (substr($dir,-1) != '/') ? ($dir.'/') : ($dir);
		if (file_exists($this->_dir) == FALSE)
		{
			mkdir($this->_dir, 0755, TRUE);
		}
	}

	/**
	 * Tente de récuréper un cache en fonction de son nom.
	 * @param string $name Nom du fichier du cache.
	 * @param number $duration Nombre de seconde de mise en cache.
	 * @return array Un tableau de variables est retourné ou NULL si le cache n'existe pas ou est expiré.
	 */
	public function read($name, $duration=self::HOUR)
	{
	    if (is_string($name) == FALSE || is_numeric($duration) == FALSE)
	    {
	    	throw new Exception('Invalid Arguments');
	    }
	    $this->_file = $name.'.tmp';
	    if ($this->is_enabled())
		{
			$file = $this->_dir.$this->_file;
			if (is_readable($file) == FALSE)
			{
				return NULL;
			}
			$lifefile = time()-filemtime($file);
			if ($lifefile > $duration)
			{
				$this->clean();
				return NULL;
			}
			$fp = fopen($file, 'r');
			$content = fread($fp, filesize($file));
			fclose($fp);
			return unserialize($content);
		}
		return NULL;
	}

	/**
	 * Sauvegarde les variables courantes en cache.
	 * @return boolean
	 */
	public function write()
	{
		if ($this->_file != NULL)
		{
			$file = $this->_dir.$this->_file;
			$fp = fopen($file, 'w');
			$res = fwrite($fp, serialize($this->_vars));
			fclose($fp);
			return ($res !== FALSE) ? (TRUE) : (FALSE);
		}
		return FALSE;
	}

	/**
	 * Ajoute une variable de cache.
	 * @param string $name Nom de la variable.
	 * @param mixed $value Valeur de la variable.
	 * @return boolean
	 */
	public function add($name, $value)
	{
		if ($this->_file != NULL)
		{
			$this->_vars[$name] = $value;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Supprime le cache courant.
	 * @return boolean
	 */
	public function clean()
	{
		if ($this->_file != NULL)
		{
			$file = $this->_dir.$this->_file;
			return (file_exists($file)) ? (unlink($file)) : (FALSE);
		}
		return FALSE;
	}

	/**
	 * Retourne le dossier de sauvegarde des caches.
	 * @return string Chemin du dossier.
	 */
	public function get_dir()
	{
		return $this->_dir;
	}
	
	/**
	 * Définie un dossier personnalisé pour le cache courant, doit être appelée avant write().
	 * @param string $dir Nouveau chemin du dossier.
	 */
	public function set_dir($dir)
	{
		if (file_exists($dir) == FALSE)
		{
			mkdir($dir,0755,TRUE);
		}
		$this->_dir = (substr($dir,-1) != '/') ? ($dir.'/') : ($dir);
	}
	
	/**
	 * Supprime tous les caches du dossier courant.
	 * @return boolean
	 */
	public function clean_all()
	{
		$caches = scandir($this->_dir);
		$return = TRUE;
		foreach ($caches as $c)
		{
			$return = $return && unlink($this->_dir.$c);
		}
		return $return;
	}
	
	/**
	 * Active l'activité de la classe.
	 */
	public function enable()
	{
		$this->_enabled = TRUE;
	}
	
	/**
	 * Désactive l'activité de la classe.
	 */
	public function disable()
	{
		$this->_enabled = FALSE;
	}
	
	/**
	 * Vérifie si la classe est en activité.
	 * @return boolean TRUE si la classe est active sinon FALSE.
	 */
	public function is_enabled()
	{
		return $this->_enabled;
	}
	
	/**
	 * Retourne une instance de la classe avec les arguments correctement ordonnés selon le constructeur de la classe.
	 * @param array $args Tableau d'arguments du constructeur.
	 * @return Cache
	 */
	protected static function __create($args)
	{
		return new self($args[0]);
	}
}
?>