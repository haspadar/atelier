<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<button type="button" class="btn btn-primary add-machine" >
    Добавить машину
</button>

<button type="button" class="btn btn-dark" data-bs-toggle="modal" title="Запустить команду на всех машинах">
    Запустить общую bash-команду
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
<div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Новая машина</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="post">
                    <div class="mb-1">
                        <label for="host" class="col-form-label">Название</label>
                        <input type="text" class="form-control" name="host" id="host">
                        <div class="invalid-host invalid-feedback"></div>
                    </div>
                    <div class="mb-1">
                        <label for="ip" class="col-form-label">IP</label>
                        <input type="text" class="form-control is-invalid" id="ip" name="ip" >
                        <div class="invalid-ip invalid-feedback"></div>
                    </div>

                    <div class=" d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

