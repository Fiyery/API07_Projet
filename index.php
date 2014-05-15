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
if (isset($config->cache) && isset($config->cache->enable) && $config->cache->enable == 1)
{
	$cache->enable();
}

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
$name = (isset($_GET['controller']) && empty($_GET['controller']) == FALSE) ? ($_GET['controller']) : ('home');
$action = (isset($_GET['action']) && empty($_GET['action']) == FALSE) ? ($_GET['action']) : ('default_action');
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
$view->content = $content;

// Initialisation du controller du menu.
$controller->load('navigation_menu', 'init');
$controller->execute();
$view->list_menu_links = $controller->show();

// Gestion des messages serveurs.
$msg_list = $site->list_messages();
if (empty($msg_list) == FALSE)
{
	$view->msg_list = $msg_list;
}

// Suppression des conflits de variables entre index et view.
$vars = array_keys(get_defined_vars());
foreach ($vars as $name) 
{
	if ($name != 'view' && $name != 'config')
	{
		unset($$name);
	}
}

$list = $view->get();
foreach ($list as $name => $value)
{
	$$name = $value;
}

$echos = ob_get_clean();
require('app/tpl/main.php');

// Affichage des éléments parasites.
if (isset($config->debug) && isset($config->debug->print_area) && $config->debug->print_area == 1)
{
	require('app/tpl/debug_area.php');
}
?>