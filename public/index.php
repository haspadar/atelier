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
    $router->get('/commands', '\Atelier\Controller\Atelier@showCommands');
    $router->get('/commands/{id}', '\Atelier\Controller\Atelier@showCommand');
    $router->delete('/machines/{id}', '\Atelier\Controller\Atelier@deleteMachineProjects');
    $router->put('/scan-projects/{id}', '\Atelier\Controller\Atelier@scanProjects');
    $router->post('/auth/{id}', '\Atelier\Controller\Atelier@auth');
    $router->put('/project-command/{id}/{command}', '\Atelier\Controller\Atelier@runProjectCommand');
    $router->get('/run-logs', '\Atelier\Controller\Atelier@showRunLogs');
    $router->get('/run-logs/{id}', '\Atelier\Controller\Atelier@showRunLog');

    $router->get("/info-logs-directories", '\Atelier\Controller\Atelier@showInfoLogsDirectories');
    $router->get("/error-logs-directories", '\Atelier\Controller\Atelier@showErrorLogsDirectories');
    $router->get("/info-logs/{name}", '\Atelier\Controller\Atelier@showInfoLogs');
    $router->get("/error-logs/{name}", '\Atelier\Controller\Atelier@showErrorLogs');
    $router->get("/get-logs/{name}/{type}", '\Atelier\Controller\Atelier@getLogs');

    $router->run();

} catch (Exception $e) {
    Logger::error($e->getMessage());
    Logger::error($e->getTraceAsString());
    throw $e;
}