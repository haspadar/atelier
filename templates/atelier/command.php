<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Command $command
 */
$command = $this->data['command'];
?>

<dl>
    <dt>Комментарий</dt>
    <dd><?=$command->getComment()?></dd>
</dl>


<h2>Репорты</h2>
<table class="table">
    <thead>
        <th>ID</th>
        <th>Проект</th>
        <th>Ответ</th>
        <th>Время начала</th>
    </thead>
    <tbody>
        <?php
        /**
         * @var $report \Atelier\Report
         */
        ?>
        <?php foreach ($this->data['reports'] as $report) :?>
            <tr>
                <td>
                    <a href="/reports/<?=$report->getId()?>">
                        <?=$report->getId()?>
                    </a>
                </td>
                <td>
                    <a href="/projects/<?=$report->getProject()->getId()?>">
                        <?=$report->getProject()->getName()?>
                    </a>
                </td>
                <td>
                    <?=$report->getShortResponse()?>
                </td>
                <td class="text-muted small" data-bs-toggle="tooltip" title="<?php if ($report->getFinishTime()) :?>За <?=\Atelier\Time::diffInGenitive($report->getStartTime(), $report->getFinishTime())?><?php else :?>Не отработала до конца<?php endif?>">
                    <?=\Atelier\Time::timeHuman($report->getStartTime())?>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>