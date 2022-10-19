#!/usr/bin/php
<?php

use Atelier\Command\ExtractSmoke;
use Atelier\Commands;

require_once __DIR__ . '/autoload_require_composer.php';

(new ExtractSmoke())->runForAll();