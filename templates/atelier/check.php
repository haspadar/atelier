<?php /** @var $this League\Plates\Template\Template */

use Atelier\RunLog;
use Atelier\Time; ?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Check $check
 */

$check = $this->data['check'];
if ($check->isIgnored()) :
?>
    <div class="alert alert-danger alert-dismissible mt-2 fade show" role="alert">
        Сообщение скрыто
    </div>
<?php
endif;
?>

<dl>
    <dt>Заголовок</dt>
    <dd>
        <?=$check->getGroupTitle()?>
    </dd>
    <dt>Текст</dt>
    <dd>
        <?=$check->getText()?>
    </dd>
    <dt>Проект</dt>
    <dd class="text-muted">
        <?php if ($check->getProjectId()) :?>
            <a href="/projects/<?=$check->getProjectId()?>" target="_blank" class="text-decoration-none">
                <?= $check->getProjectName()?>
            </a>
        <?php endif;?>
    </dd>
    <dt>Машина</dt>
    <dd class="text-muted">
        <a href="/machines/<?=$check->getMachineId()?>" target="_blank" class="text-decoration-none">
            <?= $check->getMachineHost()?>
        </a>
    </dd>
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


