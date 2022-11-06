<?php

namespace Atelier\Controller;

use Atelier\Check;
use Atelier\Command\ExtractNewProjects;
use Atelier\Commands;
use Atelier\Debug;
use Atelier\Directory;
use Atelier\Filter;
use Atelier\Flash;
use Atelier\Machines;
use Atelier\Check\Type;
use Atelier\Checks;
use Atelier\Project;
use Atelier\ProjectCommand;
use Atelier\Projects;
use Atelier\CommandReports;
use Atelier\CommandReport;
use Atelier\RotatorFragments;
use Atelier\RunLogs;
use Atelier\Validator;
use League\Plates\Engine;
use Atelier\Url;
use League\Plates\Extension\Asset;

class Atelier
{
    private Engine $templatesEngine;
    private Url $url;

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
        $this->redirect('/machines');
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

    public function getAccessLogTraffic(int $projectId)
    {
        $project = Projects::getProject($projectId);
        $accessLog = $project->getAccessLog();
        $this->showJsonResponse([
            'access_log' => $accessLog,
            'traffic' => ($project->getMachine()->createSsh()->exec(
                "cat $accessLog | awk '{print $4}' | uniq -c | sort -rn | head"
            ))
        ]);
    }

    public function ignoreCheckProject(int $checkId)
    {
        $check = Checks::getById($checkId);
        if ($check->getProjectId()) {
            $check->ignoreProject();
            Flash::addSuccess('Проект не будет проверяться');
            $this->showJsonResponse(['success' => true, 'redirect_url' => '/checks#' . $check->getTypeName()]);
        }
    }

    public function ignoreCheckMachine(int $checkId)
    {
        $check = Checks::getById($checkId);
        if ($check->getMachineId()) {
            $check->ignoreMachine();
            Flash::addSuccess('Машина не будет проверяться');
            $this->showJsonResponse(['success' => true, 'redirect_url' => '/checks#' . $check->getTypeName()]);
        }
    }

    public function ignoreCheck(int $checkId)
    {
        $check = Checks::getById($checkId);
        $check->ignore();
        Flash::addSuccess('Сообщение скрыто');
        $this->showJsonResponse(['success' => true, 'redirect_url' => '/checks#' . $check->getTypeName()]);
    }

    public function showCheck(int $id)
    {
        $check = Checks::getById($id);
        $this->templatesEngine->addData([
            'title' => 'Проверка ' . $id,
            'check' => $check,
            'command' => Commands::getCommand($check->getCommandId()),
            'report' => $check->getProjectId()
                ? CommandReports::getProjectLastReport($check->getCommandId(), $check->getProjectId())
                : CommandReports::getMachineLastReport($check->getCommandId(), $check->getMachineId())
        ]);
        echo $this->templatesEngine->make('check');
    }

    public function showChecks()
    {
        $criticalChecksCount = Checks::getChecksCount(Type::CRITICAL);
        $warningChecksCount = Checks::getChecksCount(Type::WARNING);
        $infoChecksCount = Checks::getChecksCount(Type::INFO);
        $criticalPageNumber = $this->getQueryParam('critical_page', 1);
        $warningPageNumber = $this->getQueryParam('warning_page', 1);
        $infoPageNumber = $this->getQueryParam('info_age', 1);
        $limit = 25;
        $criticalOffset = ($criticalPageNumber - 1) * $limit;
        $warningOffset = ($warningPageNumber - 1) * $limit;
        $infoOffset = ($infoPageNumber - 1) * $limit;
        $this->templatesEngine->addData([
            'title' => 'Проверки',
            'critical_checks' => Checks::getChecks(Type::CRITICAL, $limit, $criticalOffset),
            'critical_count' => $criticalChecksCount,
            'critical_page' => $criticalPageNumber,
            'critical_pages_count' => ceil($criticalChecksCount / $limit),

            'warning_checks' => Checks::getChecks(Type::WARNING, $limit, $warningOffset),
            'warning_count' => $warningChecksCount,
            'warning_page' => $warningPageNumber,
            'warning_pages_count' => ceil($warningChecksCount / $limit),

            'info_checks' => Checks::getChecks(Type::INFO, $limit, $infoOffset),
            'info_count' => $infoChecksCount,
            'info_page' => $infoPageNumber,
            'info_pages_count' => ceil($infoChecksCount / $limit),
        ]);
        echo $this->templatesEngine->make('checks');
    }

    public function deleteMachine(int $id)
    {
        Machines::delete($id);
        Flash::addSuccess('Машина удалена');
        $this->showJsonResponse(['success' => true]);
    }

    public function updateMachine(int $id)
    {
        $host = Filter::get($this->getPutParam('host'));
        $ip = Filter::get($this->getPutParam('ip'));
        $errors = Validator::validateMachine($host, $ip, $id);
        if (!$errors) {
            Machines::updateMachine($host, $ip, $id);
            Flash::addSuccess('Машина обновлена');
            $this->showJsonResponse(['success' => true]);
        }  else {
            $this->showJsonResponse(['errors' => $errors]);
        }
    }

