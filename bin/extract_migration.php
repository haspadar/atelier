#!/usr/bin/php
<?php

use Atelier\Command\ExtractMigration;

require_once __DIR__ . '/autoload_require_composer.php';

(new ExtractMigration())->runForAll();