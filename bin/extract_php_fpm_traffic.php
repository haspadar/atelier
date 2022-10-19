#!/usr/bin/php
<?php

use Atelier\Command\ExtractFreeSpace;

require_once __DIR__ . '/autoload_require_composer.php';

(new \Atelier\Command\ExtractPhpFpmTraffic())->runForAll();