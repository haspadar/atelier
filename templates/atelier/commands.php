<?php /** @var $this League\Plates\Template\Template */

use Atelier\Command; ?>
<?php $this->layout('layout');?>

<table class="table">
    <thead>
    <tr>
        <th>Команда</th>
        <th>Комментарий</th>
        <th>Типы проектов</th>
    </tr>
    </thead>
    <tbody>


    <?php
    /**
     * @var Command $command
     */
    foreach ($this->data['commands'] as $command) :?>
        <tr>
            <td>
                <a href="/commands/<?=$command->getId()?>" class="text-decoration-none">
                    <?=$command->getName()?>
                </a>
            </td>
            <td>
                <?=$command->getComment()?>
            </td>
            <td>
                <?php if ($command instanceof \Atelier\ProjectCommand) :?>
                    <?php foreach ($command->getProjectTypes() as $projectType) :?>
                        <span class="badge badge-primary">
                            <?=$projectType->getName()?>
                        </span>
                    <?php endforeach;?>
                <?php endif;?>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>