#!/usr/bin/php
<?php

use Atelier\Command\ExtractMigration;
use Atelier\Command\ExtractRotator;

require_once __DIR__ . '/autoload_require_composer.php';

(new \Atelier\Command\ExtractHttp())->runForAll();