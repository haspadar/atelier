<?php /** @var $this League\Plates\Template\Template */

use Atelier\RunLog;
use Atelier\Time; ?>

<?php $this->layout('layout');?>

<?php

$check = $this->data['check'];
?>

<dl>
    <dt>Заголовок</dt>
    <dd>
        <?=$check['group_title']?>
    </dd>
    <dt>Текст</dt>
    <dd>
        <?=$check['text']?>
    </dd>
    <dt>Проект</dt>
    <dd class="text-muted">
        <?php if ($check['project_id']) :?>
            <a href="/projects/<?=$check['project_id']?>" target="_blank" class="text-decoration-none">
                <?= \Atelier\Project::extractName($check['project_path'])?>
            </a>
        <?php endif;?>
    </dd>
    <dt>Машина</dt>
    <dd class="text-muted">
        <a href="/machines/<?=$check['machine_id']?>" target="_blank" class="text-decoration-none">
            <?= $check['machine_host']?>
        </a>
    </dd>
    <dt>Время проверки</dt>
    <dd class="text-muted">
        <?= Time::timeHuman(new DateTime($check['create_time']))?>
    </dd>
</dl>


<button class="btn btn-danger ignore-message" type="button" data-id="<?=$check['id']?>">
    Игнорировать сообщение
</button>

<?php if ($check['project_id']) :?>
    <button class="btn btn-warning ignore-project" type="button" data-id="<?=$check['id']?>">
        Не проверять проект <?=\Atelier\Project::extractName($check['project_path'])?>
    </button>
<?php endif;?>

<button class="btn btn-warning ignore-machine" type="button" data-id="<?=$check['id']?>">
    Не проверять машину <?=$check['machine_host']?>
</button>


