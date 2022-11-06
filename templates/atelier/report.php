<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\CommandReport $report
 */
$report = $this->data['report'];
?>

<dl>
    <dt>Команда</dt>
    <dd>
        <a href="/commands/<?=$report->getCommand()->getId()?>">
            <?=$report->getCommand()->getName()?>
        </a>
    </dd>
    <dt>Проект</dt>
    <dd>
        <a href="/projects/<?=$report->getProject()->getId()?>">
            <?=$report->getProject()->getName()?>
        </a>
    </dd>
    <dt>Машина</dt>
    <dd>
        <a href="/machines/<?=$report->getProject()->getMachine()->getId()?>">
            <span class="badge badge-primary">
                <?=$report->getProject()->getMachine()->getHost()?>
            </span>
        </a>
    </dd>
    <dt>Время начала</dt>
    <dd class="small text-muted">
        <?=\Atelier\Time::timeHuman($report->getStartTime())?>
    </dd>
    <dt>Время завершения</dt>
    <dd class="small text-muted">
        <?=\Atelier\Time::timeHuman($report->getFinishTime())?>
    </dd>
    <dt>Запуск</dt>
    <dd>
        <a href="/run-logs/<?=$report->getRunLogId()?>">
            <?=$report->getRunLogId()?>
        </a>
    </dd>

    <dt>Ответ</dt>
    <dd class="text-muted small">
        <?=nl2br($report->getResponse())?>
    </dd>

</dl>
