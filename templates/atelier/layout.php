<!DOCTYPE html>
<html lang="ru">
<?php
/**
 * @var $this League\Plates\Template\Template
 */
?>
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="<?= $this->asset('/bootstrap/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/bootstrap-icons.css')?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/menu.css')?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/atelier.css')?>">
    <meta name="theme-color" content="#7952b3">
    <?= $this->data['title'] ?>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="offcanvas offcanvas-start show" tabindex="-1" id="offcanvas" data-bs-keyboard="false"
     data-bs-backdrop="true" data-bs-scroll="true">
    <div class="offcanvas-header border-bottom">
        <h4>
            <a class="navbar-brand" href="#">
                <img src="/img/atelier4.svg" height="30" class="d-inline-block align-top" alt="logo">
                Atelier
            </a>
        </h4>
    </div>
    <div class="offcanvas-body px-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php if ($this->data['url']->isStartsAt(['/machines'])) : ?>active<?php endif; ?>" href="/machines">Машины</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($this->data['url']->isStartsAt(['/projects'])) : ?>active<?php endif; ?>" href="/projects">Проекты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($this->data['url']->isStartsAt(['/reports'])) : ?>active<?php endif; ?>" href="/reports">Репорты</a>
            </li>
        </ul>
        <!--        <ul class="list-unstyled ps-0">-->
        <!--            <li class="mb-1">-->
        <!--                <button class="btn btn-toggle align-items-center rounded" data-bs-toggle="collapse"-->
        <!--                        data-bs-target="#wardrobe-collapse" aria-expanded="true">-->
        <!--                    Гардероб-->
        <!--                </button>-->
        <!--                <div class="collapse show" id="wardrobe-collapse" style="">-->
        <!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
        <!--                        <li><a href="/" class="rounded">Overview</a></li>-->
        <!--                        <li><a href="#" class="rounded">Updates</a></li>-->
        <!--                        <li><a href="#" class="rounded">Reports</a></li>-->
        <!--                    </ul>-->
        <!--                </div>-->
        <!--            </li>-->
        <!--            <li class="mb-1">-->
        <!--                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"-->
        <!--                        data-bs-target="#dashboard-collapse" aria-expanded="false">-->
        <!--                    Dashboard-->
        <!--                </button>-->
        <!--                <div class="collapse" id="dashboard-collapse">-->
        <!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
        <!--                        <li><a href="#" class="rounded">Overview</a></li>-->
        <!--                        <li><a href="#" class="rounded">Weekly</a></li>-->
        <!--                        <li><a href="#" class="rounded">Monthly</a></li>-->
        <!--                        <li><a href="#" class="rounded">Annually</a></li>-->
        <!--                    </ul>-->
        <!--                </div>-->
        <!--            </li>-->
        <!--            <li class="mb-1">-->
        <!--                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"-->
        <!--                        data-bs-target="#orders-collapse" aria-expanded="false">-->
        <!--                    Orders-->
        <!--                </button>-->
        <!--                <div class="collapse" id="orders-collapse">-->
        <!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
        <!--                        <li><a href="#" class="rounded">New</a></li>-->
        <!--                        <li><a href="#" class="rounded">Processed</a></li>-->
        <!--                        <li><a href="#" class="rounded">Shipped</a></li>-->
        <!--                        <li><a href="#" class="rounded">Returned</a></li>-->
        <!--                    </ul>-->
        <!--                </div>-->
        <!--            </li>-->
        <!--            <li class="border-top my-3"></li>-->
        <!--            <li class="mb-1">-->
        <!--                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"-->
        <!--                        data-bs-target="#account-collapse" aria-expanded="false">-->
        <!--                    Account-->
        <!--                </button>-->
        <!--                <div class="collapse" id="account-collapse">-->
        <!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
        <!--                        <li><a href="#" class="rounded">New...</a></li>-->
        <!--                        <li><a href="#" class="rounded">Profile</a></li>-->
        <!--                        <li><a href="#" class="rounded">Settings</a></li>-->
        <!--                        <li><a href="#" class="rounded">Sign out</a></li>-->
        <!--                    </ul>-->
        <!--                </div>-->
        <!--            </li>-->
        <!--        </ul>-->
    </div>
</nav>
    <main class="container">
    <div class="row">
        <div class="col p-4">
            <!-- toggler -->
            <button id="sidebarCollapse" class="float-end" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"
                    role="button" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </button>

            <h1><?= $this->data['title'] ?></h1>
            <?php /** @var ?\Atelier\FlashMessage $flashMessage */?>
            <?php if ($flashMessage = $this->data['flash'] ?? null) : ?>
                <div class="alert alert-<?= strtolower($flashMessage->getType()->name) ?> alert-dismissible mt-2 fade show" role="alert">
                    <?= $flashMessage->getMessage() ?>
                </div>
            <?php endif; ?>

            <?= $this->section('content') ?>
        </div>
    </div>

        <div class="modal fade show" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" style="" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title h4" id="authModalLabel">Авторизация</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" method="post">
                            <div class="mb-3">
                                <label for="login" class="visually-hidden">Логин</label>
                                <input type="text" class="form-control" name="login" id="login" value="<?=\Atelier\Settings::getByName('machine_default_login')?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="visually-hidden">Пароль</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Пароль">
                                <div id="authError" class="d-none invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary mb-3">Проверить</button>
                            </div>
                        </form>
                    </div>
                    <!--                <div class="modal-footer">-->
                    <!--                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>-->
                    <!--                </div>-->
                </div>
            </div>
        </div>
</main>
    <footer class="container text-muted border-top mt-auto"></footer>
    <script src="<?=$this->asset('/js/jquery.min.js')?>"></script>
    <script src="<?=$this->asset('/bootstrap/js/bootstrap.js')?>"></script>
    <script src="<?=$this->asset('/bootstrap/js/bootstrap.bundle.js')?>"></script>
    <script src="<?=$this->asset('/js/menu.js')?>"></script>
    <script src="<?=$this->asset('/js/atelier.js')?>"></script>
    <script src="/js/menu.js"></script>
    <!--https://www.cssscript.com/responsive-sidebar-bootstrap-offcanvas/-->
</body>
</html>