<?php /** @var $this League\Plates\Template\Template */

use Atelier\Project;
use Atelier\Report;
use Atelier\RotatorFragment;
use Atelier\Time;

?>

<?php $this->layout('layout');?>
<?php
/**
 * @var Project $project
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

<h2>Последние репорты</h2>
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
                <?php /** @var $report ?Report */?>
                <?php $report = $this->data['last_reports'][$command->getId()] ?? null;?>
                <td class="small text-muted text-truncate" style="max-width: 200px;" title="<?=$report?->getTimeReportHtml()?>" data-bs-html="true" data-bs-toggle="tooltip">
                    <?=$report?->getShortResponse()?>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>

<h2>Фрагменты с ротатором</h2>
<?php if ($rotatorFragments = $this->data['rotator_fragments']) :?>
    <?php
    /**
     * @var $rotatorFragments RotatorFragment[]
     */
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Поле</th>
                <th>Файл</th>
                <th>Фрагмент</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rotatorFragments as $rotatorFragment) :?>
            <tr>
                <td class="small text-muted">
                    <?=$rotatorFragment->getField()?>
                </td>
                <td class="small text-muted">
                    <?=$rotatorFragment->getPath()?>
                </td>
                <td class="text-muted small" data-bs-toggle="tooltip" title="Найден <?= Time::timeHuman($rotatorFragment->getCreateTime())?>">
                    <?=$rotatorFragment->getFragment()?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else :?>
    <div class="text-muted">
        Ротатор не найден
    </div>
<?php endif;?>

<div class="alert alert-secondary report d-none small" id="response"></div>

<button class="btn btn-danger btn-sm delete-project" data-id="<?=$project->getId()?>">Удалить проект</button>

