<?php
/**
 * Site est la class qui gère les actions primaires du site telles que l'affichage de messages, la modification du titre de la page ou les redirections.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.3
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Session
 * @uses Template
 * @uses Singleton
 * @uses Request
 */
class Site extends Singleton
{
	/**
	 * Constant qui définie le format de retoure de la racine comme adresse url.
	 * @var int
	 */
	const URL = 1;
	
	/**
	 * Constant qui définie le format de retoure de la racine comme chemin de fichier.
	 * @var int
	 */
	const DIR = 2;
	
	/**
	 * Constant qui définie le format d'un message.
	 * @var int
	 */
	const ALERT_INFO = 1;
	
	/**
	 * Constant qui définie le format d'un message.
	 * @var int
	 */
	const ALERT_OK = 2;
	
	/**
	 * Constant qui définie le format d'un message.
	 * @var int
	 */
	const ALERT_WARNING = 3;
	
	/**
	 * Constant qui définie le format d'un message.
	 * @var int
	 */
	const ALERT_ERROR = 4;

	/**
	 * Variable d'instance de singleton.
	 * @var Site
	 */
	protected static $_instance = NULL;

	/**
	 * Tableau contenant les racines URL et DIR du site.
	 * @var array<string>
	 */
	private static $_root = NULL;

	/**
	 * Variable de session pour les messages.
	 * @var Session
	 */
	private $_session = NULL;

	/**
	 * Nom du module du site.
	 * @var string
	 */
	private $_module = NULL;

	/**
	 * Nom de l'action du site.
	 * @var string
	 */
	private $_action = NULL;

	/**
	 * Nom par défaut du module.
	 * @var string
	 */
	private $_default_module = NULL;

	/**
	 * Nom par défaut de l'action
	 * @var string
	 */
	private $_default_action = NULL;
	
	/**
	 * Nom de la variable du template pour le titre du site.
	 * @var string
	 */
	private $_tpl_name_title = NULL;
	
	/**
	 * Nom de la variable du template pour la description du site.
	 * @var string
	 */
	private $_tpl_name_description = NULL;
	
	/**
	 * Constructeur.
	 */
	protected function __construct()
	{
		$this->_session = Session::get_instance();
		self::$_root = array();
	}
	
	/**
	 * Définie le titre de la page.
	 * @param string $title Titre de la page.
	 * @param string $name Nom de la variable du template.
	 */
	public function set_title($title, $name=NULL)
	{
	    if ($name == NULL)
	    {
	        $name = $this->_tpl_name_title;
	    }
		Template::get_instance()->assign($name, $title);
	}
	
	/**
	 * Définie la description de la page.
	 * @param string $description Description de la page.
	 * @param string $name Nom de la variable du template.
	 */
	public function set_description($description, $name=NULL)
	{
	    if ($name == NULL)
	    {
	    	$name = $this->_tpl_name_description;
	    }
		Template::get_instance()->assign($name, $description);
	}
	
	/**
	 * Définie le module par défaut.
	 * @param string $module Nom du module par défaut.
	 */
	public function set_default_module($module)
	{
		$this->_default_module = $module;
	}
	
	/**
	 * Définie l'action par défaut.
	 * @param string $action Nom du l'action par défaut.
	 */
	public function set_default_action($action)
	{
		$this->_default_action = $action;
	}
	
	/**
	 * Définie le nom de la variable du template pour le titre.
	 * @param string $name Nom de la variable.
	 */
	public function set_tpl_name_title($name)
	{
	    $this->_tpl_name_title = $name;
	}
	
	/**
	 * Définie le nom de la variable du template pour la description.
	 * @param string $name Nom de la variable.
	 */
	public function set_tpl_name_description($name)
	{
		$this->_tpl_name_description = $name;
	}
	
	/**
	 * Retourne le module courant.
	 * @param boolean $complete Si TRUE, retourne le dossier du sous-domaine avec.
	 * @return string Nom du module.
	 */
	public function get_module($complete = FALSE)
	{
		if ($this->_module == NULL)
		{
			$request = Request::get_instance();
			// Vérifie si un paramètre de module est passé, sinon module par défaut.
			$module = ($request->module != '') ? ($request->module) : ($this->_default_module);
			$prefixe = basename(getcwd()) . '/';
			if ($prefixe == basename(self::get_root(self::DIR)) . '/')
			{
				$prefixe = '';
			}
			$this->_module = $prefixe . $module;
		}
		if ($complete == FALSE)
		{
			$module = $this->_module;
			$pos = strripos($module, '/');
			if ($pos !== FALSE)
			{
				$module = substr($module, $pos + 1);
			}
			return $module;
		}
		return $this->_module;
	}
	
