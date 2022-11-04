<?php /** @var $this League\Plates\Template\Template */

use Atelier\RunLog;
use Atelier\Time; ?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Check $check
 */

$check = $this->data['check'];
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


<button class="btn btn-danger ignore-message" type="button" data-id="<?=$check->getId()?>">
    Игнорировать сообщение
</button>

<?php if ($check['project_id']) :?>
    <button class="btn btn-warning ignore-project" type="button" data-id="<?=$check->getId()?>">
        Не проверять проект <?=$check->getProjectName()?>
    </button>
<?php endif;?>

<button class="btn btn-warning ignore-machine" type="button" data-id="<?=$check->getId()?>">
    Не проверять машину <?=$check->getMachineHost()?>
</button>


