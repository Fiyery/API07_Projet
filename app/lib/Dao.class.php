<?php
/**
 * Dao est la classe générale et mère de toutes les Dao spécifiques d'accès au table de la base de données.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.4
 * @copyright 2011-2014 Yoann Chaumin
 * @uses Base
 */
abstract class Dao
{
    /**
     * Classe de la base de données.
     * @var Base
     */
    protected static $_base = NULL;
    
    /**
     * Liste des classes.
     * @var array<string>
     */
    private static $list_class = NULL;
    
    /**
     * Liste de cache des objets chargés.
     * @var array<object>
     */
    private static $list_objects = array();

    /**
     * Constructeur
     * @param array $data Liste des couples nom-valeur des attributs de l'instance.
     */
    public function __construct($data=array())
    {
    	foreach($data as $name => $value)
    	{
    		$this->$name = $value;
    	}
    }
    
    /**
     * Retourne un attribut.
     * @param mixed $name Valeur de l'attribut.
     */
    public function __get($name)
    {
    	return $this->$name;
    }
    
    /**
     * Définie un attribut.
     * @param string $name Nom de l'attribut.
     * @param mixed $valeur Valeur de l'attribut.
     */
    public function __set($name, $value)
    {
    	$this->$name = $value;
    }
    
    /**
     * Modifie les valeurs d'une instance et se son enregsitrement dans la base de donénes.
     * @param array<string> $data Liste de couple clé-valeur à modifier.
     * @return boolean
     */
    public function modify($data=array())
    {
    	if (!is_array($data) || count($data)==0) 
    	{
    		return FALSE;
    	}
    	$class = get_called_class();
    	$table = strtolower($class); 
    	$sql = 'UPDATE `'.$table.'` SET ';
    	$i=0;
    	$sql .=  implode(' = ?, ', array_keys($data)).' = ?';
    	$list_data = array_values($data);
    	$keys = $class::get_keys(); 
    	$nb_keys = count($keys);
    	$sql .= ' WHERE ';
    	for($i=0; $i < $nb_keys; $i++)
    	{
        	$sql .= $keys[$i].' = \''.$this->$keys[$i].'\'';
        	if ($i < $nb_keys-1)
        	{
        	   $sql .= ' AND ';
        	}
    	}
    	$sql .= ';';
    	$return = self::$_base->query($sql,self::$_base->select_base($table),$list_data);
    	if ($return !== FALSE)
    	{
    	    foreach ($data as $name => $value)
    	    {
    	    	$this->$name = $value;
    	    }
    	    return TRUE;  
    	}
    	return FALSE;
    }

    /**
     * Définie la connexion avec la base de données.
     * @param Base $b Instance de connexion et de requêtage à la base de données.
     */
    final public static function set_base(Base $b)
    {
        self::$_base = $b;
    }
    
