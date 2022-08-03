<?php

namespace Atelier\Controller;

use Atelier\Commands;
use Atelier\Debug;
use Atelier\Directory;
use Atelier\Filter;
use Atelier\Flash;
use Atelier\Garage;
use Atelier\Project\ProjectType;
use Atelier\Projects;
use Atelier\Run;
use League\Plates\Engine;
use Atelier\Url;
use League\Plates\Extension\Asset;
use phpseclib3\Crypt\PublicKeyLoader;

class Atelier
{
    private Engine $templatesEngine;

    public function __construct()
    {
        $this->templatesEngine = new Engine(Directory::getTemplatesDirectory() . '/atelier');
        $this->templatesEngine->loadExtension(new Asset(Directory::getPublicDirectory(), false));
        $this->url = new Url();
        $this->templatesEngine->addData([
            'flash' => Flash::receive(),
            'url' => $this->url,
        ]);
    }

    public function showIndex()
    {
        $this->redirect('/garderobe');
    }

    public function showMachine(int $id)
    {
        $machine = Garage::getMachine($id);
        $this->templatesEngine->addData([
            'title' => 'Машина "' . $machine->getHost(),
            'machine' => $machine
        ]);
        echo $this->templatesEngine->make('machine');
    }

    public function showCommands()
    {
        $this->templatesEngine->addData([
            'title' => 'Команды',
            'commands' => Commands::getCommands()
        ]);
        echo $this->templatesEngine->make('commands');
    }

    public function showGarage()
    {
        $this->templatesEngine->addData([
            'title' => 'Гараж',
            'machines' => Garage::getMachines()
        ]);
        echo $this->templatesEngine->make('garage');
    }

    public function auth(int $machineId): void
    {
        $machine = Garage::getMachine($machineId);
        $ssh = $machine->createSsh(
            $this->getPutParam('login'),
            $this->getPutParam('password')
        );
        if ($ssh->getError()) {
            $this->showJsonResponse(['error' => $ssh->getError()]);
        } else {
            $this->showJsonResponse(['success' => true]);
        }
    }

    public function runProjectCommand(int $projectId, string $commandName): void
    {
        $project = Projects::getProject($projectId);
        if (method_exists($project, $commandName)) {
            $run = new Run($commandName, $project->getId());
            $ssh = $project->getMachine()->createSsh(
                'km',
                PublicKeyLoader::load(file_get_contents('/Users/haspadar/.ssh/id_rsa_km'))
            );
            $response = $project->$commandName($ssh);
            $run->finish();
            $this->showJsonResponse(['success' => true, 'response' => nl2br($response)]);
        } else {
            $this->showJsonResponse(['error' => 'Команда "' . $commandName . '" не найдена']);
        }
    }

    public function scanProjects(int $machineId): void
    {
        $machine = Garage::getMachine($machineId);
        $ssh = $machine->createSsh(
            $this->getPutParam('login'),
            $this->getPutParam('password')
        );
        if (!$ssh->getError()) {
            $directories = $machine->scanProjectDirectories();
            if ($newDirectories = $machine->getNewDirectories($directories)) {
                $machine->addProjects($newDirectories);
                $this->showJsonResponse([
                    'report' => count($newDirectories) > 1
                        ? 'Добавлены проекты ' . implode(', ', $newDirectories)
                        : 'Добавлен проект ' . $newDirectories[0]
                ]);
            } else {
                $this->showJsonResponse(['report' => 'Новые проекты не найдены']);
            }
        } else {
            $this->showJsonResponse(['error' => $ssh->getError()]);
        }
    }

    public function deleteMachineProjects(int $machineId)
    {
        $machine = Garage::getMachine($machineId);
        $machine->deleteProjects($machineId);
        Flash::addSuccess('Проекты на машине <a href="/garage/' . $machine->getId() . '">' . $machine->getHost() . '</a> удалены');
        $this->showJsonResponse(['success' => true]);
    }

    public function deleteProject(int $id)
    {
        $project = Projects::getProject($id);
        Projects::deleteProject($id);
        Flash::addWarning('Проект "' . $project->getName() . '" удалён на машине <a href="/garage/' . $project->getMachine()->getId() . '">' . $project->getMachine()->getHost() . '</a>');
        $this->showJsonResponse(['success' => true]);
    }

    public function showProject(int $id)
    {
        $project = Projects::getProject($id);
        $this->templatesEngine->addData([
            'title' => 'Проект "' . $project->getName() . '"',
            'project' => $project
        ]);
        echo $this->templatesEngine->make('project');
    }

    public function showProjects()
    {
        $this->templatesEngine->addData([
            'title' => 'Проекты',
            'palto' => Projects::getProjects(0, ProjectType::PALTO),
            'rotator' => Projects::getProjects(0, ProjectType::ROTATOR),
            'undefined' => Projects::getProjects(0, ProjectType::UNDEFINED),
//            'description' => $this->replaceHtml($page->getDescription()),
//            'h1' => $this->replaceHtml($page->getH1()),
//            'regions' => !is_numeric($limit) || intval($limit) > 0
//                ? Regions::getLiveRegions(null, intval($limit))
//                : [],
//            'live_categories' => \Palto\Categories::getLiveCategories(null, $this->region),
//            'breadcrumbs' => [],
//            'page' => $page
        ]);
        echo $this->templatesEngine->make('projects');
    }

    private function redirect(string $url)
    {
        header('Location: ' . $url, true, 301);
    }

    private function showJsonResponse(array $data = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    private function getPutParam(string $name): string
    {
        return $this->getPutParams()[$name] ?? '';
    }

    private function getPutParams(): array
    {
        parse_str(file_get_contents("php://input"),$params);

        return $params;
    }

    private function validateAuthParams(int $machineId, string $login, string $password): string
    {
        if (!$machineId) {
            return 'Укажите номер машины';
        }

        if (!$login) {
            return 'Укажите логин';
        }

        if (!$password) {
            return 'Укажите пароль';
        }

        return '';
    }

    private function getQueryParam(string $name): string
    {
        $unfiltered = $_GET[$name] ?? '';

        return Filter::get($unfiltered);
    }

    private function getPostParam(string $name): string
    {
        $unfiltered = $_POST[$name] ?? '';

        return Filter::get($unfiltered);
    }

    private function getParam(string $name): string
    {
        $param = $this->getPostParam($name);
        if (!$param) {
            $param = $this->getPutParam($name);
        }

        if (!$param) {
            $param = $this->getQueryParam($name);
        }

        return $param ?? '';
    }
}