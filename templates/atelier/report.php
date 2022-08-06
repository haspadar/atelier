<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Report $report
 */
$report = $this->data['report'];
?>

<dl>
    <dt>Команда</dt>
    <dd><?=$report->getCommand()->getName()?></dd>
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
    <dt>Run Log</dt>
    <dd>
        <?=$report->getRunLogId()?>
    </dd>

    <dt>Ответ</dt>
    <dd class="text-muted small">
        <?=nl2br($report->getResponse())?>
    </dd>

</dl>
