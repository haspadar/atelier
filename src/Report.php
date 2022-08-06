<?php

namespace Atelier;

use Atelier\Model\Reports;
use Atelier\Project\ProjectType;
use DateTime;

class Report
{

    private Project $project;
    private Command $command;

    public function __construct(private array $report)
    {
        $this->project = Projects::getProject($this->report['project_id']);
        $this->command = Commands::getCommand($this->report['command_id']);
    }

    public function getId(): int
    {
        return $this->report['id'];
    }

    public function getCommand(): Command
    {
        return $this->command;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getShortResponse(int $length = 135): string
    {
        return Filter::shortText(self::getResponse(), $length);
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->report && $this->report['start_time']
            ? new DateTime($this->report['start_time'])
            : null;
    }

    public function getFinishTime(): ?\DateTime
    {
        return $this->report && $this->report['finish_time']
            ? new DateTime($this->report['finish_time'])
            : null;
    }

    public function getTimeReportHtml(): string
    {
        if ($this->getStartTime() && $this->getFinishTime()) {
            return Time::timeHuman($this->getStartTime())
                . '<br><span class=\'small\'>за '
                . Time::diffInGenitive($this->getStartTime(), $this->getFinishTime())
                . '</span>';
        } elseif ($this->getStartTime()) {
            return Time::timeHuman($this->getStartTime());
        }

        return '';
    }

    public function getResponse(): string
    {
        return $this->report['response'] ?? '';
    }

    public function finish(string $response)
    {
        $this->report['response'] = $response;
        $this->report['finish_time'] = (new \DateTime())->format('Y-m-d H:i:s');
        (new Reports())->update([
            'response' => $this->report['response'],
            'finish_time' => $this->report['finish_time']
        ], $this->report['id']);
    }

    public function getRunLogId()
    {
        return $this->report['run_log_id'];
    }
}