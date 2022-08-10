<?php

namespace Atelier;

use Atelier\Model\RunLogs;

class Run
{
    protected \DateTime|null $pingTime = null;

    private int $runId;

    public function __construct() {
        $memory = memory_get_usage(true);
        $this->runId = (new RunLogs())->add([
            'start_time' => (new \DateTime())->format('Y-m-d H:i:s'),
            'user' => $this->getUser(),
            'is_cron' => $this->isCron() ? 1 : 0,
            'is_cli' => $this->isCli() ? 1 : 0,
            'script' => $this->getScriptWithParameters(),
            'pid' => $this->getPid() ?: 0,
            'memory' => $memory,
            'memory_human' => $this->getMemoryHuman($memory)
        ]);
    }

    public function getId(): int
    {
        return $this->runId;
    }

    public function ping()
    {
        if (!$this->pingTime || (new \DateTime())->getTimestamp() - $this->pingTime->getTimestamp() >= 5) {
            $this->pingTime = new \DateTime();
            $memory = memory_get_usage(true);
            Logger::debug('Run ping at ' . $this->pingTime->format('H:i:s') . ', memory ' . $this->getMemoryHuman($memory));
            (new RunLogs())->update([
                'ping_time' => $this->pingTime->format('Y-m-d H:i:s'),
                'memory' => $memory,
                'memory_human' => $this->getMemoryHuman($memory)
            ], $this->runId);
        }
    }

    public function finish()
    {
        $finishTime = new \DateTime();
        (new RunLogs())->update([
            'finish_time' => $finishTime->format('Y-m-d H:i:s')
        ], $this->runId);
        Logger::debug('Run finished at ' . $finishTime->format('H:i:s'));
    }

    public function getMemoryHuman(int $memory): string
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

        return round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * @return bool
     */
    private function isCron(): bool
    {
        return !isset($_SERVER['TERM']);
    }

    /**
     * @return bool
     */
    private function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * @return false|int
     */
    private function getPid(): int|false
    {
        return \getmypid();
    }

    /**
     * @return string
     */
    private function getScriptWithParameters(): string
    {
        return implode(' ', $_SERVER['argv'] ?? []);
    }

    /**
     * @return string
     */
    private function getUser(): string
    {
        return \get_current_user();
    }
}