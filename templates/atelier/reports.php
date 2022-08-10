<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Команда</th>
        <th>Проект</th>
        <th>Ответ</th>
        <th>Время начала</th>
    </tr>
    </thead>
    <tbody>


    <?php
    /**
     * @var \Atelier\Report $report
     */
    foreach ($this->data['reports'] as $report) :?>
        <tr>
            <td>
                <a href="/reports/<?=$report->getId()?>">
                    <?=$report->getId()?>
                </a>
            </td>
            <td>
                <?=$report->getCommand()->getName()?>
            </td>
            <td>
                <a href="/projects/<?=$report->getProject()->getId()?>" data-bs-toggle="tooltip" title="Машина <?=$report->getProject()->getMachine()->getHost()?>">
                    <?=$report->getProject()->getName()?>
                </a>
            </td>
            <td class="small">
                <?=$report->getShortResponse()?>
            </td>
            <td class="text-muted small" data-bs-toggle="tooltip" title="<?php if ($report->getFinishTime()) :?>За <?=\Atelier\Time::diffInGenitive($report->getStartTime(), $report->getFinishTime())?><?php else :?>Не отработала до конца<?php endif?>">
                <?=\Atelier\Time::timeHuman($report->getStartTime())?>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>