	/**
	 * Retourne l'action courante.
	 * @return string Valeur de l'action courante.
	 */
	public function get_action()
	{
		if ($this->_action == NULL)
		{
			$request = Request::get_instance();
			$this->_action = ($request->action != '') ? ($request->action) : ($this->_default_action);
		}
		return $this->_action;
	}
	
	/**
	 * Retourne le tableau de sous-domaine du site.
	 * @return array<string> Liste des sous-domaines.
	 */
	public function get_subdomains()
	{
		$dir = Site::get_root('DIR') . 'subdomains/';
		$subdomains = array();
		if (file_exists($dir))
		{
			$files = array_diff(scandir($dir), array(
				'.',
				'..'
			));
			foreach($files as $f)
			{
				if ($dir . $f)
				{
					$subdomains[] = $f;
				}
			}
		}
		return $subdomains;
	}
	
	/**
	 * Retourne l'arborescence de la page actuelle.
	 * @param array $name Tableau contenant la concordance entre les modules et actions et leur nouveau nom.
	 * @return string
	 */
	public function get_tree($name)
	{
		$action = $this->get_action();
		$module = $this->get_module(TRUE);
		$sepatator = "<span class='separator'>></span>";
		$root = Site::get_root();
		$way = "<a href='" . $root . "' title='Accueil du site'>Accueil</a>";
		$position = strpos($module, '/');
		if ($position === FALSE) // Cas normal.
		{
			if ($module != $this->_default_module || $action != $this->_default_action)
			{
				$name_module = (isset($name[$module]['name'])) ? ($name[$module]['name']) : ($module);
				$way .= ' ' . $sepatator . " <a href='" . $root . "?module=" . $module . "'>" . ucfirst($name_module) . "</a>";
				if ($action != $this->_default_action)
				{
					$name_action = (isset($name[$module]['modules'][$action])) ? ($name[$module]['modules'][$action]) : ($action);
					$way .= ' ' . $sepatator . " <a href='" . $root . "?module=" . $module . "&amp;action=" . $action . "'>" . ucfirst($name_action) . "</a>";
				}
			}
		}
		else // Cas sous domaine.
		{
			$complete_name = $module;
			$subdomains = substr($module, 0, $position);
			$module = $name_module = (isset($name[$complete_name]['name'])) ? ($name[$module]['name']) : (substr($module, $position + 1));
			$way .= ' ' . $sepatator . " <a href='" . $root . $subdomains . "/'>" . ucfirst($subdomains) . "</a>";
			if ($module != $this->_default_module || $action != $this->_default_action)
			{
				$way .= ' ' . $sepatator . " <a href='" . $root . "?module=" . $module . "'>" . $module . "</a>";
				if ($action != $this->_default_action)
				{
					$way .= ' ' . $sepatator . " <a href='" . $root . "?module=" . $module . "&amp;action=" . $action . "'>" . ucfirst($action) . "</a>";
				}
			}
		}
		return $way;
	}
	
	/**
	 * Ajoute un message.
	 * @param string $message Message à afficher.
	 * @param int $type Type de message parmi les constantes ALERT_*.
	 */
	public function add_message($message, $type = self::ALERT_INFO)
	{
		$temp = $this->_session->__messages;
		$temp[$message] = $type;
		$this->_session->__messages = $temp;
	}
	
	/**
	 * Retourne les éventuels messages d'information stockés et les supprime.
	 * @return array
	 */
	public function list_messages()
	{
		if ($this->_session->__messages == FALSE)
		{
			return NULL;
		}
		$list = array();
		foreach($this->_session->__messages as $message => $type)
		{
			$str[] = $this->format($message, $type);
		}
		$this->clean_messages();
		return $str;
	}
	
	/**
	 * Supprime tous les messages.
	 */
	public function clean_messages()
	{
		unset($this->_session->__messages);
	}
	
	/**
	 * Parse un message en HTML.
	 * @param string $message Message à afficher.
	 * @param int $type Type de message parmi les constantes ALERT_*.
	 * @return array Message parsé.
	 */
	private function format($message, $type = self::ALERT_INFO)
	{
		switch($type)
		{
			case self::ALERT_INFO:
			{
				$class = 'alert_info';
				break;
			}
			case self::ALERT_OK:
			{
				$class = 'alert_ok';
				break;
			}
			case self::ALERT_WARNING:
			{
				$class = 'alert_warning';
				break;
			}
			case self::ALERT_ERROR:
			{
				$class = 'alert_error';
				break;
			}
			default:
			{
				$class = 'alert_info';
			}
		}
		return array('class'=>$class, 'msg'=>$message);
	}