    /**
     * Charge un ou plusieurs intances à partir d'un ou plusieurs enregistrement de la base de données.
     * @param array|string $values Identifiant ou liste d'identifiants. Prends un nombre quelconque de paramètres.
     * @return array<object> Liste des instances.
     */
    public static function load($values)
    {
    	if (!is_numeric($values) && !is_array($values) && !is_string($values))
    	{
    		return NULL;
    	}
    	// Génération des bons paramètres.
    	$values = (is_array($values) == FALSE) ? (array(func_get_args())) : (func_get_args());
    	// Génération des informations sur la classe et la table.
    	$class = get_called_class();
    	$table = strtolower($class);
    	// Génération des informations des champs.
    	$keys = $class::get_keys();
    	$nb_keys = count($keys);
    	// Génération des valeurs en cache et des valeurs à charger.
    	$list_instances = array();
    	$to_load = array();
    	$i = 0;
    	$count_values = count($values);
    	$count_to_load = 0;
    	if ($nb_keys == 1 && $count_values == 1)
    	{
    		$values = $values[0];
    		foreach ($values as $v)
    		{
    			$index = (is_array($v)) ? (self::get_index($v)) : ($v);
    			$instance = (isset(self::$list_objects[$table][$index])) ? (self::$list_objects[$table][$index]) : (FALSE);
    			$list_instances[$i] = $instance;
    			if ($instance === FALSE)
    			{
    				$to_load[$i] = array($v);
    				$count_to_load++;
    			}
    			$i++;
    		}
    	}
    	else
    	{
    		foreach($values as $v)
    		{
    			if (count($v) == $nb_keys)
    			{
    				$index = (is_array($v)) ? (self::get_index($v)) : ($v);
    				$instance = (isset(self::$list_objects[$table][$index])) ? (self::$list_objects[$table][$index]) : (FALSE);
    				$list_instances[$i] = $instance;
    				if ($instance === FALSE)
    				{
    					$to_load[$i] = $v;
    					$count_to_load++;
    				}
    				$i++;
    			}
    		}
    	}
    	$count_instances = $i;
    	if ($count_to_load == 0) // Si aucune instance à charger, on retourne celles dans le cache ou FALSE.
    	{
    		if ($count_instances == 0) 
    		{
    		    return NULL;
    		}
    		elseif ($count_instances == 1)
    		{
    		    return $list_instances[0];
    		}
    		else
    		{
    		    return $list_instances;
    		}
    	}
    	// Préparation de la requête sql.
    	$sql = 'SELECT * FROM '.$table.' WHERE 1';
    	$search = '('.implode(',',array_fill(0,$count_to_load,'?')).')';
    	foreach ($keys as $k)
    	{
    		$sql .= ' AND '.$k.' IN '.$search;
    	}
    	// Récupération des valeurs des ids dans un tableau.
    	$to_load_values = array();
    	foreach ($to_load as $l)
    	{
    		foreach ($l as $v)
    		{
    			$to_load_values[] = $v;
    		}
    	}
    	$sql .= ';';
    	// Exécution de la requête préparée.
    	$rows = self::$_base->query($sql,self::$_base->select_base($table),$to_load_values);
    	// Composition du résultat.
    	if (is_array($rows) == FALSE)
    	{
    		$return = array();
    		foreach ($list_instances as $i)
    		{
    			if (is_object($i))
    			{
    				$return[] = $i;
    			}
    		}
    	}
    	else
    	{
    		$i = $j = 0;
    		$count_rows = count($rows);
    		$return = array();
    		while ($i < $count_instances || $j < $count_rows)
    		{
    			if (array_key_exists($i,$list_instances))
    			{
    				if (is_object($list_instances[$i]))
    				{
    					$return[] = $list_instances[$i];
    				}
    				elseif (array_key_exists($j,$rows))
    				{
    					$return[] = new $class($rows[$j]);
    					$j++;
    				}
    				$i++;
    			}
    			elseif (array_key_exists($j,$rows))
    			{
    				$return[] = new $class($rows[$j]);
    				$j++;
    			}
    			$i++;
    		}
    	}
    	$count_return = count($return);
    	if ($count_return == 0)
    	{
    		return NULL;
    	}
    	elseif ($count_return == 1)
    	{
    		return $return[0];
    	}
    	else
    	{
    		return $return;
    	}
    }
    
	/**
	 * Retourne les noms des champs contenant l'identifiant de l'objet, les clés de la table.
	 * @return array Liste des noms des colonnes.
	 */
	public static function get_keys()
	{
		$class = get_called_class();
		$fields = $class::get_fields();
		if (is_array($fields) == FALSE)
		{
			return NULL;
		}
		$keys = array();
		foreach($fields as $f)
		{
			if ($f['Key'] == 'PRI')
			{
				$keys[] = $f['Field'];
			}
		}
		return $keys;
	}
	
