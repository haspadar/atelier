<?php

use Atelier\Auth;
use Atelier\Logger;
use Bramus\Router\Router;

require_once '../vendor/autoload.php';
date_default_timezone_set('Europe/Minsk');

try {
    Auth::check();
    $templatesEngine = new League\Plates\Engine(\Atelier\Directory::getTemplatesDirectory());
    $router = new Router();
    $router->get('/', '\Atelier\Controller\Atelier@showIndex');
    $router->get('/projects', '\Atelier\Controller\Atelier@showProjects');
    $router->get('/projects/{id}', '\Atelier\Controller\Atelier@showProject');
    $router->delete('/projects/{id}', '\Atelier\Controller\Atelier@deleteProject');
    $router->get('/machines', '\Atelier\Controller\Atelier@showMachines');
    $router->get('/machines/{id}', '\Atelier\Controller\Atelier@showMachine');
    $router->get('/reports', '\Atelier\Controller\Atelier@showReports');
    $router->get('/reports/{id}', '\Atelier\Controller\Atelier@showReport');
    $router->delete('/machines/{id}', '\Atelier\Controller\Atelier@deleteMachineProjects');
    $router->put('/scan-projects/{id}', '\Atelier\Controller\Atelier@scanProjects');
    $router->post('/auth/{id}', '\Atelier\Controller\Atelier@auth');
    $router->put('/project-command/{id}/{command}', '\Atelier\Controller\Atelier@runProjectCommand');
    $router->run();

} catch (Exception $e) {
    Logger::error($e->getMessage());
    Logger::error($e->getTraceAsString());
    throw $e;
}