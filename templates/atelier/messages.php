<?php /** @var $this League\Plates\Template\Template */

use Atelier\Message\Type;
use Atelier\Project;
use Atelier\Time; ?>
<?php $this->layout('layout');?>

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active small" data-bs-toggle="pill" data-bs-target="#<?= Type::CRITICAL->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
            <?= Type::CRITICAL->name?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link small" data-bs-toggle="pill" data-bs-target="#<?= Type::WARNING->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="false">
            <?= Type::WARNING->name?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link small" data-bs-toggle="pill" data-bs-target="#<?= Type::INFO->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="false">
            <?= Type::INFO->name?>
        </button>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <?php foreach ([
            Type::CRITICAL->name => $this->data['critical_messages'],
            Type::WARNING->name => $this->data['warning_messages'] ?? [],
            Type::INFO->name => $this->data['info_messages'] ?? [],
           ] as $messageTypeName => $groupedMessages) :?>
        <div class="tab-pane fade show <?php if (Type::CRITICAL->name == $messageTypeName) :?>active<?php endif?>" id="<?=$messageTypeName?>" role="tabpanel" tabindex="0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Заголовок</th>
                        <th>Проект</th>
                        <th>Машина</th>
                        <th>Время проверки</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedMessages as $groupTitle => $messages) :?>
                        <?php foreach ($messages as $message) :?>
                            <tr>
                                <td>
                                    <a href="/messages/<?=$message['id']?>" class="text-decoration-none"><?=$groupTitle?></a>
                                </td>
                                <td>
                                    <?php if ($message['project_id']) :?>
                                        <a href="/projects/<?=$message['project_id']?>" target="_blank" class="text-decoration-none">
                                            <?= Project::extractName($message['project_path'])?>
                                        </a>
                                    <?php endif;?>
                                </td>
                                <td>
                                    <a href="/machines/<?=$message['machine_id']?>" target="_blank" class="text-decoration-none">
                                        <?= $message['machine_host']?>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= Time::timeHuman(new DateTime($message['create_time']))?></td>
                                <td>
                                    <a href="" class="btn-danger btn-sm text-decoration-none">
                                        Игнорировать
                                    </a>
<!--                                    <a href="" class="btn-warning btn-sm text-decoration-none">-->
<!--                                        Не проверять проект-->
<!--                                    </a>-->

                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php endforeach;?>
                </tbody>
            </table>
            <?php if ($messageTypeName == Type::CRITICAL->name) :?>
                <?= $this->insert('partials/pagination', [
                    'url' => '/messages?critical_page=%s',
                    'page' => $this->data['critical_page'],
                    'count' => $this->data['critical_count'],
                    'pages_count' => $this->data['critical_pages_count'],
                ]);?>
            <?php elseif ($messageTypeName == Type::WARNING->name) :?>
                <?= $this->insert('partials/pagination', [
                    'url' => '/messages?warning_page=%s',
                    'page' => $this->data['warning_page'],
                    'count' => $this->data['warning_count'],
                    'pages_count' => $this->data['warning_pages_count'],
                ]);?>
            <?php elseif ($messageTypeName == Type::INFO->name) :?>
                <?= $this->insert('partials/pagination', [
                    'url' => '/messages?info_page=%s',
                    'page' => $this->data['info_page'],
                    'count' => $this->data['info_count'],
                    'pages_count' => $this->data['info_pages_count'],
                ]);?>
            <?php endif;?>
        </div>
    <?php endforeach;?>
</div>