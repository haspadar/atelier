#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

(new \Atelier\Command\ExtractLogNames())->runForAll();