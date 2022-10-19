<?php /** @var $this League\Plates\Template\Template */

use Atelier\RunLog;
use Atelier\Time; ?>

<?php $this->layout('layout');?>

<?php
/**
 * @var RunLog $runLog
 */
$runLog = $this->data['run_log'];
?>

<dl>
    <dt>Команды</dt>
    <dd>
        <?php foreach ($runLog->getCommands() as $command) :?>
            <a href="/commands/<?=$command->getId()?>">
                <?=$command->getName()?>
            </a>
        <?php endforeach;?>
    </dd>
    <dt>Начало</dt>
    <dd class="text-muted"><?= Time::timeHuman($runLog->getStartTime())?></dd>
    <dt>Конец</dt>
    <dd class="text-muted"><?=$runLog->getFinishTime() ? Time::timeHuman($runLog->getFinishTime()) : 'не завершилась'?></dd>
    <dt>Ping time</dt>
    <dd class="text-muted"><?=$runLog->getPingTime() ? Time::timeHuman($runLog->getPingTime()) : 'пусто'?></dd>
    <dt>Пользователь</dt>
    <dd><?=$runLog->getUser()?></dd>
    <dt>Скрипт</dt>
    <dd><?=$runLog->getScript()?></dd>
    <dt>Pid</dt>
    <dd><?=$runLog->getPid()?></dd>
    <dt>Cron</dt>
    <dd><?=$runLog->isCron() ? 'да' : 'нет'?></dd>
    <dt>Командная строка</dt>
    <dd><?=$runLog->isCli() ? 'да' : 'нет'?></dd>
    <dt>Память</dt>
    <dd><?=$runLog->getMemoryHuman()?></dd>
    <dt>Ответы</dt>
    <dd>
        <li class="list-group list-group-numbered">
            <?php foreach ($runLog->getReports() as $report) :?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <a href="/reports/<?=$report->getId()?>">
                        <?=$report->getProject()->getName()?>
                    </a>
                    <span class="text-muted small">
                        <?=$report->getShortResponse()?></span>
                    </span>
                </li>
            <?php endforeach;?>
        </li>
    </dd>

</dl>
