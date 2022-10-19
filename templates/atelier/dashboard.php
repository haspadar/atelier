<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<dl class="row">
    <?php /** @var \Atelier\Warning $warning */?>
    <?php foreach ($this->data['warnings'] ?? [] as $warning) :?>
        <dt class="col-sm-2"><?=$warning->getTypeTitle()?></dt>
        <dd class="col-sm-10">
            <a href="/fittings/<?=$warning->getTypeId()?>" class="<?php if ($warning->getProblemsCount()) :?>text-danger<?php else :?>text-muted<?php endif;?>">
                <?=$warning->getProblemsCount()?>
            </a>
        </dd>
    <?php endforeach;?>
</dl>