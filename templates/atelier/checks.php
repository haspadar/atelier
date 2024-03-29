<?php /** @var $this League\Plates\Template\Template */

use Atelier\Check\Type;
use Atelier\Project;
use Atelier\Time; ?>
<?php $this->layout('layout');?>

<ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <a href="#<?= Type::CRITICAL->name?>" class="nav-link active small" data-bs-toggle="pill" data-bs-target="#<?= Type::CRITICAL->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
            <?= Type::CRITICAL->name?>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="#<?= Type::WARNING->name?>" class="nav-link small" data-bs-toggle="pill" data-bs-target="#<?= Type::WARNING->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="false">
            <?= Type::WARNING->name?>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="#<?= Type::INFO->name?>" class="nav-link small" data-bs-toggle="pill" data-bs-target="#<?= Type::INFO->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="false">
            <?= Type::INFO->name?>
        </a>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <?php foreach ([
            Type::CRITICAL->name => $this->data['critical_checks'],
            Type::WARNING->name => $this->data['warning_checks'] ?? [],
            Type::INFO->name => $this->data['info_checks'] ?? [],
           ] as $checkTypeName => $groupedChecks) :?>
        <div class="tab-pane fade show <?php if (Type::CRITICAL->name == $checkTypeName) :?>active<?php endif?>" id="<?=$checkTypeName?>" role="tabpanel" tabindex="0">
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
                    <?php foreach ($groupedChecks as $groupTitle => $checks) :?>
                        <?php /**
                         * @var \Atelier\Check $check
                         */?>
                        <?php foreach ($checks as $check) :?>
                            <tr>
                                <td>
                                    <a href="/checks/<?=$check->getId()?>" class="text-decoration-none"><?=$groupTitle?></a>
                                </td>
                                <td>
                                    <?php if ($check->getProjectId()) :?>
                                        <a href="/projects/<?=$check->getProjectId()?>" target="_blank" class="text-decoration-none">
                                            <?= $check->getProjectName()?>
                                        </a>
                                    <?php endif;?>
                                </td>
                                <td>
                                    <a href="/machines/<?=$check->getMachineId()?>" target="_blank" class="text-decoration-none">
                                        <?= $check->getMachineHost()?>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= Time::timeHuman($check->getCreateTime())?></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn-danger btn-sm text-decoration-none ignore-check" data-id="<?=$check->getId()?>">
                                        Игнорировать
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php endforeach;?>
                </tbody>
            </table>
            <?php if ($checkTypeName == Type::CRITICAL->name) :?>
                <?= $this->insert('partials/pagination', [
                    'url' => '/checks?critical_page=%s',
                    'page' => $this->data['critical_page'],
                    'count' => $this->data['critical_count'],
                    'pages_count' => $this->data['critical_pages_count'],
                ]);?>
            <?php elseif ($checkTypeName == Type::WARNING->name) :?>
                <?= $this->insert('partials/pagination', [
                    'url' => '/checks?warning_page=%s',
                    'page' => $this->data['warning_page'],
                    'count' => $this->data['warning_count'],
                    'pages_count' => $this->data['warning_pages_count'],
                ]);?>
            <?php elseif ($checkTypeName == Type::INFO->name) :?>
                <?= $this->insert('partials/pagination', [
                    'url' => '/checks?info_page=%s',
                    'page' => $this->data['info_page'],
                    'count' => $this->data['info_count'],
                    'pages_count' => $this->data['info_pages_count'],
                ]);?>
            <?php endif;?>
        </div>
    <?php endforeach;?>
</div>