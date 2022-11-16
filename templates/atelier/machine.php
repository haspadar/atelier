<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Machine $machine
 */
$machine = $this->data['machine'];
?>
<form class="machine" action="#" method="post">
    <input type="hidden" name="id" value="<?=$machine->getId()?>">
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="host" class="form-control" value="<?=$machine->getHost()?>">
        <div class="invalid-host invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label class="form-label">IP</label>
        <input type="text" class="form-control" name="ip" value="<?=$machine->getIp()?>">
        <div class="invalid-ip invalid-feedback"></div>
    </div>

    <button class="btn btn-secondary btn-dark scan-projects"
            type="button"
            data-id="<?=$machine->getId()?>"
    >
        Сканировать
    </button>
    <button type="submit" class="btn btn-primary" data-id="<?= $machine->getId() ?>">Сохранить</button>
    <button class="btn btn-secondary btn-danger remove-machine"
            type="button"
            data-id="<?=$machine->getId()?>"
            data-projects-count="<?=count($machine->getProjects())?>"
    >
        Удалить
    </button>

    <div class="alert alert-dark d-none col-6 scan-projects-report" role="alert" id="scan-projects-report-<?=$machine->getId()?>">
        <div class="loading">
            <span>Поиск...</span>
            <div class="spinner-border float-end" role="status" aria-hidden="true"></div>
        </div>
        <div class="text"></div>
    </div>

    <?php if ($projects = $machine->getProjects()) :?>
        <h2 class="mt-4">Проекты</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Проект</th>
                    <th>Тип</th>
                    <th>Access Log</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($projects as $project) :?>
                <tr>
                    <td>
                        <a href="/projects/<?=$project->getId()?>" class="text-decoration-none">
                            <?=$project->getName()?>
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            <?=$project->getTypeName()?>
                        </span>

                    </td>
                    <td>
                        <a href="#" class="show-access-log-traffic" data-project-id="<?=$project->getId()?>">
                            Показать нагрузку
                        </a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>


        <button class="btn btn-danger btn-sm mt-2 delete-machine-projects" data-id="<?=$machine->getId()?>">Удалить проекты</button>
    <?php else :?>
        <div class="text-muted mt-3">Нет проектов</div>
    <?php endif;?>

</form>

<div id="machine-nginx-traffic" data-machine-id="<?=$machine->getId()?>"></div>
<div id="machine-php-fpm-traffic" data-machine-id="<?=$machine->getId()?>"></div>


<div class="modal fade" id="deleteMachineModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" name="id" value="">
            <div class="modal-body">
                <p>
                    Удалить машину вместе с <?=count($machine->getProjects())?> <?=\Atelier\Plural::get(count($machine->getProjects()), 'проектом', 'проектами', 'проектами')?>
                </p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
                <button type="button" class="btn btn-danger ok">Да</button>
            </div>

        </div>
    </div>
</div>

