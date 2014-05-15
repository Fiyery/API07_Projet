<?php
/**
 * String regroupe des fonctions utiles sur les chaînes de caractères.
 * @author Yoann Chaumin <yoann.chaumin@gmail.com>
 * @version 1.0
 * @copyright 2011-2014 Yoann Chaumin
 */
class String
{
    /**
     * Formate une chaine pour la mettre dans l'url.
     * @param string $string Chaîne de caractères à traiter.
     * @return string Chaîne formatée.
     */
    public static function format_url($string)
    {
    	return self::format($string,'_');
    }
    
    /**
     * Formate une chaine en enlevant tous les caractères spéciaux.
     * @param string $string Chaîne de caractères à traiter.
     * @param char $space Caractère de remplacement des espaces.
     * @return string Chaîne formatée.
     */
    public static function format($string, $space='')
    {
        $special_char = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
        $normal_char = "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn";
    	$string = strtr($string, $special_char, $normal_char);
    	$string = preg_replace('/[^\w\s]/','',$string);
		$string = trim($string);
		$string = str_replace('  ',' ',$string, $count);
		while ($count > 0)
		{
			$string = str_replace('  ',' ',$string, $count);
		}
    	$string = str_replace(' ',$space,$string);
    	$string = strtolower($string);
    	return $string;
    }
}
?>