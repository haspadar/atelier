#!/usr/bin/php
<?php

use Atelier\Command\ExtractRotatorFragments;
use Atelier\Commands;

require_once __DIR__ . '/autoload_require_composer.php';

(new \Atelier\Command\ExtractHttpHeaders())->runForAll();