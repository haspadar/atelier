<?php

use Bramus\Router\Router;

require_once '../vendor/autoload.php';

try {
    \Atelier\Auth::check();
    $templatesEngine = new League\Plates\Engine(\Atelier\Directory::getTemplatesDirectory());
    $router = new Router();
    $router->get('/', '\Atelier\Controller\Atelier@showIndex');
    $router->get('/garderobe', '\Atelier\Controller\Atelier@showGarderobe');
    $router->get('/garage', '\Atelier\Controller\Atelier@showGarage');
    $router->run();

} catch (Exception $e) {
    \Atelier\Logger::error($e->getMessage());
    \Atelier\Logger::error($e->getTrace());
    throw $e;
}