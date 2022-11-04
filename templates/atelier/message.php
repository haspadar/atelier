<?php /** @var $this League\Plates\Template\Template */

use Atelier\RunLog;
use Atelier\Time; ?>

<?php $this->layout('layout');?>

<?php

$message = $this->data['message'];
?>

<dl>
    <dt>Заголовок</dt>
    <dd>
        <?=$message['group_title']?>
    </dd>
    <dt>Текст</dt>
    <dd>
        <?=$message['text']?>
    </dd>
    <dt>Проект</dt>
    <dd class="text-muted">
        <?php if ($message['project_id']) :?>
            <a href="/projects/<?=$message['project_id']?>" target="_blank" class="text-decoration-none">
                <?= \Atelier\Project::extractName($message['project_path'])?>
            </a>
        <?php endif;?>
    </dd>
    <dt>Машина</dt>
    <dd class="text-muted">
        <a href="/machines/<?=$message['machine_id']?>" target="_blank" class="text-decoration-none">
            <?= $message['machine_host']?>
        </a>
    </dd>
    <dt>Время проверки</dt>
    <dd class="text-muted">
        <?= Time::timeHuman(new DateTime($message['create_time']))?>
    </dd>
</dl>


<button class="btn btn-danger ignore-message" type="button" data-id="<?=$message['id']?>">
    Игнорировать сообщение
</button>

<?php if ($message['project_id']) :?>
    <button class="btn btn-warning ignore-project" type="button" data-id="<?=$message['id']?>">
        Не проверять проект <?=\Atelier\Project::extractName($message['project_path'])?>
    </button>
<?php endif;?>

<button class="btn btn-warning ignore-machine" type="button" data-id="<?=$message['id']?>">
    Не проверять машину <?=$message['machine_host']?>
</button>


