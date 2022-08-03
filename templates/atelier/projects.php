<?php /** @var $this League\Plates\Template\Template */

use Atelier\Project;
use Atelier\Project\ProjectType; ?>
<?php $this->layout('layout'); ?>

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active small" data-bs-toggle="pill" data-bs-target="#<?=ProjectType::PALTO->name?>" type="button" role="tab" aria-controls="pills-home" aria-selected="true"><?=ProjectType::PALTO->name?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link small" data-bs-toggle="pill" data-bs-target="#<?=ProjectType::ROTATOR->name?>" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"><?=ProjectType::ROTATOR->name?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link small" data-bs-toggle="pill" data-bs-target="#<?=ProjectType::UNDEFINED->name?>" type="button" role="tab" aria-controls="pills-contact" aria-selected="false"><?=ProjectType::UNDEFINED->name?></button>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="<?=ProjectType::PALTO->name?>" role="tabpanel" tabindex="0">
        <table class="table">
            <thead>
            <tr>
                <th>Проект</th>
                <th>Машина</th>
            </tr>
            </thead>
            <tbody>


            <?php
            /**
             * @var Project $palto
             */
            foreach ($this->data['palto'] as $palto) :?>
                <tr>
                    <td>
                        <a href="/projects/<?=$palto->getId()?>" class="text-decoration-none">
                            <?=$palto->getName()?>
                        </a>
                    </td>
                    <td>
                        <a href="/garage/<?=$palto->getMachine()->getId()?>" class="text-decoration-none">
                    <span class="badge badge-dark">
                        <?=$palto->getMachine()->getHost()?>
                    </span>
                        </a>
                    </td>
                </tr>

            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="tab-pane fade" id="<?=ProjectType::ROTATOR->name?>" role="tabpanel" tabindex="0">
        <table class="table">
            <thead>
            <tr>
                <th>Проект</th>
                <th>Машина</th>
            </tr>
            </thead>
            <tbody>


            <?php
            /**
             * @var Project $rotator
             */
            foreach ($this->data['rotator'] as $rotator) :?>
                <tr>
                    <td>
                        <a href="/projects/<?=$rotator->getId()?>" class="text-decoration-none">
                            <?=$rotator->getName()?>
                        </a>
                    </td>
                    <td>
                        <a href="/garage/<?=$rotator->getMachine()->getId()?>" class="text-decoration-none">
                    <span class="badge badge-secondary">
                        <?=$rotator->getMachine()->getHost()?>
                    </span>
                        </a>
                    </td>
                </tr>

            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="tab-pane fade" id="<?=ProjectType::UNDEFINED->name?>" role="tabpanel" tabindex="0">
        <table class="table">
            <thead>
            <tr>
                <th>Проект</th>
                <th>Машина</th>
            </tr>
            </thead>
            <tbody>


            <?php
            /**
             * @var Project $undefined
             */
            foreach ($this->data['undefined'] as $undefined) :?>
                <tr>
                    <td>
                        <a href="/projects/<?=$undefined->getId()?>" class="text-decoration-none">
                            <?=$undefined->getName()?>
                        </a>
                    </td>
                    <td>
                        <a href="/garage/<?=$undefined->getMachine()->getId()?>" class="text-decoration-none">
                    <span class="badge badge-secondary">
                        <?=$undefined->getMachine()->getHost()?>
                    </span>
                        </a>
                    </td>
                </tr>

            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
