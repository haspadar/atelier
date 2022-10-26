<?php /** @var $this League\Plates\Template\Template */

use Atelier\Machine;
use Atelier\Project;

$this->layout('layout');
?>

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <?php foreach (array_keys($this->data['grouped_projects']) as $key => $typeName) :?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php if (!$key):?>active<?php endif;?> small" data-bs-toggle="pill" data-bs-target="#<?=$typeName?>" type="button" role="tab" aria-controls="pills-home" <?php if (!$key):?>aria-selected="true"<?php endif;?>><?=$typeName?></button>
        </li>
    <?php endforeach;?>
</ul>

<div class="tab-content" id="pills-tabContent">
    <?php foreach ($this->data['grouped_projects'] as $typeName => $typeProjects) :?>
        <div class="tab-pane fade show <?php if ($typeName == array_keys($this->data['grouped_projects'])[0]) :?>active<?php endif;?>" id="<?=$typeName?>" role="tabpanel" tabindex="0">
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
                 * @var Project $project
                 */
                foreach ($typeProjects as $project) :?>
                    <tr>
                        <td>
                            <a href="/projects/<?=$project->getId()?>" class="text-decoration-none">
                                <?=$project->getName()?>
                            </a>
                        </td>
                        <td>
                            <a href="/machines/<?=$project->getMachine()->getId()?>" class="text-decoration-none">
                    <span class="badge badge-dark">
                        <?=$project->getMachine()->getHost()?>
                    </span>
                            </a>
                        </td>
                    </tr>

                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    <?php endforeach;?>
</div>
