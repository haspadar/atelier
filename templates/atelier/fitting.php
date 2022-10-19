<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<dl class="row">
    <?php /** @var \Atelier\Warning $warning */?>
    <?php $warning = $this->data['warning'];?>
    <?php if ($projects = $warning->getProjects()) :?>
        <table class="table">
            <thead>
            <tr>
                <th>Проект</th>
                <th>Машина</th>
                <th>Описание</th>
            </tr>
            </thead>
            <?php foreach ($projects as $project) :?>
                <tr>
                    <td>
                        <a href="/projects/<?=$project->getId()?>">
                            <?=$project->getName()?>
                        </a>
                    </td>
                    <td>
                        <a href="/machines/<?=$project->getMachine()->getId()?>">
                            <?=$project->getMachine()->getHost()?>
                        </a>
                    </td>
                    <td>
                        <?=$warning->getProjectProblem($project)?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php elseif ($machines = $warning->getMachines()) :?>
        <table class="table">
            <thead>
            <tr>
                <th>Машина</th>
                <th>Описание</th>
            </tr>
            </thead>
            <?php foreach ($machines as $machine) :?>
                <tr>
                    <td>
                        <a href="/machines/<?=$machine->getId()?>">
                            <?=$machine->getHost()?>
                        </a>
                    </td>
                    <td>
                        <?=$warning->getMachineProblem($machine)?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php endif;?>
</dl>