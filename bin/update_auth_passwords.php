#!/usr/bin/php
<?php

use Atelier\Command\UpdateAuthPasswords;
use Atelier\Commands;
use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$authPassword = (new CLImate())
    ->yellow()
    ->password('Введите новый пароль авторизации:')
    ->prompt();
$sudoPassword = (new CLImate())
    ->yellow()
    ->password('Введите sudo-пароль для перезапуска nginx:')
    ->prompt();
(new UpdateAuthPasswords([
    'auth_password' => $authPassword,
    'sudo_password' => $sudoPassword
]))->runForAll();