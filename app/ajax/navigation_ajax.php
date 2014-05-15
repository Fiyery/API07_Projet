<?php
// Entête de la requête de retour.
header('Content-type:text/html; charset=utf-8');

// Affichage des erreurs PHP.
ini_set('display_errors', 1);
ini_set('error_reporting',E_ALL);

// Fuseau horaire français.
date_default_timezone_set('Europe/Paris');

// Capture des affichages parasites.
ob_start();

// Contrainte de dossier : Déplacement à la racine du site.
chdir('../../');

// Chargement de classes.
require ("app/lib/ClassLoader.class.php");
$loader = new ClassLoader();
$loader->set_ext('.class.php');
$loader->add_dir('models/');
$loader->add_dir('app/lib/');
$loader->enable();

// Paramètres.
$config = Config::get_instance();
$config->set_json('app/var/config.json');

// Base de données.
$base = Base::get_instance('UTF-8');
$base->add_base($config->db->host, $config->db->name, $config->db->user, $config->db->pass);

// Système de cache.
$cache = Cache::get_instance('app/tmp/');

// Intitialisation des Dao.
Dao::set_base($base);

// Toolbox du site.
$site = Site::get_instance();

// Toolbox des requêtes.
$req = Request::get_instance();

// Toolbox de la session.
$session = Session::get_instance();

// Gestion des paramètre à envoyer à la vue.
$view = View::get_instance();
$view->root_www = $site->get_root();

// Définition des variables globales.
$vars = array(
	'conf' => $config,
	'cache' => $cache,
	'view' => $view,
	'site' => $site,
	'req' => $req,
	'session' => $session
);

// Définition générale du manager de controllers.
$controller = ControllerManager::get_instance();
$controller->set_params($vars);
$controller->set_controllers_dir('controllers/');
$controller->set_views_dir('views/');
$controller->set_view($view);

// Traitement particulier.
if (isset($_REQUEST['url']) == FALSE)
{
	$name = 'error';
	$action = 'not_found';
}
else
{
	$url = str_replace($site->get_root(), '', $_REQUEST['url']);
	preg_match('#([^\/]+)\/(([^\/]+)\/)?$#', $url, $match);
	$count = count($match);
	if ($count == 0)
	{
		$name = 'home';
		$action = 'default_action';
	}
	elseif ($count == 2)
	{
		$name = $match[1];
		$action = 'default_action';
	}
	elseif ($count == 4)
	{
		$name = $match[1];
		$action = $match[3]; 
	}
	else 
	{
		$name = 'error';
		$action = 'not_found';
	}
}
$controller->load($name, $action);

// Exécution du controller.
$executed = $controller->execute();

// Récupération du contenu de la vue.
$content = $controller->show();

// Page d'erreur si page de view et controller n'existe pas.
if ($executed == FALSE && $content === FALSE)
{
	$controller->load('error', 'not_found');
	$controller->execute();
	$content = $controller->show();
}

// Récupération des affichages parasites.
$echos = ob_get_clean();

echo $content;
?>
