<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Project $project
 */
$project = $this->data['project'];
?>

<dl>
    <dt>Путь</dt>
    <dd class="text-muted"><?=$project->getPath()?></dd>
    <dt>Тип</dt>
    <dd>
        <span class="badge badge-primary">
            <?=$project->getTypeName()?>
        </span>
    </dd>
    <dt>Машина</dt>
    <dd>
        <a href="/machines/<?=$project->getMachine()->getId()?>">
            <span class="badge badge-dark">
                <?=$project->getMachine()->getHost()?>
            </span>
        </a>
    </dd>

</dl>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Команда</th>
        <th>Описание</th>
        <th>Последний ответ</th>
    </tr>
    </thead>
    <tbody>
        <?php $commands = $project->isPalto() ? [[
                'name' => 'extractCommit',
                'comment' => 'Извлечение последнего коммита',
                'log' => $project->getLastCommit()?->format('d.m.Y H:i:s'),
            ], [
                'name' => 'extractMigration',
                'comment' => 'Извлечение последней миграции',
                'log' => $project->getLastMigrationName()
            ], [
                'name' => 'updateProject',
                'comment' => 'Обновление кода, запуск миграций',
                'log' =>  ''
            ], [
                'name' => 'runSmoke',
                'comment' => 'Запуск тестов',
                'log' => $project->getSmokeLastTime()?->format('d.m.Y H:i:s') . '<br>' . $project->getSmokeLastReport(),
                'tooltip' => $project->getSmokeLastReport()
            ]
        ] : [];?>
        <?php foreach ($this->data['commands'] as $command) :?>
            <tr>
                <td>
                    <a href="javascript:void(0);" class="run-project-command text-nowrap" title="Запустить" data-bs-toggle="tooltip" data-id="<?=$project->getId()?>" data-command="<?=$command->getName()?>">
                        <i class="bi bi-play run-icon"></i>
                        <div class="spinner-border spinner-border-sm loading-icon d-none"></div>
                        <?=$command->getName()?>
                    </a>
                    <div class="text-danger small error"></div>
                </td>
                <td><?=$command->getComment()?></td>
                <?php /** @var $report ?\Atelier\Report */?>
                <?php $report = $this->data['last_reports'][$command->getId()] ?? null;?>
                <td class="small text-muted text-truncate" style="max-width: 200px;" title="<?=$report?->getTimeReportHtml()?>" data-bs-html="true" data-bs-toggle="tooltip">
                    <?=$report?->getShortResponse()?>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>

<div class="alert alert-secondary report d-none small" id="response"></div>

<button class="btn btn-danger btn-sm delete-project" data-id="<?=$project->getId()?>">Удалить</button>

