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

<h2 class=" mt-2">Типы программ</h2>
<div class="btn-group  command-project-types" role="group" aria-label="Basic checkbox toggle button group">
    <?php /** @var \Atelier\ProjectType $projectType */?>
    <?php foreach ($this->data['project_types'] as $projectType) :?>
        <?php $isChecked = in_array($projectType, $this->data['command_project_types'])?>
        <input type="checkbox" class="btn-check command-project-type" value="<?=$projectType->getId()?>" id="type<?=$projectType->getId()?>" autocomplete="off" <?php if ($isChecked) :?>checked<?php endif;?>>
        <label class="btn btn-outline-primary" for="type<?=$projectType->getId()?>"><?=$projectType->getName()?></label>
    <?php endforeach;?>
</div>
<div class="mt-2 mb-4">
    <button class="btn btn-primary save-command-project-types" data-command-id="<?=$this->data['command']->getId()?>">
        Сохранить
    </button>
</div>

<h2>Репорты</h2>
<table class="table">
    <thead>
        <th>ID</th>
        <th>Проект</th>
        <th>Запуск</th>
        <th>Ответ</th>
        <th>Время начала</th>
    </thead>
    <tbody>
        <?php
        /**
         * @var $report \Atelier\CommandReport
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
                    <a href="/run-logs/<?=$report->getRunLogId()?>">
                        <?=$report->getRunLogId()?>
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