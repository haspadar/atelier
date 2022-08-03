<?php

use Bramus\Router\Router;

require_once '../vendor/autoload.php';

try {
    \Atelier\Auth::check();
    $templatesEngine = new League\Plates\Engine(\Atelier\Directory::getTemplatesDirectory());
    $router = new Router();
    $router->get('/', '\Atelier\Controller\Atelier@showIndex');
    $router->get('/projects', '\Atelier\Controller\Atelier@showProjects');
    $router->get('/projects/{id}', '\Atelier\Controller\Atelier@showProject');
    $router->delete('/projects/{id}', '\Atelier\Controller\Atelier@deleteProject');
    $router->get('/garage', '\Atelier\Controller\Atelier@showGarage');
    $router->get('/garage/{id}', '\Atelier\Controller\Atelier@showMachine');
    $router->delete('/garage/{id}', '\Atelier\Controller\Atelier@deleteMachineProjects');
    $router->put('/scan-projects/{id}', '\Atelier\Controller\Atelier@scanProjects');
    $router->post('/auth/{id}', '\Atelier\Controller\Atelier@auth');
    $router->put('/project-command/{id}/{command}', '\Atelier\Controller\Atelier@runProjectCommand');
    $router->run();

} catch (Exception $e) {
    \Atelier\Logger::error($e->getMessage());
    \Atelier\Logger::error($e->getTrace());
    throw $e;
}