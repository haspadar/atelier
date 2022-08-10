<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <td>Команды</td>
        <td>Проекты</td>
        <th>Начало</th>
        <th>Конец</th>
        <th>Память</th>

<!--        <th>Запустил</th>-->
<!--        <th>Cron</th>-->
<!--        <th>Командная строка</th>-->
<!--        <th>Скрипт</th>-->
<!--        <th>Pid</th>-->
<!--        <th>Последнее обновление</th>-->

    </tr>
    </thead>
    <tbody>


    <?php
    /**
     * @var \Atelier\RunLog $runLog
     */
    foreach ($this->data['run_logs'] as $runLog) :?>
        <tr>
            <td>
                <a href="/run-logs/<?=$runLog->getId()?>">
                    <?=$runLog->getId()?>
                </a>
            </td>
            <td>
                <?php foreach ($runLog->getCommands() as $command) :?>
                    <a href="/commands/<?=$command->getId()?>">
                        <?=$command->getName()?>
                    </a>
                <?php endforeach;?>
            </td>
            <td>
                <div data-bs-toggle="tooltip" title="<?=implode(', ', array_map(fn(\Atelier\Project $project) => $project->getName(), $runLog->getProjects()))?>">
                    <?=count($runLog->getProjects())?> <?=\Atelier\Plural::get(count($runLog->getProjects()), 'проект', 'проекта', 'проектов')?>
                </div>
            </td>
            <td class="text-muted">
                <?=\Atelier\Time::timeHuman($runLog->getStartTime())?>
            </td>
            <td class="text-muted">
                <?=\Atelier\Time::timeHuman($runLog->getFinishTime())?>
            </td>
            <td>
                <?=$runLog->getMemoryHuman()?>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>