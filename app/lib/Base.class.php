<?php
/**
 * Base est l'interface de connexion et de requetage à la base de données.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.4
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Singleton
 */
class Base extends Singleton
{
	/**
	 * Variable d'instance de singleton.
	 * @var Base
	 */
	protected static $_instance = NULL;
	
	/**
	 * Variable de comptage des requêtes.
	 * @var int
	 */
	private static $_count = -1;

	/**
	 * Message d'erreur.
	 * @var string
	 */
	private $_error = NULL;
	
	/**
	 * Liste des bases.
	 * @var array
	 */
	private $_data_bases = NULL;
	
	/**
	 * Nombre de base de données.
	 * @var int
	 */
	private $_data_bases_nb = 0;
	
	/**
	 * Liste des tables et de leurs liaisons.
	 * @var array<string>
	 */
	private $_tables = NULL;
	
	/**
	 * Constructeur.
	 * @param array $args Tableau des arguments.
	 */
	protected function __construct()
	{
		$this->_tables = array();
	}

	/**
	 * Exécute une requête sql. 
	 * @param string|Query $sql Requête sql à exécuté ou un objet Query.
	 * @param string $name_base Nom de la base.
	 * @param array $value Tableau contenant les valeurs vérifier par PDO.
	 * @return boolean|array Retourne le résultat de la requête, TRUE si cette dernière ne retourne rien, ou FALSE s'il y a une erreur. 
	 */
	public function query($sql,$name_base=NULL,$value=NULL)
	{
		$this->check_connection();
		if (self::$_count >= 0)
		{
			self::$_count++;
		}
		// Vérification des paramètres.
		if (is_string($name_base) == FALSE || array_key_exists($name_base,$this->_data_bases) ==  FALSE)
		{
			if (is_object($sql) == FALSE || get_class($sql) != 'Query')
			{
				if ($this->_data_bases_nb > 1)
				{
					$this->_error = 'Invalide params';
					return FALSE;
				}
			}
		}		
		// Auto-assignement de valeur de $name_base si variable non-remplie.
		if (is_object($sql))
		{
			$name_base = $sql->get_base();
		}
		elseif ($this->_data_bases_nb == 1)
		{
			$name_base = key($this->_data_bases);
		}
		if (array_key_exists($name_base,$this->_data_bases) == FALSE)
		{
			$this->_error = 'Invalide base';
			return FALSE;
		}
		$bd = $this->_data_bases[$name_base]['connexion'];
		$res = $bd->prepare($sql);
		if (is_array($value) && count($value) > 0)
		{
			$res->execute($value);
		}
		else
		{
			$res->execute();
		}
		$this->_error = $res->errorInfo();
		if ($res->rowCount() > 0)
		{ 
			return $res->fetchAll(PDO::FETCH_ASSOC);
		}
		elseif ($res->errorCode() == '00000')
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Sauvegarde de la base de donnée.
	 * @return string Contenu de la sauvegarde de la base de données.
	 */
	public function save()
	{
		$this->check_connection();
		$sauv = '';
		$taille = 100;
		foreach ($this->_data_bases as $name_base=>$valeur)
		{
			$titre = ' SAUVEGARDE de '.$name_base.' le '.date('d/m/Y à H:G:s').' ';
			$limit = floor(($taille-strlen($titre))/2);
			$sauv.="\n-- ";
			$i= 0;
			while ($i<$taille) {$sauv .= '-';$i++;}
			$sauv.="\n--";
			$i = 0;
			while ($i<$limit) {$sauv .= ' ';$i++;}
			$sauv .= $titre;
			$i = 0;
			while ($i<$limit) {$sauv .= ' ';$i++;}
			$sauv.="\n-- ";
			$i= 0;
			while ($i<$taille) {$sauv .= '-';$i++;}
			$sauv .= "\n\n";
			$tables = $this->get_tables($name_base);
			$res = $valeur['connexion']->prepare( "SHOW CREATE DATABASE `".$name_base."`;");
			$res->execute();
			$create_database = $res->fetchAll(PDO::FETCH_ASSOC);
			$sauv .= str_replace('CREATE DATABASE','CREATE DATABASE IF NOT EXISTS',$create_database[0]['Create Database'])."\nUSE `".$name_base."`;\n";
			foreach ($tables as $t)
			{
				$limit = floor(($taille-strlen($t)-2)/2);
				$sauv .= "\n-- ";
				$i = 0;
				while ($i<$taille) {$sauv .= '-';$i++;}
				$sauv.="\n--";
				$i = 0;
				while ($i<$limit) {$sauv .= ' ';$i++;}
				$sauv .= " $t ";
				$i = 0;
				while ($i<$limit) {$sauv .= ' ';$i++;}
				$sauv .= "\n-- ";
				$i = 0;
				while ($i<$taille) {$sauv .= '-';$i++;}
				$sauv .= "\n\n".'DROP TABLE IF EXISTS `'.$t.'`;'."\n";
				$res = $valeur['connexion']->prepare('SHOW CREATE TABLE `'.$t.'`;');
				$res->execute();
				$create_table = $res->fetchAll(PDO::FETCH_ASSOC);
				$sauv .= $create_table[0]['Create Table']."\n";
				$res = $valeur['connexion']->prepare('SELECT * FROM `'.$t.'`;');
				$res->execute();
				if ($res->rowCount() > 0)
				{
					$rows = $res->fetchAll(PDO::FETCH_ASSOC);
					$sauv .= "INSERT INTO $t VALUES "."\n";
					foreach ($rows as $r)
					{
						$sauv .= "(";
						$values = array();
						foreach ($r as $c)
						{
							$values[] = str_replace("'","\'",$c);
						}
						$sauv.= "'".implode("','",$values)."'),\n";
					}
					$sauv = substr($sauv,0,-2);
					$sauv .=";\n";
				}
			}
		}
		return $sauv;
	}

	/**
	 * Retourne la liste les tables des bases de données.
	 * @param string $base_name Nom de la base. Si NULL, la liste portera sur toutes les bases.
	 * @return array<string> Liste des tables.
	 */
	public function get_tables($base_name=NULL)
	{
		$this->check_connection();
		$list_tables = array();
		if ($base_name == NULL)
		{
			foreach ($this->_data_bases as $b => $v)
			{
				if ($this->_data_bases[$b]['tables'] == NULL)
				{
					$res = $this->query('SHOW TABLES',$b);
					$tab = array();
					if (is_array($res))
					{
						foreach ($res as $v)
						{
							$v = array_values($v);
							$tab[] = $v[0];
						}
						$this->_data_bases[$b]['tables'] = $tab;
					}
				}
				$list_tables = array_merge($list_tables,$this->_data_bases[$b]['tables']);
			}
		}
		else
		{
			if (isset($this->_data_bases[$base_name]) != FALSE)
			{
				if ($this->_data_bases[$base_name]['tables'] == NULL)
				{
					$res = $this->query('SHOW TABLES',$base_name);
					if ($res !== FALSE && is_array($res))
					{
						$list_tables = $res;
						while (($v = current($res)))
						{
							$v = array_values($v);
							$tab[] = $v[0];
							next($res);
						}
						$this->_data_bases[$base_name]['tables'] = $tab;
					}
				}
				$list_tables = $this->_data_bases[$base_name]['tables'];
			}
		}
		return $list_tables;
	}
	
	/**
	 * Réinitialise la liste des tables enregistrés en cache.
	 */
	public function reset_cache()
	{
		foreach ($this->_data_bases as &$b)
		{
			$b['tables'] = NULL;
		}
		$this->_tables = array();
	}
	
	/**
	 * Retourne le nom de la base de données à utiliser pour une requete en fonction du nom de la table.
	 * @param string $table Nom de la table.
	 * @return string Nom de la base de données.
	 */
	public function select_base($table)
	{
		$this->check_connection();
		if (is_string($table) == FALSE)
		{
			return NULL;
		}
		if ($this->_data_bases_nb == 1)
		{
			reset($this->_data_bases);
			return key($this->_data_bases);
		}
		$table = strtolower($table);
		$name = NULL;
		$continuer = TRUE;
		reset($this->_data_bases);
		while ((list($n,$v) = each($this->_data_bases)) && $name == NULL)
		{
			if ($this->_data_bases[$n]['tables'] == NULL)
			{
				$this->get_tables($n);
			}
			if (in_array($table,$this->_data_bases[$n]['tables']))
			{
				$name = $n;
			}
		}
		return $name;
	}

	/**
	 * Retourne la liste des champs d'une table.
	 * @param string $name Nom de la table.
	 * @return array Information sur les colonnes.
	 */
	public function get_fields($name)
	{
		$this->check_connection();
		$name = strtolower($name);
		if (array_key_exists($name,$this->_tables) === FALSE)
		{
			$res = $this->query('DESCRIBE `'.$name.'`;',$this->select_base($name));
			$this->_tables[$name] = (is_array($res)) ? ($res) : (NULL);
		}
		return $this->_tables[$name];
	}
	
	/**
	 * Retourne la liste des bases de données
	 * @return array<string> Tableau des noms de base de données.
	 */ 
	public function get_bases()
	{
		$this->check_connection();
		return array_keys($this->_data_bases);
	}
	
	/**
	 * Vérifie si la table passée en paramètre existe.
	 * @param string $name Nom de la table.
	 * @return boolean
	 */
	public function table_exists($name)
	{
		$this->check_connection();
		return (is_string($name) && in_array($name,$this->get_tables()));
	}

	/**
	 * Vérifie si la base de données passée en paramètre existe.
	 * @param string $name Nom de la base de données.
	 * @return boolean
	 */
	public function base_exists($name=NULL)
	{
		return (is_string($name) && is_array($this->_data_bases) && array_key_exists($name,$this->_data_bases));
	}

	/**
	 * Retourne le message d'erreur de la dernière requête exécutée.
	 * @return string
	 */
	public function get_error()
	{
		return $this->_error;
	}

	/**
	 * Ajoute et connecte une base de données.
	 * @throws PDOException
	 * @param string $host Nom du domaine.
	 * @param string $name Nom de la base de données.
	 * @param string $user Nom de l'utilisateur.
	 * @param string $pass Mot de passe.
	 * @param string $engine Nom du système de base de données.
	 * @param strn
	 */
	public function add_base($host, $name, $user, $pass, $engine='mysql', $charset='UTF-8') 
	{
		// Verifier que la connexion soit bien en utf-8.
		if (strtolower($charset) == 'utf-8')
		{
			$sql = "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'";
		}
		$connexion = new PDO($engine.":host=".$host.";dbname=".$name,$user,$pass);
		$this->_data_bases[$name] = array (
    		'connexion'=> $connexion,
    		'tables' => NULL
		);
		if (strtolower($charset) == 'utf-8')
		{
			$this->query($sql,$name);
		}
		$this->_data_bases_nb++;
	}
	
	/**
	 * Vérifie si la base est connectées à au moins une base de donnée.
	 */
	public function check_connection()
	{
		if ($this->_data_bases == 0)
		{
			throw new Exception('No database found');
		}
	}
	
	/**
	 * Démarre le comptage des requêtes.
	 */
	public static function start_count()
	{
		self::$_count = 0;
	}
	
	/**
	 * Termine le comptage des requêtes et affiche et retourne le résultat.
	 * @param boolean $show Si TRUE, on affiche la phrase de debbogage.
	 * @return int Nombre de requêtes effectuées depuis l'appel de start_count().
	 */
	public static function end_count($show=TRUE)
	{
		if ($show)
		{
			echo "<div>Nombre de requêtes effectuées est de <strong>".self::$_count."</strong> requêtes SQL.</div>";
		}
		$nb = self::$_count;
		self::$_count = -1;
		return $nb;
	}
	
	/**
	 * Retourne une instance de Base avec les arguments correctement ordonnés selon le constructeur de la classe.
	 * @param array $args Tableau d'arguments du constructeur.
	 * @return Base
	 */
	protected static function __create($args)
	{
		return new self($args[0]);
	}
}
?>