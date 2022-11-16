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
    $router->get('/checks', '\Atelier\Controller\Atelier@showChecks');
    $router->get('/checks/{id}', '\Atelier\Controller\Atelier@showCheck');
    $router->delete('/checks/{id}', '\Atelier\Controller\Atelier@ignoreCheck');
    $router->delete('/check-projects/{id}', '\Atelier\Controller\Atelier@ignoreCheckProject');
    $router->delete('/check-machines/{id}', '\Atelier\Controller\Atelier@ignoreCheckMachine');
    $router->get('/machines', '\Atelier\Controller\Atelier@showMachines');
    $router->get('/machines/{id}', '\Atelier\Controller\Atelier@showMachine');
    $router->put('/machines/{id}', '\Atelier\Controller\Atelier@updateMachine');
    $router->post('/machines', '\Atelier\Controller\Atelier@addMachine');
    $router->delete('/machines/{id}', '\Atelier\Controller\Atelier@deleteMachine');
    $router->delete('/machine-projects/{id}', '\Atelier\Controller\Atelier@deleteMachineProjects');
    $router->get('/reports', '\Atelier\Controller\Atelier@showReports');
    $router->get('/reports/{id}', '\Atelier\Controller\Atelier@showReport');
    $router->get('/commands', '\Atelier\Controller\Atelier@showCommands');
    $router->get('/commands/{id}', '\Atelier\Controller\Atelier@showCommand');
    $router->put('/command-project-types/{id}', '\Atelier\Controller\Atelier@updateCommandProjectTypes');
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
    $router->get("/get-access-log-traffic/{projectId}", '\Atelier\Controller\Atelier@getAccessLogTraffic');
    $router->get("/get-machine-access-log-traffic/{machineId}", '\Atelier\Controller\Atelier@getMachineAccessLogTraffic');
    $router->get("/get-machine-php-fpm-traffic/{machineId}", '\Atelier\Controller\Atelier@getMachinePhpFpmTraffic');
    $router->get("/get-project-access-log-traffic/{machineId}", '\Atelier\Controller\Atelier@getProjectAccessLogTraffic');

    $router->run();

} catch (Exception $e) {
    Logger::error($e->getMessage());
    Logger::error($e->getTraceAsString());
    throw $e;
}