    public function addMachine()
    {
        $host = Filter::get($this->getPostParam('host'));
        $ip = Filter::get($this->getPostParam('ip'));
        $errors = Validator::validateMachine($host, $ip);
        if (!$errors) {
            Machines::addMachine($host, $ip);
            Flash::addSuccess('Машина добавлена');
            $this->showJsonResponse(['success' => true]);
        }  else {
            $this->showJsonResponse(['errors' => $errors]);
        }
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
        $projectTypeId = intval($this->getQueryParam('project_type_id', 0));
        $period = $this->getQueryParam('period');
        $reportsCount = CommandReports::getReportsCount($projectTypeId, $period);
        $this->templatesEngine->addData([
            'title' => 'Репорты',
            'reports' => CommandReports::getReports($projectTypeId, $period, $limit, $offset),
            'project_types' => Projects::getTypes(),
            'project_type_id' => $projectTypeId,
            'period' => $period,
            'count' => $reportsCount,
            'page' => $pageNumber,
            'pages_count' => ceil($reportsCount / $limit),
        ]);
        echo $this->templatesEngine->make('reports');
    }

    public function showInfoLogsDirectories()
    {
        $this->templatesEngine->addData([
            'title' => 'Все логи',
            'type' => 'info',
            'directories' => Directory::getLogsDirectories(),
        ]);
        echo $this->templatesEngine->make('logs-directories');
    }

    public function showErrorLogsDirectories()
    {
        $this->templatesEngine->addData([
            'title' => 'Все ошибки',
            'type' => 'error',
            'directories' => Directory::getLogsDirectories(),
        ]);
        echo $this->templatesEngine->make('logs-directories');
    }

    public function showInfoLogs(string $name)
    {
        $this->templatesEngine->addData([
            'title' => 'Логи "' . $name . '"',
            'type' => 'info',
            'directory' => $name,
            'breadcrumbs' => array_merge([[
                'title' => 'Все логи',
                'url' => '/info-logs-directories'
            ], [
                'title' => 'Логи "' . $name . '"',
            ]])
        ]);
        echo $this->templatesEngine->make('logs');
    }

    public function showErrorLogs(string $name)
    {
        $this->templatesEngine->addData([
            'title' => 'Ошибки "' . $name . '"',
            'type' => 'error',
            'directory' => $name,
            'breadcrumbs' => array_merge([[
                'title' => 'Все ошибки',
                'url' => 'error-logs-directories'
            ], [
                'title' => 'Ошибки "' . $name . '"',
            ]])
        ]);
        echo $this->templatesEngine->make('logs');
    }

    public function getLogs(string $name, string $type)
    {
        $this->showJsonResponse(['logs' => array_reverse(\Atelier\Logs::getLogs($name, $type))]);
    }

    public function showCommands()
    {
        $this->templatesEngine->addData([
            'title' => 'Команды',
            'commands' => Commands::getCommands()
        ]);
        echo $this->templatesEngine->make('commands');
    }

    public function showRunLogs()
    {
        $pageNumber = $this->getQueryParam('page', 1);
        $limit = 25;
        $offset = ($pageNumber - 1) * $limit;
        $runLogsCount = RunLogs::getRunLogsCount();
        $this->templatesEngine->addData([
            'title' => 'Запуски',
            'run_logs' => RunLogs::getRunLogs($limit, $offset),
            'page' => $pageNumber,
            'pages_count' => ceil($runLogsCount / $limit),
            'count' => $runLogsCount
        ]);
        echo $this->templatesEngine->make('run-logs');
    }

    public function showRunLog(int $id)
    {
        $runLog = RunLogs::getRunLog($id);
        $this->templatesEngine->addData([
            'title' => 'Запуск ' . $runLog->getId(),
            'run_log' => $runLog
        ]);
        echo $this->templatesEngine->make('run-log');
    }

    public function showCommand(int $id)
    {
        $command = Commands::getCommand($id);
        $pageNumber = $this->getQueryParam('page', 1);
        $limit = 25;
        $offset = ($pageNumber - 1) * $limit;
        $this->templatesEngine->addData([
            'title' => 'Команда "' . $command->getName() . '"',
            'command' => $command,
            'reports' => CommandReports::getCommandReports($command->getId(), $limit, $offset),
            'project_types' => Projects::getTypes(),
            'command_project_types' => Commands::getCommandProjectTypes($command)
        ]);
        echo $this->templatesEngine->make('command');
    }

    public function updateCommandProjectTypes(int $id): void
    {
        $typeIds = Filter::getIntArray($this->getPutParam('type_ids'));
        Commands::updateCommandTypes($id, $typeIds);
        Flash::addSuccess('Типы программ обновлены');
        $this->showJsonResponse(['success' => true]);
    }

    public function showReport(int $id)
    {
        $report = CommandReports::getReport($id);
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
        /**
         * @var ProjectCommand $command
         */
        $command = Commands::getCommandByName($commandName);
        $report = $command->runForAll([$project]);
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
            $command = new ExtractNewProjects();
            $this->showJsonResponse(['report' => $command->run($machine)]);
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
        Projects::deleteProject($project);
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
            'last_reports' => CommandReports::getProjectLastReports($project, $commands),
            'rotator_fragments' => RotatorFragments::getByProject($project)
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

    private function getPutParam(string $name): mixed
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

    private function getQueryParam(string $name, string $default = ''): string
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