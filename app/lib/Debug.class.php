<?php
/**
 * Debug gère la gestion du debugage du site par les affichages de variable.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.0
 * @copyright 2011-2014 Yoann Chaumin
 */
class Debug 
{
	/**
	 * Affiche le paramètre puis le retourne.
	 * @param mixed $var Variable à afficher.
	 * @param boolean $hide Si TRUE, aucun affichage sinon la variable est affichée si FALSE.
	 * @return string Affichage de la variable.
	 */
	public static function show($var,$hide=FALSE)
	{
		$chaine = '<p><pre>';
		if (is_array($var) || is_object($var))
		{
			$chaine .= print_r($var,TRUE);
		}
		elseif ($var === FALSE)
		{
			$chaine .= "FALSE";
		}
		elseif ($var === TRUE)
		{
			$chaine .= "TRUE";
		}
		elseif (is_numeric($var))
		{
			$chaine .= $var;
		}
		elseif ($var === NULL)
		{
			$chaine .= "NULL";
		}
		elseif (empty($var))
		{
			$chaine .= "EMPTY";
		}
		else
		{
			$var = htmlentities($var);
			$chaine .= $var;
		}
		$chaine .= '</pre></p>';
		if ($hide == FALSE)
		{
			echo $chaine;
		}
		return $chaine;
	}
	
	/**
	 * Affiche puis retourne les appels de fonctions jusqu'au script courant.
	 * @return string Trace de l'appel de cette fonction.
	 */
	public static function trace()
	{
		ob_start();
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$trace = ob_get_clean();
		$trace = str_replace('#','<br/><div><b><u>',$trace);
		$trace = str_replace('called at [','called at : </u></b><ul><li>File : ',$trace);
		$trace = preg_replace('/:([0-9]*)\]/','</li><li>Ligne : $1</li></ul></div>',$trace);
		echo $trace;
		return $trace;
	}
}
?>