	/**
	 * Charge les paramètres de la page en fonction d'une adresse URL donnée.
	 * @param string $url Adresse URL.
	 */
	public function load($url)
	{
		$arg = parse_url($url);
		$query = explode('&', $arg['query']);
		$request = Request::get_instance();
		foreach($query as $v)
		{
			$k = explode('=', $v);
			if (count($k) == 2)
			{
				$request->$k[0] = $k[1];
			}
		}
	}
	
	/**
	 * Envoie un header de redirection au navigateur et quitte le script.
	 * @param string $module Nom du module cible ou Adresse URL.
	 * @param string $action Nom de l'action.
	 */
	public function redirect($module = 'PageIndex', $action = 'index')
	{
		if (strpos($module, '.') === FALSE && strpos($module, '/') === FALSE)
		{
			header("Location: ?module=$module&action=$action");
		}
		else
		{
			header("Location: $module");
		}
		exit();
	}
	
	/**
	 * Fait une redirection vers la page précédente ou l'index si elle n'est pas trouvé
	 */
	public function prev()
	{
		$url = (isset($_SERVER['HTTP_REFERER'])) ? ($_SERVER['HTTP_REFERER']) : (Site::get_root());
		header("Location: " . $url);
		exit();
	}
	
	/**
	 * Retourne la racine du site au format URL ou DIR.
	 * @param int $format Format de retour, utilisez les constantes de classe DIR ou URL.
	 * @return string Racine du site.
	 */
	public static function get_root($format = self::URL)
	{
		if ($format != self::URL && $format != self::DIR)
		{
			return NULL;
		}
		if (array_key_exists($format, self::$_root))
		{
			return self::$_root[$format];
		}
		$root = dirname($_SERVER['SCRIPT_FILENAME']);
		if (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/') 
		{
			$document_root = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
		}
		else 
		{
			$document_root = $_SERVER['DOCUMENT_ROOT'];
		}
		while(! file_exists($root . '/.root') && $root != $document_root)
		{
			$root = dirname($root);
		}
		if ($format == self::URL)
		{
			$root = str_replace($document_root, 'http://' . $_SERVER['SERVER_NAME'], $root);
			$root = (substr($root, - 1) != '/') ? ($root . '/') : ($root);
			self::$_root[$format] = $root;
		}
		else
		{
			$root .= '/';
			self::$_root[$format] = $root;
		}
		return $root;
	}
	
	/**
	 * Retourne le nombre de dossier à partir du dossier actuel pour atteindre la racine du site.
	 * @return number Nombre de dossiers.
	 */
	public static function get_number_dir_to_root()
	{
		$root_dir = basename($_SERVER['DOCUMENT_ROOT']);
		$curret_dir = getcwd();
		$try = 1;
		while($root_dir != basename($curret_dir) && ! file_exists($curret_dir . '/.root'))
		{
			$curret_dir = dirname($curret_dir);
			$try ++;
		}
		return $try;
	}
	
	/**
	 * Formate les urls en urls absolues.
	 * @param array|string $url Liste ou élément unique contenant l'adresse URL à parser.
	 * @return array|string URL parsée.
	 */
	public static function parse_url($url)
	{
		$list_url = NULL;
		if (is_array($url))
		{
			$list_url = array();
			foreach($url as $u)
			{
				$list_url[] = self::parse_url_unique($u);
			}
		}
		elseif (is_string($url))
		{
			$list_url = self::parse_url_unique($url);
		}
		return $list_url;
	}
	
	/**
	 * Formate une URL en URL absolue.
	 * @param astring $url L'adresse URL à parser.
	 * @return string URL parsée.
	 */
	private static function parse_url_unique($url)
	{
		if (substr($url, 0, 7) != 'http://')
		{
			$pos = strpos($url, '?');
			if ($pos !== FALSE)
			{
				$get = substr($url, $pos);
				$url = substr($url, 0, $pos);
			}
			$url = (substr($url, - 1) == '/') ? (realpath($url) . '/') : (realpath($url));
			if (DIRECTORY_SEPARATOR != '/')
			{
				$url = str_replace(DIRECTORY_SEPARATOR, '/', $url);
			}
			$url = str_replace(self::get_root(self::DIR), self::get_root(), $url);
			if ($pos !== FALSE)
			{
				$url = $url . $get;
			}
		}
		// Norme W3C & = &amp;.
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);
		return $url;
	}
	
	/**
	 * Vérifie que la request envoyé au site provienne bien de ce même site.
	 * @return boolean
	 */
	public static function check_source()
	{
		$root = self::get_root();
		$request = (array_key_exists('HTTP_REFERER',$_SERVER)) ? ($_SERVER['HTTP_REFERER']) : (NULL);
		return ($root == substr($request,0,strlen($root)));
	}
}
?>