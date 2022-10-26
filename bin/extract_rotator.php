#!/usr/bin/php
<?php

use Atelier\Command\ExtractRotator;

require_once __DIR__ . '/autoload_require_composer.php';

(new ExtractRotator())->runForAll();