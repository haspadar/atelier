<?php

namespace Atelier\Controller;

use Atelier\Command;
use Atelier\Commands;
use Atelier\Debug;
use Atelier\Directory;
use Atelier\Filter;
use Atelier\Flash;
use Atelier\Machines;
use Atelier\Project\ProjectType;
use Atelier\Projects;
use Atelier\Reports;
use League\Plates\Engine;
use Atelier\Url;
use League\Plates\Extension\Asset;
use Palto\Ads;

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
        $machine = Machines::getMachine($id);
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

    public function showMachines()
    {
        $this->templatesEngine->addData([
            'title' => 'Машины',
            'machines' => Machines::getMachines()
        ]);
        echo $this->templatesEngine->make('machines');
    }

    public function showReports()
    {
        $pageNumber = $this->getQueryParam('page', 1);
        $limit = 25;
        $offset = ($pageNumber - 1) * $limit;
        $this->templatesEngine->addData([
            'title' => 'Репорты',
            'reports' => Reports::getReports($limit, $offset)
        ]);
        echo $this->templatesEngine->make('reports');
    }

    public function showReport(int $id)
    {
        $report = Reports::getReport($id);
        $this->templatesEngine->addData([
            'title' => 'Репорт ' . $report->getId(),
            'report' => $report
        ]);
        echo $this->templatesEngine->make('report');
    }

    public function auth(int $machineId): void
    {
        $machine = Machines::getMachine($machineId);
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
        $command = Commands::getCommandByName($commandName);
        $report = Commands::run($command, [$project]);
        $this->showJsonResponse(['success' => true, 'response' => nl2br($report->getResponse())]);
    }

    public function scanProjects(int $machineId): void
    {
        $machine = Machines::getMachine($machineId);
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
        $machine = Machines::getMachine($machineId);
        $machine->deleteProjects($machineId);
        Flash::addSuccess('Проекты на машине <a href="/machines/' . $machine->getId() . '">' . $machine->getHost() . '</a> удалены');
        $this->showJsonResponse(['success' => true]);
    }

    public function deleteProject(int $id)
    {
        $project = Projects::getProject($id);
        Projects::deleteProject($id);
        Flash::addWarning('Проект "' . $project->getName() . '" удалён на машине <a href="/machines/' . $project->getMachine()->getId() . '">' . $project->getMachine()->getHost() . '</a>');
        $this->showJsonResponse(['success' => true]);
    }

    public function showProject(int $id)
    {
        $project = Projects::getProject($id);
        $commands = Commands::getProjectCommands($project);
        $this->templatesEngine->addData([
            'title' => 'Проект "' . $project->getName() . '"',
            'project' => $project,
            'commands' => $commands,
            'last_reports' => Reports::getProjectLastReports($project, $commands)
        ]);
        echo $this->templatesEngine->make('project');
    }

    public function showProjects()
    {
        $this->templatesEngine->addData([
            'title' => 'Проекты',
            'grouped_projects' => Projects::getGroupedProjects()
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

    private function getQueryParam(string $name, string $default): string
    {
        $unfiltered = $_GET[$name] ?? $default;

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