<?php /** @var $this League\Plates\Template\Template */

use Atelier\ProjectType;
use Atelier\Report;
use Atelier\Time;
use Atelier\Url; ?>
<?php $this->layout('layout');?>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Команда</th>
        <th>Проект</th>
        <th>Ответ</th>
        <th>Время запуска</th>
    </tr>
    </thead>
    <tbody>

    <div class="d-flex justify-content-start">
        <div class="btn-group me-3" role="group">
            <a class="btn btn-outline-primary <?php if (!$this->data['project_type_id']):?>active<?php endif;?>" href="<?=(new Url())->generate(['project_type_id' => null, 'page' => 1])?>">Все</a>
            <?php
            /**
             * @var ProjectType $projectType
             */?>
            <?php foreach ($this->data['project_types'] as $projectType) :?>
                <a class="btn btn-outline-primary <?php if ($this->data['project_type_id'] == $projectType->getId()):?>active<?php endif;?>" href="<?=(new Url())->generate(['project_type_id' => $projectType->getId(), 'page' => 1])?>">
                    <?=$projectType->getName()?>
                </a>
            <?php endforeach;?>
        </div>

        <div class=" btn-group" role="group">
            <a href="<?=(new Url())->generate(['period' => null, 'page' => 1])?>" class="btn btn-outline-primary <?php if (!$this->data['period']) :?>active<?php endif;?>">Все</a>
            <a href="<?=(new Url())->generate(['period' => 'today', 'page' => 1])?>" class="btn btn-outline-primary <?php if ($this->data['period'] == 'today') :?>active<?php endif;?>">Сегодня</a>
            <a href="<?=(new Url())->generate(['period' => 'yesterday', 'page' => 1])?>" class="btn btn-outline-primary <?php if ($this->data['period'] == 'yesterday') :?>active<?php endif;?>">Вчера</a>
            <a href="<?=(new Url())->generate(['period' => 'week', 'page' => 1])?>" class="btn btn-outline-primary <?php if ($this->data['period'] == 'week') :?>active<?php endif;?>">За неделю</a>
            <a href="<?=(new Url())->generate(['period' => 'month', 'page' => 1])?>" class="btn btn-outline-primary <?php if ($this->data['period'] == 'month') :?>active<?php endif;?>">За месяц</a>
        </div>
    </div>



    <?php
    /**
     * @var Report $report
     */
    foreach ($this->data['reports'] as $report) :?>
        <tr>
            <td>
                <a href="/reports/<?=$report->getId()?>">
                    <?=$report->getId()?>
                </a>
            </td>
            <td>
                <a href="/commands/<?=$report->getCommand()->getId()?>">
                    <?=$report->getCommand()->getName()?>
                </a>
            </td>
            <td>
                <a href="/projects/<?=$report->getProject()->getId()?>" data-bs-toggle="tooltip" title="Машина <?=$report->getProject()->getMachine()->getHost()?>">
                    <?=$report->getProject()->getName()?>
                </a>
            </td>
            <td class="small">
                <?=$report->getShortResponse()?>
            </td>
            <td class="text-muted small" data-bs-toggle="tooltip" title="<?php if ($report->getFinishTime()) :?>За <?= Time::diffInGenitive($report->getStartTime(), $report->getFinishTime())?><?php else :?>Не отработала до конца<?php endif?>">
                <?= Time::timeHuman($report->getStartTime())?>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>
<?= $this->insert('partials/pagination', ['url' => '/reports?page=%s']);