	/**
	 * Recherche des instances, si on ne précise aucun argument, la fonction renvoie tous les enregistrements.
	 * @param array|string $fields Nom d'un champs à recherche ou tableau associatif de champs et leur valeur.
	 * @param string $values Valeur du champs si le premier argument est une chaîne de caractères.
	 * @param int $begin Position du premier enregistrement.
	 * @param int $end Position du dernier enregistrement.
	 * @param string $order Permet le trie par un tableau à deux clés, ASC ou DESC.
	 *     Pour chaque clé, si nécessaire, il doit y avoir un tableau de nom de champ.
	 * @return array<object> Liste des objets trouvés ou NULL.
	 */
	public static function search($fields=NULL, $values=NULL, $begin=NULL, $end=NULL, $order=NULL)
	{
		$class = get_called_class();
		$table = strtolower($class);
		// Début de la requête.
		if ($fields == NULL && $values == NULL)
		{
			$sql = 'SELECT * FROM `'.$table.'`';
			$values_sql = NULL;
		}
		elseif (!is_array($fields) && ($fields == NULL || $values == NULL))
		{
			return NULL;
		}
		elseif ($fields != NULL && $values != NULL)
		{
			if (!is_string($fields) && !is_string($values) && !is_numeric($values))
			{
			    return NULL;
			}
			else
			{
				$sql = 'SELECT * FROM `'.$table.'` WHERE `'.$fields.'` = ?';
				$values_sql = array($values);
			}
		}
		elseif (is_array($fields))
		{
			$sql = 'SELECT * FROM '.$table.' WHERE ';
			$i = 0;
			$values_sql = array();
			$max = count($fields) - 1;
			foreach($fields as $c => $v)
			{
				$sql .= ($i < $max) ? ('`'.$c.'`= ? AND ') : ('`'.$c.'`= ?');
				$values_sql[] = $v;
				$i++;
			}
		}
		// Ordre.
		if (isset($order['ASC']))
		{
			if (is_array($order['ASC']) == FALSE)
			{
				$order['ASC'] = array($order['ASC']);
			}
			$sql .= ' ORDER BY `'.implode('` ASC, `',$order['ASC']).'` ASC';
			if (isset($order['DESC']))
			{
				if (is_array($order['DESC']) == FALSE)
				{
					$order['DESC'] = array($order['DESC']);
				}
				$sql .= ', `'.implode('` DESC, `',$order['DESC']).'` DESC';
			}
		}
		elseif (isset($order['DESC']))
		{
			if (is_array($order['DESC']) == FALSE)
			{
				$order['DESC'] = array($order['DESC']);
			}
			$sql .= ' ORDER BY `'.implode('` DESC, `',$order['DESC']).'` DESC';
		}
		// Limite du nombre d'enregistrements.
		$sql .= ($begin !== NULL && $end !== NULL) ? (' LIMIT '.$begin.','.($end-$begin).';') : (';');
		$res = self::$_base->query($sql,self::$_base->select_base($table),$values_sql);
		if (is_array($res) == FALSE)
		{
			return NULL;
		}
		// Instanciation.
		$tab_instances = array();
		foreach ($res as $d) // Création d'un tableau d'instances.
		{
			$tab_instances[] = new $class($d);
		}
		return $tab_instances;
	}
	
	/**
	 * Retourne le nombre d'enregistrements d'un DAO.
	 * @return int Nombre d'enregistrements
	 */
	public static function get_count()
	{
		$table = strtolower(get_called_class());
		$sql = 'SELECT count(*) NB FROM '.$table.';';
		$res = self::$_base->query($sql,self::$_base->select_base($table));
		return ($res !== FALSE) ? ($res[0]['NB']) : (0);
	}
	
	/**
	 * Traite le résultat d'une requête SQL et instancie les objets.
	 * @param array|boolean $result Résultat de la recherche.
	 * @param string $class Nom de la classe des objets de retour.
	 * @return boolean|array<Mere> Tableau d'objets ou FALSE.
	 */
	public static function handle_result($result,$class)
	{
		$return = FALSE;
		if (is_array($result))
		{
			$return = array();
			foreach($result as $d)
			{
				$return[] = new $class($d);
			}
		}
		return $return;
	}
	
	/**
	 * Renvoie la liste des champs de la table.
	 * @return array Liste des champs avec leurs informations.
	 */
	public static function get_fields()
	{
		return self::$_base->get_fields(get_called_class());
	}
	
