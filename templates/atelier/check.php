<?php /** @var $this League\Plates\Template\Template */

use Atelier\RunLog;
use Atelier\Time; ?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Check $check
 */

$check = $this->data['check'];
if (!$check->getId()) :?>
    <div class="alert alert-danger alert-dismissible mt-2 fade show" role="alert">
        Сообщение удалено
    </div>
<?php elseif ($check->isIgnored()) :
?>
    <div class="alert alert-danger alert-dismissible mt-2 fade show" role="alert">
        Сообщение скрыто
    </div>
<?php
endif;
?>

<?php if ($check->getId()) :?>
    <dl>
        <dt>Заголовок</dt>
        <dd>
            <?=$check->getGroupTitle()?>
        </dd>
        <dt>Текст</dt>
        <dd>
            <?=$check->getText()?>
        </dd>
        <?php if ($check->getProjectId()) :?>
            <dt>Проект</dt>
            <dd class="text-muted">
                <a href="/projects/<?=$check->getProjectId()?>" target="_blank" class="text-decoration-none">
                    <?= $check->getProjectName()?>
                </a>

                <button class="btn btn-danger btn-sm delete-project" data-id="<?=$check->getProjectId()?>">Удалить проект</button>
            </dd>
        <?php endif;?>

        <dt>Машина</dt>
        <dd class="text-muted">
            <a href="/machines/<?=$check->getMachineId()?>" target="_blank" class="text-decoration-none">
                <?= $check->getMachineHost()?>
            </a>
        </dd>
        <dt>Проверка</dt>
        <dd class="text-muted">
            <?= $check->getName()?>
        </dd>
        <?php /** @var \Atelier\Command $command */?>
        <?php if ($command = $this->data['command']) :?>
            <dt>Команда</dt>
            <dd class="text-muted">
                <a href="/commands/<?=$command->getId()?>" target="_blank" class="text-decoration-none">
                    <?= $command->getName()?>
                </a>
            </dd>
        <?php endif?>

        <?php /** @var \Atelier\CommandReport $report */?>
        <?php if ($report = $this->data['report']) :?>
            <dt>Репорт</dt>
            <dd>
                <div class="text-muted small">(<?= Time::timeHuman($report->getStartTime())?>)</div>
                <a href="/reports/<?=$report->getId()?>" target="_blank" class="text-decoration-none">
                    <?= $report->getShortResponse()?>
                </a>
            </dd>
        <?php endif?>

        <dt>Время проверки</dt>
        <dd class="text-muted">
            <?= Time::timeHuman($check->getCreateTime())?>
        </dd>
    </dl>

    <?php if (!$check->isIgnored()) :?>
        <button class="btn btn-danger ignore-check" type="button" data-id="<?=$check->getId()?>">
            Игнорировать сообщение
        </button>
    <?php endif;?>

    <?php if ($check->getProjectId()) :?>
        <button class="btn btn-warning ignore-check-project" type="button" data-id="<?=$check->getId()?>">
            Не проверять проект <?=$check->getProjectName()?>
        </button>
    <?php endif;?>

    <button class="btn btn-warning ignore-check-machine" type="button" data-id="<?=$check->getId()?>">
        Не проверять машину <?=$check->getMachineHost()?>
    </button>
<?php endif;


