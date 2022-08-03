<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<table class="table">
    <thead>
    <tr>
        <th>Машина</th>
        <th>IP</th>
        <th>Проекты</th>
    </tr>
    </thead>
    <tbody>


    <?php
    /**
     * @var \Atelier\Machine $machine
     */
    foreach ($this->data['machines'] as $machine) :?>
        <tr>
            <td>
                <a href="/garage/<?=$machine->getId()?>" class="text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="right" title="<?=$machine->getIp()?>">
                    <?=$machine->getHost()?>
                </a>
            </td>
            <td>
                <?=$machine->getIp()?>
            </td>
            <td>
                <?php if ($projects = $machine->getProjects()) :?>
                    <a href="/garage/<?=$machine->getId()?>" class="text-decoration-none">
                        <?=count($projects)?> <?=\Atelier\Plural::get(count($projects), 'проект', 'проекта', 'проектов')?>
                    </a>
                <?php else :?>
                    <a href="javascript:void(0);" data-id="<?=$machine->getId()?>" class="scan-projects small text-decoration-none text-muted">Сканировать</a>
                    <span id="scan-projects-report-<?=$machine->getId()?>" class="text-warning small d-none">
                        <span class="loading">
                            <span>Поиск...</span>
                        </span>
                        <span class="text"></span>
                    </span>
                <?php endif;?>
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>