<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#machinesBashModal">
    Запустить bash-команду
</button>

<table class="table">
    <thead>
    <tr>
        <th>Машина</th>
        <th>IP</th>
        <th>Php-Fpm</th>
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
                <a href="/machines/<?=$machine->getId()?>" class="text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="right" title="<?=$machine->getIp()?>">
                    <?=$machine->getHost()?>
                </a>
            </td>
            <td>
                <?=$machine->getIp()?>
            </td>
            <td>
                <?php if ($projects = $machine->getProjects()) :?>
                    <a href="/machines/<?=$machine->getId()?>" class="text-decoration-none">
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

                <?php foreach (array_unique(array_map(fn(\Atelier\Project $project) => $project->getTypeName(), $projects)) as $type) : ?>
                    <span class="badge badge-primary">
                        <?=$type?>
                    </span>
                <?php endforeach;?>
            </td>
            <td class="text-muted">
                <?=$machine->getPhpFpmTraffic()?> запр./сек.
            </td>
        </tr>

    <?php endforeach;?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="machinesMashModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>