#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

use Atelier\Command\ExtractGit;
use Atelier\Commands;

(new ExtractGit())->runForAll();