<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Atelier\Machine $machine
 */
$machine = $this->data['machine'];
?>
<form class="page" action="#" method="post">
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="host" class="form-control" value="<?=$machine->getHost()?>">
    </div>
    <div class="mb-3">
        <label class="form-label">IP</label>
        <input type="text" class="form-control" name="ip" value="<?=$machine->getIp()?>">
    </div>

    <button class="btn btn-secondary btn-dark scan-projects"
            type="button"
            data-id="<?=$machine->getId()?>"
    >
        Сканировать
    </button>
    <button type="submit" class="btn btn-primary" data-id="<?= $machine->getId() ?>">Сохранить</button>
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
                <th>Проект</th>
                <th>Тип</th>
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
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>


        <button class="btn btn-danger btn-sm mt-2 delete-machine-projects" data-id="<?=$machine->getId()?>">Удалить проекты</button>
    <?php else :?>
        <div class="text-muted mt-3">Нет проектов</div>
    <?php endif;?>

</form>

