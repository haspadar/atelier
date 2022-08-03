<?php /** @var $this League\Plates\Template\Template */

use Atelier\Command; ?>
<?php $this->layout('layout');?>

<table class="table">
    <thead>
    <tr>
        <th>Команда</th>
        <th>Начало запуска</th>
        <th>Конец запуска</th>
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
                <?=$command->getRunTime()?->format('d.m.Y H:i:s')?>
            </td>
            <td>
                <?=$command->getFinishTime()?->format('d.m.Y H:i:s')?>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>