	/**
	 * Ajoute un nouvelle enregistrement dans la base de données.
	 * @param array $value Liste des valeurs correspondant aux colonnes.
	 * @return boolean
	 */
	public static function add($value=array())
	{
		if (is_array($value) == FALSE || count($value) == 0)
		{
			return FALSE;
		}
		$class = get_called_class();
		$table = strtolower($class);
		$fields = $class::get_fields();
		$nb_values = count($value);
		if (count($fields) != $nb_values)
		{
			return FALSE;
		}
		$sql = 'INSERT INTO `'.$table.'` VALUES (';
		$i = 0;
		$value_sql = array_values($value);
		$sql .= implode(',',array_fill(0,$nb_values,'?'));
		$sql .= ');';
		return (self::$_base->query($sql,self::$_base->select_base($table),$value_sql) !== FALSE);
	}
	
	/**
	 * Supprime une instance existante dans la base de données.
	 * @param array|string $value Identifiant ou liste d'identifiants.
	 * @return boolean
	 */
	public static function delete($value=NULL)
	{
        if ($value == NULL || (!is_numeric($value) && !is_string($value) && !is_array($value)))
        {
        	return FALSE;
        }
        $class = get_called_class();
        $table = strtolower($class);
        $keys = $class::get_keys();
        $nb_values = count($value);
        if (count($keys) > 1 && (is_numeric($value) || is_string($value)))
        {
        	return FALSE;
        }
        elseif (count($keys) == 1 && (is_numeric($value) || is_string($value)))
        {
        	$sql = 'DELETE FROM `'.$table.'` WHERE '.$keys[0].'= ?;';
        	$res = self::$_base->query($sql,self::$_base->select_base($table),array($value));
        }
        elseif (count($keys) != count($value))
        {
        	return FALSE;
        }
        else	// Cas des tables n-ième.
        {
        	$sql = 'DELETE FROM `'.$table.'` WHERE ';
        	$max = count($keys);
        	$value_sql = array();
        	for($i=0; $i < $max; $i++)
        	{
        		$sql .= $keys[$i].' = ?';
        		$value_sql[] = $value[$i];
        		if ($i < $max - 1)
        		{
        			$sql .= ' AND ';
        		}
        	}
        	$sql .= ';';
        	$res = self::$_base->query($sql,self::$_base->select_base($table),$value_sql);
        }
        return ($res !== FALSE);
	}

	/**
	 * Calcul l'indice de tableau pour le cache du loader.
	 * @param array<int> $array Liste d'identifiants.
	 * @return number Identifiant unique.
	 */
	final protected static function get_index($array)
	{
		$index = 0;
		$pow = 0;
		$array = array_reverse($array);
		foreach ($array as $a)
		{
			$index += $a * pow(2,$pow++);
		}
		return $index;
	}
	
	/**
	 * Retourne un tableau contenant les éventuelles dépendances d'un enregistrement. 
	 * @return array Liste des tables et des enregistrements dépendants.
	 */
	public function get_dependances()
	{
		$class = get_called_class();
		$table = strtolower($class);
		$keys = $class::get_keys();
		if (count($keys) > 1)
		{
			return FALSE;
		}
		$primary_key = $keys[0];
		$list_tables = self::$_base->get_tables();
		foreach ($list_tables as $t)
		{
			if ($t != $table)
			{
				$champs = $class::get_fields();
				foreach($champs as $c)
				{
					if ($c['Field'] == $primary_key)
					{
					    $eng = $class::search($primary_key,$this->$primary_key);
						if (is_array($eng))
						{
							$dep[$t]=$eng;
						}
					}
				}
			}
		}
		if (!isset($dep))
		{
			return FALSE;
		}
		return $dep;
	}

	/**
	 * Retourne le nom des champs de la classe courrante.
	 * @return array<string> Liste des noms.
	 */
	public static function get_fields_name()
	{
		$class = get_called_class();
	    $tmp_fields = $class::get_fields();
		$fields = array();
		if ($tmp_fields != NULL)
		{
			foreach ($tmp_fields as $f)
			{
				$fields[] = $f['Field'];
			}
			return $fields;
		}
		return NULL;

	}
}
?>